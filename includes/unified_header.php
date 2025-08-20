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
?>

<?php if ($context === 'navigation'): ?>
    <!-- Header para páginas de navegación (inicio, formación, esya, login) -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo-section">
                <img src="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>assets/img/logo-policia.png"
                    alt="Logo Policía de Tucumán"
                    class="header-logo"
                    onerror="this.style.display='none';">
                <div class="title-section">
                    <h1>Policía de Tucumán</h1>
                    <h2><?php echo isset($GLOBALS['page_subtitle']) ? htmlspecialchars($GLOBALS['page_subtitle']) : 'Jefatura de Educación y Capacitación'; ?></h2>
                </div>
            </div>

            <?php if (!empty($breadcrumb)): ?>
                <nav class="breadcrumb">
                    <?php echo $breadcrumb; ?>
                </nav>
            <?php endif; ?>
        </div>
    </header>

<?php else: ?>
    <!-- Header para páginas del sistema (dashboard, módulos) -->
    <header>
        <div class="header-container">
            <div class="logo">
                <h2><?php echo defined('APP_NAME') ? APP_NAME : 'Sistema de Gestión de la ESyA'; ?></h2>
            </div>

            <nav>
                <ul class="main-nav">
                    <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                        <a href="<?php echo defined('BASE_URL') ? BASE_URL : '/'; ?>dashboard.php">
                            <i class="icon">🏠</i> Inicio
                        </a>
                    </li>
                    <li class="<?php echo isPageActive('aspirantes') ? 'active' : ''; ?>">
                        <a href="<?php echo defined('BASE_URL') ? BASE_URL : '/'; ?>modules/aspirantes/">
                            <i class="icon">👥</i> Aspirantes
                        </a>
                    </li>
                    <li class="<?php echo isPageActive('asistencia') ? 'active' : ''; ?>">
                        <a href="<?php echo defined('BASE_URL') ? BASE_URL : '/'; ?>modules/asistencia/">
                            <i class="icon">📋</i> Asistencia
                        </a>
                    </li>
                    <li class="<?php echo isPageActive('materias') ? 'active' : ''; ?>">
                        <a href="<?php echo defined('BASE_URL') ? BASE_URL : '/'; ?>modules/materias/">
                            <i class="icon">📚</i> Materias
                        </a>
                    </li>

                    <?php if ($user && $user['rol'] === 'admin'): ?>
                        <li class="<?php echo isPageActive('usuarios') ? 'active' : ''; ?>">
                            <a href="<?php echo defined('BASE_URL') ? BASE_URL : '/'; ?>modules/usuarios/">
                                <i class="icon">👤</i> Usuarios
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($user): ?>
                        <div class="user-nav">
                            <div class="user-profile">
                                <div class="user-avatar">
                                    <?php echo getUserInitials($user['nombre_completo']); ?>
                                </div>
                                <span class="user-name"><?php echo htmlspecialchars($user['nombre_completo']); ?></span>
                                <div class="user-dropdown">
                                    <a href="<?php echo defined('BASE_URL') ? BASE_URL : '/'; ?>perfil.php">
                                        <i class="icon">⚙️</i> Mi Perfil
                                    </a>
                                    <?php if (isset($user['division_name'])): ?>
                                        <a href="#" onclick="return false;" style="opacity: 0.7;">
                                            <i class="icon">🏢</i> <?php echo htmlspecialchars($user['division_name']); ?>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo defined('BASE_URL') ? BASE_URL : '/'; ?>logout.php">
                                        <i class="icon">🚪</i> Cerrar Sesión
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
<?php endif; ?>

<style>
    /* Iconos inline para el header */
    .icon {
        font-style: normal;
        margin-right: 8px;
        font-size: 0.9em;
        opacity: 0.8;
    }

    /* Ajuste para rutas relativas en subdirectorios */
    nav a[href*="modules/"],
    nav a[href*="dashboard.php"],
    nav a[href*="perfil.php"],
    nav a[href*="logout.php"] {
        color: white;
        text-decoration: none;
    }

    /* Mejora visual para división del usuario */
    .user-dropdown a[onclick="return false;"] {
        background-color: rgba(52, 152, 219, 0.1);
        border-left: 3px solid #3498db;
        font-weight: 500;
    }

    /* Responsive para iconos */
    @media (max-width: 768px) {
        .icon {
            display: none;
        }

        .user-name {
            display: none;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }
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