<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Weather Analytics</h1>
      <p class="mt-2 text-gray-600">Analyze weather trends and their impact on yield</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Temperature Trend</h3>
        <LineChart :data="temperatureData" title="Temperature" :format-value="v => v + '°C'" />
      </div>
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Rainfall</h3>
        <BarChart :data="rainfallData" title="Rainfall" :format-value="v => v + 'mm'" />
      </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-8">
      <h3 class="text-lg font-medium text-gray-900 mb-4">5-Day Forecast</h3>
      <Forecast :items="forecast" />
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Historical Weather Log</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temperature</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rainfall</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Humidity</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wind</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="w in history" :key="w.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ new Date(w.date).toLocaleDateString() }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ w.temperature }}°C</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ w.rainfall }} mm</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ w.humidity }}%</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ w.wind_speed }} km/h</td>
            </tr>
            <tr v-if="history.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-500">No historical data</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LineChart from '@/Components/Charts/LineChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import Forecast from '@/Pages/Weather/Forecast.vue';

const temperatureData = ref([]);
const rainfallData = ref([]);
const forecast = ref([]);
const history = ref([]);

const load = async () => {
  try {
    const [forecastRes, historyRes] = await Promise.all([
      axios.get('/api/weather/forecast'),
      axios.get('/api/weather/history')
    ]);
    forecast.value = forecastRes.data || [];
    history.value = historyRes.data?.data || historyRes.data || [];
    temperatureData.value = history.value.map(h => ({ label: new Date(h.date).toLocaleDateString(), value: h.temperature }));
    rainfallData.value = history.value.map(h => ({ label: new Date(h.date).toLocaleDateString(), value: h.rainfall }));
  } catch (e) {
    console.error('Failed to load analytics', e);
  }
};

onMounted(load);
</script>

