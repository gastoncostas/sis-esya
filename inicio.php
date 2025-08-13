<?php
require_once 'includes/config.php';

// Configuración específica para la página
$page_title = 'Policía de Tucumán - Jefatura de Educación y Capacitación';
$page_subtitle = 'Jefatura de Educación y Capacitación';

// Sistemas disponibles
$sistemas = [
    [
        'icon' => '👮‍♂️',
        'title' => 'Formación de Agentes',
        'description' => 'Sistema integral para la gestión y formación de nuevos agentes de la Policía de Tucumán.',
        'url' => 'formacion_agentes.php',
        'available' => true,
        'class' => ''
    ],
    [
        'icon' => '📚',
        'title' => 'Capacitación Continua',
        'description' => 'Plataforma de capacitación y actualización profesional para el personal policial.',
        'url' => '#',
        'available' => false,
        'class' => 'disabled'
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
    <link rel="stylesheet" href="assets/css/inicio.css">
    
    <meta name="description" content="Sistema de gestión educativa de la Policía de Tucumán">
    <meta name="keywords" content="policía, tucumán, educación, capacitación">
</head>

<body>
    <!-- Header unificado -->
    <?php 
    $GLOBALS['page_subtitle'] = $page_subtitle;
    include 'includes/unified_header.php'; 
    ?>

    <main class="main-content">
        <div class="container">
            <!-- Sección de bienvenida -->
            <section class="welcome-section">
                <h1>Jefatura de Educación y Capacitación</h1>
                <p>Seleccione el sistema al que desea acceder para continuar con su trabajo</p>
            </section>

            <!-- Tarjetas de navegación -->
            <div class="navigation-cards">
                <?php foreach ($sistemas as $sistema): ?>
                    <article class="nav-card <?= $sistema['class'] ?>">
                        <div class="card-icon">
                            <?= $sistema['icon'] ?>
                        </div>
                        <h3><?= htmlspecialchars($sistema['title']) ?></h3>
                        <p><?= htmlspecialchars($sistema['description']) ?></p>
                        
                        <?php if ($sistema['available']): ?>
                            <a href="<?= htmlspecialchars($sistema['url']) ?>" class="btn btn-primary btn-large">
                                Acceder al Sistema
                            </a>
                        <?php else: ?>
                            <button id="comingSoonBtn" class="btn btn-secondary btn-large">
                                Próximamente
                            </button>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Footer unificado -->
    <?php include 'includes/unified_footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/inicio.js"></script>
</body>

</html>