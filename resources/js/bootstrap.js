import axios from 'axios';

window.axios = axios;

const TOKEN_KEY = 'token';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
window.axios.defaults.baseURL = '/api';

// Agregar token de autenticación si existe
const token = localStorage.getItem(TOKEN_KEY);
if (token) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}

// Auth object global
window.Auth = {
    get() { return localStorage.getItem(TOKEN_KEY); },
    set(token) {
        localStorage.setItem(TOKEN_KEY, token);
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },
    clear() {
        localStorage.removeItem(TOKEN_KEY);
        delete window.axios.defaults.headers.common['Authorization'];
    }
};

