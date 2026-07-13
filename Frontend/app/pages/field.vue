<template>
  <div class="p-6 md:p-8 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Terrain</h1>
        <p class="text-sm text-gray-500 mt-0.5">Formulaires terrain et soumissions</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-opacity-90 transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouveau formulaire
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="s in stats" :key="s.label" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p :class="`text-2xl font-bold ${s.color}`">{{ s.value }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ s.label }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Forms list -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Formulaires actifs</h2>
        <div class="space-y-3">
          <div v-for="form in forms" :key="form.id" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors border border-gray-100">
            <div :class="`w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 ${form.iconBg}`">
              <span v-html="form.icon"></span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-800 truncate">{{ form.name }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ form.project }} · {{ form.submissions }} soumissions</p>
            </div>
            <span :class="`text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0 ${form.statusClass}`">{{ form.status }}</span>
          </div>
        </div>
      </div>

      <!-- Recent submissions -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Soumissions récentes</h2>
        <div class="space-y-3">
          <div v-for="sub in submissions" :key="sub.id" class="p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between mb-2">
              <div>
                <p class="text-xs font-semibold text-gray-800">{{ sub.form }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ sub.agent }} · {{ sub.location }}</p>
              </div>
              <span :class="`text-[10px] font-semibold px-2 py-1 rounded-full ${sub.statusClass}`">{{ sub.status }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-[10px] text-gray-400">{{ sub.date }}</span>
              <div class="flex gap-2">
                <button class="text-[10px] text-brand-teal font-semibold hover:underline">Voir</button>
                <button v-if="sub.status === 'En attente'" class="text-[10px] text-green-600 font-semibold hover:underline">Valider</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Map placeholder -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
      <h2 class="font-semibold text-gray-800 mb-4">Carte des interventions terrain</h2>
      <div class="w-full h-72 bg-gradient-to-br from-teal-50 to-blue-50 rounded-xl flex flex-col items-center justify-center border-2 border-dashed border-teal-200">
        <svg class="w-12 h-12 text-teal-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
        </svg>
        <p class="text-sm font-semibold text-gray-500">Carte interactive</p>
        <p class="text-xs text-gray-400 mt-1">47 points d'intervention géolocalisés</p>
        <p class="text-xs text-teal-500 mt-2 font-medium">→ Intégration carte disponible (Leaflet / Mapbox)</p>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const stats = [
  { label: 'Formulaires actifs', value: '8', color: 'text-gray-800' },
  { label: 'Soumissions ce mois', value: '124', color: 'text-blue-600' },
  { label: 'En attente validation', value: '11', color: 'text-orange-500' },
  { label: 'Validées', value: '113', color: 'text-green-600' },
]

const forms = [
  { id:1, name: 'Fiche de visite bénéficiaire', project: 'Eau Potable', submissions: 38, status: 'Actif', statusClass: 'bg-green-100 text-green-700', iconBg: 'bg-blue-50', icon: `<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>` },
  { id:2, name: 'Relevé consommation électrique', project: 'Électrification', submissions: 22, status: 'Actif', statusClass: 'bg-green-100 text-green-700', iconBg: 'bg-orange-50', icon: `<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>` },
  { id:3, name: 'Évaluation formation compétences', project: 'Formation', submissions: 45, status: 'Actif', statusClass: 'bg-green-100 text-green-700', iconBg: 'bg-purple-50', icon: `<svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>` },
  { id:4, name: 'Suivi rendement agricole', project: 'Agriculture', submissions: 19, status: 'Actif', statusClass: 'bg-green-100 text-green-700', iconBg: 'bg-teal-50', icon: `<svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945"/></svg>` },
]

const submissions = [
  { id:1, form: 'Fiche visite bénéficiaire', agent: 'Kofi A.', location: 'Village Kara Nord', date: '08 jan 2026 - 14h30', status: 'Validé', statusClass: 'bg-green-100 text-green-700' },
  { id:2, form: 'Relevé consommation électrique', agent: 'Yao M.', location: 'Quartier Agomé', date: '08 jan 2026 - 11h15', status: 'En attente', statusClass: 'bg-orange-100 text-orange-700' },
  { id:3, form: 'Fiche visite bénéficiaire', agent: 'Afi D.', location: 'Village Tcharé', date: '07 jan 2026 - 16h00', status: 'En attente', statusClass: 'bg-orange-100 text-orange-700' },
  { id:4, form: 'Suivi rendement agricole', agent: 'Paulo A.', location: 'Préfecture Plateaux', date: '07 jan 2026 - 09h45', status: 'Validé', statusClass: 'bg-green-100 text-green-700' },
  { id:5, form: 'Évaluation formation', agent: 'Edem K.', location: 'Centre Lomé', date: '06 jan 2026 - 15h20', status: 'Validé', statusClass: 'bg-green-100 text-green-700' },
]
</script>
