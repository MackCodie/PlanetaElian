<?php
header('Content-Type: application/json; charset=utf-8');
$host = 'localhost'; $db = 'dss'; $user = 'root'; $pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = trim($_POST['usuario']);
        $password = $_POST['password'];
        $algoritmo = $_POST['algoritmo']; 
        
        $hashFinal = '';

        switch ($algoritmo) {
            case 'md5':
                $hashFinal = md5($password);
                break;
            case 'sha1':
                $hashFinal = sha1($password);
                break;
            case 'sha256':  
                $hashFinal = hash('sha256', $password);
                break;
            case 'default':
                $hashFinal = password_hash($password, PASSWORD_DEFAULT);
                break;
            default:
                $hashFinal = 'Algoritmo no válido';
        }

        $sql = "INSERT INTO laboratorio_hashing (usuario_prueba, password_texto, password_encriptado, metodo_usado) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$usuario, $password, $hashFinal, $algoritmo])) {
            echo json_encode(['status' => 'ok', 'mensaje' => 'Hash generado correctamente']);
        } else {
            echo json_encode(['status' => 'error', 'mensaje' => 'Error al guardar']);
        }
    } else {
        $stmt = $pdo->query("SELECT * FROM laboratorio_hashing ORDER BY id DESC LIMIT 20");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
?>