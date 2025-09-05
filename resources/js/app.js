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

// Create Vue app
const app = createApp(App);

app.use(pinia);
app.use(router);

app.mount('#app');
