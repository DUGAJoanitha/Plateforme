<template>
  <div class="flex h-screen w-screen overflow-hidden bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col shadow-sm flex-shrink-0">
      <!-- Logo -->
      <div class="px-6 py-5 border-b border-gray-100">
        <img src="/logo.png" alt="Neo Start Technology" class="h-10 object-contain" />
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <NuxtLink to="/dashboard" class="nav-item" :class="{ active: $route.path === '/dashboard' }">
          <LucideIcon name="LayoutDashboard" class="w-5 h-5 flex-shrink-0" />
          <span>Dashboard</span>
        </NuxtLink>

        <NuxtLink to="/projects" class="nav-item" :class="{ active: $route.path.startsWith('/projects') }">
          <LucideIcon name="Folder" class="w-5 h-5 flex-shrink-0" />
          <span>Projets</span>
        </NuxtLink>

        <NuxtLink to="/activities" class="nav-item" :class="{ active: $route.path.startsWith('/activities') }">
          <LucideIcon name="CheckSquare" class="w-5 h-5 flex-shrink-0" />
          <span>Activités</span>
        </NuxtLink>

        <NuxtLink to="/finance" class="nav-item" :class="{ active: $route.path.startsWith('/finance') }">
          <LucideIcon name="CreditCard" class="w-5 h-5 flex-shrink-0" />
          <span>Finance</span>
        </NuxtLink>

        <NuxtLink to="/kpis" class="nav-item" :class="{ active: $route.path.startsWith('/kpis') }">
          <LucideIcon name="BarChart3" class="w-5 h-5 flex-shrink-0" />
          <span>KPIs</span>
        </NuxtLink>

        <NuxtLink to="/field" class="nav-item" :class="{ active: $route.path.startsWith('/field') }">
          <LucideIcon name="Map" class="w-5 h-5 flex-shrink-0" />
          <span>Terrain</span>
        </NuxtLink>
        
        <NuxtLink to="/settings" class="nav-item" :class="{ active: $route.path.startsWith('/settings') }">
          <LucideIcon name="Settings" class="w-5 h-5 flex-shrink-0" />
          <span>Paramètres</span>
        </NuxtLink>
      </nav>

      <!-- Bottom section: user profile -->
      <div class="px-4 py-4 border-t border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-full bg-[#007a82] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
            AR
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate">Alex Rivera</p>
            <p class="text-[10px] text-gray-400 truncate">alex.rivera@neostart.tech</p>
          </div>
          <button @click="logout" class="text-gray-400 hover:text-red-500 transition-colors" title="Déconnexion">
            <LucideIcon name="LogOut" class="w-4 h-4" />
          </button>
        </div>
      </div>
    </aside>

    <!-- Main content area -->
    <main class="flex-1 overflow-y-auto">
      <slot />
    </main>
  </div>
</template>

<script setup>
const router = useRouter()
const logout = () => {
  router.push('/login')
}
</script>

<style scoped>
.nav-item {
  @apply flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-800 transition-all cursor-pointer;
}
.nav-item.active {
  @apply bg-[#007a82]/10 text-[#007a82] font-semibold;
}
</style>
