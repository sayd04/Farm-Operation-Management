<template>
  <div class="field-detail-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <nav class="text-sm text-gray-500 mb-2">
            <router-link to="/fields" class="hover:text-gray-700">Fields</router-link>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ field.name }}</span>
          </nav>
          <h1 class="text-3xl font-bold text-gray-900">{{ field.name }}</h1>
          <p class="text-gray-600 mt-2">{{ field.description }}</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="editField"
            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
          >
            Edit Field
          </button>
          <button
            @click="addPlanting"
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            Add Planting
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Field Overview -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Field Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ field.size }}</div>
                <div class="text-sm text-gray-600">Acres</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ field.current_crop || 'None' }}</div>
                <div class="text-sm text-gray-600">Current Crop</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ field.soil_type }}</div>
                <div class="text-sm text-gray-600">Soil Type</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ field.status }}</div>
                <div class="text-sm text-gray-600">Status</div>
              </div>
            </div>
          </div>

          <!-- Current Planting -->
          <div v-if="currentPlanting" class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Current Planting</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="font-medium text-gray-900 mb-2">{{ currentPlanting.crop_type }}</h3>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Planted:</span>
                    <span class="font-medium">{{ formatDate(currentPlanting.planted_date) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Expected Harvest:</span>
                    <span class="font-medium">{{ formatDate(currentPlanting.expected_harvest) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Variety:</span>
                    <span class="font-medium">{{ currentPlanting.variety }}</span>
                  </div>
                </div>
              </div>
              <div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                  <div
                    class="bg-green-600 h-2 rounded-full"
                    :style="{ width: `${currentPlanting.growth_progress}%` }"
                  ></div>
                </div>
                <div class="text-sm text-gray-600">{{ currentPlanting.growth_progress }}% Complete</div>
              </div>
            </div>
          </div>

          <!-- Recent Activities -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Activities</h2>
            <div class="space-y-4">
              <div
                v-for="activity in recentActivities"
                :key="activity.id"
                class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 text-sm">{{ getActivityIcon(activity.type) }}</span>
                  </div>
                </div>
                <div class="flex-1">
                  <div class="font-medium text-gray-900">{{ activity.title }}</div>
                  <div class="text-sm text-gray-600">{{ activity.description }}</div>
                  <div class="text-xs text-gray-500 mt-1">{{ formatDate(activity.date) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Planting History -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Planting History</h2>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crop</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Planted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harvested</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yield</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="planting in plantingHistory" :key="planting.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ planting.crop_type }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(planting.planted_date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ planting.harvested_date ? formatDate(planting.harvested_date) : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ planting.yield ? `${planting.yield} bushels` : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="getStatusBadgeClass(planting.status)"
                        class="px-2 py-1 text-xs font-medium rounded-full"
                      >
                        {{ planting.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Field Map -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Field Map</h3>
            <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
              <span class="text-gray-500">Map placeholder</span>
            </div>
          </div>

          <!-- Weather -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Current Weather</h3>
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-gray-600">Temperature:</span>
                <span class="font-medium">72°F</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Humidity:</span>
                <span class="font-medium">65%</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Rainfall:</span>
                <span class="font-medium">0.2 in</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Wind:</span>
                <span class="font-medium">5 mph</span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <button
                @click="addTask"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                📋 Add Task
              </button>
              <button
                @click="viewWeather"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                🌤️ View Weather
              </button>
              <button
                @click="addNote"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                📝 Add Note
              </button>
              <button
                @click="viewReports"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                📊 View Reports
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const field = ref({
  id: null,
  name: '',
  description: '',
  size: 0,
  soil_type: '',
  current_crop: null,
  status: 'active'
})

const currentPlanting = ref({
  id: 1,
  crop_type: 'Corn',
  variety: 'Pioneer 1234',
  planted_date: '2024-03-15',
  expected_harvest: '2024-08-15',
  growth_progress: 45
})

const recentActivities = ref([
  {
    id: 1,
    type: 'planting',
    title: 'Corn planted',
    description: 'Planted Pioneer 1234 variety',
    date: '2024-03-15'
  },
  {
    id: 2,
    type: 'fertilizer',
    title: 'Fertilizer applied',
    description: 'Applied nitrogen fertilizer',
    date: '2024-03-20'
  },
  {
    id: 3,
    type: 'irrigation',
    title: 'Irrigation completed',
    description: 'Watered field for 2 hours',
    date: '2024-03-25'
  }
])

const plantingHistory = ref([
  {
    id: 1,
    crop_type: 'Corn',
    planted_date: '2024-03-15',
    harvested_date: null,
    yield: null,
    status: 'growing'
  },
  {
    id: 2,
    crop_type: 'Wheat',
    planted_date: '2023-10-01',
    harvested_date: '2024-02-15',
    yield: 45,
    status: 'completed'
  },
  {
    id: 3,
    crop_type: 'Soybeans',
    planted_date: '2023-05-01',
    harvested_date: '2023-09-15',
    yield: 38,
    status: 'completed'
  }
])

const getActivityIcon = (type) => {
  const icons = {
    planting: '🌱',
    fertilizer: '🌿',
    irrigation: '💧',
    harvest: '🌾',
    maintenance: '🔧'
  }
  return icons[type] || '📝'
}

const getStatusBadgeClass = (status) => {
  const classes = {
    growing: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    failed: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString()
}

const editField = () => {
  // Navigate to edit page or show edit modal
  console.log('Edit field')
}

const addPlanting = () => {
  // Navigate to add planting page
  router.push('/plantings/create')
}

const addTask = () => {
  // Navigate to add task page
  router.push('/tasks/create')
}

const viewWeather = () => {
  // Navigate to weather page
  router.push(`/weather/fields/${field.value.id}`)
}

const addNote = () => {
  // Show add note modal
  console.log('Add note')
}

const viewReports = () => {
  // Navigate to reports page
  router.push('/reports/crop-yield')
}

onMounted(() => {
  const fieldId = route.params.id
  // Load field data from API
  loadFieldData(fieldId)
})

const loadFieldData = async (id) => {
  try {
    // API call to load field data
    // For now, using mock data
    field.value = {
      id: id,
      name: 'North Field',
      description: 'Main corn field with excellent drainage',
      size: 25.5,
      soil_type: 'loamy',
      current_crop: 'corn',
      status: 'active'
    }
  } catch (error) {
    console.error('Error loading field data:', error)
  }
}
</script>

<style scoped>
.field-detail-page {
  min-height: 100vh;
  background-color: #f8fafc;
}
</style>