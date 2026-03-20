<?php
header('Content-Type: application/json; charset=utf-8');
$host = 'localhost'; $db = 'dss'; $user = 'root'; $pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
   
    $stmt = $pdo->query("SELECT nocontrol, nombre, grupo, periodo FROM alumnos ORDER BY fecha_registro DESC LIMIT 50");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode([]);
}
?>