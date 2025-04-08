<?php
//Registro de Clientes
session_start();
require_once './Modelo/Usuario.php';
require_once './Servicio/Db.php';
require_once './funcionesDeValidacion.php';

$mensaje = "";
$mensajeError = "";
$esSaneado = false;

if (filter_has_var(INPUT_POST, "registrar")) {

    $login = validarLogin(filter_input(INPUT_POST, "loginUsuario"));
    $clave = filter_input(INPUT_POST, "claveUsuario");
    $nombre = validarNombre(filter_input(INPUT_POST, "nombre"));
    $apellidos = validarNombre(filter_input(INPUT_POST, "apellido"));
    $correo = validarCorreo(filter_input(INPUT_POST, "email"));
    $telefono = validarTelefono(filter_input(INPUT_POST, "telefono"));
    $direccion = validarDireccion(filter_input(INPUT_POST, "direccion"));
    $iban = validarIBAN(filter_input(INPUT_POST, "iban"));
    
    $esSaneado = $login && $clave && $nombre && $apellidos && $correo && $telefono && $direccion && $iban;
    //echo $login.", ".$clave.", ".$nombre.", ".$apellidos.", ".$correo.", ".$telefono.", ".$direccion.", ".$iban;
    if ($esSaneado) {

        $usuarioExistente = Usuario::verUsuario($login);
        
        if (!$usuarioExistente) {
            $usuario = new Usuario();
            $usuario->setLogin($login);
            $usuario->setClave($clave);
            $usuario->setNombre($nombre);
            $usuario->setApellidos($apellidos);
            $usuario->setId_rol(3); 
            $usuario->setCorreo($correo);
            $usuario->setTelefono($telefono);
            $usuario->setDireccion($direccion);
            $usuario->setIban($iban);
            
            if ($usuario->añadirUsuario()) {
                $mensaje = "Usuario registrado correctamente";
                header("Location: login.php");
                exit();
            } else {
                $mensajeError = "Error al registrar el usuario";
                include_once './Vista/registroIncorrecto.php';
            }
        } else {
            $mensajeError = "El usuario ya existe";
            include_once './Vista/registroIncorrecto.php';
        }
    } else {
        $mensajeError = "Los campos obligatorios deben estar completos";
        include_once './Vista/registroIncorrecto.php';
    }
} else {
    include_once './Vista/registro.php';
}
