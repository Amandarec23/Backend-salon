🌐 Backend API para Sistema de Reservas de Salón de Eventos
Este repositorio contiene el backend API desarrollado en PHP con MySQL para el "Sistema de Reservas para Salón de Eventos ". Esta API es responsable de gestionar toda la lógica de negocio, la persistencia de datos y la interacción con la base de datos para el sistema de reservas.

🌟 Características Principales de la API
API RESTful: Implementa una arquitectura REST para la comunicación con el frontend, utilizando métodos HTTP estándar (GET, POST).

Gestión de Datos Centralizada:

Información del Salón: Proporciona detalles sobre el salón (descripción, amenidades, precios).

Imágenes del Salón: Sirve las URLs de las imágenes para la galería y el banner.

Verificación de Disponibilidad: Permite consultar la disponibilidad de fechas antes de realizar una reserva.

Gestión de Reservas: Facilita la creación de nuevas reservas.

Testimonios: Permite obtener testimonios aprobados y recibir nuevos testimonios de los usuarios para moderación.

Seguridad Robusta:

Configuración CORS: Implementa encabezados CORS para controlar el acceso desde diferentes orígenes, garantizando una comunicación segura con el frontend.

Sanitización de Datos: Todas las entradas de usuario son sanitizadas para prevenir vulnerabilidades como inyección SQL y Cross-Site Scripting (XSS).

Sentencias Preparadas (PDO): Utiliza PDO con sentencias preparadas para todas las interacciones con la base de datos, protegiendo contra ataques de inyección SQL.

Arquitectura Modular: El código está organizado en archivos y directorios lógicos, facilitando el mantenimiento, la escalabilidad y la comprensión del proyecto.

🛠️ Tecnologías Utilizadas
PHP: Lenguaje de programación del lado del servidor.

MySQL: Sistema de gestión de bases de datos relacionales.

PDO (PHP Data Objects): Extensión de PHP para el acceso a bases de datos de forma segura y consistente.

XAMPP: Entorno de desarrollo que incluye Apache (servidor web) y MySQL (base de datos), ideal para el desarrollo local.

📁 Estructura del Proyecto
La estructura del directorio backend es la siguiente:

backend/
├── api/                  # Contiene los scripts de los endpoints de la API
│   └── v1/               # Versión 1 de la API
│       ├── reservas.php  # Endpoint para gestionar reservas (crear, verificar disponibilidad)
│       ├── salon.php     # Endpoint para obtener información del salón e imágenes
│       └── testimonios.php # Endpoint para obtener y enviar testimonios
├── config/               # Archivos de configuración
│   └── database.php      # Configuración de la conexión a la base de datos
├── helpers/              # Funciones de utilidad auxiliares
│   └── utils.php         # Funciones para CORS, sanitización de entradas, y envío de respuestas JSON
└── index.php             # Router principal de la API (punto de entrada para todas las solicitudes)

🚀 API Endpoints Principales
Todos los endpoints se acceden a través del router principal index.php. La URL base para la API, asumiendo que backend está en tu htdocs de XAMPP, sería http://localhost/backend/api/v1/.

1. GET /api/v1/salon
Descripción: Obtiene los detalles generales del salón de eventos.

Parámetros de Consulta: Ninguno.

Respuesta Exitosa (200 OK):

{
    "id": 1,
    "nombre": "Salón de Eventos Oasis",
    "descripcion": "Nuestro Salón de Eventos Oasis es el lugar perfecto para...",
    "precio_dia": "500.00",
    "precio_noche": "700.00",
    "amenidades": ["Aire acondicionado", "Equipo de sonido", "Iluminación", "Proyector", "Cocina equipada", "Estacionamiento", "Wifi", "Seguridad"]
}

2. GET /api/v1/salon?action=getGalleryImages
Descripción: Obtiene la lista de imágenes del salón para la galería y el banner.

Parámetros de Consulta: action=getGalleryImages

Respuesta Exitosa (200 OK):

[
    { "url_imagen": "/assets/banner-img-1.jpg", "alt_text": "Salón de eventos bellamente decorado" },
    { "url_imagen": "/assets/salon-img-1.jpg", "alt_text": "Vista general del salón" },
    // ... más imágenes
]

3. GET /api/v1/reservas?action=checkAvailability&fecha_inicio=YYYY-MM-DD&fecha_fin=YYYY-MM-DD
Descripción: Verifica si el salón está disponible para un rango de fechas específico.

Parámetros de Consulta:

action=checkAvailability (requerido)

fecha_inicio (requerido, formato YYYY-MM-DD)

fecha_fin (requerido, formato YYYY-MM-DD)

Respuesta Exitosa (200 OK):

Disponible:

{
    "disponible": true,
    "message": "Fechas disponibles."
}

No Disponible:

{
    "disponible": false,
    "fechas_ocupadas": ["2025-08-10 a 2025-08-11"],
    "message": "Fechas no disponibles."
}

4. POST /api/v1/reservas
Descripción: Crea una nueva reserva en el sistema.

Cuerpo de la Solicitud (JSON):

{
    "nombre_completo": "Juan Pérez",
    "email": "juan.perez@example.com",
    "telefono": "1234567890",
    "fecha_inicio": "2025-07-20",
    "fecha_fin": "2025-07-22",
    "num_participantes": 50,
    "costo_total": 1400.00
}

Respuesta Exitosa (201 Created):

{
    "status": "success",
    "message": "Reserva creada exitosamente.",
    "reserva_id": 123
}

Respuesta de Error (400 Bad Request, 409 Conflict, 500 Internal Server Error):

{
    "status": "error",
    "message": "Mensaje de error descriptivo."
}

5. GET /api/v1/testimonios
Descripción: Obtiene una lista de testimonios aprobados para mostrar en el frontend.

Parámetros de Consulta: Ninguno.

Respuesta Exitosa (200 OK):

[
    { "nombre_cliente": "Ana M.", "comentario": "Excelente salón, todo perfecto.", "calificacion": 5 },
    { "nombre_cliente": "Luis P.", "comentario": "Muy buena atención y ubicación.", "calificacion": 4 }
]

6. POST /api/v1/testimonios
Descripción: Permite a los usuarios enviar un nuevo testimonio para su revisión.

Cuerpo de la Solicitud (JSON):

{
    "nombre_cliente": "Nuevo Cliente",
    "comentario": "Me encantó el lugar, muy recomendado.",
    "calificacion": 5
}

Respuesta Exitosa (201 Created):

{
    "status": "success",
    "message": "Testimonio enviado para revisión. ¡Gracias!"
}

⚙️ Configuración y Ejecución con XAMPP
Para poner en marcha esta API en tu entorno local, sigue estos pasos:

Requisitos Previos
XAMPP: Asegúrate de tener XAMPP instalado y funcionando. Puedes descargarlo desde apachefriends.org.

Pasos de Configuración
Inicia XAMPP:

Abre el panel de control de XAMPP.

Inicia los módulos Apache (tu servidor web) y MySQL (tu servidor de base de datos). Asegúrate de que estén en verde.

Crea la Base de Datos:

Abre tu navegador y ve a phpMyAdmin (normalmente http://localhost/phpmyadmin/).

En la barra lateral izquierda, haz clic en "Nueva" para crear una nueva base de datos.

Nómbrala salon_eventos_db.

Selecciona la base de datos salon_eventos_db en la barra lateral.

Ve a la pestaña "SQL" y ejecuta el siguiente script para crear las tablas necesarias:

-- Tabla para almacenar la información del salón
CREATE TABLE IF NOT EXISTS salon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio_dia DECIMAL(10, 2) NOT NULL,
    precio_noche DECIMAL(10, 2),
    amenidades TEXT
);

-- Tabla para almacenar las imágenes del salón
CREATE TABLE IF NOT EXISTS salon_imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_salon INT NOT NULL,
    url_imagen VARCHAR(512) NOT NULL,
    alt_text VARCHAR(255),
    es_banner BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    FOREIGN KEY (id_salon) REFERENCES salon(id) ON DELETE CASCADE
);

-- Tabla para almacenar las reservas
CREATE TABLE IF NOT EXISTS reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_salon INT NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    num_participantes INT NOT NULL,
    costo_total DECIMAL(10, 2) NOT NULL,
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
    FOREIGN KEY (id_salon) REFERENCES salon(id) ON DELETE CASCADE
);

-- Tabla para almacenar testimonios
CREATE TABLE IF NOT EXISTS testimonios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(255) NOT NULL,
    comentario TEXT NOT NULL,
    calificacion INT CHECK (calificacion >= 1 AND calificacion <= 5),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    aprobado BOOLEAN DEFAULT FALSE
);

Inserta Datos de Ejemplo (Recomendado):
Para que la API tenga datos que servir y puedas probarla, inserta algunos datos iniciales para el salón, imágenes y testimonios. En la misma pestaña "SQL" de phpMyAdmin, ejecuta lo siguiente:

INSERT INTO salon (nombre, descripcion, precio_dia, precio_noche, amenidades) VALUES
('Salón de Eventos Oasis', 'Nuestro Salón de Eventos Oasis es el lugar perfecto para celebrar tus momentos más especiales. Con una arquitectura elegante y versátil, ofrecemos un ambiente inigualable para bodas, quinceañeras, eventos corporativos y más. Contamos con una capacidad de hasta 200 personas y personal altamente capacitado para asegurar que tu evento sea un éxito rotundo.', 500.00, 700.00, 'Aire acondicionado,Equipo de sonido,Iluminación,Proyector,Cocina equipada,Estacionamiento,Wifi,Seguridad');

INSERT INTO salon_imagenes (id_salon, url_imagen, alt_text, es_banner, orden) VALUES
(1, '/assets/banner-img-1.jpg', 'Salón de eventos bellamente decorado', TRUE, 1),
(1, '/assets/salon-img-1.jpg', 'Vista general del salón', FALSE, 2),
(1, '/assets/salon-img-2.jpg', 'Salón decorado para boda', FALSE, 3),
(1, '/assets/salon-img-3.jpg', 'Área de recepción', FALSE, 4),
(1, '/assets/gallery-img-1.jpg', 'Evento nocturno', FALSE, 5),
(1, '/assets/gallery-img-2.jpg', 'Decoración floral', FALSE, 6),
(1, '/assets/gallery-img-3.jpg', 'Mesa de dulces', FALSE, 7),
(1, '/assets/gallery-img-4.jpg', 'Pista de baile', FALSE, 8),
(1, '/assets/gallery-img-5.jpg', 'Barra de bebidas', FALSE, 9),
(1, '/assets/gallery-img-6.jpg', 'Fachada del salón', FALSE, 10);

INSERT INTO testimonios (nombre_cliente, comentario, calificacion, aprobado) VALUES
('Ana M.', 'El Salón Oasis superó todas nuestras expectativas. Nuestra boda fue mágica, el equipo es muy profesional. ¡Lo recomendamos al 100%!', 5, TRUE),
('Luis P.', 'Celebré mi cumpleaños aquí y todo fue perfecto. El espacio es hermoso y la atención al detalle es impecable. ¡Gracias!', 4, TRUE),
('Sofía G.', 'Un lugar increíble para eventos corporativos. El ambiente es sofisticado y la tecnología funciona de maravilla. Volveremos.', 5, TRUE),
('Diego R.', 'El personal fue muy atento y flexible con nuestras peticiones. Mi quinceañera fue un sueño hecho realidad. Muy agradecida.', 5, TRUE);

Coloca la Carpeta backend:

Copia la carpeta completa backend (que contiene api, config, helpers, index.php y sus subarchivos) dentro del directorio htdocs de tu instalación de XAMPP. La ruta completa debería ser algo como C:\xampp\htdocs\backend.

Verificación de la API
Una vez que Apache y MySQL estén corriendo y los archivos estén en su lugar, puedes probar la API directamente en tu navegador o con herramientas como Postman/Insomnia:

Información del Salón: Abre http://localhost/backend/api/v1/salon

Imágenes del Salón: Abre http://localhost/backend/api/v1/salon?action=getGalleryImages

Testimonios: Abre http://localhost/backend/api/v1/testimonios

Deberías ver respuestas JSON en tu navegador. Para POST y checkAvailability, necesitarás una herramienta como Postman o el frontend de React.

🤝 Contribuciones
Las contribuciones son bienvenidas. Si encuentras un error o tienes una mejora, por favor abre un issue o envía un pull request.

👤 Autor
Ing. David Caro Morales

📄 Licencia
Este proyecto está bajo la Licencia MIT. Consulta el archivo LICENSE para más detalles.
