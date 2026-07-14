<template>
  <div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">KPIs</h1>
        <p class="page-subtitle">Indicateurs clés de performance de vos projets</p>
      </div>
      <button class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Ajouter KPI
      </button>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      <div v-for="kpi in kpis" :key="kpi.id"
           class="card hover:shadow-card-hover transition-all duration-200">
        <div class="flex items-start justify-between mb-4">
          <div>
            <p class="font-display font-bold text-sm" style="color:#083C44">{{ kpi.name }}</p>
            <p class="font-mono-label text-xs mt-0.5" style="color:#6E797A">{{ kpi.project }}</p>
          </div>
          <span class="badge" :class="kpi.perfClass">{{ kpi.perf }}</span>
        </div>

        <div class="flex items-end gap-4 mb-4">
          <div>
            <p class="text-3xl font-bold font-display tabular-nums" style="color:#083C44">{{ kpi.current }}</p>
            <p class="font-mono-label text-xs mt-0.5" style="color:#6E797A">Valeur actuelle</p>
          </div>
          <div class="text-right ml-auto">
            <p class="text-lg font-semibold font-display" style="color:#6E797A">{{ kpi.target }}</p>
            <p class="font-mono-label text-xs mt-0.5" style="color:#6E797A">Objectif</p>
          </div>
        </div>

        <!-- Progress -->
        <div class="mb-3">
          <div class="flex justify-between text-xs mb-1">
            <span style="color:#6E797A">Avancement</span>
            <span class="font-mono-label font-semibold" style="color:#22C7D6">{{ Math.min(kpi.pct, 100) }}%</span>
          </div>
          <div class="progress-track h-2.5">
            <div class="progress-fill h-2.5" :style="`width:${Math.min(kpi.pct,100)}%`"></div>
          </div>
        </div>

        <div class="flex items-center justify-between pt-3" style="border-top:1px solid #D9E1E7">
          <span class="font-mono-label text-xs" style="color:#6E797A">Dernière mesure : {{ kpi.lastMeasure }}</span>
          <button class="font-mono-label text-xs font-semibold transition-colors"
                  style="color:#22C7D6">+ Mesure</button>
        </div>
      </div>
    </div>

    <!-- History Table -->
    <div class="table-container">
      <div class="px-5 py-3.5" style="border-bottom:1px solid #D9E1E7">
        <h2 class="font-display font-semibold text-sm" style="color:#083C44">Historique des mesures récentes</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr style="background:#F8F9FF;border-bottom:1px solid #D9E1E7">
              <th class="table-header text-left px-5 py-3">Indicateur</th>
              <th class="table-header text-left px-5 py-3">Valeur</th>
              <th class="table-header text-left px-5 py-3">Période</th>
              <th class="table-header text-left px-5 py-3">Saisie par</th>
              <th class="table-header text-left px-5 py-3">Évolution</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="h in history" :key="h.id" class="table-row">
              <td class="px-5 py-3 text-sm font-medium" style="color:#121C2A">{{ h.name }}</td>
              <td class="px-5 py-3 font-display font-bold text-sm tabular-nums" style="color:#083C44">{{ h.value }}</td>
              <td class="px-5 py-3 font-mono-label text-xs" style="color:#6E797A">{{ h.period }}</td>
              <td class="px-5 py-3 text-xs" style="color:#3E494A">{{ h.by }}</td>
              <td class="px-5 py-3">
                <span class="font-mono-label text-xs font-semibold"
                      :style="h.trend > 0 ? 'color:#16A34A' : 'color:#DC2626'">
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
  { id:1, name:'Personnes avec accès eau potable', project:'Eau Potable - Nord', current:'12 450', target:'18 000', pct:69, perf:'Bon', perfClass:'badge-brand', lastMeasure:'08 jan 2026' },
  { id:2, name:'Taux d\'accès à l\'eau sécurisée', project:'Eau Potable - Nord', current:'78%', target:'95%', pct:82, perf:'Bon', perfClass:'badge-brand', lastMeasure:'08 jan 2026' },
  { id:3, name:'Villages électrifiés', project:'Électrification - Sud', current:'23', target:'50', pct:46, perf:'Faible', perfClass:'badge-warning', lastMeasure:'06 jan 2026' },
  { id:4, name:'Foyers raccordés au réseau', project:'Électrification - Sud', current:'1 840', target:'4 000', pct:46, perf:'Faible', perfClass:'badge-warning', lastMeasure:'06 jan 2026' },
  { id:5, name:'Jeunes formés', project:'Formation Jeunes', current:'340', target:'500', pct:68, perf:'Moyen', perfClass:'badge-neutral', lastMeasure:'07 jan 2026' },
  { id:6, name:'Taux d\'insertion professionnelle', project:'Formation Jeunes', current:'52%', target:'70%', pct:74, perf:'Bon', perfClass:'badge-brand', lastMeasure:'07 jan 2026' },
  { id:7, name:'Familles bénéficiaires semences', project:'Agriculture - Plateaux', current:'480', target:'800', pct:60, perf:'Moyen', perfClass:'badge-neutral', lastMeasure:'05 jan 2026' },
  { id:8, name:'Rendement agricole moyen', project:'Agriculture - Plateaux', current:'2.1t/ha', target:'3t/ha', pct:70, perf:'Bon', perfClass:'badge-brand', lastMeasure:'05 jan 2026' },
  { id:9, name:'Consultations enregistrées', project:'Santé - Lomé Est', current:'8 420', target:'8 000', pct:105, perf:'Excellent', perfClass:'badge-success', lastMeasure:'04 jan 2026' },
]

const history = [
  { id:1, name:'Personnes avec accès eau', value:'12 450', period:'Janvier 2026', by:'Kofi A.', trend:12 },
  { id:2, name:'Villages électrifiés', value:'23', period:'Janvier 2026', by:'Yao M.', trend:5 },
  { id:3, name:'Jeunes formés', value:'340', period:'Décembre 2025', by:'Edem K.', trend:15 },
  { id:4, name:'Familles bénéficiaires', value:'480', period:'Décembre 2025', by:'Paulo A.', trend:-3 },
  { id:5, name:'Consultations enregistrées', value:'8 420', period:'Décembre 2025', by:'Sika T.', trend:8 },
]
</script>
