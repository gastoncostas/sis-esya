<?php
require_once 'database.php';

class Auth {
    
    private $db;

    public function __construct() {
        $this->db = new Database();
        
        // Verificar estado de la sesión antes de manipularla
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } elseif (session_status() === PHP_SESSION_ACTIVE) {
            // Si la sesión ya está activa, regenerar ID por seguridad
            session_regenerate_id(true);
        }
        
        // Limpiar sesión si es muy antigua (más de 5 minutos)
        if (isset($_SESSION['last_activity'])) {
            $inactive = 600; // 10 minutos de inactividad
            $session_life = time() - $_SESSION['last_activity'];
            if ($session_life > $inactive) {
                $this->logout();
            }
        }
        $_SESSION['last_activity'] = time();
    }

    public function login($username, $password) {
        $conn = $this->db->getConnection();
        
        // Verificar si la tabla usuarios existe
        $tableCheck = $conn->query("SHOW TABLES LIKE 'usuarios'");
        if ($tableCheck->num_rows == 0) {
            // Si la tabla no existe, crear un usuario por defecto
            $this->createDefaultUser();
            
            // Verificar credenciales por defecto
            if ($username === 'admin' && $password === 'admin123') {
                $_SESSION['temp_auth'] = [
                    'user_id' => 1,
                    'username' => 'admin',
                    'nombre_completo' => 'Administrador del Sistema',
                    'rol' => 'administrador',
                    'created' => time()
                ];
                session_regenerate_id(true);
                return true;
            }
            return false;
        }
        
        $stmt = $conn->prepare("SELECT id, username, password, nombre_completo, rol FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($password === $user['password']) {
                // Limpiar sesión existente
                $_SESSION = [];
                
                // Establecer datos temporales
                $_SESSION['temp_auth'] = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'nombre_completo' => $user['nombre_completo'],
                    'rol' => $user['rol'],
                    'created' => time()
                ];
                
                // Regenerar ID de sesión
                session_regenerate_id(true);
                
                $this->updateLastLogin($user['id']);
                return true;
            }
        }
        
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['temp_auth']);
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public function getUserInfo() {
        if ($this->isLoggedIn()) {
            return $_SESSION['temp_auth'];
        }
        return null;
    }

    private function updateLastLogin($userId) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE usuarios SET last_login = NOW() WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
    
    private function createDefaultUser() {
        $conn = $this->db->getConnection();
        
        // Crear tabla usuarios si no existe
        $sql = "CREATE TABLE IF NOT EXISTS usuarios (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nombre_completo VARCHAR(100) NOT NULL,
            rol VARCHAR(50) NOT NULL,
            last_login DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $conn->query($sql);
        
        // Insertar usuario por defecto si no existe
        $checkUser = $conn->query("SELECT COUNT(*) as count FROM usuarios WHERE username = 'admin'");
        $result = $checkUser->fetch_assoc();
        
        if ($result['count'] == 0) {
            $insert = $conn->prepare("INSERT INTO usuarios (username, password, nombre_completo, rol) VALUES (?, ?, ?, ?)");
            $username = 'admin';
            $password = 'admin123';
            $nombre = 'Administrador del Sistema';
            $rol = 'administrador';
            
            $insert->bind_param("ssss", $username, $password, $nombre, $rol);
            $insert->execute();
        }
    }
}
?>