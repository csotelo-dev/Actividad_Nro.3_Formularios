<?php
/**
 * Archivo de conexión a la base de datos usando PDO.
 * 
 * - Carga las credenciales de la base de datos desde un archivo `.env`.
 * - Establece una conexión segura con MySQL.
 * - Configura el manejo de errores para reportarlos correctamente.
 * - Garantiza la compatibilidad con caracteres especiales (utf8mb4).
 * 
 * @author  César Augusto Sotelo Zapata
 * @version 1.0
 * @since   2025-03-11
 */

# ------------------------------
#  CARGA DEL ARCHIVO .ENV
# ------------------------------

/**
 * Carga las variables de entorno desde el archivo .env.
 * Si el archivo no existe, detiene la ejecución con un mensaje de error.
 */
if (file_exists(__DIR__ . "/.env")) {
    $variables = parse_ini_file(__DIR__ . "/.env");
} else {
    die("<h2 style='color: red;'> Error: No se encontró el archivo de configuración (.env).</h2>");
}

# ------------------------------
#  VALIDACIÓN DE CREDENCIALES
# ------------------------------

/**
 * Verifica que las variables necesarias están definidas en .env.
 * Si falta alguna, detiene la ejecución con un mensaje de error.
 */
if (!isset($variables['DB_HOST'], $variables['DB_USER'], $variables['DB_PASS'], $variables['DB_NAME'])) {
    die("<h2 style='color: red;'> Error: Configuración de base de datos incompleta en .env.</h2>");
}

# ------------------------------
#  CONEXIÓN A LA BASE DE DATOS CON PDO
# ------------------------------

/**
 * Intenta establecer la conexión con la base de datos usando PDO.
 * 
 * @throws PDOException Si ocurre un error de conexión.
 */
try {
    // Creación del objeto de conexión PDO
    $conexion = new PDO(
        "mysql:host={$variables['DB_HOST']};dbname={$variables['DB_NAME']};charset=utf8mb4",
        $variables['DB_USER'],
        $variables['DB_PASS']
    );

    // Configuración de PDO para lanzar excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $e) {
    /**
     * Si la conexión falla, se captura la excepción y se muestra un mensaje de error.
    **/

    // Mostrar un mensaje de error genérico para evitar exponer detalles internos
    die("<h2 style='color: red;'>Error de conexión. Contacte al administrador.</h2>");
}
?>
