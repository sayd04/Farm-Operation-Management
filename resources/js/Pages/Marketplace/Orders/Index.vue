<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
      <p class="mt-2 text-gray-600">View your order history and status</p>
    </div>

    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Orders</h3>
      </div>
      <div v-if="loading" class="p-6">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
      </div>
      <div v-else class="divide-y divide-gray-200">
        <div v-for="order in orders" :key="order.id" class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900">Order #{{ order.id }}</h4>
              <p class="text-xs text-gray-500">{{ new Date(order.created_at).toLocaleString() }}</p>
            </div>
            <div class="text-right">
              <span :class="statusClass(order.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">{{ order.status }}</span>
              <p class="text-sm font-medium text-gray-900 mt-1">${{ order.total_amount?.toFixed?.(2) || order.total_amount }}</p>
            </div>
          </div>
          <div class="mt-2 text-sm text-gray-600">
            {{ order.items?.length || 0 }} items
          </div>
        </div>
        <div v-if="orders.length === 0" class="px-6 py-12 text-center text-gray-500">No orders yet.</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const orders = ref([]);

const fetchOrders = async () => {
  try {
    const res = await axios.get('/api/marketplace/orders');
    orders.value = res.data?.data || res.data || [];
  } catch (e) {
    console.error('Failed to fetch orders', e);
  } finally {
    loading.value = false;
  }
};

const statusClass = (status) => {
  const map = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    processing: 'bg-purple-100 text-purple-800',
    shipped: 'bg-indigo-100 text-indigo-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return map[status] || 'bg-gray-100 text-gray-800';
};

onMounted(fetchOrders);
</script>

