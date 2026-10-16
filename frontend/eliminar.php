<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $apiUrl = "http://localhost:8080/productos/{$id}";

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    
    curl_exec($ch);
    curl_close($ch);
}

// Redirigir de vuelta al listado principal
header("Location: index.php");
exit();
?>