<?php
require_once 'database.php';

class Auth {
    
    private $db;
    private $sessionTimeout = 1800; // 30 minutos

    public function __construct() {
        $this->db = new Database();
        $this->initializeSession();
    }

    private function initializeSession() {
        // Verificar estado de la sesión antes de manipularla
        if (session_status() === PHP_SESSION_NONE) {
            // Configurar parámetros de sesión segura
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
            
            session_start();
        }
        
        // Verificar tiempo de inactividad
        $this->checkSessionTimeout();
        
        // Actualizar timestamp de última actividad
        $_SESSION['last_activity'] = time();
    }

    private function checkSessionTimeout() {
        if (isset($_SESSION['last_activity'])) {
            $inactiveTime = time() - $_SESSION['last_activity'];
            
            if ($inactiveTime > $this->sessionTimeout) {
                $this->destroySession();
                return false;
            }
        }
        return true;
    }

    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }

        $conn = $this->db->getConnection();
        
        // Consulta actualizada para la estructura real de la BD
        $stmt = $conn->prepare("
            SELECT u.id, u.username, u.password, u.nombre_completo, u.rol, u.division_id, u.activo,
                   d.nombre as division_name 
            FROM usuarios u 
            LEFT JOIN divisiones d ON u.division_id = d.id 
            WHERE u.username = ? AND u.activo = 1
        ");
        
        if (!$stmt) {
            error_log("Error en preparación de consulta: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verificar si la cuenta está activa
            if (!$user['activo']) {
                $stmt->close();
                return false;
            }
            
            // Verificar contraseña (comparación directa por ahora)
            if ($this->verifyPassword($password, $user['password'])) {
                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);
                
                // Limpiar sesión anterior
                $this->clearPreviousSession();
                
                // Establecer datos de sesión
                $_SESSION['user_authenticated'] = true;
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nombre_completo'] = $user['nombre_completo'];
                $_SESSION['rol'] = $user['rol'];
                $_SESSION['division_id'] = $user['division_id'] ? (int)$user['division_id'] : null;
                $_SESSION['division_name'] = $user['division_name'];
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();
                
                // Actualizar último login y resetear intentos
                $this->updateLastLogin($user['id']);
                
                $stmt->close();
                return true;
            }
        }
        
        $stmt->close();
        
        // Registrar intento fallido (opcional)
        $this->recordFailedLogin($username);
        
        return false;
    }

    private function verifyPassword($inputPassword, $storedPassword) {
        // Por ahora comparación directa, cambiar por password_verify en producción
        return $inputPassword === $storedPassword;
    }

    private function clearPreviousSession() {
        $keysToKeep = ['last_activity'];
        $tempData = [];
        
        foreach ($keysToKeep as $key) {
            if (isset($_SESSION[$key])) {
                $tempData[$key] = $_SESSION[$key];
            }
        }
        
        $_SESSION = $tempData;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_authenticated']) && 
               $_SESSION['user_authenticated'] === true &&
               isset($_SESSION['user_id']) &&
               $this->checkSessionTimeout();
    }

    public function getUserInfo() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? 0,
            'username' => $_SESSION['username'] ?? '',
            'nombre_completo' => $_SESSION['nombre_completo'] ?? '',
            'rol' => $_SESSION['rol'] ?? 'operador',
            'division_id' => $_SESSION['division_id'] ?? null,
            'division_name' => $_SESSION['division_name'] ?? null,
            'login_time' => $_SESSION['login_time'] ?? 0,
            'last_activity' => $_SESSION['last_activity'] ?? 0
        ];
    }

    public function hasRole($role) {
        $userInfo = $this->getUserInfo();
        return $userInfo && $userInfo['rol'] === $role;
    }

    public function isAdmin() {
        return $this->hasRole('admin');
    }

    public function logout() {
        $this->destroySession();
    }

    private function destroySession() {
        // Limpiar variables de sesión
        $_SESSION = [];
        
        // Eliminar cookie de sesión si existe
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
        
        // Destruir sesión
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    private function updateLastLogin($userId) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("UPDATE usuarios SET last_login = NOW(), login_attempts = 0 WHERE id = ?");
            
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error actualizando último login: " . $e->getMessage());
        }
    }

    private function recordFailedLogin($username) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("UPDATE usuarios SET login_attempts = login_attempts + 1 WHERE username = ?");
            
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Error registrando intento fallido: " . $e->getMessage());
        }
    }

    public function requireLogin($redirectTo = 'login.php') {
        if (!$this->isLoggedIn()) {
            header("Location: $redirectTo");
            exit();
        }
    }

    public function requireRole($role, $redirectTo = 'dashboard.php') {
        $this->requireLogin($redirectTo);
        
        if (!$this->hasRole($role)) {
            header("Location: $redirectTo");
            exit();
        }
    }

    public function getSessionInfo() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'session_id' => session_id(),
            'login_time' => $_SESSION['login_time'] ?? 0,
            'last_activity' => $_SESSION['last_activity'] ?? 0,
            'time_remaining' => $this->sessionTimeout - (time() - ($_SESSION['last_activity'] ?? 0)),
            'expires_at' => ($_SESSION['last_activity'] ?? 0) + $this->sessionTimeout
        ];
    }

    // Método para obtener todas las divisiones activas
    public function getDivisiones() {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->prepare("SELECT id, nombre, descripcion FROM divisiones WHERE activo = 1 ORDER BY nombre");
            
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                $divisiones = [];
                
                while ($row = $result->fetch_assoc()) {
                    $divisiones[] = $row;
                }
                
                $stmt->close();
                return $divisiones;
            }
        } catch (Exception $e) {
            error_log("Error obteniendo divisiones: " . $e->getMessage());
        }
        
        return [];
    }

    // Método para verificar si un usuario puede acceder a una división específica
    public function canAccessDivision($divisionId) {
        $userInfo = $this->getUserInfo();
        
        if (!$userInfo) {
            return false;
        }
        
        // Los admin pueden acceder a todo
        if ($userInfo['rol'] === 'admin') {
            return true;
        }
        
        // Los operadores solo pueden acceder a su división
        return $userInfo['division_id'] === $divisionId;
    }
}
?>