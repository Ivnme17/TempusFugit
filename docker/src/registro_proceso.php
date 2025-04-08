<?php
session_start();
require_once './Modelo/Usuario.php';
require_once './Servicio/Db.php';

$mensaje = "";
$mensajeError = "";

if (filter_has_var(INPUT_POST, "registrar")) {
    $login = filter_input(INPUT_POST, "loginUsuario");
    $clave = filter_input(INPUT_POST, "claveUsuario");
    $nombre = filter_input(INPUT_POST, "nombreUsuario");
    $apellidos = filter_input(INPUT_POST, "apellidosUsuario");
    $correo = filter_input(INPUT_POST, "correoUsuario");
    $dni = filter_input(INPUT_POST, "dniUsuario");
    $nss = filter_input(INPUT_POST, "nssUsuario");
    $telefono = filter_input(INPUT_POST, "telefonoUsuario");
    $direccion = filter_input(INPUT_POST, "direccionUsuario");
    $iban = filter_input(INPUT_POST, "ibanUsuario");
    
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
