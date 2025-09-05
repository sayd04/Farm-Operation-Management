<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Marketplace
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Buy and sell agricultural products
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          to="/marketplace/sell"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Sell Products
        </router-link>
        <router-link
          to="/cart"
          class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
          </svg>
          Cart ({{ cartItems.length }})
        </router-link>
      </div>
    </div>

    <!-- Categories -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Categories</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <button
          v-for="category in categories"
          :key="category.id"
          @click="selectCategory(category)"
          class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          :class="{ 'bg-indigo-50 border-indigo-200': selectedCategory?.id === category.id }"
        >
          <div class="text-center">
            <div class="text-2xl mb-2">{{ getCategoryIcon(category.name) }}</div>
            <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
          </div>
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
          <input
            id="search"
            v-model="filters.search"
            type="text"
            placeholder="Search products..."
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="price_min" class="block text-sm font-medium text-gray-700">Min Price</label>
          <input
            id="price_min"
            v-model="filters.price_min"
            type="number"
            placeholder="Min price"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="price_max" class="block text-sm font-medium text-gray-700">Max Price</label>
          <input
            id="price_max"
            v-model="filters.price_max"
            type="number"
            placeholder="Max price"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div class="flex items-end">
          <button
            @click="applyFilters"
            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Apply Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="products.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No products available</h3>
      <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later.</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200"
      >
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ product.crop_name }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              {{ product.unit }}
            </span>
          </div>
          
          <div class="space-y-2 mb-4">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Variety:</span>
              <span class="text-sm font-medium text-gray-900">{{ product.variety || 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Quantity:</span>
              <span class="text-sm font-medium text-gray-900">{{ product.quantity }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Quality:</span>
              <span class="text-sm font-medium text-gray-900">{{ product.quality_grade || 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Harvest Date:</span>
              <span class="text-sm font-medium text-gray-900">{{ formatDate(product.harvest_date) }}</span>
            </div>
          </div>

          <div class="border-t pt-4">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-2xl font-bold text-gray-900">${{ product.price_per_unit }}</div>
                <div class="text-sm text-gray-500">per {{ product.unit }}</div>
              </div>
              <button
                @click="addToCart(product)"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const categories = ref([])
const products = ref([])
const cartItems = ref([])
const loading = ref(false)
const selectedCategory = ref(null)

const filters = reactive({
  search: '',
  price_min: '',
  price_max: ''
})

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories')
    categories.value = response.data.categories
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchProducts = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.price_min) params.append('price_min', filters.price_min)
    if (filters.price_max) params.append('price_max', filters.price_max)
    if (selectedCategory.value) params.append('category_id', selectedCategory.value.id)
    
    const response = await axios.get(`/api/marketplace/products?${params}`)
    products.value = response.data.products
  } catch (error) {
    console.error('Error fetching products:', error)
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchProducts()
}

const selectCategory = (category) => {
  selectedCategory.value = selectedCategory.value?.id === category.id ? null : category
  fetchProducts()
}

const addToCart = (product) => {
  // Add to cart logic
  const existingItem = cartItems.value.find(item => item.id === product.id)
  if (existingItem) {
    existingItem.quantity += 1
  } else {
    cartItems.value.push({
      ...product,
      quantity: 1
    })
  }
}

const getCategoryIcon = (categoryName) => {
  const icons = {
    'Vegetables': '🥬',
    'Fruits': '🍎',
    'Grains': '🌾',
    'Herbs': '🌿',
    'Nuts': '🥜',
    'Other': '🌱'
  }
  return icons[categoryName] || '🌱'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  fetchCategories()
  fetchProducts()
})
</script>