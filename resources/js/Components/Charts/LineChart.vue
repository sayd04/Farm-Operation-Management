<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <div v-if="title" class="mb-4">
      <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
      <p v-if="subtitle" class="text-sm text-gray-600">{{ subtitle }}</p>
    </div>
    
    <div class="h-64 relative">
      <svg class="w-full h-full" viewBox="0 0 400 200">
        <!-- Grid lines -->
        <defs>
          <pattern id="grid" width="40" height="20" patternUnits="userSpaceOnUse">
            <path d="M 40 0 L 0 0 0 20" fill="none" stroke="#f3f4f6" stroke-width="1"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />
        
        <!-- Line path -->
        <path
          :d="linePath"
          fill="none"
          stroke="#10b981"
          stroke-width="2"
          class="transition-all duration-300"
        />
        
        <!-- Data points -->
        <circle
          v-for="(point, index) in chartPoints"
          :key="index"
          :cx="point.x"
          :cy="point.y"
          r="4"
          fill="#10b981"
          class="cursor-pointer hover:r-6 transition-all duration-200"
          :title="`${point.label}: ${formatValue(point.value)}`"
          @click="handlePointClick(point, index)"
        />
      </svg>
      
      <!-- Y-axis labels -->
      <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-gray-500">
        <span v-for="tick in yTicks" :key="tick" class="transform -translate-y-1/2">
          {{ formatValue(tick) }}
        </span>
      </div>
      
      <!-- X-axis labels -->
      <div class="absolute bottom-0 left-0 w-full flex justify-between text-xs text-gray-500">
        <span
          v-for="(item, index) in data"
          :key="index"
          class="transform -translate-x-1/2"
          :style="{ left: `${(index / (data.length - 1)) * 100}%` }"
        >
          {{ item.label }}
        </span>
      </div>
    </div>
    
    <div v-if="showLegend" class="mt-4 flex justify-center">
      <div class="flex items-center space-x-2">
        <div class="w-4 h-0.5 bg-green-500"></div>
        <span class="text-sm text-gray-600">{{ title }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  showLegend: {
    type: Boolean,
    default: false
  },
  maxValue: {
    type: Number,
    default: null
  },
  minValue: {
    type: Number,
    default: null
  },
  formatValue: {
    type: Function,
    default: (value) => value
  }
});

const emit = defineEmits(['point-click']);

const maxDataValue = computed(() => {
  if (props.maxValue !== null) return props.maxValue;
  return Math.max(...props.data.map(item => item.value));
});

const minDataValue = computed(() => {
  if (props.minValue !== null) return props.minValue;
  return Math.min(...props.data.map(item => item.value));
});

const yTicks = computed(() => {
  const ticks = [];
  const range = maxDataValue.value - minDataValue.value;
  const tickCount = 5;
  const tickStep = range / (tickCount - 1);
  
  for (let i = 0; i < tickCount; i++) {
    ticks.push(minDataValue.value + (tickStep * i));
  }
  
  return ticks;
});

const chartPoints = computed(() => {
  return props.data.map((item, index) => {
    const x = (index / (props.data.length - 1)) * 380 + 20; // SVG viewBox width is 400
    const normalizedValue = (item.value - minDataValue.value) / (maxDataValue.value - minDataValue.value);
    const y = 180 - (normalizedValue * 160) + 20; // SVG viewBox height is 200
    
    return {
      x,
      y,
      label: item.label,
      value: item.value
    };
  });
});

const linePath = computed(() => {
  if (chartPoints.value.length < 2) return '';
  
  let path = `M ${chartPoints.value[0].x} ${chartPoints.value[0].y}`;
  
  for (let i = 1; i < chartPoints.value.length; i++) {
    path += ` L ${chartPoints.value[i].x} ${chartPoints.value[i].y}`;
  }
  
  return path;
});

const handlePointClick = (point, index) => {
  emit('point-click', { point, index });
};
</script>
</template>
</template>