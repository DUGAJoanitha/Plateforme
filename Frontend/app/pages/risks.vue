<template>
  <div class="p-6 md:p-8 space-y-8 antialiased">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900" style="text-wrap: balance;">Gestion des Risques</h1>
        <p class="text-sm text-gray-500 mt-1" style="text-wrap: pretty;">Identifiez et suivez les risques associés à vos projets et programmes.</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-dark-teal transition-colors active:scale-[0.96] shadow-sm min-h-[40px] min-w-[40px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Déclarer un Risque
      </button>
    </div>

    <!-- Risks Table / List -->
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-transparent">
      <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900" style="text-wrap: balance;">Registre des Risques</h2>
        <div class="flex gap-2">
          <button class="text-sm px-4 py-2 rounded-lg font-medium bg-gray-100 text-gray-900 active:scale-[0.96] transition-transform shadow-sm">Tous</button>
          <button class="text-sm px-4 py-2 rounded-lg font-medium text-gray-500 hover:bg-gray-50 active:scale-[0.96] transition-colors">Critiques</button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Risque</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Projet / Programme</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Probabilité</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Impact</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Criticité</th>
              <th class="px-6 py-4"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(risk, index) in risks" :key="risk.id" 
                class="hover:bg-gray-50 transition-colors"
                :style="{ animationDelay: `${index * 50}ms` }">
              <td class="px-6 py-4">
                <p class="text-sm font-bold text-gray-900" style="text-wrap: balance;">{{ risk.title }}</p>
                <p class="text-xs text-gray-500 mt-1" style="text-wrap: pretty;">{{ risk.description }}</p>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ risk.context }}</td>
              <td class="px-6 py-4 text-sm tabular-nums text-gray-900">{{ risk.probability }}%</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ risk.impact }}</td>
              <td class="px-6 py-4">
                <span :class="[
                  'text-xs font-bold px-3 py-1 rounded-lg tabular-nums',
                  risk.criticality === 'Élevée' ? 'bg-[#ba1a1a]/10 text-[#ba1a1a]' : 
                  risk.criticality === 'Moyenne' ? 'bg-orange-100 text-orange-800' : 
                  'bg-brand-teal/10 text-brand-teal'
                ]">
                  {{ risk.criticality }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <button class="text-gray-400 hover:text-gray-900 transition-colors active:scale-[0.96] min-w-[40px] min-h-[40px] flex items-center justify-center">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const risks = [
  { id: 1, title: 'Retard de livraison matériel', description: 'Les équipements solaires sont bloqués à la douane.', context: 'Projet Électrification', probability: 80, impact: 'Majeur', criticality: 'Élevée' },
  { id: 2, title: 'Dépassement de budget', description: 'Coût des matériaux de construction en hausse.', context: 'Programme Éducation', probability: 45, impact: 'Modéré', criticality: 'Moyenne' },
  { id: 3, title: 'Désistement partenaire local', description: 'L\'ONG partenaire risque de se retirer suite à des restructurations.', context: 'Projet Eau Potable', probability: 15, impact: 'Critique', criticality: 'Faible' },
  { id: 4, title: 'Intempéries saisonnières', description: 'Les fortes pluies pourraient retarder les travaux de voirie.', context: 'Programme Rural', probability: 60, impact: 'Modéré', criticality: 'Moyenne' },
]
</script>

<style scoped>
@keyframes fadeInRow {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

tbody tr {
  animation: fadeInRow 0.4s cubic-bezier(0.2, 0, 0, 1) both;
}
</style>
