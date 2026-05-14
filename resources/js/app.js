import '../css/app.css'

// API base URL
const API_URL = '/api'

// Utility to get CSRF token from meta tag
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || ''
}

// Utility to get auth token from localStorage
function getAuthToken() {
    return localStorage.getItem('auth_token') || ''
}

// API helper functions
window.api = {
    async get(endpoint) {
        const response = await fetch(`${API_URL}${endpoint}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${getAuthToken()}`,
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        })
        return response.json()
    },

    async post(endpoint, data) {
        const response = await fetch(`${API_URL}${endpoint}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${getAuthToken()}`,
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify(data),
        })
        return response.json()
    },

    async delete(endpoint) {
        const response = await fetch(`${API_URL}${endpoint}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${getAuthToken()}`,
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        })
        return response.json()
    },
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    console.log('App initialized')
})
