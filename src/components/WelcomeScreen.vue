<script setup>
import { ref, onMounted } from 'vue'

const phrases = [
  "Chiedimi qualcosa... ti scoraggerò con stile.",
  "Sono pronto a distruggere i tuoi sogni... dai su fammi una domanda.",
  "Hai un'idea? Perfetto, ti spiego perché non funzionerà.",
  "Dimmi pure... tanto ti dirò di no.",
  "Chiedimi qualsiasi cosa. Spoiler: la risposta è 'non farlo'.",
  "Sono qui per salvarti da te stesso... chiedi pure.",
  "Hai bisogno di un consiglio pessimista? Sono il tuo AI.",
  "Pronto a sentirti dire la verità? Chiedi pure.",
  "Domanda pure... ti risponderò con brutale onestà.",
  "Vuoi sapere se è una buona idea? E se poi te ne penti?",
  "Chiedimi qualcosa... ma preparati alla delusione.",
  "Hai un progetto? Fammi indovinare: è terribile."
]

const randomPhrase = ref('')

onMounted(() => {
  randomPhrase.value = phrases[Math.floor(Math.random() * phrases.length)]
})
</script>
<template>
  <div class="text-center max-w-xl w-full px-6 pt-6 pb-12 animate-fade-in">
    <h1 class="text-4xl font-light text-text-primary mb-2 tracking-tight">Buongiorno, sono <span class="font-semibold">giamm.ai</span></h1>
    <p class="text-text-muted text-base">{{ randomPhrase }}</p>
  </div>
</template>
<style scoped>
@keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fadeIn 0.4s ease; }
</style>
