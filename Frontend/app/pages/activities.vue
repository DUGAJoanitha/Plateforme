<template>
  <div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Activités</h1>
        <p class="page-subtitle">Suivi des tâches et activités de vos projets</p>
      </div>
      <button class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouvelle activité
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="s in summary" :key="s.label" class="card text-center">
        <p class="text-2xl font-bold font-display tabular-nums" :style="`color:${s.color}`">{{ s.value }}</p>
        <p class="font-mono-label text-xs mt-1" style="color:#6E797A">{{ s.label }}</p>
      </div>
    </div>

    <!-- Activities Table -->
    <div class="table-container">
      <!-- Table header bar -->
      <div class="flex items-center justify-between px-5 py-3.5" style="border-bottom:1px solid #D9E1E7">
        <h2 class="font-display font-semibold text-sm" style="color:#083C44">Liste des activités</h2>
        <div class="flex gap-1.5">
          <button v-for="f in actFilters" :key="f" @click="actFilter = f"
            class="font-mono-label text-xs px-3 py-1.5 rounded-md transition-all duration-200"
            :style="actFilter === f
              ? 'background:linear-gradient(135deg,#22C7D6,#8EF2FC);color:#083C44;font-weight:600'
              : 'color:#6E797A;background:transparent'"
            @mouseenter="e => { if(actFilter !== f) e.target.style.background='rgba(34,199,214,0.08)' }"
            @mouseleave="e => { if(actFilter !== f) e.target.style.background='transparent' }">
            {{ f }}
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr style="background:#F8F9FF;border-bottom:1px solid #D9E1E7">
              <th class="table-header text-left px-5 py-3">Activité</th>
              <th class="table-header text-left px-5 py-3">Projet</th>
              <th class="table-header text-left px-5 py-3">Responsable</th>
              <th class="table-header text-left px-5 py-3">Échéance</th>
              <th class="table-header text-left px-5 py-3">Statut</th>
              <th class="table-header text-left px-5 py-3">Progression</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="act in filteredActivities" :key="act.id" class="table-row cursor-pointer">
              <td class="px-5 py-4">
                <p class="text-sm font-semibold" style="color:#121C2A">{{ act.name }}</p>
                <p class="text-xs mt-0.5" style="color:#6E797A">{{ act.desc }}</p>
              </td>
              <td class="px-5 py-4">
                <span class="badge badge-planned">{{ act.project }}</span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-2">
                  <div class="avatar-sm">{{ act.avatar }}</div>
                  <span class="text-xs" style="color:#3E494A">{{ act.assignee }}</span>
                </div>
              </td>
              <td class="px-5 py-4 font-mono-label text-xs" style="color:#6E797A">{{ act.deadline }}</td>
              <td class="px-5 py-4">
                <span class="badge" :class="act.badgeClass">{{ act.status }}</span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-2">
                  <div class="progress-track w-20 h-1.5">
                    <div class="progress-fill h-1.5" :style="`width:${act.pct}%`"></div>
                  </div>
                  <span class="font-mono-label text-xs tabular-nums" style="color:#6E797A">{{ act.pct }}%</span>
                </div>
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

const actFilter = ref('Toutes')
const actFilters = ['Toutes', 'En cours', 'En retard', 'Terminées']

const summary = [
  { label: 'Total activités', value: '47', color: '#083C44' },
  { label: 'En cours',        value: '28', color: '#22C7D6' },
  { label: 'En retard',       value: '5',  color: '#D97706' },
  { label: 'Terminées',       value: '14', color: '#16A34A' },
]

const activities = [
  { id:1, name:'Forage puits N°1 - Village Kara', desc:'Installation complète + test qualité', project:'Eau Potable', assignee:'Kofi A.', avatar:'KA', deadline:'15 jan 2026', status:'Terminé', badgeClass:'badge-success', pct:100 },
  { id:2, name:'Formation comité de gestion', desc:'Formation 3 jours pour 20 membres', project:'Eau Potable', assignee:'Afi D.', avatar:'AD', deadline:'30 jan 2026', status:'En cours', badgeClass:'badge-brand', pct:60 },
  { id:3, name:'Installation panneaux solaires', desc:'Pose + câblage 50 unités', project:'Électrification', assignee:'Yao M.', avatar:'YM', deadline:'10 jan 2026', status:'En retard', badgeClass:'badge-warning', pct:30 },
  { id:4, name:'Réception matériaux construction', desc:'Ciment, fer, sable pour centres santé', project:'Santé', assignee:'Sika T.', avatar:'ST', deadline:'05 fév 2026', status:'En cours', badgeClass:'badge-brand', pct:75 },
  { id:5, name:'Inscription jeunes formation', desc:'Sélection et inscription 500 candidats', project:'Formation', assignee:'Edem K.', avatar:'EK', deadline:'01 mar 2026', status:'En cours', badgeClass:'badge-brand', pct:45 },
  { id:6, name:'Étude topographique - Pistes', desc:'Relevé et cartographie 45 km', project:'Routes', assignee:'Nadia F.', avatar:'NF', deadline:'20 fév 2026', status:'Terminé', badgeClass:'badge-success', pct:100 },
  { id:7, name:'Distribution semences améliorées', desc:'Distribution à 800 familles cibles', project:'Agriculture', assignee:'Paulo A.', avatar:'PA', deadline:'28 jan 2026', status:'En cours', badgeClass:'badge-brand', pct:55 },
]

const filteredActivities = computed(() => {
  if (actFilter.value === 'Toutes') return activities
  if (actFilter.value === 'En retard') return activities.filter(a => a.status === 'En retard')
  if (actFilter.value === 'Terminées') return activities.filter(a => a.status === 'Terminé')
  return activities.filter(a => a.status === 'En cours')
})
</script>
