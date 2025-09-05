<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-4">
            <li>
              <router-link to="/tasks" class="text-gray-400 hover:text-gray-500">
                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="sr-only">Tasks</span>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-4 text-sm font-medium text-gray-500">{{ task.title }}</span>
              </div>
            </li>
          </ol>
        </nav>
        <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          {{ task.title }}
        </h2>
        <div class="mt-1 flex items-center space-x-4">
          <span :class="getPriorityBadgeClass(task.priority)" class="text-sm">
            {{ task.priority }}
          </span>
          <span :class="getStatusBadgeClass(task.status)" class="text-sm">
            {{ task.status.replace('_', ' ') }}
          </span>
        </div>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="updateTaskStatus"
          :disabled="loading"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          {{ loading ? 'Updating...' : 'Update Status' }}
        </button>
        <router-link
          :to="`/tasks/${task.id}/edit`"
          class="ml-3 bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Edit
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Task Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Task Information</h3>
          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ task.laborer?.name || 'Unassigned' }}
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Due Date</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(task.due_date) }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Estimated Hours</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ task.estimated_hours || 'Not set' }}
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Hours Worked</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ task.hours_worked || 0 }}
              </dd>
            </div>
            <div v-if="task.related_entity_type" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Related Entity</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ task.related_entity_type }} (ID: {{ task.related_entity_id }})
              </dd>
            </div>
            <div v-if="task.description" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Description</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ task.description }}</dd>
            </div>
            <div v-if="task.notes" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Notes</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ task.notes }}</dd>
            </div>
          </dl>
        </div>

        <!-- Status Update Form -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Update Status</h3>
          <form @submit.prevent="updateStatus" class="space-y-4">
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
              <select
                id="status"
                v-model="statusForm.status"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div>
              <label for="hours_worked" class="block text-sm font-medium text-gray-700">Hours Worked</label>
              <input
                id="hours_worked"
                v-model="statusForm.hours_worked"
                type="number"
                step="0.5"
                min="0"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
            </div>
            <div>
              <button
                type="submit"
                :disabled="statusLoading"
                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                {{ statusLoading ? 'Updating...' : 'Update Status' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Progress -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Progress</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Days Since Created</span>
              <span class="text-sm font-medium text-gray-900">{{ daysSinceCreated }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Days to Due Date</span>
              <span class="text-sm font-medium text-gray-900">{{ daysToDue }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Progress</span>
              <span class="text-sm font-medium text-gray-900">{{ progressPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-indigo-600 h-2 rounded-full"
                :style="{ width: `${progressPercentage}%` }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Laborer Information -->
        <div v-if="task.laborer" class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Laborer</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Name</span>
              <span class="text-sm font-medium text-gray-900">{{ task.laborer.name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Skill Level</span>
              <span class="text-sm font-medium text-gray-900">{{ task.laborer.skill_level }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Hourly Rate</span>
              <span class="text-sm font-medium text-gray-900">${{ task.laborer.hourly_rate }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Status</span>
              <span :class="getLaborerStatusBadgeClass(task.laborer.status)" class="text-sm">
                {{ task.laborer.status.replace('_', ' ') }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const task = ref({})
const loading = ref(false)
const statusLoading = ref(false)

const statusForm = reactive({
  status: '',
  hours_worked: ''
})

const daysSinceCreated = computed(() => {
  if (!task.value.created_at) return 0
  const createdDate = new Date(task.value.created_at)
  const now = new Date()
  return Math.floor((now - createdDate) / (1000 * 60 * 60 * 24))
})

const daysToDue = computed(() => {
  if (!task.value.due_date) return 'Not set'
  const dueDate = new Date(task.value.due_date)
  const now = new Date()
  const days = Math.ceil((dueDate - now) / (1000 * 60 * 60 * 24))
  return days > 0 ? days : 'Overdue'
})

const progressPercentage = computed(() => {
  if (!task.value.estimated_hours || !task.value.hours_worked) return 0
  return Math.min((task.value.hours_worked / task.value.estimated_hours) * 100, 100)
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const getPriorityBadgeClass = (priority) => {
  const classes = {
    low: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800',
    medium: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800',
    high: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800',
    urgent: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'
  }
  return classes[priority] || classes.medium
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800',
    in_progress: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800',
    completed: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800',
    cancelled: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'
  }
  return classes[status] || classes.pending
}

const getLaborerStatusBadgeClass = (status) => {
  const classes = {
    active: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800',
    inactive: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800',
    on_leave: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'
  }
  return classes[status] || classes.inactive
}

const fetchTask = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/tasks/${route.params.id}`)
    task.value = response.data.task
    statusForm.status = task.value.status
    statusForm.hours_worked = task.value.hours_worked || ''
  } catch (error) {
    console.error('Error fetching task:', error)
  } finally {
    loading.value = false
  }
}

const updateStatus = async () => {
  statusLoading.value = true
  try {
    await axios.patch(`/api/tasks/${route.params.id}/status`, statusForm)
    await fetchTask() // Refresh task data
  } catch (error) {
    console.error('Error updating task status:', error)
  } finally {
    statusLoading.value = false
  }
}

const updateTaskStatus = () => {
  // This could open a modal or navigate to a status update page
  console.log('Update task status clicked')
}

onMounted(() => {
  fetchTask()
})
</script>