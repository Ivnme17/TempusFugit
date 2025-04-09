<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago con Bizum</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 450px;
            margin: 0 auto;
            background-color: white;
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        
        .logo {
            max-width: 120px;
            margin: 0 auto 10px;
        }
        
        .checkout-title {
            font-size: 18px;
            color: #666;
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
        
        .form-container {
            padding: 20px;
        }
        
        .payment-methods {
            margin-bottom: 20px;
        }
        
        .payment-title {
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .payment-option {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .payment-option.selected {
            border-color: #00a1e4;
            background-color: #f0f9ff;
        }
        
        .payment-logo {
            width: 50px;
            margin-right: 15px;
        }
        
        .payment-info {
            flex-grow: 1;
        }
        
        .payment-name {
            font-weight: 500;
        }
        
        .payment-description {
            font-size: 12px;
            color: #777;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #666;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
        .bizum-info {
            background-color: #f0f9ff;
            border: 1px solid #d0e8f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
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
        
        .button-container {
            padding: 20px;
            position: sticky;
            bottom: 0;
            background-color: white;
            border-top: 1px solid #eee;
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
            display: flex;
            justify-content: center;
            align-items: center;
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
        
        .secure-info svg {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <svg height="40" viewBox="0 0 120 40" fill="none">
                    <rect width="120" height="40" rx="8" fill="#00A1E4"/>
                    <text x="25" y="26" font-family="Arial" font-size="20" font-weight="bold" fill="white">BIZUM</text>
                </svg>
            </div>
            <div class="checkout-title">Finalizar compra</div>
        </header>
        
        <div class="order-summary">
            <div class="summary-title">Resumen del pedido</div>
            <div class="summary-row">
                <span>Auriculares inalámbricos</span>
                <span>59,99 €</span>
            </div>
            <div class="summary-row">
                <span>Envío</span>
                <span>4,95 €</span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span>64,94 €</span>
            </div>
        </div>
        
        <div class="form-container">
            <div class="payment-methods">
                <div class="payment-title">Selecciona método de pago</div>
                
                <div class="payment-option selected">
                    <div class="payment-logo">
                        <svg height="30" viewBox="0 0 60 30" fill="none">
                            <rect width="60" height="30" rx="4" fill="#00A1E4"/>
                            <text x="12" y="19" font-family="Arial" font-size="10" font-weight="bold" fill="white">BIZUM</text>
                        </svg>
                    </div>
                    <div class="payment-info">
                        <div class="payment-name">Bizum</div>
                        <div class="payment-description">Pago rápido desde tu móvil</div>
                    </div>
                </div>
                
                <div class="payment-option">
                    <div class="payment-logo">
                        <svg height="30" viewBox="0 0 60 30" fill="none">
                            <rect width="60" height="30" rx="4" fill="#F1F1F1"/>
                            <text x="8" y="19" font-family="Arial" font-size="10" fill="#333">TARJETA</text>
                        </svg>
                    </div>
                    <div class="payment-info">
                        <div class="payment-name">Tarjeta de crédito/débito</div>
                        <div class="payment-description">Visa, Mastercard, American Express</div>
                    </div>
                </div>
                
                <div class="payment-option">
                    <div class="payment-logo">
                        <svg height="30" viewBox="0 0 60 30" fill="none">
                            <rect width="60" height="30" rx="4" fill="#F1F1F1"/>
                            <text x="8" y="19" font-family="Arial" font-size="10" fill="#333">PAYPAL</text>
                        </svg>
                    </div>
                    <div class="payment-info">
                        <div class="payment-name">PayPal</div>
                        <div class="payment-description">Paga de forma segura con tu cuenta</div>
                    </div>
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
            
            <div class="form-group">
                <label for="phone">Número de teléfono</label>
                <input type="tel" id="phone" placeholder="Ej: 600123456" pattern="[0-9]{9}" maxlength="9">
            </div>
        </div>
        
        <div class="button-container">
            <button class="pay-button">PAGAR 64,94 €</button>
            <div class="secure-info">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M9.5,4H8.5V3a2.5,2.5,0,0,0-5,0V4H2.5A1.5,1.5,0,0,0,1,5.5v4A1.5,1.5,0,0,0,2.5,11h7A1.5,1.5,0,0,0,11,9.5v-4A1.5,1.5,0,0,0,9.5,4ZM5,3a1.5,1.5,0,0,1,3,0V4H5Z" fill="#777"/>
                </svg>
                Pago seguro y encriptado
            </div>
        </div>
    </div>
</body>
</html>