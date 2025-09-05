<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <nav class="flex" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-4">
            <li>
              <router-link to="/orders" class="text-gray-400 hover:text-gray-500">
                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="sr-only">Orders</span>
              </router-link>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="ml-4 text-sm font-medium text-gray-500">Order #{{ order.id }}</span>
              </div>
            </li>
          </ol>
        </nav>
        <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Order #{{ order.id }}
        </h2>
        <div class="mt-1 flex items-center space-x-4">
          <span :class="getStatusBadgeClass(order.status)" class="text-sm">
            {{ order.status }}
          </span>
          <span class="text-sm text-gray-500">
            {{ formatDate(order.order_date || order.sale_date) }}
          </span>
        </div>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          v-if="canUpdateStatus"
          @click="updateStatus"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Update Status
        </button>
        <router-link
          to="/orders"
          class="ml-3 bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Back to Orders
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Order Items</h3>
          
          <div v-if="order.items?.length === 0" class="text-center py-6">
            <p class="text-gray-500">No items in this order</p>
          </div>
          
          <div v-else class="space-y-4">
            <div
              v-for="item in order.items"
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
                <h4 class="text-sm font-medium text-gray-900">{{ getItemName(item) }}</h4>
                <p class="text-sm text-gray-500">{{ item.quantity }} {{ item.unit || 'units' }}</p>
                <p v-if="item.description" class="text-sm text-gray-500">{{ item.description }}</p>
              </div>
              
              <div class="text-right">
                <div class="text-sm font-medium text-gray-900">${{ item.unit_price || item.price_per_unit }}</div>
                <div class="text-sm text-gray-500">per {{ item.unit || 'unit' }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
          <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
              <dt class="text-sm font-medium text-gray-500">Order Date</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(order.order_date || order.sale_date) }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Status</dt>
              <dd class="mt-1">
                <span :class="getStatusBadgeClass(order.status)" class="text-sm">
                  {{ order.status }}
                </span>
              </dd>
            </div>
            <div v-if="order.expected_delivery_date || order.delivery_date">
              <dt class="text-sm font-medium text-gray-500">Delivery Date</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ formatDate(order.expected_delivery_date || order.delivery_date) }}
              </dd>
            </div>
            <div v-if="order.payment_method">
              <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ order.payment_method }}</dd>
            </div>
            <div v-if="order.payment_status">
              <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ order.payment_status }}</dd>
            </div>
            <div v-if="order.delivery_address" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Delivery Address</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ order.delivery_address }}</dd>
            </div>
            <div v-if="order.notes" class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Notes</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ order.notes }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Order Summary -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Subtotal</span>
              <span class="text-sm font-medium text-gray-900">${{ order.total_amount || order.total_value }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Shipping</span>
              <span class="text-sm font-medium text-gray-900">${{ order.shipping_cost || '0.00' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-500">Tax</span>
              <span class="text-sm font-medium text-gray-900">${{ order.tax || '0.00' }}</span>
            </div>
            <div class="border-t pt-3">
              <div class="flex justify-between">
                <span class="text-base font-medium text-gray-900">Total</span>
                <span class="text-base font-medium text-gray-900">${{ order.total_amount || order.total_value }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
          <div class="space-y-3">
            <div>
              <dt class="text-sm font-medium text-gray-500">Name</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ getContactName() }}</dd>
            </div>
            <div v-if="getContactEmail()">
              <dt class="text-sm font-medium text-gray-500">Email</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ getContactEmail() }}</dd>
            </div>
            <div v-if="getContactPhone()">
              <dt class="text-sm font-medium text-gray-500">Phone</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ getContactPhone() }}</dd>
            </div>
          </div>
        </div>

        <!-- Status Update Form -->
        <div v-if="canUpdateStatus" class="bg-white shadow rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Update Status</h3>
          <form @submit.prevent="submitStatusUpdate" class="space-y-4">
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
              <select
                id="status"
                v-model="statusForm.status"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div>
              <button
                type="submit"
                :disabled="statusLoading"
                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                {{ statusLoading ? 'Updating...' : 'Update Status' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const order = ref({})
const loading = ref(false)
const statusLoading = ref(false)

const statusForm = reactive({
  status: ''
})

const canUpdateStatus = computed(() => {
  // Only allow status updates for certain roles or order types
  return order.value.status !== 'delivered' && order.value.status !== 'cancelled'
})

const fetchOrder = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/orders/${route.params.id}`)
    order.value = response.data.order
    statusForm.status = order.value.status
  } catch (error) {
    console.error('Error fetching order:', error)
  } finally {
    loading.value = false
  }
}

const submitStatusUpdate = async () => {
  statusLoading.value = true
  try {
    await axios.patch(`/api/orders/${route.params.id}/status`, statusForm)
    await fetchOrder() // Refresh order data
  } catch (error) {
    console.error('Error updating order status:', error)
  } finally {
    statusLoading.value = false
  }
}

const updateStatus = () => {
  // This could open a modal or scroll to the status update form
  document.getElementById('status')?.focus()
}

const getItemName = (item) => {
  return item.inventory_item?.name || item.crop_name || item.name || 'Unknown Item'
}

const getContactName = () => {
  return order.value.supplier_name || order.value.buyer?.name || 'Unknown'
}

const getContactEmail = () => {
  return order.value.supplier_contact || order.value.buyer?.email || null
}

const getContactPhone = () => {
  return order.value.buyer?.phone || null
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800',
    confirmed: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800',
    shipped: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800',
    delivered: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800',
    cancelled: 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'
  }
  return classes[status] || classes.pending
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  fetchOrder()
})
</script>