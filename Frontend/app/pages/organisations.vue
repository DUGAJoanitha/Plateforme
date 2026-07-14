<template>
  <div class="p-6 md:p-8 space-y-8 antialiased">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900" style="text-wrap: balance;">Organisation</h1>
        <p class="text-sm text-gray-500 mt-1" style="text-wrap: pretty;">Gérez la structure, les départements et les équipes de l'organisation.</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-dark-teal transition-colors active:scale-[0.96] shadow-sm min-h-[40px] min-w-[40px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouvelle Équipe
      </button>
    </div>

    <!-- Org Structure -->
    <div class="space-y-6">
      <div v-for="(dept, dIdx) in departments" :key="dept.id" 
           class="bg-white rounded-3xl p-6 shadow-sm border border-transparent"
           :style="{ animationDelay: `${dIdx * 150}ms` }">
        
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
          <svg class="w-8 h-8 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <div>
            <h2 class="text-xl font-bold text-gray-900" style="text-wrap: balance;">{{ dept.name }}</h2>
            <p class="text-sm text-gray-500 tabular-nums">{{ dept.totalMembers }} membres au total</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(team, tIdx) in dept.teams" :key="team.id" 
               class="bg-surface-bright rounded-2xl p-5 shadow-sm border border-transparent hover:shadow-md transition-shadow cursor-pointer active:scale-[0.98]">
            <div class="flex justify-between items-start mb-4">
              <h3 class="font-bold text-gray-900" style="text-wrap: balance;">{{ team.name }}</h3>
              <button class="text-gray-400 hover:text-gray-900 active:scale-[0.96] transition-transform min-w-[40px] min-h-[40px] flex items-center justify-center -mr-2 -mt-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
              </button>
            </div>
            
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Responsable: <span class="font-medium text-gray-900">{{ team.lead }}</span>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200/60 pt-4">
              <span class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Effectif</span>
              <span class="text-sm font-bold text-gray-900 bg-gray-200/50 px-2 py-1 rounded-md tabular-nums">{{ team.members }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const departments = [
  {
    id: 1, name: 'Direction des Opérations', totalMembers: 45,
    teams: [
      { id: 101, name: 'Projets Terrain', lead: 'Amina K.', members: 24 },
      { id: 102, name: 'Logistique', lead: 'Jean P.', members: 12 },
      { id: 103, name: 'Suivi & Évaluation', lead: 'Marie D.', members: 9 },
    ]
  },
  {
    id: 2, name: 'Administration & Finances', totalMembers: 18,
    teams: [
      { id: 201, name: 'Comptabilité', lead: 'Kofi S.', members: 8 },
      { id: 202, name: 'Ressources Humaines', lead: 'Sarah T.', members: 6 },
      { id: 203, name: 'Achats', lead: 'Marc L.', members: 4 },
    ]
  }
]
</script>

<style scoped>
@keyframes slideInRight {
  from { opacity: 0; transform: translateX(20px); }
  to { opacity: 1; transform: translateX(0); }
}

.space-y-6 > div {
  animation: slideInRight 0.5s cubic-bezier(0.2, 0, 0, 1) both;
}
</style>
