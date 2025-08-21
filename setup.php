<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

header('Content-Type: application/json');

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Leer el cuerpo de la solicitud JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || $input['action'] !== 'setup_tables') {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // SQL para crear las tablas faltantes
    $sqlQueries = [
        "CREATE TABLE IF NOT EXISTS `aspirantes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
            `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
            `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
            `fecha_nacimiento` date NOT NULL,
            `lugar_nacimiento` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `domicilio` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `estado_civil` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `nivel_educativo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
            `fecha_ingreso` date DEFAULT NULL,
            `observaciones` text COLLATE utf8mb4_unicode_ci,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `dni` (`dni`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS `asistencia` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `aspirante_id` int(11) NOT NULL,
            `fecha` date NOT NULL,
            `presente` tinyint(1) DEFAULT '0',
            `justificado` tinyint(1) DEFAULT '0',
            `observaciones` text COLLATE utf8mb4_unicode_ci,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `aspirante_id` (`aspirante_id`),
            KEY `fecha` (`fecha`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        "CREATE TABLE IF NOT EXISTS `materias` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
            `codigo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `horas_semanales` int(11) DEFAULT NULL,
            `profesor` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `descripcion` text COLLATE utf8mb4_unicode_ci,
            `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'activa',
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `codigo` (`codigo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    // Ejecutar todas las consultas
    foreach ($sqlQueries as $query) {
        $conn->query($query);
    }
    
    // Agregar restricción de clave foránea si no existe
    $fkCheck = $conn->query("SELECT COUNT(*) as count FROM information_schema.TABLE_CONSTRAINTS 
                            WHERE CONSTRAINT_SCHEMA = '" . DB_NAME . "' 
                            AND TABLE_NAME = 'asistencia' 
                            AND CONSTRAINT_NAME = 'asistencia_ibfk_1'");
    
    if ($fkCheck->fetch_assoc()['count'] == 0) {
        $conn->query("ALTER TABLE `asistencia` 
                    ADD CONSTRAINT `asistencia_ibfk_1` 
                    FOREIGN KEY (`aspirante_id`) 
                    REFERENCES `aspirantes` (`id`) ON DELETE CASCADE");
    }
    
    echo json_encode(['success' => true, 'message' => 'Tablas creadas exitosamente']);
    
} catch (Exception $e) {
    error_log("Error en setup: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al crear tablas: ' . $e->getMessage()]);
}