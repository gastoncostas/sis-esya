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

// Obtener departamentos de la base de datos
$deptQuery = "SELECT id, nombre FROM departamentos ORDER BY nombre";
$deptResult = $conn->query($deptQuery);
$departamentos = [];
while ($row = $deptResult->fetch_assoc()) {
    $departamentos[] = $row;
}

// Obtener localidades de la base de datos
$localQuery = "SELECT id, nombre, departamento_id FROM localidades ORDER BY nombre";
$localResult = $conn->query($localQuery);
$localidades = [];
while ($row = $localResult->fetch_assoc()) {
    $localidades[] = $row;
}

// Obtener comisiones de la base de datos
$comQuery = "SELECT id, codigo, descripcion FROM comisiones ORDER BY codigo";
$comResult = $conn->query($comQuery);
$comisiones = [];
while ($row = $comResult->fetch_assoc()) {
    $comisiones[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $estado_civil = !empty($_POST['estado_civil']) ? $_POST['estado_civil'] : null;
    $nivel_educativo = !empty($_POST['nivel_educativo']) ? $_POST['nivel_educativo'] : 'SECUNDARIO';
    $hijos = intval($_POST['hijos']);
    $direccion_real = !empty(trim($_POST['direccion_real'])) ? trim($_POST['direccion_real']) : null;
    $departamento_id = !empty($_POST['departamento_id']) ? intval($_POST['departamento_id']) : null;
    $localidad_id = !empty($_POST['localidad_id']) ? intval($_POST['localidad_id']) : null;
    $cod_postal = !empty(trim($_POST['cod_postal'])) ? trim($_POST['cod_postal']) : null;
    $telefono = !empty(trim($_POST['telefono'])) ? trim($_POST['telefono']) : null;
    $email = !empty(trim($_POST['email'])) ? trim($_POST['email']) : null;
    $nombre_padre = !empty(trim($_POST['nombre_padre'])) ? trim($_POST['nombre_padre']) : null;
    $nombre_madre = !empty(trim($_POST['nombre_madre'])) ? trim($_POST['nombre_madre']) : null;
    $vive_padre = isset($_POST['vive_padre']) ? 1 : 0;
    $vive_madre = isset($_POST['vive_madre']) ? 1 : 0;
    $nombre_fam_directo = !empty(trim($_POST['nombre_fam_directo'])) ? trim($_POST['nombre_fam_directo']) : null;
    $tel_fam_directo = !empty(trim($_POST['tel_fam_directo'])) ? trim($_POST['tel_fam_directo']) : null;
    $parentezco = !empty(trim($_POST['parentezco'])) ? trim($_POST['parentezco']) : null;
    $comision_id = !empty($_POST['comision_id']) ? intval($_POST['comision_id']) : null;
    $fecha_ingreso = !empty($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : null;
    $sit_revista = !empty($_POST['sit_revista']) ? $_POST['sit_revista'] : 'ACTIVO';
    $novedades = !empty(trim($_POST['novedades'])) ? trim($_POST['novedades']) : null;
    $estado = !empty($_POST['estado']) ? $_POST['estado'] : 'ASPIRANTE';

    // Validaciones básicas
    if (empty($dni) || empty($nombre) || empty($apellido)) {
        $error = 'DNI, nombre y apellido son obligatorios';
    } elseif (!empty($fecha_nacimiento) && !DateTime::createFromFormat('Y-m-d', $fecha_nacimiento)) {
        $error = 'Formato de fecha de nacimiento inválido';
    } elseif (!empty($fecha_ingreso) && !DateTime::createFromFormat('Y-m-d', $fecha_ingreso)) {
        $error = 'Formato de fecha de ingreso inválido';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Formato de email inválido';
    } else {
        // Verificar si el DNI ya existe
        $checkDni = $conn->prepare("SELECT id FROM cursantes WHERE dni = ?");
        $checkDni->bind_param("s", $dni);
        $checkDni->execute();
        $checkDni->store_result();

        if ($checkDni->num_rows > 0) {
            $error = 'El DNI ya está registrado en el sistema';
        } else {
            // Insertar nuevo cursante
            $stmt = $conn->prepare("INSERT INTO cursantes 
                (dni, apellido, nombre, fecha_nacimiento, estado_civil, nivel_educativo, hijos, 
                direccion_real, departamento_id, localidad_id, cod_postal, telefono, email, 
                nombre_padre, nombre_madre, vive_padre, vive_madre, nombre_fam_directo, 
                tel_fam_directo, parentezco, comision_id, fecha_ingreso, sit_revista, 
                novedades, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "ssssssisiisssssiisssissss",
                $dni,
                $apellido,
                $nombre,
                $fecha_nacimiento,
                $estado_civil,
                $nivel_educativo,
                $hijos,
                $direccion_real,
                $departamento_id,
                $localidad_id,
                $cod_postal,
                $telefono,
                $email,
                $nombre_padre,
                $nombre_madre,
                $vive_padre,
                $vive_madre,
                $nombre_fam_directo,
                $tel_fam_directo,
                $parentezco,
                $comision_id,
                $fecha_ingreso,
                $sit_revista,
                $novedades,
                $estado
            );

            if ($stmt->execute()) {
                $success = 'Cursante agregado correctamente';
                // Limpiar campos después de agregar exitosamente
                $_POST = array();
            } else {
                $error = 'Error al agregar cursante: ' . $stmt->error;
            }

            $stmt->close();
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
    <title><?php echo APP_NAME; ?> - Agregar Cursante</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/agregar_cur.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="back-link">
            <a href="index.php">← Volver al listado</a>
        </div>

        <h1>Agregar Cursante</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="dni" class="required">DNI</label>
                    <input type="text" id="dni" name="dni" value="<?php echo isset($_POST['dni']) ? htmlspecialchars($_POST['dni']) : ''; ?>" required maxlength="8" pattern="[0-9]{7,8}">
                    <small>Solo números, 7-8 dígitos</small>
                </div>

                <div class="form-group">
                    <label for="nombre" class="required">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required maxlength="100">
                </div>

                <div class="form-group">
                    <label for="apellido" class="required">Apellido</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>" required maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo isset($_POST['fecha_nacimiento']) ? htmlspecialchars($_POST['fecha_nacimiento']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de Ingreso</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="<?php echo isset($_POST['fecha_ingreso']) ? htmlspecialchars($_POST['fecha_ingreso']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="ASPIRANTE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'ASPIRANTE') ? 'selected' : ''; ?>>Aspirante</option>
                        <option value="SUPLENTE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SUPLENTE') ? 'selected' : ''; ?>>Suplente</option>
                        <option value="CURSANTE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'CURSANTE') ? 'selected' : ''; ?>>Cursante</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comision_id">Comisión</label>
                    <select id="comision_id" name="comision_id">
                        <option value="">Seleccionar</option>
                        <?php foreach ($comisiones as $comision): ?>
                            <option value="<?php echo $comision['id']; ?>" <?php echo (isset($_POST['comision_id']) && $_POST['comision_id'] == $comision['id']) ? 'selected' : ''; ?>>
                                Comisión <?php echo htmlspecialchars($comision['codigo']); ?>
                                <?php if (!empty($comision['descripcion'])): ?>
                                    <?php echo htmlspecialchars($comision['descripcion']); ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="estado_civil">Estado Civil</label>
                    <select id="estado_civil" name="estado_civil">
                        <option value="">Seleccionar</option>
                        <option value="SOLTERO/A" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'SOLTERO/A') ? 'selected' : ''; ?>>Soltero/a</option>
                        <option value="CASADO/A" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'CASADO/A') ? 'selected' : ''; ?>>Casado/a</option>
                        <option value="DIVORCIADO/A" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'DIVORCIADO/A') ? 'selected' : ''; ?>>Divorciado/a</option>
                        <option value="VIUDO/A" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'VIUDO/A') ? 'selected' : ''; ?>>Viudo/a</option>
                        <option value="CONCUBINATO" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'CONCUBINATO') ? 'selected' : ''; ?>>Concubinato</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nivel_educativo">Nivel Educativo</label>
                    <select id="nivel_educativo" name="nivel_educativo">
                        <option value="SECUNDARIO" <?php echo (isset($_POST['nivel_educativo']) && $_POST['nivel_educativo'] == 'SECUNDARIO') ? 'selected' : ''; ?>>Secundario</option>
                        <option value="TERCIARIO" <?php echo (isset($_POST['nivel_educativo']) && $_POST['nivel_educativo'] == 'TERCIARIO') ? 'selected' : ''; ?>>Terciario</option>
                        <option value="UNIVERSITARIO" <?php echo (isset($_POST['nivel_educativo']) && $_POST['nivel_educativo'] == 'UNIVERSITARIO') ? 'selected' : ''; ?>>Universitario</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hijos">Número de Hijos</label>
                    <input type="number" id="hijos" name="hijos" value="<?php echo isset($_POST['hijos']) ? htmlspecialchars($_POST['hijos']) : 0; ?>" min="0" max="20">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_padre">Nombre del Padre</label>
                    <input type="text" id="nombre_padre" name="nombre_padre" value="<?php echo isset($_POST['nombre_padre']) ? htmlspecialchars($_POST['nombre_padre']) : ''; ?>" maxlength="200">
                </div>

                <div class="form-group">
                    <label for="nombre_madre">Nombre de la Madre</label>
                    <input type="text" id="nombre_madre" name="nombre_madre" value="<?php echo isset($_POST['nombre_madre']) ? htmlspecialchars($_POST['nombre_madre']) : ''; ?>" maxlength="200">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="vive_padre">¿Vive el padre?</label>
                    <input type="checkbox" id="vive_padre" name="vive_padre" value="1" <?php echo (isset($_POST['vive_padre']) && $_POST['vive_padre'] == 1) ? 'checked' : ''; ?>>
                </div>

                <div class="form-group">
                    <label for="vive_madre">¿Vive la madre?</label>
                    <input type="checkbox" id="vive_madre" name="vive_madre" value="1" <?php echo (isset($_POST['vive_madre']) && $_POST['vive_madre'] == 1) ? 'checked' : ''; ?>>
                </div>
            </div>

            <div class="form-group">
                <label for="direccion_real">Dirección Real</label>
                <input type="text" id="direccion_real" name="direccion_real" value="<?php echo isset($_POST['direccion_real']) ? htmlspecialchars($_POST['direccion_real']) : ''; ?>" maxlength="255">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="departamento_id">Departamento</label>
                    <select id="departamento_id" name="departamento_id">
                        <option value="">Seleccionar</option>
                        <?php foreach ($departamentos as $depto): ?>
                            <option value="<?php echo $depto['id']; ?>" <?php echo (isset($_POST['departamento_id']) && $_POST['departamento_id'] == $depto['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($depto['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="localidad_id">Localidad</label>
                    <select id="localidad_id" name="localidad_id">
                        <option value="">Seleccionar</option>
                        <?php foreach ($localidades as $localidad): ?>
                            <option value="<?php echo $localidad['id']; ?>"
                                data-dept="<?php echo $localidad['departamento_id']; ?>"
                                <?php echo (isset($_POST['localidad_id']) && $_POST['localidad_id'] == $localidad['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($localidad['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cod_postal">Código Postal</label>
                    <input type="text" id="cod_postal" name="cod_postal" value="<?php echo isset($_POST['cod_postal']) ? htmlspecialchars($_POST['cod_postal']) : ''; ?>" maxlength="10">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>" maxlength="20">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" maxlength="150">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_fam_directo">Nombre Familiar Directo</label>
                    <input type="text" id="nombre_fam_directo" name="nombre_fam_directo" value="<?php echo isset($_POST['nombre_fam_directo']) ? htmlspecialchars($_POST['nombre_fam_directo']) : ''; ?>" maxlength="200">
                </div>

                <div class="form-group">
                    <label for="tel_fam_directo">Teléfono Familiar</label>
                    <input type="tel" id="tel_fam_directo" name="tel_fam_directo" value="<?php echo isset($_POST['tel_fam_directo']) ? htmlspecialchars($_POST['tel_fam_directo']) : ''; ?>" maxlength="20">
                </div>

                <div class="form-group">
                    <label for="parentezco">Parentezco</label>
                    <input type="text" id="parentezco" name="parentezco" value="<?php echo isset($_POST['parentezco']) ? htmlspecialchars($_POST['parentezco']) : ''; ?>" maxlength="100">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="sit_revista">Situación de Revista</label>
                    <select id="sit_revista" name="sit_revista">
                        <option value="ACTIVO" <?php echo (isset($_POST['sit_revista']) && $_POST['sit_revista'] == 'ACTIVO') ? 'selected' : ''; ?>>Activo</option>
                        <option value="A.R.T." <?php echo (isset($_POST['sit_revista']) && $_POST['sit_revista'] == 'A.R.T.') ? 'selected' : ''; ?>>A.R.T.</option>
                        <option value="NOTA MÉDICA" <?php echo (isset($_POST['sit_revista']) && $_POST['sit_revista'] == 'NOTA MÉDICA') ? 'selected' : ''; ?>>Nota Médica</option>
                        <option value="DISPONIBLE" <?php echo (isset($_POST['sit_revista']) && $_POST['sit_revista'] == 'DISPONIBLE') ? 'selected' : ''; ?>>Disponible</option>
                        <option value="PASIVO" <?php echo (isset($_POST['sit_revista']) && $_POST['sit_revista'] == 'PASIVO') ? 'selected' : ''; ?>>Pasivo</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="novedades">Novedades</label>
                <textarea id="novedades" name="novedades" rows="4"><?php echo isset($_POST['novedades']) ? htmlspecialchars($_POST['novedades']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Agregar Cursante</button>
                <a href="index.php" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>

    <!-- <script>
        // Script para filtrar localidades por departamento seleccionado
        document.addEventListener('DOMContentLoaded', function() {
            const departamentoSelect = document.getElementById('departamento_id');
            const localidadSelect = document.getElementById('localidad_id');
            const localidadOptions = Array.from(localidadSelect.options);

            function filterLocalidades() {
                const selectedDept = departamentoSelect.value;

                // Limpiar opciones actuales excepto la primera
                localidadSelect.innerHTML = '<option value="">Seleccionar</option>';

                // Agregar opciones filtradas
                localidadOptions.forEach(option => {
                    if (option.value === '') return; // Skip empty option

                    const optionDept = option.getAttribute('data-dept');
                    if (!selectedDept || optionDept === selectedDept || optionDept === 'null') {
                        localidadSelect.appendChild(option.cloneNode(true));
                    }
                });
            }

            departamentoSelect.addEventListener('change', filterLocalidades);

            // Filtrar al cargar la página si hay un departamento pre-seleccionado
            if (departamentoSelect.value) {
                filterLocalidades();
            }
        });
    </script> -->
</body>

</html>