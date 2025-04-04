<?php
session_start();
require_once './Servicio/Db.php';

function obtenerEmpleados() {
    $conn = Db::getConexion();
    $sql = "SELECT * FROM usuarios";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'editar':
                $conn = Db::getConexion();
                $sql = "UPDATE usuarios SET 
                        telefono = :telefono, 
                        correo = :correo, 
                        direccion = :direccion,  
                        iban = :iban 
                        WHERE id_cliente = :id_cliente";
                
                $stmt = $conn->prepare($sql);
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
