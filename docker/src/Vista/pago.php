<?php
require_once '../Modelo/Pedidos.php';
require_once '../Modelo/Usuario.php';
require_once '../Modelo/Reloj.php';
session_start();
if(!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}else {
    $login = $_SESSION['usuario'];
    $usuario = Usuario::verUsuario($login);
    $id = $usuario->getId_usuario();
    $cesta = $_SESSION['cesta'];
    $nombreReloj = $cesta[5]['nombre'];
    $reloj = Reloj::obtenerPorId($nombreReloj);  
    $cantidad = $_SESSION['cantidad'][5];
}
//Informo al usuario de que se ha realizado el pago y después de 3 segundos lo redirijo a la vistaCliente.php
if(isset($_POST['pagar'])) {
    header("refresh:30;url=vistaCliente.php");
    //echo "<dialog open style='padding: 30px; border-radius: 20px; border: none; box-shadow: 0 0 20px rgba(0,0,0,0.3); background-color:#00205B;'>
        //    <p style='color: white; font-size: 18px; margin: 0;'>Pago realizado correctamente!!</p>
      //    </dialog>";
        $cesta = $_SESSION['cesta'];
        foreach($cesta as $producto) {
            $pedido = new Pedidos(
                $id, 
                $reloj,
                date('Y-m-d H:i:s'),
                $cantidad,
                $producto['precio'],
                $producto['precio'] * $cantidad,
                'Bizum'
            );
            $pedido->insertarPedido();
        }
          exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bizum</title>
    <link rel="stylesheet" href="../css/estilospago.css">
</head>
<bod>    
    <form method="POST">
    <img src="../imagenBackground/logo-vector-bizum.jpg" alt="Bizum" class="logo" style="width: 390px; height:auto;">
        <h1>Pago</h1>
        <class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <div style="display: flex; align-items: center; gap: 10px;">
            <img src="../logoEmpresa/logoBizumMovil.png" style="width: 30px; height: auto;">
            <input type="tel" class="form-control" id="telefono" name="telefono" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" name="pagar">Realizar Pago</button>
        <button type="reset" class="btn btn-secondary">Limpiar</button>
    </form>
</body>
</html>



