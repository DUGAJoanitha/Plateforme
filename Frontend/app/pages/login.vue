<template>
  <div class="bg-gray-100 h-screen w-screen overflow-hidden flex items-center justify-center p-4 font-display">
    <!-- BEGIN: Main Login Container -->
    <main class="bg-white w-full max-w-[800px] h-full max-h-[550px] rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col md:flex-row" data-purpose="login-card">
      <!-- BEGIN: Left Column (Form Section) -->
      <section class="flex-1 p-6 md:p-8 flex flex-col justify-center gap-4" data-purpose="form-section">
        <div data-purpose="brand-logo" class="mb-2">
          <img src="/logo.png" alt="Neo Start Technology" class="h-12 object-contain" />
        </div>
        
        <!-- Welcome Message -->
        <div>
          <h2 class="text-lg font-bold text-gray-900 mb-1">Welcome to Néo Start Technology</h2>
        </div>
        
        <!-- Sign In / Sign Up Tabs -->
        <div class="flex bg-gray-50 p-1 rounded-xl" data-purpose="auth-tabs">
          <NuxtLink to="/login" class="flex-1 py-1.5 text-center text-xs font-semibold rounded-lg bg-white shadow-sm text-gray-800">Sign In</NuxtLink>
          <NuxtLink to="/signup" class="flex-1 py-1.5 text-center text-xs font-semibold rounded-lg text-gray-400 hover:text-gray-600 transition-colors">Sign Up</NuxtLink>
        </div>
        
        <!-- Login Form -->
        <form action="#" class="space-y-3" data-purpose="login-form" @submit.prevent="submitForm">
          <!-- Email Field -->
          <div>
            <label class="block text-[10px] font-semibold text-gray-700 mb-1" for="email">Email Address <span class="text-blue-500">*</span></label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
              </span>
              <input class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent transition-all" id="email" name="email" placeholder="Enter your email address" required type="email" />
            </div>
          </div>
          
          <!-- Password Field -->
          <div>
            <label class="block text-[10px] font-semibold text-gray-700 mb-1" for="password">Password <span class="text-blue-500">*</span></label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
              </span>
              <input class="block w-full pl-9 pr-9 py-2 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-teal focus:border-transparent transition-all" id="password" name="password" placeholder="Enter your password" required type="password" />
              <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" type="button">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                  <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Sign In Button -->
          <button class="w-full bg-brand-teal hover:bg-opacity-90 text-white font-semibold py-2 rounded-xl transition-all shadow-md mt-1 text-sm" type="submit">
            Sign In
          </button>
        </form>
        
        <!-- Social Login -->
        <div data-purpose="social-login">
          <div class="relative flex items-center justify-center mb-3">
            <div class="border-t border-gray-200 w-full absolute"></div>
            <span class="bg-white px-4 text-[10px] font-medium text-gray-400 relative z-10">Ou</span>
          </div>
          <div class="flex justify-center">
            <!-- Google -->
            <button class="w-full h-9 flex items-center justify-center gap-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-xs font-semibold text-gray-700">
              <img alt="Google" class="w-4 h-4" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBFhslwfMNoh7tvN3UqsNJipgs7Kc8oWI29zPkKi7M8S4Di36_giI5iImt4N6RAgyqF0v512LtUk5PXEwtuBm6CBaoGpOHrOgZeVrGx85b1AWwezsDwoKcjzlBZmAjs4pmaVqxA7TOt3Q1rBA28sgTSyJCyNQargB1mtspDoQg2IgSnciyy7KjOlDukCjgs69KypnJr6BnVWMQq9SjsjjF7DUn84lA_2rsWqLW7CJKKdC8CHYcqD5Ap7LoRIcE6pyAWj5bh_e_SiHGV" />
              Se connecter avec Google
            </button>
          </div>
        </div>
        
        <!-- Footer Links -->
        <footer class="text-center text-[9px] text-gray-400" data-purpose="form-footer">
          <p class="mb-1">© 2024 Néo Start Technology. All rights reserved.</p>
          <div class="flex justify-center gap-3">
            <a class="hover:underline" href="#">Terms & Conditions</a>
            <span class="text-gray-300">|</span>
            <a class="hover:underline" href="#">Privacy Policy</a>
          </div>
        </footer>
      </section>
      <!-- END: Left Column -->
      
      <!-- BEGIN: Right Column (Illustration Section) -->
      <section class="flex-1 relative overflow-hidden hidden md:block" data-purpose="visual-section">
        <!-- Background Image -->
        <img alt="Financial Application Illustration" class="absolute inset-0 w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBep7Jxa4xq8E_m2vnl9jVMiUd5LMKPWhwzqWgTfM6Cl9duz9OBrS5Y-YFzeRDmNsGXz3jak9zYKih3lUqcTj1lS1-icMxeU5mwC7mnwkmr_Y5vDw_r53Zw49Cugw1Lzml4HE04AhKp0KuQsEeBjO8MMqUZgx06xOJKFGNEJ4pj6mGAth2HdQDBhSohImqh9F-pUso4_B3Wl07xysVOLPHC0FBgAxU9f90LcWtT01zHMxtJ5-Wh5tOt6PwMNiU6tMLOa13wYEfEBwFXlZY" />
        <!-- Overlay for readability at bottom -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#022c30]/90 via-transparent to-transparent"></div>
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
      <!-- END: Right Column -->
    </main>
    <!-- END: Main Login Container -->
  </div>
</template>

<script setup>
definePageMeta({ layout: false })
const router = useRouter()
const submitForm = () => {
  router.push('/dashboard')
}
</script>
