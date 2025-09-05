<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Inventory Management
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Track and manage your farm inventory items
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          to="/inventory/create"
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Item
        </router-link>
      </div>
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
            placeholder="Search inventory..."
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
          <select
            id="category"
            v-model="filters.category"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Categories</option>
            <option value="seeds">Seeds</option>
            <option value="fertilizers">Fertilizers</option>
            <option value="pesticides">Pesticides</option>
            <option value="equipment">Equipment</option>
            <option value="tools">Tools</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div>
          <label for="low_stock" class="block text-sm font-medium text-gray-700">Filter</label>
          <select
            id="low_stock"
            v-model="filters.low_stock"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Items</option>
            <option value="true">Low Stock Only</option>
          </select>
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

    <!-- Inventory Grid -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="inventoryItems.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No inventory items</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by adding inventory items.</p>
      <div class="mt-6">
        <router-link
          to="/inventory/create"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Item
        </router-link>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="item in inventoryItems"
        :key="item.id"
        class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200"
      >
        <div class="p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">{{ item.name }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ item.category }}
            </span>
          </div>
          <p v-if="item.description" class="mt-2 text-sm text-gray-500">{{ item.description }}</p>
          
          <div class="mt-4 space-y-2">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Current Stock:</span>
              <span class="text-sm font-medium text-gray-900">{{ item.current_stock }} {{ item.unit }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Minimum Stock:</span>
              <span class="text-sm text-gray-500">{{ item.minimum_stock }} {{ item.unit }}</span>
            </div>
            <div v-if="item.unit_price" class="flex justify-between">
              <span class="text-sm text-gray-500">Unit Price:</span>
              <span class="text-sm text-gray-500">${{ item.unit_price }}</span>
            </div>
          </div>

          <div class="mt-4">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="flex justify-between text-sm">
                  <span>Stock Level</span>
                  <span>{{ stockPercentage(item) }}%</span>
                </div>
                <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                  <div
                    :class="getStockBarColor(item)"
                    class="h-2 rounded-full"
                    :style="{ width: `${stockPercentage(item)}%` }"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-500">
              <span v-if="item.supplier">Supplier: {{ item.supplier }}</span>
            </div>
            <div class="flex space-x-2">
              <router-link
                :to="`/inventory/${item.id}`"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
              >
                View
              </router-link>
              <button
                @click="updateStock(item)"
                class="text-green-600 hover:text-green-900 text-sm font-medium"
              >
                Update Stock
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const inventoryItems = ref([])
const loading = ref(false)
const filters = reactive({
  search: '',
  category: '',
  low_stock: ''
})

const fetchInventoryItems = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.category) params.append('category', filters.category)
    if (filters.low_stock) params.append('low_stock', filters.low_stock)
    
    const response = await axios.get(`/api/inventory-items?${params}`)
    inventoryItems.value = response.data.inventory_items
  } catch (error) {
    console.error('Error fetching inventory items:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchInventoryItems()
}

const stockPercentage = (item) => {
  if (item.minimum_stock === 0) return 100
  return Math.min((item.current_stock / item.minimum_stock) * 100, 100)
}

const getStockBarColor = (item) => {
  const percentage = stockPercentage(item)
  if (percentage <= 25) return 'bg-red-600'
  if (percentage <= 50) return 'bg-yellow-600'
  return 'bg-green-600'
}

const updateStock = (item) => {
  // This could open a modal or navigate to a stock update page
  console.log('Update stock for item:', item.name)
}

onMounted(() => {
  fetchInventoryItems()
})
</script>