<?php
/**
 * Vista de la Última Cotización Registrada.
 * 
 * Este script obtiene la última cotización almacenada en la base de datos y la muestra en una tabla.
 * 
 * - **Consulta la base de datos** para obtener la cotización más reciente.
 * - **Decodifica los datos JSON** de productos y cantidades.
 * - **Calcula el total de la cotización** con precios predefinidos.
 * - **Muestra la información en una tabla HTML** con formato Bootstrap.
 * 
 * @author  César Augusto Sotelo Zapata
 * @version 1.0
 * @since   2025-03-11
 */

require "cnx.php"; // Conexión a la base de datos

try {
    # ------------------------------
    # OBTENER LA ÚLTIMA COTIZACIÓN
    # ------------------------------

    /**
     * Consulta SQL para obtener la última cotización registrada.
     */
    $stmt = $conexion->prepare("SELECT * FROM cotizaciones ORDER BY fecha DESC LIMIT 1");
    $stmt->execute();
    $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

    /**
     * Si no hay cotizaciones en la base de datos, se muestra un mensaje de advertencia.
     */
    if (!$cotizacion) {
        die("<h2 style='color: red;'>No hay cotizaciones registradas.</h2>");
    }

    # ------------------------------
    # DECODIFICAR DATOS JSON
    # ------------------------------

    /**
     * Decodifica los productos y cantidades almacenados en formato JSON en la base de datos.
     */
    $productos = json_decode($cotizacion["productos"], true);
    $cantidades = json_decode($cotizacion["cantidades"], true);

    # ------------------------------
    # DEFINIR LISTA DE PRECIOS UNITARIOS
    # ------------------------------

    /**
     * Lista de precios predefinidos para cada producto.
     */
    $precios = [
        "Laptop Dell" => 800000,
        "Monitor Samsung" => 1200000,
        "Teclado Mecánico" => 250000,
        "Mouse Gamer" => 70000,
        "Impresora HP" => 400000,
        "Disco SSD 1TB" => 100000,
        "Memoria RAM 16GB" => 385000,
        "Tarjeta Gráfica RTX 3060" => 6000000,
        "Audífonos Inalámbricos" => 300000,
        "Silla Gamer" => 700000
    ];
} catch (PDOException $e) {
    die("<h2 style='color: red;'> Error al obtener datos: " . $e->getMessage() . "</h2>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Cotización</title>

    <!-- Estilos con Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <!-- Título Principal -->
    <h2 class="text-center">Resumen de Cotización</h2>
    
    <!-- Tabla con los datos del cliente -->
    <table class="table table-bordered mt-3">
        <tr>
            <th>Nombre</th>
            <td><?= htmlspecialchars($cotizacion["nombre"]); ?></td>
        </tr>
        <tr>
            <th>Ciudad</th>
            <td><?= htmlspecialchars($cotizacion["ciudad"]); ?></td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td><?= htmlspecialchars($cotizacion["direccion"]); ?></td>
        </tr>
        <tr>
            <th>Celular</th>
            <td><?= htmlspecialchars($cotizacion["celular"]); ?></td>
        </tr>
    </table>

    <!-- Sección de Productos Cotizados -->
    <h4 class="mt-4">Productos Cotizados</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $granTotal = 0;
            foreach ($productos as $index => $producto): 
                $cantidad = intval($cantidades[$index]); // Asegurar que la cantidad es numérica
                $precioUnitario = $precios[$producto] ?? 0; // Obtener precio o 0 si no existe en la lista
                $total = $cantidad * $precioUnitario;
                $granTotal += $total;
            ?>
            <tr>
                <td><?= htmlspecialchars($producto); ?></td>
                <td><?= $cantidad; ?></td>
                <td>$<?= number_format($precioUnitario, 2); ?></td>
                <td>$<?= number_format($total, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end"> Total General:</th>
                <th>$<?= number_format($granTotal, 2); ?></th>
            </tr>
        </tfoot>
    </table>

    <!-- Botón para volver a la cotización -->
    <div class="mt-3">
        <a href="cotizacion.php" class="btn btn-primary"> Nueva Cotización</a>
    </div>

</body>
</html>
