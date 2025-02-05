<?php


function conectarDB(){
    $conexionPDO = null;
    try {
        $conexionPDO = new mysqli();
        $conexionPDO->connect("localhost", "root", "", "espectaculos");
        
    } catch (Exception $ex) {
        die("ERROR: " . $ex->getMessage()); //Si da error al conectar, cierro la conexión
    }
    
    return $conexionPDO;
}