<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Fields Management
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Manage your farm fields and their details
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          to="/fields/create"
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Field
        </router-link>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
          <input
            id="search"
            v-model="filters.search"
            type="text"
            placeholder="Search fields..."
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="soil_type" class="block text-sm font-medium text-gray-700">Soil Type</label>
          <select
            id="soil_type"
            v-model="filters.soil_type"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Soil Types</option>
            <option value="clay">Clay</option>
            <option value="sandy">Sandy</option>
            <option value="loamy">Loamy</option>
            <option value="silty">Silty</option>
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

    <!-- Fields Grid -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="fields.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No fields</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by creating a new field.</p>
      <div class="mt-6">
        <router-link
          to="/fields/create"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Field
        </router-link>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="field in fields"
        :key="field.id"
        class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200"
      >
        <div class="p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">{{ field.name || 'Unnamed Field' }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              {{ field.soil_type }}
            </span>
          </div>
          <p class="mt-2 text-sm text-gray-500">{{ field.size }} acres</p>
          <p v-if="field.location?.address" class="mt-1 text-sm text-gray-500">
            {{ field.location.address }}
          </p>
          <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-500">
              {{ field.plantings?.length || 0 }} plantings
            </div>
            <div class="flex space-x-2">
              <router-link
                :to="`/fields/${field.id}`"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
              >
                View
              </router-link>
              <button
                @click="deleteField(field.id)"
                class="text-red-600 hover:text-red-900 text-sm font-medium"
              >
                Delete
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

const fields = ref([])
const loading = ref(false)
const filters = reactive({
  search: '',
  soil_type: ''
})

const fetchFields = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.soil_type) params.append('soil_type', filters.soil_type)
    
    const response = await axios.get(`/api/fields?${params}`)
    fields.value = response.data.fields
  } catch (error) {
    console.error('Error fetching fields:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchFields()
}

const deleteField = async (fieldId) => {
  if (!confirm('Are you sure you want to delete this field?')) return
  
  try {
    await axios.delete(`/api/fields/${fieldId}`)
    fields.value = fields.value.filter(field => field.id !== fieldId)
  } catch (error) {
    console.error('Error deleting field:', error)
    alert('Error deleting field. Please try again.')
  }
}

onMounted(() => {
  fetchFields()
})
</script>