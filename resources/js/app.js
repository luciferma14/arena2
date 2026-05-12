import './bootstrap';

// Global functions for authentication
window.setupAuthHandlers = function() {
    // Manejar respuesta de login
    const loginForm = document.querySelector('form[action*="/login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: data.email,
                        password: data.password
                    })
                });

                const result = await response.json();

                if (response.ok && result.token) {
                    localStorage.setItem('token', result.token);
                    window.location.href = '/dashboard';
                } else {
                    alert('Error: ' + (result.error || 'Credenciales inválidas'));
                }
            } catch (error) {
                console.error('Login error:', error);
                alert('Error al iniciar sesión');
            }
        });
    }

    // Manejar respuesta de register
    const registerForm = document.querySelector('form[action*="/register"]');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData);

            if (data.password !== data.password_confirmation) {
                alert('Las contraseñas no coinciden');
                return;
            }

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        nombre: data.nombre,
                        apellido: data.apellido,
                        email: data.email,
                        password: data.password,
                        password_confirmation: data.password_confirmation
                    })
                });

                const result = await response.json();

                if (response.ok && result.token) {
                    localStorage.setItem('token', result.token);
                    window.location.href = '/dashboard';
                } else {
                    alert('Error: ' + (result.error || 'No se pudo crear la cuenta'));
                }
            } catch (error) {
                console.error('Register error:', error);
                alert('Error al registrarse');
            }
        });
    }
};

// Ejecutar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.setupAuthHandlers);
} else {
    window.setupAuthHandlers();
}

