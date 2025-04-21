<?php
// He tenido que cambiar la ruta de los require_once para que funcionen correctamente
require_once __DIR__ . '/../Servicio/Db.php';
require_once __DIR__ . '/../Modelo/Usuario.php';

$usuariosObj = Usuario::listarUsuarios();

$modoEdicion = false;
$usuarioEditar = null;

if (isset($_GET['action']) && $_GET['action'] == 'editar' && isset($_GET['id_usuario'])) {
    $modoEdicion = true;
    $usuarioEditar = Usuario::verUsuario($_GET['id_usuario']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['eliminar']) && isset($_POST['login'])) {
        Usuario::eliminarUsuario($_POST['login']);
        // Redirigir para evitar reenvío del formulario
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Si se ha enviado el formulario de crear/editar
    if (isset($_POST['guardar'])) {
        $usuario = new Usuario();
        $usuario->setLogin($_POST['login']);
        $usuario->setClave($_POST['clave']);
        $usuario->setId_rol($_POST['id_rol']);
        $usuario->setNombre($_POST['nombre']);
        $usuario->setApellidos($_POST['apellidos']);
        $usuario->setDni($_POST['dni']);
        $usuario->setNss($_POST['nss'] ?? null);
        $usuario->setTelefono($_POST['telefono']);
        $usuario->setCorreo($_POST['correo']);
        $usuario->setDireccion($_POST['direccion']);
        $usuario->setIban($_POST['iban'] ?? null);
        
        if (isset($_POST['id_usuario'])) {
            $usuario->setId_usuario($_POST['id_usuario']);
            $usuario->actualizarUsuario();
        } else {
            $usuario->añadirUsuario();
        }
        
        // Redirigir para evitar reenvío del formulario
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Filtrar usuarios
$usuariosFiltrados = $usuariosObj;
if (isset($_GET['buscar']) && !empty($_GET['criterio']) && !empty($_GET['valor'])) {
    $usuariosFiltrados = Usuario::buscarUsuarios($_GET['criterio'], $_GET['valor']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Tempus Fugit</title>
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
              <a class="nav-link" aria-current="page" href="vistaEmpleado.php">Panel de Empleado</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="gestionUsuarios.php">Gestión de Usuarios</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="vistaEmpleado.php#gestionClientes">Gestión de Clientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="vistaEmpleado.php#gestionInventario">Inventario</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="vistaEmpleado.php#informes">Informes</a>
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

    <div id="header">
        <div id="logoEmpresa">
            <a href="vistaEmpleado.php"><img src="../logoEmpresa/TEMPUS-removebg-preview.png"></a>
        </div>
    </div>
    
    <div class="container mt-4">
        <h1 class="mb-4 text-center">GESTIÓN DE USUARIOS</h1>
        
        <!-- Buscador de usuarios -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Buscar Usuarios
            </div>
            <div class="card-body">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-9">
                        <select name="criterio" class="form-select">
                            <option value="login">Login</option>
                            <option value="nombre">Nombre</option>
                            <option value="apellidos">Apellidos</option>
                            <option value="dni">DNI</option>
                            <option value="correo">Correo</option>
                        </select>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="valor" class="form-control" placeholder="Valor a buscar">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="buscar" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Botón para añadir nuevo usuario -->
        <div class="mb-4">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                <i class="fas fa-plus"></i> Añadir Nuevo Usuario
            </button>
        </div>
        
        <!-- Tabla de usuarios -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                Listado de Usuarios
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Login</th>
                                <th>Rol</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>DNI</th>
                                <th>NSS</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Dirección</th>
                                <th>IBAN</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuariosFiltrados)){ ?>
                                <?php foreach ($usuariosFiltrados as $usuario){ ?>
                                <tr>
                                    <td><?= $usuario->getId_usuario() ?></td>
                                    <td><?= $usuario->getLogin() ?></td>
                                    <td>
                                        <?php 
                                            $rol = $usuario->getId_rol();
                                            switch($rol) {
                                                case 1:
                                                    echo "Admin";
                                                    break;
                                                case 2:
                                                    echo "Empleado";
                                                    break;
                                                case 3:
                                                    echo "Cliente";
                                                    break;
                                                default:
                                                    echo "Desconocido";
                                            }
                                        ?>
                                    </td>
                                    <td><?= $usuario->getNombre() ?></td>
                                    <td><?= $usuario->getApellidos() ?></td>
                                    <td><?= $usuario->getDni() ?></td>
                                    <td><?= $usuario->getNss() ?></td>
                                    <td><?= $usuario->getTelefono() ?></td>
                                    <td><?= $usuario->getCorreo() ?></td>
                                    <td><?= $usuario->getDireccion() ?></td>
                                    <td><?= $usuario->getIban() ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario" 
                                            data-id="<?= $usuario->getId_usuario() ?>" 
                                            data-login="<?= $usuario->getLogin() ?>" 
                                            data-rol="<?= $usuario->getId_rol() ?>" 
                                            data-nombre="<?= $usuario->getNombre() ?>" 
                                            data-apellidos="<?= $usuario->getApellidos() ?>" 
                                            data-dni="<?= $usuario->getDni() ?>" 
                                            data-nss="<?= $usuario->getNss() ?>" 
                                            data-telefono="<?= $usuario->getTelefono() ?>" 
                                            data-correo="<?= $usuario->getCorreo() ?>" 
                                            data-direccion="<?= $usuario->getDireccion() ?>" 
                                            data-iban="<?= $usuario->getIban() ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                            <input type="hidden" name="login" value="<?= $usuario->getLogin() ?>">
                                            <button type="submit" name="eliminar" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php }; ?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="12" class="text-center">No se encontraron usuarios</td>
                                </tr>
                            <?php }; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventana gráfica o modal para editar el usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalUsuarioLabel">Gestionar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formUsuario" method="POST" action="">
                        <input type="hidden" id="id_usuario" name="id_usuario">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="login" class="form-label">Login</label>
                                <input type="text" class="form-control" id="login" name="login" required>
                            </div>
                            <div class="col-md-6">
                                <label for="clave" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="clave" name="clave" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="id_rol" class="form-label">Rol</label>
                                <select class="form-select" id="id_rol" name="id_rol" required>
                                    <option value="1">Administrador</option>
                                    <option value="2">Empleado</option>
                                    <option value="3">Cliente</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dni" class="form-label">DNI</label>
                                <input type="text" class="form-control" id="dni" name="dni" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" required>
                            </div>
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nss" class="form-label">NSS (para empleados)</label>
                                <input type="text" class="form-control" id="nss" name="nss">
                            </div>
                            <div class="col-md-6">
                                <label for="iban" class="form-label">IBAN</label>
                                <input type="text" class="form-control" id="iban" name="iban">
                            </div>
                        </div>
                        
                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="pieDePagina" class="mt-5">
        <footer class="text-center">
            <pre>Iván Martínez Estrada - 2ºDAW - Gestión de Usuarios</pre>
        </footer>
    </div>

    <script>
        // Script para rellenar el modal con los datos del usuario al editar
        document.addEventListener('DOMContentLoaded', function() {
            const modalUsuario = document.getElementById('modalUsuario');
            modalUsuario.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                
                if (id) {
                    // Edición de usuario existente	
                    document.getElementById('modalUsuarioLabel').textContent = 'Editar Usuario';
                    document.getElementById('id_usuario').value = id;
                    document.getElementById('login').value = button.getAttribute('data-login');
                    document.getElementById('id_rol').value = button.getAttribute('data-rol');
                    document.getElementById('nombre').value = button.getAttribute('data-nombre');
                    document.getElementById('apellidos').value = button.getAttribute('data-apellidos');
                    document.getElementById('dni').value = button.getAttribute('data-dni');
                    document.getElementById('nss').value = button.getAttribute('data-nss') || '';
                    document.getElementById('telefono').value = button.getAttribute('data-telefono');
                    document.getElementById('correo').value = button.getAttribute('data-correo');
                    document.getElementById('direccion').value = button.getAttribute('data-direccion');
                    document.getElementById('iban').value = button.getAttribute('data-iban') || '';
                    
                    // Para la contraseña, mostramos un mensaje indicando que solo se cambiará si se introduce un valor
                    document.getElementById('clave').setAttribute('placeholder', 'Dejar en blanco para mantener la actual');
                    document.getElementById('clave').removeAttribute('required');
                    
                    // Mostramos/ocultamos campos según el rol
                    actualizarCamposPorRol(button.getAttribute('data-rol'));
                } else {
                    // Creación de nuevo usuario
                    document.getElementById('modalUsuarioLabel').textContent = 'Añadir Usuario';
                    document.getElementById('formUsuario').reset();
                    document.getElementById('id_usuario').value = '';
                    document.getElementById('clave').setAttribute('required', 'required');
                    document.getElementById('clave').setAttribute('placeholder', 'Contraseña');
                    
                    // Configurar valores predeterminados
                    document.getElementById('id_rol').value = '3'; // Cliente por defecto
                    actualizarCamposPorRol(3);
                }
            });
            
            // Función para mostrar/ocultar campos según el rol seleccionado
            function actualizarCamposPorRol(rol) {
                const campoNSS = document.querySelector('.col-md-6 label[for="nss"]').parentNode;
                
                if (rol == 1 || rol == 2) { // Admin o Empleado
                    campoNSS.style.display = 'block';
                } else { // Cliente
                    campoNSS.style.display = 'none';
                }
            }
            
            // Actualizar campos al cambiar el rol en el formulario
            // Nos servirá si hay algún usuario cliente que en un futuro se convierta en empleado o admin
            document.getElementById('id_rol').addEventListener('change', function() {
                actualizarCamposPorRol(this.value);
            });
        });
    </script>
</body>
</html>