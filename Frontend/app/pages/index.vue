<script setup>
// Redirection automatique vers la page de connexion
definePageMeta({ layout: false })
const router = useRouter()
onMounted(() => {
  router.replace('/login')
})
</script>

<template>
  <div class="flex items-center justify-center h-screen bg-gray-50">
    <p class="text-gray-400 text-sm">Redirection...</p>
  </div>
</template>
