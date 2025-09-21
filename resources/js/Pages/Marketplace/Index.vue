<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Marketplace</h1>
      <p class="mt-2 text-gray-600">
        Browse and purchase farm products from local farmers
      </p>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
          <input
            id="search"
            v-model="filters.search"
            type="text"
            placeholder="Search products..."
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
          />
        </div>
        
        <div>
          <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
          <select
            id="category"
            v-model="filters.category"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
          >
            <option value="">All Categories</option>
            <option value="rice">Rice</option>
            <option value="vegetables">Vegetables</option>
            <option value="fruits">Fruits</option>
            <option value="grains">Grains</option>
            <option value="other">Other</option>
          </select>
        </div>
        
        <div>
          <label for="price_range" class="block text-sm font-medium text-gray-700">Price Range</label>
          <select
            id="price_range"
            v-model="filters.price_range"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
          >
            <option value="">Any Price</option>
            <option value="0-50">$0 - $50</option>
            <option value="50-100">$50 - $100</option>
            <option value="100-200">$100 - $200</option>
            <option value="200+">$200+</option>
          </select>
        </div>
        
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
    </div>

    <!-- Products Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
        @click="$router.push(`/marketplace/products/${product.id}`)"
      >
        <div class="h-48 bg-gray-200 flex items-center justify-center">
          <img
            v-if="product.image"
            :src="product.image"
            :alt="product.name"
            class="h-full w-full object-cover"
          />
          <div v-else class="text-gray-400">
            <svg class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-sm mt-2">No image</p>
          </div>
        </div>
        
        <div class="p-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ product.name }}</h3>
          <p class="text-sm text-gray-600 mb-2">{{ product.description }}</p>
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-500">Category: {{ product.category }}</span>
            <span class="text-sm text-gray-500">Available: {{ product.quantity }} {{ product.unit }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-xl font-bold text-green-600">${{ product.price }}</span>
            <span class="text-sm text-gray-500">by {{ product.farmer_name }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- No Products Message -->
    <div v-if="!loading && filteredProducts.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-4 4m0 0l-4-4m4 4V3" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
      <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const loading = ref(true);
const products = ref([]);

const filters = ref({
  search: '',
  category: '',
  price_range: ''
});

const filteredProducts = computed(() => {
  let filtered = products.value;

  if (filters.value.search) {
    const searchTerm = filters.value.search.toLowerCase();
    filtered = filtered.filter(product =>
      product.name.toLowerCase().includes(searchTerm) ||
      product.description.toLowerCase().includes(searchTerm)
    );
  }

  if (filters.value.category) {
    filtered = filtered.filter(product => product.category === filters.value.category);
  }

  if (filters.value.price_range) {
    const [min, max] = filters.value.price_range.split('-').map(Number);
    if (max) {
      filtered = filtered.filter(product => product.price >= min && product.price <= max);
    } else {
      filtered = filtered.filter(product => product.price >= min);
    }
  }

  return filtered;
});

const fetchProducts = async () => {
  try {
    const response = await axios.get('/api/marketplace/products');
    products.value = response.data;
  } catch (error) {
    console.error('Failed to fetch products:', error);
  } finally {
    loading.value = false;
  }
};

const applyFilters = () => {
  // Filters are applied reactively through computed property
};

onMounted(() => {
  fetchProducts();
});
</script>
</template>