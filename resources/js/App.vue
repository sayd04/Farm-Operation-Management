<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav v-if="authStore.user" class="bg-green-600 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/dashboard" class="flex-shrink-0">
              <h1 class="text-white text-xl font-bold">Farm Operations</h1>
            </router-link>
            
            <!-- Navigation Links -->
            <div class="hidden md:ml-10 md:flex md:space-x-8">
              <router-link
                to="/dashboard"
                class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                :class="{ 'text-white bg-green-700': $route.path === '/dashboard' }"
              >
                Dashboard
              </router-link>
              
              <template v-if="authStore.user.role === 'farmer' || authStore.user.role === 'admin'">
                <router-link
                  to="/fields"
                  class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                  :class="{ 'text-white bg-green-700': $route.path.startsWith('/fields') }"
                >
                  Fields
                </router-link>
                <router-link
                  to="/plantings"
                  class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                  :class="{ 'text-white bg-green-700': $route.path.startsWith('/plantings') }"
                >
                  Plantings
                </router-link>
                <router-link
                  to="/tasks"
                  class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                  :class="{ 'text-white bg-green-700': $route.path.startsWith('/tasks') }"
                >
                  Tasks
                </router-link>
                <router-link
                  to="/inventory"
                  class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                  :class="{ 'text-white bg-green-700': $route.path.startsWith('/inventory') }"
                >
                  Inventory
                </router-link>
                <router-link
                  to="/weather"
                  class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                  :class="{ 'text-white bg-green-700': $route.path.startsWith('/weather') }"
                >
                  Weather
                </router-link>
              </template>
              
              <router-link
                to="/marketplace"
                class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                :class="{ 'text-white bg-green-700': $route.path.startsWith('/marketplace') }"
              >
                Marketplace
              </router-link>
              
              <template v-if="authStore.user.role === 'admin'">
                <router-link
                  to="/admin"
                  class="text-green-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                  :class="{ 'text-white bg-green-700': $route.path.startsWith('/admin') }"
                >
                  Admin
                </router-link>
              </template>
            </div>
          </div>
          
          <!-- User menu -->
          <div class="flex items-center">
            <div class="relative">
              <button
                @click="showUserMenu = !showUserMenu"
                class="flex items-center text-green-100 hover:text-white focus:outline-none"
              >
                <span class="mr-2">{{ authStore.user.name }}</span>
                <ChevronDownIcon class="h-4 w-4" />
              </button>
              
              <!-- User dropdown -->
              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                @click.away="showUserMenu = false"
              >
                <router-link
                  to="/profile"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  @click="showUserMenu = false"
                >
                  Profile
                </router-link>
                <button
                  @click="logout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
      <router-view />
    </main>

    <Toast />

    <!-- Global Loading -->
    <div v-if="loading" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Loading...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import { useAuthStore } from '@/stores/auth';
import { useAlertsStore } from '@/stores/alerts';
import Toast from '@/Components/UI/Toast.vue';

const router = useRouter();
const authStore = useAuthStore();
const alerts = useAlertsStore();

const showUserMenu = ref(false);
const loading = ref(false);

const logout = async () => {
  loading.value = true;
  await authStore.logout();
  router.push('/login');
  loading.value = false;
};

onMounted(() => {
  // Check if user is authenticated on app load
  if (authStore.token) {
    authStore.fetchUser();
  }
  // Subscribe to alerts when user is available
  watch(() => authStore.user, (u) => {
    if (u?.id) alerts.subscribe(u.id);
  }, { immediate: true });
});
</script>

<style>
/* Global styles */
body {
  font-family: 'Inter', sans-serif;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>