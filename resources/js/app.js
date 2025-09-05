import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import routes from './routes';
import { createPinia } from 'pinia';

// Create Pinia store
const pinia = createPinia();

// Create Vue Router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Global navigation guard enforcing auth and roles
router.beforeEach((to, from, next) => {
    const { useAuthStore } = require('./stores/auth');
    const authStore = useAuthStore();
    authStore.initializeAuth();

    if (to.meta?.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
        return;
    }

    if (to.meta?.requiresGuest && authStore.isAuthenticated) {
        next('/dashboard');
        return;
    }

    if (to.meta?.roles && authStore.user && !to.meta.roles.includes(authStore.user.role)) {
        next('/dashboard');
        return;
    }

    next();
});

// Create Vue app
const app = createApp(App);

app.use(pinia);
app.use(router);

app.mount('#app');
