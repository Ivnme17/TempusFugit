<!-- fichero que si se encuentra una session activa la elimina y redirige al usuario a la página principal    !>
<?php
session_start();
if (isset($_SESSION["usuario"]) && isset($_SESSION["rol"])){
    session_destroy(); 
    header("Location: index.php"); 
    exit(); 
} else {
    echo "No hay sesión activa.";
}