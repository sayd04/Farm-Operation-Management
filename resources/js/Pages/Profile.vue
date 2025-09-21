<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
      <p class="mt-2 text-gray-600">
        Manage your account information and preferences
      </p>
    </div>

    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
      </div>
      
      <form @submit.prevent="handleUpdateProfile" class="px-6 py-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input
              id="name"
              v-model="profileForm.name"
              type="text"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            />
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input
              id="email"
              v-model="profileForm.email"
              type="email"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            />
          </div>
        </div>

        <div v-if="authStore.error" class="text-red-600 text-sm">
          {{ authStore.error }}
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="authStore.loading"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
          >
            {{ authStore.loading ? 'Updating...' : 'Update Profile' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Change Password Section -->
    <div class="mt-8 bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Change Password</h3>
      </div>
      
      <form @submit.prevent="handleChangePassword" class="px-6 py-6 space-y-6">
        <div>
          <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
          <input
            id="current_password"
            v-model="passwordForm.current_password"
            type="password"
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
          />
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
            <input
              id="new_password"
              v-model="passwordForm.new_password"
              type="password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            />
          </div>
          
          <div>
            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
            <input
              id="new_password_confirmation"
              v-model="passwordForm.new_password_confirmation"
              type="password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            />
          </div>
        </div>

        <div v-if="passwordError" class="text-red-600 text-sm">
          {{ passwordError }}
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="changingPassword"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
          >
            {{ changingPassword ? 'Changing...' : 'Change Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const profileForm = ref({
  name: '',
  email: ''
});

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
});

const changingPassword = ref(false);
const passwordError = ref('');

const handleUpdateProfile = async () => {
  try {
    await authStore.updateProfile(profileForm.value);
    // Success message could be shown here
  } catch (error) {
    console.error('Profile update error:', error);
  }
};

const handleChangePassword = async () => {
  changingPassword.value = true;
  passwordError.value = '';

  try {
    await authStore.changePassword(passwordForm.value);
    // Clear form on success
    passwordForm.value = {
      current_password: '',
      new_password: '',
      new_password_confirmation: ''
    };
    // Success message could be shown here
  } catch (error) {
    passwordError.value = error.response?.data?.message || 'Password change failed';
  } finally {
    changingPassword.value = false;
  }
};

onMounted(() => {
  // Initialize form with current user data
  if (authStore.user) {
    profileForm.value = {
      name: authStore.user.name || '',
      email: authStore.user.email || ''
    };
  }
});
</script>
</template>