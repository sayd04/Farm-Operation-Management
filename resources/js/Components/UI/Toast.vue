<template>
  <div class="fixed inset-0 flex flex-col items-end space-y-2 p-4 pointer-events-none z-50">
    <transition-group name="fade" tag="div" class="w-full flex flex-col items-end space-y-2">
      <div
        v-for="t in toasts"
        :key="t.id"
        class="pointer-events-auto max-w-sm w-full bg-white shadow-lg rounded-lg ring-1 ring-black ring-opacity-5 overflow-hidden"
      >
        <div :class="['p-4 border-l-4', typeClass(t.type)]">
          <div class="flex items-start">
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">{{ t.title }}</p>
              <p class="mt-1 text-sm text-gray-500">{{ t.message }}</p>
            </div>
            <button @click="dismiss(t.id)" class="ml-3 text-gray-400 hover:text-gray-600">✕</button>
          </div>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAlertsStore } from '@/stores/alerts';

const alerts = useAlertsStore();
const toasts = computed(() => alerts.toasts);
const dismiss = (id) => alerts.removeToast(id);
const typeClass = (type) => ({
  info: 'border-blue-400',
  success: 'border-green-400',
  warning: 'border-yellow-400',
  danger: 'border-red-400',
}[type] || 'border-gray-200');
</script>

