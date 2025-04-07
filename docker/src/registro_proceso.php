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
    
    if (!empty($login) && !empty($clave) && !empty($nombre) && !empty($apellidos) && !empty($email)) {
        try {
            $conexion = Db::getConexion();
            $consulta = "SELECT COUNT(*) FROM usuarios WHERE login = :login OR email = :email";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                $usuario = new Usuario();
                $usuario->setLogin($login);
                $usuario->setClave($clave);
                $usuario->setNombre($nombre);
                $usuario->setApellidos($apellidos);
                $usuario->setId_rol(3);
                
                if ($usuario->añadirUsuario()) {
                    $mensaje = "Usuario registrado correctamente";
                    header("Location: login.html");
                    exit();
                } else {
                    $mensajeError = "Error al registrar el usuario";
                    include_once './Vista/registroIncorrecto.php';
                }
            } else {
                $mensajeError = "El usuario o email ya existe";
                include_once './Vista/registroIncorrecto.php';
            }
        } catch (PDOException $e) {
            $mensajeError = "Error en la base de datos: " . $e->getMessage();
            include_once './Vista/registroIncorrecto.php';
        }
    } else {
        $mensajeError = "Todos los campos son obligatorios";
        include_once './Vista/registroIncorrecto.php';
    }
} else {
    include_once './Vista/registro.php';
}
