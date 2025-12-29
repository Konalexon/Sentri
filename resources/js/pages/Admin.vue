<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="px-6 py-4">
          <h1 class="text-2xl font-bold text-white">Panel administracyjny</h1>
          <p class="text-sm text-gray-400">Zarządzaj użytkownikami i systemem</p>
        </div>
      </header>

      <div class="p-6">
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6 hover:border-gray-600/50 transition-all"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400">{{ stat.label }}</p>
                <p class="text-3xl font-bold mt-1" :class="stat.textColor">{{ stat.value }}</p>
              </div>
              <div
                class="w-12 h-12 rounded-xl flex items-center justify-center"
                :class="stat.bgColor"
              >
                <component :is="stat.icon" class="w-6 h-6" :class="stat.iconColor" />
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ stat.change }}</p>
          </div>
        </div>

        <!-- Users Management -->
        <div class="bg-gray-900 rounded-2xl border border-gray-700/50 overflow-hidden mb-6">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700/50">
            <h2 class="text-lg font-semibold text-white">Zarządzanie użytkownikami</h2>
            <button
              @click="showAddUserModal = true"
              class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all"
            >
              + Dodaj użytkownika
            </button>
          </div>
          
          <table class="w-full">
            <thead class="bg-gray-800/50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Użytkownik</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Rola</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Zgłoszenia</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Ostatnia aktywność</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Akcje</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-800/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center mr-3">
                      <span class="text-white font-medium">{{ user.name.charAt(0) }}</span>
                    </div>
                    <div>
                      <p class="text-white font-medium">{{ user.name }}</p>
                      <p class="text-sm text-gray-500">{{ user.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="getRoleClass(user.role)" class="px-3 py-1 text-xs font-medium rounded-full">
                    {{ getRoleLabel(user.role) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span :class="user.is_active ? 'text-green-400' : 'text-red-400'" class="flex items-center">
                    <span class="w-2 h-2 rounded-full mr-2" :class="user.is_active ? 'bg-green-400' : 'bg-red-400'"></span>
                    {{ user.is_active ? 'Aktywny' : 'Nieaktywny' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-400">{{ user.tickets_count || 0 }}</td>
                <td class="px-6 py-4 text-gray-500 text-sm">{{ user.last_seen || 'Brak danych' }}</td>
                <td class="px-6 py-4 text-right">
                  <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- System Settings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Activity Log -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Ostatnia aktywność</h3>
            <div class="space-y-4">
              <div v-for="activity in activities" :key="activity.id" class="flex items-start">
                <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center mr-3 flex-shrink-0">
                  <component :is="activity.icon" class="w-4 h-4 text-gray-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-white">{{ activity.description }}</p>
                  <p class="text-xs text-gray-500">{{ activity.time }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Szybkie akcje</h3>
            <div class="grid grid-cols-2 gap-4">
              <button class="p-4 bg-gray-800/50 rounded-xl hover:bg-gray-800 transition-colors text-left group">
                <svg class="w-8 h-8 text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-white font-medium">Eksport danych</p>
                <p class="text-xs text-gray-500">CSV, Excel, PDF</p>
              </button>
              
              <button class="p-4 bg-gray-800/50 rounded-xl hover:bg-gray-800 transition-colors text-left group">
                <svg class="w-8 h-8 text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <p class="text-white font-medium">Synchronizuj</p>
                <p class="text-xs text-gray-500">Aktualizuj dane</p>
              </button>
              
              <button class="p-4 bg-gray-800/50 rounded-xl hover:bg-gray-800 transition-colors text-left group">
                <svg class="w-8 h-8 text-yellow-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-white font-medium">Raporty</p>
                <p class="text-xs text-gray-500">Generuj raporty</p>
              </button>
              
              <button class="p-4 bg-gray-800/50 rounded-xl hover:bg-gray-800 transition-colors text-left group">
                <svg class="w-8 h-8 text-red-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <p class="text-white font-medium">Czyszczenie</p>
                <p class="text-xs text-gray-500">Usuń stare dane</p>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Add User Modal -->
    <Teleport to="body">
      <div v-if="showAddUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showAddUserModal = false"></div>
        <div class="relative bg-gray-900 rounded-2xl border border-gray-700/50 w-full max-w-md p-6 shadow-2xl">
          <h2 class="text-xl font-bold text-white mb-6">Dodaj użytkownika</h2>
          
          <form @submit.prevent="addUser" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Imię i nazwisko</label>
              <input v-model="newUser.name" type="text" required class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
              <input v-model="newUser.email" type="email" required class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Rola</label>
              <select v-model="newUser.role" class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                <option value="customer">Klient</option>
                <option value="agent">Agent</option>
                <option value="admin">Administrator</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Hasło</label>
              <input v-model="newUser.password" type="password" required class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50" />
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button type="button" @click="showAddUserModal = false" class="px-4 py-2 text-gray-400 hover:text-white transition">Anuluj</button>
              <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl">Dodaj</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, h } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const showAddUserModal = ref(false)
const newUser = ref({ name: '', email: '', role: 'customer', password: '' })

// Mock data
const users = ref([
  { id: 1, name: 'Administrator', email: 'admin@sentri.io', role: 'admin', is_active: true, tickets_count: 15, last_seen: '5 min temu' },
  { id: 2, name: 'Agent Support', email: 'agent@sentri.io', role: 'agent', is_active: true, tickets_count: 42, last_seen: '2 godz temu' },
  { id: 3, name: 'Jan Kowalski', email: 'customer@sentri.io', role: 'customer', is_active: true, tickets_count: 3, last_seen: '1 dzień temu' },
])

const UserIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })]) }
const TicketIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' })]) }
const CheckIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 13l4 4L19 7' })]) }
const ClockIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })]) }

const stats = [
  { label: 'Użytkownicy', value: 156, textColor: 'text-white', bgColor: 'bg-blue-500/20', iconColor: 'text-blue-400', icon: UserIcon, change: '+12 w tym miesiącu' },
  { label: 'Łącznie zgłoszeń', value: 1284, textColor: 'text-white', bgColor: 'bg-indigo-500/20', iconColor: 'text-indigo-400', icon: TicketIcon, change: '+89 w tym tygodniu' },
  { label: 'Rozwiązane', value: 1156, textColor: 'text-green-400', bgColor: 'bg-green-500/20', iconColor: 'text-green-400', icon: CheckIcon, change: '90% wskaźnik' },
  { label: 'Śr. czas odpowiedzi', value: '2.4h', textColor: 'text-yellow-400', bgColor: 'bg-yellow-500/20', iconColor: 'text-yellow-400', icon: ClockIcon, change: '-15% vs poprzedni miesiąc' },
]

const activities = [
  { id: 1, description: 'Admin dodał nowego agenta', time: '5 min temu', icon: UserIcon },
  { id: 2, description: 'Zgłoszenie #45 zostało zamknięte', time: '15 min temu', icon: CheckIcon },
  { id: 3, description: 'Nowy użytkownik się zarejestrował', time: '1 godz temu', icon: UserIcon },
  { id: 4, description: 'System wykonał automatyczny backup', time: '2 godz temu', icon: ClockIcon },
]

function getRoleClass(role) {
  const classes = {
    admin: 'bg-red-500/20 text-red-400',
    agent: 'bg-blue-500/20 text-blue-400',
    customer: 'bg-green-500/20 text-green-400',
  }
  return classes[role] || classes.customer
}

function getRoleLabel(role) {
  const labels = { admin: 'Administrator', agent: 'Agent', customer: 'Klient' }
  return labels[role] || role
}

function addUser() {
  users.value.push({
    id: Date.now(),
    ...newUser.value,
    is_active: true,
    tickets_count: 0,
    last_seen: 'Nigdy',
  })
  showAddUserModal.value = false
  newUser.value = { name: '', email: '', role: 'customer', password: '' }
}
</script>
