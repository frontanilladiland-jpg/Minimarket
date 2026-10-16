<?php
$apiUrl = 'http://localhost:8080/productos';
$mensajeDebug = "";

// 1. Si se envió el formulario para guardar un producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_producto'])) {
    $datosProducto = [
        "nombre"            => $_POST['nombre'] ?? '',
        "proveedor"         => $_POST['proveedor'] ?? '',
        "fecha_vencimiento" => $_POST['vencimiento'] ?? '',
        "precio"            => (float)($_POST['precio'] ?? 0),
        "stock"             => (int)($_POST['stock'] ?? 0)
    ];

    $payload = json_encode($datosProducto);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode !== 200 && $httpCode !== 201) {
        $mensajeDebug = "Error al conectar con Quarkus (Código HTTP: $httpCode). ";
        if ($curlError) {
            $mensajeDebug .= "cURL Error: $curlError. ";
        }
        $mensajeDebug .= "Respuesta de la API: " . htmlspecialchars($response);
    } else {
        header("Location: index.php");
        exit();
    }
}

// 2. Obtener la lista de productos desde Quarkus (GET)
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$responseGet = curl_exec($ch);
curl_close($ch);

$productos = json_decode($responseGet, true) ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos - Minimarket</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 1100px; margin: auto; background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; margin-top: 0; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 25px; overflow: hidden; }
        .card-header { background: #007bff; color: white; padding: 10px 15px; font-weight: bold; }
        .card-header.dark { background: #343a40; }
        .card-body { padding: 15px; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 150px; }
        label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: bold; }
        input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-success { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #212529; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; }
        .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; }
        .btn-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #f8f9fa; }
        .text-center { text-align: center; color: #6c75d9; padding: 15px; }
        .alert-error { background: #ffe6e6; color: #cc0000; padding: 15px; margin-bottom: 20px; border: 1px solid #cc0000; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body>

<div class="container">
    <h2>Gestión de Productos - Minimarket</h2>

    <!-- Alerta de depuración si falla la petición -->
    <?php if (!empty($mensajeDebug)): ?>
        <div class="alert-error">
            <strong>Aviso de Depuración:</strong><br>
            <?= $mensajeDebug ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de Registro -->
    <div class="card">
        <div class="card-header">Registrar Nuevo Producto</div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" required placeholder="Ej. Arroz">
                    </div>
                    <div class="form-group">
                        <label>Proveedor</label>
                        <input type="text" name="proveedor" placeholder="Ej. Imba">
                    </div>
                    <div class="form-group">
                        <label>Vencimiento</label>
                        <input type="date" name="vencimiento">
                    </div>
                    <div class="form-group">
                        <label>Precio</label>
                        <input type="number" step="0.01" name="precio" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" required placeholder="0">
                    </div>
                </div>
                <button type="submit" name="guardar_producto" class="btn-success">Guardar Producto</button>
            </form>
        </div>
    </div>

    <!-- Listado de Productos -->
    <div class="card">
        <div class="card-header dark">Listado de Productos Activos</div>
        <div class="card-body" style="padding: 0;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Proveedor</th>
                        <th>Vencimiento</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['id'] ?? '') ?></td>
                                <?= /* Corregido para mostrar la fecha de vencimiento que devuelve Quarkus */ '' ?>
                                <td><?= htmlspecialchars($p['nombre'] ?? '') ?></td>
                                <td><?= htmlspecialchars($p['proveedor'] ?? '-') ?></td>
                                <td><?= !empty($p['fecha_vencimiento']) ? htmlspecialchars($p['fecha_vencimiento']) : '-' ?></td>
                                <td>$us <?= number_format($p['precio'] ?? 0, 2) ?></td>
                                <?= /* Corregido para mapear correctamente el stock */ '' ?>
                                <td><?= htmlspecialchars($p['stock'] ?? 0) ?></td>
                                <td>
                                    <!-- Botones de Editar y Eliminar Lógico requeridos -->
                                    <a href="editar.php?id=<?= $p['id'] ?>" class="btn-warning">Editar</a>
                                    <a href="eliminar.php?id=<?= $p['id'] ?>" class="btn-danger" onclick="return confirm('¿Estás seguro de realizar la eliminación lógica de este producto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay productos activos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>