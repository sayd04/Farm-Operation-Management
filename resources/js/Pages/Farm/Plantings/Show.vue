<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-4">
            <li>
              <router-link to="/plantings" class="text-gray-400 hover:text-gray-500">
                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="sr-only">Plantings</span>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-4 text-sm font-medium text-gray-500">{{ planting.crop_name }}</span>
              </div>
            </li>
          </ol>
        </nav>
        <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          {{ planting.crop_name }}
        </h2>
        <p v-if="planting.variety" class="mt-1 text-sm text-gray-500">{{ planting.variety }}</p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          :to="`/plantings/${planting.id}/edit`"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Edit
        </router-link>
        <router-link
          to="/harvests/create"
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Harvest
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Planting Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Planting Information</h3>
          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Field</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <router-link
                  :to="`/fields/${planting.field?.id}`"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  {{ planting.field?.name || 'Unknown Field' }}
                </router-link>
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Planting Date</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(planting.planting_date) }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Expected Harvest</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ planting.expected_harvest_date ? formatDate(planting.expected_harvest_date) : 'Not set' }}
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Seed Quantity</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ planting.seed_quantity }} {{ planting.seed_unit }}</dd>
            </div>
            <div v-if="planting.spacing" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Spacing</dt>
              <dd class="mt-1 text-sm text-gray-900">
                Row: {{ planting.spacing.row }}m, Plant: {{ planting.spacing.plant }}m
              </dd>
            </div>
            <div v-if="planting.notes" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Notes</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ planting.notes }}</dd>
            </div>
          </dl>
        </div>

        <!-- Harvests -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Harvests</h3>
            <router-link
              to="/harvests/create"
              class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
            >
              Add Harvest
            </router-link>
          </div>
          
          <div v-if="planting.harvests?.length === 0" class="text-center py-6">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No harvests yet</h3>
            <p class="mt-1 text-sm text-gray-500">Add a harvest when you start collecting crops.</p>
          </div>
          
          <div v-else class="space-y-4">
            <div
              v-for="harvest in planting.harvests"
              :key="harvest.id"
              class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="text-sm font-medium text-gray-900">{{ formatDate(harvest.harvest_date) }}</h4>
                  <p class="text-sm text-gray-500">{{ harvest.quantity }} {{ harvest.unit }}</p>
                </div>
                <div class="text-right">
                  <p v-if="harvest.quality_grade" class="text-sm text-gray-900">
                    Grade: {{ harvest.quality_grade }}
                  </p>
                  <p v-if="harvest.total_value" class="text-sm text-gray-500">
                    ${{ harvest.total_value }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Status -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Days Since Planting</span>
              <span class="text-sm font-medium text-gray-900">{{ daysSincePlanting }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Days to Harvest</span>
              <span class="text-sm font-medium text-gray-900">{{ daysToHarvest }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Status</span>
              <span :class="statusBadgeClass">{{ statusText }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Harvests</span>
              <span class="text-sm font-medium text-gray-900">{{ planting.harvests?.length || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Quantity</span>
              <span class="text-sm font-medium text-gray-900">{{ totalHarvestQuantity }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Value</span>
              <span class="text-sm font-medium text-gray-900">${{ totalHarvestValue }}</span>
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

const planting = ref({})
const loading = ref(false)

const daysSincePlanting = computed(() => {
  if (!planting.value.planting_date) return 0
  const plantingDate = new Date(planting.value.planting_date)
  const now = new Date()
  return Math.floor((now - plantingDate) / (1000 * 60 * 60 * 24))
})

const daysToHarvest = computed(() => {
  if (!planting.value.expected_harvest_date) return 'Not set'
  const harvestDate = new Date(planting.value.expected_harvest_date)
  const now = new Date()
  const days = Math.ceil((harvestDate - now) / (1000 * 60 * 60 * 24))
  return days > 0 ? days : 'Overdue'
})

const statusText = computed(() => {
  if (!planting.value.expected_harvest_date) return 'Unknown'
  const harvestDate = new Date(planting.value.expected_harvest_date)
  const now = new Date()
  
  if (harvestDate < now) {
    return 'Ready for Harvest'
  } else {
    return 'Growing'
  }
})

const statusBadgeClass = computed(() => {
  if (!planting.value.expected_harvest_date) return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800'
  
  const harvestDate = new Date(planting.value.expected_harvest_date)
  const now = new Date()
  
  if (harvestDate < now) {
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'
  } else {
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'
  }
})

const totalHarvestQuantity = computed(() => {
  if (!planting.value.harvests) return 0
  return planting.value.harvests.reduce((total, harvest) => total + harvest.quantity, 0)
})

const totalHarvestValue = computed(() => {
  if (!planting.value.harvests) return 0
  return planting.value.harvests.reduce((total, harvest) => total + (harvest.total_value || 0), 0)
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const fetchPlanting = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/plantings/${route.params.id}`)
    planting.value = response.data.planting
  } catch (error) {
    console.error('Error fetching planting:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPlanting()
})
</script>