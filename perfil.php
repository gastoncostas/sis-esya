<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/database.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$user = $auth->getUserInfo();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validaciones básicas
    if (empty($nombre)) {
        $error = "El nombre completo es requerido";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } else {
        try {
            // Actualizar datos básicos
            if (empty($password)) {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $nombre, $email, $user['id']);
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $nombre, $email, $password, $user['id']);
            }

            if ($stmt->execute()) {
                // Actualizar datos de sesión
                $_SESSION['nombre_completo'] = $nombre;
                $success = "Perfil actualizado correctamente";

                // Refrescar datos del usuario
                $user = $auth->getUserInfo();
            } else {
                $error = "Error al actualizar el perfil: " . $conn->error;
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Obtener datos actualizados del usuario con manejo seguro
$userData = [
    'username' => '',
    'email' => '',
    'nombre_completo' => ''
];

try {
    $stmt = $conn->prepare("SELECT username, email, nombre_completo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $userData = $result->fetch_assoc();
    }
} catch (Exception $e) {
    $error = "Error al cargar datos del usuario: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(APP_NAME); ?> - Mi Perfil</title>
    <link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/unified_header_footer.css">
</head>

<body>
    <?php include 'includes/unified_header.php'; ?>

    <div class="container">
        <h1>Mi Perfil</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" class="profile-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" disabled>
                    <small class="text-muted">No se puede cambiar el nombre de usuario</small>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($userData['nombre_completo'] ?? ''); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar nueva contraseña">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <?php include 'includes/unified_footer.php'; ?>
</body>

</html>