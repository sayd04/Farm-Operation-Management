<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Tasks</h1>
        <p class="mt-2 text-gray-600">Manage rice cultivation tasks</p>
      </div>
      <router-link to="/tasks/create" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">New Task</router-link>
    </div>

    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">All Tasks</h3>
      </div>
      <div v-if="loading" class="p-6">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
      </div>
      <div v-else class="divide-y divide-gray-200">
        <div v-for="t in tasks" :key="t.id" class="px-6 py-4 flex items-center justify-between">
          <div>
            <h4 class="text-sm font-medium text-gray-900">{{ prettyType(t.type) }}</h4>
            <p class="text-xs text-gray-500">{{ t.description }}</p>
            <p class="text-xs text-gray-400">Due: {{ fmt(t.due_date) }} • Field: {{ t.field?.name || t.field_id }}</p>
          </div>
          <span :class="statusClass(t.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">{{ t.status.replace('_', ' ') }}</span>
        </div>
        <div v-if="tasks.length === 0" class="px-6 py-12 text-center text-gray-500">No tasks yet.</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const tasks = ref([]);

const riceTaskTypes = ['Land Preparation','Seedling','Transplanting','Fertilizing','Weeding','Pesticide Application','Water Management','Harvesting'];

const prettyType = (type) => type?.replace('_', ' ') || '';
const fmt = (d) => d ? new Date(d).toLocaleString() : '';
const statusClass = (s) => ({
  pending: 'bg-yellow-100 text-yellow-800',
  in_progress: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
}[s] || 'bg-gray-100 text-gray-800');

const fetchTasks = async () => {
  try {
    const res = await axios.get('/api/tasks');
    tasks.value = (res.data?.data || res.data || []).filter(t => riceTaskTypes.includes(t.type) || riceTaskTypes.includes(prettyType(t.type)));
  } catch (e) {
    console.error('Failed to load tasks', e);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchTasks);
</script>

