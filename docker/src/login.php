<?php
session_start();
require_once './Modelo/Usuario.php';
require_once './Servicio/Db.php';
$usuarios = [];
$conexion = Db::getConexion();
$consulta = "SELECT * FROM clientes";
$resultado = $conexion->query($consulta);

if ($resultado) {
    while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
        $usuarios[] = $fila;
    }
}
$mensaje = "";
$mensajeError = "";

if (filter_has_var(INPUT_POST, "iniciar")) {
    $login = filter_input(INPUT_POST, "loginUsuario");
    $clave = filter_input(INPUT_POST, "claveUsuario");
    
    $usuario = Usuario::autenticarUsuario($login, $clave);
    
    if ($usuario) {
        $_SESSION["usuario"] = $usuario->getLogin(); 
        $_SESSION["rol"] = $usuario->getId_rol();
        
        switch ($_SESSION['rol']) {
            case "1": case "2": // Empleado Y Administrador
                include_once './Vista/vistaEmpleado.php';
                break;
                
            case "3":case "4": // Cliente Y Por Defecto
                include_once './Vista/vistaCliente.html';
                break;
                
            default:
                $mensajeError .= "ERROR: Rol no reconocido.";
                include_once './Vista/loginIncorrecto.php';
        }
    } else {
        $mensajeError .= "El usuario no existe o las credenciales son incorrectas.";
        include_once './Vista/loginIncorrecto.php';
    }
} else {
    include_once '../Vista/loginIncorrecto.php';
    $mensajeError .= "ERROR: No se ha podido iniciar sesión.";
}