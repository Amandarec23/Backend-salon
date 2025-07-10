<?php
require_once '../../helpers/utils.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Solo aceptar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
}

// Obtener los datos del cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['id'])) {
    sendJsonResponse(['success' => false, 'message' => 'Datos insuficientes o id faltante'], 400);
}

$id = (int) $input['id'];
$campos = [];
$params = [];

// Permitir actualización de cualquier campo excepto 'id'
foreach ($input as $campo => $valor) {
    if ($campo === 'id') continue;
    $campos[] = "$campo = :$campo";
    $params[":$campo"] = $valor;
}

if (empty($campos)) {
    sendJsonResponse(['success' => false, 'message' => 'No hay campos para actualizar'], 400);
}

$params[':id'] = $id;
$query = "UPDATE reservas SET ".implode(', ', $campos)." WHERE id = :id";

try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    if ($stmt->rowCount() > 0) {
        sendJsonResponse(['success' => true, 'message' => 'Reserva actualizada correctamente']);
    } else {
        sendJsonResponse(['success' => false, 'message' => 'No se encontró la reserva o no hubo cambios']);
    }
} catch (PDOException $e) {
    sendJsonResponse(['success' => false, 'message' => 'Error al actualizar la reserva', 'error' => $e->getMessage()], 500);
}
