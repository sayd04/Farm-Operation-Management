<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Plantings Management
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Track and manage your crop plantings
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          to="/plantings/create"
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Planting
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
            placeholder="Search plantings..."
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="crop" class="block text-sm font-medium text-gray-700">Crop</label>
          <input
            id="crop"
            v-model="filters.crop"
            type="text"
            placeholder="Filter by crop..."
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
            <option value="active">Active</option>
            <option value="harvested">Harvested</option>
            <option value="failed">Failed</option>
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

    <!-- Plantings List -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="plantings.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No plantings</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by creating a new planting.</p>
      <div class="mt-6">
        <router-link
          to="/plantings/create"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Planting
        </router-link>
      </div>
    </div>

    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="planting in plantings" :key="planting.id">
          <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                  <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <div class="flex items-center">
                  <p class="text-sm font-medium text-gray-900">{{ planting.crop_name }}</p>
                  <span v-if="planting.variety" class="ml-2 text-sm text-gray-500">{{ planting.variety }}</span>
                </div>
                <div class="flex items-center mt-1">
                  <p class="text-sm text-gray-500">{{ planting.field?.name || 'Unknown Field' }}</p>
                  <span class="mx-2 text-gray-300">•</span>
                  <p class="text-sm text-gray-500">Planted: {{ formatDate(planting.planting_date) }}</p>
                </div>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <div class="text-right">
                <p class="text-sm text-gray-900">{{ planting.seed_quantity }} {{ planting.seed_unit }}</p>
                <p v-if="planting.expected_harvest_date" class="text-sm text-gray-500">
                  Harvest: {{ formatDate(planting.expected_harvest_date) }}
                </p>
              </div>
              <div class="flex items-center space-x-2">
                <span :class="getStatusBadgeClass(planting)">
                  {{ getStatusText(planting) }}
                </span>
                <router-link
                  :to="`/plantings/${planting.id}`"
                  class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                >
                  View
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
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const plantings = ref([])
const loading = ref(false)
const filters = reactive({
  search: '',
  crop: '',
  status: ''
})

const fetchPlantings = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.crop) params.append('crop', filters.crop)
    if (filters.status) params.append('status', filters.status)
    
    const response = await axios.get(`/api/plantings?${params}`)
    plantings.value = response.data.plantings
  } catch (error) {
    console.error('Error fetching plantings:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchPlantings()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const getStatusText = (planting) => {
  const now = new Date()
  const harvestDate = new Date(planting.expected_harvest_date)
  
  if (harvestDate < now) {
    return 'Ready for Harvest'
  } else {
    const daysLeft = Math.ceil((harvestDate - now) / (1000 * 60 * 60 * 24))
    return `${daysLeft} days left`
  }
}

const getStatusBadgeClass = (planting) => {
  const now = new Date()
  const harvestDate = new Date(planting.expected_harvest_date)
  
  if (harvestDate < now) {
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'
  } else {
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'
  }
}

onMounted(() => {
  fetchPlantings()
})
</script>