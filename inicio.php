<?php
require_once 'includes/config.php';

// Configuración específica para la página de inicio
$page_title = APP_NAME . ' - Inicio';
$page_subtitle = 'Jefatura de Educación y Capacitación';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <!-- Estilos unificados -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/unified_header_footer.css">
    <link rel="stylesheet" href="assets/css/inicio.css">
</head>

<body>
    <!-- Header unificado -->
    <?php include 'includes/unified_header.php'; ?>

    <main class="main-content">
        <div class="container">
            <section class="welcome-section">
                <h1>Bienvenido al Sistema de Gestión de la ESyA</h1>
                <p>Sistema integral para la gestión académica de la Escuela de Suboficiales y Agentes</p>
            </section>

            <div class="navigation-cards">
                <article class="nav-card">
                    <h3>Formación de Agentes</h3>
                    <p>Acceso completo a la gestión académica de la Escuela de Suboficiales y Agentes.</p>
                    <a href="formacion_agentes.php" class="btn btn-primary btn-large">Ingresar al Sistema</a>
                </article>
                
                <article class="nav-card disabled">
                    <h3>División Capacitaciones</h3>
                    <p>Sistema avanzado de gestión de capacitaciones y cursos especializados.</p>
                    <a href="#" class="btn btn-secondary btn-large" id="comingSoonBtn">Próximamente</a>
                </article>
            </div>
        </div>
    </main>

    <!-- Footer unificado -->
    <?php include 'includes/unified_footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/inicio.js"></script>
</body>

</html>