<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h1 class="text-2xl font-bold text-white">Baza wiedzy</h1>
            <p class="text-sm text-gray-400">Centrum pomocy i dokumentacji</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Szukaj artykułów..."
                class="w-96 bg-gray-800 text-white placeholder-gray-500 rounded-xl px-4 py-2 pl-10 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
              />
              <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>

            <button class="flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nowy artykuł
            </button>
          </div>
        </div>
      </header>

      <div class="p-6">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4 flex items-center">
            <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center mr-4">
              <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-white">{{ articles.length }}</p>
              <p class="text-sm text-gray-400">Artykułów</p>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4 flex items-center">
            <div class="w-12 h-12 rounded-xl bg-green-500/20 flex items-center justify-center mr-4">
              <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-white">12,847</p>
              <p class="text-sm text-gray-400">Wyświetleń</p>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4 flex items-center">
            <div class="w-12 h-12 rounded-xl bg-yellow-500/20 flex items-center justify-center mr-4">
              <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-white">4.6</p>
              <p class="text-sm text-gray-400">Średnia ocena</p>
            </div>
          </div>
          <div class="bg-gray-900 rounded-xl border border-gray-700/50 p-4 flex items-center">
            <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center mr-4">
              <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-white">{{ categories.length }}</p>
              <p class="text-sm text-gray-400">Kategorii</p>
            </div>
          </div>
        </div>

        <!-- Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div
            v-for="category in categories"
            :key="category.id"
            @click="selectedCategory = category.id"
            class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6 cursor-pointer hover:border-indigo-500/50 transition-all group"
            :class="{ 'border-indigo-500/50 ring-2 ring-indigo-500/20': selectedCategory === category.id }"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="w-14 h-14 rounded-xl flex items-center justify-center" :class="category.bgColor">
                <component :is="category.icon" class="w-7 h-7" :class="category.iconColor" />
              </div>
              <span class="text-sm text-gray-400">{{ category.count }} artykułów</span>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2 group-hover:text-indigo-400 transition-colors">{{ category.name }}</h3>
            <p class="text-sm text-gray-400">{{ category.description }}</p>
          </div>
        </div>

        <!-- Articles List -->
        <div class="bg-gray-900 rounded-2xl border border-gray-700/50 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-700/50">
            <h3 class="text-lg font-semibold text-white">Popularne artykuły</h3>
          </div>
          
          <div class="divide-y divide-gray-800">
            <div
              v-for="article in filteredArticles"
              :key="article.id"
              @click="openArticle(article)"
              class="p-6 hover:bg-gray-800/50 cursor-pointer transition-colors"
            >
              <div class="flex items-start">
                <div class="flex-1">
                  <div class="flex items-center space-x-3 mb-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-lg" :class="getCategoryClass(article.category)">
                      {{ getCategoryName(article.category) }}
                    </span>
                    <span class="text-xs text-gray-500">{{ article.readTime }} min czytania</span>
                  </div>
                  <h4 class="text-lg font-medium text-white mb-2 hover:text-indigo-400 transition-colors">{{ article.title }}</h4>
                  <p class="text-sm text-gray-400 mb-3">{{ article.excerpt }}</p>
                  <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      {{ article.views }}
                    </span>
                    <span class="flex items-center">
                      <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      {{ article.rating }}
                    </span>
                    <span>Aktualizacja: {{ article.updated }}</span>
                  </div>
                </div>
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Article Preview Modal -->
    <Teleport to="body">
      <div v-if="selectedArticle" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="selectedArticle = null"></div>
        <div class="relative bg-gray-900 rounded-2xl border border-gray-700/50 w-full max-w-3xl max-h-[80vh] overflow-hidden shadow-2xl">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700/50">
            <h2 class="text-xl font-bold text-white">{{ selectedArticle.title }}</h2>
            <button @click="selectedArticle = null" class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div class="p-6 overflow-y-auto max-h-[calc(80vh-120px)]">
            <div class="prose prose-invert max-w-none">
              <p class="text-gray-300 leading-relaxed">{{ selectedArticle.content }}</p>
            </div>
          </div>
          <div class="flex items-center justify-between px-6 py-4 border-t border-gray-700/50">
            <div class="flex items-center space-x-2 text-sm text-gray-400">
              <span>Czy ten artykuł był pomocny?</span>
              <button class="p-2 hover:bg-green-500/20 hover:text-green-400 rounded-lg transition-colors">👍</button>
              <button class="p-2 hover:bg-red-500/20 hover:text-red-400 rounded-lg transition-colors">👎</button>
            </div>
            <button class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-500 transition-colors">
              Otwórz pełny artykuł
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, h } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const searchQuery = ref('')
const selectedCategory = ref(null)
const selectedArticle = ref(null)

const BookIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })]) }
const CogIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }), h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })]) }
const CardIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })]) }
const UserIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })]) }
const ShieldIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })]) }
const ApiIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4' })]) }

const categories = [
  { id: 'getting-started', name: 'Pierwsze kroki', description: 'Rozpocznij pracę z Sentri', count: 8, icon: BookIcon, bgColor: 'bg-blue-500/20', iconColor: 'text-blue-400' },
  { id: 'tickets', name: 'Zgłoszenia', description: 'Zarządzanie zgłoszeniami', count: 12, icon: CogIcon, bgColor: 'bg-green-500/20', iconColor: 'text-green-400' },
  { id: 'billing', name: 'Płatności', description: 'Faktury i rozliczenia', count: 6, icon: CardIcon, bgColor: 'bg-yellow-500/20', iconColor: 'text-yellow-400' },
  { id: 'account', name: 'Konto', description: 'Ustawienia profilu', count: 10, icon: UserIcon, bgColor: 'bg-purple-500/20', iconColor: 'text-purple-400' },
  { id: 'security', name: 'Bezpieczeństwo', description: 'Ochrona danych', count: 7, icon: ShieldIcon, bgColor: 'bg-red-500/20', iconColor: 'text-red-400' },
  { id: 'api', name: 'API', description: 'Integracje i API', count: 15, icon: ApiIcon, bgColor: 'bg-indigo-500/20', iconColor: 'text-indigo-400' },
]

const articles = ref([
  { id: 1, title: 'Jak utworzyć nowe zgłoszenie?', excerpt: 'Dowiedz się jak szybko i skutecznie zgłosić problem lub zadać pytanie naszemu zespołowi wsparcia.', category: 'tickets', views: 1245, rating: 4.8, readTime: 3, updated: '2 dni temu', content: 'Tworzenie zgłoszenia w Sentri jest proste i intuicyjne. Kliknij przycisk "Nowe zgłoszenie" w górnej części ekranu. Wypełnij temat i opis problemu. Wybierz priorytet. Kliknij "Utwórz". Twoje zgłoszenie zostanie natychmiast przekazane do naszego zespołu.' },
  { id: 2, title: 'Resetowanie hasła - krok po kroku', excerpt: 'Szczegółowy przewodnik jak bezpiecznie zresetować hasło do swojego konta.', category: 'security', views: 892, rating: 4.9, readTime: 2, updated: '1 tydzień temu', content: 'Jeśli zapomniałeś hasła, kliknij "Zapomniałem hasła" na stronie logowania. Wprowadź swój adres email. Sprawdź skrzynkę pocztową i kliknij link resetujący. Utwórz nowe, silne hasło.' },
  { id: 3, title: 'Integracja z API - wprowadzenie', excerpt: 'Kompleksowy przewodnik po API Sentri dla programistów.', category: 'api', views: 567, rating: 4.5, readTime: 10, updated: '3 dni temu', content: 'Sentri oferuje pełne REST API do integracji z Twoimi systemami. Tworzenie tokenów API odbywa się w panelu ustawień. Wszystkie endpointy używają autoryzacji Bearer Token. Dokumentacja OpenAPI jest dostępna pod adresem /api/docs.' },
  { id: 4, title: 'Zarządzanie zespołem', excerpt: 'Jak dodawać i zarządzać członkami zespołu w Sentri.', category: 'account', views: 423, rating: 4.7, readTime: 5, updated: '5 dni temu', content: 'Dodawanie nowych członków zespołu jest proste. Przejdź do sekcji Zespół. Kliknij "Zaproś". Wprowadź email i wybierz rolę. Osoba otrzyma zaproszenie mailowe.' },
  { id: 5, title: 'Pierwsze kroki z Sentri', excerpt: 'Kompleksowy przewodnik dla nowych użytkowników platformy.', category: 'getting-started', views: 2156, rating: 4.9, readTime: 8, updated: '1 dzień temu', content: 'Witamy w Sentri! Ten przewodnik pomoże Ci rozpocząć pracę z naszą platformą. Najpierw skonfiguruj swój profil, następnie zapoznaj się z dashboardem. Poznaj główne funkcje: zgłoszenia, skrzynkę odbiorczą, zespół.' },
])

const filteredArticles = computed(() => {
  let result = articles.value
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(a => a.title.toLowerCase().includes(query) || a.excerpt.toLowerCase().includes(query))
  }
  if (selectedCategory.value) {
    result = result.filter(a => a.category === selectedCategory.value)
  }
  return result
})

function getCategoryClass(categoryId) {
  const cat = categories.find(c => c.id === categoryId)
  return cat ? `${cat.bgColor} ${cat.iconColor}` : 'bg-gray-700 text-gray-300'
}

function getCategoryName(categoryId) {
  const cat = categories.find(c => c.id === categoryId)
  return cat ? cat.name : categoryId
}

function openArticle(article) {
  selectedArticle.value = article
}
</script>
