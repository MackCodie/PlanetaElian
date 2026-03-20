<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$host = 'localhost'; $db = 'sistema_dss'; $user = 'root'; $pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // Simulación del ID del profesor logueado en la sesión
    $id_profesor_actual = $_SESSION['id_profesor'] ?? 1; // 1 para pruebas
    $permiso_requerido = 'tomar_asistencia';

    // 1. VERIFICACIÓN RBAC (La arquitectura que definimos antes)
    $stmtAcceso = $pdo->prepare("CALL sp_verificar_acceso(:id, :permiso, @tiene_acceso)");
    $stmtAcceso->execute([':id' => $id_profesor_actual, ':permiso' => $permiso_requerido]);
    $resultadoAcceso = $pdo->query("SELECT @tiene_acceso AS acceso")->fetch();

    if ($resultadoAcceso['acceso'] != 1) {
        http_response_code(403);
        throw new Exception("Bloqueado por RBAC: No tienes privilegios para registrar asistencias.");
    }

    // 2. RECIBIR Y VALIDAR DATOS
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id_materia']) || empty($input['fecha']) || empty($input['alumnos'])) {
        throw new Exception("Faltan datos en la solicitud.");
    }

    $id_materia = (int) $input['id_materia'];
    $fecha = $input['fecha']; // Idealmente validar formato YYYY-MM-DD
    $estados_permitidos = ['Presente', 'Falta', 'Retardo', 'Justificado'];

    // 3. TRANSACCIÓN SEGURA (Todo o nada)
    $pdo->beginTransaction();

    // Consulta preparada para insertar masivamente
    $sql = "INSERT INTO asistencias (id_alumno, id_profesor, id_materia, fecha, estado) 
            VALUES (:id_alumno, :id_profesor, :id_materia, :fecha, :estado)";
    $stmtInsert = $pdo->prepare($sql);

    foreach ($input['alumnos'] as $id_alumno => $estado) {
        // Validar que no inyecten un estado basura
        if (!in_array($estado, $estados_permitidos)) {
            $estado = 'Falta'; // Fallback por defecto
        }

        $stmtInsert->execute([
            ':id_alumno' => (int) $id_alumno,
            ':id_profesor' => $id_profesor_actual,
            ':id_materia' => $id_materia,
            ':fecha' => $fecha,
            ':estado' => $estado
        ]);
    }

    $pdo->commit(); // Confirmar cambios en la BD

    echo json_encode(['status' => 'ok', 'mensaje' => 'Asistencia guardada con éxito bajo validación RBAC.']);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack(); // Si hay un error a la mitad, se revierte todo para evitar datos corruptos
    }
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
?>