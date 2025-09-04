<?php require_once __DIR__.'/functions.php';
session_destroy();
header('Location: index.php?msg=Sesión%20cerrada');
