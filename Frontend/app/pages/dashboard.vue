<template>
  <div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Tableau de bord</h1>
        <p class="page-subtitle">Vue d'ensemble de vos projets et performances · {{ currentDate }}</p>
      </div>
      <button class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouveau projet
      </button>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="kpi in kpiCards" :key="kpi.title"
           class="card hover:shadow-card-hover transition-all duration-200">
        <div class="flex items-start justify-between mb-3">
          <div class="kpi-icon">
            <span v-html="kpi.icon"></span>
          </div>
          <span class="badge" :class="kpi.badgeClass">{{ kpi.trend }}</span>
        </div>
        <p class="text-2xl font-bold font-display tabular-nums" style="color:#083C44">{{ kpi.value }}</p>
        <p class="text-xs mt-1" style="color:#6E797A">{{ kpi.title }}</p>
      </div>
    </div>

    <!-- Middle Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent projects -->
      <div class="lg:col-span-2 card">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-display font-semibold text-sm" style="color:#083C44">Projets récents</h2>
          <NuxtLink to="/projects" class="text-xs font-semibold font-mono-label transition-colors"
                    style="color:#22C7D6" @mouseenter="e=>e.target.style.textDecoration='underline'"
                    @mouseleave="e=>e.target.style.textDecoration='none'">Voir tout →</NuxtLink>
        </div>
        <div class="space-y-2">
          <div v-for="project in recentProjects" :key="project.name"
               class="flex items-center gap-4 p-3 rounded-lg hover:bg-brand-tinted transition-colors cursor-pointer">
            <div class="w-1.5 h-10 rounded-full flex-shrink-0" style="background:linear-gradient(180deg,#22C7D6,#8EF2FC)"></div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold truncate" style="color:#121C2A">{{ project.name }}</p>
              <div class="flex items-center gap-2 mt-1.5">
                <div class="progress-track flex-1 h-1.5">
                  <div class="progress-fill h-1.5" :style="`width:${project.progress}%`"></div>
                </div>
                <span class="font-mono-label text-xs tabular-nums" style="color:#6E797A">{{ project.progress }}%</span>
              </div>
            </div>
            <span class="badge text-xs flex-shrink-0" :class="project.badgeClass">{{ project.status }}</span>
          </div>
        </div>
      </div>

      <!-- Activity feed -->
      <div class="card">
        <h2 class="font-display font-semibold text-sm mb-4" style="color:#083C44">Activité récente</h2>
        <div class="space-y-3">
          <div v-for="activity in activityFeed" :key="activity.time" class="flex gap-3">
            <div class="activity-dot">
              <span v-html="activity.icon"></span>
            </div>
            <div>
              <p class="text-xs font-semibold" style="color:#3E494A">{{ activity.text }}</p>
              <p class="font-mono-label text-xs mt-0.5" style="color:#6E797A">{{ activity.time }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Budget -->
      <div class="card">
        <h2 class="font-display font-semibold text-sm mb-4" style="color:#083C44">Vue budgétaire</h2>
        <div class="space-y-3">
          <div v-for="b in budgetItems" :key="b.label">
            <div class="flex justify-between text-xs mb-1">
              <span style="color:#6E797A">{{ b.label }}</span>
              <span class="font-semibold font-mono-label tabular-nums" style="color:#083C44">{{ b.value }}</span>
            </div>
            <div class="progress-track h-2">
              <div class="progress-fill h-2" :style="`width:${b.pct}%`"></div>
            </div>
          </div>
        </div>
        <div class="mt-4 pt-4 flex justify-between items-center" style="border-top:1px solid #D9E1E7">
          <span class="text-xs" style="color:#6E797A">Solde disponible</span>
          <span class="font-display font-bold text-sm tabular-nums" style="color:#22C7D6">18 500 000 FCFA</span>
        </div>
      </div>

      <!-- KPI Performance -->
      <div class="lg:col-span-2 card">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-display font-semibold text-sm" style="color:#083C44">Performance des KPIs</h2>
          <NuxtLink to="/kpis" class="font-mono-label text-xs font-semibold" style="color:#22C7D6">Détails →</NuxtLink>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div v-for="kpi in kpiPerformance" :key="kpi.name"
               class="p-4 rounded-lg" style="background:#F8F9FF;border:1px solid #D9E1E7">
            <div class="flex items-center justify-between mb-2">
              <p class="text-xs font-semibold" style="color:#3E494A">{{ kpi.name }}</p>
              <span class="font-mono-label text-xs font-bold" style="color:#22C7D6">{{ kpi.value }}</span>
            </div>
            <div class="progress-track h-1.5">
              <div class="progress-fill h-1.5" :style="`width:${kpi.pct}%`"></div>
            </div>
            <p class="font-mono-label text-xs mt-1.5" style="color:#6E797A">Objectif : {{ kpi.target }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const currentDate = new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })

const iconSvg = (path) => `<svg class="w-5 h-5" style="color:#22C7D6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${path}"/></svg>`
const actIconSvg = (path) => `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${path}"/></svg>`

const kpiCards = [
  { title: 'Projets actifs', value: '12', trend: '+2 ce mois', badgeClass: 'badge-success',
    icon: iconSvg('M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z') },
  { title: 'Budget total', value: '85M FCFA', trend: '▲ 8%', badgeClass: 'badge-brand',
    icon: iconSvg('M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z') },
  { title: 'Activités en cours', value: '47', trend: '5 en retard', badgeClass: 'badge-warning',
    icon: iconSvg('M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2') },
  { title: 'Taux de complétion', value: '68%', trend: '↑ vs 61%', badgeClass: 'badge-brand',
    icon: iconSvg('M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z') },
]

const recentProjects = [
  { name: 'Accès Eau Potable - Région Nord', progress: 78, status: 'En cours', badgeClass: 'badge-brand' },
  { name: 'Électrification Rurale - Zone Sud', progress: 45, status: 'En retard', badgeClass: 'badge-warning' },
  { name: 'Santé Communautaire - Lomé Est', progress: 100, status: 'Terminé', badgeClass: 'badge-success' },
  { name: 'Formation Professionnelle - Jeunes', progress: 30, status: 'En cours', badgeClass: 'badge-brand' },
]

const activityFeed = [
  { text: 'Dépense validée pour le projet Eau', time: 'Il y a 15 min',
    icon: actIconSvg('M5 13l4 4L19 7') },
  { text: 'Nouveau KPI ajouté : Taux d\'accès', time: 'Il y a 1h',
    icon: actIconSvg('M12 4v16m8-8H4') },
  { text: 'Activité "Forages" marquée complète', time: 'Il y a 2h',
    icon: actIconSvg('M9 12l2 2 4-4') },
  { text: 'Rapport terrain soumis - Zone A', time: 'Hier 16:30',
    icon: actIconSvg('M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z') },
  { text: 'Alerte : Budget Électrification à 90%', time: 'Hier 09:00',
    icon: actIconSvg('M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z') },
]

const budgetItems = [
  { label: 'Budget alloué', value: '85 000 000 FCFA', pct: 100 },
  { label: 'Dépenses engagées', value: '52 300 000 FCFA', pct: 62 },
  { label: 'Dépenses validées', value: '38 700 000 FCFA', pct: 46 },
  { label: 'Reste à dépenser', value: '32 700 000 FCFA', pct: 38 },
]

const kpiPerformance = [
  { name: 'Personnes bénéficiaires', value: '12 450', target: '18 000', pct: 69 },
  { name: 'Taux d\'accès eau', value: '78%', target: '95%', pct: 82 },
  { name: 'Villages électrifiés', value: '23', target: '50', pct: 46 },
  { name: 'Jeunes formés', value: '340', target: '500', pct: 68 },
]
</script>
