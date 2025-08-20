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
            $inactive = 300; // 1 segundo de inactividad
            $session_life = time() - $_SESSION['last_activity'];
            if ($session_life > $inactive) {
                $this->logout();
            }
        }
        $_SESSION['last_activity'] = time();
    }

    public function login($username, $password) {
        $conn = $this->db->getConnection();
        
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
}
?>