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

// Obtener ID del aspirante
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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Detalle del Aspirante</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/detalle_asp.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="back-link">
            <a href="index.php">← Volver al listado</a>
        </div>

        <h1>Detalle del Aspirante</h1>

        <div class="detail-card">
            <div class="detail-header">
                <h2 class="detail-title">
                    <?php echo htmlspecialchars($aspirante['apellido']) . ', ' . htmlspecialchars($aspirante['nombre']); ?>
                </h2>
                <span class="status-badge status-<?php echo htmlspecialchars($aspirante['estado']); ?>">
                    <?php echo htmlspecialchars(ucfirst($aspirante['estado'])); ?>
                </span>
            </div>

            <div class="detail-grid">
                <div class="detail-group">
                    <div class="detail-label">DNI</div>
                    <div class="detail-value"><?php echo htmlspecialchars($aspirante['dni']); ?></div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Comisión</div>
                    <div class="detail-value">
                        <?php echo $aspirante['comision'] ? 'Comisión ' . htmlspecialchars($aspirante['comision']) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Fecha de Nacimiento</div>
                    <div class="detail-value">
                        <?php echo $aspirante['fecha_nacimiento'] ? htmlspecialchars($aspirante['fecha_nacimiento']) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Fecha de Ingreso</div>
                    <div class="detail-value">
                        <?php echo $aspirante['fecha_ingreso'] ? htmlspecialchars($aspirante['fecha_ingreso']) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Lugar de Nacimiento</div>
                    <div class="detail-value">
                        <?php echo $aspirante['lugar_nacimiento'] ? htmlspecialchars($aspirante['lugar_nacimiento']) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Domicilio</div>
                    <div class="detail-value">
                        <?php echo $aspirante['domicilio'] ? htmlspecialchars($aspirante['domicilio']) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Teléfono</div>
                    <div class="detail-value">
                        <?php echo $aspirante['telefono'] ? htmlspecialchars($aspirante['telefono']) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">
                        <?php echo $aspirante['email'] ? htmlspecialchars($aspirante['email']) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Estado Civil</div>
                    <div class="detail-value">
                        <?php echo $aspirante['estado_civil'] ? htmlspecialchars(ucfirst($aspirante['estado_civil'])) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Nivel Educativo</div>
                    <div class="detail-value">
                        <?php echo $aspirante['nivel_educativo'] ? htmlspecialchars(ucfirst($aspirante['nivel_educativo'])) : '<span class="empty">No especificado</span>'; ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($aspirante['observaciones'])): ?>
            <div class="detail-group">
                <div class="detail-label">Observaciones</div>
                <div class="detail-value"><?php echo nl2br(htmlspecialchars($aspirante['observaciones'])); ?></div>
            </div>
            <?php endif; ?>

            <div class="created-info">
                Registrado el: <?php echo date('d/m/Y H:i', strtotime($aspirante['created_at'])); ?> | 
                Última actualización: <?php echo date('d/m/Y H:i', strtotime($aspirante['updated_at'])); ?>
            </div>
        </div>

        <div class="action-buttons">
            <a href="editar.php?id=<?php echo $aspirante['id']; ?>" class="btn btn-warning">Editar</a>
            <a href="eliminar.php?id=<?php echo $aspirante['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar este aspirante?')">Eliminar</a>
            <a href="index.php" class="btn btn-cancel">Volver al Listado</a>
        </div>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
</body>

</html>