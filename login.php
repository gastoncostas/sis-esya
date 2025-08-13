<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/database.php';

$auth = new Auth();

// Si ya está logueado, redirigir al dashboard
if ($auth->isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$division_info = null;

// Verificar si viene de una división específica
$division_param = $_GET['division'] ?? '';
if (!empty($division_param)) {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Mapear nombres de división
    $division_map = [
        'jefatura_cuerpo' => 'Jefatura de Cuerpo',
        'jefatura_estudios' => 'Jefatura de Estudios',
        'servicios_medicos' => 'Servicios Médicos',
        'ayudantia' => 'Ayudantía'
    ];
    
    if (isset($division_map[$division_param])) {
        $division_info = [
            'key' => $division_param,
            'name' => $division_map[$division_param]
        ];
    }
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor complete todos los campos';
    } else {
        if ($auth->login($username, $password)) {
            // Login exitoso - redirigir al dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
}

$page_title = 'Sistema ESyA - Iniciar Sesión';
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
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <!-- Header unificado para navegación -->
    <?php include 'includes/unified_header.php'; ?>

    <div class="login-wrapper">
        <div class="login-container">
            
            <?php if ($division_info): ?>
                <div class="division-info">
                    <h3><?= htmlspecialchars($division_info['name']) ?></h3>
                    <p>Escuela de Suboficiales y Agentes</p>
                </div>
            <?php endif; ?>

            <div class="login-header">
                <h1>Iniciar Sesión</h1>
                <p>Ingrese sus credenciales para acceder al sistema</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" 
                            id="username" 
                            name="username" 
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            required 
                            autofocus
                            autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" 
                            id="password" 
                            name="password" 
                            required
                            autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </form>

            <div class="back-link">
                <?php if ($division_info): ?>
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