<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
    </div>

    <!-- Product Not Found -->
    <div v-else-if="!product" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.6M15 6.334c2.33.824 4 3.05 4 5.666 0 2.34-1.009 4.29-2.6 5.824" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Product not found</h3>
      <p class="mt-1 text-sm text-gray-500">The product you're looking for doesn't exist.</p>
      <div class="mt-6">
        <router-link
          to="/marketplace"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
        >
          Back to Marketplace
        </router-link>
      </div>
    </div>

    <!-- Product Details -->
    <div v-else>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div class="aspect-w-1 aspect-h-1">
          <div class="h-96 bg-gray-200 rounded-lg overflow-hidden">
            <img
              v-if="product.image"
              :src="product.image"
              :alt="product.name"
              class="h-full w-full object-cover"
            />
            <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
              <div class="text-center">
                <svg class="h-24 w-24 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-lg">No image available</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ product.name }}</h1>
            <p class="mt-2 text-lg text-gray-600">{{ product.description }}</p>
          </div>

          <div class="flex items-center space-x-4">
            <span class="text-3xl font-bold text-green-600">${{ product.price }}</span>
            <span class="text-sm text-gray-500">per {{ product.unit }}</span>
          </div>

          <div class="space-y-4">
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-900 w-24">Category:</span>
              <span class="text-sm text-gray-600">{{ product.category }}</span>
            </div>
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-900 w-24">Available:</span>
              <span class="text-sm text-gray-600">{{ product.quantity }} {{ product.unit }}</span>
            </div>
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-900 w-24">Farmer:</span>
              <span class="text-sm text-gray-600">{{ product.farmer_name }}</span>
            </div>
            <div v-if="product.location" class="flex items-center">
              <span class="text-sm font-medium text-gray-900 w-24">Location:</span>
              <span class="text-sm text-gray-600">{{ product.location }}</span>
            </div>
          </div>

          <!-- Quantity Selection (for buyers) -->
          <div v-if="authStore.isBuyer" class="space-y-4">
            <div>
              <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
              <input
                id="quantity"
                v-model.number="selectedQuantity"
                type="number"
                :min="1"
                :max="product.quantity"
                class="mt-1 block w-32 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
              />
            </div>

            <div v-if="selectedQuantity > 0" class="bg-gray-50 p-4 rounded-lg">
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-900">Total Price:</span>
                <span class="text-lg font-bold text-green-600">${{ (product.price * selectedQuantity).toFixed(2) }}</span>
              </div>
            </div>

            <div class="flex space-x-4">
              <button
                @click="addToCart"
                :disabled="selectedQuantity <= 0 || addingToCart"
                class="flex-1 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ addingToCart ? 'Adding...' : 'Add to Cart' }}
              </button>
              <button
                @click="buyNow"
                :disabled="selectedQuantity <= 0 || buyingNow"
                class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ buyingNow ? 'Processing...' : 'Buy Now' }}
              </button>
            </div>
          </div>

          <!-- Edit/Delete buttons (for farmers) -->
          <div v-if="authStore.isFarmer && product.farmer_id === authStore.user?.id" class="flex space-x-4">
            <router-link
              :to="`/marketplace/products/${product.id}/edit`"
              class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center"
            >
              Edit Product
            </router-link>
            <button
              @click="deleteProduct"
              :disabled="deleting"
              class="flex-1 bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50"
            >
              {{ deleting ? 'Deleting...' : 'Delete Product' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Reviews Section -->
      <div v-if="reviews.length > 0" class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Customer Reviews</h2>
        <div class="space-y-4">
          <div
            v-for="review in reviews"
            :key="review.id"
            class="bg-white p-6 rounded-lg shadow"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="font-medium text-gray-900">{{ review.buyer_name }}</span>
              <div class="flex items-center">
                <div class="flex text-yellow-400">
                  <span v-for="i in 5" :key="i" class="text-sm">
                    {{ i <= review.rating ? '★' : '☆' }}
                  </span>
                </div>
                <span class="ml-2 text-sm text-gray-500">{{ review.rating }}/5</span>
              </div>
            </div>
            <p class="text-gray-600">{{ review.comment }}</p>
            <p class="text-sm text-gray-400 mt-2">{{ formatDate(review.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const product = ref(null);
const reviews = ref([]);
const selectedQuantity = ref(1);
const addingToCart = ref(false);
const buyingNow = ref(false);
const deleting = ref(false);

const fetchProduct = async () => {
  try {
    const response = await axios.get(`/api/marketplace/products/${route.params.id}`);
    product.value = response.data;
    selectedQuantity.value = 1;
  } catch (error) {
    console.error('Failed to fetch product:', error);
  } finally {
    loading.value = false;
  }
};

const fetchReviews = async () => {
  try {
    const response = await axios.get(`/api/marketplace/products/${route.params.id}/reviews`);
    reviews.value = response.data;
  } catch (error) {
    console.error('Failed to fetch reviews:', error);
  }
};

const addToCart = async () => {
  addingToCart.value = true;
  try {
    await axios.post('/api/marketplace/cart/add', {
      product_id: product.value.id,
      quantity: selectedQuantity.value
    });
    // Show success message
  } catch (error) {
    console.error('Failed to add to cart:', error);
  } finally {
    addingToCart.value = false;
  }
};

const buyNow = async () => {
  buyingNow.value = true;
  try {
    const response = await axios.post('/api/marketplace/orders', {
      items: [{
        product_id: product.value.id,
        quantity: selectedQuantity.value
      }]
    });
    router.push(`/orders/${response.data.order.id}`);
  } catch (error) {
    console.error('Failed to create order:', error);
  } finally {
    buyingNow.value = false;
  }
};

const deleteProduct = async () => {
  if (!confirm('Are you sure you want to delete this product?')) return;
  
  deleting.value = true;
  try {
    await axios.delete(`/api/marketplace/products/${product.value.id}`);
    router.push('/marketplace');
  } catch (error) {
    console.error('Failed to delete product:', error);
  } finally {
    deleting.value = false;
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  fetchProduct();
  fetchReviews();
});
</script>
</template>