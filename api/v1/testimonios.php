<?php
// backend/api/v1/testimonios.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");



// Asegurarse de que las funciones de utilidad y la conexión a la DB estén disponibles
if (!function_exists('sendJsonResponse')) {
    require_once '../../helpers/utils.php';
}
if (!class_exists('Database')) {
    require_once '../../config/database.php';
}

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// Manejar solicitudes GET
if ($method === 'GET') {
    // Obtener testimonios aprobados
    $query = "SELECT nombre_cliente, comentario, calificacion FROM testimonios WHERE aprobado = TRUE ORDER BY fecha_creacion DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $testimonios_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Renombramos las claves para adaptarlas al frontend
    $testimonios = array_map(function ($row) {
        return [
            "name" => $row["nombre_cliente"],
            "comment" => $row["comentario"],
            "rating" => (int) $row["calificacion"]
        ];
    }, $testimonios_raw);

    sendJsonResponse($testimonios);;
}
// Manejar solicitudes POST (para que los usuarios envíen un testimonio)
else if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    // Validar datos de entrada
    if (empty($data->nombre_cliente) || empty($data->comentario) || !isset($data->calificacion)) {
        sendJsonResponse(["status" => "error", "message" => "Nombre, comentario y calificación son requeridos."], 400);
    }

    // Sanitizar datos
    $nombre_cliente = sanitizeInput($data->nombre_cliente);
    $comentario = sanitizeInput($data->comentario);
    $calificacion = (int) $data->calificacion;

    // Validar calificación
    if ($calificacion < 1 || $calificacion > 5) {
        sendJsonResponse(["status" => "error", "message" => "La calificación debe ser entre 1 y 5."], 400);
    }

    // Insertar el testimonio (por defecto 'aprobado' será FALSE para moderación)
    $query = "INSERT INTO testimonios (nombre_cliente, comentario, calificacion)
              VALUES (:nombre_cliente, :comentario, :calificacion)";

    $stmt = $db->prepare($query);

    // Vincular parámetros
    $stmt->bindParam(':nombre_cliente', $nombre_cliente);
    $stmt->bindParam(':comentario', $comentario);
    $stmt->bindParam(':calificacion', $calificacion);

    if ($stmt->execute()) {
        sendJsonResponse(["status" => "success", "message" => "Testimonio enviado para revisión. ¡Gracias!"], 201);
    } else {
        sendJsonResponse(["status" => "error", "message" => "No se pudo enviar el testimonio."], 500);
    }
} else {
    sendJsonResponse(["message" => "Método no permitido."], 405);
}