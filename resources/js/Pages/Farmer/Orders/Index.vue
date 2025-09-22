<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Orders for My Products</h1>
      <p class="mt-2 text-gray-600">Manage orders placed on your harvested rice listings</p>
    </div>

    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Orders</h3>
      </div>
      <div v-if="loading" class="p-6"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div></div>
      <div v-else class="divide-y divide-gray-200">
        <div v-for="order in orders" :key="order.id" class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900">Order #{{ order.id }} — {{ order.buyer?.name || order.buyer_name }}</h4>
              <p class="text-xs text-gray-500">{{ new Date(order.created_at).toLocaleString() }}</p>
            </div>
            <div class="text-right">
              <select v-model="order.status" @change="updateStatus(order)" class="text-sm rounded-md border-gray-300">
                <option>pending</option>
                <option>confirmed</option>
                <option>processing</option>
                <option>shipped</option>
                <option>delivered</option>
                <option>cancelled</option>
              </select>
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

const load = async () => {
  try {
    const res = await axios.get('/api/farmer/orders');
    orders.value = res.data?.data || res.data || [];
  } catch (e) { console.error('Failed to load orders', e); }
  finally { loading.value = false; }
};

const updateStatus = async (order) => {
  try {
    await axios.put(`/api/farmer/orders/${order.id}`, { status: order.status });
  } catch (e) { console.error('Failed to update status', e); }
};

onMounted(load);
</script>

