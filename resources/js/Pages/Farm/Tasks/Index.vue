<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Tasks Management
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Track and manage farm tasks and labor assignments
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          to="/tasks/create"
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Task
        </router-link>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
          <input
            id="search"
            v-model="filters.search"
            type="text"
            placeholder="Search tasks..."
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select
            id="status"
            v-model="filters.status"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
          <select
            id="priority"
            v-model="filters.priority"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">All Priorities</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Tasks List -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="tasks.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by creating a new task.</p>
      <div class="mt-6">
        <router-link
          to="/tasks/create"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Task
        </router-link>
      </div>
    </div>

    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="task in tasks" :key="task.id">
          <div class="px-4 py-4 flex items-center justify-between hover:bg-gray-50">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div :class="getPriorityIconClass(task.priority)" class="h-10 w-10 rounded-full flex items-center justify-center">
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <div class="flex items-center">
                  <p class="text-sm font-medium text-gray-900">{{ task.title }}</p>
                  <span :class="getPriorityBadgeClass(task.priority)" class="ml-2">
                    {{ task.priority }}
                  </span>
                </div>
                <div class="flex items-center mt-1">
                  <p class="text-sm text-gray-500">{{ task.laborer?.name || 'Unassigned' }}</p>
                  <span class="mx-2 text-gray-300">•</span>
                  <p class="text-sm text-gray-500">Due: {{ formatDate(task.due_date) }}</p>
                </div>
                <p v-if="task.description" class="text-sm text-gray-500 mt-1">{{ task.description }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <div class="text-right">
                <p v-if="task.estimated_hours" class="text-sm text-gray-900">
                  {{ task.estimated_hours }}h estimated
                </p>
                <p v-if="task.hours_worked" class="text-sm text-gray-500">
                  {{ task.hours_worked }}h worked
                </p>
              </div>
              <div class="flex items-center space-x-2">
                <span :class="getStatusBadgeClass(task.status)">
                  {{ task.status.replace('_', ' ') }}
                </span>
                <router-link
                  :to="`/tasks/${task.id}`"
                  class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                >
                  View
                </router-link>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const tasks = ref([])
const loading = ref(false)
const filters = reactive({
  search: '',
  status: '',
  priority: ''
})

const fetchTasks = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.priority) params.append('priority', filters.priority)
    
    const response = await axios.get(`/api/tasks?${params}`)
    tasks.value = response.data.tasks
  } catch (error) {
    console.error('Error fetching tasks:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchTasks()
}

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

const getPriorityIconClass = (priority) => {
  const classes = {
    low: 'bg-gray-100 text-gray-600',
    medium: 'bg-blue-100 text-blue-600',
    high: 'bg-yellow-100 text-yellow-600',
    urgent: 'bg-red-100 text-red-600'
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

onMounted(() => {
  fetchTasks()
})
</script>