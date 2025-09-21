import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import routes from './routes';
import { createPinia } from 'pinia';
import { useAuthStore } from '@/stores/auth';

// Create Pinia store
const pinia = createPinia();

// Create Vue Router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    
    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
        return;
    }
    
    // Check if route requires guest (not authenticated)
    if (to.meta.requiresGuest && authStore.isAuthenticated) {
        next('/dashboard');
        return;
    }
    
    // Check role-based access
    if (to.meta.roles && authStore.user) {
        if (!to.meta.roles.includes(authStore.user.role)) {
            next('/dashboard'); // Redirect to dashboard if role not allowed
            return;
        }
    }
    
    next();
});

// Create Vue app
const app = createApp(App);

app.use(pinia);
app.use(router);

app.mount('#app');
