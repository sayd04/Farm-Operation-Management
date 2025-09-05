<template>
  <div class="weather-reports-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Weather Reports</h1>
          <p class="text-gray-600 mt-2">Analyze weather patterns and their impact on your farm</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="exportReport"
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            Export Report
          </button>
          <button
            @click="generateReport"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Generate Report
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Report Filters</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
            <select
              v-model="dateRange"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="last7days">Last 7 Days</option>
              <option value="last30days">Last 30 Days</option>
              <option value="last3months">Last 3 Months</option>
              <option value="lastyear">Last Year</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Field</label>
            <select
              v-model="selectedField"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Fields</option>
              <option value="north">North Field</option>
              <option value="south">South Field</option>
              <option value="east">East Field</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Weather Station</label>
            <select
              v-model="selectedStation"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Stations</option>
              <option value="main">Main Station</option>
              <option value="north">North Station</option>
              <option value="south">South Station</option>
            </select>
          </div>
          <div class="flex items-end">
            <button
              @click="updateReport"
              class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              Update Report
            </button>
          </div>
        </div>
      </div>

      <!-- Weather Summary -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 text-2xl">🌡️</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ weatherSummary.avgTemperature }}°F</div>
              <div class="text-sm text-gray-600">Avg Temperature</div>
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
              <div class="text-2xl font-bold text-gray-900">{{ weatherSummary.totalRainfall }}"</div>
              <div class="text-sm text-gray-600">Total Rainfall</div>
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
              <div class="text-2xl font-bold text-gray-900">{{ weatherSummary.avgWindSpeed }} mph</div>
              <div class="text-sm text-gray-600">Avg Wind Speed</div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 text-2xl">☀️</span>
              </div>
            </div>
            <div class="ml-4">
              <div class="text-2xl font-bold text-gray-900">{{ weatherSummary.sunshineHours }}h</div>
              <div class="text-sm text-gray-600">Sunshine Hours</div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Temperature Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Temperature Trends</h2>
          <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
            <span class="text-gray-500">Temperature chart placeholder</span>
          </div>
        </div>

        <!-- Rainfall Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Rainfall Distribution</h2>
          <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
            <span class="text-gray-500">Rainfall chart placeholder</span>
          </div>
        </div>
      </div>

      <!-- Growing Degree Days -->
      <div class="mt-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Growing Degree Days (GDD)</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
              <div class="text-3xl font-bold text-green-600">{{ gddData.today }}</div>
              <div class="text-sm text-gray-600">Today</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600">{{ gddData.week }}</div>
              <div class="text-sm text-gray-600">This Week</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-purple-600">{{ gddData.month }}</div>
              <div class="text-sm text-gray-600">This Month</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-yellow-600">{{ gddData.season }}</div>
              <div class="text-sm text-gray-600">This Season</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Weather Events -->
      <div class="mt-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Significant Weather Events</h2>
          <div class="space-y-4">
            <div
              v-for="event in weatherEvents"
              :key="event.id"
              class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg"
            >
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                  <span class="text-blue-600 text-sm">{{ getEventIcon(event.type) }}</span>
                </div>
              </div>
              <div class="flex-1">
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="font-medium text-gray-900">{{ event.title }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ event.description }}</p>
                  </div>
                  <span class="text-sm text-gray-500">{{ formatDate(event.date) }}</span>
                </div>
                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                  <span>Duration: {{ event.duration }}</span>
                  <span>Intensity: {{ event.intensity }}</span>
                  <span>Impact: {{ event.impact }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Weather Impact Analysis -->
      <div class="mt-8">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold mb-4">Weather Impact Analysis</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <h3 class="font-medium text-gray-900 mb-3">Crop Development</h3>
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Growth Stage</span>
                  <span class="font-medium">Vegetative</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Days to Maturity</span>
                  <span class="font-medium">45 days</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Stress Level</span>
                  <span class="font-medium text-green-600">Low</span>
                </div>
              </div>
            </div>
            <div>
              <h3 class="font-medium text-gray-900 mb-3">Field Conditions</h3>
              <div class="space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Soil Moisture</span>
                  <span class="font-medium">Optimal</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Field Workability</span>
                  <span class="font-medium">Good</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Disease Risk</span>
                  <span class="font-medium text-yellow-600">Moderate</span>
                </div>
              </div>
            </div>
            <div>
              <h3 class="font-medium text-gray-900 mb-3">Recommendations</h3>
              <div class="space-y-2">
                <div class="text-sm text-gray-600">
                  • Monitor soil moisture levels
                </div>
                <div class="text-sm text-gray-600">
                  • Consider fungicide application
                </div>
                <div class="text-sm text-gray-600">
                  • Plan irrigation schedule
                </div>
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

const dateRange = ref('last30days')
const selectedField = ref('')
const selectedStation = ref('')

const weatherSummary = ref({
  avgTemperature: 72,
  totalRainfall: 3.2,
  avgWindSpeed: 8.5,
  sunshineHours: 245
})

const gddData = ref({
  today: 15,
  week: 95,
  month: 420,
  season: 1250
})

const weatherEvents = ref([
  {
    id: 1,
    type: 'rain',
    title: 'Heavy Rainfall',
    description: 'Significant rainfall event with 1.2 inches in 6 hours',
    date: '2024-03-20T14:00:00Z',
    duration: '6 hours',
    intensity: 'Heavy',
    impact: 'Positive'
  },
  {
    id: 2,
    type: 'wind',
    title: 'Strong Winds',
    description: 'Wind speeds reached 25 mph with gusts up to 35 mph',
    date: '2024-03-18T10:30:00Z',
    duration: '4 hours',
    intensity: 'Strong',
    impact: 'Neutral'
  },
  {
    id: 3,
    type: 'frost',
    title: 'Frost Warning',
    description: 'Temperatures dropped to 32°F, potential frost damage',
    date: '2024-03-15T06:00:00Z',
    duration: '2 hours',
    intensity: 'Light',
    impact: 'Negative'
  }
])

const getEventIcon = (type) => {
  const icons = {
    rain: '🌧️',
    wind: '💨',
    frost: '❄️',
    heat: '🌡️',
    storm: '⛈️'
  }
  return icons[type] || '🌤️'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const updateReport = () => {
  // Update report based on selected filters
  console.log('Update report:', { 
    dateRange: dateRange.value, 
    field: selectedField.value, 
    station: selectedStation.value 
  })
}

const generateReport = () => {
  // Generate new report
  console.log('Generate report')
}

const exportReport = () => {
  // Export report logic
  console.log('Export report')
}

onMounted(() => {
  // Load weather data from API
})
</script>

<style scoped>
.weather-reports-page {
  min-height: 100vh;
  background-color: #f8fafc;
}
</style>