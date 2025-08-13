<?php
// Determinar el tipo de header basado en el contexto
$is_logged_in = false;
$user = null;
$current_page = basename($_SERVER['PHP_SELF']);

// Verificar si el usuario está logueado
if (class_exists('Auth')) {
    try {
        $auth = new Auth();
        $is_logged_in = $auth->isLoggedIn();
        if ($is_logged_in) {
            $user = $auth->getUserInfo();
        }
    } catch (Exception $e) {
        $is_logged_in = false;
        error_log("Error en header auth: " . $e->getMessage());
    }
}

// Determinar el contexto de la página
$context = 'navigation'; // Por defecto páginas de navegación (inicio, formación, etc.)
$navigation_pages = ['inicio.php', 'formacion_agentes.php', 'esya.php', 'login.php'];

if ($is_logged_in || !in_array($current_page, $navigation_pages)) {
    $context = 'system'; // Páginas del sistema (dashboard, módulos)
}

// Configuración de breadcrumb para páginas de navegación
$breadcrumb = '';
if (isset($GLOBALS['breadcrumb_items']) && is_array($GLOBALS['breadcrumb_items'])) {
    $breadcrumb_parts = [];
    foreach ($GLOBALS['breadcrumb_items'] as $item) {
        if (is_array($item) && isset($item['text'])) {
            if (isset($item['url'])) {
                $breadcrumb_parts[] = '<a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['text']) . '</a>';
            } else {
                $breadcrumb_parts[] = '<span>' . htmlspecialchars($item['text']) . '</span>';
            }
        }
    }
    $breadcrumb = implode(' <span>></span> ', $breadcrumb_parts);
}

// Función para obtener las iniciales del usuario
function getUserInitials($name)
{
    if (empty($name)) return 'U';
    
    $words = explode(' ', trim($name));
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }
    }
    return $initials ?: 'U';
}

// Función para determinar si una página está activa
function isPageActive($page_path, $current_uri = null)
{
    if ($current_uri === null) {
        $current_uri = $_SERVER['REQUEST_URI'];
    }
    return strpos($current_uri, $page_path) !== false;
}

// Determinar base URL
$base_url = '';
if (defined('BASE_URL')) {
    $base_url = BASE_URL;
} else {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base_url = $protocol . $host . $script_dir . '/';
}
?>

<?php if ($context === 'navigation'): ?>
    <!-- Header para páginas de navegación (inicio, formación, esya, login) -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo-section">
                <img src="<?= $base_url ?>assets/img/logo-policia.png"
                    alt="Logo Policía de Tucumán"
                    class="header-logo"
                    onerror="this.style.display='none';">
                <div class="title-section">
                    <h1>Policía de Tucumán</h1>
                    <h2><?= isset($GLOBALS['page_subtitle']) ? htmlspecialchars($GLOBALS['page_subtitle']) : 'Jefatura de Educación y Capacitación' ?></h2>
                </div>
            </div>

            <?php if (!empty($breadcrumb)): ?>
                <nav class="breadcrumb">
                    <?= $breadcrumb ?>
                </nav>
            <?php endif; ?>
        </div>
    </header>

<?php else: ?>
    <!-- Header para páginas del sistema (dashboard, módulos) -->
    <header>
        <div class="header-container">
            <div class="logo">
                <h2><?= defined('APP_NAME') ? htmlspecialchars(APP_NAME) : 'Sistema de Gestión de la ESyA' ?></h2>
            </div>

            <nav>
                <ul class="main-nav">
                    <li class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>dashboard.php">
                            <i class="icon">🏠</i> Inicio
                        </a>
                    </li>
                    <li class="<?= isPageActive('aspirantes') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>modules/aspirantes/">
                            <i class="icon">👥</i> Aspirantes
                        </a>
                    </li>
                    <li class="<?= isPageActive('asistencia') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>modules/asistencia/">
                            <i class="icon">📋</i> Asistencia
                        </a>
                    </li>
                    <li class="<?= isPageActive('materias') ? 'active' : '' ?>">
                        <a href="<?= $base_url ?>modules/materias/">
                            <i class="icon">📚</i> Materias
                        </a>
                    </li>

                    <?php if ($user && $user['rol'] === 'admin'): ?>
                        <li class="<?= isPageActive('usuarios') ? 'active' : '' ?>">
                            <a href="<?= $base_url ?>modules/usuarios/">
                                <i class="icon">👤</i> Usuarios
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <?php if ($user): ?>
                    <div class="user-nav">
                        <div class="user-profile">
                            <div class="user-avatar">
                                <?= getUserInitials($user['nombre_completo']) ?>
                            </div>
                            <span class="user-name"><?= htmlspecialchars($user['nombre_completo']) ?></span>
                            <div class="user-dropdown">
                                <a href="<?= $base_url ?>perfil.php">
                                    <i class="icon">⚙️</i> Mi Perfil
                                </a>
                                <?php if (!empty($user['division_name'])): ?>
                                    <a href="#" onclick="return false;" style="opacity: 0.7;">
                                        <i class="icon">🏢</i> <?= htmlspecialchars($user['division_name']) ?>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= $base_url ?>logout.php">
                                    <i class="icon">🚪</i> Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </nav>
        </div>
    </header>
<?php endif; ?>

<style>
    /* Estilos base para el header */
    .main-header {
        background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        color: white;
        padding: 20px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .logo-section {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header-logo {
        height: 60px;
        width: auto;
    }

    .title-section h1 {
        font-size: 1.8rem;
        margin: 0;
        font-weight: 600;
    }

    .title-section h2 {
        font-size: 1.1rem;
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-weight: 400;
    }

    .breadcrumb {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .breadcrumb a {
        color: white;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    /* Estilos para header del sistema */
    header {
        background-color: #2c3e50;
        color: white;
        padding: 15px 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .main-nav {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        align-items: center;
    }

    .main-nav li {
        margin-left: 20px;
    }

    .main-nav li a {
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 4px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .main-nav li a:hover,
    .main-nav li.active a {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Usuario dropdown */
    .user-nav {
        margin-left: 20px;
        position: relative;
    }

    .user-profile {
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .user-profile:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        background: #3498db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        margin-right: 10px;
    }

    .user-name {
        font-weight: 500;
    }

    .user-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background-color: #2c3e50;
        min-width: 200px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 100;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 5px;
    }

    .user-profile:hover .user-dropdown {
        display: block;
    }

    .user-dropdown a {
        display: block;
        padding: 12px 16px;
        color: white;
        text-decoration: none;
        transition: background-color 0.3s;
        border: none;
    }

    .user-dropdown a:hover {
        background-color: #3498db;
    }

    /* Iconos inline */
    .icon {
        font-style: normal;
        margin-right: 8px;
        font-size: 0.9em;
        opacity: 0.8;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-container {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .logo-section {
            flex-direction: column;
            gap: 10px;
        }

        .title-section h1 {
            font-size: 1.5rem;
        }

        .title-section h2 {
            font-size: 1rem;
        }

        .main-nav {
            flex-direction: column;
            gap: 10px;
        }

        .main-nav li {
            margin: 0;
        }

        .user-nav {
            margin: 10px 0 0 0;
        }

        .user-name {
            display: none;
        }

        .icon {
            display: none;
        }
    }

    /* División del usuario destacada */
    .user-dropdown a[onclick="return false;"] {
        background-color: rgba(52, 152, 219, 0.1);
        border-left: 3px solid #3498db;
        font-weight: 500;
    }
</style>

<script>
    // Mejorar la experiencia del menú dropdown en móviles
    document.addEventListener('DOMContentLoaded', function() {
        const userProfile = document.querySelector('.user-profile');
        const userDropdown = document.querySelector('.user-dropdown');

        if (userProfile && userDropdown) {
            // En dispositivos táctiles, usar click en lugar de hover
            if ('ontouchstart' in window) {
                userProfile.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
                });

                document.addEventListener('click', function() {
                    userDropdown.style.display = 'none';
                });

                userDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        }

        // Resaltar página activa basada en la URL actual
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.main-nav a');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.includes(href.replace(window.location.origin, ''))) {
                link.parentElement.classList.add('active');
            }
        });
    });
</script>