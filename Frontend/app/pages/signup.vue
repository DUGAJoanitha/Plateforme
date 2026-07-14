<template>
  <div class="bg-surface-container h-screen w-screen overflow-hidden flex items-center justify-center p-4">
    <!-- Main Authentication Shell -->
    <main class="bg-surface-container-lowest w-full max-w-[800px] h-full max-h-[550px] flex flex-col md:flex-row overflow-hidden shadow-xl rounded-[2rem]">
      <!-- Left Section: Registration Form -->
      <section class="w-full md:w-1/2 h-full flex flex-col px-6 py-6 md:px-8 md:py-8 relative">
        <!-- TopAppBar Anchor (Simplified Login context) -->
        <header class="flex items-center mb-3">
          <img src="/logo.png" alt="Neo Start Technology" class="h-12 object-contain" />
        </header>

        <div class="flex-grow flex flex-col justify-center max-w-[400px] mx-auto w-full gap-3">
          <div>
            <h2 class="font-headline-sm text-lg font-bold text-on-surface mb-1">Bienvenue chez Néo Start Technology</h2>
          </div>

          <!-- Tabbed Navigation -->
          <div class="flex p-1 bg-surface-container-low rounded-lg">
            <NuxtLink to="/login" class="flex-1 py-1 text-center text-xs font-semibold rounded-lg text-on-surface-variant hover:bg-surface-container transition-all">Sign In</NuxtLink>
            <NuxtLink to="/signup" class="flex-1 py-1 text-center text-xs font-semibold rounded-lg bg-surface-container-lowest shadow-sm text-on-surface">Sign Up</NuxtLink>
          </div>

          <!-- Registration Form -->
          <form class="space-y-2" @submit.prevent="submitForm">
            <div>
              <label class="block text-[10px] font-semibold text-on-surface mb-1" for="name">Nom Complet <span class="text-brand-teal">*</span></label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </span>
                <input class="block w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent transition-all text-xs" id="name" placeholder="Entrez votre nom complet" type="text" required />
              </div>
            </div>

            <div>
              <label class="block text-[10px] font-semibold text-on-surface mb-1" for="email">Adresse Email <span class="text-brand-teal">*</span></label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </span>
                <input class="block w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent transition-all text-xs" id="email" placeholder="Entrez votre adresse email" type="email" required />
              </div>
            </div>

            <div>
              <label class="block text-[10px] font-semibold text-on-surface mb-1" for="password">Mot de passe <span class="text-brand-teal">*</span></label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </span>
                <input class="block w-full pl-9 pr-9 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent transition-all text-xs" id="password" placeholder="Entrez votre mot de passe" type="password" required />
                <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" type="button">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
              </div>
            </div>

            <button class="w-full bg-brand-teal hover:bg-opacity-90 text-on-primary font-semibold text-xs py-2 rounded-lg transition-all shadow-md mt-1" type="submit">
              Créer un compte
            </button>
          </form>

          <!-- Divider -->
          <div class="relative my-2 text-center">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-outline-variant"></div>
            </div>
            <span class="relative bg-surface-container-lowest px-3 text-[9px] font-medium text-on-surface-variant">Ou</span>
          </div>

          <!-- Social Sign-up -->
          <div class="flex justify-center">
            <!-- Google -->
            <button class="w-full h-9 flex items-center justify-center gap-2 border border-outline-variant rounded-lg hover:bg-surface-container-low transition-all text-xs font-semibold text-on-surface">
              <img alt="Google" class="w-4 h-4" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAHP921nmhbTw8YEEsRKSZf4qY27BcmIWO6Wh4lR_yIyU1XtHBDdf8-JXNPaAs_j-6tiDlCxmrjFqvlTf4uS0fldkjEPrGrUStptteHkVJVb3keWL-lpn57fTrI0rqEkk8pEUHgDG-IZBVbKtVsIT76ppw0svDen9k7k2yIdqZC5PRbZTczHwJRIweJvDCsaOOdCaFWohIOauqwqhu7Zp4G_R1ug7pQnvm5Y4ZKFSBMUmTSzhXNi3-aLy_uQLsQNiq_WESDSGnKDWjE" />
              S'inscrire avec Google
            </button>
          </div>
          
          <!-- Footer Links -->
          <footer class="mt-2 text-[9px]">
            <p class="text-center text-gray-400">
              © 2024 Néo Start Technology. Tous droits réservés.
            </p>
          </footer>
        </div>
      </section>

      <!-- BEGIN: Right Column (Illustration Section) -->
      <section class="flex-1 relative overflow-hidden hidden md:block" data-purpose="visual-section">
        <!-- Background Image -->
        <img alt="Financial Application Illustration" class="absolute inset-0 w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBep7Jxa4xq8E_m2vnl9jVMiUd5LMKPWhwzqWgTfM6Cl9duz9OBrS5Y-YFzeRDmNsGXz3jak9zYKih3lUqcTj1lS1-icMxeU5mwC7mnwkmr_Y5vDw_r53Zw49Cugw1Lzml4HE04AhKp0KuQsEeBjO8MMqUZgx06xOJKFGNEJ4pj6mGAth2HdQDBhSohImqh9F-pUso4_B3Wl07xysVOLPHC0FBgAxU9f90LcWtT01zHMxtJ5-Wh5tOt6PwMNiU6tMLOa13wYEfEBwFXlZY" />
        <!-- Overlay for readability at bottom -->
        <div class="absolute inset-0 bg-black/20"></div>
        <!-- Content over illustration -->
        <div class="absolute bottom-0 left-0 right-0 p-12 text-center text-white z-10">
          <h3 class="text-2xl font-bold mb-4 px-10">A Unified Hub for Smarter Financial Decision-Making</h3>
          <p class="text-gray-300 text-sm max-w-md mx-auto leading-relaxed mb-10">
            Néo Start Technology empowers you with a unified financial command center—delivering deep insights and a 360° view of your entire economic world.
          </p>
          <!-- Carousel Indicators -->
          <div class="flex justify-center gap-2" data-purpose="carousel-indicators">
            <span class="h-1 w-16 bg-white rounded-full"></span>
            <span class="h-1 w-8 bg-white/30 rounded-full"></span>
            <span class="h-1 w-8 bg-white/30 rounded-full"></span>
            <span class="h-1 w-8 bg-white/30 rounded-full"></span>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
definePageMeta({ layout: false })
const submitForm = () => {
  console.log('Signup form submitted')
}
</script>
