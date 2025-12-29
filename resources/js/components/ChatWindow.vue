<template>
  <div class="flex flex-col h-full bg-gray-900 rounded-xl border border-gray-700/50">
    <!-- Chat Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700/50">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        </div>
        <div>
          <h3 class="text-white font-semibold">{{ ticket?.subject || 'Czat' }}</h3>
          <p class="text-sm text-gray-400">
            <span v-if="ticket?.customer">{{ ticket.customer.name }}</span>
            <span v-if="ticket?.status" class="ml-2 px-2 py-0.5 rounded-full text-xs" :class="statusClasses">
              {{ ticket.status.label }}
            </span>
          </p>
        </div>
      </div>
      
      <!-- Status Actions -->
      <div v-if="canManageTickets" class="flex items-center space-x-2">
        <button
          v-for="status in availableStatuses"
          :key="status.value"
          @click="changeStatus(status.value)"
          class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all duration-200"
          :class="status.classes"
        >
          {{ status.label }}
        </button>
      </div>
    </div>

    <!-- Messages Container -->
    <div 
      ref="messagesContainer"
      class="flex-1 overflow-y-auto px-6 py-4 space-y-4 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent"
    >
      <div v-if="loading" class="flex justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
      </div>

      <template v-else>
        <TransitionGroup name="message" tag="div" class="space-y-4">
          <div
            v-for="message in messages"
            :key="message.id"
            class="flex"
            :class="isOwnMessage(message) ? 'justify-end' : 'justify-start'"
          >
            <div class="flex max-w-[75%]" :class="isOwnMessage(message) ? 'flex-row-reverse' : 'flex-row'">
              <!-- Avatar -->
              <img
                :src="message.sender?.avatar_url"
                :alt="message.sender?.name"
                class="w-8 h-8 rounded-full flex-shrink-0"
                :class="isOwnMessage(message) ? 'ml-3' : 'mr-3'"
              />
              
              <!-- Message Bubble -->
              <div
                class="rounded-2xl px-4 py-3 shadow-lg"
                :class="[
                  isOwnMessage(message) 
                    ? 'bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-tr-none' 
                    : 'bg-gray-800 text-gray-100 rounded-tl-none',
                  message.is_internal ? 'border-2 border-yellow-500/50' : ''
                ]"
              >
                <!-- Internal Note Badge -->
                <div v-if="message.is_internal" class="flex items-center text-yellow-400 text-xs mb-2">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                  </svg>
                  Notatka wewnętrzna
                </div>
                
                <!-- Content -->
                <p class="text-sm whitespace-pre-wrap break-words">{{ message.content }}</p>
                
                <!-- Attachments -->
                <div v-if="message.attachments?.length" class="mt-3 space-y-2">
                  <div
                    v-for="attachment in message.attachments"
                    :key="attachment.id"
                    class="flex items-center space-x-2 p-2 rounded-lg"
                    :class="isOwnMessage(message) ? 'bg-indigo-500/30' : 'bg-gray-700/50'"
                  >
                    <img
                      v-if="attachment.is_image"
                      :src="attachment.url"
                      :alt="attachment.filename"
                      class="w-20 h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition"
                      @click="openAttachment(attachment)"
                    />
                    <a
                      v-else
                      :href="attachment.url"
                      target="_blank"
                      class="flex items-center space-x-2 text-sm hover:underline"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                      </svg>
                      <span>{{ attachment.filename }}</span>
                    </a>
                  </div>
                </div>
                
                <!-- Timestamp -->
                <p class="text-xs mt-2 opacity-60">
                  {{ formatTime(message.created_at) }}
                </p>
              </div>
            </div>
          </div>
        </TransitionGroup>

        <!-- Typing Indicator -->
        <div v-if="isTyping" class="flex items-center space-x-2 text-gray-400">
          <div class="flex space-x-1">
            <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
            <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
            <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
          </div>
          <span class="text-sm">Ktoś pisze...</span>
        </div>
      </template>
    </div>

    <!-- Input Area -->
    <div class="border-t border-gray-700/50 p-4">
      <!-- Attachment Preview -->
      <div v-if="attachments.length" class="flex flex-wrap gap-2 mb-3">
        <div
          v-for="(file, index) in attachments"
          :key="index"
          class="flex items-center bg-gray-800 rounded-lg px-3 py-2 text-sm text-gray-300"
        >
          <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
          </svg>
          {{ file.name }}
          <button @click="removeAttachment(index)" class="ml-2 text-gray-500 hover:text-red-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <div class="flex items-end space-x-3">
        <!-- Internal Note Toggle (for agents) -->
        <button
          v-if="canManageTickets"
          @click="isInternal = !isInternal"
          class="p-2 rounded-lg transition-colors"
          :class="isInternal ? 'bg-yellow-500/20 text-yellow-400' : 'text-gray-400 hover:text-gray-300 hover:bg-gray-800'"
          title="Notatka wewnętrzna"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </button>

        <!-- Attachment Button -->
        <label class="p-2 text-gray-400 hover:text-gray-300 hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
          </svg>
          <input
            type="file"
            multiple
            class="hidden"
            @change="handleFileSelect"
            accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt"
          />
        </label>

        <!-- Message Input -->
        <div class="flex-1 relative">
          <textarea
            v-model="newMessage"
            @keydown.enter.exact.prevent="sendMessage"
            placeholder="Napisz wiadomość..."
            rows="1"
            class="w-full bg-gray-800 text-white placeholder-gray-500 rounded-xl px-4 py-3 pr-12 resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500/50 border border-gray-700/50 transition-all"
            :class="isInternal ? 'border-yellow-500/50' : ''"
          ></textarea>
        </div>

        <!-- Send Button -->
        <button
          @click="sendMessage"
          :disabled="!canSend"
          class="p-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:from-indigo-500 hover:to-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 active:scale-95"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useTicketStore } from '@/stores/useTicketStore'
import { useAuthStore } from '@/stores/useAuthStore'
import { storeToRefs } from 'pinia'

const props = defineProps({
  ticketId: {
    type: [Number, String],
    required: true,
  },
})

const emit = defineEmits(['close'])

const ticketStore = useTicketStore()
const authStore = useAuthStore()

const { currentTicket: ticket, messages, loading } = storeToRefs(ticketStore)
const { user, canManageTickets } = storeToRefs(authStore)

const newMessage = ref('')
const isInternal = ref(false)
const attachments = ref([])
const messagesContainer = ref(null)
const isTyping = ref(false)

const canSend = computed(() => {
  return (newMessage.value.trim() || attachments.value.length) && !loading.value
})

const statusClasses = computed(() => {
  const color = ticket.value?.status?.color || 'gray'
  return {
    'bg-green-500/20 text-green-400': color === 'green',
    'bg-yellow-500/20 text-yellow-400': color === 'yellow',
    'bg-gray-500/20 text-gray-400': color === 'gray',
  }
})

const availableStatuses = computed(() => {
  const current = ticket.value?.status?.value
  const statuses = []
  
  if (current !== 'pending') {
    statuses.push({
      value: 'pending',
      label: 'Oczekujący',
      classes: 'bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30',
    })
  }
  if (current !== 'closed') {
    statuses.push({
      value: 'closed',
      label: 'Zamknij',
      classes: 'bg-gray-500/20 text-gray-400 hover:bg-gray-500/30',
    })
  }
  if (current === 'closed') {
    statuses.push({
      value: 'open',
      label: 'Otwórz ponownie',
      classes: 'bg-green-500/20 text-green-400 hover:bg-green-500/30',
    })
  }
  
  return statuses
})

function isOwnMessage(message) {
  return message.sender?.id === user.value?.id
}

function formatTime(dateString) {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  
  if (diff < 60000) return 'Teraz'
  if (diff < 3600000) return `${Math.floor(diff / 60000)} min temu`
  if (diff < 86400000) return date.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' })
  
  return date.toLocaleDateString('pl-PL', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}

async function sendMessage() {
  if (!canSend.value) return
  
  const content = newMessage.value.trim()
  const files = [...attachments.value]
  
  newMessage.value = ''
  attachments.value = []
  
  try {
    await ticketStore.sendMessage(props.ticketId, content, isInternal.value, files)
    scrollToBottom()
  } catch (err) {
    // Restore message on error
    newMessage.value = content
    console.error('Failed to send message:', err)
  }
}

async function changeStatus(status) {
  try {
    await ticketStore.updateTicketStatus(props.ticketId, status)
  } catch (err) {
    console.error('Failed to update status:', err)
  }
}

function handleFileSelect(event) {
  const files = Array.from(event.target.files)
  attachments.value = [...attachments.value, ...files].slice(0, 5)
  event.target.value = ''
}

function removeAttachment(index) {
  attachments.value.splice(index, 1)
}

function openAttachment(attachment) {
  window.open(attachment.url, '_blank')
}

function scrollToBottom() {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

// Watch for new messages and scroll
watch(messages, () => {
  scrollToBottom()
}, { deep: true })

// Load ticket on mount
onMounted(async () => {
  await ticketStore.fetchTicket(props.ticketId)
  scrollToBottom()
})

onUnmounted(() => {
  ticketStore.clearCurrentTicket()
})
</script>

<style scoped>
.message-enter-active,
.message-leave-active {
  transition: all 0.3s ease;
}

.message-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.message-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}

.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: rgba(107, 114, 128, 0.5);
  border-radius: 3px;
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background-color: rgba(107, 114, 128, 0.8);
}
</style>
