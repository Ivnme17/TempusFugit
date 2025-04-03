/**
 * Script de validación para el formulario de registro de usuarios
 * Basado en la estructura de la base de datos
 */

document.addEventListener('DOMContentLoaded', function() {
    const formularioRegistro = document.getElementById('formularioRegistro');
    
    if (formularioRegistro) {
        formularioRegistro.addEventListener('submit', validarFormulario);
    }
    
    function validarFormulario(event) {
        event.preventDefault();
        
        limpiarErrores();
        
        const login = document.getElementById('login').value.trim();
        const clave = document.getElementById('clave').value;
        const confirmarClave = document.getElementById('confirmarClave').value;
        const rol = document.getElementById('rol').value;
        const nombre = document.getElementById('nombre').value.trim();
        const apellidos = document.getElementById('apellidos').value.trim();
        
        let camposAdicionales = {};
        
        if (rol == 3) { // Cliente
            camposAdicionales = {
                telefono: document.getElementById('telefono').value.trim(),
                correo: document.getElementById('correo').value.trim(),
                direccion: document.getElementById('direccion').value.trim(),
                iban: document.getElementById('iban').value.trim()
            };
        } else if (rol == 2) { // Empleado
            camposAdicionales = {
                dni: document.getElementById('dni').value.trim(),
                nss: document.getElementById('nss').value.trim()
            };
        }
        
        let esValido = true;
        
        if (!login) {
            mostrarError('login', 'El nombre de usuario es obligatorio');
            esValido = false;
        } else if (login.length > 50) {
            mostrarError('login', 'El nombre de usuario no puede exceder los 50 caracteres');
            esValido = false;
        } else if (!/^[a-zA-Z0-9._]+$/.test(login)) {
            mostrarError('login', 'El nombre de usuario solo puede contener letras, números, puntos y guiones bajos');
            esValido = false;
        } else {
            verificarLoginUnico(login).then(esUnico => {
                if (!esUnico) {
                    mostrarError('login', 'Este nombre de usuario ya está en uso');
                    esValido = false;
                }
            });
        }
        
        if (!clave) {
            mostrarError('clave', 'La contraseña es obligatoria');
            esValido = false;
        } else if (clave.length < 8) {
            mostrarError('clave', 'La contraseña debe tener al menos 8 caracteres');
            esValido = false;
        } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(clave)) {
            mostrarError('clave', 'La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial');
            esValido = false;
        }
        
        if (clave !== confirmarClave) {
            mostrarError('confirmarClave', 'Las contraseñas no coinciden');
            esValido = false;
        }
        
        if (!nombre) {
            mostrarError('nombre', 'El nombre es obligatorio');
            esValido = false;
        } else if (nombre.length > 50) {
            mostrarError('nombre', 'El nombre no puede exceder los 50 caracteres');
            esValido = false;
        }
        
        if (!apellidos) {
            mostrarError('apellidos', 'Los apellidos son obligatorios');
            esValido = false;
        } else if (apellidos.length > 100) {
            mostrarError('apellidos', 'Los apellidos no pueden exceder los 100 caracteres');
            esValido = false;
        }
        
        if (rol == 3) { // Cliente
            if (camposAdicionales.telefono && !/^\d{9,15}$/.test(camposAdicionales.telefono)) {
                mostrarError('telefono', 'El teléfono debe tener entre 9 y 15 dígitos');
                esValido = false;
            }
            
            if (!camposAdicionales.correo) {
                mostrarError('correo', 'El correo electrónico es obligatorio');
                esValido = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(camposAdicionales.correo)) {
                mostrarError('correo', 'El correo electrónico no es válido');
                esValido = false;
            } else if (camposAdicionales.correo.length > 100) {
                mostrarError('correo', 'El correo no puede exceder los 100 caracteres');
                esValido = false;
            }
            
            if (!camposAdicionales.direccion) {
                mostrarError('direccion', 'La dirección es obligatoria');
                esValido = false;
            } else if (camposAdicionales.direccion.length > 255) {
                mostrarError('direccion', 'La dirección no puede exceder los 255 caracteres');
                esValido = false;
            }
            
            if (camposAdicionales.iban && !/^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$/.test(camposAdicionales.iban)) {
                mostrarError('iban', 'El IBAN no tiene un formato válido');
                esValido = false;
            } else if (camposAdicionales.iban.length > 34) {
                mostrarError('iban', 'El IBAN no puede exceder los 34 caracteres');
                esValido = false;
            }
        } else if (rol == 2) { // Empleado
            if (!camposAdicionales.dni) {
                mostrarError('dni', 'El DNI es obligatorio');
                esValido = false;
            } else if (!/^\d{8}[A-Z]$/.test(camposAdicionales.dni)) {
                mostrarError('dni', 'El DNI debe tener 8 dígitos seguidos de una letra mayúscula');
                esValido = false;
            }
            
            if (!camposAdicionales.nss) {
                mostrarError('nss', 'El número de seguridad social es obligatorio');
                esValido = false;
            } else if (!/^\d{12}$/.test(camposAdicionales.nss)) {
                mostrarError('nss', 'El número de seguridad social debe tener 12 dígitos');
                esValido = false;
            }
        }
        
        // Si todo es válido, enviar el formulario
        if (esValido) {
            document.getElementById('claveHash').value = hashSHA512(clave);
            
            formularioRegistro.submit();
        }
    }
    
    function mostrarError(campo, mensaje) {
        const elementoCampo = document.getElementById(campo);
        const mensajeError = document.createElement('div');
        
        mensajeError.className = 'error-mensaje';
        mensajeError.textContent = mensaje;
        
        elementoCampo.parentElement.appendChild(mensajeError);
        elementoCampo.classList.add('campo-error');
    }
    
    function limpiarErrores() {
        document.querySelectorAll('.error-mensaje').forEach(elemento => {
            elemento.remove();
        });
        
        document.querySelectorAll('.campo-error').forEach(campo => {
            campo.classList.remove('campo-error');
        });
    }
    
    async function verificarLoginUnico(login) {
        return new Promise(resolve => {
            setTimeout(() => {
                const usuariosExistentes = ['juan.perez', 'ana.lopez', 'carlos.sanchez', 'maria.gomez', 'luis.martin', 'elena.torres'];
                resolve(!usuariosExistentes.includes(login));
            }, 300);
        });
    }
    
    // Función simulada de hash SHA-512
    function hashSHA512(texto) {
        return 'hashed512_' + texto;
    }
    const selectorRol = document.getElementById('rol');
    if (selectorRol) {
        selectorRol.addEventListener('change', function() {
            const rolSeleccionado = this.value;
            const camposCliente = document.getElementById('camposCliente');
            const camposEmpleado = document.getElementById('camposEmpleado');
            
            if (camposCliente) camposCliente.style.display = 'none';
            if (camposEmpleado) camposEmpleado.style.display = 'none';
            
            if (rolSeleccionado == 3 && camposCliente) { // Cliente
                camposCliente.style.display = 'block';
            } else if (rolSeleccionado == 2 && camposEmpleado) { // Empleado
                camposEmpleado.style.display = 'block';
            }
        });
    }
});