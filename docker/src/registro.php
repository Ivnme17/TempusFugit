<?php
require_once './Servicio/Db.php';
require_once './Modelo/Usuario.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $loginUsuario = $_POST['loginUsuario'] ?? '';
    $claveUsuario = $_POST['claveUsuario'] ?? '';
    $confirmarClave = $_POST['confirmarClave'] ?? '';
    $direccion = $_POST['direccion'] ?? '';

    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Introduce un correo electrónico válido.";
    }

    if (empty($loginUsuario)) {
        $errores[] = "El nombre de usuario es obligatorio.";
    }

    if (empty($claveUsuario)) {
        $errores[] = "La contraseña es obligatoria.";
    }

    if ($claveUsuario !== $confirmarClave) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (!empty($errores)) {
        session_start();
        $_SESSION['errores'] = $errores;
        header("Location: registro.html");
        exit();
    }

    $usuario = new Usuario();
    $usuario->setLogin($loginUsuario);
    $usuario->setClave($claveUsuario);
    // Se asigna el rol por defecto
    $usuario->setId_rol(4);

    $registroExitoso = $usuario->añadirUsuario();

    if ($registroExitoso) {
        session_start();
        $_SESSION['registro_exitoso'] = true;
        header("Location: login.html");
        exit();
    } else {
        session_start();
        $_SESSION['error_registro'] = "El nombre de usuario ya existe.";
        header("Location: registro.html");
        exit();
    }
} else {
    header("Location: registro.html");
    exit();
}
?>