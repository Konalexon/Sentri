<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-950 px-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-lg shadow-indigo-500/25 mb-4">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
          Sentri
        </h1>
        <p class="mt-2 text-gray-400">Zaloguj się do panelu wsparcia</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="bg-gray-900 rounded-2xl border border-gray-700/50 p-8 shadow-xl">
        <!-- Error Alert -->
        <div v-if="error" class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm">
          {{ error }}
        </div>

        <div class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
            <input
              v-model="email"
              type="email"
              required
              autofocus
              class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition"
              placeholder="twoj@email.pl"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Hasło</label>
            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 pr-12 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition"
                placeholder="••••••••"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition"
              >
                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input
                v-model="remember"
                type="checkbox"
                class="w-4 h-4 bg-gray-800 border-gray-600 rounded text-indigo-600 focus:ring-indigo-500/50"
              />
              <span class="ml-2 text-sm text-gray-400">Zapamiętaj mnie</span>
            </label>
            <a href="#" class="text-sm text-indigo-400 hover:text-indigo-300 transition">
              Zapomniałeś hasła?
            </a>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-indigo-500/25"
          >
            <span v-if="loading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Logowanie...
            </span>
            <span v-else>Zaloguj się</span>
          </button>
        </div>
      </form>

      <!-- Register Link -->
      <p class="mt-6 text-center text-gray-400">
        Nie masz konta?
        <router-link to="/register" class="text-indigo-400 hover:text-indigo-300 font-medium transition">
          Zarejestruj się
        </router-link>
      </p>

      <!-- Demo Credentials -->
      <div class="mt-8 p-4 bg-gray-900/50 rounded-xl border border-gray-700/30">
        <p class="text-xs text-gray-500 text-center mb-2">Demo credentials:</p>
        <div class="flex flex-wrap justify-center gap-2 text-xs">
          <button @click="fillDemo('admin')" class="px-2 py-1 bg-gray-800 text-gray-400 rounded hover:bg-gray-700 transition">
            Admin
          </button>
          <button @click="fillDemo('agent')" class="px-2 py-1 bg-gray-800 text-gray-400 rounded hover:bg-gray-700 transition">
            Agent
          </button>
          <button @click="fillDemo('customer')" class="px-2 py-1 bg-gray-800 text-gray-400 rounded hover:bg-gray-700 transition">
            Customer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/useAuthStore'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const remember = ref(false)
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const demoCredentials = {
  admin: { email: 'admin@sentri.io', password: 'password' },
  agent: { email: 'agent@sentri.io', password: 'password' },
  customer: { email: 'customer@sentri.io', password: 'password' },
}

function fillDemo(role) {
  email.value = demoCredentials[role].email
  password.value = demoCredentials[role].password
}

async function handleLogin() {
  loading.value = true
  error.value = ''

  try {
    await authStore.login(email.value, password.value)
    const redirect = route.query.redirect || '/dashboard'
    router.push(redirect)
  } catch (err) {
    error.value = err.response?.data?.message || 'Nieprawidłowy email lub hasło'
  } finally {
    loading.value = false
  }
}
</script>
