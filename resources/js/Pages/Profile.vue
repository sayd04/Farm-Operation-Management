<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Profile
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Manage your account settings and preferences
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile Information -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Personal Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
          <form @submit.prevent="updateProfile" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input
                  id="name"
                  v-model="profileForm.name"
                  type="text"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                  id="email"
                  v-model="profileForm.email"
                  type="email"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input
                  id="phone"
                  v-model="profileForm.phone"
                  type="tel"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <input
                  id="role"
                  v-model="profileForm.role"
                  type="text"
                  disabled
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 sm:text-sm"
                />
              </div>
            </div>

            <div>
              <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
              <textarea
                id="address"
                v-model="profileForm.address"
                rows="3"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                placeholder="Enter your address..."
              ></textarea>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="profileLoading"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                {{ profileLoading ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
          <form @submit.prevent="changePassword" class="space-y-4">
            <div>
              <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
              <input
                id="current_password"
                v-model="passwordForm.current_password"
                type="password"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input
                  id="new_password"
                  v-model="passwordForm.new_password"
                  type="password"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
              <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input
                  id="new_password_confirmation"
                  v-model="passwordForm.new_password_confirmation"
                  type="password"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
              </div>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="passwordLoading"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                {{ passwordLoading ? 'Changing...' : 'Change Password' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Notification Preferences -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Preferences</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-medium text-gray-900">Email Notifications</h4>
                <p class="text-sm text-gray-500">Receive notifications via email</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  v-model="notifications.email"
                  type="checkbox"
                  class="sr-only peer"
                />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
              </label>
            </div>
            
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-medium text-gray-900">Weather Alerts</h4>
                <p class="text-sm text-gray-500">Get notified about weather changes</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  v-model="notifications.weather"
                  type="checkbox"
                  class="sr-only peer"
                />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
              </label>
            </div>
            
            <div class="flex items-center justify-between">
              <div>
                <h4 class="text-sm font-medium text-gray-900">Market Updates</h4>
                <p class="text-sm text-gray-500">Receive marketplace notifications</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  v-model="notifications.marketplace"
                  type="checkbox"
                  class="sr-only peer"
                />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
              </label>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button
              @click="saveNotifications"
              :disabled="notificationLoading"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
            >
              {{ notificationLoading ? 'Saving...' : 'Save Preferences' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Profile Picture -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Picture</h3>
          <div class="text-center">
            <div class="mx-auto h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center mb-4">
              <svg class="h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <button
              @click="uploadPhoto"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              Upload Photo
            </button>
          </div>
        </div>

        <!-- Account Statistics -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Account Statistics</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Member Since</span>
              <span class="text-sm font-medium text-gray-900">{{ formatDate(user.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Last Login</span>
              <span class="text-sm font-medium text-gray-900">{{ formatDate(user.last_login_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Fields</span>
              <span class="text-sm font-medium text-gray-900">{{ stats.total_fields || 0 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Total Sales</span>
              <span class="text-sm font-medium text-gray-900">${{ stats.total_sales || '0.00' }}</span>
            </div>
          </div>
        </div>

        <!-- Account Actions -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Account Actions</h3>
          <div class="space-y-3">
            <button
              @click="downloadData"
              class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
            >
              Download My Data
            </button>
            <button
              @click="deleteAccount"
              class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md"
            >
              Delete Account
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const user = ref({})
const stats = ref({})
const profileLoading = ref(false)
const passwordLoading = ref(false)
const notificationLoading = ref(false)

const profileForm = reactive({
  name: '',
  email: '',
  phone: '',
  role: '',
  address: ''
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const notifications = reactive({
  email: true,
  weather: true,
  marketplace: false
})

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/user')
    user.value = response.data.user
    
    // Populate form with user data
    profileForm.name = user.value.name
    profileForm.email = user.value.email
    profileForm.phone = user.value.phone || ''
    profileForm.role = user.value.role
    profileForm.address = user.value.address || ''
    
    // Load notification preferences
    notifications.email = user.value.notifications?.email ?? true
    notifications.weather = user.value.notifications?.weather ?? true
    notifications.marketplace = user.value.notifications?.marketplace ?? false
  } catch (error) {
    console.error('Error fetching user:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/user/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching user stats:', error)
  }
}

const updateProfile = async () => {
  profileLoading.value = true
  try {
    await axios.put('/api/user/profile', profileForm)
    alert('Profile updated successfully')
  } catch (error) {
    console.error('Error updating profile:', error)
    alert('Error updating profile. Please try again.')
  } finally {
    profileLoading.value = false
  }
}

const changePassword = async () => {
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    alert('New passwords do not match')
    return
  }
  
  passwordLoading.value = true
  try {
    await axios.put('/api/user/password', passwordForm)
    alert('Password changed successfully')
    
    // Clear form
    passwordForm.current_password = ''
    passwordForm.new_password = ''
    passwordForm.new_password_confirmation = ''
  } catch (error) {
    console.error('Error changing password:', error)
    alert('Error changing password. Please try again.')
  } finally {
    passwordLoading.value = false
  }
}

const saveNotifications = async () => {
  notificationLoading.value = true
  try {
    await axios.put('/api/user/notifications', notifications)
    alert('Notification preferences saved')
  } catch (error) {
    console.error('Error saving notifications:', error)
    alert('Error saving preferences. Please try again.')
  } finally {
    notificationLoading.value = false
  }
}

const uploadPhoto = () => {
  // Implement photo upload functionality
  console.log('Upload photo clicked')
}

const downloadData = async () => {
  try {
    const response = await axios.get('/api/user/data-export', { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'my-data.json')
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error downloading data:', error)
    alert('Error downloading data. Please try again.')
  }
}

const deleteAccount = () => {
  if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
    // Implement account deletion
    console.log('Delete account clicked')
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'Never'
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  fetchUser()
  fetchStats()
})
</script>