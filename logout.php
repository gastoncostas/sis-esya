<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Crear instancia de autenticación
$auth = new Auth();

// Cerrar sesión
$auth->logout();

// Limpiar cualquier buffer de salida
if (ob_get_level()) {
    ob_end_clean();
}

// Prevenir caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirigir al inicio con mensaje
header("Location: inicio.php?logout=success");
exit();
?>