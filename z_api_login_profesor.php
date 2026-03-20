<?php
header('Content-Type: application/json; charset=utf-8');
session_start(); // Iniciamos sesión para guardar los datos si el login es exitoso

$host = 'localhost'; 
$db = 'dss'; 
$user = 'root'; 
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false // Blindaje contra Inyección SQL
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $correo = trim($input['correo'] ?? '');
        $password_plain = $input['password'] ?? '';

        if (empty($correo) || empty($password_plain)) {
            throw new Exception("El correo y la contraseña son obligatorios.");
        }

        // 1. BUSCAR AL USUARIO (Protegido por PDO)
        // Solo traemos el hash de la BD, no lo comparamos directamente en SQL
        $stmt = $pdo->prepare("SELECT id_profesor, nombre, password_hash FROM profesores WHERE correo = ?");
        $stmt->execute([$correo]);
        $profesor = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. VERIFICAR LA CONTRASEÑA (Protegido por password_verify)
        // Si el usuario existe y la contraseña introducida coincide con el hash guardado
        if ($profesor && password_verify($password_plain, $profesor['password_hash'])) {
            
            // Login exitoso: Guardamos datos en variables de sesión
            $_SESSION['id_profesor'] = $profesor['id_profesor'];
            $_SESSION['nombre_profesor'] = $profesor['nombre'];

            echo json_encode([
                'status' => 'ok', 
                'mensaje' => '¡Bienvenido al sistema, ' . $profesor['nombre'] . '!'
            ]);
        } else {
            // Login fallido: Mensaje genérico (Anti-enumeración)
            // No le decimos al atacante si falló el correo o la contraseña
            echo json_encode([
                'status' => 'error', 
                'mensaje' => 'Credenciales incorrectas.'
            ]);
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Error en el servidor.']);
}
?>