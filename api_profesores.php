<?php
// Archivo: api_profesores.php
header('Content-Type: application/json; charset=utf-8');
require_once 'controllers/ProfesorController.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    http_response_code(401);
    die(json_encode(['status' => 'error', 'mensaje' => 'Acceso denegado.']));
}

$controller = new ProfesorController();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $controller->registrar($datos);
} 
else if ($metodo === 'GET') {
    $controller->listar();
}
else if ($metodo === 'DELETE') {
    $datos = json_decode(file_get_contents('php://input'), true);
    $controller->eliminar($datos);
}
else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido']);
}
?>