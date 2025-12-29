<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="w-16 h-16 rounded-2xl bg-gray-800 flex items-center justify-center mb-4">
      <slot name="icon">
        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
      </slot>
    </div>
    
    <h3 class="text-lg font-medium text-white mb-2">{{ title }}</h3>
    <p class="text-gray-400 max-w-sm">{{ description }}</p>
    
    <slot name="action">
      <button
        v-if="actionText"
        @click="$emit('action')"
        class="mt-6 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-500 transition-colors"
      >
        {{ actionText }}
      </button>
    </slot>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Brak danych',
  },
  description: {
    type: String,
    default: 'Nie znaleziono żadnych elementów.',
  },
  actionText: {
    type: String,
    default: null,
  },
})

defineEmits(['action'])
</script>
