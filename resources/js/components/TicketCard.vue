<template>
  <div
    class="flex items-center p-4 rounded-xl border transition-all duration-300 cursor-pointer group"
    :class="[
      isSelected 
        ? 'bg-indigo-500/10 border-indigo-500/50 shadow-lg shadow-indigo-500/10' 
        : 'bg-gray-900 border-gray-700/50 hover:border-gray-600/50 hover:bg-gray-800/50'
    ]"
    @click="$emit('select', ticket)"
  >
    <!-- Priority Indicator -->
    <div 
      class="w-1 h-12 rounded-full mr-4 flex-shrink-0"
      :class="priorityBarColor"
    ></div>

    <!-- Ticket Info -->
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 mb-1">
        <span class="text-xs text-gray-500 font-mono">#{{ ticket.id }}</span>
        <StatusBadge :status="ticket.status" />
        <PriorityBadge :priority="ticket.priority" />
      </div>
      
      <h3 class="text-white font-medium truncate group-hover:text-indigo-300 transition-colors">
        {{ ticket.subject }}
      </h3>
      
      <p class="text-sm text-gray-400 line-clamp-1 mt-1">
        {{ ticket.description }}
      </p>

      <!-- Tags -->
      <div v-if="ticket.tags?.length" class="flex flex-wrap gap-1 mt-2">
        <span
          v-for="tag in ticket.tags.slice(0, 3)"
          :key="tag.id"
          class="px-2 py-0.5 text-xs rounded-full"
          :style="{ backgroundColor: tag.color + '20', color: tag.color }"
        >
          {{ tag.name }}
        </span>
        <span v-if="ticket.tags.length > 3" class="text-xs text-gray-500">
          +{{ ticket.tags.length - 3 }}
        </span>
      </div>
    </div>

    <!-- Right Side: Avatar & Time -->
    <div class="ml-4 flex flex-col items-end flex-shrink-0">
      <img
        v-if="ticket.customer?.avatar_url"
        :src="ticket.customer.avatar_url"
        :alt="ticket.customer.name"
        class="w-8 h-8 rounded-full ring-2 ring-gray-700"
        :title="ticket.customer.name"
      />
      <p class="text-xs text-gray-500 mt-2">
        {{ formatRelativeTime(ticket.created_at) }}
      </p>
      
      <!-- Unread indicator -->
      <div 
        v-if="hasUnread"
        class="w-2 h-2 bg-indigo-500 rounded-full mt-2 animate-pulse"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import StatusBadge from './StatusBadge.vue'
import PriorityBadge from './PriorityBadge.vue'

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
  isSelected: {
    type: Boolean,
    default: false,
  },
  hasUnread: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['select'])

const priorityBarColor = computed(() => {
  const colors = {
    slate: 'bg-slate-500',
    blue: 'bg-blue-500',
    orange: 'bg-orange-500',
    red: 'bg-red-500',
  }
  return colors[props.ticket.priority?.color] || 'bg-blue-500'
})

function formatRelativeTime(dateString) {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return 'teraz'
  if (minutes < 60) return `${minutes}m`
  if (hours < 24) return `${hours}h`
  if (days < 7) return `${days}d`
  
  return date.toLocaleDateString('pl-PL', { day: 'numeric', month: 'short' })
}
</script>
