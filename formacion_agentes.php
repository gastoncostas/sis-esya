<?php
require_once 'includes/config.php';

// Configuración específica para la página
$page_title = APP_NAME . ' - Formación de Agentes';
$breadcrumb = [
    ['url' => 'inicio.php', 'text' => 'Inicio'],
    ['text' => 'Formación de Agentes']
];
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
    <link rel="stylesheet" href="assets/css/formacion_agentes.css">
</head>

<body>
    <!-- Header unificado -->
    <?php include 'includes/unified_header.php'; ?>

    <main class="main-content">
        <div class="container">
            <section class="welcome-section">
                <h1>Formación de Agentes</h1>
                <p>Seleccione el sistema al que desea acceder</p>
            </section>

            <div class="navigation-cards">
                <article class="nav-card">
                    <h3>Sistema de Gestión de la ESyA</h3>
                    <p>Gestión integral de la Escuela de Suboficiales y Agentes - Control de aspirantes, materias y asistencias</p>
                    <a href="esya.php" class="btn btn-primary btn-large">Acceder</a>
                </article>

                <article class="nav-card disabled">
                    <h3>Aula Virtual</h3>
                    <p>Plataforma de educación a distancia y recursos digitales para la formación</p>
                    <a href="#" class="btn btn-secondary btn-large" id="developmentBtn">En Desarrollo</a>
                </article>
            </div>

            <div class="back-section">
                <a href="inicio.php" class="btn btn-back">← Volver al Inicio</a>
            </div>
        </div>
    </main>

    <!-- Footer unificado -->
    <?php include 'includes/unified_footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/formacion_agentes.js"></script>
</body>

</html>