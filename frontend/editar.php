<?php
$apiUrl = 'http://localhost:8080/productos';
$id = $_GET['id'] ?? null;
$mensajeDebug = "";
$producto = [
    'nombre' => '', 
    'proveedor' => '', 
    'fecha_vencimiento' => '', 
    'precio' => '', 
    'stock' => ''
];

if (!$id) {
    header("Location: index.php");
    exit();
}

// Obtener la lista para buscar los datos del producto actual a editar
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$responseGet = curl_exec($ch);
curl_close($ch);
$productos = json_decode($responseGet, true) ?? [];

foreach ($productos as $p) {
    if (isset($p['id']) && $p['id'] == $id) {
        $producto = $p;
        break;
    }
}

// Si se envió el formulario para actualizar (PUT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_producto'])) {
    $datosActualizados = [
        "nombre"            => $_POST['nombre'] ?? '',
        "proveedor"         => $_POST['proveedor'] ?? '',
        "fecha_vencimiento" => $_POST['vencimiento'] ?? '',
        "precio"            => (float)($_POST['precio'] ?? 0),
        "stock"             => (int)($_POST['stock'] ?? 0)
    ];

    $payload = json_encode($datosActualizados);

    $ch = curl_init($apiUrl . '/' . $id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        header("Location: index.php");
        exit();
    } else {
        $mensajeDebug = "Error al actualizar el producto (Código HTTP: $httpCode). Respuesta: " . htmlspecialchars($response);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Minimarket</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: bold; }
        input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-primary { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; display: inline-block; font-weight: bold; }
        .btn-secondary:hover { background: #5a6268; }
        .alert-error { background: #ffe6e6; color: #cc0000; padding: 15px; margin-bottom: 20px; border: 1px solid #cc0000; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body>

<div class="container">
    <h2>Editar Producto</h2>

    <?php if (!empty($mensajeDebug)): ?>
        <div class="alert-error"><?= $mensajeDebug ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Proveedor</label>
            <input type="text" name="proveedor" value="<?= htmlspecialchars($producto['proveedor'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Fecha de Vencimiento</label>
            <input type="date" name="vencimiento" value="<?= htmlspecialchars($producto['fecha_vencimiento'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Precio</label>
            <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($producto['precio'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" value="<?= htmlspecialchars($producto['stock'] ?? '') ?>" required>
        </div>

        <button type="submit" name="actualizar_producto" class="btn-primary">Actualizar Producto</button>
        <a href="index.php" class="btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>