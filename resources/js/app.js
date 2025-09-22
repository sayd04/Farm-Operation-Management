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
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Ensure auth header is set and user loaded when token exists
    if (authStore.isAuthenticated && !authStore.user) {
        try {
            await authStore.fetchUser();
        } catch (_) {
            // ignore; bootstrap interceptors will handle 401
        }
    }

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

    // Enforce farmer onboarding: if farmer without farm profile, redirect to onboarding
    const isFarmer = authStore.user?.role === 'farmer';
    const hasFarmProfile = !!(authStore.user && (authStore.user.farm_profile || authStore.user.farm_profile_completed));
    if (authStore.isAuthenticated && isFarmer && !hasFarmProfile && to.path !== '/onboarding') {
        next('/onboarding');
        return;
    }

    // Check role-based access
    if (to.meta.roles && authStore.user) {
        if (!to.meta.roles.includes(authStore.user.role)) {
            next('/dashboard');
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
