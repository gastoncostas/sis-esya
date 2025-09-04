<?php 
require_once __DIR__.'/functions.php';

// Redirigir usuarios ya autenticados
if (isset($_SESSION["user"])) { 
    header("Location: dashboard.php"); 
    exit; 
}

// Solo procesar POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header('Location: index.php'); 
    exit; 
}

// Obtener y limpiar credenciales
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validaciones básicas
if (empty($username) || empty($password)) {
    header('Location: index.php?error=Usuario%20y%20contraseña%20son%20requeridos');
    exit;
}

// Obtener usuarios registrados
$users = read_users();

// Verificar credenciales
if (isset($users[$username])) {
    // Verificar contraseña hasheada
    if (password_verify($password, $users[$username]['password'])) {
        // Crear sesión de usuario
        $_SESSION['user'] = [
            'username' => $username,
            'name' => $users[$username]['name'],
            'role' => $users[$username]['role'],
            'email' => $users[$username]['email'] ?? '',
            'turno' => $users[$username]['turno'] ?? 'mañana',
            'comision' => $users[$username]['comision'] ?? ''
        ];
        
        // Redirigir al dashboard
        header('Location: dashboard.php');
        exit;
    }
}

// Si llega aquí, las credenciales son inválidas
header('Location: index.php?error=Usuario%20o%20contraseña%20inválidos');
exit;
?>