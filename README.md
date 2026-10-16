 Minimarket - Sistema de Gestión de Productos

Documentación técnica y guía de ejecución del proyecto Minimarket, desarrollado con un backend en Quarkus (Java REST API), un frontend en PHP con cURL y persistencia de datos relacional.

---

 Arquitectura del Sistema

El sistema sigue una arquitectura orientada a servicios estructurada en tres capas principales:

- **Frontend (PHP + Bootstrap):
  - Interfaz web interactiva que consume la API REST del backend mediante llamadas cURL.
  - Vistas principales: `index.php` (listado y creación), `editar.php` (edición de campos) y `eliminar.php` (eliminación lógica).
  - Manejo seguro de datos, fechas y campos nulos.
- **Backend (Quarkus / Java):**
  - API REST optimizada para alto rendimiento.
  - Aplica lógica de negocio y validación de endpoints.
  - Gestión de datos con JPA / Hibernate.
- **Base de Datos Relacional (PostgreSQL / MySQL):
  - Desplegada mediante contenedores (Docker).
  - Estructura relacional orientada al control de inventario de productos.

---

Configuración y Variables de Entorno

Siguiendo las mejores prácticas de seguridad, las credenciales sensibles y parámetros de red no están expuestos en el código fuente del repositorio. Se gestionan mediante un archivo `.env` o variables de entorno del sistema:

| Variable | Descripción | Valor por Defecto |
| :--- | :--- | :--- |
| `DB_HOST` | Host de la base de datos | `localhost` |
| `DB_PORT` | Puerto de conexión a la base de datos | `5432` |
| `DB_NAME` | Nombre de la base de datos | `minimarket_db` |
| `DB_USER` | Usuario con permisos de acceso | `tu_usuario_db` |
| `DB_PASSWORD` | Contraseña segura del usuario | `tu_contrasena_segura` |
| `QUARKUS_HTTP_PORT` | Puerto de escucha del servicio backend | `8080` |
| `API_BASE_URL` | URL base consumida por el frontend | `http://localhost:8080/productos` |

---

🚀 Instrucciones de Ejecución

Prerrequisitos
- Java JDK 17 o superior
- PHP 8.x (con extensión cURL habilitada)
- Docker y Docker Compose
- Apache Maven (incluido ejecutable wrapper `mvnw`)

---

 1. Levantar base de datos

Inicie los servicios de la base de datos relacional en un contenedor Docker ejecutando:

```bash
docker-compose up -d
2. Ejecutar backend
Navegue a la carpeta del backend en la terminal e inicie el servidor Quarkus en modo desarrollo:

En Windows (CMD / PowerShell):

DOS
.\mvnw.cmd quarkus:dev
En Linux / macOS:

Bash
./mvnw quarkus:dev
3. Ejecutar frontend
Desde la carpeta del frontend en la terminal, inicie el servidor web embebido de PHP:

Bash
php -S localhost:8000
