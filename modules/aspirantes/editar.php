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
$aspirante = null;

// Obtener ID del aspirante a editar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Cargar datos del aspirante
$stmt = $conn->prepare("SELECT * FROM aspirantes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$aspirante = $result->fetch_assoc();
$stmt->close();

// Función auxiliar para manejar valores nulos en htmlspecialchars
function safe_html($value) {
    return $value !== null ? htmlspecialchars($value) : '';
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $domicilio = trim($_POST['domicilio']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $estado = $_POST['estado'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $lugar_nacimiento = trim($_POST['lugar_nacimiento']);
    $estado_civil = $_POST['estado_civil'];
    $nivel_educativo = $_POST['nivel_educativo'];
    $observaciones = trim($_POST['observaciones']);

    // Validaciones básicas
    if (empty($dni) || empty($nombre) || empty($apellido)) {
        $error = 'DNI, nombre y apellido son obligatorios';
    } else {
        // Verificar si el DNI ya existe (excluyendo el actual)
        $checkDni = $conn->prepare("SELECT id FROM aspirantes WHERE dni = ? AND id != ?");
        $checkDni->bind_param("si", $dni, $id);
        $checkDni->execute();
        $checkDni->store_result();
        
        if ($checkDni->num_rows > 0) {
            $error = 'El DNI ya está registrado en el sistema';
        } else {
            // Actualizar datos del aspirante
            $stmt = $conn->prepare("UPDATE aspirantes SET dni = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, domicilio = ?, telefono = ?, email = ?, estado = ?, fecha_ingreso = ?, lugar_nacimiento = ?, estado_civil = ?, nivel_educativo = ?, observaciones = ? WHERE id = ?");
            
            // Manejar valores nulos para la base de datos
            $fecha_nacimiento = empty($fecha_nacimiento) ? null : $fecha_nacimiento;
            $fecha_ingreso = empty($fecha_ingreso) ? null : $fecha_ingreso;
            $domicilio = empty($domicilio) ? null : $domicilio;
            $telefono = empty($telefono) ? null : $telefono;
            $email = empty($email) ? null : $email;
            $lugar_nacimiento = empty($lugar_nacimiento) ? null : $lugar_nacimiento;
            $estado_civil = empty($estado_civil) ? null : $estado_civil;
            $nivel_educativo = empty($nivel_educativo) ? null : $nivel_educativo;
            $observaciones = empty($observaciones) ? null : $observaciones;
            
            $stmt->bind_param("sssssssssssssi", $dni, $nombre, $apellido, $fecha_nacimiento, $domicilio, $telefono, $email, $estado, $fecha_ingreso, $lugar_nacimiento, $estado_civil, $nivel_educativo, $observaciones, $id);

            if ($stmt->execute()) {
                $success = 'Aspirante actualizado correctamente';
                // Recargar datos actualizados
                $stmt_reload = $conn->prepare("SELECT * FROM aspirantes WHERE id = ?");
                $stmt_reload->bind_param("i", $id);
                $stmt_reload->execute();
                $result_reload = $stmt_reload->get_result();
                $aspirante = $result_reload->fetch_assoc();
                $stmt_reload->close();
            } else {
                $error = 'Error al actualizar el aspirante: ' . $stmt->error;
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
    <title><?php echo APP_NAME; ?> - Editar Aspirante</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
    <link rel="stylesheet" href="../../assets/css/editar_asp.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="back-link">
            <a href="index.php">← Volver al listado</a>
        </div>

        <h1>Editar Aspirante</h1>

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
                    <input type="text" id="dni" name="dni" value="<?php echo safe_html($aspirante['dni']); ?>" required maxlength="20">
                </div>

                <div class="form-group">
                    <label for="nombre" class="required">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo safe_html($aspirante['nombre']); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="apellido" class="required">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo safe_html($aspirante['apellido']); ?>" required maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo safe_html($aspirante['fecha_nacimiento']); ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de Ingreso</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo safe_html($aspirante['fecha_ingreso']); ?>">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo" <?php echo ($aspirante['estado'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo ($aspirante['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="graduado" <?php echo ($aspirante['estado'] === 'graduado') ? 'selected' : ''; ?>>Graduado</option>
                        <option value="baja" <?php echo ($aspirante['estado'] === 'baja') ? 'selected' : ''; ?>>Baja</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="domicilio">Domicilio</label>
                <input type="text" id="domicilio" name="domicilio" value="<?php echo safe_html($aspirante['domicilio']); ?>" maxlength="300">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo safe_html($aspirante['telefono']); ?>" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo safe_html($aspirante['email']); ?>" maxlength="150">
                </div>
            </div>

            <div class="form-group">
                <label for="lugar_nacimiento">Lugar de Nacimiento</label>
                <input type="text" id="lugar_nacimiento" name="lugar_nacimiento" value="<?php echo safe_html($aspirante['lugar_nacimiento']); ?>" maxlength="200">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="estado_civil">Estado Civil</label>
                    <select id="estado_civil" name="estado_civil">
                        <option value="">Seleccionar</option>
                        <option value="soltero" <?php echo ($aspirante['estado_civil'] === 'soltero') ? 'selected' : ''; ?>>Soltero/a</option>
                        <option value="casado" <?php echo ($aspirante['estado_civil'] === 'casado') ? 'selected' : ''; ?>>Casado/a</option>
                        <option value="divorciado" <?php echo ($aspirante['estado_civil'] === 'divorciado') ? 'selected' : ''; ?>>Divorciado/a</option>
                        <option value="viudo" <?php echo ($aspirante['estado_civil'] === 'viudo') ? 'selected' : ''; ?>>Viudo/a</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nivel_educativo">Nivel Educativo</label>
                    <select id="nivel_educativo" name="nivel_educativo">
                        <option value="">Seleccionar</option>
                        <option value="primario" <?php echo ($aspirante['nivel_educativo'] === 'primario') ? 'selected' : ''; ?>>Primario</option>
                        <option value="secundario" <?php echo ($aspirante['nivel_educativo'] === 'secundario') ? 'selected' : ''; ?>>Secundario</option>
                        <option value="terciario" <?php echo ($aspirante['nivel_educativo'] === 'terciario') ? 'selected' : ''; ?>>Terciario</option>
                        <option value="universitario" <?php echo ($aspirante['nivel_educativo'] === 'universitario') ? 'selected' : ''; ?>>Universitario</option>
                        <option value="posgrado" <?php echo ($aspirante['nivel_educativo'] === 'posgrado') ? 'selected' : ''; ?>>Posgrado</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="4"><?php echo safe_html($aspirante['observaciones']); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Aspirante</button>
                <a href="index.php" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    <script src="../../assets/js/editar_asp.js"></script>
</body>

</html>