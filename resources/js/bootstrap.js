import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

import { Ziggy } from './ziggy';
window.Ziggy = Ziggy;

// --- Interceptors ---

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const status  = error.response?.status;
        const message = error.response?.data?.message;

        if (status === 401) {
            // Session expirée — redirige vers login
            window.location.href = '/login';
            return Promise.reject(error);
        }

        if (status === 419) {
            // CSRF token expiré — recharge la page pour en obtenir un nouveau
            window.location.reload();
            return Promise.reject(error);
        }

        if (status === 403) {
            console.warn('Accès refusé:', message);
        }

        if (status === 422) {
            // Laisse les composants gérer les erreurs de validation
            return Promise.reject(error);
        }

        if (status === 429) {
            console.warn('Rate limit atteint:', message);
        }

        if (status >= 500) {
            console.error('Erreur serveur:', message ?? error.message);
        }

        return Promise.reject(error);
    }
);
