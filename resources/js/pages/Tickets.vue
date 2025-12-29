<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <!-- Header -->
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h1 class="text-2xl font-bold text-white">Zgłoszenia</h1>
            <p class="text-sm text-gray-400">Zarządzaj wszystkimi zgłoszeniami</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Szukaj zgłoszeń..."
                class="w-80 bg-gray-800 text-white placeholder-gray-500 rounded-xl px-4 py-2 pl-10 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                @keyup.enter="handleSearch"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>

            <!-- Filter -->
            <select
              v-model="statusFilter"
              @change="applyFilters"
              class="bg-gray-800 text-white rounded-xl px-4 py-2 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
            >
              <option value="">Wszystkie statusy</option>
              <option value="open">Otwarte</option>
              <option value="pending">Oczekujące</option>
              <option value="closed">Zamknięte</option>
            </select>

            <!-- New Ticket -->
            <button
              @click="showNewTicketModal = true"
              class="flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all shadow-lg shadow-indigo-500/25"
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
        <!-- Tickets Table -->
        <div class="bg-gray-900 rounded-2xl border border-gray-700/50 overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-800/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Temat</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Priorytet</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Klient</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Agent</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Data</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Akcje</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
              <tr v-if="loading" v-for="i in 5" :key="i" class="animate-pulse">
                <td class="px-6 py-4"><div class="h-4 w-10 bg-gray-800 rounded"></div></td>
                <td class="px-6 py-4"><div class="h-4 w-48 bg-gray-800 rounded"></div></td>
                <td class="px-6 py-4"><div class="h-6 w-20 bg-gray-800 rounded-full"></div></td>
                <td class="px-6 py-4"><div class="h-6 w-16 bg-gray-800 rounded-full"></div></td>
                <td class="px-6 py-4"><div class="h-4 w-24 bg-gray-800 rounded"></div></td>
                <td class="px-6 py-4"><div class="h-4 w-24 bg-gray-800 rounded"></div></td>
                <td class="px-6 py-4"><div class="h-4 w-20 bg-gray-800 rounded"></div></td>
                <td class="px-6 py-4"><div class="h-8 w-8 bg-gray-800 rounded"></div></td>
              </tr>

              <tr
                v-else
                v-for="ticket in tickets"
                :key="ticket.id"
                class="hover:bg-gray-800/50 transition-colors cursor-pointer"
                @click="openTicket(ticket)"
              >
                <td class="px-6 py-4 text-sm text-gray-500 font-mono">#{{ ticket.id }}</td>
                <td class="px-6 py-4">
                  <div class="text-white font-medium">{{ ticket.subject }}</div>
                  <div class="text-sm text-gray-500 truncate max-w-xs">{{ ticket.description }}</div>
                </td>
                <td class="px-6 py-4">
                  <span
                    class="px-3 py-1 text-xs font-medium rounded-full"
                    :class="getStatusClass(ticket.status)"
                  >
                    {{ ticket.status?.label }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span
                    class="px-3 py-1 text-xs font-medium rounded-full"
                    :class="getPriorityClass(ticket.priority)"
                  >
                    {{ ticket.priority?.label }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-300">{{ ticket.customer?.name }}</td>
                <td class="px-6 py-4 text-sm text-gray-300">{{ ticket.agent?.name || '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(ticket.created_at) }}</td>
                <td class="px-6 py-4 text-right">
                  <button class="p-2 hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </td>
              </tr>

              <tr v-if="!loading && tickets.length === 0">
                <td colspan="8" class="px-6 py-12 text-center">
                  <svg class="w-12 h-12 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="text-gray-400">Brak zgłoszeń</p>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="pagination.lastPage > 1" class="flex items-center justify-between px-6 py-4 border-t border-gray-700/50">
            <p class="text-sm text-gray-400">
              Pokazuje {{ (pagination.currentPage - 1) * pagination.perPage + 1 }} - {{ Math.min(pagination.currentPage * pagination.perPage, pagination.total) }} z {{ pagination.total }}
            </p>
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
      </div>
    </main>

    <!-- New Ticket Modal -->
    <Teleport to="body">
      <div v-if="showNewTicketModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showNewTicketModal = false"></div>
        <div class="relative bg-gray-900 rounded-2xl border border-gray-700/50 w-full max-w-lg p-6 shadow-2xl">
          <h2 class="text-xl font-bold text-white mb-6">Nowe zgłoszenie</h2>
          
          <!-- Error Alert -->
          <div v-if="createError" class="mb-4 p-4 bg-red-500/20 border border-red-500/50 rounded-xl text-red-400 text-sm">
            {{ createError }}
          </div>
          
          <form @submit.prevent="createTicket" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Temat</label>
              <input
                v-model="newTicket.subject"
                type="text"
                required
                minlength="3"
                placeholder="Krótki opis problemu"
                class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Opis</label>
              <textarea
                v-model="newTicket.description"
                rows="4"
                required
                minlength="5"
                placeholder="Opisz szczegółowo swój problem..."
                class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 resize-none"
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
              <button type="button" @click="showNewTicketModal = false" class="px-4 py-2 text-gray-400 hover:text-white transition">Anuluj</button>
              <button 
                type="submit" 
                :disabled="isCreating"
                class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
              >
                <svg v-if="isCreating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isCreating ? 'Tworzenie...' : 'Utwórz' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useTicketStore } from '@/stores/useTicketStore'
import { storeToRefs } from 'pinia'
import Sidebar from '@/components/Sidebar.vue'

const router = useRouter()
const ticketStore = useTicketStore()
const { tickets, loading, pagination } = storeToRefs(ticketStore)

const searchQuery = ref('')
const statusFilter = ref('')
const showNewTicketModal = ref(false)
const isCreating = ref(false)
const createError = ref('')
const newTicket = ref({ subject: '', description: '', priority: 'medium' })

function getStatusClass(status) {
  const classes = {
    green: 'bg-green-500/20 text-green-400',
    yellow: 'bg-yellow-500/20 text-yellow-400',
    gray: 'bg-gray-500/20 text-gray-400',
  }
  return classes[status?.color] || classes.gray
}

function getPriorityClass(priority) {
  const classes = {
    slate: 'bg-slate-500/20 text-slate-400',
    blue: 'bg-blue-500/20 text-blue-400',
    orange: 'bg-orange-500/20 text-orange-400',
    red: 'bg-red-500/20 text-red-400',
  }
  return classes[priority?.color] || classes.blue
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('pl-PL', { 
    day: 'numeric', 
    month: 'short',
    year: 'numeric'
  })
}

function handleSearch() {
  ticketStore.setFilters({ search: searchQuery.value })
  ticketStore.fetchTickets()
}

function applyFilters() {
  ticketStore.setFilters({ status: statusFilter.value })
  ticketStore.fetchTickets()
}

function goToPage(page) {
  ticketStore.fetchTickets(page)
}

function openTicket(ticket) {
  router.push(`/tickets/${ticket.id}`)
}

async function createTicket() {
  isCreating.value = true
  createError.value = ''
  
  try {
    await ticketStore.createTicket(newTicket.value)
    showNewTicketModal.value = false
    newTicket.value = { subject: '', description: '', priority: 'medium' }
    // Refresh list
    await ticketStore.fetchTickets()
  } catch (err) {
    console.error('Create ticket error:', err)
    const errors = err.response?.data?.errors
    if (errors) {
      createError.value = Object.values(errors).flat().join(', ')
    } else {
      createError.value = err.response?.data?.message || 'Wystąpił błąd podczas tworzenia zgłoszenia'
    }
  } finally {
    isCreating.value = false
  }
}

onMounted(() => {
  ticketStore.fetchTickets()
})
</script>
