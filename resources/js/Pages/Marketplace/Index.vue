<template>
  <div class="marketplace-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Marketplace</h1>
          <p class="text-gray-600 mt-2">Buy and sell agricultural products</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="viewCart"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Cart ({{ cartItemCount }})
          </button>
          <button
            @click="sellProduct"
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
          >
            Sell Product
          </button>
        </div>
      </div>

      <!-- Categories -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <button
            v-for="category in categories"
            :key="category.id"
            @click="selectCategory(category.id)"
            :class="selectedCategory === category.id ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700'"
            class="p-3 rounded-lg hover:bg-gray-200 transition-colors"
          >
            <div class="text-2xl mb-2">{{ category.icon }}</div>
            <div class="text-sm font-medium">{{ category.name }}</div>
          </button>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search products..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
            <select
              v-model="priceFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Prices</option>
              <option value="0-50">$0 - $50</option>
              <option value="50-100">$50 - $100</option>
              <option value="100-500">$100 - $500</option>
              <option value="500+">$500+</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
            <select
              v-model="locationFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Locations</option>
              <option value="local">Local (within 50 miles)</option>
              <option value="regional">Regional (within 200 miles)</option>
              <option value="national">National</option>
            </select>
          </div>
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
        >
          <div class="aspect-w-16 aspect-h-9 bg-gray-200">
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
              <span class="text-gray-500 text-4xl">{{ product.icon }}</span>
            </div>
          </div>
          <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ product.name }}</h3>
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ product.description }}</p>
            
            <div class="flex items-center justify-between mb-3">
              <div class="text-2xl font-bold text-green-600">${{ product.price }}</div>
              <div class="text-sm text-gray-500">{{ product.unit }}</div>
            </div>
            
            <div class="flex items-center justify-between mb-3">
              <div class="text-sm text-gray-600">
                <span class="font-medium">{{ product.seller_name }}</span>
                <span class="ml-1">• {{ product.location }}</span>
              </div>
              <div class="flex items-center">
                <span class="text-yellow-400">★</span>
                <span class="text-sm text-gray-600 ml-1">{{ product.rating }}</span>
              </div>
            </div>
            
            <div class="flex space-x-2">
              <button
                @click="viewProduct(product.id)"
                class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              >
                View Details
              </button>
              <button
                @click="addToCart(product.id)"
                class="flex-1 bg-green-600 text-white px-3 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
              >
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredProducts.length === 0" class="text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">🛒</div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">No products found</h3>
        <p class="text-gray-600 mb-6">Try adjusting your search or filters</p>
        <button
          @click="clearFilters"
          class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Clear Filters
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="filteredProducts.length > 0" class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
          <button
            @click="previousPage"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
          >
            Previous
          </button>
          <span class="px-3 py-2 text-sm text-gray-700">
            Page {{ currentPage }} of {{ totalPages }}
          </span>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
          >
            Next
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const searchQuery = ref('')
const selectedCategory = ref('')
const priceFilter = ref('')
const locationFilter = ref('')
const currentPage = ref(1)
const itemsPerPage = 12

const categories = ref([
  { id: 'seeds', name: 'Seeds', icon: '🌱' },
  { id: 'fertilizer', name: 'Fertilizer', icon: '🌿' },
  { id: 'equipment', name: 'Equipment', icon: '🚜' },
  { id: 'tools', name: 'Tools', icon: '🔧' },
  { id: 'livestock', name: 'Livestock', icon: '🐄' },
  { id: 'produce', name: 'Produce', icon: '🥕' }
])

const products = ref([
  {
    id: 1,
    name: 'Corn Seeds - Pioneer 1234',
    description: 'High-yield corn seeds perfect for spring planting. Excellent disease resistance.',
    price: 180.00,
    unit: 'per bag',
    category: 'seeds',
    seller_name: 'AgriSupply Co.',
    location: 'Springfield, IL',
    rating: 4.8,
    icon: '🌱',
    stock: 50
  },
  {
    id: 2,
    name: 'Nitrogen Fertilizer',
    description: 'High-grade nitrogen fertilizer for optimal crop growth.',
    price: 450.00,
    unit: 'per ton',
    category: 'fertilizer',
    seller_name: 'Farm Depot',
    location: 'Des Moines, IA',
    rating: 4.6,
    icon: '🌿',
    stock: 25
  },
  {
    id: 3,
    name: 'John Deere Tractor',
    description: 'Used 2018 John Deere 6120M tractor in excellent condition.',
    price: 45000.00,
    unit: 'each',
    category: 'equipment',
    seller_name: 'Equipment Sales Inc.',
    location: 'Cedar Rapids, IA',
    rating: 4.9,
    icon: '🚜',
    stock: 1
  },
  {
    id: 4,
    name: 'Fresh Organic Tomatoes',
    description: 'Freshly harvested organic tomatoes from local farm.',
    price: 3.50,
    unit: 'per lb',
    category: 'produce',
    seller_name: 'Green Valley Farm',
    location: 'Madison, WI',
    rating: 4.7,
    icon: '🍅',
    stock: 200
  },
  {
    id: 5,
    name: 'Garden Tools Set',
    description: 'Complete set of professional garden tools.',
    price: 89.99,
    unit: 'per set',
    category: 'tools',
    seller_name: 'Tool Master',
    location: 'Milwaukee, WI',
    rating: 4.5,
    icon: '🔧',
    stock: 15
  },
  {
    id: 6,
    name: 'Holstein Dairy Cows',
    description: 'Registered Holstein dairy cows, excellent milk production.',
    price: 2500.00,
    unit: 'per head',
    category: 'livestock',
    seller_name: 'Dairy Farm LLC',
    location: 'Green Bay, WI',
    rating: 4.8,
    icon: '🐄',
    stock: 8
  }
])

const cartItemCount = ref(3) // Mock cart count

const filteredProducts = computed(() => {
  let filtered = products.value

  // Filter by category
  if (selectedCategory.value) {
    filtered = filtered.filter(product => product.category === selectedCategory.value)
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(query) ||
      product.description.toLowerCase().includes(query) ||
      product.seller_name.toLowerCase().includes(query)
    )
  }

  // Filter by price range
  if (priceFilter.value) {
    const [min, max] = priceFilter.value.split('-').map(p => p === '+' ? Infinity : parseFloat(p))
    filtered = filtered.filter(product => {
      const price = product.price
      return price >= min && (max === undefined || price <= max)
    })
  }

  // Filter by location
  if (locationFilter.value) {
    // Mock location filtering
    filtered = filtered.filter(product => {
      if (locationFilter.value === 'local') return product.location.includes('IL')
      if (locationFilter.value === 'regional') return product.location.includes('IA') || product.location.includes('WI')
      return true
    })
  }

  // Pagination
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(products.value.length / itemsPerPage)
})

const selectCategory = (categoryId) => {
  selectedCategory.value = selectedCategory.value === categoryId ? '' : categoryId
  currentPage.value = 1
}

const viewProduct = (id) => {
  router.push(`/marketplace/products/${id}`)
}

const addToCart = (id) => {
  // Add to cart logic
  console.log('Add to cart:', id)
  cartItemCount.value++
}

const viewCart = () => {
  router.push('/cart')
}

const sellProduct = () => {
  // Navigate to sell product page
  console.log('Sell product')
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  priceFilter.value = ''
  locationFilter.value = ''
  currentPage.value = 1
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

onMounted(() => {
  // Load products from API
})
</script>

<style scoped>
.marketplace-page {
  min-height: 100vh;
  background-color: #f8fafc;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>