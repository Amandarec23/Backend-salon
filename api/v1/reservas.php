<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!function_exists('sendJsonResponse')) {
    require_once '../../helpers/utils.php';
}

if (!class_exists('Database')) {
    require_once '../../config/database.php';
}

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Manejar solicitudes GET
if ($method === 'GET') {
    if ($action === 'checkAvailability') {
        $fecha_inicio = isset($_GET['fecha_inicio']) ? sanitizeInput($_GET['fecha_inicio']) : '';
        $fecha_fin = isset($_GET['fecha_fin']) ? sanitizeInput($_GET['fecha_fin']) : '';

        if (empty($fecha_inicio) || empty($fecha_fin)) {
            sendJsonResponse(["message" => "Fechas de inicio y fin son requeridas."], 400);
        }

        $query = "SELECT fecha_inicio, fecha_fin FROM reservas 
                  WHERE id_salon = 1 AND estado != 'cancelada' AND
                  ((fecha_inicio <= :fecha_fin AND fecha_fin >= :fecha_inicio))";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();

        $overlapping_reservations = $stmt->fetchAll();

        if (count($overlapping_reservations) > 0) {
            $fechas_ocupadas = [];
            foreach ($overlapping_reservations as $res) {
                $fechas_ocupadas[] = $res['fecha_inicio'] . ' a ' . $res['fecha_fin'];
            }
            sendJsonResponse([
                "disponible" => false,
                "fechas_ocupadas" => $fechas_ocupadas,
                "message" => "Fechas no disponibles."
            ]);
        } else {
            sendJsonResponse(["disponible" => true, "message" => "Fechas disponibles."]);
        }
    } else {
        $query = "SELECT * FROM reservas ORDER BY fecha_reserva DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $reservas = $stmt->fetchAll();
        sendJsonResponse($reservas);
    }
}

// Manejar solicitudes POST (crear nueva reserva)
else if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (
        empty($data->nombre_completo) || empty($data->email) || empty($data->telefono) ||
        empty($data->fecha_inicio) || empty($data->fecha_fin) || empty($data->num_participantes) ||
        empty($data->costo_total)
    ) {
        sendJsonResponse(["status" => "error", "message" => "Todos los campos son requeridos."], 400);
    }

    $nombre_completo = sanitizeInput($data->nombre_completo);
    $email = sanitizeInput($data->email);
    $telefono = sanitizeInput($data->telefono);
    $fecha_inicio = sanitizeInput($data->fecha_inicio);
    $fecha_fin = sanitizeInput($data->fecha_fin);
    $num_participantes = (int) $data->num_participantes;
    $costo_total = (float) $data->costo_total;
    $id_salon = 1;
    $estado = 'pendiente';

    // Verificar disponibilidad antes de insertar
    $query_check = "SELECT COUNT(*) FROM reservas 
                    WHERE id_salon = :id_salon AND estado != 'cancelada' AND
                    ((fecha_inicio <= :fecha_fin AND fecha_fin >= :fecha_inicio))";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':id_salon', $id_salon);
    $stmt_check->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt_check->bindParam(':fecha_fin', $fecha_fin);
    $stmt_check->execute();
    $count_overlapping = $stmt_check->fetchColumn();

    if ($count_overlapping > 0) {
        sendJsonResponse(["status" => "error", "message" => "Las fechas seleccionadas ya están reservadas."], 409);
    }

    // Insertar la reserva
    $query = "INSERT INTO reservas (id_salon, nombre_completo, email, telefono, fecha_inicio, fecha_fin, num_participantes, costo_total, estado)
              VALUES (:id_salon, :nombre_completo, :email, :telefono, :fecha_inicio, :fecha_fin, :num_participantes, :costo_total, :estado)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':id_salon', $id_salon);
    $stmt->bindParam(':nombre_completo', $nombre_completo);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);
    $stmt->bindParam(':num_participantes', $num_participantes);
    $stmt->bindParam(':costo_total', $costo_total);
    $stmt->bindParam(':estado', $estado);

    if ($stmt->execute()) {
        sendJsonResponse(["status" => "success", "message" => "Reserva creada exitosamente.", "reserva_id" => $db->lastInsertId()], 201);
    } else {
        sendJsonResponse(["status" => "error", "message" => "No se pudo crear la reserva."], 500);
    }
} else {
    sendJsonResponse(["message" => "Método no permitido."], 405);
}

// Manejar solicitudes para obtener fechas ocupadas
if ($action === 'fechasOcupadas') {
    $query = "SELECT fecha_inicio, fecha_fin FROM reservas 
              WHERE id_salon = 1 AND estado != 'cancelada'";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $fechas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $fechas_ocupadas = [];

    foreach ($fechas as $f) {
        $inicio = new DateTime($f['fecha_inicio']);
        $fin = new DateTime($f['fecha_fin']);
        while ($inicio <= $fin) {
            $fechas_ocupadas[] = $inicio->format('Y-m-d');
            $inicio->modify('+1 day');
        }
    }

    sendJsonResponse(array_unique($fechas_ocupadas));
}