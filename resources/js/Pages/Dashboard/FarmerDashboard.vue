<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Farmer Dashboard</h1>
      <p class="mt-2 text-gray-600">Overview of your rice operations</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <div class="lg:col-span-2">
        <WeatherDashboard />
      </div>
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-3">
          <button @click="$router.push('/tasks/create')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Create New Task</button>
          <button @click="$router.push('/harvests/create')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Record Harvest</button>
          <button @click="$router.push('/inventory')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Check Inventory</button>
        </div>
      </div>
    </div>

    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Upcoming Tasks (7 days)</h3>
      </div>
      <div class="divide-y divide-gray-200">
        <div v-for="task in upcomingTasks" :key="task.id" class="px-6 py-4 flex items-center justify-between">
          <div>
            <h4 class="text-sm font-medium text-gray-900">{{ task.type }}</h4>
            <p class="text-sm text-gray-500">{{ task.description }}</p>
          </div>
          <span class="text-xs text-gray-500">{{ new Date(task.due_date).toLocaleString() }}</span>
        </div>
        <div v-if="!upcomingTasks.length" class="px-6 py-8 text-center text-gray-500">No upcoming tasks</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import WeatherDashboard from '@/Pages/Weather/Dashboard.vue';
import axios from 'axios';

const upcomingTasks = ref([]);

const fetchUpcomingTasks = async () => {
  try {
    const res = await axios.get('/api/tasks?range=7');
    upcomingTasks.value = res.data?.data || res.data || [];
  } catch (e) {
    console.error('Failed to load upcoming tasks', e);
  }
};

onMounted(fetchUpcomingTasks);
</script>

