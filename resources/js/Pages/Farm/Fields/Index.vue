<template>
  <div class="fields-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Fields</h1>
          <p class="text-gray-600 mt-2">Manage your farm fields and their details</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Add New Field
        </button>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search fields..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="statusFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="fallow">Fallow</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Crop Type</label>
            <select
              v-model="cropFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Crops</option>
              <option value="corn">Corn</option>
              <option value="wheat">Wheat</option>
              <option value="soybeans">Soybeans</option>
              <option value="rice">Rice</option>
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

      <!-- Fields Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="field in filteredFields"
          :key="field.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
        >
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-xl font-semibold text-gray-900">{{ field.name }}</h3>
              <span
                :class="getStatusBadgeClass(field.status)"
                class="px-2 py-1 text-xs font-medium rounded-full"
              >
                {{ field.status }}
              </span>
            </div>
            
            <div class="space-y-2 mb-4">
              <div class="flex justify-between">
                <span class="text-gray-600">Size:</span>
                <span class="font-medium">{{ field.size }} acres</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Crop:</span>
                <span class="font-medium">{{ field.current_crop || 'None' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Soil Type:</span>
                <span class="font-medium">{{ field.soil_type }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Last Planted:</span>
                <span class="font-medium">{{ formatDate(field.last_planted) }}</span>
              </div>
            </div>

            <div class="flex space-x-2">
              <button
                @click="viewField(field.id)"
                class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              >
                View Details
              </button>
              <button
                @click="editField(field.id)"
                class="flex-1 bg-gray-600 text-white px-3 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm"
              >
                Edit
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredFields.length === 0" class="text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">🌾</div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">No fields found</h3>
        <p class="text-gray-600 mb-6">Get started by adding your first field</p>
        <button
          @click="showCreateModal = true"
          class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Add Your First Field
        </button>
      </div>

      <!-- Create Field Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
          <h2 class="text-xl font-semibold mb-4">Add New Field</h2>
          
          <form @submit.prevent="createField" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Field Name</label>
              <input
                v-model="newField.name"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Size (acres)</label>
              <input
                v-model="newField.size"
                type="number"
                step="0.1"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Soil Type</label>
              <select
                v-model="newField.soil_type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">Select soil type</option>
                <option value="clay">Clay</option>
                <option value="sandy">Sandy</option>
                <option value="loamy">Loamy</option>
                <option value="silty">Silty</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="newField.description"
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
                {{ loading ? 'Creating...' : 'Create Field' }}
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
const statusFilter = ref('')
const cropFilter = ref('')

const fields = ref([
  {
    id: 1,
    name: 'North Field',
    size: 25.5,
    soil_type: 'loamy',
    current_crop: 'corn',
    status: 'active',
    last_planted: '2024-03-15',
    description: 'Main corn field with excellent drainage'
  },
  {
    id: 2,
    name: 'South Field',
    size: 18.2,
    soil_type: 'clay',
    current_crop: 'wheat',
    status: 'active',
    last_planted: '2024-02-20',
    description: 'Wheat field with clay soil'
  },
  {
    id: 3,
    name: 'East Field',
    size: 32.0,
    soil_type: 'sandy',
    current_crop: null,
    status: 'fallow',
    last_planted: '2023-10-15',
    description: 'Currently fallow, planning for soybeans'
  }
])

const newField = ref({
  name: '',
  size: '',
  soil_type: '',
  description: ''
})

const filteredFields = computed(() => {
  return fields.value.filter(field => {
    const matchesSearch = field.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         field.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || field.status === statusFilter.value
    const matchesCrop = !cropFilter.value || field.current_crop === cropFilter.value
    
    return matchesSearch && matchesStatus && matchesCrop
  })
})

const getStatusBadgeClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    fallow: 'bg-yellow-100 text-yellow-800',
    maintenance: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
  if (!date) return 'Never'
  return new Date(date).toLocaleDateString()
}

const viewField = (id) => {
  router.push(`/fields/${id}`)
}

const editField = (id) => {
  // Navigate to edit page or show edit modal
  console.log('Edit field:', id)
}

const createField = async () => {
  loading.value = true
  try {
    // API call to create field
    const field = {
      id: Date.now(),
      ...newField.value,
      status: 'active',
      current_crop: null,
      last_planted: null
    }
    fields.value.push(field)
    
    // Reset form
    newField.value = {
      name: '',
      size: '',
      soil_type: '',
      description: ''
    }
    showCreateModal.value = false
  } catch (error) {
    console.error('Error creating field:', error)
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  cropFilter.value = ''
}

onMounted(() => {
  // Load fields from API
})
</script>

<style scoped>
.fields-page {
  min-height: 100vh;
  background-color: #f8fafc;
}
</style>