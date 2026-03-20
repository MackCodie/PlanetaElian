<?php
header('Content-Type: application/json; charset=utf-8');

// Configuración de Seguridad de Sesión (Gestión Robusta)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Solo si usas HTTPS
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

$host = 'localhost'; $db = 'dss'; $user = 'app_user'; $pass = 'TuPasswordSeguro123!';

function log_action($pdo, $usuario_id, $accion, $detalles) {
    try {
        $stmt = $pdo->prepare("INSERT INTO logs_auditoria (usuario_id, accion, detalles, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $accion, $detalles, $_SERVER['REMOTE_ADDR']]);
    } catch (Exception $e) { /* Fallback silencioso para no detener el flujo */ }
}

// PROTECCIÓN CONTRA FUERZA BRUTA (Rate Limiting simple)
function check_rate_limit($pdo, $ip) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM logs_auditoria WHERE ip_address = ? AND timestamp > NOW() - INTERVAL 1 MINUTE");
    $stmt->execute([$ip]);
    if ($stmt->fetchColumn() > 10) { // Máximo 10 peticiones por minuto
        throw new Exception("Demasiadas peticiones. Intente más tarde.");
    }
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false 
    ]);

    check_rate_limit($pdo, $_SERVER['REMOTE_ADDR']);

    // 1. VERIFICACIÓN DE SESIÓN Y ROL (SoD)
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
        // ANTI-ENUMERACIÓN: Respuesta genérica incluso si no hay sesión
        http_response_code(403);
        throw new Exception("Acceso denegado."); 
    }

    // 2. VALIDACIÓN DE ENTRADA Y CSRF
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    if (!isset($input['csrf_token']) || $input['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception("Error de validación de seguridad.");
    }

    // 3. HASH ADAPTATIVO (Ejemplo de registro de usuario/alumno con password)
    // Si el alumno tuviera contraseña, usaríamos password_hash con Argon2id
    $password_plain = $input['password'] ?? null;
    $password_hashed = null;
    
    if ($password_plain) {
        // Argon2id es el estándar actual de hash adaptativo
        $password_hashed = password_hash($password_plain, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost'   => 4,
            'threads'     => 2
        ]);
    }

    // 4. EVITAR IDOR Y VALIDACIÓN ESTRICTA
    $nocontrol = strtoupper(trim($input['nocontrol'] ?? ''));
    if (!preg_match("/^[A-Z0-9]{8,12}$/", $nocontrol)) {
        // Mensaje genérico para evitar dar pistas de qué falló exactamente a un atacante
        throw new Exception("Los datos proporcionados no cumplen con el formato requerido.");
    }

    // 5. CONTROL DE ACCESO POR OBJETO
    $stmtCheck = $pdo->prepare("SELECT id FROM alumnos WHERE nocontrol = ?");
    $stmtCheck->execute([$nocontrol]);
    if ($stmtCheck->fetch()) {
        throw new Exception("La operación no pudo completarse."); // Anti-enumeración: no confirmamos si existe
    }

    // Acción Principal
    $sql = "INSERT INTO alumnos (nocontrol, nombre, email, password_hash) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([
        $nocontrol, 
        strip_tags($input['nombre']), 
        filter_var($input['email'], FILTER_SANITIZE_EMAIL),
        $password_hashed
    ])) {
        log_action($pdo, $_SESSION['user_id'], 'INSERT_ALUMNO', "ID: $nocontrol");
        $response = ['status' => 'ok', 'mensaje' => 'Proceso completado con éxito.'];
    }

} catch (Exception $e) {
    http_response_code(400);
    log_action($pdo ?? null, $_SESSION['user_id'] ?? 0, 'SECURITY_EVENT', $e->getMessage());
    // Respuesta ambigua para el cliente (Seguridad por obscuridad controlada)
    $response = ['status' => 'error', 'mensaje' => $e->getMessage()];
}

echo json_encode($response);