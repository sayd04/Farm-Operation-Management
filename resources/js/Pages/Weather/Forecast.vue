<template>
  <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
    <div v-for="day in forecast" :key="day.date" class="text-center p-4 border rounded-lg">
      <div class="text-sm font-medium text-gray-900">{{ formatDate(day.date) }}</div>
      <div class="text-3xl my-2">{{ getWeatherIcon(day.condition) }}</div>
      <div class="text-sm text-gray-600">{{ day.condition }}</div>
      <div class="text-sm text-gray-900">
        <span class="font-medium">{{ day.max_temp }}°</span>
        <span class="text-gray-500">{{ day.min_temp }}°</span>
      </div>
      <div class="text-xs text-gray-500">Rain: {{ day.rain_chance }}%</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  items: { type: Array, default: () => [] }
});

const forecast = computed(() => props.items || []);

const formatDate = (dateString) => new Date(dateString).toLocaleDateString('en-US', { weekday: 'short' });
const getWeatherIcon = (condition) => {
  const icons = { sunny: '☀️', clear: '☀️', 'partly-cloudy': '⛅', cloudy: '☁️', overcast: '☁️', rainy: '🌧️', stormy: '⛈️', foggy: '🌫️' };
  return icons[condition?.toLowerCase()] || '🌤️';
};
</script>

