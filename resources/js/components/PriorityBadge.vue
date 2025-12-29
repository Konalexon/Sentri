<template>
  <span
    class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full"
    :class="priorityClasses"
  >
    <svg v-if="isUrgent" class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
    </svg>
    {{ priority.label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  priority: {
    type: Object,
    required: true,
  },
})

const priorityClasses = computed(() => {
  const classes = {
    slate: 'bg-slate-500/20 text-slate-400',
    blue: 'bg-blue-500/20 text-blue-400',
    orange: 'bg-orange-500/20 text-orange-400',
    red: 'bg-red-500/20 text-red-400 font-semibold',
  }
  return classes[props.priority.color] || classes.blue
})

const isUrgent = computed(() => props.priority.value === 'urgent')
</script>
