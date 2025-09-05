<template>
  <div class="field-weather-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <nav class="text-sm text-gray-500 mb-2">
            <router-link to="/weather" class="hover:text-gray-700">Weather</router-link>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ field.name }}</span>
          </nav>
          <h1 class="text-3xl font-bold text-gray-900">{{ field.name }} Weather</h1>
          <p class="text-gray-600 mt-2">Detailed weather conditions for this field</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="refreshWeather"
            :disabled="loading"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
          >
            {{ loading ? 'Refreshing...' : 'Refresh' }}
          </button>
          <button
            @click="viewFieldDetails"
            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
          >
            View Field
          </button>
        </div>
      </div>

      <!-- Current Weather -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 text-2xl">🌡️</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ currentWeather.temperature }}°F</div>
              <div class="text-sm text-gray-600">Temperature</div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 text-2xl">💧</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ currentWeather.humidity }}%</div>
              <div class="text-sm text-gray-600">Humidity</div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 text-2xl">🌧️</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ currentWeather.rainfall }} in</div>
              <div class="text-sm text-gray-600">Rainfall (24h)</div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 text-2xl">💨</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ currentWeather.wind_speed }} mph</div>
              <div class="text-sm text-gray-600">Wind Speed</div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Weather Chart -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Temperature & Humidity (24h)</h2>
            <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
              <span class="text-gray-500">Temperature and humidity chart placeholder</span>
            </div>
          </div>

          <!-- Rainfall Chart -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Rainfall (7 days)</h2>
            <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
              <span class="text-gray-500">Rainfall chart placeholder</span>
            </div>
          </div>

          <!-- Weather History -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Weather History</h2>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">High</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Low</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rainfall</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Humidity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wind</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="day in weatherHistory" :key="day.date">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ formatDate(day.date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ day.high }}°F
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ day.low }}°F
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ day.rainfall }} in
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ day.humidity }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ day.wind_speed }} mph
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Field Details -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Field Details</h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Size:</span>
                <span class="font-medium">{{ field.size }} acres</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Soil Type:</span>
                <span class="font-medium">{{ field.soil_type }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Current Crop:</span>
                <span class="font-medium">{{ field.current_crop || 'None' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Weather Station:</span>
                <span class="font-medium">{{ field.weather_station }}</span>
              </div>
            </div>
          </div>

          <!-- Growing Degree Days -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Growing Degree Days</h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Today:</span>
                <span class="font-medium">{{ gdd.today }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">This Week:</span>
                <span class="font-medium">{{ gdd.week }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">This Month:</span>
                <span class="font-medium">{{ gdd.month }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">This Season:</span>
                <span class="font-medium">{{ gdd.season }}</span>
              </div>
            </div>
          </div>

          <!-- Soil Conditions -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Soil Conditions</h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Soil Temperature:</span>
                <span class="font-medium">{{ soilConditions.temperature }}°F</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Soil Moisture:</span>
                <span class="font-medium">{{ soilConditions.moisture }}%</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">pH Level:</span>
                <span class="font-medium">{{ soilConditions.ph }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Last Updated:</span>
                <span class="font-medium">{{ formatDate(soilConditions.last_updated) }}</span>
              </div>
            </div>
          </div>

          <!-- Weather Alerts -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Field-Specific Alerts</h3>
            <div class="space-y-3">
              <div
                v-for="alert in fieldAlerts"
                :key="alert.id"
                :class="getAlertClass(alert.severity)"
                class="p-3 rounded-lg border-l-4"
              >
                <div class="font-medium text-sm">{{ alert.title }}</div>
                <div class="text-xs text-gray-600 mt-1">{{ alert.description }}</div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <button
                @click="viewHistoricalData"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                📊 View Historical Data
              </button>
              <button
                @click="setFieldAlerts"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                🔔 Set Field Alerts
              </button>
              <button
                @click="exportFieldData"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                📤 Export Field Data
              </button>
              <button
                @click="compareWithOtherFields"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                🔄 Compare Fields
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const loading = ref(false)

const field = ref({
  id: null,
  name: '',
  size: 0,
  soil_type: '',
  current_crop: '',
  weather_station: ''
})

const currentWeather = ref({
  temperature: 72,
  humidity: 65,
  rainfall: 0.2,
  wind_speed: 5
})

const weatherHistory = ref([
  {
    date: '2024-03-25',
    high: 75,
    low: 55,
    rainfall: 0.1,
    humidity: 60,
    wind_speed: 8
  },
  {
    date: '2024-03-24',
    high: 73,
    low: 52,
    rainfall: 0.0,
    humidity: 58,
    wind_speed: 6
  },
  {
    date: '2024-03-23',
    high: 70,
    low: 48,
    rainfall: 0.3,
    humidity: 72,
    wind_speed: 12
  },
  {
    date: '2024-03-22',
    high: 68,
    low: 45,
    rainfall: 0.0,
    humidity: 55,
    wind_speed: 4
  },
  {
    date: '2024-03-21',
    high: 72,
    low: 50,
    rainfall: 0.0,
    humidity: 62,
    wind_speed: 7
  }
])

const gdd = ref({
  today: 15,
  week: 95,
  month: 420,
  season: 1250
})

const soilConditions = ref({
  temperature: 58,
  moisture: 45,
  ph: 6.8,
  last_updated: '2024-03-25T10:00:00Z'
})

const fieldAlerts = ref([
  {
    id: 1,
    title: 'Low Soil Moisture',
    description: 'Soil moisture below optimal level',
    severity: 'warning'
  },
  {
    id: 2,
    title: 'Temperature Alert',
    description: 'Expected frost tonight',
    severity: 'danger'
  }
])

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getAlertClass = (severity) => {
  const classes = {
    warning: 'bg-yellow-50 border-yellow-400',
    info: 'bg-blue-50 border-blue-400',
    danger: 'bg-red-50 border-red-400'
  }
  return classes[severity] || 'bg-gray-50 border-gray-400'
}

const refreshWeather = async () => {
  loading.value = true
  try {
    // API call to refresh weather data
    await new Promise(resolve => setTimeout(resolve, 1000)) // Simulate API call
  } catch (error) {
    console.error('Error refreshing weather:', error)
  } finally {
    loading.value = false
  }
}

const viewFieldDetails = () => {
  router.push(`/fields/${field.value.id}`)
}

const viewHistoricalData = () => {
  // Navigate to historical data page
  console.log('View historical data')
}

const setFieldAlerts = () => {
  // Show field alerts settings modal
  console.log('Set field alerts')
}

const exportFieldData = () => {
  // Export field weather data
  console.log('Export field data')
}

const compareWithOtherFields = () => {
  // Navigate to field comparison page
  console.log('Compare with other fields')
}

onMounted(() => {
  const fieldId = route.params.id
  // Load field weather data from API
  loadFieldWeatherData(fieldId)
})

const loadFieldWeatherData = async (id) => {
  try {
    // API call to load field weather data
    // For now, using mock data
    field.value = {
      id: id,
      name: 'North Field',
      size: 25.5,
      soil_type: 'loamy',
      current_crop: 'corn',
      weather_station: 'North Station'
    }
  } catch (error) {
    console.error('Error loading field weather data:', error)
  }
}
</script>

<style scoped>
.field-weather-page {
  min-height: 100vh;
  background-color: #f8fafc;
}
</style>