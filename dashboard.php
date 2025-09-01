<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = $auth->getUserInfo();
$db = new Database();
$conn = $db->getConnection();

// Función para verificar si una tabla existe
function tableExists($conn, $tableName)
{
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

// Función segura para contar registros
function safeCount($conn, $table, $where = '')
{
    if (!tableExists($conn, $table)) {
        return 0;
    }

    try {
        $query = "SELECT COUNT(*) as total FROM $table";
        if (!empty($where)) {
            $query .= " WHERE $where";
        }

        $result = $conn->query($query);
        if ($result) {
            return $result->fetch_assoc()['total'];
        }
        return 0;
    } catch (Exception $e) {
        error_log("Error counting records from $table: " . $e->getMessage());
        return 0;
    }
}

// Obtener estadísticas de manera segura
$stats = [
    'aspirantes' => safeCount($conn, 'aspirantes'),
    'activos' => safeCount($conn, 'aspirantes', "estado = 'activo'"),
    'asistencias_hoy' => 0
];

// Asistencias de hoy (solo si la tabla existe) - CORREGIDO: solo presentes
if (tableExists($conn, 'asistencia')) {
    $today = date('Y-m-d');
    // Contar solo los registros donde presente = 1 (true)
    $stats['asistencias_hoy'] = safeCount($conn, 'asistencia', "fecha = '$today' AND presente = 1");

    // Asegurar que no supere el total de aspirantes
    if ($stats['asistencias_hoy'] > $stats['aspirantes']) {
        $stats['asistencias_hoy'] = $stats['aspirantes'];
    }
}

// Verificar si es necesario crear tablas básicas
$tablesToCheck = ['aspirantes', 'asistencia', 'materias'];
$missingTables = [];

foreach ($tablesToCheck as $table) {
    if (!tableExists($conn, $table)) {
        $missingTables[] = $table;
    }
}

// Si el usuario es admin y faltan tablas, mostrar alerta
$showSetupAlert = (!empty($missingTables) && $user['rol'] === 'admin');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Dashboard</title>
    <!-- Estilos unificados -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/unified_header_footer.css">
</head>

<body>
    <!-- Header unificado - se detecta automáticamente que es una página del sistema -->
    <?php include 'includes/unified_header.php'; ?>

    <main class="main-content">
        <div class="container">
            <!-- Alerta de configuración para administradores -->
            <?php if ($showSetupAlert): ?>
                <div class="setup-alert">
                    <h3>⚠️ Configuración Requerida</h3>
                    <p>El sistema detectó que faltan algunas tablas esenciales en la base de datos:</p>
                    <ul>
                        <?php foreach ($missingTables as $table): ?>
                            <li><?php echo htmlspecialchars($table); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p>Por favor, ejecute el script de configuración para crear las tablas necesarias.</p>
                    <button class="btn-setup" onclick="runSetup()">Ejecutar Configuración</button>
                </div>
            <?php endif; ?>

            <section class="dashboard-welcome-section">
                <h1>Bienvenido <?php echo htmlspecialchars($user['nombre_completo']); ?></h1>

                <?php if (isset($user['division_name'])): ?>
                    <div class="division-badge">
                        <span class="badge"><?php echo htmlspecialchars($user['division_name']); ?></span>
                    </div>
                <?php endif; ?>

                <p class="user-role">Rol: <?php echo ucfirst($user['rol']); ?></p>
            </section>

            <!-- Estadísticas mejoradas -->
            <div class="stats-container">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $stats['aspirantes']; ?></span>
                    <div class="stat-label">Total Cursantes</div>
                    <h3>Registro completo de candidatos en el sistema</h3>
                </div>

                <div class="stat-card">
                    <span class="stat-number"><?php echo $stats['activos']; ?></span>
                    <div class="stat-label">Cursantes Activos</div>
                    <h3>Candidatos en proceso de formación</h3>
                </div>

                <div class="stat-card">
                    <span class="stat-number"><?php echo $stats['asistencias_hoy']; ?></span>
                    <div class="stat-label">Asistencias Hoy</div>
                    <h3>Registros de asistencia del día</h3>
                </div>
            </div>

            <!-- Módulos del dashboard -->
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <div class="card-icon">👥</div>
                    <h2>Cursantes</h2>
                    <p>Gestión completa de los cursantes: registro, edición y seguimiento de su progreso académico en tiempo real.</p>
                    <a href="modules/aspirantes/" class="btn-dashboard">Acceder al Módulo</a>
                </div>

                <div class="dashboard-card">
                    <div class="card-icon">📋</div>
                    <h2>Control de Asistencia</h2>
                    <p>Registro diario de asistencia por materia y fecha, con observaciones personalizadas y reportes automáticos.</p>
                    <a href="modules/asistencia/" class="btn-dashboard">Registrar Asistencia</a>
                </div>

                <div class="dashboard-card">
                    <div class="card-icon">📚</div>
                    <h2>Gestión de Materias</h2>
                    <p>Administración completa del plan de estudios, profesores y carga horaria de cada materia.</p>
                    <a href="modules/materias/" class="btn-dashboard">Ver Materias</a>
                </div>

                <?php if ($user['rol'] === 'admin'): ?>
                    <div class="dashboard-card">
                        <div class="card-icon">👤</div>
                        <h2>Administración de Usuarios</h2>
                        <p>Gestión completa de usuarios del sistema: creación, edición, asignación de roles y control de permisos.</p>
                        <a href="modules/usuarios/" class="btn-dashboard">Gestionar Usuarios</a>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-icon">📊</div>
                        <h2>Reportes y Estadísticas</h2>
                        <p>Generación de reportes estadísticos y de seguimiento académico para la toma de decisiones.</p>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-icon">⚙️</div>
                        <h2>Configuración del Sistema</h2>
                        <p>Herramientas de administración y configuración del sistema ESyA.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer unificado - se adapta automáticamente al contexto del sistema -->
    <?php include 'includes/unified_footer.php'; ?>

    <!-- Scripts adicionales para el dashboard -->
    <script src="assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de contadores
            const statNumbers = document.querySelectorAll('.stat-number');

            statNumbers.forEach(counter => {
                const target = parseInt(counter.textContent);
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current);
                }, 16);
            });

            // Efecto de hover mejorado para las cards
            const dashboardCards = document.querySelectorAll('.dashboard-card');

            dashboardCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Mostrar información contextual
            const showContextInfo = () => {
                const userRole = '<?php echo $user['rol']; ?>';
                const divisionName = '<?php echo isset($user['division_name']) ? $user['division_name'] : ''; ?>';

                if (divisionName) {
                    console.log(`Usuario conectado: ${userRole} - ${divisionName}`);
                } else {
                    console.log(`Usuario conectado: ${userRole}`);
                }
            };

            showContextInfo();
        });

        // Función para ejecutar configuración
        function runSetup() {
            if (confirm('¿Está seguro de que desea ejecutar la configuración del sistema? Esto creará las tablas necesarias en la base de datos.')) {
                fetch('setup.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'setup_tables'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Configuración completada con éxito. La página se recargará.');
                            location.reload();
                        } else {
                            alert('Error en la configuración: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión: '.error.message);
                    });
            }
        }
    </script>
</body>

</html>