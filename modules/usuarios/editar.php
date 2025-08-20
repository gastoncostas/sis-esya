<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/database.php';

$auth = new Auth();

// Solo permitir acceso a administradores
if (!$auth->isLoggedIn() || $auth->getUserInfo()['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

// Obtener ID del usuario a editar
$userId = $_GET['id'] ?? 0;
$userData = [
    'username' => '',
    'nombre_completo' => '',
    'email' => '',
    'rol' => 'operador'
];

// Cargar datos del usuario
if ($userId) {
    $stmt = $conn->prepare("SELECT username, nombre_completo, email, rol FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $userData = $result->fetch_assoc();
    } else {
        $error = "Usuario no encontrado";
    }
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = trim($_POST['rol'] ?? 'operador');
    $password = trim($_POST['password'] ?? '');

    // Validaciones
    if (empty($nombre) || empty($email)) {
        $error = "Nombre y email son requeridos";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido";
    } else {
        try {
            // Actualizar con o sin contraseña
            if (empty($password)) {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, rol = ? WHERE id = ?");
                $stmt->bind_param("sssi", $nombre, $email, $rol, $userId);
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, rol = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $nombre, $email, $rol, $password, $userId);
            }

            if ($stmt->execute()) {
                $success = "Usuario actualizado correctamente";
                // Recargar datos
                $stmt = $conn->prepare("SELECT username, nombre_completo, email, rol FROM usuarios WHERE id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $userData = $result->fetch_assoc();
            } else {
                $error = "Error al actualizar el usuario: " . $conn->error;
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(APP_NAME); ?> - Editar Usuario</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <h1>Editar Usuario</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" class="form-container">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($userData['username']); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($userData['nombre_completo']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="admin" <?php echo ($userData['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="operador" <?php echo ($userData['rol'] === 'operador') ? 'selected' : ''; ?>>Operador</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                <input type="password" id="password" name="password">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="../usuarios/" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
</body>

</html>