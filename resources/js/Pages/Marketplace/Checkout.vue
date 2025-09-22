<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Checkout</h1>
    <form @submit.prevent="placeOrder" class="bg-white shadow rounded-lg p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Full Name</label>
          <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Shipping Address</label>
        <textarea v-model="form.address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">City</label>
          <input v-model="form.city" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">State/Province</label>
          <input v-model="form.state" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Postal Code</label>
          <input v-model="form.postal_code" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
        <select v-model="form.payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
          <option value="cod">Cash on Delivery</option>
          <option value="card">Credit/Debit Card</option>
        </select>
      </div>

      <div class="flex items-center justify-end">
        <button :disabled="submitting" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
          {{ submitting ? 'Placing Order...' : 'Place Order' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const submitting = ref(false);
const form = reactive({
  name: '',
  email: '',
  address: '',
  city: '',
  state: '',
  postal_code: '',
  payment_method: 'cod'
});

const placeOrder = async () => {
  submitting.value = true;
  try {
    const response = await axios.post('/api/marketplace/orders/checkout', form);
    router.push('/orders');
  } catch (e) {
    console.error('Failed to place order', e);
  } finally {
    submitting.value = false;
  }
};
</script>

