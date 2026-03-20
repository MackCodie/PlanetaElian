<?php
header('Content-Type: application/json; charset=utf-8');

// Configuración de base de datos (Ajusta si usas 'dss' o 'sistema_toni')
$host = 'localhost'; 
$db = 'dss'; 
$user = 'root'; 
$pass = '';

try {
    // La conexión PDO ya incluye protecciones base si se usa correctamente
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false // Fundamental para evitar Inyecciones SQL
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recibir datos del formulario (o de JSON)
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $nombre = strip_tags(trim($input['nombre']));
        $correo = filter_var(trim($input['correo']), FILTER_SANITIZE_EMAIL);
        $password_plain = $input['password'];

        // Validación básica
        if (empty($nombre) || empty($correo) || empty($password_plain)) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato del correo no es válido.");
        }

        // ENCRIPTACIÓN BCRYPT (El estándar seguro en PHP)
        $password_hash = password_hash($password_plain, PASSWORD_BCRYPT);

        // CONSULTA PREPARADA (Protección contra Inyección SQL)
        $sql = "INSERT INTO profesores (nombre, correo, password_hash) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Al pasar los datos en el execute, PDO los sanitiza automáticamente
        if ($stmt->execute([$nombre, $correo, $password_hash])) {
            echo json_encode(['status' => 'ok', 'mensaje' => 'Profesor registrado con éxito.']);
        }
    }

} catch (PDOException $e) {
    // Manejo de error si el correo ya existe (UNIQUE constraint)
    if ($e->getCode() == 23000) {
        echo json_encode(['status' => 'error', 'mensaje' => 'Este correo ya está registrado.']);
    } else {
        echo json_encode(['status' => 'error', 'mensaje' => 'Error de base de datos.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
?>