<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Weather Dashboard</h1>
      <p class="mt-2 text-gray-600">
        Monitor weather conditions and forecasts for your farm
      </p>
    </div>

    <!-- Current Weather Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Current Conditions</h3>
        <div v-if="loading" class="flex justify-center items-center h-32">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
        </div>
        <div v-else-if="currentWeather" class="space-y-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="text-6xl">
                {{ getWeatherIcon(currentWeather.condition) }}
              </div>
              <div>
                <div class="text-4xl font-bold text-gray-900">{{ currentWeather.temperature }}°C</div>
                <div class="text-lg text-gray-600">{{ currentWeather.condition }}</div>
                <div class="text-sm text-gray-500">{{ currentWeather.location }}</div>
              </div>
            </div>
            <div class="text-right text-sm text-gray-500">
              <div>Feels like {{ currentWeather.feels_like }}°C</div>
              <div>Humidity: {{ currentWeather.humidity }}%</div>
              <div>Wind: {{ currentWeather.wind_speed }} km/h</div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Weather Alerts</h3>
        <div v-if="alerts.length === 0" class="text-center text-gray-500 py-8">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="mt-2">No weather alerts</p>
        </div>
        <div v-else class="space-y-3">
          <div
            v-for="alert in alerts"
            :key="alert.id"
            class="p-3 rounded-lg border-l-4"
            :class="getAlertClass(alert.severity)"
          >
            <div class="flex items-center justify-between">
              <div>
                <h4 class="font-medium text-gray-900">{{ alert.title }}</h4>
                <p class="text-sm text-gray-600">{{ alert.description }}</p>
              </div>
              <span class="text-xs text-gray-500">{{ formatDate(alert.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Forecast -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
      <h3 class="text-lg font-medium text-gray-900 mb-4">7-Day Forecast</h3>
      <div v-if="loading" class="flex justify-center items-center h-32">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-7 gap-4">
        <div
          v-for="day in forecast"
          :key="day.date"
          class="text-center p-4 border rounded-lg hover:bg-gray-50"
        >
          <div class="text-sm font-medium text-gray-900">{{ formatDateShort(day.date) }}</div>
          <div class="text-3xl my-2">{{ getWeatherIcon(day.condition) }}</div>
          <div class="text-sm text-gray-600">{{ day.condition }}</div>
          <div class="text-sm text-gray-900">
            <span class="font-medium">{{ day.max_temp }}°</span>
            <span class="text-gray-500">{{ day.min_temp }}°</span>
          </div>
          <div class="text-xs text-gray-500">Rain: {{ day.rain_chance }}%</div>
        </div>
      </div>
    </div>

    <!-- Weather Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Temperature Trend</h3>
        <LineChart
          :data="temperatureData"
          title="Temperature"
          :format-value="(value) => value + '°C'"
        />
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Precipitation</h3>
        <BarChart
          :data="precipitationData"
          title="Rainfall"
          :format-value="(value) => value + 'mm'"
        />
      </div>
    </div>

    <!-- Field-Specific Weather -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Field Weather Conditions</h3>
      <div v-if="fieldWeather.length === 0" class="text-center text-gray-500 py-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <p class="mt-2">No field weather data available</p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="field in fieldWeather"
          :key="field.field_id"
          class="border rounded-lg p-4"
        >
          <div class="flex items-center justify-between mb-2">
            <h4 class="font-medium text-gray-900">{{ field.field_name }}</h4>
            <span class="text-2xl">{{ getWeatherIcon(field.condition) }}</span>
          </div>
          <div class="space-y-1 text-sm text-gray-600">
            <div>Temperature: {{ field.temperature }}°C</div>
            <div>Humidity: {{ field.humidity }}%</div>
            <div>Soil Moisture: {{ field.soil_moisture }}%</div>
            <div>Last Updated: {{ formatTime(field.updated_at) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import axios from 'axios';

const loading = ref(true);
const currentWeather = ref(null);
const forecast = ref([]);
const alerts = ref([]);
const fieldWeather = ref([]);
const temperatureData = ref([]);
const precipitationData = ref([]);

const fetchWeatherData = async () => {
  try {
    // Fetch current weather
    const currentResponse = await axios.get('/api/weather/current');
    currentWeather.value = currentResponse.data;

    // Fetch forecast
    const forecastResponse = await axios.get('/api/weather/forecast');
    forecast.value = forecastResponse.data;

    // Fetch alerts
    const alertsResponse = await axios.get('/api/weather/alerts');
    alerts.value = alertsResponse.data;

    // Fetch field weather
    const fieldResponse = await axios.get('/api/weather/fields');
    fieldWeather.value = fieldResponse.data;

    // Prepare chart data
    prepareChartData();
  } catch (error) {
    console.error('Failed to fetch weather data:', error);
  } finally {
    loading.value = false;
  }
};

const prepareChartData = () => {
  // Prepare temperature data for chart
  temperatureData.value = forecast.value.map(day => ({
    label: formatDateShort(day.date),
    value: day.avg_temp || ((day.max_temp + day.min_temp) / 2)
  }));

  // Prepare precipitation data for chart
  precipitationData.value = forecast.value.map(day => ({
    label: formatDateShort(day.date),
    value: day.precipitation || 0
  }));
};

const getWeatherIcon = (condition) => {
  const icons = {
    'sunny': '☀️',
    'clear': '☀️',
    'partly-cloudy': '⛅',
    'cloudy': '☁️',
    'overcast': '☁️',
    'rainy': '🌧️',
    'stormy': '⛈️',
    'snowy': '❄️',
    'foggy': '🌫️'
  };
  return icons[condition?.toLowerCase()] || '🌤️';
};

const getAlertClass = (severity) => {
  const classes = {
    'low': 'bg-blue-50 border-blue-400',
    'medium': 'bg-yellow-50 border-yellow-400',
    'high': 'bg-red-50 border-red-400',
    'critical': 'bg-red-100 border-red-600'
  };
  return classes[severity] || 'bg-gray-50 border-gray-400';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const formatDateShort = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', { weekday: 'short' });
};

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('en-US', { 
    hour: '2-digit', 
    minute: '2-digit' 
  });
};

onMounted(() => {
  fetchWeatherData();
});
</script>
</template>