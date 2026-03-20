<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config/Database.php';
require_once 'controllers/AlumnoController.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['status' => 'error', 'mensaje' => 'Acceso denegado.']));
}

$database = new Database();
$db = $database->connect();
$id_rol = $_SESSION['id_rol'];

// Función interna para validar permiso contra la BD
function tienePermiso($db, $id_rol, $permisoRequerido) {
    $stmt = $db->prepare("
        SELECT 1 FROM rol_permiso rp 
        JOIN permisos p ON rp.id_permiso = p.id_permiso 
        WHERE rp.id_rol = ? AND p.nombre_permiso = ?
    ");
    $stmt->execute([$id_rol, $permisoRequerido]);
    return $stmt->fetchColumn() !== false;
}

$controller = new AlumnoController();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    if (!tienePermiso($db, $id_rol, 'alumno_insert')) {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'mensaje' => 'No tienes permiso para registrar.']));
    }

    if (isset($_POST['modo']) && $_POST['modo'] === 'masivo') {
        $controller->registrarMasivo($_POST, $_FILES);
    } else {
        $datos = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $controller->registrar($datos);
    }
} 
else if ($metodo === 'GET') {
    if (!tienePermiso($db, $id_rol, 'alumno_select')) {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'mensaje' => 'No tienes permiso para ver registros.']));
    }
    $controller->listar();
}
else if ($metodo === 'DELETE') {
    if (!tienePermiso($db, $id_rol, 'alumno_delete')) {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'mensaje' => 'No tienes permiso para eliminar.']));
    }
    $datos = json_decode(file_get_contents('php://input'), true);
    $controller->eliminar($datos);
}
else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido']);
}
?>