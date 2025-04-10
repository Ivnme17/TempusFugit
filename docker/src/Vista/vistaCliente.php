<?php
session_start();

if(isset($_POST['eliminar'])){
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    
    if($id !== null && isset($_SESSION['cesta'][$id])){
        unset($_SESSION['cesta'][$id]);
        unset($_SESSION['cantidad'][$id]);
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

if(filter_input(INPUT_POST,"finalizar") && isset($_SESSION['cesta']) && !empty($_SESSION['cesta'])){
    include_once "./Vista/pago.php";
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
              <a class="nav-link" href="#productos">Tienda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#soporte">Soporte</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-warning" href="../verificacion_empleado.php" style="color: #4E3B31; margin-left: 10px;">
                ¿Eres empleado/a?
              </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Vista/indexCliente.html" tabindex="-1" aria-disabled="true">
                    <i class="fa-solid fa-house"></i>
                </a>
            </li>
          </ul>

          <form class="d-flex">
            <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search">
            <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          </form>
          
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>TF-2024-001</td>
                    <td>15/03/2024</td>
                    <td>Reloj Lotus Multifunction</td>
                    <td>Enviado</td>
                    <td>
                        <button>Detalles</button>
                        <button>Seguimiento</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div id="productos">
        <h2>CATÁLOGO DE RELOJES</h2>
        <fieldset class="producto">
            <legend>Reloj Lotus</legend>
            <img src="https://static6.festinagroup.com/product/lotus/watches/detail/big/l18812_3.webp" alt="Reloj Lotus Multifunction">
            <p>RELOJ DE HOMBRE LOTUS MULTIFUNCTION CON ESFERA NEGRA 18812/3 - 89,00 €</p>
            <button>Añadir al Carrito</button>
        </fieldset>
        <fieldset class="producto">
            <legend>Reloj Casio G-Shock</legend>
            <img src="https://www.baroli.es/wp-content/uploads/2015/12/GA-120BB-1AER.jpg" alt="Reloj Casio G-Shock">
            <p>CASIO G-SHOCK RESISTENTE AL AGUA Y GOLPES - 120,00 €</p>
            <button>Añadir al Carrito</button>
        </fieldset>
    </div>

    <div id="pieDePagina">
        <footer>
            <pre>Iván Martínez Estrada - 2ºDAW - Área Cliente</pre>
        </footer>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const botonCarrito = document.querySelector('a[href="#carrito"]');
            
            const seccionCarrito = document.getElementById('carrito');
            
            botonCarrito.addEventListener('click', function(e) {
                e.preventDefault();
                if (seccionCarrito.style.display === 'none' || !seccionCarrito.style.display) {
                    seccionCarrito.style.display = 'block';
                    seccionCarrito.scrollIntoView({ behavior: 'smooth' });
                } else {
                    seccionCarrito.style.display = 'none';
                }
            });
            
            const botonesAñadir = document.querySelectorAll('.producto button');
            
            botonesAñadir.forEach(boton => {
                boton.addEventListener('click', function() {
                    const producto = this.closest('.producto');
                    const nombre = producto.querySelector('legend').textContent;
                    const precio = producto.querySelector('p').textContent.match(/\d+,\d+/)[0];
                    
                    alert(`¡${nombre} añadido al carrito!`);
                });
            });
        });
    </script>
</body>
</html>