<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h1 class="text-2xl font-bold text-white">Dziennik aktywności</h1>
            <p class="text-sm text-gray-400">Historia wszystkich akcji w systemie</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <select v-model="filterType" class="bg-gray-800 text-white rounded-xl px-4 py-2 border border-gray-700/50">
              <option value="all">Wszystkie typy</option>
              <option value="ticket">Zgłoszenia</option>
              <option value="user">Użytkownicy</option>
              <option value="system">System</option>
              <option value="security">Bezpieczeństwo</option>
            </select>

            <select v-model="filterPeriod" class="bg-gray-800 text-white rounded-xl px-4 py-2 border border-gray-700/50">
              <option value="today">Dzisiaj</option>
              <option value="week">Ostatni tydzień</option>
              <option value="month">Ostatni miesiąc</option>
              <option value="all">Cała historia</option>
            </select>

            <button class="flex items-center px-4 py-2 bg-gray-800 text-white font-medium rounded-xl border border-gray-700/50 hover:bg-gray-700">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Eksportuj
            </button>
          </div>
        </div>
      </header>

      <div class="p-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400">Dzisiaj</p>
                <p class="text-2xl font-bold text-white">{{ todayCount }}</p>
              </div>
              <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400">Ten tydzień</p>
                <p class="text-2xl font-bold text-white">{{ weekCount }}</p>
              </div>
              <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400">Ostrzeżenia</p>
                <p class="text-2xl font-bold text-yellow-400">{{ warningCount }}</p>
              </div>
              <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400">Błędy</p>
                <p class="text-2xl font-bold text-red-400">{{ errorCount }}</p>
              </div>
              <div class="w-10 h-10 rounded-lg bg-red-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Activity Timeline -->
        <div class="bg-gray-900 rounded-2xl border border-gray-700/50 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-700/50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Ostatnia aktywność</h3>
            <button @click="refreshLogs" class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
              <svg class="w-5 h-5" :class="{ 'animate-spin': isRefreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>

          <div class="divide-y divide-gray-800">
            <div
              v-for="log in filteredLogs"
              :key="log.id"
              class="px-6 py-4 hover:bg-gray-800/50 transition-colors"
            >
              <div class="flex items-start">
                <!-- Icon -->
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-4 flex-shrink-0" :class="getTypeClass(log.type)">
                  <component :is="getTypeIcon(log.type)" class="w-5 h-5" />
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-1">
                    <h4 class="font-medium text-white">{{ log.action }}</h4>
                    <span class="text-sm text-gray-500">{{ log.time }}</span>
                  </div>
                  <p class="text-sm text-gray-400 mb-2">{{ log.description }}</p>
                  <div class="flex items-center space-x-4 text-sm">
                    <span class="flex items-center text-gray-500">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      {{ log.user }}
                    </span>
                    <span class="flex items-center text-gray-500">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                      </svg>
                      {{ log.ip }}
                    </span>
                    <span :class="getStatusClass(log.status)" class="px-2 py-0.5 rounded text-xs font-medium">
                      {{ log.status }}
                    </span>
                  </div>
                </div>

                <!-- Actions -->
                <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg ml-4">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Load More -->
          <div class="px-6 py-4 border-t border-gray-700/50 text-center">
            <button class="px-6 py-2 text-indigo-400 hover:text-indigo-300 font-medium">
              Załaduj więcej
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, h } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const filterType = ref('all')
const filterPeriod = ref('today')
const isRefreshing = ref(false)

const TicketIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'text-blue-400' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' })]) }
const UserIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'text-green-400' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })]) }
const SystemIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'text-purple-400' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }), h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })]) }
const SecurityIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'text-red-400' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' })]) }

const logs = ref([
  { id: 1, type: 'ticket', action: 'Nowe zgłoszenie utworzone', description: 'Zgłoszenie #45 "Problem z logowaniem" zostało utworzone przez klienta.', user: 'Jan Kowalski', ip: '192.168.1.100', time: '2 min temu', status: 'success' },
  { id: 2, type: 'user', action: 'Użytkownik zalogował się', description: 'Pomyślne logowanie do panelu administracyjnego.', user: 'admin@sentri.io', ip: '10.0.0.1', time: '5 min temu', status: 'success' },
  { id: 3, type: 'ticket', action: 'Zgłoszenie zamknięte', description: 'Zgłoszenie #42 zostało rozwiązane i zamknięte.', user: 'Anna Nowak', ip: '192.168.1.105', time: '15 min temu', status: 'success' },
  { id: 4, type: 'security', action: 'Nieudana próba logowania', description: 'Wykryto 3 nieudane próby logowania dla konta customer@example.com.', user: 'customer@example.com', ip: '203.0.113.50', time: '25 min temu', status: 'warning' },
  { id: 5, type: 'system', action: 'Automatyczny backup', description: 'Wykonano automatyczny backup bazy danych.', user: 'System', ip: 'localhost', time: '1 godz temu', status: 'success' },
  { id: 6, type: 'user', action: 'Profil zaktualizowany', description: 'Użytkownik zaktualizował swoje dane profilowe.', user: 'Piotr Wiśniewski', ip: '192.168.1.110', time: '2 godz temu', status: 'success' },
  { id: 7, type: 'security', action: 'Zmiana hasła', description: 'Hasło zostało pomyślnie zmienione.', user: 'Maria Kamińska', ip: '192.168.1.112', time: '3 godz temu', status: 'success' },
  { id: 8, type: 'system', action: 'Błąd połączenia z API', description: 'Nie udało się połączyć z zewnętrznym API (timeout).', user: 'System', ip: 'localhost', time: '4 godz temu', status: 'error' },
  { id: 9, type: 'ticket', action: 'Wiadomość wysłana', description: 'Agent wysłał odpowiedź na zgłoszenie #40.', user: 'Tomasz Zieliński', ip: '192.168.1.101', time: '5 godz temu', status: 'success' },
  { id: 10, type: 'user', action: 'Nowy użytkownik', description: 'Zarejestrowano nowe konto klienta.', user: 'nowy.klient@example.com', ip: '198.51.100.25', time: '6 godz temu', status: 'success' },
])

const todayCount = computed(() => logs.value.length)
const weekCount = computed(() => 89)
const warningCount = computed(() => logs.value.filter(l => l.status === 'warning').length)
const errorCount = computed(() => logs.value.filter(l => l.status === 'error').length)

const filteredLogs = computed(() => {
  if (filterType.value === 'all') return logs.value
  return logs.value.filter(l => l.type === filterType.value)
})

function getTypeClass(type) {
  const classes = {
    ticket: 'bg-blue-500/20',
    user: 'bg-green-500/20',
    system: 'bg-purple-500/20',
    security: 'bg-red-500/20',
  }
  return classes[type] || 'bg-gray-700'
}

function getTypeIcon(type) {
  const icons = { ticket: TicketIcon, user: UserIcon, system: SystemIcon, security: SecurityIcon }
  return icons[type] || SystemIcon
}

function getStatusClass(status) {
  const classes = {
    success: 'bg-green-500/20 text-green-400',
    warning: 'bg-yellow-500/20 text-yellow-400',
    error: 'bg-red-500/20 text-red-400',
  }
  return classes[status] || 'bg-gray-700 text-gray-400'
}

function refreshLogs() {
  isRefreshing.value = true
  setTimeout(() => { isRefreshing.value = false }, 1000)
}
</script>
