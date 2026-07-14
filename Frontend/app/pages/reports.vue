<template>
  <div class="p-6 md:p-8 space-y-8 antialiased">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900" style="text-wrap: balance;">Rapports</h1>
        <p class="text-sm text-gray-500 mt-1" style="text-wrap: pretty;">Générez et consultez les rapports d'avancement et d'analyse.</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-dark-teal transition-colors active:scale-[0.96] shadow-sm min-h-[40px] min-w-[40px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Générer un Rapport
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="(report, index) in reports" :key="report.id" 
           class="bg-white rounded-3xl p-6 shadow-sm border border-transparent flex flex-col gap-4"
           :style="{ animationDelay: `${index * 80}ms` }">
        <div class="flex justify-between items-start">
          <svg class="w-8 h-8 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-lg uppercase tracking-wide">{{ report.type }}</span>
        </div>
        
        <div>
          <h3 class="text-lg font-bold text-gray-900" style="text-wrap: balance;">{{ report.name }}</h3>
          <p class="text-sm text-gray-500 mt-1" style="text-wrap: pretty;">Généré le <span class="tabular-nums font-medium text-gray-900">{{ report.date }}</span></p>
        </div>

        <div class="mt-auto pt-4 flex gap-2">
          <button class="flex-1 bg-gray-50 text-gray-900 text-sm font-semibold py-2 rounded-lg hover:bg-gray-100 transition-colors active:scale-[0.96] shadow-sm min-h-[40px]">
            Consulter
          </button>
          <button class="bg-gray-50 text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors active:scale-[0.96] shadow-sm min-h-[40px] min-w-[40px] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const reports = [
  { id: 1, name: 'Bilan Trimestriel - Q1 2026', type: 'Financier', date: '31 Mar 2026' },
  { id: 2, name: 'Rapport d\'Impact Rural', type: 'Évaluation', date: '15 Fév 2026' },
  { id: 3, name: 'Analyse des Risques Globaux', type: 'Audit', date: '10 Fév 2026' },
  { id: 4, name: 'Statut des KPIs - Février', type: 'Performance', date: '01 Fév 2026' },
  { id: 5, name: 'Rapport Annuel 2025', type: 'Général', date: '15 Jan 2026' },
]
</script>

<style scoped>
@keyframes popIn {
  from { opacity: 0; transform: scale(0.96) translateY(8px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.grid > div {
  animation: popIn 0.5s cubic-bezier(0.2, 0, 0, 1) both;
}
</style>
