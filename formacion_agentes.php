<?php
require_once 'includes/config.php';

// Configuración específica para la página
$page_title = APP_NAME . ' - Formación de Agentes';
$breadcrumb = [
    ['url' => 'inicio.php', 'text' => 'Inicio'],
    ['text' => 'Formación de Agentes']
];

$systems = [
    [
        'title' => 'Sistema de Gestión de la ESyA',
        'description' => 'Gestión integral de la Escuela de Suboficiales y Agentes - Control de cursantes, materias, asistencia y novedades.',
        'link' => 'esya.php',
        'class' => '',
        'btn_class' => 'btn-primary',
        'btn_text' => 'Acceder'
    ],
    [
        'title' => 'Aula Virtual',
        'description' => 'Plataforma destinada a los cursantes para brindarles recursos digitales para su formación.',
        'link' => 'aula1-con-bruno',
        'class' => '',
        'btn_class' => 'btn-secondary',
        'btn_text' => 'Acceder'
    ]
];
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
                <?php foreach ($systems as $system): ?>
                <article class="nav-card <?= $system['class'] ?>">
                    <h3><?= htmlspecialchars($system['title']) ?></h3>
                    <p><?= htmlspecialchars($system['description']) ?></p>
                    <a href="<?= $system['link'] ?>" class="btn <?= $system['btn_class'] ?> btn-large"><?= htmlspecialchars($system['btn_text']) ?></a>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="back-section">
                <a href="inicio.php" class="btn btn-back">Volver al Inicio</a>
            </div>
        </div>
    </main>

    <!-- Footer unificado -->
    <?php include 'includes/unified_footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/formacion_agentes.js"></script>
</body>

</html>