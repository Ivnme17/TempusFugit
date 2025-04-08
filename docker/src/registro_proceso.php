<?php
session_start();
require_once './Modelo/Usuario.php';
require_once './Servicio/Db.php';

$mensaje = "";
$mensajeError = "";

if (filter_has_var(INPUT_POST, "registrar")) {
    $login = filter_input(INPUT_POST, "loginUsuario", FILTER_SANITIZE_STRING);
    $clave = filter_input(INPUT_POST, "claveUsuario", FILTER_SANITIZE_STRING);
    $nombre = filter_input(INPUT_POST, "nombreUsuario", FILTER_SANITIZE_STRING);
    $apellidos = filter_input(INPUT_POST, "apellidosUsuario", FILTER_SANITIZE_STRING);
    $correo = filter_input(INPUT_POST, "correoUsuario", FILTER_SANITIZE_EMAIL);
    $dni = filter_input(INPUT_POST, "dniUsuario", FILTER_SANITIZE_STRING);
    $nss = filter_input(INPUT_POST, "nssUsuario", FILTER_SANITIZE_STRING);
    $telefono = filter_input(INPUT_POST, "telefonoUsuario", FILTER_SANITIZE_STRING);
    $direccion = filter_input(INPUT_POST, "direccionUsuario", FILTER_SANITIZE_STRING);
    $iban = filter_input(INPUT_POST, "ibanUsuario", FILTER_SANITIZE_STRING);
    
    if (!empty($login) && !empty($clave) && !empty($nombre) && !empty($apellidos)) {

        $usuarioExistente = Usuario::verUsuario($login);
        
        if (!$usuarioExistente) {
            $usuario = new Usuario();
            $usuario->setLogin($login);
            $usuario->setClave($clave);
            $usuario->setNombre($nombre);
            $usuario->setApellidos($apellidos);
            $usuario->setId_rol(3); 
            $usuario->setCorreo($correo);
            $usuario->setDni($dni);
            $usuario->setNss($nss);
            $usuario->setTelefono($telefono);
            $usuario->setDireccion($direccion);
            $usuario->setIban($iban);
            
            if ($usuario->añadirUsuario()) {
                $mensaje = "Usuario registrado correctamente";
                header("Location: login.html");
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
