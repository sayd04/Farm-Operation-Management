<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Weather Reports
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Analyze weather patterns and their impact on farming
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="exportReport"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export Report
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Report Filters</h3>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label for="field" class="block text-sm font-medium text-gray-700">Field</label>
          <select
            id="field"
            v-model="filters.field"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Fields</option>
            <option v-for="field in fields" :key="field.id" :value="field.id">{{ field.name }}</option>
          </select>
        </div>
        <div>
          <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
          <input
            id="date_from"
            v-model="filters.date_from"
            type="date"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
          <input
            id="date_to"
            v-model="filters.date_to"
            type="date"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div class="flex items-end">
          <button
            @click="generateReport"
            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Generate Report
          </button>
        </div>
      </div>
    </div>

    <!-- Weather Summary -->
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
                <dt class="text-sm font-medium text-gray-500 truncate">Avg Temperature</dt>
                <dd class="text-lg font-medium text-gray-900">{{ summary.avg_temperature || '--' }}°C</dd>
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
                <dt class="text-sm font-medium text-gray-500 truncate">Total Rainfall</dt>
                <dd class="text-lg font-medium text-gray-900">{{ summary.total_rainfall || '--' }}mm</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Avg Wind Speed</dt>
                <dd class="text-lg font-medium text-gray-900">{{ summary.avg_wind_speed || '--' }} km/h</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Avg Humidity</dt>
                <dd class="text-lg font-medium text-gray-900">{{ summary.avg_humidity || '--' }}%</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Temperature Chart -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Temperature Trends</h3>
      <div class="h-64 flex items-end justify-between space-x-1">
        <div
          v-for="(day, index) in temperatureData"
          :key="index"
          class="flex-1 flex flex-col items-center"
        >
          <div
            class="bg-blue-500 rounded-t w-full"
            :style="{ height: `${((day.temperature - minTemp) / (maxTemp - minTemp)) * 200}px` }"
            :title="`${day.date}: ${day.temperature}°C`"
          ></div>
          <div class="text-xs text-gray-500 mt-1">{{ formatDateShort(day.date) }}</div>
        </div>
      </div>
    </div>

    <!-- Rainfall Chart -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Rainfall Distribution</h3>
      <div class="h-64 flex items-end justify-between space-x-1">
        <div
          v-for="(day, index) in rainfallData"
          :key="index"
          class="flex-1 flex flex-col items-center"
        >
          <div
            class="bg-green-500 rounded-t w-full"
            :style="{ height: `${(day.rainfall / maxRainfall) * 200}px` }"
            :title="`${day.date}: ${day.rainfall}mm`"
          ></div>
          <div class="text-xs text-gray-500 mt-1">{{ formatDateShort(day.date) }}</div>
        </div>
      </div>
    </div>

    <!-- Weather Alerts -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Weather Alerts</h3>
      <div v-if="alerts.length === 0" class="text-center py-8">
        <p class="text-gray-500">No weather alerts for the selected period</p>
      </div>
      <div v-else class="space-y-3">
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

    <!-- Weather Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Temperature Statistics</h3>
        <div class="space-y-3">
          <div class="flex justify-between">
            <span class="text-sm text-gray-500">Highest Temperature</span>
            <span class="text-sm font-medium text-gray-900">{{ stats.temperature.max }}°C</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm text-gray-500">Lowest Temperature</span>
            <span class="text-sm font-medium text-gray-900">{{ stats.temperature.min }}°C</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm text-gray-500">Average Temperature</span>
            <span class="text-sm font-medium text-gray-900">{{ stats.temperature.avg }}°C</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm text-gray-500">Temperature Range</span>
            <span class="text-sm font-medium text-gray-900">{{ stats.temperature.range }}°C</span>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Rainfall Statistics</h3>
        <div class="space-y-3">
          <div class="flex justify-between">
            <span class="text-sm text-gray-500">Total Rainfall</span>
            <span class="text-sm font-medium text-gray-900">{{ stats.rainfall.total }}mm</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm text-gray-500">Average Daily</span>
            <span class="text-sm font-medium text-gray-900">{{ stats.rainfall.daily_avg }}mm</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm text-gray-500">Rainy Days</span>
            <span class="text-sm font-medium text-gray-900">{{ stats.rainfall.rainy_days }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm text-gray-500">Heaviest Rainfall</span>
            <span class="text-sm font-medium text-gray-900">{{ stats.rainfall.max_daily }}mm</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'

const summary = ref({})
const temperatureData = ref([])
const rainfallData = ref([])
const alerts = ref([])
const stats = ref({})
const fields = ref([])
const loading = ref(false)

const filters = reactive({
  field: '',
  date_from: '',
  date_to: ''
})

const minTemp = computed(() => {
  if (temperatureData.value.length === 0) return 0
  return Math.min(...temperatureData.value.map(day => day.temperature))
})

const maxTemp = computed(() => {
  if (temperatureData.value.length === 0) return 1
  return Math.max(...temperatureData.value.map(day => day.temperature))
})

const maxRainfall = computed(() => {
  if (rainfallData.value.length === 0) return 1
  return Math.max(...rainfallData.value.map(day => day.rainfall))
})

const fetchFields = async () => {
  try {
    const response = await axios.get('/api/fields')
    fields.value = response.data.fields || []
  } catch (error) {
    console.error('Error fetching fields:', error)
  }
}

const generateReport = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.field) params.append('field', filters.field)
    if (filters.date_from) params.append('date_from', filters.date_from)
    if (filters.date_to) params.append('date_to', filters.date_to)
    
    const response = await axios.get(`/api/reports/weather?${params}`)
    const data = response.data
    
    summary.value = data.summary || {}
    temperatureData.value = data.temperature_data || []
    rainfallData.value = data.rainfall_data || []
    alerts.value = data.alerts || []
    stats.value = data.statistics || {}
  } catch (error) {
    console.error('Error generating weather report:', error)
  } finally {
    loading.value = false
  }
}

const exportReport = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.field) params.append('field', filters.field)
    if (filters.date_from) params.append('date_from', filters.date_from)
    if (filters.date_to) params.append('date_to', filters.date_to)
    
    const response = await axios.get(`/api/reports/weather/export?${params}`, { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'weather-report.pdf')
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error exporting report:', error)
  }
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

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const formatDateShort = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

onMounted(() => {
  // Set default date range to last 30 days
  const today = new Date()
  const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000))
  
  filters.date_from = thirtyDaysAgo.toISOString().split('T')[0]
  filters.date_to = today.toISOString().split('T')[0]
  
  fetchFields()
  generateReport()
})
</script>