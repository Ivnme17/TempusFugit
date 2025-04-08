<?php
session_start();
require_once './funciones.inc.php';
require_once './Db.php';
require_once './Reloj.php';

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
            $_SESSION['cesta'][$relojId] = [
                'nombre' => $reloj->getNombreCompleto(),
                'precio' => $reloj->getPrecio()
            ];
        }
    }
}

if (filter_has_var(INPUT_POST, "comprar")) {
    header('Location: vistaCliente.php');
    exit();
} elseif (filter_has_var(INPUT_POST, "logoff")) {
    header('Location: cerrarSesion.php');
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
            width: 200px;
            text-align: center;
        }
        .producto-imagen {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div id='productos'>
        <div class="productos-grid">
            <?php foreach ($relojes as $reloj) { ?>
                <div class="producto-card">
                    <img src="<?php echo htmlspecialchars($reloj->getUrlImagen()); ?>" 
                         alt="<?php echo htmlspecialchars($reloj->getNombreCompleto()); ?>" 
                         class="producto-imagen">
                    <h3><?php echo htmlspecialchars($reloj->getNombreCompleto()); ?></h3>
                    <p>Precio: <?php echo htmlspecialchars($reloj->getPrecioFormateado()); ?></p>
                    <form action="" method="post">
                        <input type="hidden" name="producto" value="<?php echo htmlspecialchars($reloj->getId()); ?>">
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
