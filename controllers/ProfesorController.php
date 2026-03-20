<?php
// Archivo: controllers/ProfesorController.php
require_once 'config/Database.php';
require_once 'models/Profesor.php';

class ProfesorController {
    private $db;
    private $profesor;
    private $error_generico = "Favor de introducir correctamente los caracteres";

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->profesor = new Profesor($this->db);
    }

    public function registrar($datos) {
        try {
            $this->profesor->nombre = trim(strip_tags($datos['nombre'] ?? ''));
            $this->profesor->correo = filter_var(trim($datos['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password_plain = $datos['password'] ?? ''; 

            if (!preg_match("/^[a-zA-ZÁ-ÿ\s.]{3,60}$/", $this->profesor->nombre)) throw new Exception($this->error_generico);
            if (!filter_var($this->profesor->correo, FILTER_VALIDATE_EMAIL)) throw new Exception($this->error_generico);
            if (strlen($password_plain) < 8) throw new Exception($this->error_generico);

            // BCRYPT EXCLUSIVO PARA ADMINISTRATIVOS Y DOCENTES
            $this->profesor->password_hash = password_hash($password_plain, PASSWORD_BCRYPT);

            if ($this->profesor->crear()) {
                echo json_encode(['status' => 'ok', 'mensaje' => 'Docente registrado exitosamente.']);
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            if (strpos($mensaje, '23000') !== false || strpos($mensaje, 'Duplicate') !== false) {
                $mensaje = "Error al procesar la solicitud. El correo podría ya estar registrado.";
            }
            echo json_encode(['status' => 'error', 'mensaje' => $mensaje]);
        }
    }

    public function listar() {
        try {
            $stmt = $this->profesor->leerTodos();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }

    public function eliminar($datos) {
        try {
            $id_profesor = filter_var($datos['id_profesor'] ?? '', FILTER_SANITIZE_NUMBER_INT);
            $admin_password = $datos['admin_password'] ?? '';

            if (empty($id_profesor) || empty($admin_password)) {
                throw new Exception("Acceso denegado o datos incompletos.");
            }

            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $id_admin = $_SESSION['user_id'] ?? null;

            if (!$id_admin) throw new Exception("Sesión inválida.");

            $stmt = $this->db->prepare("SELECT password_hash FROM usuarios WHERE id_usuario = ? AND id_rol = 1");
            $stmt->execute([$id_admin]);
            $admin_db = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin_db || !password_verify($admin_password, $admin_db['password_hash'])) {
                throw new Exception("Contraseña de administrador incorrecta. Operación cancelada.");
            }

            $this->profesor->id_profesor = $id_profesor;
            if ($this->profesor->eliminar()) {
                echo json_encode(['status' => 'ok', 'mensaje' => 'Docente eliminado de forma segura.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
        }
    }
}
?>