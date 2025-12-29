<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h1 class="text-2xl font-bold text-white">Analityka</h1>
            <p class="text-sm text-gray-400">Statystyki i raporty wydajności</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- Date Range -->
            <select v-model="dateRange" class="bg-gray-800 text-white rounded-xl px-4 py-2 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
              <option value="7d">Ostatnie 7 dni</option>
              <option value="30d">Ostatnie 30 dni</option>
              <option value="90d">Ostatnie 90 dni</option>
              <option value="1y">Ostatni rok</option>
            </select>

            <button class="flex items-center px-4 py-2 bg-gray-800 text-white font-medium rounded-xl border border-gray-700/50 hover:bg-gray-700 transition-all">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Eksportuj PDF
            </button>
          </div>
        </div>
      </header>

      <div class="p-6">
        <!-- Main Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div v-for="stat in mainStats" :key="stat.label" class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center" :class="stat.bgColor">
                <component :is="stat.icon" class="w-6 h-6" :class="stat.iconColor" />
              </div>
              <span :class="stat.trend > 0 ? 'text-green-400' : 'text-red-400'" class="text-sm font-medium flex items-center">
                <svg v-if="stat.trend > 0" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                {{ Math.abs(stat.trend) }}%
              </span>
            </div>
            <p class="text-3xl font-bold text-white mb-1">{{ stat.value }}</p>
            <p class="text-sm text-gray-400">{{ stat.label }}</p>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Ticket Trend Chart -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-6">Trend zgłoszeń</h3>
            <div class="h-64 flex items-end justify-between space-x-2">
              <div v-for="(bar, index) in chartData" :key="index" class="flex-1 flex flex-col items-center">
                <div
                  class="w-full rounded-t-lg bg-gradient-to-t from-indigo-600 to-purple-600 transition-all duration-500"
                  :style="{ height: `${bar.value}%` }"
                ></div>
                <span class="text-xs text-gray-500 mt-2">{{ bar.label }}</span>
              </div>
            </div>
          </div>

          <!-- Resolution Time -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-6">Czas rozwiązania</h3>
            <div class="space-y-4">
              <div v-for="item in resolutionStats" :key="item.label">
                <div class="flex items-center justify-between text-sm mb-2">
                  <span class="text-gray-400">{{ item.label }}</span>
                  <span class="text-white font-medium">{{ item.value }}</span>
                </div>
                <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                  <div class="h-full rounded-full transition-all duration-500" :class="item.color" :style="{ width: `${item.percent}%` }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Category Distribution -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-6">Kategorie zgłoszeń</h3>
            <div class="space-y-4">
              <div v-for="cat in categories" :key="cat.name" class="flex items-center">
                <div class="w-3 h-3 rounded-full mr-3" :class="cat.color"></div>
                <span class="flex-1 text-gray-300">{{ cat.name }}</span>
                <span class="text-white font-medium">{{ cat.count }}</span>
                <span class="text-gray-500 text-sm ml-2">({{ cat.percent }}%)</span>
              </div>
            </div>
          </div>

          <!-- Top Agents -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-6">Top agenci</h3>
            <div class="space-y-4">
              <div v-for="(agent, index) in topAgents" :key="agent.name" class="flex items-center">
                <span class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-xs font-bold text-white mr-3">
                  {{ index + 1 }}
                </span>
                <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center mr-3">
                  <span class="text-white text-sm font-medium">{{ agent.name.charAt(0) }}</span>
                </div>
                <div class="flex-1">
                  <p class="text-white font-medium">{{ agent.name }}</p>
                  <p class="text-xs text-gray-500">{{ agent.tickets }} zgłoszeń</p>
                </div>
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <span class="ml-1 text-gray-400">{{ agent.rating }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Customer Satisfaction -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-6">Zadowolenie klientów</h3>
            <div class="flex items-center justify-center mb-6">
              <div class="relative">
                <svg class="w-32 h-32 transform -rotate-90">
                  <circle cx="64" cy="64" r="56" stroke="#1f2937" stroke-width="8" fill="none" />
                  <circle cx="64" cy="64" r="56" stroke="url(#gradient)" stroke-width="8" fill="none" stroke-linecap="round" :stroke-dasharray="`${4.2 * 351.86 / 5} 351.86`" />
                  <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                      <stop offset="0%" stop-color="#6366f1" />
                      <stop offset="100%" stop-color="#a855f7" />
                    </linearGradient>
                  </defs>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                  <span class="text-3xl font-bold text-white">4.2</span>
                  <span class="text-sm text-gray-400">/ 5.0</span>
                </div>
              </div>
            </div>
            <div class="space-y-2">
              <div v-for="rating in ratings" :key="rating.stars" class="flex items-center">
                <span class="text-sm text-gray-400 w-12">{{ rating.stars }} ⭐</span>
                <div class="flex-1 h-2 bg-gray-800 rounded-full mx-2 overflow-hidden">
                  <div class="h-full bg-yellow-400 rounded-full" :style="{ width: `${rating.percent}%` }"></div>
                </div>
                <span class="text-sm text-gray-400 w-10 text-right">{{ rating.percent }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, h } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const dateRange = ref('30d')

const TicketIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' })]) }
const CheckIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 13l4 4L19 7' })]) }
const ClockIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })]) }
const StarIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z' })]) }

const mainStats = [
  { label: 'Nowe zgłoszenia', value: '284', trend: 12, bgColor: 'bg-blue-500/20', iconColor: 'text-blue-400', icon: TicketIcon },
  { label: 'Rozwiązane', value: '256', trend: 8, bgColor: 'bg-green-500/20', iconColor: 'text-green-400', icon: CheckIcon },
  { label: 'Średni czas', value: '2.4h', trend: -15, bgColor: 'bg-yellow-500/20', iconColor: 'text-yellow-400', icon: ClockIcon },
  { label: 'Ocena klientów', value: '4.2', trend: 5, bgColor: 'bg-purple-500/20', iconColor: 'text-purple-400', icon: StarIcon },
]

const chartData = [
  { label: 'Pon', value: 65 },
  { label: 'Wt', value: 80 },
  { label: 'Śr', value: 55 },
  { label: 'Czw', value: 90 },
  { label: 'Pt', value: 75 },
  { label: 'Sob', value: 30 },
  { label: 'Nd', value: 20 },
]

const resolutionStats = [
  { label: '< 1 godz', value: '45%', percent: 45, color: 'bg-green-500' },
  { label: '1-4 godz', value: '30%', percent: 30, color: 'bg-blue-500' },
  { label: '4-24 godz', value: '15%', percent: 15, color: 'bg-yellow-500' },
  { label: '> 24 godz', value: '10%', percent: 10, color: 'bg-red-500' },
]

const categories = [
  { name: 'Problemy techniczne', count: 89, percent: 35, color: 'bg-blue-500' },
  { name: 'Płatności', count: 64, percent: 25, color: 'bg-green-500' },
  { name: 'Konta użytkowników', count: 51, percent: 20, color: 'bg-yellow-500' },
  { name: 'Ogólne pytania', count: 38, percent: 15, color: 'bg-purple-500' },
  { name: 'Inne', count: 13, percent: 5, color: 'bg-gray-500' },
]

const topAgents = [
  { name: 'Tomasz Zieliński', tickets: 203, rating: '4.9' },
  { name: 'Jan Kowalski', tickets: 156, rating: '4.8' },
  { name: 'Piotr Wiśniewski', tickets: 124, rating: '4.7' },
  { name: 'Anna Nowak', tickets: 89, rating: '4.6' },
]

const ratings = [
  { stars: 5, percent: 45 },
  { stars: 4, percent: 30 },
  { stars: 3, percent: 15 },
  { stars: 2, percent: 7 },
  { stars: 1, percent: 3 },
]
</script>
