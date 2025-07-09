<?php
// backend/api/v1/salon.php

// Asegurarse de que las funciones de utilidad y la conexión a la DB estén disponibles
if (!function_exists('sendJsonResponse')) {
    require_once '../../helpers/utils.php';
}
if (!class_exists('Database')) {
    require_once '../../config/database.php';
}

$database = new Database();
$db = $database->getConnection();

// Obtener el método de la solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el parámetro 'action' de la URL (ej. ?action=getGalleryImages)
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Manejar solicitudes GET
if ($method === 'GET') {
    if ($action === 'getGalleryImages') {
        // Obtener imágenes de la galería
        // Asumimos que el ID del salón es 1 por defecto para este ejemplo.
        // En una aplicación real, podrías pasar el ID del salón como parámetro.
        $query = "SELECT url_imagen, alt_text FROM salon_imagenes WHERE id_salon = 1 ORDER BY orden ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $images = $stmt->fetchAll();

        sendJsonResponse($images);
    } else {
        // Obtener los detalles del salón
        // Asumimos que el ID del salón es 1 por defecto para este ejemplo.
        $query = "SELECT id, nombre, descripcion, precio_dia, precio_noche, amenidades FROM salon WHERE id = 1 LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $salon = $stmt->fetch();

        if ($salon) {
            // Convertir la cadena de amenidades a un array si se guardó como tal
            // Si se guardó como JSON, usar json_decode()
            $salon['amenidades'] = explode(', ', $salon['amenidades']); // Ejemplo para cadena separada por comas

            sendJsonResponse($salon);
        } else {
            sendJsonResponse(["message" => "Salón no encontrado."], 404);
        }
    }
} else {
    // Método no permitido
    sendJsonResponse(["message" => "Método no permitido."], 405);
}