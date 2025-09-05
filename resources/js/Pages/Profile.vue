<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Profile Settings</h2>
      </div>
      
      <div class="p-6">
        <!-- Profile Information -->
        <div class="mb-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Information</h3>
          
          <form @submit.prevent="updateProfile" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input
                  id="name"
                  v-model="profileForm.name"
                  type="text"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                />
              </div>
              
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                  id="email"
                  v-model="profileForm.email"
                  type="email"
                  required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                />
              </div>
              
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input
                  id="phone"
                  v-model="profileForm.phone"
                  type="tel"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                />
              </div>
              
              <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <input
                  id="role"
                  :value="authStore.user?.role"
                  type="text"
                  disabled
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500"
                />
              </div>
            </div>

            <!-- Address -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-2">Address</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                  <label for="street" class="block text-sm font-medium text-gray-700">Street</label>
                  <input
                    id="street"
                    v-model="profileForm.address.street"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                  />
                </div>
                
                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                  <input
                    id="city"
                    v-model="profileForm.address.city"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                  />
                </div>
                
                <div>
                  <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                  <input
                    id="state"
                    v-model="profileForm.address.state"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                  />
                </div>
                
                <div>
                  <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                  <input
                    id="country"
                    v-model="profileForm.address.country"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                  />
                </div>
                
                <div>
                  <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                  <input
                    id="postal_code"
                    v-model="profileForm.address.postal_code"
                    type="text"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
                  />
                </div>
              </div>
            </div>

            <div v-if="profileError" class="text-red-600 text-sm">
              {{ profileError }}
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="profileLoading"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
              >
                {{ profileLoading ? 'Updating...' : 'Update Profile' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password -->
        <div class="border-t border-gray-200 pt-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
          
          <form @submit.prevent="changePassword" class="space-y-4 max-w-md">
            <div>
              <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
              <input
                id="current_password"
                v-model="passwordForm.current_password"
                type="password"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
              />
            </div>
            
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
              <input
                id="password"
                v-model="passwordForm.password"
                type="password"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
              />
            </div>
            
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
              <input
                id="password_confirmation"
                v-model="passwordForm.password_confirmation"
                type="password"
                required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"
              />
            </div>

            <div v-if="passwordError" class="text-red-600 text-sm">
              {{ passwordError }}
            </div>

            <div>
              <button
                type="submit"
                :disabled="passwordLoading"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
              >
                {{ passwordLoading ? 'Changing...' : 'Change Password' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const profileForm = ref({
  name: '',
  email: '',
  phone: '',
  address: {
    street: '',
    city: '',
    state: '',
    country: '',
    postal_code: ''
  }
});

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
});

const profileLoading = ref(false);
const passwordLoading = ref(false);
const profileError = ref('');
const passwordError = ref('');

const updateProfile = async () => {
  profileLoading.value = true;
  profileError.value = '';

  try {
    await authStore.updateProfile(profileForm.value);
    // Show success message
    alert('Profile updated successfully!');
  } catch (error) {
    profileError.value = error.response?.data?.message || 'Failed to update profile';
  } finally {
    profileLoading.value = false;
  }
};

const changePassword = async () => {
  passwordLoading.value = true;
  passwordError.value = '';

  try {
    await authStore.changePassword(passwordForm.value);
    // Clear form and show success
    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    };
    alert('Password changed successfully!');
  } catch (error) {
    passwordError.value = error.response?.data?.message || 'Failed to change password';
  } finally {
    passwordLoading.value = false;
  }
};

onMounted(() => {
  // Populate form with current user data
  if (authStore.user) {
    profileForm.value = {
      name: authStore.user.name || '',
      email: authStore.user.email || '',
      phone: authStore.user.phone || '',
      address: {
        street: authStore.user.address?.street || '',
        city: authStore.user.address?.city || '',
        state: authStore.user.address?.state || '',
        country: authStore.user.address?.country || '',
        postal_code: authStore.user.address?.postal_code || ''
      }
    };
  }
});
</script>