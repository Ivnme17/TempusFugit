<?php
header('Content-Type: application/json');
require_once './Modelo/Usuario.php';

try {
    // Verificar que la petición sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    $datos = json_decode(file_get_contents('php://input'), true);
    
    // Verificar que se recibieron datos válidos
    if ($datos === null) {
        throw new Exception('Datos JSON inválidos');
    }
    
    if (!isset($datos['login']) || empty(trim($datos['login']))) {
        throw new Exception('No se proporcionó un login válido');
    }
    
    $login = trim($datos['login']);
    
    // Agregar logging para debug
    error_log("Verificando usuario: " . $login);
    
    $usuario = Usuario::verUsuario($login);
    
    // Verificar explícitamente el resultado
    $existe = ($usuario !== null && $usuario !== false);
    
    // Log del resultado
    error_log("Resultado para '$login': " . ($existe ? 'EXISTE' : 'NO EXISTE'));
    
    echo json_encode([
        'existe' => $existe,
        'status' => 'success',
        'login_verificado' => $login // Para debug
    ]);
    
} catch (Exception $e) {
    error_log("Error en verificar_usuario.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'status' => 'error'
    ]);
}