<script setup></script>
<template>
  <div class="text-center max-w-xl w-full px-6 py-12 animate-fade-in">
    <h1 class="text-4xl font-light text-text-primary mb-2 tracking-tight">Buongiorno, sono <span class="font-semibold">giamm.ai</span></h1>
    <p class="text-text-muted text-base">Chiedimi qualcosa... ti scoraggerò con stile.</p>
  </div>
</template>
<style scoped>
@keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.4s ease; }
</style>
