<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: ../../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $estado = $_POST['estado'];
    $fecha_ingreso = $_POST['fecha_ingreso'];

    // Validaciones básicas
    if (empty($dni) || empty($nombre) || empty($apellido)) {
        $error = 'DNI, nombre y apellido son obligatorios';
    } else {
        $stmt = $conn->prepare("INSERT INTO aspirantes (dni, nombre, apellido, fecha_nacimiento, direccion, telefono, email, estado, fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $dni, $nombre, $apellido, $fecha_nacimiento, $direccion, $telefono, $email, $estado, $fecha_ingreso);

        if ($stmt->execute()) {
            $success = 'Aspirante registrado correctamente';
            $_POST = array(); // Limpiar el formulario
        } else {
            $error = 'Error al registrar el aspirante: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Nuevo Aspirante</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <h1>Registrar Nuevo Aspirante</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="dni">DNI *</label>
                    <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($_POST['dni'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($_POST['fecha_nacimiento'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de Ingreso</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo htmlspecialchars($_POST['fecha_ingreso'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo" <?php echo (($_POST['estado'] ?? '') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo (($_POST['estado'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="graduado" <?php echo (($_POST['estado'] ?? '') === 'graduado') ? 'selected' : ''; ?>>Graduado</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="index.php" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
</body>

</html>