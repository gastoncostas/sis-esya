<?php
require_once 'includes/config.php';

// Configuración específica para la página de inicio
$GLOBALS['page_subtitle'] = 'Jefatura de Educación y Capacitación';
// No hay breadcrumb en la página de inicio (es la raíz)
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Inicio</title>

    <!-- Estilos unificados -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/unified_header_footer.css">
</head>

<body>
    <!-- Header unificado - detecta automáticamente que es una página de navegación -->
    <?php include 'includes/unified_header.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="welcome-section">
                <h1>Bienvenido al Sistema de Gestión de la ESyA</h1>
            </div>

            <div class="navigation-cards">
                <div class="nav-card">
                    <div class="card-icon">
                        <i class="icon-education"></i>
                    </div>
                    <h3>Formación de Agentes</h3>
                    <p>Acceso completo a la gestión académica de la Escuela de Suboficiales y Agentes.</p>
                    <a href="formacion_agentes.php" class="btn btn-primary btn-large">Ingresar al Sistema</a>
                </div>

                <div class="nav-card disabled">
                    <div class="card-icon">
                        <i class="icon-training"></i>
                    </div>
                    <h3>División Capacitaciones</h3>
                    <p>Sistema avanzado de gestión de capacitaciones y cursos especializados para el desarrollo profesional continuo.</p>
                    <a href="#" class="btn btn-secondary btn-large" onclick="showComingSoon(); return false;">Próximamente</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer unificado - se adapta automáticamente al contexto de navegación -->
    <?php include 'includes/unified_footer.php'; ?>
</body>

</html>