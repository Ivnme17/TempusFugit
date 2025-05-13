<?php
//Registro de Clientes
session_start();
require_once './Modelo/Usuario.php';
require_once './Servicio/Db.php';
require_once './funcionesDeValidacion.php';

$mensaje = "";
$mensajeError = "";
$esSaneado = true;
$camposInvalidos = [];

if (filter_has_var(INPUT_POST, "registrar")) {

    $login = validarLogin(filter_input(INPUT_POST, "loginUsuario"));
    if (!$login) {
        $esSaneado = false;
        $camposInvalidos['loginUsuario'] = true;
    }

    $clave = filter_input(INPUT_POST, "claveUsuario");
    if (!$clave || strlen($clave) < 8) {
        $esSaneado = false;
        $camposInvalidos['claveUsuario'] = true;
    }

    $nombre = validarNombre(filter_input(INPUT_POST, "nombre"));
    if (!$nombre) {
        $esSaneado = false;
        $camposInvalidos['nombre'] = true;
    }

    // Similar validation for other fields
    $apellidos = validarNombre(filter_input(INPUT_POST, "apellido"));
    if (!$apellidos) {
        $esSaneado = false;
        $camposInvalidos['apellido'] = true;
    }

    $correo = validarCorreo(filter_input(INPUT_POST, "email"));
    if (!$correo) {
        $esSaneado = false;
        $camposInvalidos['email'] = true;
    }

    $telefono = validarTelefono(filter_input(INPUT_POST, "telefono"));
    if (!$telefono) {
        $esSaneado = false;
        $camposInvalidos['telefono'] = true;
    }

    $direccion = validarDireccion(filter_input(INPUT_POST, "direccion"));
    if (!$direccion) {
        $esSaneado = false;
        $camposInvalidos['direccion'] = true;
    }

    $iban = validarIBAN(filter_input(INPUT_POST, "iban"));
    if (!$iban) {
        $esSaneado = false;
        $camposInvalidos['iban'] = true;
    }

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
                $_SESSION['camposInvalidos'] = $camposInvalidos;
                include_once './Vista/registroIncorrecto.php';
            }
        } else {
            $mensajeError = "El usuario ya existe";
            $_SESSION['camposInvalidos'] = $camposInvalidos;
            include_once './Vista/registroIncorrecto.php';
        }
    } else {
        $mensajeError = "Los campos obligatorios deben estar completos";
        $_SESSION['camposInvalidos'] = $camposInvalidos;
        include_once './Vista/registroIncorrecto.php';
    }
} else {
    include_once './Vista/registro.php';
}
