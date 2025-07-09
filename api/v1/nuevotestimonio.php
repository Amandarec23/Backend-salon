<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../../config/database.php';
require_once '../../helpers/utils.php';

$db = (new Database())->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (empty($data->nombre_cliente) || empty($data->comentario) || !isset($data->calificacion)) {
    sendJsonResponse(["status" => "error", "message" => "Todos los campos son requeridos."], 400);
}

$nombre = sanitizeInput($data->nombre_cliente);
$comentario = sanitizeInput($data->comentario);
$calificacion = (int)$data->calificacion;

if ($calificacion < 1 || $calificacion > 5) {
    sendJsonResponse(["status" => "error", "message" => "La calificación debe estar entre 1 y 5."], 400);
}

$query = "INSERT INTO testimonios (nombre_cliente, comentario, calificacion) 
          VALUES (:nombre, :comentario, :calificacion)";

$stmt = $db->prepare($query);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':comentario', $comentario);
$stmt->bindParam(':calificacion', $calificacion);

if ($stmt->execute()) {
    sendJsonResponse(["status" => "success", "message" => "Tu testimonio ha sido enviado para revisión. ¡Gracias!"], 201);
} else {
    sendJsonResponse(["status" => "error", "message" => "No se pudo guardar tu testimonio."], 500);
}