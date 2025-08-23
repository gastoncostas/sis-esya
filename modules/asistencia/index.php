<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: ../../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Obtener lista de comisiones disponibles
$comisiones = ['A', 'B', 'C', 'D', 'E', 'F'];

// Obtener parámetros del formulario
$comision_seleccionada = isset($_GET['comision']) ? $_GET['comision'] : 'A';
$fecha_seleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Inicializar variables
$aspirantes = [];
$asistencias_registradas = [];
$error = '';
$success = '';

// Obtener lista de aspirantes de la comisión seleccionada
$stmt_aspirantes = $conn->prepare("SELECT id, dni, nombre, apellido, comision FROM aspirantes WHERE comision = ? ORDER BY apellido, nombre");
$stmt_aspirantes->bind_param("s", $comision_seleccionada);
$stmt_aspirantes->execute();
$result_aspirantes = $stmt_aspirantes->get_result();

while ($row = $result_aspirantes->fetch_assoc()) {
    $aspirantes[$row['id']] = $row;
}

// Obtener asistencias registradas para la fecha y comisión seleccionada
if (!empty($fecha_seleccionada)) {
    $stmt_asistencias = $conn->prepare("SELECT aspirante_id, presente, observaciones FROM asistencia WHERE comision = ? AND fecha = ?");
    $stmt_asistencias->bind_param("ss", $comision_seleccionada, $fecha_seleccionada);
    $stmt_asistencias->execute();
    $result_asistencias = $stmt_asistencias->get_result();
    
    while ($row = $result_asistencias->fetch_assoc()) {
        $asistencias_registradas[$row['aspirante_id']] = $row;
    }
}

// Procesar formulario de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comision = $_POST['comision'];
    $fecha = $_POST['fecha'];
    $asistencias = $_POST['asistencia'] ?? [];
    $observaciones = $_POST['observaciones'] ?? [];

    $registros_afectados = 0;

    foreach ($aspirantes as $aspirante_id => $aspirante) {
        $presente = isset($asistencias[$aspirante_id]) ? 1 : 0;
        $obs = isset($observaciones[$aspirante_id]) ? trim($observaciones[$aspirante_id]) : '';

        // Verificar si ya existe un registro para este aspirante en esta fecha
        $stmt_check = $conn->prepare("SELECT id FROM asistencia WHERE aspirante_id = ? AND comision = ? AND fecha = ?");
        $stmt_check->bind_param("iss", $aspirante_id, $comision, $fecha);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Actualizar registro existente
            $stmt = $conn->prepare("UPDATE asistencia SET presente = ?, observaciones = ? WHERE aspirante_id = ? AND comision = ? AND fecha = ?");
            $stmt->bind_param("isiss", $presente, $obs, $aspirante_id, $comision, $fecha);
        } else {
            // Insertar nuevo registro
            $stmt = $conn->prepare("INSERT INTO asistencia (aspirante_id, comision, fecha, presente, observaciones) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issis", $aspirante_id, $comision, $fecha, $presente, $obs);
        }

        if ($stmt->execute()) {
            $registros_afectados++;
        }
        $stmt->close();
        $stmt_check->close();
    }

    if ($registros_afectados > 0) {
        $success = "Asistencia registrada correctamente para $registros_afectados aspirantes de la Comisión $comision";
        // Recargar asistencias actualizadas
        $stmt_asistencias = $conn->prepare("SELECT aspirante_id, presente, observaciones FROM asistencia WHERE comision = ? AND fecha = ?");
        $stmt_asistencias->bind_param("ss", $comision_seleccionada, $fecha_seleccionada);
        $stmt_asistencias->execute();
        $result_asistencias = $stmt_asistencias->get_result();
        
        $asistencias_registradas = [];
        while ($row = $result_asistencias->fetch_assoc()) {
            $asistencias_registradas[$row['aspirante_id']] = $row;
        }
    } else {
        $error = "Error al registrar la asistencia";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Asistencia</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
    <style>
        .select-all-container {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .select-all-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .select-all-btn:hover {
            background-color: #218838;
        }
        .deselect-all-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .deselect-all-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <h1>Registro de Asistencia</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="GET" action="" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="comision">Comisión</label>
                    <select id="comision" name="comision" required onchange="this.form.submit()">
                        <option value="">Seleccione una comisión</option>
                        <?php foreach ($comisiones as $comision): ?>
                            <option value="<?php echo $comision; ?>" <?php echo ($comision == $comision_seleccionada) ? 'selected' : ''; ?>>
                                Comisión <?php echo htmlspecialchars($comision); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($fecha_seleccionada); ?>" onchange="this.form.submit()">
                </div>
            </div>
        </form>

        <?php if (count($aspirantes) > 0): ?>
            <form method="POST" action="" id="asistenciaForm">
                <input type="hidden" name="comision" value="<?php echo htmlspecialchars($comision_seleccionada); ?>">
                <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha_seleccionada); ?>">

                <h2>Lista de Aspirantes - Comisión <?php echo $comision_seleccionada; ?> - <?php echo date('d/m/Y', strtotime($fecha_seleccionada)); ?></h2>
                
                <div class="select-all-container">
                    <button type="button" class="select-all-btn" onclick="selectAll()">✓ Seleccionar Todos</button>
                    <button type="button" class="deselect-all-btn" onclick="deselectAll()">✗ Deseleccionar Todos</button>
                </div>
                
                <div class="table-info">
                    <?php 
                    $total_aspirantes = count($aspirantes);
                    $presentes = 0;
                    $ausentes = 0;
                    
                    foreach ($asistencias_registradas as $asistencia) {
                        if ($asistencia['presente']) {
                            $presentes++;
                        } else {
                            $ausentes++;
                        }
                    }
                    ?>
                    Total: <?php echo $total_aspirantes; ?> | 
                    Presentes: <span style="color: green"><?php echo $presentes; ?></span> | 
                    Ausentes: <span style="color: red"><?php echo $ausentes; ?></span>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Presente</th>
                            <th>Aspirante</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aspirantes as $aspirante_id => $aspirante): 
                            $asistencia = $asistencias_registradas[$aspirante_id] ?? null;
                            $esta_presente = $asistencia ? $asistencia['presente'] : 1; // Por defecto presente
                            $observacion = $asistencia ? $asistencia['observaciones'] : '';
                        ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="asistencia[<?php echo $aspirante_id; ?>]" value="1" <?php echo $esta_presente ? 'checked' : ''; ?> class="asistencia-checkbox">
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($aspirante['apellido'] . ', ' . $aspirante['nombre'] . ' (' . $aspirante['dni'] . ')'); ?>
                                </td>
                                <td>
                                    <?php if ($asistencia): ?>
                                        <span class="status-badge status-<?php echo $asistencia['presente'] ? 'activo' : 'inactivo'; ?>">
                                            <?php echo $asistencia['presente'] ? 'Presente' : 'Ausente'; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge" style="background-color: #6c757d; color: white;">
                                            Sin registrar
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <input type="text" name="observaciones[<?php echo $aspirante_id; ?>]" 
                                            value="<?php echo htmlspecialchars($observacion); ?>" 
                                            placeholder="Observaciones"
                                            style="width: 100%">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Registrar Asistencia</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info">
                No hay aspirantes registrados en la Comisión <?php echo $comision_seleccionada; ?>.
            </div>
        <?php endif; ?>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    
    <script>
        function selectAll() {
            const checkboxes = document.querySelectorAll('.asistencia-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateCounters();
        }

        function deselectAll() {
            const checkboxes = document.querySelectorAll('.asistencia-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateCounters();
        }

        function updateCounters() {
            const checkboxes = document.querySelectorAll('.asistencia-checkbox');
            const total = checkboxes.length;
            let presentes = 0;
            
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    presentes++;
                }
            });
            
            const ausentes = total - presentes;
            
            // Actualizar la visualización de contadores
            document.querySelector('.table-info').innerHTML = `
                Total: ${total} | 
                Presentes: <span style="color: green">${presentes}</span> | 
                Ausentes: <span style="color: red">${ausentes}</span>
            `;
        }

        // Actualizar contadores cuando cambien las casillas
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.asistencia-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCounters);
            });
            
            // Actualizar contadores iniciales
            updateCounters();
        });
    </script>
</body>

</html>