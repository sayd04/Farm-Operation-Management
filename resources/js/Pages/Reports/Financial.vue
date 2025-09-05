<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
          Financial Reports
        </h2>
        <p class="mt-1 text-sm text-gray-500">
          Track income, expenses, and profitability
        </p>
      </div>
      <div class="mt-4 flex md:mt-0 md:ml-4">
        <button
          @click="exportReport"
          class="bg-white border border-gray-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Export Report
        </button>
      </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Report Period</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
          <input
            id="date_from"
            v-model="filters.date_from"
            type="date"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div>
          <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
          <input
            id="date_to"
            v-model="filters.date_to"
            type="date"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>
        <div class="flex items-end">
          <button
            @click="generateReport"
            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Generate Report
          </button>
        </div>
      </div>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                <dd class="text-lg font-medium text-gray-900">${{ summary.total_revenue || '0.00' }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Expenses</dt>
                <dd class="text-lg font-medium text-gray-900">${{ summary.total_expenses || '0.00' }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Net Profit</dt>
                <dd class="text-lg font-medium" :class="summary.net_profit >= 0 ? 'text-green-600' : 'text-red-600'">
                  ${{ summary.net_profit || '0.00' }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Profit Margin</dt>
                <dd class="text-lg font-medium text-gray-900">{{ summary.profit_margin || '0' }}%</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Revenue vs Expenses Chart -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue vs Expenses</h3>
      <div class="h-64 flex items-end justify-between space-x-2">
        <div
          v-for="(month, index) in monthlyData"
          :key="index"
          class="flex-1 flex flex-col items-center"
        >
          <div class="w-full flex flex-col items-end space-y-1 mb-2">
            <div
              class="bg-green-500 rounded-t"
              :style="{ height: `${(month.revenue / maxValue) * 200}px`, width: '100%' }"
              :title="`Revenue: $${month.revenue}`"
            ></div>
            <div
              class="bg-red-500 rounded-t"
              :style="{ height: `${(month.expenses / maxValue) * 200}px`, width: '100%' }"
              :title="`Expenses: $${month.expenses}`"
            ></div>
          </div>
          <div class="text-xs text-gray-500">{{ month.month }}</div>
        </div>
      </div>
      <div class="flex justify-center space-x-6 mt-4">
        <div class="flex items-center">
          <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
          <span class="text-sm text-gray-600">Revenue</span>
        </div>
        <div class="flex items-center">
          <div class="w-3 h-3 bg-red-500 rounded mr-2"></div>
          <span class="text-sm text-gray-600">Expenses</span>
        </div>
      </div>
    </div>

    <!-- Expense Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Expense Breakdown</h3>
        <div class="space-y-3">
          <div
            v-for="category in expenseBreakdown"
            :key="category.name"
            class="flex items-center justify-between"
          >
            <div class="flex items-center">
              <div class="w-3 h-3 rounded-full mr-3" :style="{ backgroundColor: category.color }"></div>
              <span class="text-sm font-medium text-gray-900">{{ category.name }}</span>
            </div>
            <div class="text-right">
              <div class="text-sm font-medium text-gray-900">${{ category.amount }}</div>
              <div class="text-xs text-gray-500">{{ category.percentage }}%</div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Revenue Sources</h3>
        <div class="space-y-3">
          <div
            v-for="source in revenueSources"
            :key="source.name"
            class="flex items-center justify-between"
          >
            <div class="flex items-center">
              <div class="w-3 h-3 rounded-full mr-3" :style="{ backgroundColor: source.color }"></div>
              <span class="text-sm font-medium text-gray-900">{{ source.name }}</span>
            </div>
            <div class="text-right">
              <div class="text-sm font-medium text-gray-900">${{ source.amount }}</div>
              <div class="text-xs text-gray-500">{{ source.percentage }}%</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Transactions -->
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Description
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Type
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="transaction in recentTransactions" :key="transaction.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(transaction.date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ transaction.description }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ transaction.type }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                  :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'">
                {{ transaction.type === 'income' ? '+' : '-' }}${{ transaction.amount }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'

const summary = ref({})
const monthlyData = ref([])
const expenseBreakdown = ref([])
const revenueSources = ref([])
const recentTransactions = ref([])
const loading = ref(false)

const filters = reactive({
  date_from: '',
  date_to: ''
})

const maxValue = computed(() => {
  if (monthlyData.value.length === 0) return 1
  return Math.max(...monthlyData.value.map(month => Math.max(month.revenue, month.expenses)))
})

const generateReport = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.date_from) params.append('date_from', filters.date_from)
    if (filters.date_to) params.append('date_to', filters.date_to)
    
    const response = await axios.get(`/api/reports/financial?${params}`)
    const data = response.data
    
    summary.value = data.summary || {}
    monthlyData.value = data.monthly_data || []
    expenseBreakdown.value = data.expense_breakdown || []
    revenueSources.value = data.revenue_sources || []
    recentTransactions.value = data.recent_transactions || []
  } catch (error) {
    console.error('Error generating financial report:', error)
  } finally {
    loading.value = false
  }
}

const exportReport = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.date_from) params.append('date_from', filters.date_from)
    if (filters.date_to) params.append('date_to', filters.date_to)
    
    const response = await axios.get(`/api/reports/financial/export?${params}`, { responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'financial-report.pdf')
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Error exporting report:', error)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  // Set default date range to last 30 days
  const today = new Date()
  const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000))
  
  filters.date_from = thirtyDaysAgo.toISOString().split('T')[0]
  filters.date_to = today.toISOString().split('T')[0]
  
  generateReport()
})
</script>