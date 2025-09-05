<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Admin Dashboard
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          System overview and administration tools
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="refreshData"
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

    <!-- Stats Overview -->
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

    <!-- System Health -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">System Health</h3>
        
        <div v-if="systemHealth" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold" :class="getHealthColor(systemHealth.database.status)">
              {{ systemHealth.database.status === 'connected' ? '✓' : '✗' }}
            </div>
            <div class="text-sm text-gray-500">Database</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold" :class="getHealthColor(systemHealth.cache.status)">
              {{ systemHealth.cache.status === 'connected' ? '✓' : '✗' }}
            </div>
            <div class="text-sm text-gray-500">Cache</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold" :class="getHealthColor(systemHealth.storage.status)">
              {{ systemHealth.storage.status === 'connected' ? '✓' : '✗' }}
            </div>
            <div class="text-sm text-gray-500">Storage</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">
              {{ systemHealth.memory_usage.current }}
            </div>
            <div class="text-sm text-gray-500">Memory Usage</div>
          </div>
        </div>
        
        <div v-else class="text-center py-8">
          <p class="text-gray-500">Loading system health...</p>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Users -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Users</h3>
            <router-link
              to="/admin/users"
              class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
            >
              View All
            </router-link>
          </div>
          
          <div v-if="recentUsers.length === 0" class="text-center py-6">
            <p class="text-gray-500">No recent users</p>
          </div>
          
          <div v-else class="space-y-3">
            <div
              v-for="user in recentUsers"
              :key="user.id"
              class="flex items-center space-x-3"
            >
              <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                  <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
              </div>
              <div class="flex-shrink-0">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ user.role }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- System Alerts -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">System Alerts</h3>
          
          <div v-if="alerts.length === 0" class="text-center py-6">
            <p class="text-gray-500">No system alerts</p>
          </div>
          
          <div v-else class="space-y-3">
            <div
              v-for="alert in alerts"
              :key="alert.id"
              class="rounded-md p-3"
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
                    {{ alert.title }}
                  </h3>
                  <div class="mt-2 text-sm" :class="getAlertTextClass(alert.severity)">
                    <p>{{ alert.message }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <router-link
            to="/admin/users"
            class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <div class="text-center">
              <svg class="mx-auto h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
              </svg>
              <h4 class="mt-2 text-sm font-medium text-gray-900">Manage Users</h4>
              <p class="mt-1 text-sm text-gray-500">View and manage user accounts</p>
            </div>
          </router-link>
          
          <router-link
            to="/admin/settings"
            class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <div class="text-center">
              <svg class="mx-auto h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <h4 class="mt-2 text-sm font-medium text-gray-900">System Settings</h4>
              <p class="mt-1 text-sm text-gray-500">Configure system settings</p>
            </div>
          </router-link>
          
          <button
            @click="clearCache"
            class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <div class="text-center">
              <svg class="mx-auto h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              <h4 class="mt-2 text-sm font-medium text-gray-900">Clear Cache</h4>
              <p class="mt-1 text-sm text-gray-500">Clear system cache</p>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const stats = ref({})
const systemHealth = ref(null)
const recentUsers = ref([])
const alerts = ref([])
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

const fetchRecentUsers = async () => {
  try {
    const response = await axios.get('/api/admin/users?limit=5')
    recentUsers.value = response.data.users || []
  } catch (error) {
    console.error('Error fetching recent users:', error)
  }
}

const fetchAlerts = async () => {
  try {
    const response = await axios.get('/api/admin/alerts')
    alerts.value = response.data.alerts || []
  } catch (error) {
    console.error('Error fetching alerts:', error)
  }
}

const refreshData = async () => {
  loading.value = true
  try {
    await Promise.all([
      fetchStats(),
      fetchSystemHealth(),
      fetchRecentUsers(),
      fetchAlerts()
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

const getHealthColor = (status) => {
  return status === 'connected' ? 'text-green-600' : 'text-red-600'
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
  refreshData()
})
</script>