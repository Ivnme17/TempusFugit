<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Tempus Fugit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
form {
    background-color: rgba(255, 215, 0, 0.1); 
    padding: 30px; 
    border-radius: 10px;
    max-width: 500px; 
    margin: 20px auto; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

form h2 {
    color: #4E3B31;
    margin-bottom: 20px;
    text-align: center;
}

input[type="text"], 
input[type="email"], 
input[type="tel"], 
input[type="password"], 
textarea {
    width: 100%;
    padding: 12px 15px;
    margin: 10px 0 15px 0;
    border: 2px solid #FFD700;
    border-radius: 8px;
    background-color: #fff; 
    color: #333;
    font-size: 16px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

input[type="text"]:focus, 
input[type="email"]:focus, 
input[type="tel"]:focus, 
input[type="password"]:focus, 
textarea:focus {
    outline: none;
    border-color: #FFC300;
    box-shadow: 0 0 8px rgba(255, 215, 0, 0.3);
}

textarea {
    resize: vertical;
    min-height: 100px;
}

form label {
    display: block;
    margin-bottom: 5px;
    color: #FFD700; 
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2); 
}

input[type="submit"], 
input[type="button"] {
    width: 48%;
    margin: 10px 1%;
    padding: 12px 20px;
    background-color: #FFD700; 
    color: #4E3B31; 
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.2s;
}

input[type="submit"]:hover, 
input[type="button"]:hover {
    background-color: #FFC300;
    transform: scale(1.05);
}

input[type="submit"] {
    background-color: #FFD700;
}

input[type="button"] {
    background-color: #E0E0E0;
    color: #4E3B31;
}
#formulario h2{
color: #FFD700;
}
.incorrecto {
    border-color: #dc3545 !important;
    background-color:rgb(249, 173, 173) !important;
}
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="../registro.js"></script>

    <nav class="navbar navbar-expand-xl navbar-light" style="background-color: transparent;">
      <div class="container-fluid">
        <div class="collapse navbar-collapse show" id="navbarLight">
          <ul class="navbar-nav me-auto mb-2 mb-xl-0">
            <li class="nav-item">
              <a class="nav-link" href="login.html">¿Estás dado de alta?</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div id="header">
        <div id="logoEmpresa">
            <a href="../index.html"><img src="../logoEmpresa/TEMPUS-removebg-preview.png" alt="Logo de Tempus Fugit"></a>
        </div>
    </div>

    <div id="formulario">
        <form action="../registro_proceso.php" method="POST">
            <h2>Registro de Usuario</h2>
                   
            <label for="loginUsuario">Nombre de Usuario:</label>
            <input type="text" id="loginUsuario" name="loginUsuario" placeholder="Juan.Garcia" required 
            class="<?php echo isset($_SESSION['camposInvalidos']['loginUsuario']) ? 'incorrecto' : ''; ?>">

            <label for="nombre">Nombre </label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan" required
            class="<?php echo isset($_SESSION['camposInvalidos']['nombre']) ? 'incorrecto' : ''; ?>">

            <label for="apellidos">Apellidos </label>
            <input type="text" id="apellido" name="apellido" placeholder="Ej: García Pérez" required
            class="<?php echo isset($_SESSION['camposInvalidos']['apellido']) ? 'incorrecto' : ''; ?>">
            
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required
            class="<?php echo isset($_SESSION['camposInvalidos']['email']) ? 'incorrecto' : ''; ?>">
            
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" placeholder="Ej: 612345678" required
            class="<?php echo isset($_SESSION['camposInvalidos']['telefono']) ? 'incorrecto' : ''; ?>">
            
            <label for="claveUsuario">Contraseña:</label>
            <div style="position: relative;"></div>
            <input type="password" id="claveUsuario" name="claveUsuario" placeholder="Longitud mínima 8" required
                class="<?php echo isset($_SESSION['camposInvalidos']['claveUsuario']) ? 'incorrecto' : ''; ?>">
            <button type="button" onclick="alternarContrasena('claveUsuario')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none;">
                <img src="../botones/ojo-abierto.png" id="claveUsuario-icono" alt="Mostrar/Ocultar contraseña" style="width: 20px; height: 20px;">
            </button>
            <label for="confirmarClave">Confirmar Contraseña:</label>
            <div style="position: relative;">
            <input type="password" id="confirmarClave" name="confirmarClave" required>
            <button type="button" onclick="alternarContrasena('confirmarClave')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none;">
                <img src="../botones/ojo-abierto.png" id="confirmarClave-icono" alt="Mostrar/Ocultar contraseña" style="width: 20px; height: 20px;">
            </button>
            </div>
            
            <label for="iban">IBAN:</label>
            <select id="iban" name="iban" class="form-control <?php echo isset($_SESSION['camposInvalidos']['iban']) ? 'incorrecto' : ''; ?>">
            <option value="">Seleccione un IBAN</option>
            <option value="ES6112343456420456323532">España (ES)</option>
            <option value="PT50000201231234567890154">Portugal (PT)</option>
            </select>

            <label for="direccion">Dirección:</label>
            <textarea id="direccion" name="direccion" placeholder="Cmno de la Luna 25, Almeria, España"
            class="<?php echo isset($_SESSION['camposInvalidos']['direccion']) ? 'incorrecto' : ''; ?>"></textarea>
            
            <button type="submit" name="registrar">Registrar</button>
            <input type="button" value="Cancelar" onclick="location.href='../index.html'">
        </form>
    </div>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($mensajeError); ?>
    </div>
    <div id="pieDePagina">
        <footer>
            <pre>Iván Martínez Estrada - 2ºDAW</pre>
        </footer>
    </div>
</body>
</html></footer>