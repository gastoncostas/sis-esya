<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();

// Requerir login
$auth->requireLogin();

$user = $auth->getUserInfo();
$db = new Database();
$conn = $db->getConnection();

// Obtener estadísticas de forma segura
$stats = [
    'aspirantes' => 0,
    'activos' => 0,
    'asistencias_hoy' => 0
];

try {
    // Total aspirantes
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM aspirantes");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['aspirantes'] = (int)($row['total'] ?? 0);
        $stmt->close();
    }

    // Aspirantes activos
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM aspirantes WHERE estado = ?");
    if ($stmt) {
        $estado = 'activo';
        $stmt->bind_param("s", $estado);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['activos'] = (int)($row['total'] ?? 0);
        $stmt->close();
    }

    // Asistencias hoy
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM asistencia WHERE fecha = ?");
    if ($stmt) {
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['asistencias_hoy'] = (int)($row['total'] ?? 0);
        $stmt->close();
    }
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
}

$page_title = APP_NAME . ' - Dashboard';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Estilos unificados -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/unified_header_footer.css">

</head>

<body>
    <!-- Header unificado - se detecta automáticamente que es una página del sistema -->
    <?php include 'includes/unified_header.php'; ?>

    <div class="container">
        <div class="welcome-header">
            <h1>Bienvenido <?= htmlspecialchars($user['nombre_completo']) ?></h1>

            <?php if (!empty($user['division_name'])): ?>
                <div class="division-badge">
                    <span class="badge"><?= htmlspecialchars($user['division_name']) ?></span>
                </div>
            <?php endif; ?>

            <p class="user-role">Rol: <?= ucfirst($user['rol']) ?></p>
        </div>

        <!-- Estadísticas mejoradas -->
        <div class="stats-container">
            <div class="stat-card">
                <span class="stat-number"><?= $stats['aspirantes'] ?></span>
                <div class="stat-label">Total Aspirantes</div>
                <h3>Registro completo de candidatos en el sistema</h3>
            </div>

            <div class="stat-card">
                <span class="stat-number"><?= $stats['activos'] ?></span>
                <div class="stat-label">Aspirantes Activos</div>
                <h3>Candidatos en proceso de formación</h3>
            </div>

            <div class="stat-card">
                <span class="stat-number"><?= $stats['asistencias_hoy'] ?></span>
                <div class="stat-label">Asistencias Hoy</div>
                <h3>Registros de asistencia del día</h3>
            </div>
        </div>

        <!-- Módulos del dashboard -->
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <div class="card-icon">👥</div>
                <h2>Aspirantes</h2>
                <p>Gestión completa de aspirantes: registro, edición y seguimiento de su progreso académico en tiempo real.</p>
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

            <?php if ($auth->isAdmin()): ?>
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
                    <a href="modules/reportes/" class="btn-dashboard">Ver Reportes</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

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

            // Mostrar información contextual
            const userRole = '<?= $user['rol'] ?>';
            const divisionName = '<?= $user['division_name'] ?? '' ?>';

            if (divisionName) {
                console.log(`Usuario conectado: ${userRole} - ${divisionName}`);
            } else {
                console.log(`Usuario conectado: ${userRole}`);
            }
        });
    </script>
</body>

</html>