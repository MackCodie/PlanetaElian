<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost'; 
$db = 'dss'; 
$user = 'root'; 
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
   
    // Solo seleccionamos datos seguros de mostrar
    $stmt = $pdo->query("SELECT id_profesor, nombre, correo FROM profesores ORDER BY id_profesor DESC LIMIT 50");
    
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo json_encode([]);
}
?>