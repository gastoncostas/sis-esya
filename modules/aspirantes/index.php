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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Aspirantes</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="module-header">
            <h1>Gestión de Aspirantes</h1>
            <a href="agregar.php" class="btn btn-primary">Nuevo Aspirante</a>
        </div>

        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Buscar por DNI, nombre o apellido">
                <button type="submit" class="btn btn-search">Buscar</button>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $query = "SELECT * FROM aspirantes";

                if (!empty($search)) {
                    $query .= " WHERE dni LIKE ? OR nombre LIKE ? OR apellido LIKE ?";
                    $stmt = $conn->prepare($query);
                    $searchParam = "%$search%";
                    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
                } else {
                    $stmt = $conn->prepare($query);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['dni']); ?></td>
                        <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td class="actions">
                            <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Editar</a>
                            <a href="eliminar.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Está seguro?')">Eliminar</a>
                            <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn btn-view">Ver</a>
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