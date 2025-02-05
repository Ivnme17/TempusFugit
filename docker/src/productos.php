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

$conexion = conectar();
$buscarProductos = $conexion->query('SELECT cod, nombre_corto, PVP FROM producto'); // Asegúrate de que 'cod' esté en la consulta

$productos = []; // Inicializa el array de productos

if ($buscarProductos) {
    // Extrae los productos a un array
    while ($registros = $buscarProductos->fetch(PDO::FETCH_ASSOC)) {
        $productos[] = $registros;
    }
}

if (filter_has_var(INPUT_POST, "enviar")) {
    $codProd = filter_input(INPUT_POST, 'producto', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if (is_string($codProd)) {
        $prodSQL = 'SELECT cod, nombre_corto, PVP FROM producto WHERE cod = :codProd'; // Asegúrate de que 'cod' esté en la consulta
        $stmt = $conexion->prepare($prodSQL);
        $stmt->bindValue('codProd', $codProd);
        if ($stmt->execute()) {
            if (($datos = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                $producto['nombre'] = $datos['nombre_corto'];
                $producto['precio'] = $datos['PVP'];
                $_SESSION['cesta'][$codProd] = $producto; // Añade el producto a la cesta
            }
        }
    }
}

if (filter_has_var(INPUT_POST, "comprar")) {
    header('Location: cesta.php');
    exit();//Exit para salir de la página y no siga ejecutandose
} elseif (filter_has_var(INPUT_POST, "logoff")) {
    header('Location: logoff.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
</head>
<body>
    <div id='productos'>
        <ol>
            <?php foreach ($productos as $clave) { ?>
                <li>
                    <?php echo "Nombre: " . $clave['nombre_corto'] . ", precio: " . $clave['PVP']; ?>
                </li>
            <?php } ?>
        </ol>
        <br><br>
        <?php foreach ($productos as $claveP) { ?>
            <form action="" method="post">
                <input type="hidden" name="producto" value="<?php echo $claveP['cod']; ?>"> <!-- Cambiado a 'cod' -->
                <label><?php echo $claveP['nombre_corto']; ?></label>
                <br><br>
                <label><?php echo $claveP['PVP']; ?></label>
                <br><br>
                <button type="submit" name="enviar">Añadir</button> 
            </form>
        <?php } ?>
    </div>
    <form method="post">
        <button type='submit' name='comprar'>Comprar</button>
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