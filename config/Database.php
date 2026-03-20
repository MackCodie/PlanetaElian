<?php
// Archivo: config/Database.php
class Database {
    private $host = 'localhost';
    private $db_name = 'dss'; 
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false 
            ]);
        } catch(PDOException $e) {
            die(json_encode(['status' => 'error', 'mensaje' => 'Error de base de datos.']));
        }
        return $this->conn;
    }
}
?>