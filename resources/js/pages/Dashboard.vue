<template>
  <div class="min-h-screen bg-gray-950">
    <!-- Sidebar -->
    <Sidebar @toggle="handleSidebarToggle" />

    <!-- Main Content -->
    <main
      class="transition-all duration-300"
      :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'"
    >
      <!-- Top Bar -->
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h1 class="text-2xl font-bold text-white">Dashboard</h1>
            <p class="text-sm text-gray-400">Witaj, {{ user?.name }}! 👋</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Szukaj zgłoszeń..."
                class="w-64 bg-gray-800 text-white placeholder-gray-500 rounded-xl px-4 py-2 pl-10 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition"
                @keyup.enter="handleSearch"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>

            <!-- New Ticket Button -->
            <button
              @click="showNewTicketModal = true"
              class="flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all duration-200 shadow-lg shadow-indigo-500/25 transform hover:scale-105"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nowe zgłoszenie
            </button>
          </div>
        </div>
      </header>

      <div class="p-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div
            v-for="stat in statsCards"
            :key="stat.label"
            class="relative overflow-hidden bg-gray-900 rounded-2xl p-6 border border-gray-700/50 group hover:border-gray-600/50 transition-all duration-300"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400 mb-1">{{ stat.label }}</p>
                <p class="text-3xl font-bold" :class="stat.textColor">{{ stat.value }}</p>
              </div>
              <div
                class="w-12 h-12 rounded-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"
                :class="stat.bgColor"
              >
                <component :is="stat.icon" class="w-6 h-6" :class="stat.iconColor" />
              </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r" :class="stat.gradient"></div>
          </div>
        </div>

        <!-- Main Grid: Tickets List + Chat -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Tickets List -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700/50">
              <h2 class="text-lg font-semibold text-white">Ostatnie zgłoszenia</h2>
              <div class="flex space-x-2">
                <button
                  v-for="status in statusFilters"
                  :key="status.value"
                  @click="setFilter(status.value)"
                  class="px-3 py-1 text-xs font-medium rounded-lg transition-all"
                  :class="currentFilter === status.value ? status.activeClass : 'text-gray-400 hover:text-white hover:bg-gray-800'"
                >
                  {{ status.label }}
                </button>
              </div>
            </div>

            <div class="divide-y divide-gray-800">
              <div v-if="loading" class="p-8 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500 mx-auto"></div>
                <p class="mt-4 text-gray-400">Ładowanie zgłoszeń...</p>
              </div>

              <div v-else-if="tickets.length === 0" class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-gray-400">Brak zgłoszeń</p>
              </div>

              <div
                v-else
                v-for="ticket in tickets"
                :key="ticket.id"
                @click="selectTicket(ticket)"
                class="p-4 hover:bg-gray-800/50 cursor-pointer transition-colors"
                :class="{ 'bg-indigo-500/10 border-l-2 border-indigo-500': selectedTicket?.id === ticket.id }"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                      <span class="text-xs text-gray-500">#{{ ticket.id }}</span>
                      <span
                        class="px-2 py-0.5 text-xs font-medium rounded-full"
                        :class="getStatusClass(ticket.status)"
                      >
                        {{ ticket.status.label }}
                      </span>
                      <span
                        class="px-2 py-0.5 text-xs font-medium rounded-full"
                        :class="getPriorityClass(ticket.priority)"
                      >
                        {{ ticket.priority.label }}
                      </span>
                    </div>
                    <h3 class="mt-1 text-white font-medium truncate">{{ ticket.subject }}</h3>
                    <p class="mt-1 text-sm text-gray-400 line-clamp-2">{{ ticket.description }}</p>
                  </div>
                  <div class="ml-4 flex-shrink-0 text-right">
                    <img
                      v-if="ticket.customer?.avatar_url"
                      :src="ticket.customer.avatar_url"
                      :alt="ticket.customer.name"
                      class="w-8 h-8 rounded-full"
                    />
                    <p class="mt-1 text-xs text-gray-500">{{ formatDate(ticket.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.lastPage > 1" class="flex items-center justify-center px-6 py-4 border-t border-gray-700/50">
              <div class="flex space-x-2">
                <button
                  v-for="page in pagination.lastPage"
                  :key="page"
                  @click="goToPage(page)"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-all"
                  :class="pagination.currentPage === page ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800'"
                >
                  {{ page }}
                </button>
              </div>
            </div>
          </div>

          <!-- Chat Window -->
          <div v-if="selectedTicket" class="h-[600px]">
            <ChatWindow :ticket-id="selectedTicket.id" @close="selectedTicket = null" />
          </div>
          <div v-else class="flex items-center justify-center h-[600px] bg-gray-900 rounded-2xl border border-gray-700/50">
            <div class="text-center">
              <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <p class="text-gray-400">Wybierz zgłoszenie, aby rozpocząć czat</p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- New Ticket Modal -->
    <Teleport to="body">
      <div v-if="showNewTicketModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showNewTicketModal = false"></div>
        <div class="relative bg-gray-900 rounded-2xl border border-gray-700/50 w-full max-w-lg p-6 shadow-2xl">
          <h2 class="text-xl font-bold text-white mb-6">Nowe zgłoszenie</h2>
          
          <form @submit.prevent="createTicket" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Temat</label>
              <input
                v-model="newTicket.subject"
                type="text"
                required
                class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                placeholder="Krótki opis problemu..."
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Opis</label>
              <textarea
                v-model="newTicket.description"
                rows="4"
                required
                class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 resize-none"
                placeholder="Szczegółowy opis problemu..."
              ></textarea>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Priorytet</label>
              <select
                v-model="newTicket.priority"
                class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
              >
                <option value="low">Niski</option>
                <option value="medium">Średni</option>
                <option value="high">Wysoki</option>
                <option value="urgent">Pilny</option>
              </select>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="showNewTicketModal = false"
                class="px-4 py-2 text-gray-400 hover:text-white transition"
              >
                Anuluj
              </button>
              <button
                type="submit"
                :disabled="creatingTicket"
                class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all disabled:opacity-50"
              >
                {{ creatingTicket ? 'Tworzenie...' : 'Utwórz zgłoszenie' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, h } from 'vue'
import { useTicketStore } from '@/stores/useTicketStore'
import { useAuthStore } from '@/stores/useAuthStore'
import { storeToRefs } from 'pinia'
import Sidebar from '@/components/Sidebar.vue'
import ChatWindow from '@/components/ChatWindow.vue'

const ticketStore = useTicketStore()
const authStore = useAuthStore()

const { tickets, loading, pagination, statistics } = storeToRefs(ticketStore)
const { user } = storeToRefs(authStore)

const sidebarExpanded = ref(true)
const selectedTicket = ref(null)
const searchQuery = ref('')
const currentFilter = ref(null)
const showNewTicketModal = ref(false)
const creatingTicket = ref(false)
const newTicket = ref({
  subject: '',
  description: '',
  priority: 'medium',
})

const statusFilters = [
  { value: null, label: 'Wszystkie', activeClass: 'bg-gray-700 text-white' },
  { value: 'open', label: 'Otwarte', activeClass: 'bg-green-500/20 text-green-400' },
  { value: 'pending', label: 'Oczekujące', activeClass: 'bg-yellow-500/20 text-yellow-400' },
  { value: 'closed', label: 'Zamknięte', activeClass: 'bg-gray-500/20 text-gray-400' },
]

// Icon components
const TicketIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' })
  ])
}

const OpenIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
  ])
}

const PendingIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
  ])
}

const ClosedIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 13l4 4L19 7' })
  ])
}

const statsCards = computed(() => [
  {
    label: 'Wszystkie zgłoszenia',
    value: statistics.value.total,
    textColor: 'text-white',
    bgColor: 'bg-indigo-500/20',
    iconColor: 'text-indigo-400',
    icon: TicketIcon,
    gradient: 'from-indigo-500 to-purple-500',
  },
  {
    label: 'Otwarte',
    value: statistics.value.open,
    textColor: 'text-green-400',
    bgColor: 'bg-green-500/20',
    iconColor: 'text-green-400',
    icon: OpenIcon,
    gradient: 'from-green-500 to-emerald-500',
  },
  {
    label: 'Oczekujące',
    value: statistics.value.pending,
    textColor: 'text-yellow-400',
    bgColor: 'bg-yellow-500/20',
    iconColor: 'text-yellow-400',
    icon: PendingIcon,
    gradient: 'from-yellow-500 to-orange-500',
  },
  {
    label: 'Zamknięte',
    value: statistics.value.closed,
    textColor: 'text-gray-400',
    bgColor: 'bg-gray-500/20',
    iconColor: 'text-gray-400',
    icon: ClosedIcon,
    gradient: 'from-gray-500 to-gray-600',
  },
])

function handleSidebarToggle(expanded) {
  sidebarExpanded.value = expanded
}

function selectTicket(ticket) {
  selectedTicket.value = ticket
}

function setFilter(status) {
  currentFilter.value = status
  ticketStore.setFilters({ status })
  ticketStore.fetchTickets()
}

function handleSearch() {
  ticketStore.setFilters({ search: searchQuery.value })
  ticketStore.fetchTickets()
}

function goToPage(page) {
  ticketStore.fetchTickets(page)
}

function getStatusClass(status) {
  const classes = {
    green: 'bg-green-500/20 text-green-400',
    yellow: 'bg-yellow-500/20 text-yellow-400',
    gray: 'bg-gray-500/20 text-gray-400',
  }
  return classes[status.color] || classes.gray
}

function getPriorityClass(priority) {
  const classes = {
    slate: 'bg-slate-500/20 text-slate-400',
    blue: 'bg-blue-500/20 text-blue-400',
    orange: 'bg-orange-500/20 text-orange-400',
    red: 'bg-red-500/20 text-red-400',
  }
  return classes[priority.color] || classes.blue
}

function formatDate(dateString) {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  
  if (diff < 60000) return 'Teraz'
  if (diff < 3600000) return `${Math.floor(diff / 60000)} min`
  if (diff < 86400000) return `${Math.floor(diff / 3600000)} godz`
  
  return date.toLocaleDateString('pl-PL', { day: 'numeric', month: 'short' })
}

async function createTicket() {
  creatingTicket.value = true
  
  try {
    const ticket = await ticketStore.createTicket(newTicket.value)
    showNewTicketModal.value = false
    selectedTicket.value = ticket
    newTicket.value = { subject: '', description: '', priority: 'medium' }
  } catch (err) {
    console.error('Failed to create ticket:', err)
  } finally {
    creatingTicket.value = false
  }
}

onMounted(async () => {
  await Promise.all([
    ticketStore.fetchTickets(),
    ticketStore.fetchStatistics(),
  ])
  ticketStore.subscribeToAllTickets()
})

onUnmounted(() => {
  ticketStore.unsubscribeAll()
})
</script>
