<?php
// Recuperamos la información de la sesión
session_start();
// Y comprobamos que el usuario se haya autentificado
if (!isset($_SESSION['usuario'])) {
    die("Error debe <a href='login.php'>identificarse</a>.<br />");
} else{
    session_unset();
    header('Location: login.php');
}
