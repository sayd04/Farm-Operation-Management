<template>
  <div class="cart-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
          <p class="text-gray-600 mt-2">{{ cartItems.length }} items in your cart</p>
        </div>
        <button
          @click="continueShopping"
          class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
        >
          Continue Shopping
        </button>
      </div>

      <div v-if="cartItems.length === 0" class="text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">🛒</div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Your cart is empty</h3>
        <p class="text-gray-600 mb-6">Add some products to get started</p>
        <button
          @click="continueShopping"
          class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Browse Products
        </button>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2 space-y-6">
          <div
            v-for="item in cartItems"
            :key="item.id"
            class="bg-white rounded-lg shadow-md p-6"
          >
            <div class="flex items-start space-x-4">
              <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-gray-500 text-2xl">{{ item.icon }}</span>
              </div>
              
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ item.name }}</h3>
                <p class="text-gray-600 text-sm mb-2">{{ item.description }}</p>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                  <span>Sold by: {{ item.seller_name }}</span>
                  <span>•</span>
                  <span>{{ item.location }}</span>
                </div>
              </div>
              
              <div class="flex flex-col items-end space-y-4">
                <div class="text-right">
                  <div class="text-lg font-bold text-green-600">${{ item.price }}</div>
                  <div class="text-sm text-gray-600">{{ item.unit }}</div>
                </div>
                
                <div class="flex items-center space-x-2">
                  <button
                    @click="decreaseQuantity(item.id)"
                    class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300"
                  >
                    -
                  </button>
                  <span class="w-12 text-center font-medium">{{ item.quantity }}</span>
                  <button
                    @click="increaseQuantity(item.id)"
                    class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300"
                  >
                    +
                  </button>
                </div>
                
                <div class="text-right">
                  <div class="text-lg font-bold">${{ (item.price * item.quantity).toFixed(2) }}</div>
                </div>
                
                <button
                  @click="removeItem(item.id)"
                  class="text-red-600 hover:text-red-800 text-sm"
                >
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
            
            <div class="space-y-3 mb-6">
              <div class="flex justify-between">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium">${{ subtotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Shipping:</span>
                <span class="font-medium">${{ shipping.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Tax:</span>
                <span class="font-medium">${{ tax.toFixed(2) }}</span>
              </div>
              <div class="border-t border-gray-200 pt-3">
                <div class="flex justify-between text-lg font-bold">
                  <span>Total:</span>
                  <span>${{ total.toFixed(2) }}</span>
                </div>
              </div>
            </div>
            
            <button
              @click="proceedToCheckout"
              class="w-full bg-green-600 text-white py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 mb-4"
            >
              Proceed to Checkout
            </button>
            
            <div class="text-center">
              <p class="text-sm text-gray-600 mb-2">Secure checkout with</p>
              <div class="flex justify-center space-x-2">
                <span class="text-sm text-gray-500">💳</span>
                <span class="text-sm text-gray-500">🏦</span>
                <span class="text-sm text-gray-500">💰</span>
              </div>
            </div>
          </div>
          
          <!-- Promo Code -->
          <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Promo Code</h3>
            <div class="flex space-x-2">
              <input
                v-model="promoCode"
                type="text"
                placeholder="Enter promo code"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <button
                @click="applyPromoCode"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                Apply
              </button>
            </div>
            <div v-if="appliedPromo" class="mt-2 text-sm text-green-600">
              Promo code applied! 10% discount
            </div>
          </div>
          
          <!-- Shipping Information -->
          <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Shipping Information</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Standard Shipping:</span>
                <span>3-5 business days</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Express Shipping:</span>
                <span>1-2 business days</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Free shipping on orders over:</span>
                <span>$100</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const promoCode = ref('')
const appliedPromo = ref(false)

const cartItems = ref([
  {
    id: 1,
    name: 'Corn Seeds - Pioneer 1234',
    description: 'High-yield corn seeds perfect for spring planting',
    price: 180.00,
    unit: 'per bag',
    quantity: 2,
    seller_name: 'AgriSupply Co.',
    location: 'Springfield, IL',
    icon: '🌱'
  },
  {
    id: 2,
    name: 'Nitrogen Fertilizer',
    description: 'High-grade nitrogen fertilizer for optimal crop growth',
    price: 450.00,
    unit: 'per ton',
    quantity: 1,
    seller_name: 'Farm Depot',
    location: 'Des Moines, IA',
    icon: '🌿'
  },
  {
    id: 3,
    name: 'Garden Tools Set',
    description: 'Complete set of professional garden tools',
    price: 89.99,
    unit: 'per set',
    quantity: 1,
    seller_name: 'Tool Master',
    location: 'Milwaukee, WI',
    icon: '🔧'
  }
])

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const shipping = computed(() => {
  return subtotal.value > 100 ? 0 : 15.00
})

const tax = computed(() => {
  return subtotal.value * 0.08 // 8% tax
})

const total = computed(() => {
  let totalAmount = subtotal.value + shipping.value + tax.value
  if (appliedPromo.value) {
    totalAmount *= 0.9 // 10% discount
  }
  return totalAmount
})

const increaseQuantity = (itemId) => {
  const item = cartItems.value.find(item => item.id === itemId)
  if (item) {
    item.quantity++
  }
}

const decreaseQuantity = (itemId) => {
  const item = cartItems.value.find(item => item.id === itemId)
  if (item && item.quantity > 1) {
    item.quantity--
  }
}

const removeItem = (itemId) => {
  cartItems.value = cartItems.value.filter(item => item.id !== itemId)
}

const applyPromoCode = () => {
  if (promoCode.value.toLowerCase() === 'save10') {
    appliedPromo.value = true
    promoCode.value = ''
  }
}

const proceedToCheckout = () => {
  // Navigate to checkout page
  router.push('/checkout')
}

const continueShopping = () => {
  router.push('/marketplace')
}
</script>

<style scoped>
.cart-page {
  min-height: 100vh;
  background-color: #f8fafc;
}
</style>