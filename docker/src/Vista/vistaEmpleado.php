<?php
// He tenido que cambiar la ruta de los require_once para que funcionen correctamente
require_once __DIR__ . '/../Servicio/Db.php';
require_once __DIR__ . '/../Modelo/Usuario.php';
require_once __DIR__ . '/../Modelo/Pedidos.php'; 
require_once __DIR__ . '/../Modelo/Reloj.php';

$clientesObj = Usuario::listarClientes();

$relojes = [];
$conexion = Db::getConexion();
$consulta = "SELECT * FROM relojes"; 
$resultado = $conexion->query($consulta);

if ($resultado) {
  while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
    $relojes[] = $fila;
  }
}

$masVendidos = [];
$conexion = Db::getConexion();
$consulta = "SELECT * FROM relojes WHERE stock < 5 ORDER BY stock ASC LIMIT 5";
$resultado = $conexion->query($consulta);
if ($resultado) {
  while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
    $masVendidos[] = $fila;
  }
}

$totalPrecio = 0; 
$conexion = Db::getConexion();
$consulta = "SELECT SUM(precio) as total_precio FROM relojes";
$resultado = $conexion->query($consulta);
if ($resultado) {
  $fila = $resultado->fetch(PDO::FETCH_ASSOC);
  $totalPrecio = $fila['total_precio'];
}

$todosPedidos = Pedidos::obtenerTodosPedidos();
// Ordenar por fecha_pedido en orden descendente
usort($todosPedidos, fn($a, $b) => strtotime($b['fecha_pedido']) - strtotime($a['fecha_pedido']));
// Tomar solo los primeros 5
$pedidos = array_slice($todosPedidos, 0, 5);

if(filter_input(INPUT_POST,'Eliminar')){
  unset($_SESSION['cesta']);
  unset($_SESSION['cantidad']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action'])) {
    switch ($_POST['action']) {
      case 'agregarMarcaModelo':
        // Código para insertar una nueva marca/modelo
        $conexion = Db::getConexion();
        $consulta = "INSERT INTO marca_modelo (marca, modelo) VALUES (:marca, :modelo)";
        $stmt = $conexion->prepare($consulta);
        
        $resultado = $stmt->execute([
          ':marca' => $_POST['marca'],
          ':modelo' => $_POST['modelo']
        ]);
        
        if ($resultado) {
          header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1&message=Marca+y+modelo+agregados+correctamente#gestionInventario');
        } else {
          header('Location: ' . $_SERVER['PHP_SELF'] . '?error=1&message=Error+al+agregar+marca+y+modelo#gestionInventario');
        }
        break;
      case 'agregarReloj':
        // Crear un nuevo objeto Reloj con los datos del formulario
        $nuevoReloj = new Reloj([
          'id_marca_modelo' => $_POST['id_marca_modelo'],
          'precio' => $_POST['precio'],
          'tipo' => $_POST['tipo'],
          'stock' => $_POST['stock'],
          'url_imagen' => $_POST['url_imagen']
        ]);
        // Guardar el nuevo reloj
        if ($nuevoReloj->guardar()) {
          header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1&message=Reloj+agregado+correctamente#gestionInventario');
        } else {
          header('Location: ' . $_SERVER['PHP_SELF'] . '?error=1&message=Error+al+agregar+el+reloj#gestionInventario');
        }
        break;      
      case 'editarReloj':
        // Crear un nuevo objeto Reloj con los datos actualizados del formulario
        $nuevoReloj = new Reloj([
          'id_marca_modelo' => $_POST['id_marca_modelo'],
          'precio' => $_POST['precio'],
          'tipo' => $_POST['tipo'],
          'stock' => $_POST['stock'],
          'url_imagen' => $_POST['url_imagen']
        ]);

        // Actuaslizar el reloj
        if ($nuevoReloj->guardar()) {
          header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1&message=Reloj+agregado+correctamente#gestionInventario');
        } else {
          header('Location: ' . $_SERVER['PHP_SELF'] . '?error=1&message=Error+al+agregar+el+reloj#gestionInventario');
        }
        break;
        
      case 'editarCliente':
        // Código para actualizar el cliente
        $conexion = Db::getConexion();
        $consulta = "UPDATE usuarios SET 
                    login = :login,
                    correo = :correo, 
                    direccion = :direccion, 
                    iban = :iban 
                    WHERE id_usuario = :id_usuario";
        
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([
            ':login' => $_POST['login'],
            ':correo' => $_POST['correo'],
            ':direccion' => $_POST['direccion'],
            ':iban' => $_POST['iban'],
            ':id_usuario' => $_POST['id_usuario']
        ]);
        
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1&message=Cliente+actualizado+correctamente#gestionClientes');
        break;
        
      case 'editarPedido':
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1&message=Pedido+actualizado+correctamente#informes');
        break;
        case 'eliminarReloj':
          if (isset($_POST['id_reloj'])) {
            $reloj = Reloj::obtenerPorId($_POST['id_reloj']);
            if ($reloj && $reloj->eliminar()) {
              header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1&message=Reloj+eliminado+correctamente#gestionInventario');
            } else {
              header('Location: ' . $_SERVER['PHP_SELF'] . '?error=1&message=Error+al+eliminar+el+reloj#gestionInventario');
            }
          }
          break;
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Empleado - Tempus Fugit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/estilos.css">
    <script src="../js/validarEdiciones.js" defer></script>
    <style>
        .btn-primary {
            background-color:#FFD700;
            border-color: #FFD700;
            color: black;
        }
        
        .btn-secondary {
            background-color: #FFD700;
            border-color: #FFD700;
            color:black;
        }
        
        .btn-info {
            background-color: #FFD700;
            border-color: #FFD700;
            color: white;
        }
        
        .btn-success {
            background-color: #FFD700;
            border-color: #FFD700;
            color: black;
        }

        .modal-body {
            color: #333;
        }
        
        .form-control {
            color: #212529;
            background-color: #fff;
            border-color: #ced4da;
        }
        
        .form-label {
            color: #212529;
            font-weight: 500;
        }
        
        .modal-header {
            background-color: #f8f9fa;
        }
        
        .modal-title {
            color: #212529;
        }
        
        .btn-close {
          filter: none;
        }
    </style>
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
              <a class="nav-link" aria-current="page" href="#inicio">Panel de Empleado</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#gestionClientes">Gestión de Clientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#gestionInventario">Inventario</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#informes">Informes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../Vista/indexEmpleado.php" tabindex="-1" aria-disabled="true">
                <i class="fa-solid fa-building"></i>
              </a>
            </li>
          </ul>
          <div id="botonesDeRegistro">
            <a href="../cerrarSesion.php" class="btn btn-danger">Cerrar Sesión</a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mensaje de éxito -->
    <?php if(isset($_GET['success']) && $_GET['success'] == 1){ ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_GET['message'] ?? 'Operación realizada con éxito') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php }; ?>

    <div id="header">
        <div id="logoEmpresa">
            <a href="vista-empleado.html"><img src="../logoEmpresa/TEMPUS-removebg-preview.png"></a>
        </div>
    
    <div id="gestionClientes">
        <h1>GESTIÓN DE CLIENTES</h1>
        <p>Aquí podrás administrar y realizar seguimiento de los clientes de Tempus Fugit.</p>
        <table class="table table-striped table-bordered table-hover">
            <thead>
          <tr>
              <th>ID Usuario</th>
              <th>cliente</th>
              <th>Correo</th>
              <th>Dirección</th>
              <th>IBAN</th>
              <th>Acciones</th>
          </tr>
            </thead>
            <tbody>
            <?php             
            if (!empty($clientesObj)) {
              foreach ($clientesObj as $cliente) { ?>
                <tr>
                  <td><?= $cliente->getId_usuario() ?></td>
                  <td><?= $cliente->getLogin() ?></td>
                  <td><?= $cliente->getCorreo() ?></td>
                  <td><?= $cliente->getDireccion() ?></td>
                  <td><?= $cliente->getIban() ?></td>
                  <td>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarClienteModal<?= $cliente->getId_usuario() ?>">
                      <i class="fa-solid fa-pen-to-square"></i> Editar
                    </button>
                    <button type="button" class="btn btn-secondary">
                      Eliminar
                    </button>
                  </td>
                </tr>
            <?php }
            } ?>
            </tbody>
        </table>
    </div>
    
    <div id="gestionInventario">
        <h1>INVENTARIO DE RELOJES</h1>
        <div class="mb-3">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#agregarRelojModal">
                <i class="fa-solid fa-plus"></i> Añadir Nuevo Reloj
            </button>
            <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#agregarMarcaModeloModal">
                <i class="fa-solid fa-plus"></i> Añadir Nueva Marca/Modelo
            </button>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <thead>
          <tr>
              <th>ID Reloj</th>
              <th>ID Marca/Modelo</th>
              <th>Precio</th>
              <th>Tipo</th>
              <th>Stock</th>
              <th>URL Imagen</th>
              <th>Acciones</th>
          </tr>
            </thead>
            <tbody>
            <?php foreach($relojes as $reloj){ ?>
          <tr>
              <td><?= $reloj['id_reloj']; ?></td>
              <td><?= $reloj['id_marca_modelo']; ?></td>
              <td><?= $reloj['precio']; ?> €</td>
              <td><?= $reloj['tipo']; ?></td>
              <td><?= $reloj['stock']; ?></td>
              <td><?= $reloj['url_imagen']; ?></td>
              <td>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarRelojModal<?= $reloj['id_reloj']; ?>">
                  <i class="fa-solid fa-pen-to-square"></i> Editar
                </button>
                <button type="button" class="btn btn-secondary">
                  Eliminar
                </button>
              </td>
          </tr>
            <?php } ?>
            </tbody>
        </table>
          </div>

          <div id="informes">
        <h1>INFORMES Y ESTADÍSTICAS</h1>

        <h2>Últimos 5 Pedidos</h2>
        <div>
        <table class="table table-striped table-bordered table-hover">
            <thead>
          <tr>
              <th>ID Pedido</th>
              <th>ID Usuario</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Acciones</th>
          </tr>
            </thead>
            <tbody>
            <?php foreach($pedidos as $pedido){ ?>
          <tr>
              <td><?= $pedido['id_pedido']; ?></td>
              <td><?= $pedido['id_usuario']; ?></td>
              <td><?= $pedido['fecha_pedido']; ?></td>
              <td><?= isset($pedido['precio_final']) ? $pedido['precio_final'] : $pedido['precio_unitario'] * $pedido['cantidad']; ?> €</td>
              <td>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#verPedidoModal<?= $pedido['id_pedido']; ?>">
                  <i class="fa-solid fa-eye"></i> Ver detalles
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarPedidoModal<?= $pedido['id_pedido']; ?>">
                  <i class="fa-solid fa-pen-to-square"></i> Editar
                </button>
              </td>
          </tr>
            <?php } ?>
            </tbody>
        </table>

        <h1>Productos con menor stock</h1>
        <div>
        <table class="table table-striped table-bordered table-hover">
            <thead>
          <tr>
              <th>ID Reloj</th>
              <th>ID Marca/Modelo</th>
              <th>Precio</th>
              <th>Tipo</th>
              <th>Stock</th>
              <th>Acciones</th>
          </tr>
            </thead>
            <tbody>
            <?php foreach($masVendidos as $masvendido){ ?>
          <tr>
              <td><?= $masvendido['id_reloj']; ?></td>
              <td><?= $masvendido['id_marca_modelo']; ?></td>
              <td><?= $masvendido['precio']; ?> €</td>
              <td><?= $masvendido['tipo']; ?></td>
              <td><?= $masvendido['stock']; ?></td>
              <td>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarRelojModal<?= $masvendido['id_reloj']; ?>">
                  <i class="fa-solid fa-pen-to-square"></i> Editar
                </button>
                <button type="button" class="btn btn-secondary">
                  Eliminar
                </button>
              </td>
          </tr>
            <?php } ?>
            </tbody>
        </table>
        <h3> Precio Total de articulos: <?= $totalPrecio?> €</h3>
        </div>
    </div>

    <div id="pieDePagina">
        <footer>
            <pre>Iván Martínez Estrada - 2ºDAW - Vista Empleado</pre>
        </footer>
    </div>
    <!-- FORMULARIO PARA AÑADIR NUEVA MARCA/MODELO -->
    <div class="modal fade" id="agregarMarcaModeloModal" tabindex="-1" aria-labelledby="agregarMarcaModeloModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="agregarMarcaModeloModalLabel">Añadir Nueva Marca/Modelo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" id="formAgregarMarcaModelo">
              <input type="hidden" name="action" value="agregarMarcaModelo">
              
              <div class="mb-3">
                <label for="marca_nueva" class="form-label">Marca</label>
                <input type="text" class="form-control" id="marca_nueva" name="marca" required>
              </div>
              
              <div class="mb-3">
                <label for="modelo_nuevo" class="form-label">Modelo</label>
                <input type="text" class="form-control" id="modelo_nuevo" name="modelo" required>
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Añadir Marca/Modelo</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- FORMULARIO PARA AÑADIR NUEVO RELOJ -->
    <div class="modal fade" id="agregarRelojModal" tabindex="-1" aria-labelledby="agregarRelojModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="agregarRelojModalLabel">Añadir Nuevo Reloj</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" id="formAgregarReloj">
              <input type="hidden" name="action" value="agregarReloj">
              
              <div class="mb-3">
                <label for="id_marca_modelo_nuevo" class="form-label">ID Marca/Modelo</label>
                <input type="text" class="form-control" id="id_marca_modelo_nuevo" name="id_marca_modelo" required>
              </div>
              
              <div class="mb-3">
                <label for="precio_nuevo" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio_nuevo" name="precio" required>
              </div>
              
              <div class="mb-3">
                <label for="tipo_nuevo" class="form-label">Tipo</label>
                <select class="form-control" id="tipo_nuevo" name="tipo" required>
                  <option value="">Seleccione un tipo</option>
                  <option value="analógico">Analógico</option>
                  <option value="digital">Digital</option>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="stock_nuevo" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock_nuevo" name="stock" min="0" required>
              </div>
              
              <div class="mb-3">
                <label for="url_imagen_nuevo" class="form-label">URL Imagen</label>
                <input type="text" class="form-control" id="url_imagen_nuevo" name="url_imagen" required>
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Añadir Reloj</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- FORMULARIO PARA EDITAR CLIENTES -->
    <?php if (!empty($clientesObj)) {
        foreach ($clientesObj as $cliente) { ?>
    <div class="modal fade" id="editarClienteModal<?= $cliente->getId_usuario() ?>" tabindex="-1" aria-labelledby="editarClienteModalLabel<?= $cliente->getId_usuario() ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editarClienteModalLabel<?= $cliente->getId_usuario() ?>">Editar Cliente: <?= $cliente->getLogin() ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
              <input type="hidden" name="action" value="editarCliente">
              <input type="hidden" name="id_usuario" value="<?= $cliente->getId_usuario() ?>">
              
              <div class="mb-3">
                <label for="login<?= $cliente->getId_usuario() ?>" class="form-label">Login</label>
                <input type="text" class="form-control" id="login<?= $cliente->getId_usuario() ?>" name="login" value="<?= $cliente->getLogin() ?>">
              </div>
              
              <div class="mb-3">
                <label for="correo<?= $cliente->getId_usuario() ?>" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo<?= $cliente->getId_usuario() ?>" name="correo" value="<?= $cliente->getCorreo() ?>">
              </div>
              
              <div class="mb-3">
                <label for="direccion<?= $cliente->getId_usuario() ?>" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion<?= $cliente->getId_usuario() ?>" name="direccion" value="<?= $cliente->getDireccion() ?>">
              </div>
              
              <div class="mb-3">
                <label for="iban<?= $cliente->getId_usuario() ?>" class="form-label">IBAN</label>
                <input type="text" class="form-control" id="iban<?= $cliente->getId_usuario() ?>" name="iban" value="<?= $cliente->getIban() ?>">
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php }
    } ?>
    
    <!-- FORMULARIO PARA EDITAR RELOJES -->
    <?php foreach($relojes as $reloj){ ?>
    <div class="modal fade" id="editarRelojModal<?= $reloj['id_reloj']; ?>" tabindex="-1" aria-labelledby="editarRelojModalLabel<?= $reloj['id_reloj']; ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editarRelojModalLabel<?= $reloj['id_reloj']; ?>">Editar Reloj ID: <?= $reloj['id_reloj']; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
              <input type="hidden" name="action" value="editarReloj">
              <input type="hidden" name="id_reloj" value="<?= $reloj['id_reloj']; ?>">
              
              <div class="mb-3">
                <label for="id_marca_modelo<?= $reloj['id_reloj']; ?>" class="form-label">ID Marca/Modelo</label>
                <input type="text" class="form-control" id="id_marca_modelo<?= $reloj['id_reloj']; ?>" name="id_marca_modelo" value="<?= $reloj['id_marca_modelo']; ?>">
              </div>
              
              <div class="mb-3">
                <label for="precio<?= $reloj['id_reloj']; ?>" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio<?= $reloj['id_reloj']; ?>" name="precio" value="<?= $reloj['precio']; ?>">
              </div>
              
              <div class="mb-3">
                <label for="tipo<?= $reloj['id_reloj']; ?>" class="form-label">Tipo</label>
                <input type="text" class="form-control" id="tipo<?= $reloj['id_reloj']; ?>" name="tipo" value="<?= $reloj['tipo']; ?>">
              </div>
              
              <div class="mb-3">
                <label for="stock<?= $reloj['id_reloj']; ?>" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock<?= $reloj['id_reloj']; ?>" name="stock" value="<?= $reloj['stock']; ?>">
              </div>
              
              <div class="mb-3">
                <label for="url_imagen<?= $reloj['id_reloj']; ?>" class="form-label">URL Imagen</label>
                <input type="text" class="form-control" id="url_imagen<?= $reloj['id_reloj']; ?>" name="url_imagen" value="<?= $reloj['url_imagen']; ?>">
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
    
    <!-- VER DETALLES DE PEDIDOS -->
    <?php foreach($pedidos as $pedido){ ?>
    <div class="modal fade" id="verPedidoModal<?= $pedido['id_pedido']; ?>" tabindex="-1" aria-labelledby="verPedidoModalLabel<?= $pedido['id_pedido']; ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="verPedidoModalLabel<?= $pedido['id_pedido']; ?>">Detalles del Pedido #<?= $pedido['id_pedido']; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>ID Pedido:</strong> <?= $pedido['id_pedido']; ?></p>
            <p><strong>ID Usuario:</strong> <?= $pedido['id_usuario']; ?></p>
            <p><strong>Fecha:</strong> <?= $pedido['fecha_pedido']; ?></p>
            <p><strong>Total:</strong> <?= isset($pedido['precio_final']) ? $pedido['precio_final'] : $pedido['precio_unitario'] * $pedido['cantidad']; ?> €</p>            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- EDITAR PEDIDOS -->
    <div class="modal fade" id="editarPedidoModal<?= $pedido['id_pedido']; ?>" tabindex="-1" aria-labelledby="editarPedidoModalLabel<?= $pedido['id_pedido']; ?>" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editarPedidoModalLabel<?= $pedido['id_pedido']; ?>">Editar Pedido #<?= $pedido['id_pedido']; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
              <input type="hidden" name="action" value="editarPedido">
              <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido']; ?>">
              
              <div class="mb-3">
                <label for="id_usuario<?= $pedido['id_pedido']; ?>" class="form-label">ID Usuario</label>
                <input type="text" class="form-control" id="id_usuario<?= $pedido['id_pedido']; ?>" name="id_usuario" value="<?= $pedido['id_usuario']; ?>" readonly>
              </div>
              
              <div class="mb-3">
                <label for="fecha_pedido<?= $pedido['id_pedido']; ?>" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha_pedido<?= $pedido['id_pedido']; ?>" name="fecha_pedido" value="<?= date('Y-m-d', strtotime($pedido['fecha_pedido'])); ?>">
              </div>
              
              <div class="mb-3">
                <label for="precio_final<?= $pedido['id_pedido']; ?>" class="form-label">Precio Final</label>
                <input type="number" step="0.01" class="form-control" id="precio_final<?= $pedido['id_pedido']; ?>" name="precio_final" value="<?= isset($pedido['precio_final']) ? $pedido['precio_final'] : $pedido['precio_unitario'] * $pedido['cantidad']; ?>">
              </div>
              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
</body>
</html>