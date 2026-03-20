<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$host = 'localhost'; $db = 'sistema_dss'; $user = 'root'; $pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // 1. PREVENCIÓN DE IDOR: El identificador viene de la sesión segura, no del usuario.
    // Asumimos que al hacer login, guardaste su número de control en la sesión.
    $mi_nocontrol = $_SESSION['nocontrol'] ?? '21460266'; // Usando el de BRAYAN RAFAEL para el ejemplo
    
    // 2. VERIFICACIÓN RBAC
    $stmtAcceso = $pdo->prepare("CALL sp_verificar_acceso(:id, :permiso, @tiene_acceso)");
    // Aquí pasarías el id_usuario real de la sesión. Usamos 4 como ejemplo del rol 'Alumno'
    $stmtAcceso->execute([':id' => 4, ':permiso' => 'ver_asistencia']); 
    $resultadoAcceso = $pdo->query("SELECT @tiene_acceso AS acceso")->fetch();

    if ($resultadoAcceso['acceso'] != 1) {
        http_response_code(403);
        throw new Exception("Acceso denegado por RBAC.");
    }

    // 3. CONSULTA SEGURA (Solo trae mis datos)
    $sql = "SELECT a.fecha, a.estado, m.nombre_materia 
            FROM asistencias a
            INNER JOIN alumnos al ON a.id_alumno = al.id_alumno
            INNER JOIN materias m ON a.id_materia = m.id_materia
            WHERE al.nocontrol = :nocontrol
            ORDER BY a.fecha DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nocontrol' => $mi_nocontrol]);
    $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'ok', 
        'alumno' => 'CARRILLO SOLIS BRAYAN RAFAEL', // Idealmente esto también viene de la BD/Sesión
        'data' => $asistencias
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
?>