<?php
require_once 'config/Database.php';

class PermisoController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function actualizarMatriz($datosPermisos) {
        try {
            $this->db->beginTransaction();

            // Borramos permisos actuales de roles 2 y 3 (el Admin no se toca)
            $queryDelete = "DELETE FROM rol_permiso WHERE id_rol IN (2, 3)";
            $this->db->exec($queryDelete);

            if (!empty($datosPermisos)) {
                $queryInsert = "INSERT INTO rol_permiso (id_rol, id_permiso) VALUES (:id_rol, :id_permiso)";
                $stmt = $this->db->prepare($queryInsert);

                foreach ($datosPermisos as $p) {
                    if ($p['id_rol'] == 1) continue; 
                    $stmt->execute([
                        ':id_rol' => $p['id_rol'],
                        ':id_permiso' => $p['id_permiso']
                    ]);
                }
            }

            $this->db->commit();
            echo json_encode(['status' => 'ok', 'mensaje' => 'Matriz actualizada correctamente.']);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
        }
    }

    public function obtenerMatriz() {
        $stmt = $this->db->prepare("SELECT id_rol, id_permiso FROM rol_permiso");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
?>