<template>
  <aside
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-gray-900 border-r border-gray-700/50 transition-all duration-300"
    :class="isExpanded ? 'w-64' : 'w-20'"
  >
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-700/50">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <span v-if="isExpanded" class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
          Sentri
        </span>
      </div>
      <button
        @click="toggleSidebar"
        class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors lg:hidden"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path v-if="isExpanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <router-link
        v-for="item in navigationItems"
        :key="item.name"
        :to="item.to"
        class="flex items-center px-3 py-3 rounded-xl text-gray-400 hover:text-white hover:bg-gray-800/50 transition-all duration-200 group"
        :class="{ 'bg-gradient-to-r from-indigo-600/20 to-purple-600/20 text-white border border-indigo-500/30': isActive(item.to) }"
      >
        <component :is="item.icon" class="w-5 h-5 flex-shrink-0" :class="{ 'text-indigo-400': isActive(item.to) }" />
        <span v-if="isExpanded" class="ml-3 font-medium">{{ item.name }}</span>
        <span
          v-if="item.badge && isExpanded"
          class="ml-auto px-2 py-0.5 text-xs font-medium rounded-full"
          :class="item.badgeClass"
        >
          {{ item.badge }}
        </span>
      </router-link>

      <!-- Admin Section -->
      <div v-if="adminItems.length > 0" class="pt-4 mt-4 border-t border-gray-700/50">
        <p v-if="isExpanded" class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase">Admin</p>
        <router-link
          v-for="item in adminItems"
          :key="item.name"
          :to="item.to"
          class="flex items-center px-3 py-3 rounded-xl text-red-400 hover:text-white hover:bg-red-500/20 transition-all duration-200 group"
          :class="{ 'bg-red-500/20 text-white border border-red-500/30': isActive(item.to) }"
        >
          <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
          <span v-if="isExpanded" class="ml-3 font-medium">{{ item.name }}</span>
        </router-link>
      </div>
    </nav>

    <!-- Statistics (collapsed shows icons only) -->
    <div v-if="isExpanded" class="px-4 py-4 border-t border-gray-700/50">
      <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Statystyki</h3>
      <div class="grid grid-cols-2 gap-2">
        <div class="bg-gray-800/50 rounded-lg p-3 text-center">
          <p class="text-2xl font-bold text-green-400">{{ statistics.open }}</p>
          <p class="text-xs text-gray-500">Otwarte</p>
        </div>
        <div class="bg-gray-800/50 rounded-lg p-3 text-center">
          <p class="text-2xl font-bold text-yellow-400">{{ statistics.pending }}</p>
          <p class="text-xs text-gray-500">Oczekujące</p>
        </div>
      </div>
    </div>

    <!-- User Profile -->
    <div class="border-t border-gray-700/50 p-4">
      <div class="flex items-center" :class="isExpanded ? 'space-x-3' : 'justify-center'">
        <img
          :src="user?.avatar_url"
          :alt="user?.name"
          class="w-10 h-10 rounded-full ring-2 ring-gray-700"
        />
        <div v-if="isExpanded" class="flex-1 min-w-0">
          <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
          <p class="text-xs text-gray-500 truncate">{{ roleLabel }}</p>
        </div>
        <button
          v-if="isExpanded"
          @click="logout"
          class="p-2 text-gray-400 hover:text-red-400 hover:bg-gray-800 rounded-lg transition-colors"
          title="Wyloguj"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Expand/Collapse Toggle (desktop) -->
    <button
      @click="toggleSidebar"
      class="hidden lg:flex absolute -right-3 top-20 w-6 h-6 items-center justify-center bg-gray-800 border border-gray-700 rounded-full text-gray-400 hover:text-white hover:bg-gray-700 transition-colors"
    >
      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path v-if="isExpanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
    </button>
  </aside>
</template>

<script setup>
import { ref, computed, h } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/useAuthStore'
import { useTicketStore } from '@/stores/useTicketStore'
import { storeToRefs } from 'pinia'

const emit = defineEmits(['toggle'])

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const ticketStore = useTicketStore()

const { user } = storeToRefs(authStore)
const { statistics } = storeToRefs(ticketStore)

const isExpanded = ref(true)

const roleLabel = computed(() => {
  const roles = {
    admin: 'Administrator',
    agent: 'Agent',
    customer: 'Klient',
  }
  return roles[user.value?.role] || ''
})

// Icon components as render functions
const DashboardIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' })
  ])
}

const TicketIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z' })
  ])
}

const InboxIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4' })
  ])
}

const SettingsIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })
  ])
}

const AccountIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })
  ])
}

const TeamIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
  ])
}

const AnalyticsIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' })
  ])
}

const KnowledgeIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })
  ])
}

const navigationItems = computed(() => [
  {
    name: 'Dashboard',
    to: '/dashboard',
    icon: DashboardIcon,
  },
  {
    name: 'Zgłoszenia',
    to: '/tickets',
    icon: TicketIcon,
    badge: statistics.value.open || null,
    badgeClass: 'bg-indigo-500/20 text-indigo-400',
  },
  {
    name: 'Skrzynka',
    to: '/inbox',
    icon: InboxIcon,
  },
  {
    name: 'Zespół',
    to: '/team',
    icon: TeamIcon,
  },
  {
    name: 'Analityka',
    to: '/analytics',
    icon: AnalyticsIcon,
  },
  {
    name: 'Baza wiedzy',
    to: '/knowledge-base',
    icon: KnowledgeIcon,
  },
  {
    name: 'Integracje',
    to: '/integrations',
    icon: IntegrationsIcon,
  },
  {
    name: 'Aktywność',
    to: '/activity-log',
    icon: ActivityIcon,
  },
  {
    name: 'Ustawienia',
    to: '/settings',
    icon: SettingsIcon,
  },
  {
    name: 'Moje konto',
    to: '/account',
    icon: AccountIcon,
  },
])

const IntegrationsIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1' })
  ])
}

const ActivityIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' })
  ])
}

// Admin-only navigation
const AdminIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })
  ])
}

const adminItems = computed(() => {
  if (user.value?.role !== 'admin') return []
  return [
    {
      name: 'Panel admina',
      to: '/admin',
      icon: AdminIcon,
    },
  ]
})

function isActive(path) {
  return route.path.startsWith(path)
}

function toggleSidebar() {
  isExpanded.value = !isExpanded.value
  emit('toggle', isExpanded.value)
}

async function logout() {
  await authStore.logout()
  router.push('/login')
}
</script>
