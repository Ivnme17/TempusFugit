<?php
session_start();
require_once '../Servicio/Db.php';
require_once '../Modelo/Reloj.php';
if (!isset($_SESSION['usuario'])) {
    die("Error debe <a href='login.php'>identificarse</a>.<br />");
}

if (!isset($_SESSION['cesta'])) {
    $_SESSION['cesta'] = [];
}

$relojes = Reloj::obtenerTodos();

if (filter_has_var(INPUT_POST, "enviar")) {
    $relojId = filter_input(INPUT_POST, 'producto', FILTER_SANITIZE_NUMBER_INT);
    if ($relojId) {
        $reloj = Reloj::obtenerPorId($relojId);
        if ($reloj && $reloj->disponible()) {
            $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT);
            $cantidad = $cantidad > 0 ? $cantidad : 1;
            
            $_SESSION['cesta'][$relojId] = [
                'nombre' => $reloj->getNombreCompleto(),
                'precio' => $reloj->getPrecio()
            ];
            $_SESSION['cantidad'][$relojId] = $cantidad;
        }
    }
}

if (filter_has_var(INPUT_POST, "comprar")) {
    header('Location: vistaCliente.php');
    exit();
} elseif (filter_has_var(INPUT_POST, "logoff")) {
    header('Location: ../cerrarSesion.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relojes</title>
    <style>
        .producto-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 250px;
            text-align: center;
        }
        
    </style>
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
</head>
<body>
<div class="filtros">
    <form method="get">
        <select name="marca">
            <option value="">Todas las marcas</option>
            <?php
            $marcas = array_unique(array_map(function($reloj) {
                return $reloj->getMarca();
            }, $relojes));
            
            foreach ($marcas as $marca) {
                $seleccionado = (isset($_GET['marca']) && $_GET['marca'] === $marca) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($marca) . "' $seleccionado>" . htmlspecialchars($marca) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Filtrar</button>
    </form>
</div>
<?php
if (isset($_GET['marca']) && $_GET['marca'] !== '') {
    $relojes = Reloj::obtenerPorMarca($_GET['marca']);
}
?>
    <div id='productos'>
        <div class="productos-grid">
            <?php foreach ($relojes as $reloj) { ?>
                <div class="producto-card">
                <div style="background-color: white; width: 250px; height: 250px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <img src="<?php echo htmlspecialchars($reloj->getUrlImagen()); ?>" 
                        alt="<?php echo htmlspecialchars($reloj->getNombreCompleto()); ?>" 
                        style="max-width: 230px; max-height: 230px; object-fit: contain;">
                </div>
                    <h3><?php echo htmlspecialchars($reloj->getNombreCompleto()); ?></h3>
                    <p>Precio: <?php echo htmlspecialchars($reloj->getPrecioFormateado()); ?></p>
                    <form action="" method="post">
                        <input type="hidden" name="producto" value="<?php echo htmlspecialchars($reloj->getId()); ?>">
                        <input type="number" name="cantidad" min="1" value="1" style="width: 50px; margin-bottom: 5px;"><br>
                        <button type="submit" name="enviar" <?php echo $reloj->disponible() ? '' : 'disabled'; ?>>
                            <?php echo $reloj->disponible() ? 'Añadir al carrito' : 'Sin stock'; ?>
                        </button>
                    </form>
                </div>
            <?php } ?>  
        </div>
    </div>
    <form method="post">
        <button type='submit' name='comprar'>Ir al carrito</button>
    </form>
    <footer>
        <form method='post'>
            <div class='desconexion'>
                <input type='submit' name='logoff' value='Cerrar Sesion'>
            </div>
        </form>
    </footer>
</body>
</html>
