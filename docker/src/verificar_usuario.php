<?php
header('Content-Type: application/json');
require_once './Modelo/Usuario.php';  // Esta ruta está correcta porque está al mismo nivel

$datos = json_decode(file_get_contents('php://input'), true);
$login = $datos['login'] ?? '';

$respuesta = ['existe' => false];

if ($login) {
    $usuario = Usuario::verUsuario($login);
    $respuesta['existe'] = ($usuario !== null);
}

echo json_encode($respuesta);