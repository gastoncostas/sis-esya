<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/database.php';

$auth = new Auth();

if (!$auth->isLoggedIn() || $auth->getUserInfo()['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Al inicio del archivo, después de abrir la conexión
$deleteStatus = $_GET['delete'] ?? '';

if ($deleteStatus === 'success') {
    echo '<div class="alert alert-success">Usuario eliminado correctamente</div>';
} elseif ($deleteStatus === 'error') {
    echo '<div class="alert alert-danger">No se pudo eliminar el usuario</div>';
}

// Procesar búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Consulta base
$query = "SELECT id, username, nombre_completo, email, rol, created_at FROM usuarios";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " WHERE username LIKE ? OR nombre_completo LIKE ? OR email LIKE ?";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = "sss";
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
    <title><?php echo APP_NAME; ?> - Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="module-header">
            <h1>Gestión de Usuarios</h1>
            <div>
                <a href="agregar.php" class="btn btn-primary">Nuevo Usuario</a>
            </div>
        </div>

        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Buscar por usuario, nombre o email" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-search">Buscar</button>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo ucfirst($row['rol']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                        <td class="actions">
                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Editar</a>
                            <?php if ($row['username'] !== 'admin'): ?>
                                <a href="eliminar.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/usuarios.js"></script>
</body>

</html>