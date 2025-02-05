 <!DOCTYPE html>
<?php
require_once './funciones.inc.php';
$error = "";

// Comprobamos si ya se ha enviado el formulario
if (filter_has_var(INPUT_POST, "enviar")) {
    // Obtenemos los datos enviados por POST y los volcamos a variables
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password');

    // Si el usuario o la contraseña están vacíos, mandamos un mensaje
    if (empty($usuario) || empty($password)) {
        $error = "Debes introducir un nombre de usuario y una contraseña";
    } else {
        // Conectamos a la base de datos para comenzar las comprobaciones del usuario 
        $con = conectar();
        // Creamos la consulta parametrizada para comprobar el usuario y la contraseña
        $sql = "SELECT usuario, contrasena FROM usuarios WHERE usuario = :login"; 
        try {
            // Preparamos la consulta
            $resultado = $con->prepare($sql);
            // Parámetros de la consulta
            $resultado->bindValue(":login", $usuario);
            // Ejecutamos la consulta
            if ($resultado->execute()) {
                // Volcamos los resultados en un array 
                $fila = $resultado->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Error al ejecutar la consulta.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos al ejecutar la consulta. " . $e->getMessage();
        }

        // Si no hay errores, verificamos si el usuario existe
        if (!isset($error)) {
            if ($fila !== false) {
                // Ciframos la contraseña ingresada por el usuario
                $passwordCifrada = hash('sha256', $password); 

                // Comparamos la contraseña cifrada con la almacenada en la base de datos cifrada
                if ($passwordCifrada === hash('sha256',$fila['contrasena'])) {
                    // Iniciamos la sesión
                    session_start();
                    // Creamos la variable de usuario con el nombre del usuario
                    $_SESSION['usuario'] = $fila['usuario']; 
                    // Redireccionamos a la página que nos interesa
                    header("Location: productos.php");
                    exit; // Asegúrate de salir después de redirigir
                } else {
                    // Si las credenciales no son válidas, se vuelven a pedir
                    $error = "Usuario o contraseña no válidos!";
                }
            } else {
                // Si el usuario no existe
                $error = "Usuario o contraseña no válidos!";
            }
        }
    }
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <div id='login'>
        <form action='' method='post'>
            <fieldset>
                <legend>Login</legend>
                <div><span class='error'><?php if(isset($error)) echo $error; ?></span></div><!-- Espacio para mensajes de error -->
                <div class='campo'>
                    <label for='usuario'>Usuario: </label><br/>
                    <input type='text' name='usuario' id='usuario' maxlength="50" /><br/>
                </div>
                <div class='campo'>
                    <label for='password'>Contraseña:</label><br/>
                    <input type='password' name='password' id='password' maxlength="50" /><br/>
                </div>
                <div class='campo'>
                    <input type='submit' name='enviar' value='Enviar' />
                </div>
            </fieldset>
        </form>
    </div>
</body>
</html>