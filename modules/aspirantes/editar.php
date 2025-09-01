<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/database.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: ../../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';
$cursante = null;

// Obtener ID del cursante a editar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Cargar datos del cursante
$stmt = $conn->prepare("SELECT * FROM cursante WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$cursante = $result->fetch_assoc();
$stmt->close();

// Función auxiliar para manejar valores nulos en htmlspecialchars
function safe_html($value)
{
    return $value !== null ? htmlspecialchars($value) : '';
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $estado_civil = $_POST['estado_civil'];
    $hijos = intval($_POST['hijos']);
    $nombre_hijos = trim($_POST['nombre_hijos']);
    $nombre_padre = trim($_POST['nombre_padre']);
    $nombre_madre = trim($_POST['nombre_madre']);
    $vive_padre = isset($_POST['vive_padre']) ? 1 : 0;
    $vive_madre = isset($_POST['vive_madre']) ? 1 : 0;
    $direccion_real = trim($_POST['direccion_real']);
    $depto = $_POST['depto'];
    $localidad = $_POST['localidad'];
    $cod_postal = trim($_POST['cod_postal']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $nombre_fam_directo = trim($_POST['nombre_fam_directo']);
    $tel_fam_directo = trim($_POST['tel_fam_directo']);
    $parentezco = trim($_POST['parentezco']);
    $comision = $_POST['comision'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $sit_revista = trim($_POST['sit_revista']);
    $novedades = trim($_POST['novedades']);
    $estado = $_POST['estado'];

    // Validaciones básicas
    if (empty($dni) || empty($nombre) || empty($apellido)) {
        $error = 'DNI, nombre y apellido son obligatorios';
    } else {
        // Verificar si el DNI ya existe (excluyendo el actual)
        $checkDni = $conn->prepare("SELECT id FROM cursante WHERE dni = ? AND id != ?");
        $checkDni->bind_param("si", $dni, $id);
        $checkDni->execute();
        $checkDni->store_result();

        if ($checkDni->num_rows > 0) {
            $error = 'El DNI ya está registrado en el sistema';
        } else {
            // Actualizar datos del cursante
            $stmt = $conn->prepare("UPDATE cursante SET 
                dni = ?, nombre = ?, apellido = ?, fecha_nacimiento = ?, 
                estado_civil = ?, hijos = ?, nombre_hijos = ?, nombre_padre = ?, 
                nombre_madre = ?, vive_padre = ?, vive_madre = ?, direccion_real = ?, 
                depto = ?, localidad = ?, cod_postal = ?, telefono = ?, email = ?, 
                nombre_fam_directo = ?, tel_fam_directo = ?, parentezco = ?, 
                comision = ?, fecha_ingreso = ?, sit_revista = ?, novedades = ?, estado = ? 
                WHERE id = ?");

            // Manejar valores nulos para la base de datos
            $fecha_nacimiento = empty($fecha_nacimiento) ? null : $fecha_nacimiento;
            $fecha_ingreso = empty($fecha_ingreso) ? null : $fecha_ingreso;
            $estado_civil = empty($estado_civil) ? null : $estado_civil;
            $nombre_hijos = empty($nombre_hijos) ? null : $nombre_hijos;
            $nombre_padre = empty($nombre_padre) ? null : $nombre_padre;
            $nombre_madre = empty($nombre_madre) ? null : $nombre_madre;
            $direccion_real = empty($direccion_real) ? null : $direccion_real;
            $depto = empty($depto) ? null : $depto;
            $localidad = empty($localidad) ? null : $localidad;
            $cod_postal = empty($cod_postal) ? null : $cod_postal;
            $telefono = empty($telefono) ? null : $telefono;
            $email = empty($email) ? null : $email;
            $nombre_fam_directo = empty($nombre_fam_directo) ? null : $nombre_fam_directo;
            $tel_fam_directo = empty($tel_fam_directo) ? null : $tel_fam_directo;
            $parentezco = empty($parentezco) ? null : $parentezco;
            $sit_revista = empty($sit_revista) ? null : $sit_revista;
            $novedades = empty($novedades) ? null : $novedades;

            $stmt->bind_param(
                "sssssisssiissssssssssssssi",
                $dni,
                $nombre,
                $apellido,
                $fecha_nacimiento,
                $estado_civil,
                $hijos,
                $nombre_hijos,
                $nombre_padre,
                $nombre_madre,
                $vive_padre,
                $vive_madre,
                $direccion_real,
                $depto,
                $localidad,
                $cod_postal,
                $telefono,
                $email,
                $nombre_fam_directo,
                $tel_fam_directo,
                $parentezco,
                $comision,
                $fecha_ingreso,
                $sit_revista,
                $novedades,
                $estado,
                $id
            );

            if ($stmt->execute()) {
                $success = 'Cursante actualizado correctamente';
                // Recargar datos actualizados
                $stmt_reload = $conn->prepare("SELECT * FROM cursante WHERE id = ?");
                $stmt_reload->bind_param("i", $id);
                $stmt_reload->execute();
                $result_reload = $stmt_reload->get_result();
                $cursante = $result_reload->fetch_assoc();
                $stmt_reload->close();
            } else {
                $error = 'Error al actualizar el cursante: ' . $stmt->error;
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
    <title><?php echo APP_NAME; ?> - Editar Cursante</title>
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

        <h1>Editar Cursante</h1>

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
                    <input type="text" id="dni" name="dni" value="<?php echo safe_html($cursante['dni']); ?>" required maxlength="20">
                </div>

                <div class="form-group">
                    <label for="nombre" class="required">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo safe_html($cursante['nombre']); ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="apellido" class="required">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo safe_html($cursante['apellido']); ?>" required maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo safe_html($cursante['fecha_nacimiento']); ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de Ingreso</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo safe_html($cursante['fecha_ingreso']); ?>">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo" <?php echo ($cursante['estado'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo ($cursante['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        <option value="graduado" <?php echo ($cursante['estado'] === 'graduado') ? 'selected' : ''; ?>>Graduado</option>
                        <option value="baja" <?php echo ($cursante['estado'] === 'baja') ? 'selected' : ''; ?>>Baja</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comision" class="required">Comisión</label>
                    <select id="comision" name="comision" required>
                        <option value="A" <?php echo ($cursante['comision'] === 'A') ? 'selected' : ''; ?>>Comisión A</option>
                        <option value="B" <?php echo ($cursante['comision'] === 'B') ? 'selected' : ''; ?>>Comisión B</option>
                        <option value="C" <?php echo ($cursante['comision'] === 'C') ? 'selected' : ''; ?>>Comisión C</option>
                        <option value="D" <?php echo ($cursante['comision'] === 'D') ? 'selected' : ''; ?>>Comisión D</option>
                        <option value="E" <?php echo ($cursante['comision'] === 'E') ? 'selected' : ''; ?>>Comisión E</option>
                        <option value="F" <?php echo ($cursante['comision'] === 'F') ? 'selected' : ''; ?>>Comisión F</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="estado_civil">Estado Civil</label>
                    <select id="estado_civil" name="estado_civil">
                        <option value="">Seleccionar</option>
                        <option value="soltero" <?php echo ($cursante['estado_civil'] === 'soltero') ? 'selected' : ''; ?>>Soltero/a</option>
                        <option value="casado" <?php echo ($cursante['estado_civil'] === 'casado') ? 'selected' : ''; ?>>Casado/a</option>
                        <option value="divorciado" <?php echo ($cursante['estado_civil'] === 'divorciado') ? 'selected' : ''; ?>>Divorciado/a</option>
                        <option value="viudo" <?php echo ($cursante['estado_civil'] === 'viudo') ? 'selected' : ''; ?>>Viudo/a</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hijos">Número de Hijos</label>
                    <input type="number" id="hijos" name="hijos" value="<?php echo safe_html($cursante['hijos']); ?>" min="0">
                </div>

                <div class="form-group">
                    <label for="nombre_hijos">Nombres de Hijos</label>
                    <textarea id="nombre_hijos" name="nombre_hijos" rows="2"><?php echo safe_html($cursante['nombre_hijos']); ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_padre">Nombre del Padre</label>
                    <input type="text" id="nombre_padre" name="nombre_padre" value="<?php echo safe_html($cursante['nombre_padre']); ?>" maxlength="200">
                </div>

                <div class="form-group">
                    <label for="nombre_madre">Nombre de la Madre</label>
                    <input type="text" id="nombre_madre" name="nombre_madre" value="<?php echo safe_html($cursante['nombre_madre']); ?>" maxlength="200">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="vive_padre">¿Vive el padre?</label>
                    <input type="checkbox" id="vive_padre" name="vive_padre" value="1" <?php echo ($cursante['vive_padre'] == 1) ? 'checked' : ''; ?>>
                </div>

                <div class="form-group">
                    <label for="vive_madre">¿Vive la madre?</label>
                    <input type="checkbox" id="vive_madre" name="vive_madre" value="1" <?php echo ($cursante['vive_madre'] == 1) ? 'checked' : ''; ?>>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion_real">Dirección Real</label>
                <input type="text" id="direccion_real" name="direccion_real" value="<?php echo safe_html($cursante['direccion_real']); ?>" maxlength="300">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="depto">Departamento</label>
                    <select id="depto" name="depto">
                        <option value="">Seleccionar</option>
                        <option value="CAPITAL" <?php echo ($cursante['depto'] === 'CAPITAL') ? 'selected' : ''; ?>>CAPITAL</option>
                        <option value="BURRUYACÚ" <?php echo ($cursante['depto'] === 'BURRUYACÚ') ? 'selected' : ''; ?>>BURRUYACÚ</option>
                        <option value="CHICLIGASTA" <?php echo ($cursante['depto'] === 'CHICLIGASTA') ? 'selected' : ''; ?>>CHICLIGASTA</option>
                        <option value="CRUZ ALTA" <?php echo ($cursante['depto'] === 'CRUZ ALTA') ? 'selected' : ''; ?>>CRUZ ALTA</option>
                        <option value="FAMAILLÁ" <?php echo ($cursante['depto'] === 'FAMAILLÁ') ? 'selected' : ''; ?>>FAMAILLÁ</option>
                        <option value="GRANEROS" <?php echo ($cursante['depto'] === 'GRANEROS') ? 'selected' : ''; ?>>GRANEROS</option>
                        <option value="J.B. ALBERDI" <?php echo ($cursante['depto'] === 'J.B. ALBERDI') ? 'selected' : ''; ?>>J.B. ALBERDI</option>
                        <option value="LA COCHA" <?php echo ($cursante['depto'] === 'LA COCHA') ? 'selected' : ''; ?>>LA COCHA</option>
                        <option value="LEALES" <?php echo ($cursante['depto'] === 'LEALES') ? 'selected' : ''; ?>>LEALES</option>
                        <option value="LULES" <?php echo ($cursante['depto'] === 'LULES') ? 'selected' : ''; ?>>LULES</option>
                        <option value="MONTEROS" <?php echo ($cursante['depto'] === 'MONTEROS') ? 'selected' : ''; ?>>MONTEROS</option>
                        <option value="RÍO CHICO" <?php echo ($cursante['depto'] === 'RÍO CHICO') ? 'selected' : ''; ?>>RÍO CHICO</option>
                        <option value="SIMOCA" <?php echo ($cursante['depto'] === 'SIMOCa') ? 'selected' : ''; ?>>SIMOCA</option>
                        <option value="TAFÍ DEL VALLE" <?php echo ($cursante['depto'] === 'TAFÍ DEL VALLE') ? 'selected' : ''; ?>>TAFÍ DEL VALLE</option>
                        <option value="TAFÍ VIEJO" <?php echo ($cursante['depto'] === 'TAFÍ VIEJO') ? 'selected' : ''; ?>>TAFÍ VIEJO</option>
                        <option value="TRANCAS" <?php echo ($cursante['depto'] === 'TRANCAS') ? 'selected' : ''; ?>>TRANCAS</option>
                        <option value="YERBA BUENA" <?php echo ($cursante['depto'] === 'YERBA BUENA') ? 'selected' : ''; ?>>YERBA BUENA</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="localidad">Localidad</label>
                    <input type="text" id="localidad" name="localidad" value="<?php echo safe_html($cursante['localidad']); ?>" maxlength="100">
                </div>

                <div class="form-group">
                    <label for="cod_postal">Código Postal</label>
                    <input type="text" id="cod_postal" name="cod_postal" value="<?php echo safe_html($cursante['cod_postal']); ?>" maxlength="10">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo safe_html($cursante['telefono']); ?>" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo safe_html($cursante['email']); ?>" maxlength="150">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_fam_directo">Nombre Familiar Directo</label>
                    <input type="text" id="nombre_fam_directo" name="nombre_fam_directo" value="<?php echo safe_html($cursante['nombre_fam_directo']); ?>" maxlength="200">
                </div>

                <div class="form-group">
                    <label for="tel_fam_directo">Teléfono Familiar</label>
                    <input type="text" id="tel_fam_directo" name="tel_fam_directo" value="<?php echo safe_html($cursante['tel_fam_directo']); ?>" maxlength="50">
                </div>

                <div class="form-group">
                    <label for="parentezco">Parentezco</label>
                    <input type="text" id="parentezco" name="parentezco" value="<?php echo safe_html($cursante['parentezco']); ?>" maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="sit_revista">Situación de Revista</label>
                    <input type="text" id="sit_revista" name="sit_revista" value="<?php echo safe_html($cursante['sit_revista']); ?>" maxlength="100">
                </div>
            </div>

            <div class="form-group">
                <label for="novedades">Novedades</label>
                <textarea id="novedades" name="novedades" rows="4"><?php echo safe_html($cursante['novedades']); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Cursante</button>
                <a href="index.php" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    <script src="../../assets/js/editar_asp.js"></script>
</body>

</html>