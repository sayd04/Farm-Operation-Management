<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Planting</h1>
    <form v-if="loaded" @submit.prevent="save" class="bg-white shadow rounded-lg p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Field</label>
          <select v-model="form.field_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            <option v-for="f in fields" :key="f.id" :value="f.id">{{ f.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Rice Varietal</label>
          <select v-model="form.varietal" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            <option v-for="v in varietals" :key="v.name" :value="v.name">{{ v.name }}</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Sowing Date</label>
          <input v-model="form.sowing_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Transplanting Date</label>
          <input v-model="form.transplanting_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Expected Harvest Date</label>
          <input v-model="form.expected_harvest_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
      </div>

      <div class="flex items-center justify-end">
        <button :disabled="saving" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
          {{ saving ? 'Saving...' : 'Update Planting' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const id = route.params.id;
const fields = ref([]);
const varietals = ref([]);
const form = ref({});
const loaded = ref(false);
const saving = ref(false);

const load = async () => {
  try {
    const [fieldsRes, varietalsRes, plantingRes] = await Promise.all([
      axios.get('/api/fields'),
      axios.get('/api/rice-varieties'),
      axios.get(`/api/plantings/${id}`)
    ]);
    fields.value = fieldsRes.data?.data || fieldsRes.data || [];
    varietals.value = (varietalsRes.data?.data || varietalsRes.data || []).map(v => ({ name: v.name || v.varietal || v.code }));
    form.value = plantingRes.data;
    loaded.value = true;
  } catch (e) {
    console.error('Failed to load planting', e);
  }
};

const save = async () => {
  saving.value = true;
  try {
    await axios.put(`/api/plantings/${id}`, form.value);
    router.push('/plantings');
  } catch (e) {
    console.error('Failed to update planting', e);
  } finally {
    saving.value = false;
  }
};

onMounted(load);
</script>

