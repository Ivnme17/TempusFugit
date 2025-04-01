<?php
//Dividir Controlador y Vista
require_once './Servicio/Db.php';
require_once './Modelo/Reloj.php';

if (isset($_GET['marca'])) {
    $relojes = Reloj::obtenerPorMarca($_GET['marca']);
} else {
    $relojes = Reloj::obtenerTodos();
}

// Relojes por marca
$relojesPorMarca = [];
foreach ($relojes as $reloj) {
    $relojesPorMarca[$reloj->getMarca()][] = $reloj;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Relojes - Tempus Fugit</title>
    <link rel="stylesheet" href="estilosVistaProductos.css">
</head>
<body>
    <div id="migasDePan">
        <fieldset>
            <ul class="migas">
                <li><a href="../index.html">Inicio</a></li>
                <li>Catálogo</li>
            </ul>
        </fieldset>
    </div>
    
    <h1>Catálogo de Relojes</h1>
    
    <?php if (empty($relojes)): ?>
        <p>No hay relojes disponibles en este momento.</p>
    <?php else: ?>
        <?php foreach ($relojesPorMarca as $marca => $relojesMarca): ?>
            <h2><?= htmlspecialchars($marca); ?></h2>
            <div id="productos<?= htmlspecialchars($marca); ?>" class="productos-container">
                <?php foreach ($relojesMarca as $reloj): ?>
                    <fieldset class="producto">
                        <legend>Reloj <?= htmlspecialchars($reloj->getNombreCompleto()); ?></legend>
                        <img src="<?= htmlspecialchars($reloj->getUrlImagen()); ?>" 
                             alt="<?= htmlspecialchars("Reloj " . $reloj->getNombreCompleto()); ?>">
                        <p>
                            <?= htmlspecialchars(strtoupper($reloj->getNombreCompleto())); ?> 
                            - <?= htmlspecialchars($reloj->getPrecioFormateado()); ?>
                        </p>
                        <?php if ($reloj->getDisponibilidad()): ?>
                            <button name="comprar" data-id="<?= htmlspecialchars($reloj->getId()); ?>">Comprar</button>
                        <?php else: ?>
                            <p class="no-disponible">No disponible</p>
                        <?php endif; ?>
                    </fieldset>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesComprar = document.querySelectorAll('button[name="comprar"]');
        
        botonesComprar.forEach(boton => {
            boton.addEventListener('click', function() {
                const idReloj = this.getAttribute('data-id');
                alert('Añadido al carrito: Reloj ID ' + idReloj);
                // Añadir al carrito
                // Por ejemplo, usando AJAX para enviar el ID al servidor
            });
        });
    });
    </script>
</body>
</html>