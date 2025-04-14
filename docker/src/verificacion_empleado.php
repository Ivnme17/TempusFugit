<?php
session_start();
require_once './Servicio/Db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Verificar si ya es empleado o administrador
if ($_SESSION['rol'] == '1' || $_SESSION['rol'] == '2') {
    header('Location: vista-empleado.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Empleado - Tempus Fugit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body { background-color: #f4f4f4; }
        .verificacion-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: rgba(255, 215, 0, 0.1);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-custom {
            background-color: #FFD700;
            color: #4E3B31;
        }
        .btn-custom:hover {
            background-color: #FFC300;
        }
    </style>
</head>
<body>
    <div class="verificacion-container">
    <div id="logoEmpresa">
            <a href="index.html"><img src="../logoEmpresa/TEMPUS-removebg-preview.png" width="200" height="auto"></a>
        </div>

        <?php
        // Mostrar mensajes de error o éxito
        if (isset($_SESSION['mensaje_error'])) {
            echo '<div class="alert alert-danger">' . 
                 htmlspecialchars($_SESSION['mensaje_error']) . 
                 '</div>';
            unset($_SESSION['mensaje_error']);
        }

        if (isset($_SESSION['mensaje_exito'])) {
            echo '<div class="alert alert-success">' . 
                 htmlspecialchars($_SESSION['mensaje_exito']) . 
                 '</div>';
            unset($_SESSION['mensaje_exito']);
        }
        ?>

        <form action="../verificacion_empleado.php" method="POST">
            <h2 class="text-center mb-4">Verificación de Empleado</h2>
           
            <div class="mb-3">
                <label for="nss" class="form-label">Número de Seguridad Social (NSS):</label>
                <input type="text" class="form-control" id="nss" name="nss" required placeholder="Introduce tu NSS">
                <div class="form-text" style="color:#FFD700">Tu NSS es necesario para verificar tu condición de empleado.</div>
            </div>
           
            <div class="mb-3">
                <label for="loginUsuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" class="form-control" id="loginUsuario" name="loginUsuario" required placeholder="Introduce tu nombre de usuario" value="<?php echo htmlspecialchars($_SESSION['usuario']); ?>" readonly>
            </div>
           
            <div class="d-grid">
                <button type="submit" class="btn btn-custom">Verificar</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>