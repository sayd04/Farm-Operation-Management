<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Plantings</h1>
        <p class="mt-2 text-gray-600">Manage your rice planting cycles</p>
      </div>
      <router-link to="/plantings/create" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">New Planting</router-link>
    </div>

    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Active Plantings</h3>
      </div>
      <div v-if="loading" class="p-6">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
      </div>
      <div v-else class="divide-y divide-gray-200">
        <div v-for="p in plantings" :key="p.id" class="px-6 py-4 flex items-center justify-between">
          <div>
            <h4 class="text-sm font-medium text-gray-900">{{ p.varietal }} • {{ p.field?.name || `Field ${p.field_id}` }}</h4>
            <p class="text-xs text-gray-500">Sowing: {{ fmt(p.sowing_date) }} • Transplant: {{ fmt(p.transplanting_date) || '-' }} • Expected Harvest: {{ fmt(p.expected_harvest_date) }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <span class="text-xs text-gray-500">Stage: {{ p.stage || inferStage(p) }}</span>
            <router-link :to="`/plantings/${p.id}/edit" class="text-green-700 hover:underline">Edit</router-link>
          </div>
        </div>
        <div v-if="plantings.length === 0" class="px-6 py-12 text-center text-gray-500">No plantings yet.</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const plantings = ref([]);

const fetchPlantings = async () => {
  try {
    const res = await axios.get('/api/plantings');
    plantings.value = res.data?.data || res.data || [];
  } catch (e) {
    console.error('Failed to load plantings', e);
  } finally {
    loading.value = false;
  }
};

const fmt = (d) => d ? new Date(d).toLocaleDateString() : '';

const inferStage = (p) => {
  const now = new Date();
  const sow = p.sowing_date ? new Date(p.sowing_date) : null;
  const trans = p.transplanting_date ? new Date(p.transplanting_date) : null;
  const harvest = p.expected_harvest_date ? new Date(p.expected_harvest_date) : null;
  if (sow && now < trans) return 'Seedling';
  if (trans && now < harvest) return 'Vegetative';
  if (harvest && now >= harvest) return 'Harvesting';
  return 'Planned';
};

onMounted(fetchPlantings);
</script>

