<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/database.php';

$auth = new Auth();

// Solo permitir acceso a administradores
if (!$auth->isLoggedIn() || $auth->getUserInfo()['rol'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$userId = $_GET['id'] ?? 0;
$success = false;

// Verificar que no sea el usuario admin principal
$stmt = $conn->prepare("SELECT username FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($user['username'] !== 'admin') {
        // Eliminar el usuario
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $success = $stmt->execute();
    }
}

// Redirigir con mensaje
header("Location: ../usuarios/?delete=" . ($success ? 'success' : 'error'));
exit();
