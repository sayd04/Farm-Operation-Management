<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Tasks Calendar</h1>
      <p class="mt-2 text-gray-600">Visualize rice cultivation schedule</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
        <div v-for="day in week" :key="day.date" class="border rounded-lg p-3">
          <div class="text-sm font-medium text-gray-900">{{ day.label }}</div>
          <div class="mt-2 space-y-2">
            <div v-for="task in day.tasks" :key="task.id" class="text-xs px-2 py-1 rounded bg-green-50 border border-green-200">
              {{ task.type }} — {{ new Date(task.due_date).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const week = ref([]);

const generateWeek = (tasks) => {
  const start = new Date();
  start.setHours(0,0,0,0);
  const days = [];
  for (let i = 0; i < 7; i++) {
    const d = new Date(start);
    d.setDate(start.getDate() + i);
    const label = d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
    const tasksForDay = tasks.filter(t => {
      const due = new Date(t.due_date);
      return due.getFullYear() === d.getFullYear() && due.getMonth() === d.getMonth() && due.getDate() === d.getDate();
    });
    days.push({ date: d.toISOString().slice(0,10), label, tasks: tasksForDay });
  }
  week.value = days;
};

const loadTasks = async () => {
  try {
    const res = await axios.get('/api/tasks?range=7');
    const tasks = res.data?.data || res.data || [];
    generateWeek(tasks);
  } catch (e) {
    console.error('Failed to load tasks', e);
  }
};

onMounted(loadTasks);
</script>

