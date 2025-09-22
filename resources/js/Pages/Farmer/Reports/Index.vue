<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
      <p class="mt-2 text-gray-600">Yield, financials, and weather correlation</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Yield Over Time</h3>
        <LineChart :data="yieldSeries" title="Yield (kg/ha)" :format-value="v => v + ' kg/ha'" />
      </div>
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Yield by Varietal</h3>
        <BarChart :data="yieldByVarietal" title="Yield by Varietal" :format-value="v => v + ' kg/ha'" />
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Income vs Expenses</h3>
        <LineChart :data="financialSeries" title="Income vs Expenses" :format-value="v => '$' + v" />
      </div>
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Rainfall vs Yield</h3>
        <LineChart :data="rainfallVsYield" title="Weather Correlation" :format-value="v => v" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LineChart from '@/Components/Charts/LineChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';

const yieldSeries = ref([]);
const yieldByVarietal = ref([]);
const financialSeries = ref([]);
const rainfallVsYield = ref([]);

const load = async () => {
  try {
    const [yieldRes, varietalRes, financeRes, corrRes] = await Promise.all([
      axios.get('/api/reports/yield'),
      axios.get('/api/reports/yield-by-varietal'),
      axios.get('/api/reports/financial'),
      axios.get('/api/reports/weather-correlation')
    ]);
    const y = yieldRes.data || [];
    yieldSeries.value = y.map(r => ({ label: r.period || r.date, value: r.yield_kg_per_ha }));

    const yv = varietalRes.data || [];
    yieldByVarietal.value = yv.map(r => ({ label: r.varietal, value: r.yield_kg_per_ha }));

    const f = financeRes.data || [];
    financialSeries.value = f.map(r => ({ label: r.period, value: r.income - r.expenses }));

    const c = corrRes.data || [];
    rainfallVsYield.value = c.map(r => ({ label: r.period, value: r.rainfall_mm + 'mm / ' + r.yield_kg_per_ha + 'kg/ha' }));
  } catch (e) {
    console.error('Failed to load reports', e);
  }
};

onMounted(load);
</script>

