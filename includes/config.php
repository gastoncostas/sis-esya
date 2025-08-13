<?php
/**
 * Configuración principal del sistema ESyA
 * 
 * Define constantes y configuraciones globales para la aplicación
 */

// 1. Configuración de entorno
define('ENVIRONMENT', 'development'); // 'production' o 'development'

// 2. Configuración de la aplicación
define('APP_NAME', 'Sistema de Gestión de la ESyA');
define('APP_VERSION', '1.0.0');

// Determinar BASE_URL automáticamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$base_path = rtrim($script_dir, '/') . '/';

define('BASE_URL', $protocol . $host . $base_path);

// 3. Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'esyabd');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// 4. Configuración de seguridad
define('PEPPER', 'ESyA2024_SecureKey_' . md5(__DIR__)); // Clave única basada en el directorio
define('SESSION_TIMEOUT', 1800); // 30 minutos en segundos
define('PASSWORD_MIN_LENGTH', 6);

// 5. Configuración de sesión segura
ini_set('session.name', 'ESYA_SESSION');
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
ini_set('session.cookie_lifetime', 0); // Expira al cerrar el navegador
ini_set('session.entropy_length', 32);
ini_set('session.hash_function', 'sha256');

// 6. Manejo de errores según entorno
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// 7. Zona horaria
date_default_timezone_set('America/Argentina/Tucuman');

// 8. Configuraciones adicionales
define('PASSWORD_RESET_TIMEOUT', 3600); // 1 hora para reset de contraseña
define('MAX_LOGIN_ATTEMPTS', 5); // Máximo intentos de login
define('LOGIN_LOCKOUT_TIME', 300); // 5 minutos de bloqueo

// 9. Configuración de archivos y directorios
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// 10. Crear directorio de logs si no existe
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

// 11. Función de autoload mejorada
spl_autoload_register(function ($class_name) {
    $directories = [
        __DIR__ . '/classes/',
        __DIR__ . '/models/',
        __DIR__ . '/controllers/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 12. Función helper para logging
function logError($message, $context = []) {
    $log_message = date('[Y-m-d H:i:s] ') . $message;
    if (!empty($context)) {
        $log_message .= ' | Context: ' . json_encode($context);
    }
    error_log($log_message);
}

// 13. Función helper para sanitización
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// 14. Función helper para validación de email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// 15. Configuración de headers de seguridad
function setSecurityHeaders() {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}

// Aplicar headers de seguridad
setSecurityHeaders();

// 16. Iniciar buffer de salida con compresión
if (!ob_get_level()) {
    ob_start('ob_gzhandler');
}

// 17. Constantes de estado
define('STATUS_ACTIVE', 'activo');
define('STATUS_INACTIVE', 'inactivo');
define('STATUS_GRADUATED', 'graduado');

define('ROLE_ADMIN', 'admin');
define('ROLE_OPERATOR', 'operador');

// 18. Configuración de mensajes del sistema
define('MSG_LOGIN_SUCCESS', 'Inicio de sesión exitoso');
define('MSG_LOGIN_FAILED', 'Usuario o contraseña incorrectos');
define('MSG_ACCESS_DENIED', 'Acceso denegado');
define('MSG_SESSION_EXPIRED', 'Su sesión ha expirado');

// 19. Función para obtener configuración de la base de datos
function getDatabaseConfig() {
    return [
        'host' => DB_HOST,
        'name' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
        'charset' => DB_CHARSET
    ];
}

// 20. Verificar dependencias críticas
$required_extensions = ['mysqli', 'session', 'json'];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        die("Error crítico: La extensión PHP '$ext' es requerida pero no está disponible.");
    }
}

// 21. Función para debug (solo en desarrollo)
function debugLog($data, $label = 'DEBUG') {
    if (ENVIRONMENT === 'development') {
        $message = $label . ': ' . (is_array($data) ? json_encode($data) : $data);
        error_log($message);
    }
}
?>