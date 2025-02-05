<?php
session_start();
require_once './funciones.inc.php';

if (!isset($_SESSION['usuario'])) {
    die("Error debe <a href='login.php'>identificarse</a>.<br />");
}

// Inicializa la cesta si no existe
if (!isset($_SESSION['cesta'])) {
    $_SESSION['cesta'] = []; // Inicializa la cesta como un array vacío
}

// Si la cesta está vacía, mostramos un mensaje
$cesta_vacia = true;
if (count($_SESSION['cesta']) == 0) {
    print "<p>Cesta vacía</p>";
} else {
    // Si no está vacía, mostrar su contenido
    foreach ($_SESSION['cesta'] as $codigo => $producto) {
        print "<p>$codigo: " . $producto['nombre'] . " - Precio: " . $producto['precio'] . "</p>";
        $cesta_vacia = false;
    }
}

// Comprobamos si se ha enviado el formulario de vaciar la cesta
if (filter_has_var(INPUT_POST, 'vaciar')) {
    unset($_SESSION['cesta']);
}

if (filter_has_var(INPUT_POST, "pagar")) {
    header('Location: pagar.php');
} elseif (filter_has_var(INPUT_POST, "logoff")) {
    header('Location: logoff.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cesta</title>
</head>
<body>
    <ul>
        <?php
        $importeTotal = 0; // Inicializa el total
        foreach ($_SESSION['cesta'] as $codigo => $producto) {
            echo "<li>";
            echo "Nombre: " . $producto['nombre'] . ", Precio: " . $producto['precio'];
            $importeTotal += $producto['precio']; // Suma los precios de los productos seleccionados
            echo "</li>";
        }
        ?>
    </ul>

    <p>IMPORTE TOTAL: <?php echo $importeTotal; ?></p>
    <br><br>
    <form method='post' action=''>
        <div class='desconexion'><!-- Creamos botón logoff -->
            <input type='submit' name='pagar' value='Pagar'>
        </div>
    </form>
</body>
<footer>
    <form method='post' action=''>
        <div class='desconexion'><!-- Creamos botón logoff -->
            <input type='submit' name='logoff' value='Cerrar Sesion'>
        </div>
    </form>
</footer>
</html>