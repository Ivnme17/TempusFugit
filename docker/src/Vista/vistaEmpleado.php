<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Empleado - Tempus Fugit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../estilos.css">
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
          </ul>
          <form class="d-flex">
            <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search">
            <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          </form>
          <div id="botonesDeRegistro">
            <button onclick="location.href='../index.html'">Cerrar Sesión</button>
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
              <th>ID Cliente</th>
              <th>ID Usuario</th>
              <th>Telefono</th>
              <th>Correo</th>
              <th>Dirección</th>
              <th>IBAN</th>
              <th>Acciones</th>
          </tr>
            </thead>
            <tbody>
            <?php foreach($usuarios as $usuario){ ?>
          <tr>
              <td><?= $usuario['id_cliente']; ?></td>
              <td><?= $usuario['id_usuario']; ?></td>
              <td><?= $usuario['telefono']; ?></td>
              <td><?= $usuario['correo']; ?></td>
              <td><?= $usuario['direccion']; ?></td>
              <td><?= $usuario['iban']; ?></td>
              <td>
            <button>Ver Detalles</button>
            <button>Editar</button>
              </td>
          </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    
    <div id="gestionInventario">
        <h1>INVENTARIO DE RELOJES</h1>
        <fieldset class="producto">
            <legend>Stock de Relojes</legend>
            <p>Total de Relojes: 150</p>
            <p>Marcas: Rolex (50), Casio (40), Lotus (60)</p>
        </fieldset>
    </div>

    <div id="informes">
        <h1>INFORMES Y ESTADÍSTICAS</h1>
        <div>
            <h2>Ventas Mensuales</h2>
            <p>Total Ventas: 45,000 €</p>
            <p>Reloj más vendido: Lotus Multifunction</p>
        </div>
    </div>

    <div id="pieDePagina">
        <footer>
            <pre>Iván Martínez Estrada - 2ºDAW - Vista Empleado</pre>
        </footer>
    </div>
</body>
</html>