<?php
// Archivo: api_rbac.php
header('Content-Type: application/json; charset=utf-8');
require_once 'controllers/PermisoController.php';

session_start();

// Solo el ADMIN entra aquí
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'mensaje' => 'No autorizado']));
}

$controller = new PermisoController();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    $controller->actualizarMatriz($datos);
} 
else if ($metodo === 'GET') {
    $controller->obtenerMatriz();
}
?>