<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

$auth = new Auth();

// Solo permitir acceso a administradores
$auth->requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

// Procesar mensajes de estado
$message = '';
$messageType = '';

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'added':
            $message = 'Usuario agregado correctamente';
            $messageType = 'success';
            break;
        case 'updated':
            $message = 'Usuario actualizado correctamente';
            $messageType = 'success';
            break;
        case 'deleted':
            $message = 'Usuario eliminado correctamente';
            $messageType = 'success';
            break;
        case 'error':
            $message = 'Ha ocurrido un error en la operación';
            $messageType = 'danger';
            break;
    }
}

// Procesar búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$recordsPerPage = 15;
$offset = ($page - 1) * $recordsPerPage;

// Construir consulta con paginación
$whereClause = "WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $whereClause .= " AND (u.username LIKE ? OR u.nombre_completo LIKE ? OR u.email LIKE ? OR d.nombre LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
    $types = "ssss";
}

// Contar total de registros
$countQuery = "SELECT COUNT(*) as total 
               FROM usuarios u 
               LEFT JOIN divisiones d ON u.division_id = d.id 
               $whereClause";

$stmt = $conn->prepare($countQuery);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalRecords = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
$stmt->close();

// Obtener registros de la página actual
$query = "SELECT u.id, u.username, u.nombre_completo, u.email, u.rol, u.activo, u.last_login, u.created_at,
                 d.nombre as division_nombre
          FROM usuarios u 
          LEFT JOIN divisiones d ON u.division_id = d.id
          $whereClause 
          ORDER BY u.created_at DESC 
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);

// Preparar parámetros con limit y offset
$finalParams = $params;
$finalTypes = $types;
$finalParams[] = $recordsPerPage;
$finalParams[] = $offset;
$finalTypes .= "ii";

if (!empty($finalParams)) {
    $stmt->bind_param($finalTypes, ...$finalParams);
}

$stmt->execute();
$result = $stmt->get_result();

// Obtener estadísticas
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN rol = 'admin' THEN 1 ELSE 0 END) as admins,
    SUM(CASE WHEN last_login IS NOT NULL THEN 1 ELSE 0 END) as con_acceso
    FROM usuarios";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
    <style>
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-summary-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-summary-number {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
        }

        .stat-summary-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .user-avatar-small {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
            margin-right: 10px;
        }

        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .role-admin {
            background: #e74c3c;
            color: white;
        }

        .role-operador {
            background: #3498db;
            color: white;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .last-login {
            font-size: 0.85rem;
            color: #7f8c8d;
        }

        .division-info {
            font-size: 0.85rem;
            color: #6c757d;
            font-style: italic;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 30px 0;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }

        .pagination a:hover {
            background: #f8f9fa;
        }

        .pagination .current {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .protected-user {
            opacity: 0.8;
        }

        .protected-user .btn-delete {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="module-header">
            <h1>Gestión de Usuarios</h1>
            <a href="agregar.php" class="btn btn-primary">Nuevo Usuario</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas rápidas -->
        <div class="stats-summary">
            <div class="stat-summary-card">
                <div class="stat-summary-number"><?= $stats['total'] ?></div>
                <div class="stat-summary-label">Total Usuarios</div>
            </div>
            
            <div class="stat-summary-card">
                <div class="stat-summary-number"><?= $stats['activos'] ?></div>
                <div class="stat-summary-label">Activos</div>
            </div>
            
            <div class="stat-summary-card">
                <div class="stat-summary-number"><?= $stats['admins'] ?></div>
                <div class="stat-summary-label">Administradores</div>
            </div>
            
            <div class="stat-summary-card">
                <div class="stat-summary-number"><?= $stats['con_acceso'] ?></div>
                <div class="stat-summary-label">Con Acceso</div>
            </div>
        </div>

        <!-- Barra de búsqueda -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" 
                       name="search" 
                       placeholder="Buscar por usuario, nombre, email o división" 
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-search">Buscar</button>
                <?php if (!empty($search)): ?>
                    <a href="?" class="btn btn-cancel">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Información de resultados -->
        <?php if (!empty($search)): ?>
            <p>Se encontraron <?= $totalRecords ?> resultados para "<?= htmlspecialchars($search) ?>"</p>
        <?php else: ?>
            <p>Total: <?= $totalRecords ?> usuarios registrados</p>
        <?php endif; ?>

        <!-- Tabla de usuarios -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>División</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="<?= ($row['username'] === 'admin') ? 'protected-user' : '' ?>">
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar-small">
                                        <?= strtoupper(substr($row['nombre_completo'], 0, 2)) ?>
                                    </div>
                                    <strong><?= htmlspecialchars($row['username']) ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <?php if ($row['division_nombre']): ?>
                                    <span class="division-info"><?= htmlspecialchars($row['division_nombre']) ?></span>
                                <?php else: ?>
                                    <em>Sin asignar</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="role-badge role-<?= $row['rol'] ?>">
                                    <?= ucfirst($row['rol']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= $row['activo'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $row['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['last_login']): ?>
                                    <div class="last-login">
                                        <?= date('d/m/Y H:i', strtotime($row['last_login'])) ?>
                                    </div>
                                <?php else: ?>
                                    <em>Nunca</em>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-edit">Editar</a>
                                
                                <?php if ($row['username'] !== 'admin'): ?>
                                    <a href="eliminar.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-delete" 
                                       onclick="return confirmDelete('<?= htmlspecialchars($row['username']) ?>', '<?= htmlspecialchars($row['nombre_completo']) ?>')">
                                       Eliminar
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-delete" title="Usuario protegido">Protegido</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <?php if (!empty($search)): ?>
                                No se encontraron usuarios que coincidan con su búsqueda.
                            <?php else: ?>
                                No hay usuarios registrados.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php
                $baseUrl = "?";
                if (!empty($search)) {
                    $baseUrl .= "search=" . urlencode($search) . "&";
                }
                ?>
                
                <?php if ($page > 1): ?>
                    <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>">&laquo; Anterior</a>
                <?php endif; ?>

                <?php
                // Mostrar páginas cercanas
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);

                if ($start > 1) {
                    echo '<a href="' . $baseUrl . 'page=1">1</a>';
                    if ($start > 2) {
                        echo '<span>...</span>';
                    }
                }

                for ($i = $start; $i <= $end; $i++) {
                    if ($i == $page) {
                        echo '<span class="current">' . $i . '</span>';
                    } else {
                        echo '<a href="' . $baseUrl . 'page=' . $i . '">' . $i . '</a>';
                    }
                }

                if ($end < $totalPages) {
                    if ($end < $totalPages - 1) {
                        echo '<span>...</span>';
                    }
                    echo '<a href="' . $baseUrl . 'page=' . $totalPages . '">' . $totalPages . '</a>';
                }
                ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>">Siguiente &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
    <script src="../../assets/js/script.js"></script>
    
    <script>
        function confirmDelete(username, fullName) {
            const message = `¿Está seguro de eliminar el usuario?\n\n` +
                          `Usuario: ${username}\n` +
                          `Nombre: ${fullName}\n\n` +
                          `Esta acción no se puede deshacer y el usuario perderá acceso al sistema.`;
            
            return confirm(message);
        }

        // Proteger usuario admin
        document.addEventListener('DOMContentLoaded', function() {
            const protectedButtons = document.querySelectorAll('.protected-user .btn-delete');
            
            protectedButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    alert('El usuario administrador no puede ser eliminado por seguridad del sistema.');
                    return false;
                });
            });
        });
    </script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>