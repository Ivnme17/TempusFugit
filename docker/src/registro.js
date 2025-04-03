document.addEventListener('DOMContentLoaded', function() {
    const formularioRegistro = document.getElementById('formularioRegistro');
    
    if (formularioRegistro) {
        formularioRegistro.addEventListener('submit', validarFormulario);
    }
    
    function validarFormulario(event) {
        event.preventDefault();
        
        limpiarErrores();
        
        const loginUsuario = document.getElementById('loginUsuario').value.trim();
        const claveUsuario = document.getElementById('claveUsuario').value;
        const confirmarClave = document.getElementById('confirmarClave').value;
        const nombre = document.getElementById('nombre').value.trim();
        const email = document.getElementById('email').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        const direccion = document.getElementById('direccion').value.trim();
        
        let esValido = true;
        
        // Validar nombre de usuario
        if (!loginUsuario) {
            mostrarError('loginUsuario', 'El nombre de usuario es obligatorio');
            esValido = false;
        } else if (loginUsuario.length > 50) {
            mostrarError('loginUsuario', 'El nombre de usuario no puede exceder los 50 caracteres');
            esValido = false;
        } else if (!/^[a-zA-Z0-9._]+$/.test(loginUsuario)) {
            mostrarError('loginUsuario', 'El nombre de usuario solo puede contener letras, números, puntos y guiones bajos');
            esValido = false;
        }
        
        // Validar contraseña
        if (!claveUsuario) {
            mostrarError('claveUsuario', 'La contraseña es obligatoria');
            esValido = false;
        } else if (claveUsuario.length < 8) {
            mostrarError('claveUsuario', 'La contraseña debe tener al menos 8 caracteres');
            esValido = false;
        } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(claveUsuario)) {
            mostrarError('claveUsuario', 'La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial');
            esValido = false;
        }
        
        // Validar confirmación de contraseña
        if (claveUsuario !== confirmarClave) {
            mostrarError('confirmarClave', 'Las contraseñas no coinciden');
            esValido = false;
        }
        
        // Validar nombre
        if (!nombre) {
            mostrarError('nombre', 'El nombre es obligatorio');
            esValido = false;
        } else if (nombre.length > 100) {
            mostrarError('nombre', 'El nombre no puede exceder los 100 caracteres');
            esValido = false;
        }
        
        // Validar email
        if (!email) {
            mostrarError('email', 'El correo electrónico es obligatorio');
            esValido = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            mostrarError('email', 'El correo electrónico no es válido');
            esValido = false;
        }
        
        // Validar teléfono (opcional)
        if (telefono && !/^\d{9,15}$/.test(telefono)) {
            mostrarError('telefono', 'El teléfono debe tener entre 9 y 15 dígitos');
            esValido = false;
        }
        
        // Validar dirección (opcional)
        if (direccion && direccion.length > 255) {
            mostrarError('direccion', 'La dirección no puede exceder los 255 caracteres');
            esValido = false;
        }
        
        if (esValido) {
            document.getElementById('claveHash').value = hashSHA512(claveUsuario);
            formularioRegistro.submit();
        }
    }
    
    // Mantener las funciones auxiliares existentes (mostrarError, limpiarErrores, verificarLoginUnico, hashSHA512)
});
