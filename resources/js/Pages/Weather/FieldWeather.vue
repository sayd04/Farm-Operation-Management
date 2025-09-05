<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-4">
            <li>
              <router-link to="/weather" class="text-gray-400 hover:text-gray-500">
                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="sr-only">Weather</span>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-4 text-sm font-medium text-gray-500">{{ field.name }}</span>
              </div>
            </li>
          </ol>
        </nav>
        <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          {{ field.name }} Weather
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Weather conditions and history for this field
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

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Current Weather -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Current Weather</h3>
          <div v-if="currentWeather" class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">{{ currentWeather.temperature }}°C</div>
              <div class="text-sm text-gray-500">Temperature</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">{{ currentWeather.humidity }}%</div>
              <div class="text-sm text-gray-500">Humidity</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">{{ currentWeather.rainfall }}mm</div>
              <div class="text-sm text-gray-500">Rainfall (24h)</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">{{ currentWeather.wind_speed }} km/h</div>
              <div class="text-sm text-gray-500">Wind Speed</div>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <p class="text-gray-500">No current weather data available</p>
          </div>
        </div>

        <!-- Weather History Chart -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Weather History</h3>
            <select
              v-model="selectedPeriod"
              @change="fetchWeatherHistory"
              class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="7">Last 7 days</option>
              <option value="30">Last 30 days</option>
              <option value="90">Last 90 days</option>
            </select>
          </div>
          
          <div v-if="weatherHistory.length === 0" class="text-center py-8">
            <p class="text-gray-500">No weather history data available</p>
          </div>
          
          <div v-else class="space-y-4">
            <!-- Temperature Chart -->
            <div>
              <h4 class="text-sm font-medium text-gray-700 mb-2">Temperature</h4>
              <div class="h-32 bg-gray-50 rounded-lg flex items-end justify-between p-2">
                <div
                  v-for="(day, index) in weatherHistory"
                  :key="index"
                  class="bg-blue-500 rounded-t"
                  :style="{ height: `${(day.avg_temperature / 40) * 100}%`, width: `${100 / weatherHistory.length}%` }"
                  :title="`${day.date}: ${day.avg_temperature}°C`"
                ></div>
              </div>
            </div>
            
            <!-- Rainfall Chart -->
            <div>
              <h4 class="text-sm font-medium text-gray-700 mb-2">Rainfall</h4>
              <div class="h-32 bg-gray-50 rounded-lg flex items-end justify-between p-2">
                <div
                  v-for="(day, index) in weatherHistory"
                  :key="index"
                  class="bg-blue-600 rounded-t"
                  :style="{ height: `${Math.min((day.total_rainfall / 50) * 100, 100)}%`, width: `${100 / weatherHistory.length}%` }"
                  :title="`${day.date}: ${day.total_rainfall}mm`"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Weather Alerts -->
        <div v-if="alerts.length > 0" class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Weather Alerts</h3>
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

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Field Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Field Information</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Size</span>
              <span class="text-sm font-medium text-gray-900">{{ field.size }} acres</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Soil Type</span>
              <span class="text-sm font-medium text-gray-900">{{ field.soil_type }}</span>
            </div>
            <div v-if="field.location?.address" class="flex justify-between">
              <span class="text-sm text-gray-500">Location</span>
              <span class="text-sm font-medium text-gray-900">{{ field.location.address }}</span>
            </div>
          </div>
        </div>

        <!-- Weather Statistics -->
        <div v-if="statistics" class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Avg Temperature</span>
              <span class="text-sm font-medium text-gray-900">{{ statistics.temperature.average }}°C</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Avg Humidity</span>
              <span class="text-sm font-medium text-gray-900">{{ statistics.humidity.average }}%</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Rainfall</span>
              <span class="text-sm font-medium text-gray-900">{{ statistics.rainfall.total }}mm</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Avg Wind Speed</span>
              <span class="text-sm font-medium text-gray-900">{{ statistics.wind_speed.average }} km/h</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const field = ref({})
const currentWeather = ref(null)
const weatherHistory = ref([])
const alerts = ref([])
const statistics = ref(null)
const loading = ref(false)
const selectedPeriod = ref('7')

const fetchField = async () => {
  try {
    const response = await axios.get(`/api/fields/${route.params.id}`)
    field.value = response.data.field
  } catch (error) {
    console.error('Error fetching field:', error)
  }
}

const fetchCurrentWeather = async () => {
  try {
    const response = await axios.get(`/api/weather/fields/${route.params.id}/current`)
    currentWeather.value = response.data.weather
  } catch (error) {
    console.error('Error fetching current weather:', error)
  }
}

const fetchWeatherHistory = async () => {
  try {
    const response = await axios.get(`/api/weather/fields/${route.params.id}/trends?group_by=daily&date_from=${getDateFrom()}`)
    weatherHistory.value = response.data.trends
  } catch (error) {
    console.error('Error fetching weather history:', error)
  }
}

const fetchWeatherAlerts = async () => {
  try {
    const response = await axios.get(`/api/weather/fields/${route.params.id}/alerts`)
    alerts.value = response.data.alerts
  } catch (error) {
    console.error('Error fetching weather alerts:', error)
  }
}

const fetchWeatherStatistics = async () => {
  try {
    const response = await axios.get(`/api/weather/fields/${route.params.id}/statistics`)
    statistics.value = response.data.statistics
  } catch (error) {
    console.error('Error fetching weather statistics:', error)
  }
}

const refreshWeather = async () => {
  loading.value = true
  try {
    await Promise.all([
      fetchCurrentWeather(),
      fetchWeatherHistory(),
      fetchWeatherAlerts(),
      fetchWeatherStatistics()
    ])
  } finally {
    loading.value = false
  }
}

const getDateFrom = () => {
  const days = parseInt(selectedPeriod.value)
  const date = new Date()
  date.setDate(date.getDate() - days)
  return date.toISOString().split('T')[0]
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
  fetchField()
  refreshWeather()
})
</script>