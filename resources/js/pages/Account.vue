<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="px-6 py-4">
          <h1 class="text-2xl font-bold text-white">Moje konto</h1>
          <p class="text-sm text-gray-400">Zarządzaj swoim profilem i bezpieczeństwem</p>
        </div>
      </header>

      <div class="p-6 max-w-4xl">
        <!-- Profile Card -->
        <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6 mb-6">
          <div class="flex items-center">
            <div class="relative">
              <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                <span class="text-3xl font-bold text-white">{{ user?.name?.charAt(0) }}</span>
              </div>
              <button class="absolute -bottom-2 -right-2 w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center hover:bg-indigo-500 transition-colors">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
            </div>
            <div class="ml-6">
              <h2 class="text-xl font-bold text-white">{{ user?.name }}</h2>
              <p class="text-gray-400">{{ user?.email }}</p>
              <span
                class="inline-block mt-2 px-3 py-1 text-xs font-medium rounded-full"
                :class="getRoleClass(user?.role)"
              >
                {{ user?.role?.label }}
              </span>
            </div>
          </div>
        </div>

        <!-- Profile Form -->
        <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6 mb-6">
          <h3 class="text-lg font-semibold text-white mb-6">Informacje o profilu</h3>
          
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Imię i nazwisko</label>
                <input
                  v-model="profile.name"
                  type="text"
                  class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                <input
                  v-model="profile.email"
                  type="email"
                  class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Telefon</label>
                <input
                  v-model="profile.phone"
                  type="tel"
                  placeholder="+48 123 456 789"
                  class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Lokalizacja</label>
                <input
                  v-model="profile.location"
                  type="text"
                  placeholder="Warszawa, Polska"
                  class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                />
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Bio</label>
              <textarea
                v-model="profile.bio"
                rows="3"
                placeholder="Opowiedz coś o sobie..."
                class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 resize-none"
              ></textarea>
            </div>
            
            <div class="flex justify-end">
              <button
                type="submit"
                class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all shadow-lg shadow-indigo-500/25"
              >
                Zapisz profil
              </button>
            </div>
          </form>
        </div>

        <!-- Security -->
        <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6 mb-6">
          <h3 class="text-lg font-semibold text-white mb-6">Bezpieczeństwo</h3>
          
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
              <div>
                <p class="text-white font-medium">Zmień hasło</p>
                <p class="text-sm text-gray-400">Ostatnia zmiana: 30 dni temu</p>
              </div>
              <button
                @click="showPasswordModal = true"
                class="px-4 py-2 bg-gray-700 text-white rounded-xl hover:bg-gray-600 transition-colors"
              >
                Zmień
              </button>
            </div>
            
            <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
              <div>
                <p class="text-white font-medium">Uwierzytelnianie dwuskładnikowe</p>
                <p class="text-sm text-gray-400">Dodatkowa warstwa bezpieczeństwa</p>
              </div>
              <button class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-500 transition-colors">
                Włącz
              </button>
            </div>
            
            <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-xl">
              <div>
                <p class="text-white font-medium">Aktywne sesje</p>
                <p class="text-sm text-gray-400">Zarządzaj zalogowanymi urządzeniami</p>
              </div>
              <button class="px-4 py-2 bg-gray-700 text-white rounded-xl hover:bg-gray-600 transition-colors">
                Zobacz (3)
              </button>
            </div>
          </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-gray-900 rounded-2xl border border-red-500/30 p-6">
          <h3 class="text-lg font-semibold text-red-400 mb-6">Strefa zagrożenia</h3>
          
          <div class="flex items-center justify-between p-4 bg-red-500/10 rounded-xl">
            <div>
              <p class="text-white font-medium">Usuń konto</p>
              <p class="text-sm text-gray-400">Ta akcja jest nieodwracalna</p>
            </div>
            <button class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-500 transition-colors">
              Usuń konto
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Password Modal -->
    <Teleport to="body">
      <div v-if="showPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showPasswordModal = false"></div>
        <div class="relative bg-gray-900 rounded-2xl border border-gray-700/50 w-full max-w-md p-6 shadow-2xl">
          <h2 class="text-xl font-bold text-white mb-6">Zmień hasło</h2>
          
          <form @submit.prevent="changePassword" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Obecne hasło</label>
              <input type="password" class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Nowe hasło</label>
              <input type="password" class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Potwierdź nowe hasło</label>
              <input type="password" class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50" />
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button type="button" @click="showPasswordModal = false" class="px-4 py-2 text-gray-400 hover:text-white transition">Anuluj</button>
              <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl">Zmień hasło</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/useAuthStore'
import { storeToRefs } from 'pinia'
import Sidebar from '@/components/Sidebar.vue'

const authStore = useAuthStore()
const { user } = storeToRefs(authStore)

const showPasswordModal = ref(false)

const profile = ref({
  name: user.value?.name || '',
  email: user.value?.email || '',
  phone: '',
  location: '',
  bio: '',
})

function getRoleClass(role) {
  const classes = {
    admin: 'bg-red-500/20 text-red-400',
    agent: 'bg-blue-500/20 text-blue-400',
    customer: 'bg-green-500/20 text-green-400',
  }
  return classes[role?.value] || classes.customer
}

function updateProfile() {
  alert('Profil zaktualizowany!')
}

function changePassword() {
  showPasswordModal.value = false
  alert('Hasło zmienione!')
}
</script>
