<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Set up your farm profile</h1>
      <p class="mt-2 text-gray-600">Complete this one-time setup to access all farmer features.</p>
    </div>

    <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Field Size (ha)</label>
          <input v-model.number="form.field_size" type="number" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Location</label>
          <input v-model="form.location" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Soil Type</label>
          <select v-model="form.soil_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
            <option disabled value="">Select soil type</option>
            <option>Clay</option>
            <option>Loam</option>
            <option>Sandy Loam</option>
            <option>Silt Loam</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Water Source</label>
          <select v-model="form.water_source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
            <option disabled value="">Select source</option>
            <option>Irrigation Canal</option>
            <option>Rainfed</option>
            <option>Deep Well</option>
            <option>River</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Preferred Rice Varietal</label>
          <select v-model="form.preferred_varietal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
            <option disabled value="">Select varietal</option>
            <option>IR64</option>
            <option>Jasmine</option>
            <option>Basmati</option>
            <option>NSIC Rc222</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Previous Yield (kg/ha)</label>
          <input v-model.number="form.previous_yield_kg_per_ha" type="number" min="0" step="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
      </div>

      <div class="flex items-center justify-between">
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        <button :disabled="loading" type="submit" class="ml-auto inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
          <svg v-if="loading" class="-ml-1 mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24"></svg>
          Save and Continue
        </button>
      </div>
    </form>
  </div>
  
</template>

<script setup>
import { reactive, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();

const form = reactive({
  field_size: null,
  location: '',
  soil_type: '',
  water_source: '',
  preferred_varietal: '',
  previous_yield_kg_per_ha: null,
  notes: ''
});

const loading = computed(() => auth.loading);
const error = computed(() => auth.error);

const submit = async () => {
  await auth.submitFarmProfile(form);
  router.push('/dashboard');
};
</script>

