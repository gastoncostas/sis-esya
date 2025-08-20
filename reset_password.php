<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/database.php';

$auth = new Auth();
$error = '';
$success = '';

if ($auth->isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE token_reset = ? AND token_expira > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $error = "Enlace inválido o expirado";
    }
} else {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($password) || strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } elseif ($password !== $confirm) {
        $error = "Las contraseñas no coinciden";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $update = $conn->prepare("UPDATE usuarios SET password = ?, token_reset = NULL, token_expira = NULL WHERE token_reset = ?");
        $update->bind_param("ss", $hashedPassword, $token);

        if ($update->execute()) {
            $success = "Contraseña actualizada correctamente. <a href='login.php'>Iniciar sesión</a>";
        } else {
            $error = "Error al actualizar la contraseña";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Restablecer Contraseña</title>
    <link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/unified_header_footer.css">
</head>

<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="assets/img/logo-policia.png" alt="Logo Policía de Tucumán">
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php else: ?>

            <form method="POST" action="" class="login-form">
                <input type="hidden" name="reset" value="1">

                <div class="form-group">
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Restablecer Contraseña</button>
            </form>

        <?php endif; ?>
    </div>
</body>

</html>