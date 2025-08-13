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

    <!-- Estilos específicos del dashboard -->
    <style>
        /* Mejoras visuales para el dashboard */
        .welcome-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite alternate;
        }

        .welcome-header h1 {
            font-size: 2.5rem;
            margin: 0 0 10px 0;
            position: relative;
            z-index: 2;
        }

        .welcome-header .user-role {
            font-size: 1.2rem;
            opacity: 0.9;
            margin: 10px 0;
            position: relative;
            z-index: 2;
        }

        .division-badge {
            margin: 15px 0;
            position: relative;
            z-index: 2;
        }

        .badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        /* Estadísticas mejoradas */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2980b9);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .stat-card .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
            display: block;
        }

        .stat-card .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Dashboard cards mejoradas */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db, #2980b9);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: white;
        }

        .dashboard-card h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .dashboard-card p {
            margin-bottom: 25px;
            color: #7f8c8d;
            line-height: 1.6;
        }

        .btn-dashboard {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-dashboard:hover {
            background: linear-gradient(135deg, #2980b9, #1f4e79);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.3);
        }

        /* Animaciones */
        @keyframes shimmer {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(0deg);
            }

            100% {
                transform: translateX(100%) translateY(100%) rotate(180deg);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-header,
        .stats-container,
        .dashboard-cards {
            animation: fadeInUp 0.6s ease-out;
        }

        .stats-container {
            animation-delay: 0.1s;
        }

        .dashboard-cards {
            animation-delay: 0.2s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-header h1 {
                font-size: 2rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
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