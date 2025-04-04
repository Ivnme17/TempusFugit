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
    $email = filter_input(INPUT_POST, "emailUsuario", FILTER_SANITIZE_EMAIL);
    
    if (!empty($login) && !empty($clave) && !empty($nombre) && !empty($apellidos) && !empty($email)) {
        try {
            $conexion = Db::getConexion();
            // Verificar si el usuario ya existe
            $consulta = "SELECT COUNT(*) FROM usuarios WHERE login = :login OR email = :email";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->fetchColumn() == 0) {
                // Hash de la contraseña
                $claveHash = password_hash($clave, PASSWORD_DEFAULT);
                
                // Insertar nuevo usuario
                $consulta = "INSERT INTO usuarios (login, password, nombre, apellidos, email, id_rol) 
                            VALUES (:login, :password, :nombre, :apellidos, :email, 3)";
                $stmt = $conexion->prepare($consulta);
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':password', $claveHash);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':email', $email);
                
                if ($stmt->execute()) {
                    $mensaje = "Usuario registrado correctamente";
                    header("Location: login.html");
                    exit();
                } else {
                    $mensajeError = "Error al registrar el usuario";
                    include_once './Vista/registro.php';
                }
            } else {
                $mensajeError = "El usuario o email ya existe";
                include_once './Vista/registro.php';
            }
        } catch (PDOException $e) {
            $mensajeError = "Error en la base de datos: " . $e->getMessage();
            include_once './Vista/registro.php';
        }
    } else {
        $mensajeError = "Todos los campos son obligatorios";
        include_once './Vista/registro.php';
    }
} else {
    include_once './Vista/registro.php';
}
