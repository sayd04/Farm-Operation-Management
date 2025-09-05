<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-4">
            <li>
              <router-link to="/marketplace" class="text-gray-400 hover:text-gray-500">
                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="sr-only">Marketplace</span>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-4 text-sm font-medium text-gray-500">{{ product.crop_name }}</span>
              </div>
            </li>
          </ol>
        </nav>
        <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          {{ product.crop_name }}
        </h2>
        <p v-if="product.variety" class="mt-1 text-sm text-gray-500">{{ product.variety }}</p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          to="/marketplace"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Back to Marketplace
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Product Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Product Details</h3>
          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Crop Name</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ product.crop_name }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Variety</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ product.variety || 'N/A' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Quantity Available</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ product.quantity }} {{ product.unit }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Quality Grade</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <span v-if="product.quality_grade" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Grade {{ product.quality_grade }}
                </span>
                <span v-else class="text-gray-500">Not specified</span>
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Harvest Date</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(product.harvest_date) }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Field Location</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ product.field?.name || 'Unknown Field' }}</dd>
            </div>
            <div v-if="product.notes" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Notes</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ product.notes }}</dd>
            </div>
          </dl>
        </div>

        <!-- Seller Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Seller Information</h3>
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
            </div>
            <div>
              <h4 class="text-sm font-medium text-gray-900">{{ product.seller?.name || 'Unknown Seller' }}</h4>
              <p class="text-sm text-gray-500">{{ product.seller?.email || 'No contact information' }}</p>
              <p v-if="product.seller?.phone" class="text-sm text-gray-500">{{ product.seller.phone }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Purchase Card -->
        <div class="bg-white shadow rounded-lg p-6">
          <div class="text-center">
            <div class="text-3xl font-bold text-gray-900">${{ product.price_per_unit }}</div>
            <div class="text-sm text-gray-500">per {{ product.unit }}</div>
          </div>
          
          <div class="mt-6">
            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
            <input
              id="quantity"
              v-model="purchaseForm.quantity"
              type="number"
              min="1"
              :max="product.quantity"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
            <p class="mt-1 text-sm text-gray-500">Available: {{ product.quantity }} {{ product.unit }}</p>
          </div>

          <div class="mt-4">
            <div class="flex justify-between text-sm">
              <span class="text-gray-500">Subtotal</span>
              <span class="font-medium">${{ subtotal }}</span>
            </div>
            <div class="flex justify-between text-sm mt-1">
              <span class="text-gray-500">Shipping</span>
              <span class="font-medium">${{ shippingCost }}</span>
            </div>
            <div class="border-t pt-2 mt-2">
              <div class="flex justify-between">
                <span class="text-base font-medium text-gray-900">Total</span>
                <span class="text-base font-medium text-gray-900">${{ total }}</span>
              </div>
            </div>
          </div>

          <div class="mt-6 space-y-3">
            <button
              @click="addToCart"
              class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              Add to Cart
            </button>
            <button
              @click="buyNow"
              class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              Buy Now
            </button>
          </div>
        </div>

        <!-- Delivery Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Information</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Estimated Delivery</span>
              <span class="text-sm font-medium text-gray-900">3-5 business days</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Shipping Method</span>
              <span class="text-sm font-medium text-gray-900">Standard</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Return Policy</span>
              <span class="text-sm font-medium text-gray-900">7 days</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const product = ref({})
const loading = ref(false)

const purchaseForm = reactive({
  quantity: 1
})

const subtotal = computed(() => {
  return (product.value.price_per_unit * purchaseForm.quantity).toFixed(2)
})

const shippingCost = computed(() => {
  // Simple shipping calculation
  return (purchaseForm.quantity * 2).toFixed(2)
})

const total = computed(() => {
  return (parseFloat(subtotal.value) + parseFloat(shippingCost.value)).toFixed(2)
})

const fetchProduct = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/marketplace/products/${route.params.id}`)
    product.value = response.data.product
  } catch (error) {
    console.error('Error fetching product:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = () => {
  // Add to cart logic
  console.log('Adding to cart:', {
    product: product.value,
    quantity: purchaseForm.quantity
  })
  // Navigate to cart or show success message
  router.push('/cart')
}

const buyNow = () => {
  // Direct purchase logic
  console.log('Buying now:', {
    product: product.value,
    quantity: purchaseForm.quantity,
    total: total.value
  })
  // Navigate to checkout
  router.push('/checkout')
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  fetchProduct()
})
</script>