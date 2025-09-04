<?php
require_once __DIR__.'/functions.php';
require_login();

if (isset($_GET['file']) && isset($_GET['modulo']) && isset($_GET['materia'])) {
    $file = basename($_GET['file']);
    $modulo = intval($_GET['modulo']);
    $materia = $_GET['materia'];
    
    // Validar que la materia existe
    $subject = subject_by_slug($materia);
    if (!$subject) {
        die('Materia no encontrada');
    }
    
    // Validar que el módulo es correcto
    if ($modulo < 1 || $modulo > 2) {
        die('Módulo no válido');
    }
    
    $file_path = uploads_path($materia, $modulo) . '/' . $file;
    
    if (file_exists($file_path)) {
        // Headers para forzar descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        
        // Limpiar buffer de salida y leer el archivo
        flush();
        readfile($file_path);
        exit;
    } else {
        die('El archivo no existe');
    }
} else {
    die('Parámetros incorrectos');
}
?>