<?php
// Archivo: models/Alumno.php
class Alumno {
    private $conn;
    private $table = 'alumnos';

    public $nocontrol;
    public $nombre;
    public $email;
    public $password_hash;
    public $grupo;
    public $periodo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function crear() {
        $query = "INSERT INTO " . $this->table . " 
                  (nocontrol, nombre, email, password_hash, grupo, periodo) 
                  VALUES (:nocontrol, :nombre, :email, :password_hash, :grupo, :periodo)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nocontrol', $this->nocontrol);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':grupo', $this->grupo);
        $stmt->bindParam(':periodo', $this->periodo);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { throw new Exception("Duplicate"); }
            throw new Exception("Error DB");
        }
    }

    public function leerTodos() {
        // Ordenamos por nombre para evitar problemas si no existe la columna fecha_registro
        $query = "SELECT nocontrol, nombre, grupo, periodo FROM " . $this->table . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function eliminar() {
        $query = "DELETE FROM " . $this->table . " WHERE nocontrol = :nocontrol";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nocontrol', $this->nocontrol);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar en la base de datos.");
        }
    }
}
?>