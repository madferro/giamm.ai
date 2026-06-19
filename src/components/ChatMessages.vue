<script setup>
import ChatMessage from './ChatMessage.vue'
const props = defineProps({ messages: { type: Array, required: true }, loading: { type: Boolean, default: false } })
</script>
<template>
  <div class="w-full flex flex-col">
    <ChatMessage v-for="msg in messages" :key="msg.id" :message="msg" />
    <div v-if="loading" class="flex justify-center border-b border-border">
      <div class="container mx-auto px-5 py-6 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-accent to-accent-hover flex items-center justify-center text-white text-xs font-bold shrink-0">AI</div>
        <div class="flex gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-accent animate-bounce" style="animation-delay: -0.32s"></span>
          <span class="w-1.5 h-1.5 rounded-full bg-accent animate-bounce" style="animation-delay: -0.16s"></span>
          <span class="w-1.5 h-1.5 rounded-full bg-accent animate-bounce"></span>
        </div>
      </div>
    </div>
  </div>
</template>
