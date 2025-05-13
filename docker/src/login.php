<?php
session_start();
require_once './Modelo/Usuario.php';

$mensaje = "";
$mensajeError = "";

if (filter_has_var(INPUT_POST, "iniciar")) {
    $login = filter_input(INPUT_POST, "loginUsuario");
    $clave = filter_input(INPUT_POST, "claveUsuario");
    
    $usuario = Usuario::autenticarUsuario($login, $clave);
    
    if (!empty($usuario)) {
        $_SESSION["usuario"] = $usuario->getLogin(); 
        $_SESSION["rol"] = $usuario->getId_rol();
        
        switch ($_SESSION['rol']) {
            case "1":
                header("Location: ./Vista/backOffice.php");
                break;
            case "2": // Empleado Y Administrador
                header("Location: ./Vista/vistaEmpleado.php");
                break;
                
            case "3":case "4": // Cliente Y Por Defecto
                header("Location: ./Vista/vistaCliente.php");
                break;
                
            default:
                $mensajeError .= "ERROR: Rol no reconocido.";
                include_once './Vista/loginIncorrecto.php';
        }
    } else {
        $_SESSION['camposInvalidos']['loginUsuario'] = true;
        $mensajeError .= "El usuario no existe o las credenciales son incorrectas.";
        include_once './Vista/loginIncorrecto.php';
    }
} else {
    include_once 'Vista/loginIncorrecto.php';
    $mensajeError .= "ERROR: No se ha podido iniciar sesión.";
}