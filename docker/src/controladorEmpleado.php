<?php
session_start();
require_once './Servicio/Db.php';

function obtenerEmpleados() {
    $conexion = Db::getConexion();
    $consulta = "SELECT * FROM usuarios";
    $stmt = $conexion->query($consulta);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'editar':
                $conexion = Db::getConexion();
                $consulta = "UPDATE usuarios SET 
                        telefono = :telefono, 
                        correo = :correo, 
                        direccion = :direccion,  
                        iban = :iban 
                        WHERE id_cliente = :id_cliente";
                
                $stmt = $conexion->prepare($consulta);
                $stmt->execute([
                    ':telefono' => $_POST['telefono'],
                    ':correo' => $_POST['correo'],
                    ':direccion' => $_POST['direccion'],
                    ':iban' => $_POST['iban'],
                    ':id_cliente' => $_POST['id_cliente']
                ]);
                break;
        }
    }
}

$usuarios = obtenerEmpleados();
Db::cerrarConexion();
