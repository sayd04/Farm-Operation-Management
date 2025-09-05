<template>
  <div class="task-detail-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <nav class="text-sm text-gray-500 mb-2">
            <router-link to="/tasks" class="hover:text-gray-700">Tasks</router-link>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ task.title }}</span>
          </nav>
          <h1 class="text-3xl font-bold text-gray-900">{{ task.title }}</h1>
          <p class="text-gray-600 mt-2">{{ task.description }}</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="toggleTaskCompletion"
            :class="task.completed ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
            class="text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            {{ task.completed ? 'Mark Incomplete' : 'Mark Complete' }}
          </button>
          <button
            @click="editTask"
            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
          >
            Edit Task
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Task Overview -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Task Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ task.priority }}</div>
                <div class="text-sm text-gray-600">Priority</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ task.status }}</div>
                <div class="text-sm text-gray-600">Status</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ task.estimated_hours || 'N/A' }}</div>
                <div class="text-sm text-gray-600">Est. Hours</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ daysUntilDue }}</div>
                <div class="text-sm text-gray-600">Days Until Due</div>
              </div>
            </div>
          </div>

          <!-- Task Details -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Task Details</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <p class="text-gray-900">{{ task.description || 'No description provided' }}</p>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Field</label>
                  <p class="text-gray-900">{{ task.field_name || 'No field assigned' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                  <p class="text-gray-900">{{ task.assigned_to || 'Unassigned' }}</p>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Created Date</label>
                  <p class="text-gray-900">{{ formatDate(task.created_at) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                  <p class="text-gray-900">{{ formatDate(task.due_date) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Time Tracking -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Time Tracking</h2>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-gray-600">Estimated Hours:</span>
                <span class="font-medium">{{ task.estimated_hours || 'Not set' }} hours</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600">Actual Hours:</span>
                <span class="font-medium">{{ task.actual_hours || 0 }} hours</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600">Progress:</span>
                <span class="font-medium">{{ timeProgress }}%</span>
              </div>
              
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: `${timeProgress}%` }"
                ></div>
              </div>
              
              <div class="flex space-x-3">
                <button
                  @click="startTimer"
                  :disabled="timerRunning"
                  class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
                >
                  {{ timerRunning ? 'Timer Running...' : 'Start Timer' }}
                </button>
                <button
                  @click="stopTimer"
                  :disabled="!timerRunning"
                  class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50"
                >
                  Stop Timer
                </button>
                <button
                  @click="addTime"
                  class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                  Add Time
                </button>
              </div>
            </div>
          </div>

          <!-- Comments -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Comments</h2>
            <div class="space-y-4">
              <div
                v-for="comment in comments"
                :key="comment.id"
                class="border-l-4 border-blue-500 pl-4 py-2"
              >
                <div class="flex justify-between items-start mb-2">
                  <span class="font-medium text-gray-900">{{ comment.author }}</span>
                  <span class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</span>
                </div>
                <p class="text-gray-700">{{ comment.content }}</p>
              </div>
              
              <div class="mt-4">
                <textarea
                  v-model="newComment"
                  rows="3"
                  placeholder="Add a comment..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
                <button
                  @click="addComment"
                  :disabled="!newComment.trim()"
                  class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                >
                  Add Comment
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Task Actions -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <button
                @click="assignTask"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                👤 Assign Task
              </button>
              <button
                @click="duplicateTask"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                📋 Duplicate Task
              </button>
              <button
                @click="viewField"
                v-if="task.field_id"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                🌾 View Field
              </button>
              <button
                @click="createRelatedTask"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
              >
                ➕ Create Related Task
              </button>
            </div>
          </div>

          <!-- Task History -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Task History</h3>
            <div class="space-y-3">
              <div
                v-for="history in taskHistory"
                :key="history.id"
                class="flex items-start space-x-3"
              >
                <div class="flex-shrink-0">
                  <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 text-xs">{{ getHistoryIcon(history.type) }}</span>
                  </div>
                </div>
                <div class="flex-1">
                  <div class="text-sm text-gray-900">{{ history.description }}</div>
                  <div class="text-xs text-gray-500">{{ formatDate(history.created_at) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Related Tasks -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Related Tasks</h3>
            <div class="space-y-3">
              <div
                v-for="relatedTask in relatedTasks"
                :key="relatedTask.id"
                class="p-3 border border-gray-200 rounded-lg"
              >
                <div class="font-medium text-gray-900">{{ relatedTask.title }}</div>
                <div class="text-sm text-gray-600">{{ relatedTask.status }}</div>
                <div class="text-xs text-gray-500">{{ formatDate(relatedTask.due_date) }}</div>
              </div>
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

const task = ref({
  id: null,
  title: '',
  description: '',
  field_id: null,
  field_name: '',
  priority: '',
  status: '',
  due_date: '',
  assigned_to: '',
  estimated_hours: 0,
  actual_hours: 0,
  completed: false,
  created_at: ''
})

const timerRunning = ref(false)
const newComment = ref('')

const comments = ref([
  {
    id: 1,
    author: 'John Smith',
    content: 'Started working on this task. Will need to check weather conditions first.',
    created_at: '2024-03-25T10:00:00Z'
  },
  {
    id: 2,
    author: 'Mike Johnson',
    content: 'Weather looks good for tomorrow. Should be able to complete this task.',
    created_at: '2024-03-25T14:30:00Z'
  }
])

const taskHistory = ref([
  {
    id: 1,
    type: 'created',
    description: 'Task created',
    created_at: '2024-03-20T09:00:00Z'
  },
  {
    id: 2,
    type: 'assigned',
    description: 'Assigned to John Smith',
    created_at: '2024-03-20T09:15:00Z'
  },
  {
    id: 3,
    type: 'started',
    description: 'Task started',
    created_at: '2024-03-25T10:00:00Z'
  }
])

const relatedTasks = ref([
  {
    id: 2,
    title: 'Check irrigation system',
    status: 'in_progress',
    due_date: '2024-03-30'
  },
  {
    id: 4,
    title: 'Monitor field conditions',
    status: 'pending',
    due_date: '2024-04-05'
  }
])

const daysUntilDue = computed(() => {
  if (!task.value.due_date) return 'N/A'
  const today = new Date()
  const dueDate = new Date(task.value.due_date)
  const diffTime = dueDate - today
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays > 0 ? diffDays : 0
})

const timeProgress = computed(() => {
  if (!task.value.estimated_hours || task.value.estimated_hours === 0) return 0
  return Math.min((task.value.actual_hours / task.value.estimated_hours) * 100, 100)
})

const getHistoryIcon = (type) => {
  const icons = {
    created: '➕',
    assigned: '👤',
    started: '▶️',
    completed: '✅',
    updated: '✏️'
  }
  return icons[type] || '📝'
}

const formatDate = (date) => {
  if (!date) return 'Not set'
  return new Date(date).toLocaleDateString()
}

const toggleTaskCompletion = () => {
  task.value.completed = !task.value.completed
  task.value.status = task.value.completed ? 'completed' : 'pending'
  // API call to update task
}

const editTask = () => {
  // Navigate to edit page or show edit modal
  console.log('Edit task')
}

const startTimer = () => {
  timerRunning.value = true
  // Start timer logic
}

const stopTimer = () => {
  timerRunning.value = false
  // Stop timer and add time to actual_hours
}

const addTime = () => {
  // Show modal to manually add time
  console.log('Add time')
}

const addComment = () => {
  if (!newComment.value.trim()) return
  
  const comment = {
    id: Date.now(),
    author: 'Current User',
    content: newComment.value,
    created_at: new Date().toISOString()
  }
  comments.value.push(comment)
  newComment.value = ''
}

const assignTask = () => {
  // Show assign task modal
  console.log('Assign task')
}

const duplicateTask = () => {
  // Duplicate task logic
  console.log('Duplicate task')
}

const viewField = () => {
  if (task.value.field_id) {
    router.push(`/fields/${task.value.field_id}`)
  }
}

const createRelatedTask = () => {
  // Navigate to create task with pre-filled field
  router.push('/tasks/create')
}

onMounted(() => {
  const taskId = route.params.id
  // Load task data from API
  loadTaskData(taskId)
})

const loadTaskData = async (id) => {
  try {
    // API call to load task data
    // For now, using mock data
    task.value = {
      id: id,
      title: 'Apply herbicide to North Field',
      description: 'Apply pre-emergent herbicide to prevent weed growth',
      field_id: 1,
      field_name: 'North Field',
      priority: 'high',
      status: 'pending',
      due_date: '2024-04-01',
      assigned_to: 'John Smith',
      estimated_hours: 4,
      actual_hours: 1.5,
      completed: false,
      created_at: '2024-03-20T09:00:00Z'
    }
  } catch (error) {
    console.error('Error loading task data:', error)
  }
}
</script>

<style scoped>
.task-detail-page {
  min-height: 100vh;
  background-color: #f8fafc;
}
</style>