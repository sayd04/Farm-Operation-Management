<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Orders
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Track your purchase and sales orders
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          to="/marketplace"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Browse Marketplace
        </router-link>
      </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
      <nav class="-mb-px flex space-x-8">
        <button
          @click="activeTab = 'purchases'"
          :class="activeTab === 'purchases' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          My Purchases
        </button>
        <button
          @click="activeTab = 'sales'"
          :class="activeTab === 'sales' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          My Sales
        </button>
      </nav>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
          <input
            id="search"
            v-model="filters.search"
            type="text"
            placeholder="Search orders..."
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            v-model="filters.status"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
          <input
            id="date_from"
            v-model="filters.date_from"
            type="date"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Orders List -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="orders.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ activeTab === 'purchases' ? 'You haven\'t made any purchases yet.' : 'You haven\'t made any sales yet.' }}
      </p>
      <div class="mt-6">
        <router-link
          to="/marketplace"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          {{ activeTab === 'purchases' ? 'Start Shopping' : 'List Products' }}
        </router-link>
      </div>
    </div>

    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="order in orders" :key="order.id">
          <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                  <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <div class="flex items-center">
                  <p class="text-sm font-medium text-gray-900">Order #{{ order.id }}</p>
                  <span :class="getStatusBadgeClass(order.status)" class="ml-2">
                    {{ order.status }}
                  </span>
                </div>
                <div class="flex items-center mt-1">
                  <p class="text-sm text-gray-500">
                    {{ activeTab === 'purchases' ? 'From' : 'To' }}: {{ getOrderParty(order) }}
                  </p>
                  <span class="mx-2 text-gray-300">•</span>
                  <p class="text-sm text-gray-500">{{ formatDate(order.order_date || order.sale_date) }}</p>
                </div>
                <p v-if="order.items?.length" class="text-sm text-gray-500 mt-1">
                  {{ order.items.length }} item(s)
                </p>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <div class="text-right">
                <p class="text-sm font-medium text-gray-900">${{ order.total_amount || order.total_value }}</p>
                <p class="text-sm text-gray-500">{{ getPaymentStatus(order) }}</p>
              </div>
              <div class="flex items-center space-x-2">
                <router-link
                  :to="`/orders/${order.id}`"
                  class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                >
                  View Details
                </router-link>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const orders = ref([])
const loading = ref(false)
const activeTab = ref('purchases')

const filters = reactive({
  search: '',
  status: '',
  date_from: ''
})

const fetchOrders = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.date_from) params.append('date_from', filters.date_from)
    
    const endpoint = activeTab.value === 'purchases' ? '/api/orders' : '/api/sales'
    const response = await axios.get(`${endpoint}?${params}`)
    orders.value = response.data.orders || response.data.sales || []
  } catch (error) {
    console.error('Error fetching orders:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchOrders()
}

const getOrderParty = (order) => {
  if (activeTab.value === 'purchases') {
    return order.supplier_name || 'Unknown Supplier'
  } else {
    return order.buyer?.name || 'Unknown Buyer'
  }
}

const getPaymentStatus = (order) => {
  return order.payment_status || order.payment_method || 'Unknown'
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800',
    confirmed: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800',
    shipped: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800',
    delivered: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800',
    cancelled: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'
  }
  return classes[status] || classes.pending
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

// Watch for tab changes
watch(activeTab, () => {
  fetchOrders()
})

onMounted(() => {
  fetchOrders()
})
</script>