import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Set base URL
window.axios.defaults.baseURL = window.location.origin;

// Add token from localStorage if available
const token = localStorage.getItem('token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Response interceptor for auth/permission errors
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error?.response?.status;
        if (status === 401 || status === 419) {
            // Unauth or CSRF timeout: clear token and redirect to login
            localStorage.removeItem('token');
            delete window.axios.defaults.headers.common['Authorization'];
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }
        if (status === 403) {
            // Forbidden: optionally notify user, here we just log
            console.warn('Access forbidden');
        }
        return Promise.reject(error);
    }
);

// Laravel Echo (Pusher/WebSocket) setup
try {
    window.Pusher = Pusher;
    const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
    if (pusherKey) {
        const scheme = import.meta.env.VITE_PUSHER_SCHEME ?? (location.protocol === 'https:' ? 'https' : 'http');
        const forceTLS = scheme === 'https';
        const wsHost = import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname;
        const wsPort = import.meta.env.VITE_PUSHER_PORT ?? (forceTLS ? 443 : 6001);
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
            wsHost,
            wsPort,
            wssPort: 443,
            forceTLS,
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
            withCredentials: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: token ? { Authorization: `Bearer ${token}` } : {},
            },
        });
    }
} catch (e) {
    console.warn('Echo setup skipped:', e);
}

// Export axios for use in Vue components
export default axios;
