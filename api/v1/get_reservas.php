<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

require_once '../../helpers/utils.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT * FROM reservas";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $reservas = $stmt->fetchAll();
    sendJsonResponse(['success' => true, 'data' => $reservas]);
} catch (PDOException $e) {
    sendJsonResponse(['success' => false, 'message' => 'Error al obtener reservas', 'error' => $e->getMessage()], 500);
}
