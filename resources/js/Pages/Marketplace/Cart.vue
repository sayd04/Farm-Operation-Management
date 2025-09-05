<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Shopping Cart
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Review your items before checkout
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <router-link
          to="/marketplace"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Continue Shopping
        </router-link>
      </div>
    </div>

    <div v-if="cartItems.length === 0" class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
      <p class="mt-1 text-sm text-gray-500">Start shopping to add items to your cart.</p>
      <div class="mt-6">
        <router-link
          to="/marketplace"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          Start Shopping
        </router-link>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Cart Items -->
      <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cart Items</h3>
            <div class="space-y-4">
              <div
                v-for="item in cartItems"
                :key="item.id"
                class="flex items-center space-x-4 border-b border-gray-200 pb-4"
              >
                <div class="flex-shrink-0">
                  <div class="h-16 w-16 bg-gray-200 rounded-lg flex items-center justify-center">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                  </div>
                </div>
                
                <div class="flex-1 min-w-0">
                  <h4 class="text-sm font-medium text-gray-900">{{ item.crop_name }}</h4>
                  <p v-if="item.variety" class="text-sm text-gray-500">{{ item.variety }}</p>
                  <p class="text-sm text-gray-500">{{ item.quantity }} {{ item.unit }} available</p>
                </div>
                
                <div class="flex items-center space-x-2">
                  <button
                    @click="decreaseQuantity(item)"
                    class="text-gray-400 hover:text-gray-600"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <span class="text-sm font-medium text-gray-900 w-8 text-center">{{ item.cart_quantity }}</span>
                  <button
                    @click="increaseQuantity(item)"
                    class="text-gray-400 hover:text-gray-600"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  </button>
                </div>
                
                <div class="text-right">
                  <div class="text-sm font-medium text-gray-900">${{ itemSubtotal(item) }}</div>
                  <div class="text-sm text-gray-500">${{ item.price_per_unit }} each</div>
                </div>
                
                <button
                  @click="removeItem(item)"
                  class="text-red-400 hover:text-red-600"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
            
            <div class="space-y-3">
              <div class="flex justify-between">
                <span class="text-sm text-gray-500">Subtotal</span>
                <span class="text-sm font-medium text-gray-900">${{ subtotal }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-500">Shipping</span>
                <span class="text-sm font-medium text-gray-900">${{ shippingCost }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-500">Tax</span>
                <span class="text-sm font-medium text-gray-900">${{ tax }}</span>
              </div>
              <div class="border-t pt-3">
                <div class="flex justify-between">
                  <span class="text-base font-medium text-gray-900">Total</span>
                  <span class="text-base font-medium text-gray-900">${{ total }}</span>
                </div>
              </div>
            </div>

            <div class="mt-6">
              <button
                @click="proceedToCheckout"
                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                Proceed to Checkout
              </button>
            </div>

            <div class="mt-4 text-center">
              <p class="text-sm text-gray-500">
                Secure checkout with SSL encryption
              </p>
            </div>
          </div>
        </div>

        <!-- Shipping Information -->
        <div class="mt-6 bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Information</h3>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const cartItems = ref([])

const subtotal = computed(() => {
  return cartItems.value.reduce((total, item) => {
    return total + (item.price_per_unit * item.cart_quantity)
  }, 0).toFixed(2)
})

const shippingCost = computed(() => {
  // Simple shipping calculation based on total items
  const totalItems = cartItems.value.reduce((total, item) => total + item.cart_quantity, 0)
  return (totalItems * 2).toFixed(2)
})

const tax = computed(() => {
  return (parseFloat(subtotal.value) * 0.08).toFixed(2) // 8% tax
})

const total = computed(() => {
  return (parseFloat(subtotal.value) + parseFloat(shippingCost.value) + parseFloat(tax.value)).toFixed(2)
})

const itemSubtotal = (item) => {
  return (item.price_per_unit * item.cart_quantity).toFixed(2)
}

const increaseQuantity = (item) => {
  if (item.cart_quantity < item.quantity) {
    item.cart_quantity += 1
  }
}

const decreaseQuantity = (item) => {
  if (item.cart_quantity > 1) {
    item.cart_quantity -= 1
  }
}

const removeItem = (item) => {
  const index = cartItems.value.findIndex(cartItem => cartItem.id === item.id)
  if (index > -1) {
    cartItems.value.splice(index, 1)
  }
}

const proceedToCheckout = () => {
  // Navigate to checkout page
  router.push('/checkout')
}

const loadCartItems = () => {
  // Load cart items from localStorage or API
  const savedCart = localStorage.getItem('cart')
  if (savedCart) {
    cartItems.value = JSON.parse(savedCart)
  }
}

const saveCartItems = () => {
  // Save cart items to localStorage
  localStorage.setItem('cart', JSON.stringify(cartItems.value))
}

onMounted(() => {
  loadCartItems()
})

// Watch for changes and save to localStorage
import { watch } from 'vue'
watch(cartItems, saveCartItems, { deep: true })
</script>