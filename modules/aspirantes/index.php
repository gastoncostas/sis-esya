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

// Configuración de paginación
$limit = 20; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];
$types = '';

if (!empty($search)) {
    $where = " WHERE c.dni LIKE ? OR c.nombre LIKE ? OR c.apellido LIKE ?";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam];
    $types = 'sss';
}

// Consulta para obtener el total de registros (para paginación)
$countQuery = "SELECT COUNT(*) as total FROM cursantes c" . $where;
$countStmt = $conn->prepare($countQuery);

if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();
$totalResult = $countStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalCursantes = $totalRow['total'];
$totalPages = ceil($totalCursantes / $limit);

// Consulta principal con paginación y JOINs
$query = "SELECT 
    c.id,
    c.dni, 
    c.apellido, 
    c.nombre,
    co.codigo as comision,
    c.estado,
    d.nombre as departamento_nombre,
    l.nombre as localidad_nombre
FROM cursantes c
LEFT JOIN comisiones co ON c.comision_id = co.id
LEFT JOIN departamentos d ON c.departamento_id = d.id  
LEFT JOIN localidades l ON c.localidad_id = l.id" . $where . " 
ORDER BY c.apellido, c.nombre LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Cursantes</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="module-header">
            <h1>Gestión de Cursantes</h1>
            <div>
                <a href="agregar.php" class="btn btn-primary">Nuevo Cursante</a>
                <a href="importar_excel.php" class="btn btn-success">📤 Importar desde CSV</a>
            </div>
        </div>

        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Buscar por DNI, nombre o apellido" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-search">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="btn btn-cancel">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($totalCursantes > 0): ?>
            <div class="results-info">
                Mostrando <?php echo $result->num_rows; ?> de <?php echo $totalCursantes; ?> cursantes
            </div>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Comisión</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['dni']); ?></td>
                            <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td>
                                <?php if (!empty($row['comision'])): ?>
                                    <span class="comision-badge">
                                        <?php echo htmlspecialchars($row['comision']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Sin asignar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower(htmlspecialchars($row['estado'])); ?>">
                                    <?php echo htmlspecialchars(ucfirst(strtolower($row['estado']))); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn btn-view" title="Ver detalle">👁️</a>
                                <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-edit" title="Editar">✏️</a>
                                <a href="eliminar.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar este cursante?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-data">
                            <?php if (!empty($search)): ?>
                                No se encontraron cursantes que coincidan con "<?php echo htmlspecialchars($search); ?>"
                            <?php else: ?>
                                No hay cursantes registrados
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn">Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn">Siguiente</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    <script src="../../assets/js/script.js"></script>
</body>

</html>