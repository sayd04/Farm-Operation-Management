<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <div v-if="title" class="mb-4">
      <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
      <p v-if="subtitle" class="text-sm text-gray-600">{{ subtitle }}</p>
    </div>
    
    <div class="h-64 flex items-end justify-between space-x-2">
      <div
        v-for="(item, index) in data"
        :key="index"
        class="flex-1 flex flex-col items-center"
      >
        <div
          class="w-full bg-green-200 rounded-t transition-all duration-300 hover:bg-green-300 cursor-pointer"
          :style="{ height: `${getBarHeight(item.value)}%` }"
          :title="`${item.label}: ${item.value}`"
          @click="handleBarClick(item, index)"
        ></div>
        <div class="mt-2 text-xs text-gray-600 text-center">
          <div class="font-medium">{{ item.label }}</div>
          <div class="text-gray-500">{{ formatValue(item.value) }}</div>
        </div>
      </div>
    </div>
    
    <div v-if="showLegend" class="mt-4 flex flex-wrap gap-2">
      <div
        v-for="(item, index) in data"
        :key="index"
        class="flex items-center space-x-2"
      >
        <div class="w-3 h-3 bg-green-400 rounded"></div>
        <span class="text-sm text-gray-600">{{ item.label }}</span>
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
  formatValue: {
    type: Function,
    default: (value) => value
  }
});

const emit = defineEmits(['bar-click']);

const maxDataValue = computed(() => {
  if (props.maxValue !== null) return props.maxValue;
  return Math.max(...props.data.map(item => item.value));
});

const getBarHeight = (value) => {
  if (maxDataValue.value === 0) return 0;
  return (value / maxDataValue.value) * 100;
};

const handleBarClick = (item, index) => {
  emit('bar-click', { item, index });
};
</script>
</template>