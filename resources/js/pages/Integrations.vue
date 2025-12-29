<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h1 class="text-2xl font-bold text-white">Integracje</h1>
            <p class="text-sm text-gray-400">Połącz Sentri z ulubionymi narzędziami</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Szukaj integracji..."
                class="w-64 bg-gray-800 text-white placeholder-gray-500 rounded-xl px-4 py-2 pl-10 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="px-6 pb-0">
          <div class="flex space-x-1">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              class="px-4 py-3 text-sm font-medium border-b-2 transition-all"
              :class="activeTab === tab.id ? 'text-indigo-400 border-indigo-400' : 'text-gray-400 border-transparent hover:text-white'"
            >
              {{ tab.label }}
              <span v-if="tab.count" class="ml-2 px-2 py-0.5 text-xs rounded-full" :class="activeTab === tab.id ? 'bg-indigo-500/20' : 'bg-gray-700'">{{ tab.count }}</span>
            </button>
          </div>
        </div>
      </header>

      <div class="p-6">
        <!-- Connected Integrations -->
        <div v-if="activeTab === 'connected'" class="space-y-6">
          <div v-if="connectedIntegrations.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <p class="text-gray-400 mb-4">Nie masz jeszcze żadnych połączonych integracji</p>
            <button @click="activeTab = 'available'" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-xl">Przeglądaj integracje</button>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="integration in connectedIntegrations"
              :key="integration.id"
              class="bg-gray-900 rounded-2xl border border-green-500/30 p-6"
            >
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4" :style="{ backgroundColor: integration.bgColor }">
                    <img :src="integration.logo" :alt="integration.name" class="w-7 h-7" />
                  </div>
                  <div>
                    <h3 class="font-semibold text-white">{{ integration.name }}</h3>
                    <span class="text-xs text-green-400 flex items-center">
                      <span class="w-2 h-2 rounded-full bg-green-400 mr-1.5"></span>
                      Połączono
                    </span>
                  </div>
                </div>
                <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </button>
              </div>
              <p class="text-sm text-gray-400 mb-4">{{ integration.description }}</p>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Połączono: {{ integration.connectedAt }}</span>
                <button @click="disconnectIntegration(integration)" class="text-red-400 hover:text-red-300">Rozłącz</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Available Integrations -->
        <div v-if="activeTab === 'available'">
          <!-- Categories -->
          <div class="flex flex-wrap gap-2 mb-6">
            <button
              v-for="cat in categories"
              :key="cat.id"
              @click="selectedCategory = selectedCategory === cat.id ? null : cat.id"
              class="px-4 py-2 text-sm font-medium rounded-xl transition-all"
              :class="selectedCategory === cat.id ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white'"
            >
              {{ cat.label }}
            </button>
          </div>

          <!-- Integration Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div
              v-for="integration in filteredIntegrations"
              :key="integration.id"
              class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6 hover:border-indigo-500/50 transition-all group"
            >
              <div class="flex items-center mb-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mr-4" :style="{ backgroundColor: integration.bgColor }">
                  <img :src="integration.logo" :alt="integration.name" class="w-8 h-8" />
                </div>
                <div>
                  <h3 class="font-semibold text-white group-hover:text-indigo-400 transition-colors">{{ integration.name }}</h3>
                  <span class="text-xs text-gray-500">{{ integration.category }}</span>
                </div>
              </div>
              <p class="text-sm text-gray-400 mb-4 line-clamp-2">{{ integration.description }}</p>
              <div class="flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-500">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                  {{ integration.users }}+ użytkowników
                </div>
                <button
                  @click="connectIntegration(integration)"
                  class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-500 transition-colors opacity-0 group-hover:opacity-100"
                >
                  Połącz
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- API & Webhooks -->
        <div v-if="activeTab === 'api'" class="space-y-6">
          <!-- API Key Section -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Klucze API</h3>
            <p class="text-sm text-gray-400 mb-6">Używaj kluczy API do integracji z zewnętrznymi systemami.</p>
            
            <div class="space-y-4">
              <div v-for="key in apiKeys" :key="key.id" class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-white">{{ key.name }}</p>
                    <p class="text-sm text-gray-500">Utworzono: {{ key.created }} • Ostatnie użycie: {{ key.lastUsed }}</p>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <code class="px-3 py-1 bg-gray-900 rounded text-sm text-gray-400 font-mono">{{ key.preview }}</code>
                  <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                  </button>
                  <button class="p-2 text-gray-400 hover:text-red-400 hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <button class="mt-4 flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-500">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nowy klucz API
            </button>
          </div>

          <!-- Webhooks Section -->
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Webhooki</h3>
            <p class="text-sm text-gray-400 mb-6">Otrzymuj powiadomienia w czasie rzeczywistym o zdarzeniach w Sentri.</p>
            
            <div class="space-y-4">
              <div v-for="webhook in webhooks" :key="webhook.id" class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-white">{{ webhook.url }}</p>
                    <p class="text-sm text-gray-500">{{ webhook.events.join(', ') }}</p>
                  </div>
                </div>
                <div class="flex items-center space-x-4">
                  <span :class="webhook.active ? 'text-green-400' : 'text-gray-500'" class="text-sm">
                    {{ webhook.active ? 'Aktywny' : 'Nieaktywny' }}
                  </span>
                  <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <button class="mt-4 flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-500">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Dodaj webhook
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const searchQuery = ref('')
const activeTab = ref('available')
const selectedCategory = ref(null)

const tabs = [
  { id: 'connected', label: 'Połączone', count: 3 },
  { id: 'available', label: 'Dostępne', count: 20 },
  { id: 'api', label: 'API & Webhooks', count: null },
]

const categories = [
  { id: 'communication', label: 'Komunikacja' },
  { id: 'crm', label: 'CRM' },
  { id: 'automation', label: 'Automatyzacja' },
  { id: 'analytics', label: 'Analityka' },
  { id: 'ecommerce', label: 'E-commerce' },
  { id: 'productivity', label: 'Produktywność' },
]

const integrations = ref([
  { id: 1, name: 'Slack', category: 'Komunikacja', categoryId: 'communication', description: 'Otrzymuj powiadomienia o zgłoszeniach bezpośrednio w Slack.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/slack.svg', bgColor: '#4A154B', users: 15000, connected: true, connectedAt: '15 gru 2024' },
  { id: 2, name: 'Discord', category: 'Komunikacja', categoryId: 'communication', description: 'Integracja z serwerem Discord dla powiadomień zespołowych.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/discord.svg', bgColor: '#5865F2', users: 8500, connected: false },
  { id: 3, name: 'Microsoft Teams', category: 'Komunikacja', categoryId: 'communication', description: 'Synchronizuj zgłoszenia z Microsoft Teams.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/microsoftteams.svg', bgColor: '#6264A7', users: 12000, connected: true, connectedAt: '10 gru 2024' },
  { id: 4, name: 'Salesforce', category: 'CRM', categoryId: 'crm', description: 'Dwukierunkowa synchronizacja z Salesforce CRM.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/salesforce.svg', bgColor: '#00A1E0', users: 5600, connected: false },
  { id: 5, name: 'HubSpot', category: 'CRM', categoryId: 'crm', description: 'Połącz zgłoszenia z kontaktami HubSpot.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/hubspot.svg', bgColor: '#FF7A59', users: 7800, connected: false },
  { id: 6, name: 'Zapier', category: 'Automatyzacja', categoryId: 'automation', description: 'Połącz Sentri z 5000+ aplikacji przez Zapier.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/zapier.svg', bgColor: '#FF4A00', users: 25000, connected: true, connectedAt: '5 gru 2024' },
  { id: 7, name: 'Make', category: 'Automatyzacja', categoryId: 'automation', description: 'Twórz zaawansowane automatyzacje z Make (Integromat).', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/integromat.svg', bgColor: '#6D00CC', users: 4200, connected: false },
  { id: 8, name: 'Google Analytics', category: 'Analityka', categoryId: 'analytics', description: 'Śledź metryki wsparcia w Google Analytics.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/googleanalytics.svg', bgColor: '#E37400', users: 18000, connected: false },
  { id: 9, name: 'Shopify', category: 'E-commerce', categoryId: 'ecommerce', description: 'Integracja z zamówieniami i klientami Shopify.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/shopify.svg', bgColor: '#7AB55C', users: 9500, connected: false },
  { id: 10, name: 'WooCommerce', category: 'E-commerce', categoryId: 'ecommerce', description: 'Połącz sklep WooCommerce ze zgłoszeniami.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/woocommerce.svg', bgColor: '#96588A', users: 6700, connected: false },
  { id: 11, name: 'Jira', category: 'Produktywność', categoryId: 'productivity', description: 'Twórz zadania Jira ze zgłoszeń.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/jira.svg', bgColor: '#0052CC', users: 11000, connected: false },
  { id: 12, name: 'Notion', category: 'Produktywność', categoryId: 'productivity', description: 'Eksportuj zgłoszenia do Notion.', logo: 'https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/notion.svg', bgColor: '#000000', users: 8900, connected: false },
])

const connectedIntegrations = computed(() => integrations.value.filter(i => i.connected))

const filteredIntegrations = computed(() => {
  let result = integrations.value.filter(i => !i.connected)
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(i => i.name.toLowerCase().includes(query) || i.description.toLowerCase().includes(query))
  }
  if (selectedCategory.value) {
    result = result.filter(i => i.categoryId === selectedCategory.value)
  }
  return result
})

const apiKeys = [
  { id: 1, name: 'Production API Key', preview: 'sk_live_...x8Kj', created: '1 gru 2024', lastUsed: '2 godz temu' },
  { id: 2, name: 'Development API Key', preview: 'sk_test_...m2Np', created: '15 lis 2024', lastUsed: '5 dni temu' },
]

const webhooks = [
  { id: 1, url: 'https://api.example.com/webhooks/sentri', events: ['ticket.created', 'ticket.updated'], active: true },
  { id: 2, url: 'https://hooks.slack.com/services/...', events: ['message.sent'], active: true },
]

function connectIntegration(integration) {
  integration.connected = true
  integration.connectedAt = 'Teraz'
  activeTab.value = 'connected'
}

function disconnectIntegration(integration) {
  integration.connected = false
  integration.connectedAt = null
}
</script>
