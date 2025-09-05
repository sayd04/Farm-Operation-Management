<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">
        Welcome back, {{ authStore.user?.name }}!
      </h1>
      <p class="mt-2 text-gray-600">
        Here's what's happening on your farm today.
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div
          v-for="(stat, key) in dashboardData.stats"
          :key="key"
          class="bg-white overflow-hidden shadow rounded-lg"
        >
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <component
                  :is="getStatIcon(key)"
                  class="h-6 w-6 text-gray-400"
                />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ formatStatLabel(key) }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ formatStatValue(key, stat) }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Weather Widget (Farmers only) -->
      <div
        v-if="(authStore.isFarmer || authStore.isAdmin) && dashboardData.weather_data?.length"
        class="bg-white shadow rounded-lg p-6 mb-8"
      >
        <h3 class="text-lg font-medium text-gray-900 mb-4">Weather Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="item in dashboardData.weather_data.slice(0, 3)"
            :key="item.field._id"
            class="border rounded-lg p-4"
          >
            <div class="flex items-center justify-between mb-2">
              <h4 class="font-medium text-gray-900">
                {{ item.field.name || `Field ${item.field._id}` }}
              </h4>
              <component
                :is="getWeatherIcon(item.weather.conditions)"
                class="h-6 w-6 text-blue-500"
              />
            </div>
            <div class="space-y-1 text-sm text-gray-600">
              <p>Temperature: {{ item.weather.temperature }}°C</p>
              <p>Humidity: {{ item.weather.humidity }}%</p>
              <p>Conditions: {{ item.weather.conditions }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Tasks (Farmers only) -->
      <div
        v-if="(authStore.isFarmer || authStore.isAdmin) && dashboardData.recent_tasks?.length"
        class="bg-white shadow rounded-lg mb-8"
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Recent Tasks</h3>
        </div>
        <div class="divide-y divide-gray-200">
          <div
            v-for="task in dashboardData.recent_tasks"
            :key="task._id"
            class="px-6 py-4"
          >
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-medium text-gray-900">
                  {{ task.task_type.replace('_', ' ').toUpperCase() }}
                </h4>
                <p class="text-sm text-gray-500">
                  {{ task.description }}
                </p>
                <p class="text-xs text-gray-400">
                  Due: {{ formatDate(task.due_date) }}
                </p>
              </div>
              <span
                :class="getTaskStatusClass(task.status)"
                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
              >
                {{ task.status.replace('_', ' ') }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders (Buyers) -->
      <div
        v-if="authStore.isBuyer && dashboardData.recent_orders?.length"
        class="bg-white shadow rounded-lg mb-8"
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
        </div>
        <div class="divide-y divide-gray-200">
          <div
            v-for="order in dashboardData.recent_orders"
            :key="order._id"
            class="px-6 py-4"
          >
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-medium text-gray-900">
                  Order #{{ order._id.slice(-6) }}
                </h4>
                <p class="text-sm text-gray-500">
                  {{ order.order_items?.length }} items
                </p>
                <p class="text-xs text-gray-400">
                  {{ formatDate(order.created_at) }}
                </p>
              </div>
              <div class="text-right">
                <span
                  :class="getOrderStatusClass(order.status)"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ order.status }}
                </span>
                <p class="text-sm font-medium text-gray-900 mt-1">
                  ${{ order.total_amount }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Available Products (Buyers) -->
      <div
        v-if="authStore.isBuyer && dashboardData.available_products?.length"
        class="bg-white shadow rounded-lg"
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Available Products</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
          <div
            v-for="product in dashboardData.available_products.slice(0, 6)"
            :key="product._id"
            class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
            @click="$router.push(`/marketplace/products/${product._id}`)"
          >
            <h4 class="font-medium text-gray-900">{{ product.name }}</h4>
            <p class="text-sm text-gray-500">{{ product.category }}</p>
            <div class="flex justify-between items-center mt-2">
              <span class="text-lg font-bold text-green-600">
                ${{ product.price }}
              </span>
              <span class="text-sm text-gray-500">
                {{ product.quantity }} {{ product.unit }} available
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import {
  HomeIcon,
  ChartBarIcon,
  ExclamationTriangleIcon,
  ShoppingCartIcon,
  SunIcon,
  CloudIcon,
} from '@heroicons/vue/24/outline';

const authStore = useAuthStore();
const loading = ref(true);
const dashboardData = ref({});

const fetchDashboardData = async () => {
  try {
    const response = await axios.get('/api/dashboard');
    dashboardData.value = response.data;
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error);
  } finally {
    loading.value = false;
  }
};

const getStatIcon = (key) => {
  const iconMap = {
    total_fields: HomeIcon,
    active_plantings: ChartBarIcon,
    pending_tasks: ExclamationTriangleIcon,
    total_orders: ShoppingCartIcon,
    pending_orders: ExclamationTriangleIcon,
    completed_orders: ChartBarIcon,
  };
  return iconMap[key] || HomeIcon;
};

const formatStatLabel = (key) => {
  const labelMap = {
    total_fields: 'Total Fields',
    active_plantings: 'Active Plantings',
    pending_tasks: 'Pending Tasks',
    overdue_tasks: 'Overdue Tasks',
    low_stock_items: 'Low Stock Items',
    total_orders: 'Total Orders',
    pending_orders: 'Pending Orders',
    completed_orders: 'Completed Orders',
    total_spent: 'Total Spent',
  };
  return labelMap[key] || key.replace('_', ' ').toUpperCase();
};

const formatStatValue = (key, value) => {
  if (key === 'total_spent') {
    return `$${value}`;
  }
  return value;
};

const getWeatherIcon = (condition) => {
  const iconMap = {
    clear: SunIcon,
    cloudy: CloudIcon,
    rainy: CloudIcon,
    stormy: CloudIcon,
  };
  return iconMap[condition] || SunIcon;
};

const getTaskStatusClass = (status) => {
  const classMap = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classMap[status] || 'bg-gray-100 text-gray-800';
};

const getOrderStatusClass = (status) => {
  const classMap = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    processing: 'bg-purple-100 text-purple-800',
    shipped: 'bg-indigo-100 text-indigo-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classMap[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  fetchDashboardData();
});
</script>