<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
      <p class="mt-2 text-gray-600">
        Review your items before checkout
      </p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
    </div>

    <!-- Empty Cart -->
    <div v-else-if="cartItems.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
      <p class="mt-1 text-sm text-gray-500">Start adding some products to get started.</p>
      <div class="mt-6">
        <router-link
          to="/marketplace"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
        >
          Continue Shopping
        </router-link>
      </div>
    </div>

    <!-- Cart Items -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Cart Items List -->
      <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Cart Items</h3>
          </div>
          <div class="divide-y divide-gray-200">
            <div
              v-for="item in cartItems"
              :key="item.id"
              class="px-6 py-4"
            >
              <div class="flex items-center space-x-4">
                <!-- Product Image -->
                <div class="flex-shrink-0 h-20 w-20 bg-gray-200 rounded-lg overflow-hidden">
                  <img
                    v-if="item.product.image"
                    :src="item.product.image"
                    :alt="item.product.name"
                    class="h-full w-full object-cover"
                  />
                  <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>

                <!-- Product Info -->
                <div class="flex-1 min-w-0">
                  <h4 class="text-lg font-medium text-gray-900">{{ item.product.name }}</h4>
                  <p class="text-sm text-gray-500">{{ item.product.description }}</p>
                  <p class="text-sm text-gray-500">by {{ item.product.farmer_name }}</p>
                </div>

                <!-- Quantity and Price -->
                <div class="flex items-center space-x-4">
                  <div class="flex items-center space-x-2">
                    <button
                      @click="updateQuantity(item.id, item.quantity - 1)"
                      :disabled="updating"
                      class="p-1 rounded-full hover:bg-gray-100 disabled:opacity-50"
                    >
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                      </svg>
                    </button>
                    <span class="w-8 text-center">{{ item.quantity }}</span>
                    <button
                      @click="updateQuantity(item.id, item.quantity + 1)"
                      :disabled="updating || item.quantity >= item.product.quantity"
                      class="p-1 rounded-full hover:bg-gray-100 disabled:opacity-50"
                    >
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                    </button>
                  </div>
                  <div class="text-right">
                    <p class="text-lg font-medium text-gray-900">${{ (item.product.price * item.quantity).toFixed(2) }}</p>
                    <p class="text-sm text-gray-500">${{ item.product.price }} each</p>
                  </div>
                  <button
                    @click="removeItem(item.id)"
                    :disabled="updating"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-full disabled:opacity-50"
                  >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg sticky top-8">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Order Summary</h3>
          </div>
          <div class="px-6 py-4 space-y-4">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Subtotal</span>
              <span class="text-sm font-medium text-gray-900">${{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Shipping</span>
              <span class="text-sm font-medium text-gray-900">${{ shipping.toFixed(2) }}</span>
            </div>
            <div class="border-t pt-4">
              <div class="flex justify-between">
                <span class="text-base font-medium text-gray-900">Total</span>
                <span class="text-base font-medium text-gray-900">${{ total.toFixed(2) }}</span>
              </div>
            </div>
            <button
              @click="checkout"
              :disabled="checkingOut || cartItems.length === 0"
              class="w-full bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ checkingOut ? 'Processing...' : 'Proceed to Checkout' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const loading = ref(true);
const cartItems = ref([]);
const updating = ref(false);
const checkingOut = ref(false);

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
});

const shipping = computed(() => {
  // Simple shipping calculation - could be more complex
  return subtotal.value > 100 ? 0 : 10;
});

const total = computed(() => {
  return subtotal.value + shipping.value;
});

const fetchCart = async () => {
  try {
    const response = await axios.get('/api/marketplace/cart');
    cartItems.value = response.data;
  } catch (error) {
    console.error('Failed to fetch cart:', error);
  } finally {
    loading.value = false;
  }
};

const updateQuantity = async (itemId, newQuantity) => {
  if (newQuantity <= 0) return;
  
  updating.value = true;
  try {
    await axios.put(`/api/marketplace/cart/${itemId}`, {
      quantity: newQuantity
    });
    await fetchCart(); // Refresh cart
  } catch (error) {
    console.error('Failed to update quantity:', error);
  } finally {
    updating.value = false;
  }
};

const removeItem = async (itemId) => {
  updating.value = true;
  try {
    await axios.delete(`/api/marketplace/cart/${itemId}`);
    await fetchCart(); // Refresh cart
  } catch (error) {
    console.error('Failed to remove item:', error);
  } finally {
    updating.value = false;
  }
};

const checkout = async () => {
  checkingOut.value = true;
  try {
    const response = await axios.post('/api/marketplace/orders', {
      items: cartItems.value.map(item => ({
        product_id: item.product.id,
        quantity: item.quantity
      }))
    });
    router.push(`/orders/${response.data.order.id}`);
  } catch (error) {
    console.error('Failed to checkout:', error);
  } finally {
    checkingOut.value = false;
  }
};

onMounted(() => {
  fetchCart();
});
</script>
</template>