<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

// Obtener lista de materias activas
$materias = [];
$stmt = $conn->prepare("SELECT id, nombre, profesor FROM materias WHERE activo = 1 ORDER BY nombre");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $materias[$row['id']] = $row['nombre'] . ' (' . $row['profesor'] . ')';
    }
    $stmt->close();
}

// Obtener lista de aspirantes activos
$aspirantes = [];
$stmt = $conn->prepare("SELECT id, dni, nombre, apellido FROM aspirantes WHERE estado = 'activo' ORDER BY apellido, nombre");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $aspirantes[$row['id']] = $row['apellido'] . ', ' . $row['nombre'] . ' (DNI: ' . $row['dni'] . ')';
    }
    $stmt->close();
}

// Procesar formulario de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materia_id = (int)($_POST['materia_id'] ?? 0);
    $fecha = $_POST['fecha'] ?? '';
    $asistencias = $_POST['asistencia'] ?? [];
    $observaciones_data = $_POST['observaciones'] ?? [];

    // Validaciones
    if (empty($materia_id) || empty($fecha)) {
        $error = 'Materia y fecha son obligatorios';
    } elseif (!isset($materias[$materia_id])) {
        $error = 'Materia no válida';
    } elseif (empty($asistencias)) {
        $error = 'Debe seleccionar al menos un aspirante';
    } else {
        try {
            // Verificar si ya existe asistencia para esta materia y fecha
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM asistencia WHERE materia_id = ? AND fecha = ?");
            $stmt->bind_param("is", $materia_id, $fecha);
            $stmt->execute();
            $existeAsistencia = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($existeAsistencia) {
                $error = 'Ya existe registro de asistencia para esta materia y fecha. Use la opción de editar.';
            } else {
                // Comenzar transacción
                $conn->autocommit(false);
                $registrados = 0;

                // Preparar consulta de inserción
                $stmt = $conn->prepare("INSERT INTO asistencia (aspirante_id, materia_id, fecha, presente, observaciones) VALUES (?, ?, ?, ?, ?)");

                foreach ($asistencias as $aspirante_id => $presente_value) {
                    $aspirante_id = (int)$aspirante_id;
                    $presente = $presente_value ? 1 : 0;
                    $observaciones = trim($observaciones_data[$aspirante_id] ?? '');

                    // Verificar que el aspirante existe y está activo
                    if (!isset($aspirantes[$aspirante_id])) {
                        continue;
                    }

                    $stmt->bind_param("iisis", $aspirante_id, $materia_id, $fecha, $presente, $observaciones);
                    
                    if ($stmt->execute()) {
                        $registrados++;
                    }
                }

                $stmt->close();

                if ($registrados > 0) {
                    $conn->commit();
                    $success = "Asistencia registrada correctamente para $registrados aspirantes";
                    
                    // Limpiar formulario después del éxito
                    $_POST = [];
                } else {
                    $conn->rollback();
                    $error = 'No se pudo registrar ninguna asistencia';
                }

                $conn->autocommit(true);
            }
        } catch (Exception $e) {
            $conn->rollback();
            $conn->autocommit(true);
            $error = 'Error al registrar asistencia: ' . $e->getMessage();
            error_log("Error en registro de asistencia: " . $e->getMessage());
        }
    }
}

// Obtener registros de asistencia recientes para mostrar
$recentAttendance = [];
$stmt = $conn->prepare("
    SELECT a.fecha, m.nombre as materia, 
           COUNT(*) as total_aspirantes,
           SUM(a.presente) as presentes
    FROM asistencia a
    INNER JOIN materias m ON a.materia_id = m.id
    GROUP BY a.fecha, a.materia_id, m.nombre
    ORDER BY a.fecha DESC, m.nombre
    LIMIT 10
");

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recentAttendance[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Registro de Asistencia</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
    <style>
        .attendance-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .attendance-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .attendance-table tr:hover {
            background-color: #f8f9fa;
        }

        .checkbox-cell {
            text-align: center;
            width: 80px;
        }

        .checkbox-cell input[type="checkbox"] {
            transform: scale(1.2);
        }

        .observaciones-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .recent-attendance {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .recent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .recent-item:last-child {
            border-bottom: none;
        }

        .attendance-stats {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .select-all-controls {
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .btn-select-all {
            margin-right: 10px;
            padding: 6px 12px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="module-header">
            <h1>Registro de Asistencia</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (empty($materias)): ?>
            <div class="alert alert-warning">
                No hay materias activas configuradas. 
                <a href="../materias/agregar.php">Agregue materias</a> antes de registrar asistencia.
            </div>
        <?php elseif (empty($aspirantes)): ?>
            <div class="alert alert-warning">
                No hay aspirantes activos registrados. 
                <a href="../aspirantes/agregar.php">Agregue aspirantes</a> antes de registrar asistencia.
            </div>
        <?php else: ?>
            <!-- Formulario de registro de asistencia -->
            <div class="attendance-form">
                <h2>Nueva Asistencia</h2>
                
                <form method="POST" action="" id="attendanceForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="materia_id">Materia *</label>
                            <select id="materia_id" name="materia_id" required>
                                <option value="">Seleccione una materia</option>
                                <?php foreach ($materias as $id => $nombre): ?>
                                    <option value="<?= $id ?>" 
                                            <?= (($_POST['materia_id'] ?? '') == $id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha">Fecha *</label>
                            <input type="date" 
                                   id="fecha" 
                                   name="fecha" 
                                   required 
                                   value="<?= htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')) ?>"
                                   max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="select-all-controls">
                        <button type="button" class="btn btn-secondary btn-select-all" onclick="selectAll(true)">
                            Marcar Todos Presentes
                        </button>
                        <button type="button" class="btn btn-secondary btn-select-all" onclick="selectAll(false)">
                            Marcar Todos Ausentes
                        </button>
                    </div>

                    <h3>Lista de Aspirantes (<?= count($aspirantes) ?>)</h3>

                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">Presente</th>
                                <th>Aspirante</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aspirantes as $id => $nombre): ?>
                                <tr>
                                    <td class="checkbox-cell">
                                        <input type="checkbox" 
                                               name="asistencia[<?= $id ?>]" 
                                               value="1"
                                               <?= isset($_POST['asistencia'][$id]) ? 'checked' : 'checked' ?>>
                                    </td>
                                    <td><?= htmlspecialchars($nombre) ?></td>
                                    <td>
                                        <input type="text" 
                                               name="observaciones[<?= $id ?>]" 
                                               class="observaciones-input"
                                               placeholder="Observaciones (opcional)"
                                               value="<?= htmlspecialchars($_POST['observaciones'][$id] ?? '') ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Registrar Asistencia</button>
                        <button type="button" class="btn btn-cancel" onclick="window.location.href='historial.php'">
                            Ver Historial
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Registros recientes -->
        <?php if (!empty($recentAttendance)): ?>
            <div class="recent-attendance">
                <h3>Registros Recientes</h3>
                <div class="recent-list">
                    <?php foreach ($recentAttendance as $record): ?>
                        <div class="recent-item">
                            <div>
                                <strong><?= htmlspecialchars($record['materia']) ?></strong>
                                <br>
                                <small><?= date('d/m/Y', strtotime($record['fecha'])) ?></small>
                            </div>
                            <div class="attendance-stats">
                                <?= $record['presentes'] ?>/<?= $record['total_aspirantes'] ?> presentes
                                (<?= round(($record['presentes'] / $record['total_aspirantes']) * 100, 1) ?>%)
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="historial.php" class="btn btn-outline">Ver Historial Completo</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    <script src="../../assets/js/script.js"></script>
    
    <script>
        // Funciones JavaScript para manejo del formulario
        function selectAll(present) {
            const checkboxes = document.querySelectorAll('input[name^="asistencia["]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = present;
            });
        }

        // Confirmar antes de enviar si hay muchos ausentes
        document.getElementById('attendanceForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name^="asistencia["]:checked');
            const totalAspirantes = document.querySelectorAll('input[name^="asistencia["]').length;
            const presentes = checkboxes.length;
            const ausentes = totalAspirantes - presentes;
            
            if (ausentes > totalAspirantes * 0.5) {
                const confirmMessage = `Hay ${ausentes} aspirantes marcados como ausentes de ${totalAspirantes} total.\n\n¿Está seguro de continuar?`;
                
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Verificar que se haya seleccionado al menos materia y fecha
            const materia = document.getElementById('materia_id').value;
            const fecha = document.getElementById('fecha').value;
            
            if (!materia || !fecha) {
                alert('Por favor complete la materia y fecha antes de continuar.');
                e.preventDefault();
                return false;
            }
        });

        // Actualizar contador dinámico
        function updateAttendanceCounter() {
            const checkboxes = document.querySelectorAll('input[name^="asistencia["]:checked');
            const totalAspirantes = document.querySelectorAll('input[name^="asistencia["]').length;
            const presentes = checkboxes.length;
            
            // Actualizar el título si existe un elemento para ello
            const titleElement = document.querySelector('h3');
            if (titleElement && titleElement.textContent.includes('Lista de Aspirantes')) {
                titleElement.textContent = `Lista de Aspirantes (${presentes}/${totalAspirantes} presentes)`;
            }
        }

        // Agregar listeners para actualizar contador
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name^="asistencia["]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateAttendanceCounter);
            });
            
            // Llamar una vez al cargar para establecer estado inicial
            updateAttendanceCounter();
        });

        // Validar fecha (no permitir fechas futuras)
        document.getElementById('fecha').addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate > today) {
                alert('No se puede registrar asistencia para fechas futuras.');
                this.value = '<?= date('Y-m-d') ?>';
            }
        });
    </script>
</body>

</html>

<?php
$conn->close();
?>