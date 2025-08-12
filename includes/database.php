<?php
require_once 'config.php';

class Database
{
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($this->conn->connect_error) {
                throw new Exception("Error de conexión: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8");
        } catch (Exception $e) {
            error_log($e->getMessage());
            die("Error al conectar con la base de datos. Por favor intente más tarde.");
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // Método seguro para ejecutar consultas
    public function executeQuery($sql, $params = [], $types = "")
    {
        try {
            $stmt = $this->conn->prepare($sql);

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            error_log("Error en consulta SQL: " . $e->getMessage());
            return false;
        }
    }
}