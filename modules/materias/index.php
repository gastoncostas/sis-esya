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

// Procesar búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Consulta base
$query = "SELECT * FROM materias";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " WHERE nombre LIKE ? OR profesor LIKE ?";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm];
    $types = "ss";
}

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
    <title><?php echo APP_NAME; ?> - Gestión de Materias</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="module-header">
            <h1>Gestión de Materias</h1>
            <div>
                <a href="agregar.php" class="btn btn-primary">Nueva Materia</a>
            </div>
        </div>

        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Buscar por nombre o profesor" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-search">Buscar</button>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Profesor</th>
                    <th>Horas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['profesor']); ?></td>
                        <td><?php echo htmlspecialchars($row['horas_cursado']); ?></td>
                        <td class="actions">
                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Editar</a>
                            <a href="eliminar.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar esta materia?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    <script src="../../assets/js/script.js"></script>
</body>

</html>