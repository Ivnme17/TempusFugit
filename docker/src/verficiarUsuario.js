document.addEventListener('DOMContentLoaded', function() {
    const loginInput = document.getElementById('loginUsuario');
    if (loginInput) {
        loginInput.addEventListener('blur', function() {
            if (this.value.trim() !== '') {
                verificarUsuario(this.value);
            }
        });
    }
});

function verificarUsuario(login) {
    fetch('../verificar_usuario.php', {
        method: 'POST',
        body: JSON.stringify({ login: login }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const mensajeDiv = document.getElementById('mensaje-disponibilidad');
        if (data.existe) {
            mensajeDiv.textContent = 'Este nombre de usuario ya está en uso';
            mensajeDiv.style.color = 'red';
        } else {
            mensajeDiv.textContent = 'Nombre de usuario disponible';
            mensajeDiv.style.color = 'green';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}