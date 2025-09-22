import axios from 'axios';

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

// Export axios for use in Vue components
export default axios;
