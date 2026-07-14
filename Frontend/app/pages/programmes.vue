<template>
  <div class="p-6 md:p-8 space-y-8 antialiased">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900" style="text-wrap: balance;">Programmes</h1>
        <p class="text-sm text-gray-500 mt-1" style="text-wrap: pretty;">Gérez les programmes stratégiques et suivez leur avancement global.</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-dark-teal transition-colors active:scale-[0.96] shadow-sm min-h-[40px] min-w-[40px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouveau Programme
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="(stat, index) in stats" :key="index" class="bg-white rounded-3xl p-6 shadow-sm border border-transparent">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 mb-2">{{ stat.label }}</p>
            <p class="text-4xl font-bold text-gray-900 tabular-nums">{{ stat.value }}</p>
          </div>
          <!-- Icon without background -->
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="stat.iconPath"></svg>
        </div>
      </div>
    </div>

    <!-- Programmes List -->
    <div class="space-y-6">
      <h2 class="text-xl font-semibold text-gray-900" style="text-wrap: balance;">Programmes Actifs</h2>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div v-for="(prog, index) in programmes" :key="prog.id" 
             class="bg-white rounded-3xl p-6 shadow-md border border-transparent flex flex-col gap-6"
             :style="{ animationDelay: `${index * 100}ms` }">
          
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900" style="text-wrap: balance;">{{ prog.name }}</h3>
              <p class="text-sm text-gray-500 mt-2" style="text-wrap: pretty;">{{ prog.description }}</p>
            </div>
            <button class="text-gray-400 hover:text-gray-900 transition-colors active:scale-[0.96] min-w-[40px] min-h-[40px] flex items-center justify-center -mr-2 -mt-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
              </svg>
            </button>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-xs text-gray-500 mb-1">Budget Alloué</p>
              <p class="text-lg font-bold text-gray-900 tabular-nums">{{ prog.budget }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
              <p class="text-xs text-gray-500 mb-1">Projets Inclus</p>
              <p class="text-lg font-bold text-gray-900 tabular-nums">{{ prog.projectCount }}</p>
            </div>
          </div>

          <div class="flex items-center justify-between border-t border-gray-100 pt-4">
            <div class="flex items-center gap-2 text-gray-500">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="text-sm font-medium tabular-nums">{{ prog.date }}</span>
            </div>
            <span class="text-sm font-bold text-brand-teal px-3 py-1 rounded-lg bg-brand-teal/10 tabular-nums">{{ prog.completion }}%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const stats = [
  { 
    label: 'Programmes Actifs', 
    value: '12', 
    iconPath: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />'
  },
  { 
    label: 'Budget Total (k€)', 
    value: '4 250', 
    iconPath: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
  },
  { 
    label: 'Projets Associés', 
    value: '45', 
    iconPath: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />'
  }
]

const programmes = [
  { id: 1, name: 'Développement Rural Phase 1', description: 'Amélioration des infrastructures rurales, incluant l\'accès à l\'eau potable et l\'électrification des villages isolés.', budget: '1 200 k€', projectCount: 14, date: 'Jusqu\'au 31 Déc 2026', completion: 65 },
  { id: 2, name: 'Éducation pour Tous', description: 'Construction de centres de formation et distribution de matériel éducatif dans les régions défavorisées.', budget: '850 k€', projectCount: 8, date: 'Jusqu\'au 15 Sep 2026', completion: 40 },
  { id: 3, name: 'Modernisation Santé', description: 'Équipement des centres de santé régionaux et formation du personnel médical aux nouvelles technologies.', budget: '1 500 k€', projectCount: 12, date: 'Jusqu\'au 01 Mar 2027', completion: 15 },
  { id: 4, name: 'Soutien Agriculture Locale', description: 'Programme d\'aide aux agriculteurs avec des semences améliorées et des techniques d\'irrigation modernes.', budget: '700 k€', projectCount: 11, date: 'Jusqu\'au 30 Juin 2026', completion: 82 },
]
</script>

<style scoped>
@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

.grid > div {
  animation: slideUpFade 0.6s cubic-bezier(0.2, 0, 0, 1) both;
}
</style>
