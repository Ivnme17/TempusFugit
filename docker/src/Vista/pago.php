<?php
session_start();

 if (!isset($_SESSION['cliente'])) {
    header("Location: ../Vista/login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="" action="">
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



