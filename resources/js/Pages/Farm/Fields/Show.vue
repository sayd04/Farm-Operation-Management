<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-4">
            <li>
              <router-link to="/fields" class="text-gray-400 hover:text-gray-500">
                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="sr-only">Fields</span>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-4 text-sm font-medium text-gray-500">{{ field.name || 'Field Details' }}</span>
              </div>
            </li>
          </ol>
        </nav>
        <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          {{ field.name || 'Unnamed Field' }}
        </h2>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          :to="`/fields/${field.id}/edit`"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Edit
        </router-link>
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

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Field Information -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Field Information</h3>
          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Size</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ field.size }} acres</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Soil Type</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ field.soil_type }}
                </span>
              </dd>
            </div>
            <div v-if="field.location?.address" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Address</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ field.location.address }}</dd>
            </div>
            <div v-if="field.description" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Description</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ field.description }}</dd>
            </div>
          </dl>
        </div>

        <!-- Plantings -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Plantings</h3>
            <router-link
              to="/plantings/create"
              class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
            >
              Add Planting
            </router-link>
          </div>
          
          <div v-if="field.plantings?.length === 0" class="text-center py-6">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No plantings</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a planting to this field.</p>
          </div>
          
          <div v-else class="space-y-4">
            <div
              v-for="planting in field.plantings"
              :key="planting.id"
              class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="text-sm font-medium text-gray-900">{{ planting.crop_name }}</h4>
                  <p class="text-sm text-gray-500">{{ planting.variety }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm text-gray-900">{{ planting.planting_date }}</p>
                  <p class="text-sm text-gray-500">{{ planting.seed_quantity }} {{ planting.seed_unit }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Weather -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Current Weather</h3>
          <div v-if="weather" class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Temperature</span>
              <span class="text-sm font-medium text-gray-900">{{ weather.temperature }}°C</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Humidity</span>
              <span class="text-sm font-medium text-gray-900">{{ weather.humidity }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Rainfall</span>
              <span class="text-sm font-medium text-gray-900">{{ weather.rainfall }}mm</span>
            </div>
          </div>
          <div v-else class="text-center py-4">
            <p class="text-sm text-gray-500">No weather data available</p>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Plantings</span>
              <span class="text-sm font-medium text-gray-900">{{ field.plantings?.length || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Active Plantings</span>
              <span class="text-sm font-medium text-gray-900">{{ activePlantings }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Harvests</span>
              <span class="text-sm font-medium text-gray-900">{{ totalHarvests }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const field = ref({})
const weather = ref(null)
const loading = ref(false)

const activePlantings = computed(() => {
  if (!field.value.plantings) return 0
  const now = new Date()
  return field.value.plantings.filter(planting => {
    const harvestDate = new Date(planting.expected_harvest_date)
    return harvestDate > now
  }).length
})

const totalHarvests = computed(() => {
  if (!field.value.plantings) return 0
  return field.value.plantings.reduce((total, planting) => {
    return total + (planting.harvests?.length || 0)
  }, 0)
})

const fetchField = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/fields/${route.params.id}`)
    field.value = response.data.field
  } catch (error) {
    console.error('Error fetching field:', error)
  } finally {
    loading.value = false
  }
}

const fetchWeather = async () => {
  try {
    const response = await axios.get(`/api/weather/fields/${route.params.id}/current`)
    weather.value = response.data.weather
  } catch (error) {
    console.error('Error fetching weather:', error)
  }
}

onMounted(() => {
  fetchField()
  fetchWeather()
})
</script>