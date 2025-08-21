<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/database.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();
$user = $auth->getUserInfo();

$error = '';
$success = '';

// Verificar estructura de la tabla usuarios y agregar columna email si no existe
$checkColumn = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'email'");
if ($checkColumn->num_rows == 0) {
    // Agregar columna email si no existe
    $conn->query("ALTER TABLE usuarios ADD COLUMN email VARCHAR(150) NULL AFTER username");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validaciones básicas
    if (empty($nombre)) {
        $error = "El nombre completo es requerido";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email inválido";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } else {
        try {
            // Actualizar datos básicos
            if (empty($password)) {
                if (empty($email)) {
                    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ? WHERE id = ?");
                    $stmt->bind_param("si", $nombre, $user['user_id']);
                } else {
                    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $nombre, $email, $user['user_id']);
                }
            } else {
                if (empty($email)) {
                    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $nombre, $password, $user['user_id']);
                } else {
                    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $nombre, $email, $password, $user['user_id']);
                }
            }

            if ($stmt->execute()) {
                // Actualizar datos de sesión
                $_SESSION['temp_auth']['nombre_completo'] = $nombre;
                if (!empty($email)) {
                    $_SESSION['temp_auth']['email'] = $email;
                }
                $success = "Perfil actualizado correctamente";

                // Refrescar datos del usuario
                $user = $auth->getUserInfo();
            } else {
                $error = "Error al actualizar el perfil: " . $conn->error;
            }
            
            if (isset($stmt)) {
                $stmt->close();
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Obtener datos actualizados del usuario con manejo seguro
$userData = [
    'username' => $user['username'] ?? '',
    'email' => $user['email'] ?? '',
    'nombre_completo' => $user['nombre_completo'] ?? ''
];

// Si no tenemos email en sesión, intentar obtenerlo de la base de datos
if (empty($userData['email'])) {
    try {
        $stmt = $conn->prepare("SELECT username, email, nombre_completo FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $user['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $dbUser = $result->fetch_assoc();
            $userData['email'] = $dbUser['email'] ?? '';
            $userData['nombre_completo'] = $dbUser['nombre_completo'] ?? $userData['nombre_completo'];
            $userData['username'] = $dbUser['username'] ?? $userData['username'];
            
            // Actualizar sesión con el email si se encontró
            if (!empty($dbUser['email'])) {
                $_SESSION['temp_auth']['email'] = $dbUser['email'];
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        // Silenciar error para no interrumpir la visualización
        error_log("Error al cargar datos del usuario: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(APP_NAME); ?> - Mi Perfil</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/unified_header_footer.css">
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .profile-form {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #3498db;
        }
        
        input:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .alert-danger {
            background: #ffe6e6;
            color: #c0392b;
            border: 2px solid #ffcccc;
        }
        
        .alert-success {
            background: #e6ffe6;
            color: #27ae60;
            border: 2px solid #ccffcc;
        }
        
        .text-muted {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>

<body>
    <?php include 'includes/unified_header.php'; ?>

    <div class="container">
        <h1>Mi Perfil</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" class="profile-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($userData['username']); ?>" disabled>
                    <span class="text-muted">No se puede cambiar el nombre de usuario</span>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" placeholder="Opcional">
                </div>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($userData['nombre_completo']); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar nueva contraseña">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <?php include 'includes/unified_footer.php'; ?>

    <script>
        // Validación del formulario
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const email = document.getElementById('email').value;
                
                // Validar que las contraseñas coincidan
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return false;
                }
                
                // Validar email si se proporciona
                if (email && !/\S+@\S+\.\S+/.test(email)) {
                    e.preventDefault();
                    alert('Por favor ingrese un email válido');
                    return false;
                }
            });
        });
    </script>
</body>

</html>