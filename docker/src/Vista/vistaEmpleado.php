<?php
// He tenido que cambiar la ruta de los require_once para que funcionen correctamente
require_once __DIR__ . '/../Servicio/Db.php';
require_once __DIR__ . '/../Modelo/Usuario.php';
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
          <form class="d-flex">
            <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search">
            <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          </form>
          <div id="botonesDeRegistro">
            <a href="../cerrarSesion.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </div>
        </div>
      </div>
    </nav>

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
                    <a href="controladorEmpleado.php?action=editar&id_cliente=<?= $cliente->getId_usuario() ?>">
                      <i class="fa-solid fa-pen-to-square"></i>
                      <input type="button" value="Eliminar" class="btn btn-secondary">
                      <input type="button" value="Editar" class="btn btn-primary">
                    </a>
                  </td>
                </tr>
            <?php }
            } ?>
            </tbody>
        </table>
    </div>
    
    <div id="gestionInventario">
        <h1>INVENTARIO DE RELOJES</h1>
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
          <a href="controladorEmpleado.php?action=editar&id_reloj=<?= $reloj['id_reloj']; ?>"></a>
              <i class="fa-solid fa-pen-to-square"></i>
              <input type="button" value="Eliminar" class="btn btn-secondary">
              <input type="button" value="Editar" class="btn btn-primary">
          </a>
              </td>
          </tr>
            <?php } ?>
            </tbody>
        </table>
          </div>

          <div id="informes">
        <h1>INFORMES Y ESTADÍSTICAS</h1>
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
          <a href="controladorEmpleado.php?action=editar&id_reloj=<?= $masvendido['id_reloj']; ?>">
              <i class="fa-solid fa-pen-to-square"></i>
              <input type="button" value="Eliminar" class="btn btn-secondary">
              <input type="button" value="Editar" class="btn btn-primary">
          </a>
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
</body>
</html>