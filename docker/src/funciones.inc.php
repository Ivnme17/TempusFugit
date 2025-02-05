<?php
define('DB_USER', 'root');
define('DB_PASSWD', '');
define('DB_NAME', 'dwes');
define('DB_HOST', 'localhost');
function conectar()
{
$db_host = DB_HOST; //hostname

$db_name = DB_NAME; //databasename
$db_user = DB_USER; //username

$user_pw = DB_PASSWD; // password
try {
$con = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $user_pw);
//$conexionPDO = new PDO('mysql:host=localhost;dbname=espectaculos', 'root', '',$opciones);

$con->setAttribute (PDO::ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
$con->exec("set names utf8");
} catch (PDOException $e) { //Se capturan los mensajes de error
    die("Error: " . $e->getMessage());
}
return $con;
}