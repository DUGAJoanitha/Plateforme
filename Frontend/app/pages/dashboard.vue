<template>
  <div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
        <p class="text-sm text-gray-500 mt-0.5">Vue d'ensemble de vos projets et performances</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-xs text-gray-400">Mise à jour : {{ currentDate }}</span>
        <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-opacity-90 transition-all shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Nouveau projet
        </button>
      </div>
    </div>

    <!-- KPI Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="kpi in kpiCards" :key="kpi.title" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
          <div :class="`w-10 h-10 rounded-xl flex items-center justify-center ${kpi.iconBg}`">
            <span v-html="kpi.icon"></span>
          </div>
          <span :class="`text-xs font-semibold px-2 py-1 rounded-full ${kpi.badgeClass}`">{{ kpi.trend }}</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ kpi.value }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ kpi.title }}</p>
      </div>
    </div>

    <!-- Middle Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Projects list -->
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-gray-800">Projets récents</h2>
          <NuxtLink to="/projects" class="text-xs font-semibold text-brand-teal hover:underline">Voir tout</NuxtLink>
        </div>
        <div class="space-y-3">
          <div v-for="project in recentProjects" :key="project.name" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer">
            <div :class="`w-2 h-10 rounded-full flex-shrink-0 ${project.color}`"></div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-800 truncate">{{ project.name }}</p>
              <div class="flex items-center gap-2 mt-1">
                <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                  <div :class="`h-1.5 rounded-full ${project.barColor}`" :style="`width: ${project.progress}%`"></div>
                </div>
                <span class="text-xs text-gray-500 flex-shrink-0 tabular-nums">{{ project.progress }}%</span>
              </div>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="text-xs font-semibold" :class="project.statusClass">{{ project.status }}</p>
              <p class="text-[10px] text-gray-400">{{ project.deadline }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Activity feed -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Activité récente</h2>
        <div class="space-y-4">
          <div v-for="activity in activityFeed" :key="activity.time" class="flex gap-3">
            <div :class="`w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center ${activity.iconBg}`">
              <span v-html="activity.icon" class="text-white"></span>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-700">{{ activity.text }}</p>
              <p class="text-[10px] text-gray-400 mt-0.5">{{ activity.time }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Budget Overview -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Vue budgétaire</h2>
        <div class="space-y-3">
          <div v-for="b in budgetItems" :key="b.label">
            <div class="flex justify-between text-xs mb-1">
              <span class="text-gray-600">{{ b.label }}</span>
              <span class="font-semibold text-gray-800 tabular-nums">{{ b.value }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
              <div :class="`h-2 rounded-full ${b.color}`" :style="`width: ${b.pct}%`"></div>
            </div>
          </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
          <span class="text-xs text-gray-500">Solde disponible</span>
          <span class="text-sm font-bold text-brand-teal tabular-nums">18 500 000 FCFA</span>
        </div>
      </div>

      <!-- KPIs performance -->
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-gray-800">Performance des KPIs</h2>
          <NuxtLink to="/kpis" class="text-xs font-semibold text-brand-teal hover:underline">Détails</NuxtLink>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div v-for="kpi in kpiPerformance" :key="kpi.name" class="p-4 rounded-xl bg-gray-50 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
              <p class="text-xs font-semibold text-gray-700">{{ kpi.name }}</p>
              <span :class="`text-xs font-bold ${kpi.color}`">{{ kpi.value }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
              <div :class="`h-1.5 rounded-full ${kpi.barColor}`" :style="`width: ${kpi.pct}%`"></div>
            </div>
            <p class="text-[10px] text-gray-400 mt-1.5">Objectif : {{ kpi.target }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const currentDate = new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })

const kpiCards = [
  {
    title: 'Projets actifs',
    value: '12',
    trend: '+2 ce mois',
    iconBg: 'bg-blue-50',
    badgeClass: 'bg-green-100 text-green-700',
    icon: `<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>`
  },
  {
    title: 'Budget total',
    value: '85M FCFA',
    trend: '▲ 8%',
    iconBg: 'bg-teal-50',
    badgeClass: 'bg-teal-100 text-teal-700',
    icon: `<svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
  },
  {
    title: 'Activités en cours',
    value: '47',
    trend: '5 en retard',
    iconBg: 'bg-orange-50',
    badgeClass: 'bg-orange-100 text-orange-700',
    icon: `<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`
  },
  {
    title: 'Taux de complétion',
    value: '68%',
    trend: '↑ vs 61%',
    iconBg: 'bg-purple-50',
    badgeClass: 'bg-purple-100 text-purple-700',
    icon: `<svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>`
  }
]

const recentProjects = [
  { name: 'Accès Eau Potable - Région Nord', progress: 78, color: 'bg-blue-500', barColor: 'bg-blue-500', status: 'En cours', statusClass: 'text-blue-600', deadline: 'Fin mars 2026' },
  { name: 'Électrification Rurale - Zone Sud', progress: 45, color: 'bg-teal-500', barColor: 'bg-teal-500', status: 'En retard', statusClass: 'text-orange-500', deadline: 'Fin jan. 2026' },
  { name: 'Santé Communautaire - Lomé Est', progress: 92, color: 'bg-green-500', barColor: 'bg-green-500', status: 'Terminé', statusClass: 'text-green-600', deadline: 'Clôturé' },
  { name: 'Formation Professionnelle - Jeunes', progress: 30, color: 'bg-purple-500', barColor: 'bg-purple-500', status: 'Planifié', statusClass: 'text-purple-600', deadline: 'Fin juin 2026' },
]

const activityFeed = [
  { text: 'Dépense validée pour le projet Eau', time: 'Il y a 15 min', iconBg: 'bg-green-500', icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>` },
  { text: 'Nouveau KPI ajouté : Taux d\'accès', time: 'Il y a 1h', iconBg: 'bg-blue-500', icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>` },
  { text: 'Activité "Forages" marquée complète', time: 'Il y a 2h', iconBg: 'bg-teal-500', icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>` },
  { text: 'Rapport terrain soumis - Zone A', time: 'Hier 16:30', iconBg: 'bg-orange-500', icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>` },
  { text: 'Alerte : Budget Électrification à 90%', time: 'Hier 09:00', iconBg: 'bg-red-500', icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>` },
]

const budgetItems = [
  { label: 'Budget alloué', value: '85 000 000 FCFA', pct: 100, color: 'bg-gray-300' },
  { label: 'Dépenses engagées', value: '52 300 000 FCFA', pct: 62, color: 'bg-blue-500' },
  { label: 'Dépenses validées', value: '38 700 000 FCFA', pct: 46, color: 'bg-teal-500' },
  { label: 'Reste à dépenser', value: '32 700 000 FCFA', pct: 38, color: 'bg-green-400' },
]

const kpiPerformance = [
  { name: 'Personnes bénéficiaires', value: '12 450', target: '18 000', pct: 69, color: 'text-blue-600', barColor: 'bg-blue-500' },
  { name: 'Taux d\'accès eau', value: '78%', target: '95%', pct: 82, color: 'text-teal-600', barColor: 'bg-teal-500' },
  { name: 'Villages électrifiés', value: '23', target: '50', pct: 46, color: 'text-orange-600', barColor: 'bg-orange-400' },
  { name: 'Jeunes formés', value: '340', target: '500', pct: 68, color: 'text-purple-600', barColor: 'bg-purple-500' },
]
</script>
