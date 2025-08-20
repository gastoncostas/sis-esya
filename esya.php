<?php
require_once 'includes/config.php';

// Configuración específica para la página
$page_title = APP_NAME . ' - Divisiones ESyA';
$breadcrumb = [
    ['url' => 'inicio.php', 'text' => 'Inicio'],
    ['url' => 'formacion_agentes.php', 'text' => 'Formación de Agentes'],
    ['text' => 'Sistema ESyA']
];
$divisions = [
    [
        'icon' => 'icon-command',
        'title' => 'Jefatura de Cuerpo',
        'description' => 'Gestión administrativa y operativa del cuerpo de cadetes',
        'url' => 'login.php?division=jefatura_cuerpo'
    ],
    [
        'icon' => 'icon-studies',
        'title' => 'Jefatura de Estudios',
        'description' => 'Coordinación académica y planes de estudio',
        'url' => 'login.php?division=jefatura_estudios'
    ],
    [
        'icon' => 'icon-medical',
        'title' => 'Servicios Médicos',
        'description' => 'Atención médica y seguimiento sanitario',
        'url' => 'login.php?division=servicios_medicos'
    ],
    [
        'icon' => 'icon-assistant',
        'title' => 'Ayudantía',
        'description' => 'Servicios de apoyo y asistencia administrativa',
        'url' => 'login.php?division=ayudantia'
    ]
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
    <link rel="stylesheet" href="assets/css/esya.css">
</head>

<body>
    <!-- Header unificado -->
    <?php include 'includes/unified_header.php'; ?>

    <main class="main-content">
        <div class="container">
            <section class="welcome-section">
                <h1>Escuela de Suboficiales y Agentes</h1>
                <p>Seleccione la división a la que desea acceder</p>
            </section>

            <div class="divisions-grid">
                <?php foreach ($divisions as $division): ?>
                <article class="division-card">
                    <div class="card-icon">
                        <i class="<?= $division['icon'] ?>"></i>
                    </div>
                    <h3><?= $division['title'] ?></h3>
                    <p><?= $division['description'] ?></p>
                    <a href="<?= $division['url'] ?>" class="btn btn-primary btn-large">Acceder</a>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="back-section">
                <a href="formacion_agentes.php" class="btn btn-back">← Volver a Formación</a>
            </div>
        </div>
    </main>

    <!-- Footer unificado -->
    <?php include 'includes/unified_footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/esya.js"></script>
</body>

</html>