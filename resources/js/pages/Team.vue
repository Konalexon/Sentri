<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h1 class="text-2xl font-bold text-white">Zespół</h1>
            <p class="text-sm text-gray-400">Zarządzaj członkami zespołu</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Szukaj..."
                class="w-64 bg-gray-800 text-white placeholder-gray-500 rounded-xl px-4 py-2 pl-10 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>

            <!-- View Toggle -->
            <div class="flex bg-gray-800 rounded-lg p-1">
              <button
                @click="viewMode = 'grid'"
                class="p-2 rounded-lg transition-colors"
                :class="viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white'"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
              </button>
              <button
                @click="viewMode = 'list'"
                class="p-2 rounded-lg transition-colors"
                :class="viewMode === 'list' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white'"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
              </button>
            </div>

            <button
              @click="showInviteModal = true"
              class="flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all shadow-lg shadow-indigo-500/25"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              Zaproś
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
                <p class="text-sm text-gray-400">Łącznie</p>
                <p class="text-2xl font-bold text-white">{{ team.length }}</p>
              </div>
              <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400">Online</p>
                <p class="text-2xl font-bold text-green-400">{{ onlineCount }}</p>
              </div>
              <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                <span class="w-3 h-3 rounded-full bg-green-400 animate-pulse"></span>
              </div>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400">Agenci</p>
                <p class="text-2xl font-bold text-indigo-400">{{ agentCount }}</p>
              </div>
              <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-400">Oczekujące zaproszenia</p>
                <p class="text-2xl font-bold text-yellow-400">2</p>
              </div>
              <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Grid View -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="member in filteredTeam"
            :key="member.id"
            class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6 hover:border-indigo-500/50 transition-all group"
          >
            <div class="flex flex-col items-center text-center">
              <!-- Avatar -->
              <div class="relative mb-4">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                  <span class="text-2xl font-bold text-white">{{ member.name.split(' ').map(n => n[0]).join('') }}</span>
                </div>
                <span
                  class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-gray-900"
                  :class="member.status === 'online' ? 'bg-green-400' : member.status === 'away' ? 'bg-yellow-400' : 'bg-gray-500'"
                ></span>
              </div>

              <h3 class="text-lg font-semibold text-white mb-1">{{ member.name }}</h3>
              <p class="text-sm text-gray-400 mb-2">{{ member.email }}</p>
              
              <span
                class="px-3 py-1 text-xs font-medium rounded-full mb-4"
                :class="getRoleClass(member.role)"
              >
                {{ getRoleLabel(member.role) }}
              </span>

              <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                <span>{{ member.ticketsHandled }} zgłoszeń</span>
              </div>

              <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                </button>
                <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </button>
                <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- List View -->
        <div v-else class="bg-gray-900 rounded-2xl border border-gray-700/50 overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-800/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Pracownik</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Rola</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Zgłoszeń</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Ocena</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Dołączył</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-gray-400 uppercase">Akcje</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
              <tr v-for="member in filteredTeam" :key="member.id" class="hover:bg-gray-800/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="relative mr-3">
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                        <span class="text-white font-medium">{{ member.name.charAt(0) }}</span>
                      </div>
                      <span
                        class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-gray-900"
                        :class="member.status === 'online' ? 'bg-green-400' : member.status === 'away' ? 'bg-yellow-400' : 'bg-gray-500'"
                      ></span>
                    </div>
                    <div>
                      <p class="text-white font-medium">{{ member.name }}</p>
                      <p class="text-sm text-gray-500">{{ member.email }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span :class="getRoleClass(member.role)" class="px-3 py-1 text-xs font-medium rounded-full">
                    {{ getRoleLabel(member.role) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="flex items-center text-sm" :class="member.status === 'online' ? 'text-green-400' : member.status === 'away' ? 'text-yellow-400' : 'text-gray-500'">
                    <span class="w-2 h-2 rounded-full mr-2" :class="member.status === 'online' ? 'bg-green-400' : member.status === 'away' ? 'bg-yellow-400' : 'bg-gray-500'"></span>
                    {{ member.status === 'online' ? 'Online' : member.status === 'away' ? 'Zaraz wracam' : 'Offline' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-gray-300">{{ member.ticketsHandled }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <svg v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= member.rating ? 'text-yellow-400' : 'text-gray-700'" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="ml-2 text-sm text-gray-400">{{ member.rating }}.0</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-gray-500 text-sm">{{ member.joinedAt }}</td>
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
      </div>
    </main>

    <!-- Invite Modal -->
    <Teleport to="body">
      <div v-if="showInviteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showInviteModal = false"></div>
        <div class="relative bg-gray-900 rounded-2xl border border-gray-700/50 w-full max-w-md p-6 shadow-2xl">
          <h2 class="text-xl font-bold text-white mb-6">Zaproś do zespołu</h2>
          
          <form @submit.prevent="inviteMember" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
              <input v-model="invite.email" type="email" required placeholder="email@example.com" class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Rola</label>
              <select v-model="invite.role" class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                <option value="agent">Agent</option>
                <option value="admin">Administrator</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Wiadomość (opcjonalna)</label>
              <textarea v-model="invite.message" rows="3" placeholder="Cześć, zapraszam Cię do naszego zespołu..." class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 resize-none"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button type="button" @click="showInviteModal = false" class="px-4 py-2 text-gray-400 hover:text-white transition">Anuluj</button>
              <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl">Wyślij zaproszenie</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const searchQuery = ref('')
const viewMode = ref('grid')
const showInviteModal = ref(false)
const invite = ref({ email: '', role: 'agent', message: '' })

const team = ref([
  { id: 1, name: 'Jan Kowalski', email: 'jan.kowalski@sentri.io', role: 'admin', status: 'online', ticketsHandled: 156, rating: 5, joinedAt: '15 mar 2024' },
  { id: 2, name: 'Anna Nowak', email: 'anna.nowak@sentri.io', role: 'agent', status: 'online', ticketsHandled: 89, rating: 4, joinedAt: '22 kwi 2024' },
  { id: 3, name: 'Piotr Wiśniewski', email: 'piotr.wisniewski@sentri.io', role: 'agent', status: 'away', ticketsHandled: 124, rating: 5, joinedAt: '10 lut 2024' },
  { id: 4, name: 'Maria Kamińska', email: 'maria.kaminska@sentri.io', role: 'agent', status: 'offline', ticketsHandled: 67, rating: 4, joinedAt: '5 maj 2024' },
  { id: 5, name: 'Tomasz Zieliński', email: 'tomasz.zielinski@sentri.io', role: 'admin', status: 'online', ticketsHandled: 203, rating: 5, joinedAt: '1 sty 2024' },
  { id: 6, name: 'Katarzyna Lewandowska', email: 'kasia.lewandowska@sentri.io', role: 'agent', status: 'offline', ticketsHandled: 45, rating: 4, joinedAt: '18 cze 2024' },
])

const filteredTeam = computed(() => {
  if (!searchQuery.value) return team.value
  const query = searchQuery.value.toLowerCase()
  return team.value.filter(m => 
    m.name.toLowerCase().includes(query) || 
    m.email.toLowerCase().includes(query)
  )
})

const onlineCount = computed(() => team.value.filter(m => m.status === 'online').length)
const agentCount = computed(() => team.value.filter(m => m.role === 'agent').length)

function getRoleClass(role) {
  return role === 'admin' ? 'bg-red-500/20 text-red-400' : 'bg-blue-500/20 text-blue-400'
}

function getRoleLabel(role) {
  return role === 'admin' ? 'Administrator' : 'Agent'
}

function inviteMember() {
  alert(`Zaproszenie wysłane do: ${invite.value.email}`)
  showInviteModal.value = false
  invite.value = { email: '', role: 'agent', message: '' }
}
</script>
