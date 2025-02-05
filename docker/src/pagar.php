<?php
// Recuperamos la información de la sesión
session_start();
// Y comprobamos que el usuario se haya autentificado
if (!isset($_SESSION['usuario'])) {
    die("Error debe <a href='login.php'>identificarse</a>.<br />");
}

?>
<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
<?php
$total = 0;
foreach ($_SESSION['cesta'] as $codigo => $producto) {
echo "<p><span class='codigo '>$codigo</span>";
echo "<span class='nombre'>{$producto ['nombre']}</span>";
echo "<span class='precio'>{$producto ['precio']}</span></p>"; $total += $producto['precio'];
}
?>
<hr />
<p><span class='pagar'>Precio total: <?php print $total; ?> </span></p>
    </body>
</html>
