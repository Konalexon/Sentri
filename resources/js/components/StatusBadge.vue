<template>
  <span
    class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full"
    :class="statusClasses"
  >
    <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="dotClass"></span>
    {{ status.label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: Object,
    required: true,
  },
})

const statusClasses = computed(() => {
  const classes = {
    green: 'bg-green-500/20 text-green-400',
    yellow: 'bg-yellow-500/20 text-yellow-400',
    gray: 'bg-gray-500/20 text-gray-400',
  }
  return classes[props.status.color] || classes.gray
})

const dotClass = computed(() => {
  const classes = {
    green: 'bg-green-400',
    yellow: 'bg-yellow-400',
    gray: 'bg-gray-400',
  }
  return classes[props.status.color] || classes.gray
})
</script>
