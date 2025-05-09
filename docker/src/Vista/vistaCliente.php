<?php
require_once '../Servicio/Db.php';
require_once '../Modelo/Pedidos.php';
require_once '../Modelo/Usuario.php'; 
session_start();
if(filter_input(INPUT_POST,"finalizar") && isset($_SESSION['cesta']) && !empty($_SESSION['cesta'])){
    header("Location: pago.php");
    exit();
}
if(filter_input(INPUT_POST,"finalizar")){
    echo "<dialog open style='padding: 20px; border-radius: 330px; border: none; box-shadow: 0 0 20px rgba(0,0,0,0.3); background-color:#FFD700;'>
           <p style='color: brown; font-size: 18px; margin: 0;'>La cesta está vacía</p>
        </dialog>";
    header("Location: vistaCliente.php");
}
$clienteId = $_SESSION['usuario'];
$_SESSION['pedidos'] = [];

try {
    $usuario = Usuario::verUsuario($_SESSION['usuario']);
    if ($usuario) {
        $clienteId = $usuario->getId_usuario();
        
        if ($clienteId) {
            $pedidos = Pedidos::obtenerPedidosConProductos($clienteId);
            if ($pedidos) {
                $_SESSION['pedidos'] = $pedidos;
            }
        }
    }
} catch(PDOException $e) {
    error_log("Error: " . $e->getMessage());
}
   
if(isset($_POST['eliminar'])){
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    
    if($id !== null && isset($_SESSION['cesta'][$id])){
        unset($_SESSION['cesta'][$id]);
        unset($_SESSION['cantidad'][$id]);
        
        include_once '../Vista/vistaCliente.php';
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Cliente - Tempus Fugit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <nav class="navbar navbar-expand-xl navbar-light" style="background-color: transparent;">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLight" aria-controls="navbarLight" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse show" id="navbarLight">
          <ul class="navbar-nav me-auto mb-2 mb-xl-0">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="#inicio">Área Personal</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#misPedidos">Mis Pedidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Vista/indexCliente.php" tabindex="-1" aria-disabled="true">
                    <i class="fa-solid fa-house"></i>
                </a>
            </li>
          </ul>
          <a href="#carrito" class="btn btn-warning ms-2 me-2" id="botonCarrito">
          <i class="fa-solid fa-cart-shopping"></i>
          </a>         
          <div id="botonesDeRegistro">
            <a href="../cerrarSesion.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        </div>
      </div>
    </nav>

    <div id="header">
        <div id="logoEmpresa">
            <a href="vista-cliente.html"><img src="../logoEmpresa/TEMPUS-removebg-preview.png"></a>
        </div>
    </div>
    <div id="carrito" style="display: none;">
  <h1>MI CARRITO</h1>
  <div class="table-responsive">
      <table class="table table-striped">
          <thead>
              <tr>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th>Cantidad</th>
                  <th>Subtotal</th>
                  <th>Acciones</th>
              </tr>
          </thead>
          <tbody id="items-carrito">
            <?php if(!isset($_SESSION['cesta']) || empty($_SESSION['cesta'])){ ?>
                <tr>
                    <td colspan="5" class="text-center">No hay productos en el carrito.</td>
                </tr>
            <?php } else { ?>
                    <?php foreach ($_SESSION['cesta'] as $id => $reloj) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reloj['nombre']); ?></td>
                            <td><?php echo number_format($reloj['precio'], 2); ?> €</td>
                            <td>
                                <?php 
                                $cantidad = isset($_SESSION['cantidad'][$id]) ? $_SESSION['cantidad'][$id] : 1;
                                echo $cantidad;
                                ?>
                            </td>
                            <td>
                                <?php echo number_format($reloj['precio'] * $cantidad, 2); ?> €
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <button type="submit" class="btn btn-danger" name="eliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
          </tbody>
          <tfoot>
              <tr>
                  <td colspan="3"><strong>Total:</strong></td>
                  <td>
                      <?php
                      $total = 0;
                      if(isset($_SESSION['cesta'])) {
                          foreach($_SESSION['cesta'] as $id => $reloj) {
                              $cantidad = isset($_SESSION['cantidad'][$id]) ? $_SESSION['cantidad'][$id] : 1;
                              $total += $reloj['precio'] * $cantidad;
                          }
                      }
                      echo number_format($total, 2) . ' €';
                      ?>
                  </td>
                  <td></td>
              </tr>
          </tfoot>
      </table>
  </div>
  <div class="d-flex justify-content-end gap-2 mt-3">
      <form method="POST">
          <button type="submit" class="boton finalizar" name="finalizar" value="1">Finalizar Compra</button>
      </form>
  </div>
</div>
    <div id="misPedidos">
        <h1>MIS PEDIDOS</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nº Pedido</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
               <?php
              /* echo "Client ID: " . $clienteId;
               if(isset($_SESSION['pedidos'])) {
                var_dump($_SESSION['pedidos'])
                ;}*/
                ?>
            <?php if(isset($_SESSION['pedidos']) && !empty($_SESSION['pedidos'])){ ?>
    <?php foreach($_SESSION['pedidos'] as $pedido){ ?>
        <tr>
            <td><?= $pedido['id_pedido'] ?></td>
            <td><?= $pedido['fecha_pedido'] ?></td>
            <td>
                Precio: <?= number_format($pedido['precio_base'], 2) ?> € 
                (Final: <?= number_format($pedido['precio_final'], 2) ?> €)
            </td>
            <td>Completado</td>  
        </tr>
    <?php }; ?>
<?php }else{ ?>
    <tr>
        <td colspan="5" class="text-center">No tienes ningún pedido realizado.</td>
    </tr>
<?php }; ?>
            </tbody>
        </table>
    </div>

    <div id="pieDePagina">
        <footer>
            <pre>Iván Martínez Estrada - 2ºDAW - Área Cliente</pre>
        </footer>
    </div>
    
    <!-- Este script es para hacer que el carrito aparezca y desaparezca cuando hago clic -->

    <script>
            // Espero a que se cargue toda la página antes de ejecutar nada
            document.addEventListener('DOMContentLoaded', function() {
                // Busco el botón o enlace que tiene el ancla #carrito
                // Este es el botón que mostrará/ocultará el carrito
                const botonCarrito = document.querySelector('a[href="#carrito"]');
                
                // Aquí busco la sección donde está el carrito
                // Tiene que tener un id="carrito" en el HTML
                const seccionCarrito = document.getElementById('carrito');
                
                // Le añado un evento al botón para que haga algo cuando lo pulso
                botonCarrito.addEventListener('click', function(e) {
                    // Esto es para que no me lleve a otra página cuando pulso el enlace
                    // Porque por defecto los enlaces con # te llevan a esa parte de la página
                    e.preventDefault();
                    
                    // Compruebo si el carrito está oculto (display:none) o no se ve
                    // El !seccionCarrito.style.display es por si no tiene estilo definido
                    if (seccionCarrito.style.display === 'none' || !seccionCarrito.style.display) {
                        // Si está oculto, lo muestro
                        seccionCarrito.style.display = 'block';
                        
                        // Y hago scroll hasta el carrito para que se vea bien
                        // El smooth hace que el scroll sea suave y queda más bonito
                        seccionCarrito.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        // Si ya está visible, lo oculto
                        seccionCarrito.style.display = 'none';
                    }
                });
            });
        </script>
</body>
</html>