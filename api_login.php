<?php
// Archivo: api_login.php
header('Content-Type: application/json; charset=utf-8');
require_once 'config/Database.php';

session_start();

$database = new Database();
$db = $database->connect();

// Recibimos los datos del fetch
$datos = json_decode(file_get_contents('php://input'), true);

// IMPORTANTE: Tu HTML envía "identificador", no "usuario"
$email = filter_var($datos['identificador'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $datos['password'] ?? '';

if (empty($email) || empty($password)) {
    die(json_encode(['status' => 'error', 'mensaje' => 'Campos vacíos']));
}

try {
    // Buscamos en la tabla usuarios
    $stmt = $db->prepare("SELECT id_usuario, correo, password_hash, id_rol FROM usuarios WHERE correo = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        // --- SESIÓN EXITOSA ---
        $_SESSION['user_id'] = $usuario['id_usuario'];
        $_SESSION['id_rol'] = $usuario['id_rol'];

        // Mapeo de roles
        $roles_nombres = [1 => 'ADMIN', 2 => 'PROFESOR', 3 => 'ALUMNO'];
        $rol_nombre = $roles_nombres[$usuario['id_rol']] ?? 'INVITADO';
        $_SESSION['role'] = $rol_nombre;

        // Redirección según rol
        // Si el rol es 1 (Admin) va a menu_usuarios, si es 2 (Profe) va a dashboard_profesor
        $redirect = ($usuario['id_rol'] == 1) ? 'menu_usuarios.html' : 'dashboard_profesor.html';

        echo json_encode([
            'status' => 'ok',
            'mensaje' => 'Bienvenido',
            'redirect' => $redirect
        ]);
    } else {
        // Fallo de credenciales
        http_response_code(401);
        echo json_encode(['status' => 'error', 'mensaje' => 'Credenciales incorrectas']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'mensaje' => 'Error: ' . $e->getMessage()]);
}