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
    
    if(!isset($_SESSION['cesta']) || empty($_SESSION['cesta'])) {
        header("Location: vistaCliente.php");
        exit();
    }
    
    $cesta = $_SESSION['cesta'];
    // Asegurarse de que la cesta tenga elementos
    $nombreReloj = isset($cesta[5]['nombre']) ? $cesta[5]['nombre'] : (isset($cesta[0]['nombre']) ? $cesta[0]['nombre'] : '');
    $reloj = Reloj::obtenerPorNombre($nombreReloj);
    $cantidad = isset($_SESSION['cantidad'][5]) ? $_SESSION['cantidad'][5] : 1;
}

// Informo al usuario de que se ha realizado el pago y después de 3 segundos lo redirijo a la vistaCliente.php
if(isset($_POST['pagar'])) {
    header("refresh:3;url=vistaCliente.php");
    echo "<dialog open style='padding: 30px; border-radius: 20px; border: none; box-shadow: 0 0 20px rgba(0,0,0,0.3); background-color:#00205B;'>
           <p style='color: white; font-size: 18px; margin: 0;'>Pago realizado correctamente!!</p>
           <pre style=color:white>redirigiendo...</pre>
        </dialog>";
    
    $cesta = $_SESSION['cesta'];
    foreach($cesta as $index => $producto) {
        $relojActual = Reloj::obtenerPorNombre($producto['nombre']);
        
        $cantidadActual = isset($_SESSION['cantidad'][$index]) ? $_SESSION['cantidad'][$index] : 1;
        
        $pedido = new Pedidos(
            $id, 
            $relojActual->getId(),
            (new DateTime())->setTimezone(new DateTimeZone('Europe/Madrid'))->format('Y-m-d H:i:s'),
            $cantidadActual,
            $producto['precio'],
            'BIZUM' 
        );
        
        $idPedido = $pedido->insertarPedido();
        
        if($idPedido) {
            $precio_base = $producto['precio'] * $cantidadActual;
            $descuento_porcentaje = isset($producto['descuento']) ? $producto['descuento'] : 0.00;
            
            $detalles = [
                'id_pedido' => $idPedido,
                'id_usuario' => $id,
                'precio_base' => $precio_base,
                'descuento_porcentaje' => $descuento_porcentaje,
                'impuesto_porcentaje' => 21.00, // IVA estándar
                'fecha_actualizacion' => (new DateTime())->setTimezone(new DateTimeZone('Europe/Madrid'))->format('Y-m-d H:i:s'),
                'notas' => 'Pedido de ' . $producto['nombre']
            ];
            
            $idPedido = $pedido->insertarPedido();
            if ($idPedido) {
                $detalles = [
                    'id_usuario' => $id,
                    'precio_base' => $precio_base,
                    'descuento_porcentaje' => $descuento_porcentaje,
                    'impuesto_porcentaje' => 21.00,
                    'fecha_actualizacion' => (new DateTime())->setTimezone(new DateTimeZone('Europe/Madrid'))->format('Y-m-d H:i:s'),
                    'notas' => 'Pedido de ' . $producto['nombre']
                ];
                
                $pedido->insertarDetallesPedido($detalles);
            }
        }
    }
    
    
    unset($_SESSION['cesta']);
    unset($_SESSION['cantidad']);
    
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
<body>    
    <form method="POST">
    <img src="../imagenBackground/logo-vector-bizum.jpg" alt="Bizum" class="logo" style="width: 390px; height:auto;">
        <h1>Pago</h1>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <div style="display: flex; align-items: center; gap: 10px;">
            <img src="../logoEmpresa/logoBizumMovil.png" style="width: 30px; height: auto;">
            <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{9}" maxlength="9" title="Debe ingresar un número de teléfono válido de 9 dígitos" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" name="pagar">Realizar Pago</button>
        <button type="reset" class="btn btn-secondary">Limpiar</button>
    </form>
</body>
</html>