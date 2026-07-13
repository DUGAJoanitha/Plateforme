<template>
  <div class="p-6 md:p-8 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">KPIs</h1>
        <p class="text-sm text-gray-500 mt-0.5">Indicateurs clés de performance de vos projets</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-opacity-90 transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Ajouter KPI
      </button>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      <div v-for="kpi in kpis" :key="kpi.id" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div>
            <p class="text-sm font-bold text-gray-800">{{ kpi.name }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ kpi.project }}</p>
          </div>
          <span :class="`text-xs font-semibold px-2 py-1 rounded-full ${kpi.perfClass}`">{{ kpi.perf }}</span>
        </div>

        <!-- Values -->
        <div class="flex items-end gap-4 mb-4">
          <div>
            <p class="text-3xl font-bold text-gray-900">{{ kpi.current }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Valeur actuelle</p>
          </div>
          <div class="text-right ml-auto">
            <p class="text-lg font-semibold text-gray-500">{{ kpi.target }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Objectif</p>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="mb-3">
          <div class="flex justify-between text-xs mb-1">
            <span class="text-gray-500">Avancement</span>
            <span class="font-semibold" :class="kpi.pct >= 100 ? 'text-green-600' : kpi.pct >= 70 ? 'text-blue-600' : 'text-orange-500'">{{ Math.min(kpi.pct, 100) }}%</span>
          </div>
          <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div :class="`h-2.5 rounded-full transition-all ${kpi.barColor}`" :style="`width: ${Math.min(kpi.pct, 100)}%`"></div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
          <span class="text-[10px] text-gray-400">Dernière mesure : {{ kpi.lastMeasure }}</span>
          <button class="text-xs font-semibold text-brand-teal hover:underline">+ Mesure</button>
        </div>
      </div>
    </div>

    <!-- Performance table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Historique des mesures récentes</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Indicateur</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Valeur</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Période</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Saisie par</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Évolution</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="h in history" :key="h.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3 text-sm text-gray-800 font-medium">{{ h.name }}</td>
              <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ h.value }}</td>
              <td class="px-5 py-3 text-xs text-gray-500">{{ h.period }}</td>
              <td class="px-5 py-3 text-xs text-gray-600">{{ h.by }}</td>
              <td class="px-5 py-3">
                <span :class="`text-xs font-semibold ${h.trend > 0 ? 'text-green-600' : 'text-red-500'}`">
                  {{ h.trend > 0 ? '↑' : '↓' }} {{ Math.abs(h.trend) }}%
                </span>
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

const kpis = [
  { id:1, name: 'Personnes avec accès eau potable', project: 'Eau Potable - Nord', current: '12 450', target: '18 000', pct: 69, perf: 'Bon', perfClass: 'bg-blue-100 text-blue-700', barColor: 'bg-blue-500', lastMeasure: '08 jan 2026' },
  { id:2, name: 'Taux d\'accès à l\'eau sécurisée', project: 'Eau Potable - Nord', current: '78%', target: '95%', pct: 82, perf: 'Bon', perfClass: 'bg-blue-100 text-blue-700', barColor: 'bg-blue-500', lastMeasure: '08 jan 2026' },
  { id:3, name: 'Villages électrifiés', project: 'Électrification - Sud', current: '23', target: '50', pct: 46, perf: 'Faible', perfClass: 'bg-orange-100 text-orange-700', barColor: 'bg-orange-400', lastMeasure: '06 jan 2026' },
  { id:4, name: 'Foyers raccordés au réseau', project: 'Électrification - Sud', current: '1 840', target: '4 000', pct: 46, perf: 'Faible', perfClass: 'bg-orange-100 text-orange-700', barColor: 'bg-orange-400', lastMeasure: '06 jan 2026' },
  { id:5, name: 'Jeunes formés', project: 'Formation Jeunes', current: '340', target: '500', pct: 68, perf: 'Moyen', perfClass: 'bg-yellow-100 text-yellow-700', barColor: 'bg-yellow-400', lastMeasure: '07 jan 2026' },
  { id:6, name: 'Taux d\'insertion professionnelle', project: 'Formation Jeunes', current: '52%', target: '70%', pct: 74, perf: 'Bon', perfClass: 'bg-blue-100 text-blue-700', barColor: 'bg-blue-500', lastMeasure: '07 jan 2026' },
  { id:7, name: 'Familles bénéficiaires semences', project: 'Agriculture - Plateaux', current: '480', target: '800', pct: 60, perf: 'Moyen', perfClass: 'bg-yellow-100 text-yellow-700', barColor: 'bg-teal-500', lastMeasure: '05 jan 2026' },
  { id:8, name: 'Rendement agricole moyen', project: 'Agriculture - Plateaux', current: '2.1t/ha', target: '3t/ha', pct: 70, perf: 'Bon', perfClass: 'bg-blue-100 text-blue-700', barColor: 'bg-teal-500', lastMeasure: '05 jan 2026' },
  { id:9, name: 'Consultations enregistrées', project: 'Santé - Lomé Est', current: '8 420', target: '8 000', pct: 105, perf: 'Excellent', perfClass: 'bg-green-100 text-green-700', barColor: 'bg-green-500', lastMeasure: '04 jan 2026' },
]

const history = [
  { id:1, name: 'Personnes avec accès eau', value: '12 450', period: 'Janvier 2026', by: 'Kofi A.', trend: 12 },
  { id:2, name: 'Villages électrifiés', value: '23', period: 'Janvier 2026', by: 'Yao M.', trend: 5 },
  { id:3, name: 'Jeunes formés', value: '340', period: 'Décembre 2025', by: 'Edem K.', trend: 15 },
  { id:4, name: 'Familles bénéficiaires', value: '480', period: 'Décembre 2025', by: 'Paulo A.', trend: -3 },
  { id:5, name: 'Consultations enregistrées', value: '8 420', period: 'Décembre 2025', by: 'Sika T.', trend: 8 },
]
</script>
