document.addEventListener('DOMContentLoaded', function() {
    // Validación para formularios de edición de clientes
    const clienteForms = document.querySelectorAll('form[action][name="action"][value="editarCliente"]');
    clienteForms.forEach(form => {
        form.addEventListener('submit', validarClienteForm);
    });

    // Validación para formularios de edición de relojes
    const relojForms = document.querySelectorAll('form[action][name="action"][value="editarReloj"]');
    relojForms.forEach(form => {
        form.addEventListener('submit', validarRelojForm);
    });

    // Validación para formularios de edición de pedidos
    const pedidoForms = document.querySelectorAll('form[action][name="action"][value="editarPedido"]');
    pedidoForms.forEach(form => {
        form.addEventListener('submit', validarPedidoForm);
    });

    function validarClienteForm(e) {
        const form = e.target;
        let esValido = true;
        let mensajeError = '';

        // Validar login (no vacío y longitud entre 3-50)
        const login = form.querySelector('[name="login"]').value.trim();
        if (!login || login.length < 3 || login.length > 50) {
            mensajeError += 'El login debe tener entre 3 y 50 caracteres.\n';
            esValido = false;
        }

        // Validar correo
        const correo = form.querySelector('[name="correo"]').value.trim();
        if (correo && !validarEmail(correo)) {
            mensajeError += 'El formato del correo electrónico no es válido.\n';
            esValido = false;
        }

        // Validar dirección (si no está vacía, longitud máxima 255)
        const direccion = form.querySelector('[name="direccion"]').value.trim();
        if (direccion && direccion.length > 255) {
            mensajeError += 'La dirección no puede exceder los 255 caracteres.\n';
            esValido = false;
        }

        // Validar IBAN (si no está vacío, debe tener formato correcto)
        const iban = form.querySelector('[name="iban"]').value.trim();
        if (iban && !validarIBAN(iban)) {
            mensajeError += 'El formato del IBAN no es válido.\n';
            esValido = false;
        }

        if (!esValido) {
            e.preventDefault();
            alert('Por favor, corrija los siguientes errores:\n' + mensajeError);
        }
    }

    // Función para validar formularios de relojes
    function validarRelojForm(e) {
        const form = e.target;
        let esValido = true;
        let mensajeError = '';

        // Validar ID marca/modelo (debe ser un número entero positivo)
        const idMarcaModelo = form.querySelector('[name="id_marca_modelo"]').value.trim();
        if (!idMarcaModelo || !Number.isInteger(Number(idMarcaModelo)) || Number(idMarcaModelo) <= 0) {
            mensajeError += 'El ID de marca/modelo debe ser un número entero positivo.\n';
            esValido = false;
        }

        // Validar precio (debe ser un número positivo con hasta 2 decimales)
        const precio = form.querySelector('[name="precio"]').value.trim();
        if (!precio || !validarDecimal(precio) || parseFloat(precio) <= 0) {
            mensajeError += 'El precio debe ser un número positivo con hasta 2 decimales.\n';
            esValido = false;
        }

        // Validar tipo (debe ser "digital" o "analógico")
        const tipo = form.querySelector('[name="tipo"]').value.trim();
        if (!tipo || (tipo !== 'digital' && tipo !== 'analógico')) {
            mensajeError += 'El tipo debe ser "digital" o "analógico".\n';
            esValido = false;
        }

        // Validar stock (debe ser un número entero no negativo)
        const stock = form.querySelector('[name="stock"]').value.trim();
        if (!stock || !Number.isInteger(Number(stock)) || Number(stock) < 0) {
            mensajeError += 'El stock debe ser un número entero no negativo.\n';
            esValido = false;
        }

        // Validar URL de imagen (si no está vacía, debe tener formato URL)
        const urlImagen = form.querySelector('[name="url_imagen"]').value.trim();
        if (urlImagen && !validarURL(urlImagen)) {
            mensajeError += 'El formato de la URL de imagen no es válido.\n';
            esValido = false;
        }

        if (!esValido) {
            e.preventDefault();
            alert('Por favor, corrija los siguientes errores:\n' + mensajeError);
        }
    }

    // Función para validar formularios de pedidos
    function validarPedidoForm(e) {
        const form = e.target;
        let esValido = true;
        let mensajeError = '';

        // Validar fecha (debe ser una fecha válida no futura)
        const fechaPedido = form.querySelector('[name="fecha_pedido"]').value;
        const fechaActual = new Date().toISOString().split('T')[0];
        if (!fechaPedido || fechaPedido > fechaActual) {
            mensajeError += 'La fecha del pedido no puede ser futura.\n';
            esValido = false;
        }

        // Validar precio final (debe ser un número positivo con hasta 2 decimales)
        const precioFinal = form.querySelector('[name="precio_final"]').value.trim();
        if (!precioFinal || !validarDecimal(precioFinal) || parseFloat(precioFinal) <= 0) {
            mensajeError += 'El precio final debe ser un número positivo con hasta 2 decimales.\n';
            esValido = false;
        }

        if (!esValido) {
            e.preventDefault();
            alert('Por favor, corrija los siguientes errores:\n' + mensajeError);
        }
    }

    function validarEmail(email) {
        const formatoEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return formatoEmail.test(email);
    }

    // Función auxiliar para validar IBAN (simplificada)
    function validarIBAN(iban) {
        // Expresión regular básica para IBAN: 2 letras seguidas de hasta 32 caracteres alfanuméricos
        const patronIBAN = /^[A-Z]{2}[0-9A-Z]{10,32}$/;
        return patronIBAN.test(iban.replace(/\s/g, '').toUpperCase());
    }

    // Función auxiliar para validar decimales (hasta 2 decimales)
    function validarDecimal(value) {
        const patron = /^\d+(\.\d{1,2})?$/;
        return patron.test(value);
    }

    // Función auxiliar para validar URL
    function validarURL(url) {
        try {
            new URL(url);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Indica visualmente que los campos obligatorios
    const camposObligatorios = document.querySelectorAll('input[required], select[required], textarea[required]');
    camposObligatorios.forEach(field => {
        const label = field.previousElementSibling;
        if (label && label.classList.contains('form-label')) {
            label.innerHTML += ' <span class="text-danger">*</span>';
        }
    });

    // Validación en tiempo real para campos importantes
    const camposDeFormulario = document.querySelectorAll('.form-control');
    camposDeFormulario.forEach(input => {
        input.addEventListener('blur', function() {
            validarCampo(this);
        });
    });

    function validarCampo(campo) {
        const nombre = campo.name;
        const valor = campo.value.trim();
        let mensajeError = '';
        let esValido = true;

        // Validación específica según el tipo de campo
        switch(nombre) {
            case 'login':
                if (!valor || valor.length < 3 || valor.length > 50) {
                    mensajeError = 'Debe tener entre 3 y 50 caracteres';
                    esValido = false;
                }
                break;
            case 'correo':
                if (valor && !validarEmail(valor)) {
                    mensajeError = 'Formato de email no válido';
                    esValido = false;
                }
                break;
            case 'iban':
                if (valor && !validarIBAN(valor)) {
                    mensajeError = 'Formato de IBAN no válido';
                    esValido = false;
                }
                break;
            case 'precio':
            case 'precio_final':
                if (!validarDecimal(valor) || parseFloat(valor) <= 0) {
                    mensajeError = 'Debe ser un número positivo con hasta 2 decimales';
                    esValido = false;
                }
                break;
            case 'stock':
                if (!Number.isInteger(Number(valor)) || Number(valor) < 0) {
                    mensajeError = 'Debe ser un número entero no negativo';
                    esValido = false;
                }
                break;
            case 'url_imagen':
                if (valor && !validarURL(valor)) {
                    mensajeError = 'URL no válida';
                    esValido = false;
                }
                break;
        }

        // Mostrar feedback visual
        if (!esValido) {
            campo.classList.add('is-invalid');
            
            // Crear o actualizar mensaje de error
            let feedbackDiv = campo.nextElementSibling;
            if (!feedbackDiv || !feedbackDiv.classList.contains('invalid-feedback')) {
                feedbackDiv = document.createElement('div');
                feedbackDiv.className = 'invalid-feedback';
                campo.parentNode.insertBefore(feedbackDiv, campo.nextSibling);
            }
            feedbackDiv.textContent = mensajeError;
        } else {
            campo.classList.remove('is-invalid');
            campo.classList.add('is-valid');
            
            // Eliminar mensaje de error si existe
            const feedbackDiv = campo.nextElementSibling;
            if (feedbackDiv && feedbackDiv.classList.contains('invalid-feedback')) {
                feedbackDiv.remove();
            }
        }
    }
});