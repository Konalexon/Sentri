<template>
  <div class="animate-pulse space-y-4">
    <div v-for="i in count" :key="i" class="flex items-center p-4 bg-gray-900 rounded-xl border border-gray-700/50">
      <!-- Priority bar skeleton -->
      <div class="w-1 h-12 bg-gray-800 rounded-full mr-4"></div>
      
      <!-- Content skeleton -->
      <div class="flex-1 space-y-3">
        <div class="flex items-center space-x-2">
          <div class="h-4 w-12 bg-gray-800 rounded"></div>
          <div class="h-5 w-16 bg-gray-800 rounded-full"></div>
          <div class="h-5 w-14 bg-gray-800 rounded-full"></div>
        </div>
        <div class="h-5 w-3/4 bg-gray-800 rounded"></div>
        <div class="h-4 w-1/2 bg-gray-800 rounded"></div>
      </div>
      
      <!-- Avatar skeleton -->
      <div class="ml-4 flex flex-col items-end space-y-2">
        <div class="w-8 h-8 bg-gray-800 rounded-full"></div>
        <div class="h-3 w-10 bg-gray-800 rounded"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  count: {
    type: Number,
    default: 5,
  },
})
</script>
