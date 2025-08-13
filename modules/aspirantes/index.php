<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$db = new Database();
$conn = $db->getConnection();

// Procesar búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$recordsPerPage = 20;
$offset = ($page - 1) * $recordsPerPage;

// Construir consulta con paginación
$whereClause = "";
$params = [];
$types = "";

if (!empty($search)) {
    $whereClause = "WHERE dni LIKE ? OR nombre LIKE ? OR apellido LIKE ?";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = "sss";
}

// Contar total de registros
$countQuery = "SELECT COUNT(*) as total FROM aspirantes $whereClause";
$stmt = $conn->prepare($countQuery);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$totalRecords = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);
$stmt->close();

// Obtener registros de la página actual
$query = "SELECT id, dni, nombre, apellido, estado, fecha_ingreso, created_at 
          FROM aspirantes $whereClause 
          ORDER BY apellido, nombre 
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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Aspirantes</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
    <style>
        /* Estilos adicionales para el módulo */
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

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-activo {
            background: #d4edda;
            color: #155724;
        }

        .status-inactivo {
            background: #f8d7da;
            color: #721c24;
        }

        .status-graduado {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="module-header">
            <h1>Gestión de Aspirantes</h1>
            <a href="agregar.php" class="btn btn-primary">Nuevo Aspirante</a>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="stats-summary">
            <?php
            // Obtener estadísticas
            $statsQuery = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN estado = 'graduado' THEN 1 ELSE 0 END) as graduados,
                SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos
                FROM aspirantes";
            
            $statsResult = $conn->query($statsQuery);
            $stats = $statsResult->fetch_assoc();
            ?>
            
            <div class="stat-summary-card">
                <div class="stat-summary-number"><?= $stats['total'] ?></div>
                <div class="stat-summary-label">Total</div>
            </div>
            
            <div class="stat-summary-card">
                <div class="stat-summary-number"><?= $stats['activos'] ?></div>
                <div class="stat-summary-label">Activos</div>
            </div>
            
            <div class="stat-summary-card">
                <div class="stat-summary-number"><?= $stats['graduados'] ?></div>
                <div class="stat-summary-label">Graduados</div>
            </div>
            
            <div class="stat-summary-card">
                <div class="stat-summary-number"><?= $stats['inactivos'] ?></div>
                <div class="stat-summary-label">Inactivos</div>
            </div>
        </div>

        <!-- Barra de búsqueda -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" 
                       name="search" 
                       placeholder="Buscar por DNI, nombre o apellido" 
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
            <p>Total: <?= $totalRecords ?> aspirantes registrados</p>
        <?php endif; ?>

        <!-- Tabla de aspirantes -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Fecha Ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['dni']) ?></td>
                            <td><?= htmlspecialchars($row['apellido']) ?></td>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $row['estado'] ?>">
                                    <?= ucfirst($row['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?= $row['fecha_ingreso'] ? date('d/m/Y', strtotime($row['fecha_ingreso'])) : '-' ?>
                            </td>
                            <td class="actions">
                                <a href="detalle.php?id=<?= $row['id'] ?>" class="btn btn-view">Ver</a>
                                <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-edit">Editar</a>
                                <a href="eliminar.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-delete" 
                                   onclick="return confirm('¿Está seguro de eliminar este aspirante?')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <?php if (!empty($search)): ?>
                                No se encontraron aspirantes que coincidan con su búsqueda.
                            <?php else: ?>
                                No hay aspirantes registrados aún.
                                <br><br>
                                <a href="agregar.php" class="btn btn-primary">Registrar Primer Aspirante</a>
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
        // Confirmar eliminación con más contexto
        document.addEventListener('DOMContentLoaded', function() {
            const deleteLinks = document.querySelectorAll('a[href*="eliminar.php"]');
            
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const row = this.closest('tr');
                    const dni = row.cells[0].textContent;
                    const apellido = row.cells[1].textContent;
                    const nombre = row.cells[2].textContent;
                    
                    const confirmMessage = `¿Está seguro de eliminar al aspirante?\n\n` +
                                         `DNI: ${dni}\n` +
                                         `Nombre: ${nombre} ${apellido}\n\n` +
                                         `Esta acción no se puede deshacer.`;
                    
                    if (confirm(confirmMessage)) {
                        window.location.href = this.href;
                    }
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