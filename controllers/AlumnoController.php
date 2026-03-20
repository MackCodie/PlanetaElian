<?php
// Archivo: controllers/AlumnoController.php
require_once 'config/Database.php';
require_once 'models/Alumno.php';

class AlumnoController {
    private $db;
    private $alumno;
    private $error_generico = "Favor de introducir correctamente los caracteres";

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->alumno = new Alumno($this->db);
    }

    // --- REGISTRAR INDIVIDUAL ---
    public function registrar($datos) {
        try {
            $this->alumno->nocontrol = strtoupper(trim(strip_tags($datos['nocontrol'] ?? '')));
            $this->alumno->nombre = trim(strip_tags($datos['nombre'] ?? ''));
            $this->alumno->email = filter_var(trim($datos['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $this->alumno->grupo = strtoupper(trim(strip_tags($datos['grupo'] ?? '')));
            $this->alumno->periodo = trim(strip_tags($datos['periodo'] ?? ''));
            $password_plain = $datos['password'] ?? ''; 

            if (!preg_match("/^[A-Z0-9]{8,12}$/", $this->alumno->nocontrol)) throw new Exception($this->error_generico);
            if (strlen($this->alumno->nombre) < 3 || strlen($this->alumno->nombre) > 60) throw new Exception($this->error_generico);
            if (!filter_var($this->alumno->email, FILTER_VALIDATE_EMAIL)) throw new Exception($this->error_generico);
            if (strlen($this->alumno->grupo) > 10 || empty($this->alumno->grupo)) throw new Exception($this->error_generico);
            if (strlen($password_plain) < 8) throw new Exception($this->error_generico);

            $this->alumno->password_hash = password_hash($password_plain, PASSWORD_ARGON2ID, ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 2]);

            if ($this->alumno->crear()) {
                echo json_encode(['status' => 'ok', 'mensaje' => 'Alumno registrado exitosamente.']);
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            if (strpos($mensaje, '23000') !== false || strpos($mensaje, 'Duplicate') !== false) {
                $mensaje = "Error al procesar la solicitud. Verifique los datos ingresados.";
            }
            echo json_encode(['status' => 'error', 'mensaje' => $mensaje]);
        }
    }

    // --- REGISTRAR MASIVO ---
    public function registrarMasivo($postData, $files) {
        try {
            if (empty($postData['grupo']) || empty($postData['periodo'])) throw new Exception("Error al procesar la solicitud.");
            if (!isset($files['archivoTxt']) || $files['archivoTxt']['error'] !== UPLOAD_ERR_OK) throw new Exception("Error al procesar el archivo.");

            $grupo = strtoupper(trim(strip_tags($postData['grupo'])));
            $periodo = strip_tags(trim($postData['periodo']));
            $archivo = $files['archivoTxt']['tmp_name'];

            $handle = fopen($archivo, "r");
            if (!$handle) throw new Exception("Error al procesar el archivo.");

            $header = fgetcsv($handle, 1000, ",");
            $registrados = 0;
            $password_plain = 'Alumno2026!'; 
            $hash_general = password_hash($password_plain, PASSWORD_ARGON2ID, ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 2]);

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) < 3) continue; 
                $this->alumno->nocontrol = preg_replace('/[^A-Z0-9]/', '', strtoupper(trim($data[0])));
                $nombre = trim($data[1]);
                if (isset($data[3]) && trim($data[3]) !== '…') $nombre .= " " . trim($data[3]);
                
                $this->alumno->nombre = strip_tags($nombre);
                $this->alumno->email = filter_var(trim($data[2]), FILTER_SANITIZE_EMAIL);
                $this->alumno->grupo = $grupo;
                $this->alumno->periodo = $periodo;
                $this->alumno->password_hash = $hash_general;

                if (!empty($this->alumno->nocontrol) && filter_var($this->alumno->email, FILTER_VALIDATE_EMAIL)) {
                    try { $this->alumno->crear(); $registrados++; } catch (Exception $e) { continue; } 
                }
            }
            fclose($handle);

            if ($registrados > 0) {
                echo json_encode(['status' => 'ok', 'mensaje' => "¡Éxito! Se registraron $registrados alumnos nuevos."]);
            } else {
                echo json_encode(['status' => 'error', 'mensaje' => "No se registraron alumnos. Verifique los datos ingresados."]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
        }
    }

    // --- LISTAR ---
    public function listar() {
        try {
            $stmt = $this->alumno->leerTodos();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }

    // --- ELIMINAR SEGURO (Re-autenticación) ---
    public function eliminar($datos) {
        try {
            $nocontrol = strtoupper(trim(strip_tags($datos['nocontrol'] ?? '')));
            $admin_password = $datos['admin_password'] ?? '';

            if (empty($nocontrol) || empty($admin_password)) {
                throw new Exception("Acceso denegado o datos incompletos.");
            }

            // Iniciar sesión y validar Admin
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $id_admin = $_SESSION['user_id'] ?? null;

            if (!$id_admin) throw new Exception("Sesión inválida.");

            $stmt = $this->db->prepare("SELECT password_hash FROM usuarios WHERE id_usuario = ? AND id_rol = 1");
            $stmt->execute([$id_admin]);
            $admin_db = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin_db || !password_verify($admin_password, $admin_db['password_hash'])) {
                throw new Exception("Contraseña de administrador incorrecta. Operación cancelada.");
            }

            $this->alumno->nocontrol = $nocontrol;
            if ($this->alumno->eliminar()) {
                echo json_encode(['status' => 'ok', 'mensaje' => 'Alumno eliminado de forma segura.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
        }
    }
}
?>