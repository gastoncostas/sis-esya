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
$aspirante = null;

// Obtener ID del cursante a eliminar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Cargar datos del cursante para mostrar confirmación
$stmt = $conn->prepare("SELECT id, dni, apellido, nombre, comision, estado FROM cursante WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$aspirante = $result->fetch_assoc();
$stmt->close();

// Procesar confirmación de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $stmt = $conn->prepare("DELETE FROM cursante WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = 'El cursante ha sido eliminado correctamente.';
        $aspirante = null; // Para no mostrar los datos en la vista
    } else {
        $error = 'Error al eliminar el cursante: ' . $stmt->error;
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Eliminar Cursante</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
    <link rel="stylesheet" href="../../assets/css/eliminar_asp.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <main class="container">
        <div class="back-link">
            <a href="index.php">← Volver al listado</a>
        </div>
        <h1>Eliminar Cursante</h1>

        <div class="content-wrapper">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($aspirante): ?>
                <div class="alert alert-warning">
                    <p>
                        ¿Estás seguro de que deseas eliminar a este cursante?
                        Esta acción es irreversible y los datos no se podrán recuperar.
                    </p>
                </div>

                <div class="confirmation-box">
                    <div class="aspirante-info">
                        <div class="aspirante-name">
                            <?php echo htmlspecialchars($aspirante['apellido']) . ', ' . htmlspecialchars($aspirante['nombre']); ?>
                        </div>
                        <div class="aspirante-details">
                            DNI: <?php echo htmlspecialchars($aspirante['dni']); ?> |
                            Comisión: <?php echo htmlspecialchars($aspirante['comision']); ?> |
                            Estado: <?php echo htmlspecialchars(ucfirst($aspirante['estado'])); ?>
                        </div>
                    </div>

                    <form method="POST" action="">
                        <div class="form-actions">
                            <button type="submit" name="confirm" value="1" class="btn btn-danger" onclick="return confirm('¿Está absolutamente seguro? Esta acción es irreversible.')">
                                Confirmar Eliminación
                            </button>
                            <button type="submit" name="cancel" value="1" class="btn btn-cancel">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../../includes/unified_footer.php'; ?>
</body>

</html>