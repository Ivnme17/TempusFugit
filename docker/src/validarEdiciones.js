document.addEventListener('DOMContentLoaded', function() {
    // Validación para formularios de edición de clientes
    const clienteForms = document.querySelectorAll('form[action][name="action"][value="editarCliente"]');
    clienteForms.forEach(form => {
        form.addEventListener('submit', validateClienteForm);
    });

    // Validación para formularios de edición de relojes
    const relojForms = document.querySelectorAll('form[action][name="action"][value="editarReloj"]');
    relojForms.forEach(form => {
        form.addEventListener('submit', validateRelojForm);
    });

    // Validación para formularios de edición de pedidos
    const pedidoForms = document.querySelectorAll('form[action][name="action"][value="editarPedido"]');
    pedidoForms.forEach(form => {
        form.addEventListener('submit', validatePedidoForm);
    });

    function validateClienteForm(e) {
        const form = e.target;
        let isValid = true;
        let errorMsg = '';

        // Validar login (no vacío y longitud entre 3-50)
        const login = form.querySelector('[name="login"]').value.trim();
        if (!login || login.length < 3 || login.length > 50) {
            errorMsg += 'El login debe tener entre 3 y 50 caracteres.\n';
            isValid = false;
        }

        // Validar correo
        const correo = form.querySelector('[name="correo"]').value.trim();
        if (correo && !validateEmail(correo)) {
            errorMsg += 'El formato del correo electrónico no es válido.\n';
            isValid = false;
        }

        // Validar dirección (si no está vacía, longitud máxima 255)
        const direccion = form.querySelector('[name="direccion"]').value.trim();
        if (direccion && direccion.length > 255) {
            errorMsg += 'La dirección no puede exceder los 255 caracteres.\n';
            isValid = false;
        }

        // Validar IBAN (si no está vacío, debe tener formato correcto)
        const iban = form.querySelector('[name="iban"]').value.trim();
        if (iban && !validateIBAN(iban)) {
            errorMsg += 'El formato del IBAN no es válido.\n';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Por favor, corrija los siguientes errores:\n' + errorMsg);
        }
    }

    // Función para validar formularios de relojes
    function validateRelojForm(e) {
        const form = e.target;
        let isValid = true;
        let errorMsg = '';

        // Validar ID marca/modelo (debe ser un número entero positivo)
        const idMarcaModelo = form.querySelector('[name="id_marca_modelo"]').value.trim();
        if (!idMarcaModelo || !Number.isInteger(Number(idMarcaModelo)) || Number(idMarcaModelo) <= 0) {
            errorMsg += 'El ID de marca/modelo debe ser un número entero positivo.\n';
            isValid = false;
        }

        // Validar precio (debe ser un número positivo con hasta 2 decimales)
        const precio = form.querySelector('[name="precio"]').value.trim();
        if (!precio || !validateDecimal(precio) || parseFloat(precio) <= 0) {
            errorMsg += 'El precio debe ser un número positivo con hasta 2 decimales.\n';
            isValid = false;
        }

        // Validar tipo (debe ser "digital" o "analógico")
        const tipo = form.querySelector('[name="tipo"]').value.trim();
        if (!tipo || (tipo !== 'digital' && tipo !== 'analógico')) {
            errorMsg += 'El tipo debe ser "digital" o "analógico".\n';
            isValid = false;
        }

        // Validar stock (debe ser un número entero no negativo)
        const stock = form.querySelector('[name="stock"]').value.trim();
        if (!stock || !Number.isInteger(Number(stock)) || Number(stock) < 0) {
            errorMsg += 'El stock debe ser un número entero no negativo.\n';
            isValid = false;
        }

        // Validar URL de imagen (si no está vacía, debe tener formato URL)
        const urlImagen = form.querySelector('[name="url_imagen"]').value.trim();
        if (urlImagen && !validateURL(urlImagen)) {
            errorMsg += 'El formato de la URL de imagen no es válido.\n';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Por favor, corrija los siguientes errores:\n' + errorMsg);
        }
    }

    // Función para validar formularios de pedidos
    function validatePedidoForm(e) {
        const form = e.target;
        let isValid = true;
        let errorMsg = '';

        // Validar fecha (debe ser una fecha válida no futura)
        const fechaPedido = form.querySelector('[name="fecha_pedido"]').value;
        const fechaActual = new Date().toISOString().split('T')[0];
        if (!fechaPedido || fechaPedido > fechaActual) {
            errorMsg += 'La fecha del pedido no puede ser futura.\n';
            isValid = false;
        }

        // Validar precio final (debe ser un número positivo con hasta 2 decimales)
        const precioFinal = form.querySelector('[name="precio_final"]').value.trim();
        if (!precioFinal || !validateDecimal(precioFinal) || parseFloat(precioFinal) <= 0) {
            errorMsg += 'El precio final debe ser un número positivo con hasta 2 decimales.\n';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Por favor, corrija los siguientes errores:\n' + errorMsg);
        }
    }

    // Función auxiliar para validar email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Función auxiliar para validar IBAN (simplificada)
    function validateIBAN(iban) {
        // Expresión regular básica para IBAN: 2 letras seguidas de hasta 32 caracteres alfanuméricos
        const re = /^[A-Z]{2}[0-9A-Z]{10,32}$/;
        return re.test(iban.replace(/\s/g, '').toUpperCase());
    }

    // Función auxiliar para validar decimales (hasta 2 decimales)
    function validateDecimal(value) {
        const re = /^\d+(\.\d{1,2})?$/;
        return re.test(value);
    }

    // Función auxiliar para validar URL
    function validateURL(url) {
        try {
            new URL(url);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Añadir indicadores visuales para campos obligatorios
    const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
    requiredFields.forEach(field => {
        const label = field.previousElementSibling;
        if (label && label.classList.contains('form-label')) {
            label.innerHTML += ' <span class="text-danger">*</span>';
        }
    });

    // Agregar validación en tiempo real para campos importantes
    const allInputs = document.querySelectorAll('.form-control');
    allInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });

    function validateField(field) {
        const fieldName = field.name;
        const value = field.value.trim();
        let errorMsg = '';
        let isValid = true;

        // Validación específica según el tipo de campo
        switch(fieldName) {
            case 'login':
                if (!value || value.length < 3 || value.length > 50) {
                    errorMsg = 'Debe tener entre 3 y 50 caracteres';
                    isValid = false;
                }
                break;
            case 'correo':
                if (value && !validateEmail(value)) {
                    errorMsg = 'Formato de email no válido';
                    isValid = false;
                }
                break;
            case 'iban':
                if (value && !validateIBAN(value)) {
                    errorMsg = 'Formato de IBAN no válido';
                    isValid = false;
                }
                break;
            case 'precio':
            case 'precio_final':
                if (!validateDecimal(value) || parseFloat(value) <= 0) {
                    errorMsg = 'Debe ser un número positivo con hasta 2 decimales';
                    isValid = false;
                }
                break;
            case 'stock':
                if (!Number.isInteger(Number(value)) || Number(value) < 0) {
                    errorMsg = 'Debe ser un número entero no negativo';
                    isValid = false;
                }
                break;
            case 'url_imagen':
                if (value && !validateURL(value)) {
                    errorMsg = 'URL no válida';
                    isValid = false;
                }
                break;
        }

        // Mostrar feedback visual
        if (!isValid) {
            field.classList.add('is-invalid');
            
            // Crear o actualizar mensaje de error
            let feedbackDiv = field.nextElementSibling;
            if (!feedbackDiv || !feedbackDiv.classList.contains('invalid-feedback')) {
                feedbackDiv = document.createElement('div');
                feedbackDiv.className = 'invalid-feedback';
                field.parentNode.insertBefore(feedbackDiv, field.nextSibling);
            }
            feedbackDiv.textContent = errorMsg;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            
            // Eliminar mensaje de error si existe
            const feedbackDiv = field.nextElementSibling;
            if (feedbackDiv && feedbackDiv.classList.contains('invalid-feedback')) {
                feedbackDiv.remove();
            }
        }
    }
});