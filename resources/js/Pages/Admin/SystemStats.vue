<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          System Statistics
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Detailed system performance and usage statistics
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="refreshStats"
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

    <!-- System Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.total_users || '--' }}</dd>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Fields</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.total_fields || '--' }}</dd>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                <dd class="text-lg font-medium text-gray-900">${{ stats.total_sales || '--' }}</dd>
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
                <dt class="text-sm font-medium text-gray-500 truncate">Active Orders</dt>
                <dd class="text-lg font-medium text-gray-900">{{ stats.active_orders || '--' }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- System Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Memory Usage -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Memory Usage</h3>
        <div v-if="systemHealth" class="space-y-4">
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500">Current Usage</span>
              <span class="font-medium">{{ systemHealth.memory_usage.current }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500">Peak Usage</span>
              <span class="font-medium">{{ systemHealth.memory_usage.peak }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-red-600 h-2 rounded-full" style="width: 80%"></div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <p class="text-gray-500">Loading memory usage...</p>
        </div>
      </div>

      <!-- Disk Usage -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Disk Usage</h3>
        <div v-if="systemHealth" class="space-y-4">
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500">Used Space</span>
              <span class="font-medium">{{ systemHealth.disk_usage.used }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-yellow-600 h-2 rounded-full" :style="{ width: `${systemHealth.disk_usage.percentage}%` }"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-gray-500">Total:</span>
              <span class="font-medium ml-1">{{ systemHealth.disk_usage.total }}</span>
            </div>
            <div>
              <span class="text-gray-500">Free:</span>
              <span class="font-medium ml-1">{{ systemHealth.disk_usage.free }}</span>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <p class="text-gray-500">Loading disk usage...</p>
        </div>
      </div>
    </div>

    <!-- Database Statistics -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Database Statistics</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-gray-900">{{ dbStats.users || '--' }}</div>
          <div class="text-sm text-gray-500">Users</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-gray-900">{{ dbStats.fields || '--' }}</div>
          <div class="text-sm text-gray-500">Fields</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-gray-900">{{ dbStats.plantings || '--' }}</div>
          <div class="text-sm text-gray-500">Plantings</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-gray-900">{{ dbStats.sales || '--' }}</div>
          <div class="text-sm text-gray-500">Sales</div>
        </div>
      </div>
    </div>

    <!-- System Health Status -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">System Health Status</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="text-center p-4 border rounded-lg">
          <div class="text-2xl font-bold mb-2" :class="getHealthColor(systemHealth?.database?.status)">
            {{ systemHealth?.database?.status === 'connected' ? '✓' : '✗' }}
          </div>
          <div class="text-sm font-medium text-gray-900">Database</div>
          <div class="text-xs text-gray-500">{{ systemHealth?.database?.message || 'Unknown' }}</div>
        </div>
        <div class="text-center p-4 border rounded-lg">
          <div class="text-2xl font-bold mb-2" :class="getHealthColor(systemHealth?.cache?.status)">
            {{ systemHealth?.cache?.status === 'connected' ? '✓' : '✗' }}
          </div>
          <div class="text-sm font-medium text-gray-900">Cache</div>
          <div class="text-xs text-gray-500">{{ systemHealth?.cache?.message || 'Unknown' }}</div>
        </div>
        <div class="text-center p-4 border rounded-lg">
          <div class="text-2xl font-bold mb-2" :class="getHealthColor(systemHealth?.storage?.status)">
            {{ systemHealth?.storage?.status === 'connected' ? '✓' : '✗' }}
          </div>
          <div class="text-sm font-medium text-gray-900">Storage</div>
          <div class="text-xs text-gray-500">{{ systemHealth?.storage?.message || 'Unknown' }}</div>
        </div>
      </div>
    </div>

    <!-- System Actions -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">System Actions</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button
          @click="clearCache"
          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          <div class="text-center">
            <svg class="mx-auto h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <h4 class="mt-2 text-sm font-medium text-gray-900">Clear Cache</h4>
            <p class="mt-1 text-sm text-gray-500">Clear application cache</p>
          </div>
        </button>
        
        <button
          @click="optimizeDatabase"
          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          <div class="text-center">
            <svg class="mx-auto h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
            </svg>
            <h4 class="mt-2 text-sm font-medium text-gray-900">Optimize Database</h4>
            <p class="mt-1 text-sm text-gray-500">Optimize database performance</p>
          </div>
        </button>
        
        <button
          @click="generateReport"
          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          <div class="text-center">
            <svg class="mx-auto h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h4 class="mt-2 text-sm font-medium text-gray-900">Generate Report</h4>
            <p class="mt-1 text-sm text-gray-500">Generate system report</p>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({})
const systemHealth = ref(null)
const dbStats = ref({})
const loading = ref(false)

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/admin/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const fetchSystemHealth = async () => {
  try {
    const response = await axios.get('/api/admin/system/health')
    systemHealth.value = response.data
  } catch (error) {
    console.error('Error fetching system health:', error)
  }
}

const fetchDatabaseStats = async () => {
  try {
    const response = await axios.get('/api/admin/database/stats')
    dbStats.value = response.data
  } catch (error) {
    console.error('Error fetching database stats:', error)
  }
}

const refreshStats = async () => {
  loading.value = true
  try {
    await Promise.all([
      fetchStats(),
      fetchSystemHealth(),
      fetchDatabaseStats()
    ])
  } finally {
    loading.value = false
  }
}

const clearCache = async () => {
  try {
    await axios.post('/api/admin/system/clear-cache')
    alert('Cache cleared successfully')
  } catch (error) {
    console.error('Error clearing cache:', error)
    alert('Error clearing cache')
  }
}

const optimizeDatabase = async () => {
  try {
    await axios.post('/api/admin/database/optimize')
    alert('Database optimization completed')
  } catch (error) {
    console.error('Error optimizing database:', error)
    alert('Error optimizing database')
  }
}

const generateReport = async () => {
  try {
    const response = await axios.get('/api/admin/reports/system', { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'system-report.pdf')
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error generating report:', error)
    alert('Error generating report')
  }
}

const getHealthColor = (status) => {
  return status === 'connected' ? 'text-green-600' : 'text-red-600'
}

onMounted(() => {
  refreshStats()
})
</script>