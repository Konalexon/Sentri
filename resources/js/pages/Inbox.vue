<template>
  <div class="min-h-screen bg-gray-950">
    <Sidebar />
    
    <main class="lg:ml-64 transition-all duration-300">
      <header class="sticky top-0 z-40 bg-gray-900/80 backdrop-blur-xl border-b border-gray-700/50">
        <div class="flex items-center justify-between px-6 py-4">
          <div>
            <h1 class="text-2xl font-bold text-white">Skrzynka odbiorcza</h1>
            <p class="text-sm text-gray-400">Twoje wiadomości i powiadomienia</p>
          </div>
          
          <div class="flex items-center space-x-2">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              class="px-4 py-2 text-sm font-medium rounded-xl transition-all"
              :class="activeTab === tab.id ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800'"
            >
              {{ tab.label }}
              <span v-if="tab.count" class="ml-2 px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ tab.count }}</span>
            </button>
          </div>
        </div>
      </header>

      <div class="flex h-[calc(100vh-73px)]">
        <!-- Message List -->
        <div class="w-96 border-r border-gray-700/50 overflow-y-auto flex-shrink-0">
          <div
            v-for="message in filteredMessages"
            :key="message.id"
            class="flex items-start p-4 border-b border-gray-800 cursor-pointer transition-colors"
            :class="[
              selectedMessage?.id === message.id ? 'bg-indigo-600/10 border-l-2 border-l-indigo-500' : 'hover:bg-gray-800/50',
              !message.read && 'bg-indigo-500/5'
            ]"
            @click="selectMessage(message)"
          >
            <!-- Unread indicator -->
            <div class="w-2 h-2 mt-2 mr-3 rounded-full flex-shrink-0" :class="message.read ? 'bg-transparent' : 'bg-indigo-500'"></div>
            
            <!-- Avatar -->
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center flex-shrink-0 mr-3">
              <span class="text-white font-medium">{{ message.sender.charAt(0) }}</span>
            </div>
            
            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-1">
                <span class="font-medium text-white text-sm truncate">{{ message.sender }}</span>
                <span class="text-xs text-gray-500 flex-shrink-0">{{ formatTime(message.time) }}</span>
              </div>
              <p class="text-sm text-gray-300 font-medium truncate">{{ message.subject }}</p>
              <p class="text-xs text-gray-500 truncate">{{ message.preview }}</p>
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="filteredMessages.length === 0" class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <p class="text-gray-400">Brak wiadomości</p>
          </div>
        </div>

        <!-- Message Detail -->
        <div class="flex-1 overflow-y-auto">
          <div v-if="selectedMessage" class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
              <div class="flex items-center">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center mr-4">
                  <span class="text-xl font-bold text-white">{{ selectedMessage.sender.charAt(0) }}</span>
                </div>
                <div>
                  <h2 class="text-lg font-semibold text-white">{{ selectedMessage.sender }}</h2>
                  <p class="text-sm text-gray-400">{{ selectedMessage.email || 'brak@email.com' }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors" title="Odpowiedz">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                  </svg>
                </button>
                <button class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors" title="Archiwizuj">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                  </svg>
                </button>
                <button class="p-2 text-gray-400 hover:text-red-400 hover:bg-gray-800 rounded-lg transition-colors" title="Usuń">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Subject -->
            <div class="mb-6">
              <h3 class="text-xl font-bold text-white mb-2">{{ selectedMessage.subject }}</h3>
              <div class="flex items-center text-sm text-gray-500">
                <span>{{ formatDateTime(selectedMessage.time) }}</span>
                <span v-if="selectedMessage.ticket" class="ml-4 px-2 py-1 bg-indigo-500/20 text-indigo-400 rounded text-xs">
                  Zgłoszenie #{{ selectedMessage.ticket }}
                </span>
              </div>
            </div>

            <!-- Body -->
            <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-6 mb-6">
              <div class="prose prose-invert max-w-none">
                <p class="text-gray-300 leading-relaxed whitespace-pre-wrap">{{ selectedMessage.body }}</p>
              </div>
            </div>

            <!-- Quick Reply -->
            <div class="bg-gray-900 rounded-2xl border border-gray-700/50 p-4">
              <div class="flex items-start space-x-4">
                <div class="flex-1">
                  <textarea
                    v-model="replyText"
                    rows="3"
                    placeholder="Napisz odpowiedź..."
                    class="w-full bg-gray-800 text-white rounded-xl px-4 py-3 border border-gray-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 resize-none"
                  ></textarea>
                </div>
                <button 
                  @click="sendReply"
                  :disabled="!replyText.trim()"
                  class="px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-500 hover:to-purple-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- No message selected -->
          <div v-else class="flex items-center justify-center h-full">
            <div class="text-center">
              <svg class="w-20 h-20 mx-auto text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <p class="text-gray-500 text-lg">Wybierz wiadomość, aby zobaczyć szczegóły</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Sidebar from '@/components/Sidebar.vue'

const activeTab = ref('all')
const selectedMessage = ref(null)
const replyText = ref('')

const tabs = [
  { id: 'all', label: 'Wszystkie', count: null },
  { id: 'unread', label: 'Nieprzeczytane', count: 3 },
  { id: 'starred', label: 'Oznaczone', count: null },
]

// Mock data - in real app this would come from API
const messages = ref([
  {
    id: 1,
    sender: 'Jan Kowalski',
    email: 'jan.kowalski@example.com',
    subject: 'Re: Problem z logowaniem',
    preview: 'Dziękuję za szybką odpowiedź. Problem został rozwiązany...',
    body: `Dziękuję za szybką odpowiedź. Problem został rozwiązany po zresetowaniu hasła.

Wszystko działa teraz poprawnie. Doceniam Waszą pomoc!

Pozdrawiam,
Jan Kowalski`,
    time: new Date(Date.now() - 1000 * 60 * 5),
    read: false,
    ticket: 1,
  },
  {
    id: 2,
    sender: 'System',
    email: 'noreply@sentri.io',
    subject: 'Nowe zgłoszenie #3',
    preview: 'Zostało utworzone nowe zgłoszenie wymagające Twojej uwagi.',
    body: `Zostało utworzone nowe zgłoszenie wymagające Twojej uwagi.

Temat: Błąd przy eksporcie danych
Priorytet: Wysoki
Autor: Anna Nowak

Prosimy o jak najszybsze rozpatrzenie.`,
    time: new Date(Date.now() - 1000 * 60 * 30),
    read: false,
    ticket: 3,
  },
  {
    id: 3,
    sender: 'Anna Nowak',
    email: 'anna.nowak@company.pl',
    subject: 'Pytanie o funkcjonalność',
    preview: 'Czy możliwe jest dodanie eksportu do PDF?',
    body: `Dzień dobry,

Czy możliwe jest dodanie eksportu do PDF? Obecnie mamy możliwość eksportu tylko do CSV i Excel, ale nasi klienci często proszą o PDF.

Czy jest to funkcjonalność, którą planujecie dodać w przyszłości?

Z poważaniem,
Anna Nowak
Dział Sprzedaży`,
    time: new Date(Date.now() - 1000 * 60 * 60 * 2),
    read: true,
    ticket: 2,
  },
  {
    id: 4,
    sender: 'Administrator',
    email: 'admin@sentri.io',
    subject: 'Aktualizacja systemu',
    preview: 'Planowana przerwa techniczna w dniu 30.12.2024...',
    body: `Szanowni Użytkownicy,

Informujemy o planowanej przerwie technicznej:

📅 Data: 30.12.2024
🕐 Godziny: 02:00 - 04:00
⚙️ Cel: Aktualizacja systemu do wersji 2.0

W tym czasie system będzie niedostępny. Przepraszamy za niedogodności.

Zespół Sentri`,
    time: new Date(Date.now() - 1000 * 60 * 60 * 24),
    read: true,
    ticket: null,
  },
])

const filteredMessages = computed(() => {
  if (activeTab.value === 'unread') {
    return messages.value.filter(m => !m.read)
  }
  return messages.value
})

function formatTime(date) {
  const now = new Date()
  const diff = now - date
  
  if (diff < 60000) return 'Teraz'
  if (diff < 3600000) return `${Math.floor(diff / 60000)} min`
  if (diff < 86400000) return `${Math.floor(diff / 3600000)} godz`
  
  return date.toLocaleDateString('pl-PL', { day: 'numeric', month: 'short' })
}

function formatDateTime(date) {
  return date.toLocaleString('pl-PL', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function selectMessage(message) {
  selectedMessage.value = message
  message.read = true
  replyText.value = ''
}

function sendReply() {
  if (!replyText.value.trim()) return
  
  // In real app, this would call API
  alert(`Odpowiedź wysłana: ${replyText.value}`)
  replyText.value = ''
}
</script>
