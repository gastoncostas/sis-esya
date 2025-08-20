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

// Obtener lista de materias
$materias = [];
$result = $conn->query("SELECT id, nombre FROM materias ORDER BY nombre");
while ($row = $result->fetch_assoc()) {
    $materias[$row['id']] = $row['nombre'];
}

// Obtener lista de aspirantes
$aspirantes = [];
$result = $conn->query("SELECT id, dni, nombre, apellido FROM aspirantes ORDER BY apellido, nombre");
while ($row = $result->fetch_assoc()) {
    $aspirantes[$row['id']] = $row['apellido'] . ', ' . $row['nombre'] . ' (' . $row['dni'] . ')';
}








// Procesar formulario de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materia_id = $_POST['materia_id'];
    $fecha = $_POST['fecha'];
    $asistencias = $_POST['asistencia'] ?? [];

    // Registrar asistencias
    foreach ($asistencias as $aspirante_id => $presente) {
        $presente = $presente ? 1 : 0;
        $observaciones = $_POST['observaciones'][$aspirante_id] ?? '';

        $stmt = $conn->prepare("INSERT INTO asistencia (aspirante_id, materia_id, fecha, presente, observaciones) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisis", $aspirante_id, $materia_id, $fecha, $presente, $observaciones);
        $stmt->execute();
    }

    $success = "Asistencia registrada correctamente para " . count($asistencias) . " aspirantes";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Asistencia</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <h1>Registro de Asistencia</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="materia_id">Materia *</label>
                    <select id="materia_id" name="materia_id" required>
                        <option value="">Seleccione una materia</option>
                        <?php foreach ($materias as $id => $nombre): ?>
                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha">Fecha *</label>
                    <input type="date" id="fecha" name="fecha" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <h2>Lista de Aspirantes</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Presente</th>
                        <th>Aspirante</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aspirantes as $id => $nombre): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="asistencia[<?php echo $id; ?>]" value="1" checked>
                            </td>
                            <td><?php echo htmlspecialchars($nombre); ?></td>
                            <td>
                                <input type="text" name="observaciones[<?php echo $id; ?>]" placeholder="Observaciones">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Registrar Asistencia</button>
            </div>
        </form>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
</body>

</html>