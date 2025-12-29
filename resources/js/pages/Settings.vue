<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="px-6 py-4">
          <h1 class="text-2xl font-bold text-white">Ustawienia</h1>
          <p class="text-sm text-gray-400">Zarządzaj ustawieniami systemu</p>
        </div>
      </header>

      <div class="p-6 max-w-4xl">
        <!-- Tabs -->
        <div class="flex space-x-1 mb-8 bg-gray-900 rounded-xl p-1 w-fit">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-all"
            :class="activeTab === tab.id ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white'"
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- General Settings -->
        <div v-if="activeTab === 'general'" class="space-y-6">
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h2 class="text-lg font-semibold text-white mb-6">Ustawienia ogólne</h2>
            
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Nazwa firmy</label>
                <input
                  v-model="settings.companyName"
                  type="text"
                  class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Email wsparcia</label>
                <input
                  v-model="settings.supportEmail"
                  type="email"
                  class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Strefa czasowa</label>
                <select
                  v-model="settings.timezone"
                  class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                >
                  <option value="Europe/Warsaw">Europa/Warszawa</option>
                  <option value="Europe/London">Europa/Londyn</option>
                  <option value="America/New_York">Ameryka/Nowy Jork</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Język</label>
                <select
                  v-model="settings.language"
                  class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                >
                  <option value="pl">Polski</option>
                  <option value="en">English</option>
                  <option value="de">Deutsch</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Notifications -->
        <div v-if="activeTab === 'notifications'" class="space-y-6">
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h2 class="text-lg font-semibold text-white mb-6">Powiadomienia</h2>
            
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
                <div>
                  <p class="text-white font-medium">Powiadomienia email</p>
                  <p class="text-sm text-gray-400">Otrzymuj powiadomienia o nowych zgłoszeniach</p>
                </div>
                <button
                  @click="notifications.email = !notifications.email"
                  class="relative w-12 h-6 rounded-full transition-colors"
                  :class="notifications.email ? 'bg-indigo-600' : 'bg-gray-700'"
                >
                  <span
                    class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform"
                    :class="notifications.email ? 'translate-x-6' : ''"
                  ></span>
                </button>
              </div>
              
              <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
                <div>
                  <p class="text-white font-medium">Powiadomienia push</p>
                  <p class="text-sm text-gray-400">Powiadomienia w przeglądarce</p>
                </div>
                <button
                  @click="notifications.push = !notifications.push"
                  class="relative w-12 h-6 rounded-full transition-colors"
                  :class="notifications.push ? 'bg-indigo-600' : 'bg-gray-700'"
                >
                  <span
                    class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform"
                    :class="notifications.push ? 'translate-x-6' : ''"
                  ></span>
                </button>
              </div>
              
              <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
                <div>
                  <p class="text-white font-medium">Dźwięki powiadomień</p>
                  <p class="text-sm text-gray-400">Odtwarzaj dźwięk przy nowej wiadomości</p>
                </div>
                <button
                  @click="notifications.sound = !notifications.sound"
                  class="relative w-12 h-6 rounded-full transition-colors"
                  :class="notifications.sound ? 'bg-indigo-600' : 'bg-gray-700'"
                >
                  <span
                    class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform"
                    :class="notifications.sound ? 'translate-x-6' : ''"
                  ></span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Appearance -->
        <div v-if="activeTab === 'appearance'" class="space-y-6">
          <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6">
            <h2 class="text-lg font-semibold text-white mb-6">Wygląd</h2>
            
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-4">Motyw</label>
                <div class="grid grid-cols-3 gap-4">
                  <button
                    v-for="theme in themes"
                    :key="theme.id"
                    @click="selectedTheme = theme.id"
                    class="p-4 rounded-xl border-2 transition-all"
                    :class="selectedTheme === theme.id ? 'border-indigo-500 bg-indigo-500/10' : 'border-gray-700 hover:border-gray-600'"
                  >
                    <div class="w-full h-16 rounded-lg mb-3" :class="theme.preview"></div>
                    <p class="text-sm text-white font-medium">{{ theme.label }}</p>
                  </button>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-4">Kolor akcentu</label>
                <div class="flex space-x-3">
                  <button
                    v-for="color in accentColors"
                    :key="color.value"
                    @click="accentColor = color.value"
                    class="w-10 h-10 rounded-full transition-transform hover:scale-110"
                    :class="[color.class, accentColor === color.value ? 'ring-2 ring-white ring-offset-2 ring-offset-gray-900' : '']"
                  ></button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <div class="mt-8 flex justify-end">
          <button
            @click="saveSettings"
            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all shadow-lg shadow-indigo-500/25"
          >
            Zapisz zmiany
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const activeTab = ref('general')
const tabs = [
  { id: 'general', label: 'Ogólne' },
  { id: 'notifications', label: 'Powiadomienia' },
  { id: 'appearance', label: 'Wygląd' },
]

const settings = ref({
  companyName: 'Sentri Support',
  supportEmail: 'support@sentri.io',
  timezone: 'Europe/Warsaw',
  language: 'pl',
})

const notifications = ref({
  email: true,
  push: true,
  sound: false,
})

const selectedTheme = ref('dark')
const themes = [
  { id: 'light', label: 'Jasny', preview: 'bg-gray-200' },
  { id: 'dark', label: 'Ciemny', preview: 'bg-gray-800' },
  { id: 'system', label: 'Systemowy', preview: 'bg-gradient-to-r from-gray-200 to-gray-800' },
]

const accentColor = ref('indigo')
const accentColors = [
  { value: 'indigo', class: 'bg-indigo-500' },
  { value: 'purple', class: 'bg-purple-500' },
  { value: 'blue', class: 'bg-blue-500' },
  { value: 'green', class: 'bg-green-500' },
  { value: 'orange', class: 'bg-orange-500' },
  { value: 'pink', class: 'bg-pink-500' },
]

function saveSettings() {
  alert('Ustawienia zapisane!')
}
</script>
