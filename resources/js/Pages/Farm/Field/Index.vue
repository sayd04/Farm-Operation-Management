<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Farm Fields</h1>
          <p class="mt-2 text-gray-600">
            Manage your farm fields and track their status
          </p>
        </div>
        <button
          @click="showCreateModal = true"
          class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          Add New Field
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Fields</dt>
                <dd class="text-lg font-medium text-gray-900">{{ fields.length }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Active Fields</dt>
                <dd class="text-lg font-medium text-gray-900">{{ activeFields }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Area</dt>
                <dd class="text-lg font-medium text-gray-900">{{ totalArea }} acres</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Avg Yield</dt>
                <dd class="text-lg font-medium text-gray-900">{{ averageYield }} tons/acre</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Fields Table -->
    <DataTable
      :data="fields"
      :columns="columns"
      title="Farm Fields"
      :searchable="true"
      :actions="['edit', 'delete']"
      :loading="loading"
      @edit="editField"
      @delete="deleteField"
    >
      <template #cell-status="{ value }">
        <span
          :class="getStatusClass(value)"
          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
        >
          {{ value }}
        </span>
      </template>

      <template #cell-area="{ value }">
        {{ value }} acres
      </template>

      <template #cell-last_planted="{ value }">
        {{ formatDate(value) }}
      </template>

      <template #actions>
        <button
          @click="showCreateModal = true"
          class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          Add Field
        </button>
      </template>
    </DataTable>

    <!-- Create/Edit Field Modal -->
    <Modal
      v-model:show="showCreateModal"
      :title="editingField ? 'Edit Field' : 'Add New Field'"
      size="lg"
    >
      <form @submit.prevent="saveField" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Field Name</label>
            <input
              id="name"
              v-model="fieldForm.name"
              type="text"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
              placeholder="Enter field name"
            />
          </div>

          <div>
            <label for="area" class="block text-sm font-medium text-gray-700">Area (acres)</label>
            <input
              id="area"
              v-model.number="fieldForm.area"
              type="number"
              step="0.1"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
              placeholder="Enter area in acres"
            />
          </div>

          <div>
            <label for="soil_type" class="block text-sm font-medium text-gray-700">Soil Type</label>
            <select
              id="soil_type"
              v-model="fieldForm.soil_type"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            >
              <option value="">Select soil type</option>
              <option value="clay">Clay</option>
              <option value="sandy">Sandy</option>
              <option value="loamy">Loamy</option>
              <option value="silty">Silty</option>
            </select>
          </div>

          <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select
              id="status"
              v-model="fieldForm.status"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            >
              <option value="active">Active</option>
              <option value="fallow">Fallow</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            id="description"
            v-model="fieldForm.description"
            rows="3"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            placeholder="Enter field description"
          ></textarea>
        </div>

        <div v-if="error" class="text-red-600 text-sm">
          {{ error }}
        </div>

        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
          >
            {{ saving ? 'Saving...' : (editingField ? 'Update Field' : 'Create Field') }}
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import DataTable from '@/Components/UI/DataTable.vue';
import Modal from '@/Components/UI/Modal.vue';
import axios from 'axios';

const fields = ref([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const showCreateModal = ref(false);
const editingField = ref(null);

const fieldForm = ref({
  name: '',
  area: '',
  soil_type: '',
  status: 'active',
  description: ''
});

const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'area', label: 'Area', sortable: true },
  { key: 'soil_type', label: 'Soil Type', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'last_planted', label: 'Last Planted', sortable: true, type: 'date' }
];

const activeFields = computed(() => {
  return fields.value.filter(field => field.status === 'active').length;
});

const totalArea = computed(() => {
  return fields.value.reduce((sum, field) => sum + (field.area || 0), 0).toFixed(1);
});

const averageYield = computed(() => {
  const fieldsWithYield = fields.value.filter(field => field.average_yield);
  if (fieldsWithYield.length === 0) return '0.0';
  const totalYield = fieldsWithYield.reduce((sum, field) => sum + field.average_yield, 0);
  return (totalYield / fieldsWithYield.length).toFixed(1);
});

const fetchFields = async () => {
  try {
    const response = await axios.get('/api/fields');
    fields.value = response.data;
  } catch (error) {
    console.error('Failed to fetch fields:', error);
  } finally {
    loading.value = false;
  }
};

const saveField = async () => {
  saving.value = true;
  error.value = '';

  try {
    if (editingField.value) {
      await axios.put(`/api/fields/${editingField.value.id}`, fieldForm.value);
    } else {
      await axios.post('/api/fields', fieldForm.value);
    }
    
    await fetchFields();
    closeModal();
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save field';
  } finally {
    saving.value = false;
  }
};

const editField = (field) => {
  editingField.value = field;
  fieldForm.value = { ...field };
  showCreateModal.value = true;
};

const deleteField = async (field) => {
  if (!confirm(`Are you sure you want to delete "${field.name}"?`)) return;

  try {
    await axios.delete(`/api/fields/${field.id}`);
    await fetchFields();
  } catch (error) {
    console.error('Failed to delete field:', error);
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  editingField.value = null;
  fieldForm.value = {
    name: '',
    area: '',
    soil_type: '',
    status: 'active',
    description: ''
  };
  error.value = '';
};

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    fallow: 'bg-yellow-100 text-yellow-800',
    maintenance: 'bg-blue-100 text-blue-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
  if (!dateString) return 'Never';
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  fetchFields();
});
</script>
</template>