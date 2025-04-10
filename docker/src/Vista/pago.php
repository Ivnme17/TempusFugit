<?php
session_start();
if(isset($_POST['submit'])) {
    $mensaje="Pago realizado con éxito";
    include_once("../Vista/alerta.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bizum</title>
    <link rel="stylesheet" href="../css/estilospago.css">
</head>
<body>    
    <form method="" action="">
    <img src="../imagenBackground/logo-vector-bizum.jpg" alt="Bizum" class="logo" style="width: 390px; height:auto;">
        <h1>Pago</h1>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" required>
        </div>
        <button type="submit" class="btn btn-primary">Realizar Pago</button>
        <button type="reset" class="btn btn-secondary">Limpiar</button>
    </form>
</body>
</html>



