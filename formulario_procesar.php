<?php

/**
 * Procesamiento del formulario de cotización.
 * 
 * Este script maneja la validación, seguridad y almacenamiento de la cotización en la base de datos.
 * 
 * - **Protección CSRF**: Verifica y elimina el token CSRF después del uso.
 * - **Validaciones de entrada**: Se sanitizan y validan los datos antes de guardarlos.
 * - **Conexión a la base de datos**: Se utiliza PDO para seguridad y eficiencia.
 * - **Manejo de errores**: Se registran errores en logs y se muestran mensajes controlados al usuario.
 * 
 * @author  César Augusto Sotelo Zapata
 * @version 1.0
 * @since   2025-03-11
 */

session_start(); // Iniciar sesión para manejar tokens CSRF
require "cnx.php"; // Conexión a la base de datos

# ------------------------------
# PROTECCIÓN CSRF
# ------------------------------

/**
 * Verifica la validez del token CSRF para evitar ataques de falsificación de solicitudes.
 */
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    unset($_SESSION['csrf_token']); 
    die("<h2 style='color: red;'>Error: CSRF token inválido. Recarga la página e intenta nuevamente.</h2>");
}

/**
 * Una vez validado, el token CSRF se elimina de la sesión para evitar reutilización.
 */
unset($_SESSION['csrf_token']);

# ------------------------------
# VALIDACIÓN DE CONEXIÓN A LA BASE DE DATOS
# ------------------------------

/**
 * Verifica que la conexión a la base de datos se haya establecido correctamente.
 */
if (!isset($conexion)) {
    die("<h2 style='color: red;'>Error crítico: No se pudo establecer conexión a la base de datos.</h2>");
}

# ------------------------------
# PROCESAMIENTO DEL FORMULARIO
# ------------------------------

/**
 * Si el método de solicitud es POST, se procesan los datos del formulario.
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {

        # ------------------------------
        # SANITIZACIÓN Y VALIDACIÓN DE ENTRADAS
        # ------------------------------

        /**
         * Sanitiza y valida los campos del formulario.
         */
        $nombre = filter_var(trim($_POST['nombre'] ?? ''), FILTER_SANITIZE_STRING);
        $ciudad = filter_var(trim($_POST['ciudad'] ?? ''), FILTER_SANITIZE_STRING);
        $direccion = filter_var(trim($_POST['direccion'] ?? ''), FILTER_SANITIZE_STRING);
        $celular = filter_var(trim($_POST['celular'] ?? ''), FILTER_SANITIZE_NUMBER_INT);

        /**
         * Validación del campo celular.
         * - Debe contener solo números.
         * - Debe tener entre 10 y 11 dígitos.
         */
        if (!preg_match('/^\d{10}$/', $celular)) {
            throw new Exception("Número de celular inválido.");
        }

        # ------------------------------
        # VALIDACIÓN DE PRODUCTOS
        # ------------------------------

        /**
         * Recupera la lista de productos seleccionados y sus cantidades.
         */
        $productosSeleccionados = $_POST['productos'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];

        /**
         * Verifica que los productos y las cantidades sean arrays válidos y coincidan en tamaño.
         */
        if (!is_array($productosSeleccionados) || !is_array($cantidades) || count($productosSeleccionados) !== count($cantidades)) {
            throw new Exception("Los productos y cantidades no son válidos.");
        }

        # ------------------------------
        # CONVERSIÓN DE DATOS A JSON
        # ------------------------------

        /**
         * Convierte los arrays de productos y cantidades a formato JSON para almacenarlos en la base de datos.
         */
        $productosJSON = json_encode($productosSeleccionados, JSON_UNESCAPED_UNICODE);
        $cantidadesJSON = json_encode($cantidades, JSON_UNESCAPED_UNICODE);

        # ------------------------------
        #  INSERCIÓN EN BASE DE DATOS
        # ------------------------------

        /**
         * Prepara la consulta SQL utilizando consultas preparadas para evitar inyección SQL.
         */
        $sql = "INSERT INTO cotizaciones (nombre, ciudad, direccion, celular, productos, cantidades)
                VALUES (:nombre, :ciudad, :direccion, :celular, :productos, :cantidades)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':ciudad', $ciudad);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':productos', $productosJSON);
        $stmt->bindParam(':cantidades', $cantidadesJSON);

        /**
         * Ejecuta la consulta en la base de datos.
         */
        $stmt->execute();
        

        # ------------------------------
        # REDIRECCIÓN A LA PÁGINA DE RESULTADOS
        # ------------------------------

        /**
         * Muestra un mensaje de éxito y redirige a la página de vista.
         */
        echo "<div class='alert alert-success'> Cotización guardada exitosamente.</div>";
        header("Location: vista.php");
        exit;

    } catch (Exception $e) {
        /**
         * Captura cualquier excepción y maneja el error de manera segura.
         * Se registra en logs y se muestra un mensaje amigable al usuario.
         */
        error_log("Error al guardar cotización: " . $e->getMessage());
        echo "<div class='alert alert-danger'> Error en el formulario: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>
