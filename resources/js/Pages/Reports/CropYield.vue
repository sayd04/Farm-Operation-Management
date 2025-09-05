<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Crop Yield Reports
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Track crop production and yield performance
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
          <label for="crop" class="block text-sm font-medium text-gray-700">Crop</label>
          <select
            id="crop"
            v-model="filters.crop"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Crops</option>
            <option v-for="crop in crops" :key="crop" :value="crop">{{ crop }}</option>
          </select>
        </div>
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
          <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
          <select
            id="year"
            v-model="filters.year"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Years</option>
            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
          </select>
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

    <!-- Yield Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Yield</dt>
                <dd class="text-lg font-medium text-gray-900">{{ summary.total_yield || '0' }} {{ summary.unit || 'kg' }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Area</dt>
                <dd class="text-lg font-medium text-gray-900">{{ summary.total_area || '0' }} acres</dd>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Average Yield</dt>
                <dd class="text-lg font-medium text-gray-900">{{ summary.average_yield || '0' }} {{ summary.unit || 'kg' }}/acre</dd>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Value</dt>
                <dd class="text-lg font-medium text-gray-900">${{ summary.total_value || '0.00' }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Yield by Crop Chart -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Yield by Crop</h3>
      <div class="h-64 flex items-end justify-between space-x-2">
        <div
          v-for="(crop, index) in cropYields"
          :key="index"
          class="flex-1 flex flex-col items-center"
        >
          <div
            class="bg-green-500 rounded-t w-full mb-2"
            :style="{ height: `${(crop.yield / maxYield) * 200}px` }"
            :title="`${crop.name}: ${crop.yield} ${summary.unit || 'kg'}`"
          ></div>
          <div class="text-xs text-gray-500 text-center">{{ crop.name }}</div>
        </div>
      </div>
    </div>

    <!-- Yield by Field -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Yield by Field</h3>
        <div class="space-y-3">
          <div
            v-for="field in fieldYields"
            :key="field.id"
            class="flex items-center justify-between"
          >
            <div class="flex items-center">
              <div class="w-3 h-3 rounded-full mr-3" :style="{ backgroundColor: field.color }"></div>
              <span class="text-sm font-medium text-gray-900">{{ field.name }}</span>
            </div>
            <div class="text-right">
              <div class="text-sm font-medium text-gray-900">{{ field.yield }} {{ summary.unit || 'kg' }}</div>
              <div class="text-xs text-gray-500">{{ field.area }} acres</div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Yield Trends</h3>
        <div class="h-48 flex items-end justify-between space-x-1">
          <div
            v-for="(month, index) in yieldTrends"
            :key="index"
            class="flex-1 flex flex-col items-center"
          >
            <div
              class="bg-blue-500 rounded-t w-full"
              :style="{ height: `${(month.yield / maxTrendYield) * 150}px` }"
              :title="`${month.month}: ${month.yield} ${summary.unit || 'kg'}`"
            ></div>
            <div class="text-xs text-gray-500 mt-1">{{ month.month }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Harvest Records -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Harvest Records</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Crop
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Field
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Quantity
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Quality
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Value
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="harvest in recentHarvests" :key="harvest.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(harvest.harvest_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ harvest.crop_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ harvest.field?.name || 'Unknown Field' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ harvest.quantity }} {{ harvest.unit }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ harvest.quality_grade || 'N/A' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${{ harvest.total_value || '0.00' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'

const summary = ref({})
const cropYields = ref([])
const fieldYields = ref([])
const yieldTrends = ref([])
const recentHarvests = ref([])
const crops = ref([])
const fields = ref([])
const years = ref([])
const loading = ref(false)

const filters = reactive({
  crop: '',
  field: '',
  year: ''
})

const maxYield = computed(() => {
  if (cropYields.value.length === 0) return 1
  return Math.max(...cropYields.value.map(crop => crop.yield))
})

const maxTrendYield = computed(() => {
  if (yieldTrends.value.length === 0) return 1
  return Math.max(...yieldTrends.value.map(month => month.yield))
})

const fetchFilters = async () => {
  try {
    const [cropsResponse, fieldsResponse] = await Promise.all([
      axios.get('/api/crops'),
      axios.get('/api/fields')
    ])
    
    crops.value = cropsResponse.data.crops || []
    fields.value = fieldsResponse.data.fields || []
    
    // Generate years (last 5 years)
    const currentYear = new Date().getFullYear()
    years.value = Array.from({ length: 5 }, (_, i) => currentYear - i)
  } catch (error) {
    console.error('Error fetching filter data:', error)
  }
}

const generateReport = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.crop) params.append('crop', filters.crop)
    if (filters.field) params.append('field', filters.field)
    if (filters.year) params.append('year', filters.year)
    
    const response = await axios.get(`/api/reports/crop-yield?${params}`)
    const data = response.data
    
    summary.value = data.summary || {}
    cropYields.value = data.crop_yields || []
    fieldYields.value = data.field_yields || []
    yieldTrends.value = data.yield_trends || []
    recentHarvests.value = data.recent_harvests || []
  } catch (error) {
    console.error('Error generating crop yield report:', error)
  } finally {
    loading.value = false
  }
}

const exportReport = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.crop) params.append('crop', filters.crop)
    if (filters.field) params.append('field', filters.field)
    if (filters.year) params.append('year', filters.year)
    
    const response = await axios.get(`/api/reports/crop-yield/export?${params}`, { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'crop-yield-report.pdf')
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error exporting report:', error)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  fetchFilters()
  generateReport()
})
</script>