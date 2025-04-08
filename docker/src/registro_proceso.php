<?php
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
    $nombre = validarNombre(filter_input(INPUT_POST, "nombreUsuario"));
    $apellidos = validarNombre(filter_input(INPUT_POST, "apellidosUsuario"));
    $correo = validarCorreo(filter_input(INPUT_POST, "correoUsuario"));
    $dni = validarDNI(filter_input(INPUT_POST, "dniUsuario"));
    $nss = validarNSS(filter_input(INPUT_POST, "nssUsuario"));
    $telefono = validarTelefono(filter_input(INPUT_POST, "telefonoUsuario"));
    $direccion = validarDireccion(filter_input(INPUT_POST, "direccionUsuario"));
    $iban = validarIBAN(filter_input(INPUT_POST, "ibanUsuario"));
    
    $esSaneado = $login && $clave && $nombre && $apellidos && $correo && $dni && $nss && $telefono && $direccion && $iban;
    echo $esSaneado ? "Datos saneados correctamente" : "Error en el saneado de datos";
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
