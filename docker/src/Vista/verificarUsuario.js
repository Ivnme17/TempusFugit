document.addEventListener('DOMContentLoaded', function() {
    console.log('Script inicializado');
    
    const loginInput = document.getElementById('loginUsuario');
    if (!loginInput) {
        console.error('No se encontró el elemento loginUsuario');
        return;
    }

    console.log('Input encontrado:', loginInput);
    
    // Variable para controlar peticiones múltiples
    let timeoutId = null;
    
    loginInput.addEventListener('input', function() {
        // Limpiar timeout anterior
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        
        // Esperar 500ms después de que el usuario deje de escribir
        timeoutId = setTimeout(() => {
            verificarUsuario(this.value.trim());
        }, 500);
    });
    
    loginInput.addEventListener('blur', function() {
        // También verificar cuando pierde el foco
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        verificarUsuario(this.value.trim());
    });
    
    async function verificarUsuario(valor) {
        console.log('Valor a verificar:', valor);
        
        const mensajeDiv = document.getElementById('mensaje-disponibilidad');
        if (!mensajeDiv) {
            console.error('No se encontró el div de mensaje');
            return;
        }
        
        // Limpiar mensaje si el campo está vacío
        if (valor === '') {
            mensajeDiv.textContent = '';
            return;
        }
        
        // Mostrar mensaje de verificando
        mensajeDiv.textContent = 'Verificando...';
        mensajeDiv.style.color = 'orange';
        
        try {
            const response = await fetch('../verificar_usuario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ login: valor })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Respuesta del servidor:', data);
            
            // Verificar que la respuesta sea válida
            if (data.status !== 'success') {
                throw new Error(data.error || 'Error desconocido');
            }
            
            // Actualizar mensaje basado en la respuesta
            if (data.existe === true) {
                mensajeDiv.textContent = 'Este nombre de usuario ya está en uso';
                mensajeDiv.style.color = 'red';
                mensajeDiv.style.fontWeight = 'bold';
            } else if (data.existe === false) {
                mensajeDiv.textContent = 'Nombre de usuario disponible';
                mensajeDiv.style.color = 'green';
                mensajeDiv.style.fontWeight = 'bold';
            } else {
                // Caso inesperado
                console.warn('Respuesta inesperada:', data);
                mensajeDiv.textContent = 'Error al verificar disponibilidad';
                mensajeDiv.style.color = 'orange';
            }
            
        } catch (error) {
            console.error('Error en la verificación:', error);
            mensajeDiv.textContent = 'Error al verificar disponibilidad';
            mensajeDiv.style.color = 'red';
        }
    }
});