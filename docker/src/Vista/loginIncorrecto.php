<?php
$mensajeError = isset($mensajeError) ? $mensajeError : '';
$camposInvalidos = isset($_SESSION['camposInvalidos']) ? $_SESSION['camposInvalidos'] : [];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/estilosFormulario.css">
        <style>
            .campo-invalido {
                border: 2px solid red;
                background-color: #ffe6e6;
            }
        </style>
    </head>
    <body>
        <h1>Formulario de inicio de sesion de usuarios</h1>
        <form action="../login.php" method="post">
                <label>Introduce un login</label><br><br>
                <input type="text" name="loginUsuario" 
                       class="<?php echo isset($camposInvalidos['loginUsuario']) ? 'campo-invalido' : ''; ?>"
                       value=""><br><br>
                <label>Introduce una contraseña</label><br><br>
                <input type="password" name="claveUsuario" 
                       class="<?php echo isset($camposInvalidos['loginUsuario']) ? 'campo-invalido' : ''; ?>"
                       value=""><br><br>
                <button type="submit" name="iniciar">Iniciar Sesión</button>
                <input id="cancelar" type="button" value="Cancelar" onclick="location.href='../index.html'">
        </form>

        <div id="error" style="color: red; font-weight: bold; text-align: center;"> 
            <p><?php echo $mensajeError; ?></p>
        </div>
    </body>
</html>
<?php
unset($_SESSION['camposInvalidos']);
?>
