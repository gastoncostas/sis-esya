<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();

// Comportamiento inteligente:
// - Si está logueado, va al dashboard
// - Si no está logueado, va a la página de inicio
if ($auth->isLoggedIn()) {
    header("Location: dashboard.php");
} else {
    header("Location: inicio.php");
}
exit();
