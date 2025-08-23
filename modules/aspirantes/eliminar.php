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

// Obtener ID del aspirante a eliminar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Verificar si el aspirante existe
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

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm'])) {
        // Eliminar el aspirante
        $stmt = $conn->prepare("DELETE FROM aspirantes WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $success = 'Aspirante eliminado correctamente';
            // Redirigir después de 2 segundos
            header("Refresh: 2; URL=index.php");
        } else {
            $error = 'Error al eliminar el aspirante: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        // Cancelar eliminación
        header("Location: detalle.php?id=" . $id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Eliminar Aspirante</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/eliminar_asp.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="back-link">
            <a href="detalle.php?id=<?php echo $id; ?>">← Volver al detalle</a>
        </div>

        <h1>Eliminar Aspirante</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <div class="success-icon">✓</div>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <p>Redirigiendo al listado...</p>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <strong>¡Advertencia!</strong> Esta acción no se puede deshacer. Se eliminarán todos los datos del aspirante permanentemente.
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

    <?php include '../../includes/unified_footer.php'; ?>
</body>

</html>