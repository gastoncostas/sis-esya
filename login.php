<?php
// 1. Iniciar buffer de salida para evitar problemas con headers
ob_start();

// 2. Manejo de sesiones seguro - debe ser LO PRIMERO
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 3. Limpieza de sesión previa
$_SESSION = array(); // Vaciar array de sesión

// 4. Destruir y reiniciar sesión de forma segura
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

session_destroy();

// 5. Iniciar nueva sesión con ID regenerado
session_start();
session_regenerate_id(true);

// 6. Ahora incluir configuraciones y dependencias
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/database.php';

$auth = new Auth();

// 7. Redirección segura si ya está autenticado
if ($auth->isLoggedIn()) {
    if (!headers_sent()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo '<script>window.location.href = "dashboard.php";</script>';
        exit();
    }
}

// 8. Obtener información de la división
$division = filter_input(INPUT_GET, 'division', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$division_names = [
    'jefatura_cuerpo' => 'Jefatura de Cuerpo',
    'jefatura_estudios' => 'Jefatura de Estudios',
    'servicios_medicos' => 'Servicios Médicos',
    'ayudantia' => 'Ayudantía'
];

$division_title = $division_names[$division] ?? 'Sistema ESyA';

// 9. Manejo de mensajes
$error = '';
$success = '';

if (isset($_GET['logout'])) {
    $success = 'Sesión cerrada correctamente';
}

// 10. Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($username) || empty($password)) {
        $error = "Usuario y contraseña son requeridos";
    } else {
        if ($auth->login($username, $password)) {
            if (!empty($division)) {
                $_SESSION['temp_auth']['division'] = $division;
                $_SESSION['temp_auth']['division_name'] = $division_title;
            }
            
            if (!headers_sent()) {
                header("Location: dashboard.php");
                exit();
            } else {
                echo '<script>window.location.href = "dashboard.php";</script>';
                exit();
            }
        } else {
            $error = "Credenciales incorrectas";
        }
    }
}

// 11. Limpiar buffer antes de enviar HTML
ob_end_clean();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> - Login</title>
    
    <!-- Estilos unificados -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/unified_header_footer.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <!-- Header unificado -->
    <?php include 'includes/unified_header.php'; ?>

    <div class="login-wrapper">
        <div class="login-container">
            <?php if (!empty($division)): ?>
                <div class="division-info">
                    <h3><?= htmlspecialchars($division_title) ?></h3>
                    <p>Acceso al sistema de gestión</p>
                </div>
            <?php endif; ?>

            <div class="login-header">
                <h1><?= htmlspecialchars(APP_NAME) ?></h1>
                <p>Sistema de Gestión Académica</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <?php if (!empty($division)): ?>
                    <input type="hidden" name="division" value="<?= htmlspecialchars($division) ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn">Ingresar</button>
            </form>

            <div class="back-link">
                <?php if (!empty($division)): ?>
                    <a href="esya.php">← Volver a Divisiones</a>
                <?php else: ?>
                    <a href="inicio.php">← Volver al Inicio</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer unificado -->
    <?php include 'includes/unified_footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/login.js"></script>
</body>

</html>