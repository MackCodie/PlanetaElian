<?php
// Archivo: models/Profesor.php
class Profesor {
    private $conn;
    private $table = 'profesores';

    public $id_profesor;
    public $nombre;
    public $correo;
    public $password_hash;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, correo, password_hash) 
                  VALUES (:nombre, :correo, :password_hash)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':password_hash', $this->password_hash);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { throw new Exception("Duplicate"); }
            throw new Exception("Error DB");
        }
    }

    public function leerTodos() {
        $query = "SELECT id_profesor, nombre, correo FROM " . $this->table . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function eliminar() {
        $query = "DELETE FROM " . $this->table . " WHERE id_profesor = :id_profesor";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_profesor', $this->id_profesor);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar en la base de datos.");
        }
    }
}
?>