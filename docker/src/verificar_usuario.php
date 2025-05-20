<?php
header('Content-Type: application/json');
require_once './Modelo/Usuario.php';

try {
    $datos = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($datos['login'])) {
        throw new Exception('No se proporcionó el login');
    }
    
    $login = $datos['login'];
    $usuario = Usuario::verUsuario($login);
    
    echo json_encode([
        'existe' => ($usuario !== null),
        'status' => 'success'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'status' => 'error'
    ]);
}