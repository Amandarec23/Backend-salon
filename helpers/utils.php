<?php
// backend/helpers/utils.php

/**
 * Configura los encabezados CORS (Cross-Origin Resource Sharing).
 * Esto permite que tu frontend (ej. React en localhost:3000) pueda hacer solicitudes a tu backend.
 * Ajusta 'http://localhost:3000' al dominio de tu frontend en producción.
 */
function setCorsHeaders()
{
    // Permite solicitudes desde cualquier origen (para desarrollo).
    // En producción, deberías restringirlo a tu dominio de frontend específico.
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Métodos HTTP permitidos
    header("Access-Control-Max-Age: 3600"); // Cuánto tiempo se puede cachear la respuesta preflight
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Manejar solicitudes OPTIONS (preflight requests)
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

/**
 * Sanitiza una cadena de texto para prevenir inyección de código y XSS.
 * @param string $data La cadena a sanitizar.
 * @return string La cadena sanitizada.
 */
function sanitizeInput($data)
{
    $data = trim($data); // Elimina espacios en blanco del inicio y final
    $data = stripslashes($data); // Elimina barras invertidas
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convierte caracteres especiales a entidades HTML
    return $data;
}

/**
 * Envía una respuesta JSON al cliente y termina la ejecución.
 * @param array $data Los datos a enviar en formato JSON.
 * @param int $statusCode El código de estado HTTP.
 */
function sendJsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}