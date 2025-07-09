<?php
// backend/config/database.php

/**
 * Clase para manejar la conexión a la base de datos usando PDO.
 */
class Database
{
    private $host = "localhost"; // Host de la base de datos (generalmente localhost para XAMPP)
    private $db_name = "salon_eventos"; // Nombre de tu base de datos
    private $username = "root"; // Usuario de la base de datos (por defecto 'root' en XAMPP)
    private $password = ""; // Contraseña de la base de datos (por defecto vacía en XAMPP)
    public $conn;

    /**
     * Obtiene la conexión a la base de datos.
     * @return PDO|null Objeto PDO de la conexión o null si falla.
     */
    public function getConnection()
    {
        $this->conn = null;
        try {
            // Configuración de DSN (Data Source Name)
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            // Opciones de PDO para manejo de errores y modo de fetch
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en caso de error
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Obtener resultados como arrays asociativos
                PDO::ATTR_EMULATE_PREPARES => false, // Deshabilitar emulación de preparaciones para seguridad
            ];
            // Crear una nueva instancia de PDO
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $exception) {
            // Capturar y mostrar errores de conexión
            error_log("Error de conexión a la base de datos: " . $exception->getMessage());
            // En un entorno de producción, no se debe mostrar el mensaje de error directamente al usuario.
            // En su lugar, se podría devolver un error genérico o registrarlo.
            // Aquí se puede enviar una respuesta JSON o un mensaje de error genérico
            http_response_code(500); // Internal Server Error
            echo json_encode(["message" => "Error de conexión a la base de datos."]);
            exit(); // Terminar la ejecución si no hay conexión a la DB
        }
        return $this->conn;
    }
}