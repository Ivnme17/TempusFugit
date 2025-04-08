<?php

/**
 * Sanea y valida el nombre de usuario (login)
 * @param string $login
 * 
 */
function validarLogin($login) {
    $login = stripslashes($login);
    $login = strip_tags($login);
    $login = htmlspecialchars($login);
    return $login;
}

/**
 * Sanea y valida la contraseña
 * @param string $clave
 * 
 */
function validarClave($clave) {
    $clave = stripslashes(strip_tags(htmlspecialchars(trim($clave))));
    return $clave;
}

/**
 * Sanea y valida el nombre y apellidos
 * @param string $nombre
 * 
 */
function validarNombre($nombre) {
    $nombre = stripslashes(strip_tags(htmlspecialchars(trim($nombre))));
    return $nombre;
}

/**
 * Sanea y valida el correo electrónico
 * @param string $correo
 * 
 */
function validarCorreo($correo) {
    $correo = stripslashes(strip_tags(htmlspecialchars(trim($correo))));
    return $correo;
}

/**
 * Sanea y valida el DNI español
 * @param string $dni
 * 
 */
function validarDNI($dni) {
    $dni = stripslashes(strip_tags(htmlspecialchars(trim($dni))));
    return $dni;
}

/**
 * Sanea y valida el Número de la Seguridad Social español
 * @param string $nss
 * 
 */
function validarNSS($nss) {
    $nss = stripslashes(strip_tags(htmlspecialchars(trim($nss))));
    return $nss;
}

/**
 * Sanea y valida el número de teléfono
 * @param string $telefono
 * 
 */
function validarTelefono($telefono) {
    $telefono = stripslashes(strip_tags(htmlspecialchars(trim($telefono))));
    return $telefono;
}

/**
 * Sanitizes and validates address
 * @param string $direccion
 * 
 */
function validarDireccion($direccion) {
    $direccion = stripslashes(strip_tags(htmlspecialchars(trim($direccion))));
    return $direccion;
}

/**
 * Sanitizes and validates Spanish IBAN
 * @param string $iban
 * @return string|false Returns sanitized IBAN if valid, false otherwise
 */
function validarIBAN($iban) {
    $iban = stripslashes(strip_tags(htmlspecialchars(trim($iban))));
    
    $iban = strtoupper(str_replace(' ', '', $iban));
    
    if (!preg_match('/^ES[0-9]{22}$/', $iban)) {
        return false;
    }
    
    return $iban;
}