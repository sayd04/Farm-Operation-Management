<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Record Harvest</h1>
    <form @submit.prevent="save" class="bg-white shadow rounded-lg p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Planting</label>
          <select v-model="form.planting_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            <option disabled value="">Select planting</option>
            <option v-for="p in plantings" :key="p.id" :value="p.id">{{ p.varietal }} — {{ p.field?.name || p.field_id }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Date</label>
          <input v-model="form.date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Yield (kg)</label>
          <input v-model.number="form.yield_kg" type="number" min="0" step="0.1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Area (ha)</label>
          <input v-model.number="form.area_ha" type="number" min="0" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Moisture (%)</label>
          <input v-model.number="form.moisture_percent" type="number" min="0" max="100" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Broken Grains (%)</label>
          <input v-model.number="form.broken_grains_percent" type="number" min="0" max="100" step="0.1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Quality Notes</label>
          <input v-model="form.quality_notes" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
      </div>

      <div class="flex items-center justify-end">
        <button :disabled="saving" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
          {{ saving ? 'Saving...' : 'Record Harvest' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const plantings = ref([]);
const saving = ref(false);

const form = ref({
  planting_id: '',
  date: '',
  yield_kg: null,
  area_ha: null,
  moisture_percent: null,
  broken_grains_percent: null,
  quality_notes: ''
});

const save = async () => {
  saving.value = true;
  try {
    await axios.post('/api/harvests', form.value);
    window.history.back();
  } catch (e) {
    console.error('Failed to record harvest', e);
  } finally {
    saving.value = false;
  }
};

const loadPlantings = async () => {
  try {
    const res = await axios.get('/api/plantings');
    plantings.value = res.data?.data || res.data || [];
  } catch (e) {
    console.error('Failed to load plantings', e);
  }
};

onMounted(loadPlantings);
</script>

