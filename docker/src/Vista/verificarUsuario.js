document.addEventListener('DOMContentLoaded', function() {
    console.log('Script inicializado');
    
    const loginInput = document.getElementById('loginUsuario');
    if (!loginInput) {
        console.error('No se encontró el elemento loginUsuario');
        return;
    }

    console.log('Input encontrado:', loginInput);
    
    loginInput.addEventListener('blur', async function() {
        const valor = this.value.trim();
        console.log('Valor a verificar:', valor);
        
        if (valor !== '') {
            try {
                // Cambiamos la ruta para que apunte correctamente al archivo
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
                
                const mensajeDiv = document.getElementById('mensaje-disponibilidad');
                if (!mensajeDiv) {
                    console.error('No se encontró el div de mensaje');
                    return;
                }
                
                if (data.existe) {
                    mensajeDiv.textContent = 'Este nombre de usuario ya está en uso';
                    mensajeDiv.style.color = 'red';
                } else {
                    mensajeDiv.textContent = 'Nombre de usuario disponible';
                    mensajeDiv.style.color = 'green';
                }
            } catch (error) {
                console.error('Error en la verificación:', error);
            }
        }
    });
});