<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Create Planting</h1>
    <form @submit.prevent="save" class="bg-white shadow rounded-lg p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Field</label>
          <select v-model="form.field_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            <option disabled value="">Select field</option>
            <option v-for="f in fields" :key="f.id" :value="f.id">{{ f.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Rice Varietal</label>
          <select v-model="form.varietal" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            <option disabled value="">Select varietal</option>
            <option v-for="v in varietals" :key="v.name" :value="v.name">{{ v.name }} ({{ v.growth_days }} days)</option>
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
          <input :value="expectedHarvestDate" type="date" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
      </div>

      <div class="flex items-center justify-end">
        <button :disabled="saving" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
          {{ saving ? 'Saving...' : 'Create Planting' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

const fields = ref([]);
const varietals = ref([]); // { name, growth_days }
const saving = ref(false);

const form = ref({
  field_id: '',
  varietal: '',
  sowing_date: '',
  transplanting_date: '',
  expected_harvest_date: '',
  notes: ''
});

const expectedHarvestDate = computed(() => {
  if (!form.value.sowing_date || !form.value.varietal) return '';
  const varietal = varietals.value.find(v => v.name === form.value.varietal);
  if (!varietal?.growth_days) return '';
  const base = new Date(form.value.sowing_date);
  const days = Number(varietal.growth_days) || 110; // default
  const result = new Date(base);
  result.setDate(result.getDate() + days);
  const yyyy = result.getFullYear();
  const mm = String(result.getMonth() + 1).padStart(2, '0');
  const dd = String(result.getDate()).padStart(2, '0');
  return `${yyyy}-${mm}-${dd}`;
});

watch(expectedHarvestDate, (val) => {
  form.value.expected_harvest_date = val;
});

const save = async () => {
  saving.value = true;
  try {
    await axios.post('/api/plantings', form.value);
    window.history.back();
  } catch (e) {
    console.error('Failed to create planting', e);
  } finally {
    saving.value = false;
  }
};

const loadOptions = async () => {
  try {
    const [fieldsRes, varietalsRes] = await Promise.all([
      axios.get('/api/fields'),
      axios.get('/api/rice-varieties')
    ]);
    fields.value = fieldsRes.data?.data || fieldsRes.data || [];
    varietals.value = (varietalsRes.data?.data || varietalsRes.data || []).map(v => ({ name: v.name || v.varietal || v.code, growth_days: v.growth_days || v.duration_days || v.growth_duration_days }));
  } catch (e) {
    console.error('Failed to load options', e);
  }
};

onMounted(loadOptions);
</script>

