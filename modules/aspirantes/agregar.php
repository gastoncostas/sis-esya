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

// Verificar si la tabla aspirantes existe, si no, crearla
$tableCheck = $conn->query("SHOW TABLES LIKE 'aspirantes'");
if ($tableCheck->num_rows == 0) {
    // Crear tabla aspirantes si no existe
    $createTable = "CREATE TABLE IF NOT EXISTS `aspirantes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
        `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `fecha_nacimiento` date NOT NULL,
        `lugar_nacimiento` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `domicilio` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `estado_civil` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `nivel_educativo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
        `fecha_ingreso` date DEFAULT NULL,
        `observaciones` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `dni` (`dni`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->query($createTable);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $domicilio = trim($_POST['domicilio']); // Cambiado de 'direccion' a 'domicilio'
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $estado = $_POST['estado'];
    $fecha_ingreso = $_POST['fecha_ingreso'];

    // Validaciones básicas
    if (empty($dni) || empty($nombre) || empty($apellido)) {
        $error = 'DNI, nombre y apellido son obligatorios';
    } else {
        // Verificar si el DNI ya existe
        $checkDni = $conn->prepare("SELECT id FROM aspirantes WHERE dni = ?");
        $checkDni->bind_param("s", $dni);
        $checkDni->execute();
        $checkDni->store_result();
        
        if ($checkDni->num_rows > 0) {
            $error = 'El DNI ya está registrado en el sistema';
        } else {
            // Insertar con los nombres de columna correctos
            $stmt = $conn->prepare("INSERT INTO aspirantes (dni, nombre, apellido, fecha_nacimiento, domicilio, telefono, email, estado, fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $dni, $nombre, $apellido, $fecha_nacimiento, $domicilio, $telefono, $email, $estado, $fecha_ingreso);

            if ($stmt->execute()) {
                $success = 'Aspirante registrado correctamente';
                $_POST = array(); // Limpiar el formulario
            } else {
                $error = 'Error al registrar el aspirante: ' . $stmt->error;
            }
        }
        $checkDni->close();
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
    <link rel="stylesheet" href="../../assets/css/agregar_asp.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <h1>Registrar Nuevo Aspirante</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="dni" class="required">DNI</label>
                    <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($_POST['dni'] ?? ''); ?>" required maxlength="20">
                </div>

                <div class="form-group">
                    <label for="nombre" class="required">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="apellido" class="required">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>" required maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($_POST['fecha_nacimiento'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de Ingreso</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo htmlspecialchars($_POST['fecha_ingreso'] ?? date('Y-m-d')); ?>">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo" <?php echo (($_POST['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo (($_POST['estado'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="graduado" <?php echo (($_POST['estado'] ?? '') === 'graduado') ? 'selected' : ''; ?>>Graduado</option>
                        <option value="baja" <?php echo (($_POST['estado'] ?? '') === 'baja') ? 'selected' : ''; ?>>Baja</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="domicilio">Domicilio</label> <!-- Cambiado de 'direccion' a 'domicilio' -->
                <input type="text" id="domicilio" name="domicilio" value="<?php echo htmlspecialchars($_POST['domicilio'] ?? ''); ?>" maxlength="300">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" maxlength="150">
                </div>
            </div>

            <div class="form-group">
                <label for="lugar_nacimiento">Lugar de Nacimiento</label>
                <input type="text" id="lugar_nacimiento" name="lugar_nacimiento" value="<?php echo htmlspecialchars($_POST['lugar_nacimiento'] ?? ''); ?>" maxlength="200">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="estado_civil">Estado Civil</label>
                    <select id="estado_civil" name="estado_civil">
                        <option value="">Seleccionar</option>
                        <option value="soltero" <?php echo (($_POST['estado_civil'] ?? '') === 'soltero') ? 'selected' : ''; ?>>Soltero/a</option>
                        <option value="casado" <?php echo (($_POST['estado_civil'] ?? '') === 'casado') ? 'selected' : ''; ?>>Casado/a</option>
                        <option value="divorciado" <?php echo (($_POST['estado_civil'] ?? '') === 'divorciado') ? 'selected' : ''; ?>>Divorciado/a</option>
                        <option value="viudo" <?php echo (($_POST['estado_civil'] ?? '') === 'viudo') ? 'selected' : ''; ?>>Viudo/a</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nivel_educativo">Nivel Educativo</label>
                    <select id="nivel_educativo" name="nivel_educativo">
                        <option value="">Seleccionar</option>
                        <option value="primario" <?php echo (($_POST['nivel_educativo'] ?? '') === 'primario') ? 'selected' : ''; ?>>Primario</option>
                        <option value="secundario" <?php echo (($_POST['nivel_educativo'] ?? '') === 'secundario') ? 'selected' : ''; ?>>Secundario</option>
                        <option value="terciario" <?php echo (($_POST['nivel_educativo'] ?? '') === 'terciario') ? 'selected' : ''; ?>>Terciario</option>
                        <option value="universitario" <?php echo (($_POST['nivel_educativo'] ?? '') === 'universitario') ? 'selected' : ''; ?>>Universitario</option>
                        <option value="posgrado" <?php echo (($_POST['nivel_educativo'] ?? '') === 'posgrado') ? 'selected' : ''; ?>>Posgrado</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="4"><?php echo htmlspecialchars($_POST['observaciones'] ?? ''); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Aspirante</button>
                <a href="index.php" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>


</body>

</html>