<template>
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <!-- Table Header -->
    <div v-if="title" class="px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
    </div>

    <!-- Search and Actions -->
    <div v-if="searchable || actions" class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <!-- Search -->
        <div v-if="searchable" class="flex-1 max-w-lg">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Search..."
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm"
            />
          </div>
        </div>

        <!-- Actions -->
        <div v-if="actions" class="flex space-x-3">
          <slot name="actions"></slot>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              :class="{ 'cursor-pointer': column.sortable }"
              @click="column.sortable ? sort(column.key) : null"
            >
              <div class="flex items-center space-x-1">
                <span>{{ column.label }}</span>
                <span v-if="column.sortable" class="flex flex-col">
                  <svg
                    class="w-3 h-3 text-gray-400"
                    :class="{ 'text-gray-600': sortKey === column.key && sortDirection === 'asc' }"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                  </svg>
                  <svg
                    class="w-3 h-3 text-gray-400 -mt-1"
                    :class="{ 'text-gray-600': sortKey === column.key && sortDirection === 'desc' }"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" />
                  </svg>
                </span>
              </div>
            </th>
            <th v-if="actions" scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="loading">
            <td :colspan="columns.length + (actions ? 1 : 0)" class="px-6 py-12 text-center">
              <div class="flex justify-center items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                <span class="ml-2 text-gray-600">Loading...</span>
              </div>
            </td>
          </tr>
          <tr v-else-if="filteredData.length === 0">
            <td :colspan="columns.length + (actions ? 1 : 0)" class="px-6 py-12 text-center">
              <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No data found</h3>
                <p class="mt-1 text-sm text-gray-500">No records match your current search criteria.</p>
              </div>
            </td>
          </tr>
          <tr
            v-else
            v-for="(row, index) in paginatedData"
            :key="getRowKey(row, index)"
            class="hover:bg-gray-50"
          >
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
            >
              <slot
                :name="`cell-${column.key}`"
                :row="row"
                :value="getNestedValue(row, column.key)"
                :column="column"
              >
                <span v-if="column.type === 'date'">
                  {{ formatDate(getNestedValue(row, column.key)) }}
                </span>
                <span v-else-if="column.type === 'currency'">
                  ${{ getNestedValue(row, column.key) }}
                </span>
                <span v-else-if="column.type === 'badge'">
                  <span
                    :class="getBadgeClass(getNestedValue(row, column.key), column.badgeOptions)"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  >
                    {{ getNestedValue(row, column.key) }}
                  </span>
                </span>
                <span v-else>
                  {{ getNestedValue(row, column.key) }}
                </span>
              </slot>
            </td>
            <td v-if="actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end space-x-2">
                <slot name="row-actions" :row="row" :index="index">
                  <button
                    v-if="actions.includes('edit')"
                    @click="$emit('edit', row)"
                    class="text-green-600 hover:text-green-900"
                  >
                    Edit
                  </button>
                  <button
                    v-if="actions.includes('delete')"
                    @click="$emit('delete', row)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Delete
                  </button>
                </slot>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="paginated && totalPages > 1" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, filteredData.length) }} of {{ filteredData.length }} results
        </div>
        <div class="flex space-x-2">
          <button
            @click="currentPage = Math.max(1, currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="currentPage = page"
            :class="[
              'px-3 py-1 text-sm border rounded-md',
              page === currentPage
                ? 'bg-green-600 text-white border-green-600'
                : 'border-gray-300 hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>
          <button
            @click="currentPage = Math.min(totalPages, currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Array,
    required: true
  },
  title: {
    type: String,
    default: ''
  },
  searchable: {
    type: Boolean,
    default: false
  },
  actions: {
    type: [Array, Boolean],
    default: false
  },
  paginated: {
    type: Boolean,
    default: false
  },
  perPage: {
    type: Number,
    default: 10
  },
  loading: {
    type: Boolean,
    default: false
  },
  rowKey: {
    type: String,
    default: 'id'
  }
});

const emit = defineEmits(['edit', 'delete', 'sort']);

const searchTerm = ref('');
const sortKey = ref('');
const sortDirection = ref('asc');
const currentPage = ref(1);

const filteredData = computed(() => {
  let filtered = props.data;

  // Search filter
  if (searchTerm.value && props.searchable) {
    const term = searchTerm.value.toLowerCase();
    filtered = filtered.filter(row =>
      props.columns.some(column => {
        const value = getNestedValue(row, column.key);
        return value && value.toString().toLowerCase().includes(term);
      })
    );
  }

  // Sort
  if (sortKey.value) {
    filtered = [...filtered].sort((a, b) => {
      const aVal = getNestedValue(a, sortKey.value);
      const bVal = getNestedValue(b, sortKey.value);
      
      if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1;
      if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1;
      return 0;
    });
  }

  return filtered;
});

const totalPages = computed(() => {
  return Math.ceil(filteredData.value.length / props.perPage);
});

const paginatedData = computed(() => {
  if (!props.paginated) return filteredData.value;
  
  const start = (currentPage.value - 1) * props.perPage;
  const end = start + props.perPage;
  return filteredData.value.slice(start, end);
});

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  
  let start = Math.max(1, current - 2);
  let end = Math.min(total, current + 2);
  
  if (end - start < 4) {
    if (start === 1) {
      end = Math.min(total, start + 4);
    } else {
      start = Math.max(1, end - 4);
    }
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

const sort = (key) => {
  if (sortKey.value === key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortDirection.value = 'asc';
  }
  emit('sort', { key, direction: sortDirection.value });
};

const getNestedValue = (obj, path) => {
  return path.split('.').reduce((current, key) => current?.[key], obj);
};

const getRowKey = (row, index) => {
  return getNestedValue(row, props.rowKey) || index;
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString();
};

const getBadgeClass = (value, options = {}) => {
  const defaultClasses = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800',
    pending: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-gray-100 text-gray-800'
  };
  
  const classes = { ...defaultClasses, ...options };
  return classes[value] || 'bg-gray-100 text-gray-800';
};

// Reset pagination when data changes
watch(() => props.data, () => {
  currentPage.value = 1;
});

// Reset pagination when search changes
watch(searchTerm, () => {
  currentPage.value = 1;
});
</script>
</template>