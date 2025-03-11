<?php
/**
 * Formulario de Cotización con Seguridad CSRF.
 * 
 * Este script genera un formulario seguro para que los usuarios puedan solicitar cotizaciones de productos.
 * - Protege contra ataques CSRF generando un token de seguridad.
 * - Utiliza Bootstrap para una mejor presentación visual.
 * - Permite la selección de productos y sus cantidades dinámicamente.
 * 
 * @author  César Augusto Sotelo Zapata
 * @version 1.0
 * @since   2025-03-11
 */

# ------------------------------
# INICIAR SESIÓN Y PROTEGER CSRF
# ------------------------------

session_start(); 
require "cnx.php"; 

/**
 * Genera un token CSRF si no existe en la sesión para prevenir ataques.
 */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Cotización</title>

    <!-- Bootstrap para mejorar el diseño -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <script>
        /**
         * Habilita o deshabilita el campo de cantidad basado en si el checkbox está seleccionado.
         * @param {HTMLInputElement} checkbox - El checkbox del producto seleccionado.
         */
        function toggleCantidad(checkbox) {
            let cantidadInput = document.getElementById("cantidad_" + checkbox.value);
            cantidadInput.disabled = !checkbox.checked;
            if (!checkbox.checked) cantidadInput.value = '';
        }
    </script>
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h2 class="mb-0">Formulario de Cotización</h2>
            </div>
            <div class="card-body">
                
                <!-- Formulario seguro con Token CSRF -->
                <form action="formulario_procesar.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                    <!-- Campo: Nombres y Apellidos -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombres y Apellidos:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <!-- Campo: Ciudad (Desplegable) -->
                    <div class="mb-3">
                        <label for="ciudad" class="form-label">Ciudad:</label>
                        <select class="form-select" id="ciudad" name="ciudad" required>
                            <option value="">Seleccione una ciudad</option>
                            <option value="Bogotá">Bogotá</option>
                            <option value="Medellín">Medellín</option>
                            <option value="Cali">Cali</option>
                            <option value="Barranquilla">Barranquilla</option>
                        </select>
                    </div>

                    <!-- Campo: Dirección -->
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required>
                    </div>

                    <!-- Campo: Celular (Validado con 10 dígitos) -->
                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular:</label>
                        <input type="tel" class="form-control" id="celular" name="celular" maxlength="10" minlength="10" pattern="\d{10}" title="Debe ser un número de 10 dígitos" required>
                    </div>

                    <h4 class="text-primary mt-4">Selecciona los productos y sus cantidades:</h4>

                    <div class="row">
                        <?php
                        # ------------------------------
                        #  LISTA DE PRODUCTOS DISPONIBLES
                        # ------------------------------

                        /**
                         * Lista de productos disponibles en la tienda.
                         * Se genera dinámicamente con PHP para facilitar actualizaciones.
                         */
                        $productos = [
                            "Laptop Dell", "Monitor Samsung", "Teclado Mecánico", "Mouse Gamer", "Impresora HP",
                            "Disco SSD 1TB", "Memoria RAM 16GB", "Tarjeta Gráfica RTX 3060", "Audífonos Inalámbricos", "Silla Gamer"
                        ];

                        # Generar los checkboxes con inputs dinámicos
                        foreach ($productos as $index => $producto) {
                            echo "<div class='col-md-6 mb-3'>
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' id='producto_$index' name='productos[]' value='$producto' onclick='toggleCantidad(this)'>
                                        <label class='form-check-label' for='producto_$index'>$producto</label>
                                    </div>
                                    <input type='number' class='form-control mt-1' id='cantidad_$producto' name='cantidad[]' min='1' placeholder='Cantidad' disabled>
                                  </div>";
                        }
                        ?>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <!-- Botón Enviar -->
                        <button type="submit" class="btn btn-success">Enviar cotización</button>

                        <!-- Botón Cancelar -->
                        <a href="index.html" class="btn btn-danger">Cancelar</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>
</html>
