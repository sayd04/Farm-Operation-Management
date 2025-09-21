<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Inventory Management</h1>
          <p class="mt-2 text-gray-600">
            Track your farm supplies, equipment, and materials
          </p>
        </div>
        <button
          @click="showCreateModal = true"
          class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          Add Inventory Item
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                <dd class="text-lg font-medium text-gray-900">{{ inventoryItems.length }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Low Stock</dt>
                <dd class="text-lg font-medium text-gray-900">{{ lowStockItems }}</dd>
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
                <dt class="text-sm font-medium text-gray-500 truncate">In Stock</dt>
                <dd class="text-lg font-medium text-gray-900">{{ inStockItems }}</dd>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Value</dt>
                <dd class="text-lg font-medium text-gray-900">${{ totalValue.toFixed(2) }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Inventory Items Table -->
    <DataTable
      :data="inventoryItems"
      :columns="columns"
      title="Inventory Items"
      :searchable="true"
      :actions="['edit', 'delete']"
      :loading="loading"
      @edit="editItem"
      @delete="deleteItem"
    >
      <template #cell-category="{ value }">
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
          {{ value }}
        </span>
      </template>

      <template #cell-quantity="{ value, row }">
        <div class="flex items-center space-x-2">
          <span>{{ value }} {{ row.unit }}</span>
          <span
            v-if="value <= row.min_stock"
            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"
          >
            Low Stock
          </span>
        </div>
      </template>

      <template #cell-unit_price="{ value }">
        ${{ value.toFixed(2) }}
      </template>

      <template #cell-total_value="{ row }">
        ${{ (row.quantity * row.unit_price).toFixed(2) }}
      </template>

      <template #actions>
        <button
          @click="showCreateModal = true"
          class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          Add Item
        </button>
      </template>
    </DataTable>

    <!-- Create/Edit Item Modal -->
    <Modal
      v-model:show="showCreateModal"
      :title="editingItem ? 'Edit Inventory Item' : 'Add New Inventory Item'"
      size="lg"
    >
      <form @submit.prevent="saveItem" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
            <input
              id="name"
              v-model="itemForm.name"
              type="text"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
              placeholder="Enter item name"
            />
          </div>

          <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select
              id="category"
              v-model="itemForm.category"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            >
              <option value="">Select category</option>
              <option value="seeds">Seeds</option>
              <option value="fertilizers">Fertilizers</option>
              <option value="pesticides">Pesticides</option>
              <option value="equipment">Equipment</option>
              <option value="tools">Tools</option>
              <option value="materials">Materials</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
            <input
              id="quantity"
              v-model.number="itemForm.quantity"
              type="number"
              min="0"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
              placeholder="Enter quantity"
            />
          </div>

          <div>
            <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
            <select
              id="unit"
              v-model="itemForm.unit"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            >
              <option value="">Select unit</option>
              <option value="kg">Kilograms (kg)</option>
              <option value="lbs">Pounds (lbs)</option>
              <option value="liters">Liters</option>
              <option value="gallons">Gallons</option>
              <option value="pieces">Pieces</option>
              <option value="bags">Bags</option>
              <option value="boxes">Boxes</option>
            </select>
          </div>

          <div>
            <label for="unit_price" class="block text-sm font-medium text-gray-700">Unit Price ($)</label>
            <input
              id="unit_price"
              v-model.number="itemForm.unit_price"
              type="number"
              step="0.01"
              min="0"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
              placeholder="Enter unit price"
            />
          </div>

          <div>
            <label for="min_stock" class="block text-sm font-medium text-gray-700">Minimum Stock Level</label>
            <input
              id="min_stock"
              v-model.number="itemForm.min_stock"
              type="number"
              min="0"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
              placeholder="Enter minimum stock level"
            />
          </div>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            id="description"
            v-model="itemForm.description"
            rows="3"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
            placeholder="Enter item description"
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
            {{ saving ? 'Saving...' : (editingItem ? 'Update Item' : 'Create Item') }}
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

const inventoryItems = ref([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const showCreateModal = ref(false);
const editingItem = ref(null);

const itemForm = ref({
  name: '',
  category: '',
  quantity: '',
  unit: '',
  unit_price: '',
  min_stock: '',
  description: ''
});

const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'category', label: 'Category', sortable: true },
  { key: 'quantity', label: 'Quantity', sortable: true },
  { key: 'unit_price', label: 'Unit Price', sortable: true, type: 'currency' },
  { key: 'total_value', label: 'Total Value', sortable: true, type: 'currency' },
  { key: 'min_stock', label: 'Min Stock', sortable: true }
];

const lowStockItems = computed(() => {
  return inventoryItems.value.filter(item => item.quantity <= item.min_stock).length;
});

const inStockItems = computed(() => {
  return inventoryItems.value.filter(item => item.quantity > item.min_stock).length;
});

const totalValue = computed(() => {
  return inventoryItems.value.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
});

const fetchInventoryItems = async () => {
  try {
    const response = await axios.get('/api/inventory/items');
    inventoryItems.value = response.data;
  } catch (error) {
    console.error('Failed to fetch inventory items:', error);
  } finally {
    loading.value = false;
  }
};

const saveItem = async () => {
  saving.value = true;
  error.value = '';

  try {
    if (editingItem.value) {
      await axios.put(`/api/inventory/items/${editingItem.value.id}`, itemForm.value);
    } else {
      await axios.post('/api/inventory/items', itemForm.value);
    }
    
    await fetchInventoryItems();
    closeModal();
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save inventory item';
  } finally {
    saving.value = false;
  }
};

const editItem = (item) => {
  editingItem.value = item;
  itemForm.value = { ...item };
  showCreateModal.value = true;
};

const deleteItem = async (item) => {
  if (!confirm(`Are you sure you want to delete "${item.name}"?`)) return;

  try {
    await axios.delete(`/api/inventory/items/${item.id}`);
    await fetchInventoryItems();
  } catch (error) {
    console.error('Failed to delete inventory item:', error);
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  editingItem.value = null;
  itemForm.value = {
    name: '',
    category: '',
    quantity: '',
    unit: '',
    unit_price: '',
    min_stock: '',
    description: ''
  };
  error.value = '';
};

onMounted(() => {
  fetchInventoryItems();
});
</script>
</template>