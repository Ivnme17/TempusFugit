<?php
// Verificar si hay una sesión activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay productos en la cesta
if (!isset($_SESSION['cesta']) || empty($_SESSION['cesta'])) {
    header("Location: index.php");
    exit;
}

// Calcular el total del pedido
$total = 0;
if (isset($_SESSION['cesta'])) {
    foreach ($_SESSION['cesta'] as $id => $reloj) {
        $cantidad = isset($_SESSION['cantidad'][$id]) ? $_SESSION['cantidad'][$id] : 1;
        $total += $reloj['precio'] * $cantidad;
    }
}

// Procesar el pago
if (filter_input(INPUT_POST, "procesar_pago")) {
    $telefono = filter_input(INPUT_POST, "telefono", FILTER_SANITIZE_NUMBER_INT);
    
    if (strlen($telefono) == 9) {
        // Aquí iría la lógica para procesar el pago con Bizum
        // Por ahora solo simularemos que el pago fue exitoso
        
        // Generar un número de pedido
        $numeroPedido = "TF-" . date("Y") . "-" . sprintf("%03d", rand(1, 999));
        
        // Guardar el pedido en la base de datos (esto sería implementado según tu estructura)
        // Por ahora solo mostraremos un mensaje de éxito
        
        // Limpiar el carrito
        unset($_SESSION['cesta']);
        unset($_SESSION['cantidad']);
        
        // Redirigir a una página de confirmación
        $_SESSION['mensaje_exito'] = "Pago realizado con éxito. Tu número de pedido es: " . $numeroPedido;
        header("Location: confirmacion_pedido.php");
        exit;
    } else {
        $error = "El número de teléfono debe tener 9 dígitos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago con Bizum - Tempus Fugit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .container-bizum {
            max-width: 450px;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .bizum-header {
            background-color: #00a1e4;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .bizum-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .order-summary {
            background-color: #f9f9f9;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .summary-title {
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-weight: bold;
        }
        
        .bizum-info {
            background-color: #f0f9ff;
            border: 1px solid #d0e8f9;
            border-radius: 8px;
            padding: 15px;
            margin: 20px;
        }
        
        .bizum-info-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: #00a1e4;
        }
        
        .bizum-info-text {
            font-size: 14px;
            line-height: 1.5;
        }
        
        .form-container {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .pay-button {
            width: 100%;
            padding: 15px;
            background-color: #00a1e4;
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .pay-button:hover {
            background-color: #0089c3;
        }
        
        .secure-info {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #777;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin: 20px 0;
            color: #6c757d;
            text-decoration: none;
        }
        
        .back-link:hover {
            color: #5a6268;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <div id="header">
        <div id="logoEmpresa">
            <a href="index.php"><img src="../logoEmpresa/TEMPUS-removebg-preview.png" alt="Logo Tempus Fugit"></a>
        </div>
    </div>
    
    <div class="container-bizum">
        <div class="bizum-header">
            <div class="bizum-logo">BIZUM</div>
            <div>Finalizar compra</div>
        </div>
        
        <div class="order-summary">
            <div class="summary-title">Resumen del pedido</div>
            <?php foreach ($_SESSION['cesta'] as $id => $reloj): ?>
                <?php $cantidad = isset($_SESSION['cantidad'][$id]) ? $_SESSION['cantidad'][$id] : 1; ?>
                <div class="summary-row">
                    <span><?php echo htmlspecialchars($reloj['nombre']); ?> (x<?php echo $cantidad; ?>)</span>
                    <span><?php echo number_format($reloj['precio'] * $cantidad, 2); ?> €</span>
                </div>
            <?php endforeach; ?>
            
            <div class="summary-total">
                <span>Total</span>
                <span><?php echo number_format($total, 2); ?> €</span>
            </div>
        </div>
        
        <div class="bizum-info">
            <div class="bizum-info-title">¿Cómo funciona?</div>
            <div class="bizum-info-text">
                1. Introduce tu número de teléfono asociado a Bizum<br>
                2. Recibirás una notificación en tu app bancaria<br>
                3. Confirma el pago en tu aplicación bancaria
            </div>
        </div>
        
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="telefono">Número de teléfono</label>
                    <input type="tel" name="telefono" id="telefono" class="form-control" placeholder="Ej: 600123456" pattern="[0-9]{9}" maxlength="9" required>
                    <?php if (isset($error)): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" name="procesar_pago" class="pay-button">PAGAR <?php echo number_format($total, 2); ?> €</button>
                
                <div class="secure-info mt-3">
                    <i class="fa-solid fa-lock me-1"></i>
                    Pago seguro y encriptado
                </div>
            </form>
        </div>
        
        <a href="index.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Volver al carrito
        </a>
    </div>
</body>
</html>