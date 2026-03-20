<?php
// Archivo: api_verificar_sesion.php
header('Content-Type: application/json; charset=utf-8');
require_once 'config/Database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['autenticado' => false]);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    
    // Obtenemos el ID de rol de la sesión
    $id_rol = $_SESSION['id_rol'];

    // Consultamos la matriz de permisos para este rol
    $stmt = $db->prepare("
        SELECT p.nombre_permiso 
        FROM rol_permiso rp 
        JOIN permisos p ON rp.id_permiso = p.id_permiso 
        WHERE rp.id_rol = ?
    ");
    $stmt->execute([$id_rol]);
    $permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'autenticado' => true,
        'rol' => $_SESSION['role'],
        'permisos' => $permisos
    ]);

} catch (Exception $e) {
    echo json_encode(['autenticado' => false]);
}