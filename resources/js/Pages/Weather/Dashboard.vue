<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Weather Dashboard
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Monitor weather conditions across your fields
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="refreshWeather"
          :disabled="loading"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ loading ? 'Refreshing...' : 'Refresh' }}
        </button>
      </div>
    </div>

    <!-- Current Weather Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Temperature</dt>
                <dd class="text-lg font-medium text-gray-900">{{ currentWeather?.temperature || '--' }}°C</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Humidity</dt>
                <dd class="text-lg font-medium text-gray-900">{{ currentWeather?.humidity || '--' }}%</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Rainfall (24h)</dt>
                <dd class="text-lg font-medium text-gray-900">{{ currentWeather?.rainfall || '--' }}mm</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Wind Speed</dt>
                <dd class="text-lg font-medium text-gray-900">{{ currentWeather?.wind_speed || '--' }} km/h</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Weather Alerts -->
    <div v-if="alerts.length > 0" class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Weather Alerts</h3>
        <div class="space-y-3">
          <div
            v-for="alert in alerts"
            :key="alert.id"
            class="rounded-md p-4"
            :class="getAlertClass(alert.severity)"
          >
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5" :class="getAlertIconClass(alert.severity)" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium" :class="getAlertTextClass(alert.severity)">
                  {{ alert.type.replace('_', ' ').toUpperCase() }}
                </h3>
                <div class="mt-2 text-sm" :class="getAlertTextClass(alert.severity)">
                  <p>{{ alert.message }}</p>
                  <p class="mt-1">Date: {{ formatDate(alert.date) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Field Weather -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Field Weather Conditions</h3>
        
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>

        <div v-else-if="fieldWeather.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No field weather data</h3>
          <p class="mt-1 text-sm text-gray-500">Weather data will appear here when available.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="field in fieldWeather"
            :key="field.id"
            class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
          >
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-sm font-medium text-gray-900">{{ field.name }}</h4>
              <router-link
                :to="`/weather/fields/${field.id}`"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
              >
                View Details
              </router-link>
            </div>
            
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Temperature</span>
                <span class="font-medium">{{ field.weather?.temperature || '--' }}°C</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Humidity</span>
                <span class="font-medium">{{ field.weather?.humidity || '--' }}%</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Rainfall</span>
                <span class="font-medium">{{ field.weather?.rainfall || '--' }}mm</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Wind</span>
                <span class="font-medium">{{ field.weather?.wind_speed || '--' }} km/h</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const currentWeather = ref(null)
const fieldWeather = ref([])
const alerts = ref([])
const loading = ref(false)

const fetchWeatherData = async () => {
  loading.value = true
  try {
    // Fetch current weather
    const weatherResponse = await axios.get('/api/weather/current')
    currentWeather.value = weatherResponse.data.weather

    // Fetch field weather
    const fieldsResponse = await axios.get('/api/fields')
    fieldWeather.value = fieldsResponse.data.fields

    // Fetch weather alerts
    const alertsResponse = await axios.get('/api/weather/alerts')
    alerts.value = alertsResponse.data.alerts
  } catch (error) {
    console.error('Error fetching weather data:', error)
  } finally {
    loading.value = false
  }
}

const refreshWeather = () => {
  fetchWeatherData()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const getAlertClass = (severity) => {
  const classes = {
    warning: 'bg-yellow-50 border border-yellow-200',
    error: 'bg-red-50 border border-red-200',
    info: 'bg-blue-50 border border-blue-200'
  }
  return classes[severity] || classes.info
}

const getAlertIconClass = (severity) => {
  const classes = {
    warning: 'text-yellow-400',
    error: 'text-red-400',
    info: 'text-blue-400'
  }
  return classes[severity] || classes.info
}

const getAlertTextClass = (severity) => {
  const classes = {
    warning: 'text-yellow-800',
    error: 'text-red-800',
    info: 'text-blue-800'
  }
  return classes[severity] || classes.info
}

onMounted(() => {
  fetchWeatherData()
})
</script>