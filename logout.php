<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Destruir completamente la sesión
session_start();
$_SESSION = [];
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');

// Redirigir al login
header("Location: login.php?logout=1");
exit();
