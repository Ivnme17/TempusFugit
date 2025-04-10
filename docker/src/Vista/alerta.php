<?php
if (isset($mensaje)) {
?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Alerta</title>
        <style>
            .alert {
                padding: 20px;
                background-color: #4CAF50;
                color: white;
                margin-bottom: 15px;
                border-radius: 4px;
                text-align: center;
            }
            .alert-container {
                width: 100%;
                max-width: 500px;
                margin: 20px auto;
            }
            .close-btn {
                background-color: white;
                color: #4CAF50;
                border: none;
                padding: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <div class="alert-container">
            <div class="alert">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
            <div style="text-align: center;">
                <button class="close-btn" onclick="window.history.back()">Aceptar</button>
            </div>
        </div>
    </body>
    </html>
<?php
} else {
    header("Location: ../index.php");
    exit();
}?>
