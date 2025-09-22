<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">My Store</h1>
        <p class="mt-2 text-gray-600">Manage harvested rice listings</p>
      </div>
      <button @click="showModal = true" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">New Listing</button>
    </div>

    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Listings</h3>
      </div>
      <div v-if="loading" class="p-6"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div></div>
      <div v-else class="divide-y divide-gray-200">
        <div v-for="l in listings" :key="l.id" class="px-6 py-4 flex items-center justify-between">
          <div>
            <h4 class="text-sm font-medium text-gray-900">{{ l.title || l.varietal }} — {{ l.quantity }} kg</h4>
            <p class="text-xs text-gray-500">Price: ${{ l.price }} • Varietal: {{ l.varietal }} • Description: {{ l.description }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <button @click="edit(l)" class="text-green-700 hover:underline">Edit</button>
            <button @click="remove(l)" class="text-red-700 hover:underline">Delete</button>
          </div>
        </div>
        <div v-if="listings.length === 0" class="px-6 py-12 text-center text-gray-500">No listings yet.</div>
      </div>
    </div>

    <Modal v-model:show="showModal" :title="editing ? 'Edit Listing' : 'New Listing'">
      <form @submit.prevent="save" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Varietal</label>
            <select v-model="form.varietal" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
              <option v-for="v in varietals" :key="v" :value="v">{{ v }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Price ($/kg)</label>
            <input v-model.number="form.price" type="number" min="0" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Quantity (kg)</label>
            <input v-model.number="form.quantity" type="number" min="0" step="0.1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
        </div>
        <div class="flex items-center justify-end">
          <button :disabled="saving" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            {{ saving ? 'Saving...' : (editing ? 'Update Listing' : 'Create Listing') }}
          </button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Modal from '@/Components/UI/Modal.vue';

const loading = ref(true);
const listings = ref([]);
const showModal = ref(false);
const saving = ref(false);
const editing = ref(null);
const varietals = ref(['IR64','Jasmine','Basmati','NSIC Rc222']);
const form = ref({ varietal: '', price: null, quantity: null, title: '', description: '' });

const load = async () => {
  try {
    const res = await axios.get('/api/farmer/marketplace/listings');
    listings.value = res.data?.data || res.data || [];
  } catch (e) { console.error('Failed to load listings', e); }
  finally { loading.value = false; }
};

const save = async () => {
  saving.value = true;
  try {
    if (editing.value) {
      await axios.put(`/api/farmer/marketplace/listings/${editing.value.id}`, form.value);
    } else {
      await axios.post('/api/farmer/marketplace/listings', form.value);
    }
    await load();
    showModal.value = false;
    editing.value = null;
  } catch (e) { console.error('Failed to save listing', e); }
  finally { saving.value = false; }
};

const edit = (l) => {
  editing.value = l;
  form.value = { varietal: l.varietal, price: l.price, quantity: l.quantity, title: l.title, description: l.description };
  showModal.value = true;
};

const remove = async (l) => {
  if (!confirm('Delete this listing?')) return;
  try { await axios.delete(`/api/farmer/marketplace/listings/${l.id}`); await load(); }
  catch (e) { console.error('Failed to delete listing', e); }
};

onMounted(load);
</script>

