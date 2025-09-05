<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-4">
            <li>
              <router-link to="/inventory" class="text-gray-400 hover:text-gray-500">
                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="sr-only">Inventory</span>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-4 text-sm font-medium text-gray-500">{{ item.name }}</span>
              </div>
            </li>
          </ol>
        </nav>
        <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          {{ item.name }}
        </h2>
        <div class="mt-1 flex items-center space-x-4">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            {{ item.category }}
          </span>
          <span v-if="isLowStock" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
            Low Stock
          </span>
        </div>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="updateStock"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Update Stock
        </button>
        <router-link
          :to="`/inventory/${item.id}/edit`"
          class="ml-3 bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Edit
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Item Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Item Information</h3>
          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Category</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ item.category }}
                </span>
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Unit</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ item.unit }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Current Stock</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ item.current_stock }} {{ item.unit }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Minimum Stock</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ item.minimum_stock }} {{ item.unit }}</dd>
            </div>
            <div v-if="item.unit_price">
              <dt class="text-sm font-medium text-gray-500">Unit Price</dt>
              <dd class="mt-1 text-sm text-gray-900">${{ item.unit_price }}</dd>
            </div>
            <div v-if="item.supplier">
              <dt class="text-sm font-medium text-gray-500">Supplier</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ item.supplier }}</dd>
            </div>
            <div v-if="item.location">
              <dt class="text-sm font-medium text-gray-500">Location</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ item.location }}</dd>
            </div>
            <div v-if="item.expiry_date">
              <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(item.expiry_date) }}</dd>
            </div>
            <div v-if="item.description" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Description</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ item.description }}</dd>
            </div>
            <div v-if="item.notes" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Notes</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ item.notes }}</dd>
            </div>
          </dl>
        </div>

        <!-- Stock Update Form -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Update Stock</h3>
          <form @submit.prevent="submitStockUpdate" class="space-y-4">
            <div>
              <label for="operation" class="block text-sm font-medium text-gray-700">Operation</label>
              <select
                id="operation"
                v-model="stockForm.operation"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="add">Add Stock</option>
                <option value="subtract">Subtract Stock</option>
                <option value="set">Set Stock</option>
              </select>
            </div>
            <div>
              <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
              <input
                id="quantity"
                v-model="stockForm.quantity"
                type="number"
                step="0.01"
                min="0"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                :placeholder="`Enter quantity in ${item.unit}`"
              />
            </div>
            <div>
              <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
              <textarea
                id="notes"
                v-model="stockForm.notes"
                rows="3"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Add notes about this stock update..."
              ></textarea>
            </div>
            <div>
              <button
                type="submit"
                :disabled="stockLoading"
                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                {{ stockLoading ? 'Updating...' : 'Update Stock' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Stock Status -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Status</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Current Stock</span>
              <span class="text-sm font-medium text-gray-900">{{ item.current_stock }} {{ item.unit }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Minimum Stock</span>
              <span class="text-sm text-gray-500">{{ item.minimum_stock }} {{ item.unit }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Stock Level</span>
              <span class="text-sm font-medium text-gray-900">{{ stockPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                :class="stockBarColor"
                class="h-2 rounded-full"
                :style="{ width: `${stockPercentage}%` }"
              ></div>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Status</span>
              <span :class="stockStatusBadgeClass" class="text-sm">
                {{ stockStatus }}
              </span>
            </div>
          </div>
        </div>

        <!-- Value Information -->
        <div v-if="item.unit_price" class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Value Information</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Unit Price</span>
              <span class="text-sm font-medium text-gray-900">${{ item.unit_price }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Value</span>
              <span class="text-sm font-medium text-gray-900">${{ totalValue }}</span>
            </div>
          </div>
        </div>

        <!-- Expiry Warning -->
        <div v-if="isExpiringSoon" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-yellow-800">Expiry Warning</h3>
              <div class="mt-2 text-sm text-yellow-700">
                <p>This item expires on {{ formatDate(item.expiry_date) }}.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const item = ref({})
const loading = ref(false)
const stockLoading = ref(false)

const stockForm = reactive({
  operation: 'add',
  quantity: '',
  notes: ''
})

const stockPercentage = computed(() => {
  if (item.value.minimum_stock === 0) return 100
  return Math.min((item.value.current_stock / item.value.minimum_stock) * 100, 100)
})

const isLowStock = computed(() => {
  return item.value.current_stock <= item.value.minimum_stock
})

const stockStatus = computed(() => {
  if (isLowStock.value) return 'Low Stock'
  if (stockPercentage.value <= 50) return 'Medium Stock'
  return 'Good Stock'
})

const stockStatusBadgeClass = computed(() => {
  if (isLowStock.value) {
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'
  }
  if (stockPercentage.value <= 50) {
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'
  }
  return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'
})

const stockBarColor = computed(() => {
  if (isLowStock.value) return 'bg-red-600'
  if (stockPercentage.value <= 50) return 'bg-yellow-600'
  return 'bg-green-600'
})

const totalValue = computed(() => {
  if (!item.value.unit_price) return 0
  return (item.value.current_stock * item.value.unit_price).toFixed(2)
})

const isExpiringSoon = computed(() => {
  if (!item.value.expiry_date) return false
  const expiryDate = new Date(item.value.expiry_date)
  const now = new Date()
  const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24))
  return daysUntilExpiry <= 30 && daysUntilExpiry > 0
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const fetchItem = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/inventory-items/${route.params.id}`)
    item.value = response.data.inventory_item
  } catch (error) {
    console.error('Error fetching inventory item:', error)
  } finally {
    loading.value = false
  }
}

const submitStockUpdate = async () => {
  stockLoading.value = true
  try {
    await axios.patch(`/api/inventory-items/${route.params.id}/stock`, stockForm)
    await fetchItem() // Refresh item data
    stockForm.quantity = ''
    stockForm.notes = ''
  } catch (error) {
    console.error('Error updating stock:', error)
  } finally {
    stockLoading.value = false
  }
}

const updateStock = () => {
  // This could open a modal or scroll to the stock update form
  document.getElementById('quantity')?.focus()
}

onMounted(() => {
  fetchItem()
})
</script>