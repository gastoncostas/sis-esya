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
define('BASE_URL', 'http://localhost/sis-esya/'); // Ajustar según tu entorno

// 3. Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'esyabd');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// 4. Configuración de seguridad
define('PEPPER', 'TuCadenaSecretaUnicaAquí'); // Cambiar por un valor único
define('SESSION_TIMEOUT', 1800); // 30 minutos en segundos

// 5. Configuración de sesión segura
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 si usas HTTPS
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
ini_set('session.cookie_lifetime', 0); // Expira al cerrar el navegador

// 6. Manejo de errores según entorno
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 7. Zona horaria
date_default_timezone_set('America/Argentina/Tucuman');

// 8. Otras configuraciones
define('PASSWORD_RESET_TIMEOUT', 3600); // 1 hora para reset de contraseña

// 9. Autoload básico para clases
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 10. Iniciar buffer de salida
ob_start();