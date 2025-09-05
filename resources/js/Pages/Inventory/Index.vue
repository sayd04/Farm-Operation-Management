<template>
  <div class="inventory-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Inventory</h1>
          <p class="text-gray-600 mt-2">Manage your farm supplies and equipment</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Add New Item
        </button>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 text-lg">📦</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ totalItems }}</div>
              <div class="text-sm text-gray-600">Total Items</div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <span class="text-green-600 text-lg">✅</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ inStockItems }}</div>
              <div class="text-sm text-gray-600">In Stock</div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                <span class="text-yellow-600 text-lg">⚠️</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ lowStockItems }}</div>
              <div class="text-sm text-gray-600">Low Stock</div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <span class="text-red-600 text-lg">❌</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ outOfStockItems }}</div>
              <div class="text-sm text-gray-600">Out of Stock</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search inventory..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="categoryFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Categories</option>
              <option value="seeds">Seeds</option>
              <option value="fertilizer">Fertilizer</option>
              <option value="pesticides">Pesticides</option>
              <option value="equipment">Equipment</option>
              <option value="tools">Tools</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="statusFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Status</option>
              <option value="in_stock">In Stock</option>
              <option value="low_stock">Low Stock</option>
              <option value="out_of_stock">Out of Stock</option>
            </select>
          </div>
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Inventory Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="item in filteredItems"
          :key="item.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
        >
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-xl font-semibold text-gray-900">{{ item.name }}</h3>
              <span
                :class="getStatusBadgeClass(item.status)"
                class="px-2 py-1 text-xs font-medium rounded-full"
              >
                {{ item.status }}
              </span>
            </div>
            
            <div class="space-y-2 mb-4">
              <div class="flex justify-between">
                <span class="text-gray-600">Category:</span>
                <span class="font-medium">{{ item.category }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Quantity:</span>
                <span class="font-medium">{{ item.quantity }} {{ item.unit }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Unit Price:</span>
                <span class="font-medium">${{ item.unit_price }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Total Value:</span>
                <span class="font-medium">${{ (item.quantity * item.unit_price).toFixed(2) }}</span>
              </div>
            </div>

            <!-- Stock Level Indicator -->
            <div class="mb-4">
              <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Stock Level</span>
                <span>{{ getStockPercentage(item) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  :class="getStockBarClass(item)"
                  class="h-2 rounded-full transition-all duration-300"
                  :style="{ width: `${getStockPercentage(item)}%` }"
                ></div>
              </div>
            </div>

            <div class="flex space-x-2">
              <button
                @click="viewItem(item.id)"
                class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              >
                View Details
              </button>
              <button
                @click="editItem(item.id)"
                class="flex-1 bg-gray-600 text-white px-3 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm"
              >
                Edit
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredItems.length === 0" class="text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">📦</div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">No inventory items found</h3>
        <p class="text-gray-600 mb-6">Get started by adding your first inventory item</p>
        <button
          @click="showCreateModal = true"
          class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Add Your First Item
        </button>
      </div>

      <!-- Create Item Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
          <h2 class="text-xl font-semibold mb-4">Add New Inventory Item</h2>
          
          <form @submit.prevent="createItem" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Item Name</label>
              <input
                v-model="newItem.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
              <select
                v-model="newItem.category"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select category</option>
                <option value="seeds">Seeds</option>
                <option value="fertilizer">Fertilizer</option>
                <option value="pesticides">Pesticides</option>
                <option value="equipment">Equipment</option>
                <option value="tools">Tools</option>
              </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input
                  v-model="newItem.quantity"
                  type="number"
                  min="0"
                  step="0.1"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                <select
                  v-model="newItem.unit"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                >
                  <option value="">Select unit</option>
                  <option value="lbs">Pounds</option>
                  <option value="bags">Bags</option>
                  <option value="gallons">Gallons</option>
                  <option value="pieces">Pieces</option>
                  <option value="tons">Tons</option>
                </select>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
              <input
                v-model="newItem.unit_price"
                type="number"
                min="0"
                step="0.01"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="newItem.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>

            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showCreateModal = false"
                class="px-4 py-2 text-gray-600 hover:text-gray-800"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
              >
                {{ loading ? 'Creating...' : 'Create Item' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const showCreateModal = ref(false)
const searchQuery = ref('')
const categoryFilter = ref('')
const statusFilter = ref('')

const items = ref([
  {
    id: 1,
    name: 'Corn Seeds - Pioneer 1234',
    category: 'seeds',
    quantity: 50,
    unit: 'bags',
    unit_price: 180.00,
    description: 'High-yield corn seeds for spring planting',
    min_stock: 10,
    max_stock: 100,
    status: 'in_stock'
  },
  {
    id: 2,
    name: 'Nitrogen Fertilizer',
    category: 'fertilizer',
    quantity: 5,
    unit: 'tons',
    unit_price: 450.00,
    description: 'High-grade nitrogen fertilizer',
    min_stock: 2,
    max_stock: 20,
    status: 'low_stock'
  },
  {
    id: 3,
    name: 'Herbicide - Roundup',
    category: 'pesticides',
    quantity: 0,
    unit: 'gallons',
    unit_price: 25.00,
    description: 'Broad-spectrum herbicide',
    min_stock: 5,
    max_stock: 50,
    status: 'out_of_stock'
  },
  {
    id: 4,
    name: 'Tractor Fuel',
    category: 'equipment',
    quantity: 200,
    unit: 'gallons',
    unit_price: 3.50,
    description: 'Diesel fuel for farm equipment',
    min_stock: 50,
    max_stock: 500,
    status: 'in_stock'
  }
])

const newItem = ref({
  name: '',
  category: '',
  quantity: '',
  unit: '',
  unit_price: '',
  description: ''
})

const filteredItems = computed(() => {
  return items.value.filter(item => {
    const matchesSearch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         item.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = !categoryFilter.value || item.category === categoryFilter.value
    const matchesStatus = !statusFilter.value || item.status === statusFilter.value
    
    return matchesSearch && matchesCategory && matchesStatus
  })
})

const totalItems = computed(() => items.value.length)
const inStockItems = computed(() => items.value.filter(item => item.status === 'in_stock').length)
const lowStockItems = computed(() => items.value.filter(item => item.status === 'low_stock').length)
const outOfStockItems = computed(() => items.value.filter(item => item.status === 'out_of_stock').length)

const getStatusBadgeClass = (status) => {
  const classes = {
    in_stock: 'bg-green-100 text-green-800',
    low_stock: 'bg-yellow-100 text-yellow-800',
    out_of_stock: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStockPercentage = (item) => {
  if (item.max_stock === 0) return 0
  return Math.min((item.quantity / item.max_stock) * 100, 100)
}

const getStockBarClass = (item) => {
  const percentage = getStockPercentage(item)
  if (percentage < 20) return 'bg-red-600'
  if (percentage < 50) return 'bg-yellow-600'
  return 'bg-green-600'
}

const viewItem = (id) => {
  router.push(`/inventory/${id}`)
}

const editItem = (id) => {
  // Navigate to edit page or show edit modal
  console.log('Edit item:', id)
}

const createItem = async () => {
  loading.value = true
  try {
    const item = {
      id: Date.now(),
      ...newItem.value,
      quantity: parseFloat(newItem.value.quantity),
      unit_price: parseFloat(newItem.value.unit_price),
      min_stock: 0,
      max_stock: 100,
      status: 'in_stock'
    }
    items.value.push(item)
    
    // Reset form
    newItem.value = {
      name: '',
      category: '',
      quantity: '',
      unit: '',
      unit_price: '',
      description: ''
    }
    showCreateModal.value = false
  } catch (error) {
    console.error('Error creating item:', error)
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  categoryFilter.value = ''
  statusFilter.value = ''
}

onMounted(() => {
  // Load inventory from API
})
</script>

<style scoped>
.inventory-page {
  min-height: 100vh;
  background-color: #f8fafc;
}
</style>