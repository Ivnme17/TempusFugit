<?php
session_start();
require_once './Modelo/Usuario.php';
require_once './Servicio/Db.php';

$mensaje = "";
$mensajeError = "";

if (filter_has_var(INPUT_POST, "iniciar")) {
    $login = filter_input(INPUT_POST, "loginUsuario", FILTER_SANITIZE_STRING);
    $clave = filter_input(INPUT_POST, "claveUsuario", FILTER_SANITIZE_STRING);
    
    $usuario = Usuario::autenticarUsuario($login, $clave);
    
    if ($usuario) {
        $_SESSION["usuario"] = $usuario->getLogin(); 
        $_SESSION["rol"] = $usuario->getId_rol();
        
        switch ($_SESSION['rol']) {
            case "1": case "2": // Empleado Y Administrador
                include_once './Vista/vista-empleado.html';
                break;
                
            case "3": // Cliente
                include_once './Vista/vista-cliente.html';
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
    include_once './Vista/login.html';
}