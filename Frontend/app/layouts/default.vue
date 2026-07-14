<template>
  <div class="flex h-screen w-screen overflow-hidden" style="background:#F8F9FF">
    <!-- ── Sidebar ──────────────────────────────────────── -->
    <aside class="sidebar-root w-64 flex flex-col flex-shrink-0">

      <!-- Logo zone -->
      <div class="px-6 py-5 border-b" style="border-color:rgba(142,242,252,0.12)">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
               style="background:linear-gradient(135deg,#22C7D6,#8EF2FC)">
            <svg class="w-4 h-4" fill="none" stroke="#083C44" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <span class="font-display font-bold text-white text-sm tracking-tight">NéoStart</span>
        </div>
      </div>

      <!-- Nav label -->
      <div class="px-5 pt-5 pb-2">
        <span class="font-mono-label text-label-sm uppercase tracking-wider"
              style="color:rgba(142,242,252,0.45)">Navigation</span>
      </div>

      <!-- Navigation links -->
      <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto">
        <NuxtLink to="/dashboard" class="nav-link" :class="{ 'nav-link--active': $route.path === '/dashboard' }">
          <LucideIcon name="LayoutDashboard" class="w-4 h-4 flex-shrink-0" />
          <span>Dashboard</span>
        </NuxtLink>

        <NuxtLink to="/projects" class="nav-link" :class="{ 'nav-link--active': $route.path.startsWith('/projects') }">
          <LucideIcon name="Folder" class="w-4 h-4 flex-shrink-0" />
          <span>Projets</span>
        </NuxtLink>

        <NuxtLink to="/activities" class="nav-link" :class="{ 'nav-link--active': $route.path.startsWith('/activities') }">
          <LucideIcon name="CheckSquare" class="w-4 h-4 flex-shrink-0" />
          <span>Activités</span>
        </NuxtLink>

        <NuxtLink to="/finance" class="nav-link" :class="{ 'nav-link--active': $route.path.startsWith('/finance') }">
          <LucideIcon name="CreditCard" class="w-4 h-4 flex-shrink-0" />
          <span>Finance</span>
        </NuxtLink>

        <NuxtLink to="/kpis" class="nav-link" :class="{ 'nav-link--active': $route.path.startsWith('/kpis') }">
          <LucideIcon name="BarChart3" class="w-4 h-4 flex-shrink-0" />
          <span>KPIs</span>
        </NuxtLink>

        <NuxtLink to="/field" class="nav-link" :class="{ 'nav-link--active': $route.path.startsWith('/field') }">
          <LucideIcon name="Map" class="w-4 h-4 flex-shrink-0" />
          <span>Terrain</span>
        </NuxtLink>

        <NuxtLink to="/risks" class="nav-link" :class="{ 'nav-link--active': $route.path.startsWith('/risks') }">
          <LucideIcon name="ShieldAlert" class="w-4 h-4 flex-shrink-0" />
          <span>Risques</span>
        </NuxtLink>

        <NuxtLink to="/settings" class="nav-link" :class="{ 'nav-link--active': $route.path.startsWith('/settings') }">
          <LucideIcon name="Settings" class="w-4 h-4 flex-shrink-0" />
          <span>Paramètres</span>
        </NuxtLink>
      </nav>

      <!-- User profile -->
      <div class="px-4 py-4 border-t" style="border-color:rgba(142,242,252,0.12)">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 font-mono-label text-xs font-semibold"
               style="background:linear-gradient(135deg,#22C7D6,#8EF2FC);color:#083C44">
            AR
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-white truncate">Alex Rivera</p>
            <p class="text-[10px] truncate" style="color:rgba(142,242,252,0.5)">alex@neostart.tech</p>
          </div>
          <button @click="logout" class="transition-colors" style="color:rgba(255,255,255,0.35)"
                  @mouseenter="e => e.target.style.color='#DC2626'"
                  @mouseleave="e => e.target.style.color='rgba(255,255,255,0.35)'">
            <LucideIcon name="LogOut" class="w-4 h-4" />
          </button>
        </div>
      </div>
    </aside>

    <!-- ── Main content ─────────────────────────────────── -->
    <main class="flex-1 overflow-y-auto">
      <slot />
    </main>
  </div>
</template>

<script setup>
const router = useRouter()
const logout = () => router.push('/login')
</script>

<style scoped>
.sidebar-root {
  background: #083C44;
  box-shadow: 4px 0 32px rgba(8,60,68,0.3);
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border-radius: 0 9999px 9999px 0; /* partial pill — right rounded */
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: rgba(217, 225, 231, 0.7);
  text-decoration: none;
  transition: all 0.18s ease;
  position: relative;
  margin-right: 8px;
  border-left: 2px solid transparent;
}

.nav-link:hover {
  color: #8EF2FC;
  background: rgba(142, 242, 252, 0.07);
  border-left-color: rgba(34,199,214,0.4);
}

.nav-link--active {
  color: #22C7D6;
  background: rgba(34, 199, 214, 0.12);
  border-left-color: #22C7D6;
  font-weight: 600;
  box-shadow: inset 0 0 12px rgba(34,199,214,0.08), 0 0 8px rgba(34,199,214,0.15);
}

.nav-link--active :deep(svg) {
  color: #22C7D6;
  filter: drop-shadow(0 0 4px rgba(34,199,214,0.6));
}
</style>
