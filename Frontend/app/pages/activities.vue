<template>
  <div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Activités</h1>
        <p class="text-sm text-gray-500 mt-0.5">Suivi des tâches et activités de vos projets</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-opacity-90 transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouvelle activité
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="s in summary" :key="s.label" class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
        <p :class="`text-2xl font-bold ${s.color}`">{{ s.value }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ s.label }}</p>
      </div>
    </div>

    <!-- Activities Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Liste des activités</h2>
        <div class="flex gap-2">
          <button v-for="f in actFilters" :key="f" @click="actFilter = f"
            :class="`text-xs px-3 py-1.5 rounded-lg font-medium transition-all ${actFilter === f ? 'bg-brand-teal text-white' : 'text-gray-500 hover:bg-gray-50'}`">
            {{ f }}
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Activité</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Projet</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Responsable</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Échéance</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Statut</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Progression</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="act in filteredActivities" :key="act.id" class="hover:bg-gray-50 transition-colors cursor-pointer">
              <td class="px-5 py-4">
                <p class="text-sm font-semibold text-gray-800">{{ act.name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ act.desc }}</p>
              </td>
              <td class="px-5 py-4">
                <span class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">{{ act.project }}</span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full bg-brand-teal flex items-center justify-center text-white text-[10px] font-bold">{{ act.avatar }}</div>
                  <span class="text-xs text-gray-700">{{ act.assignee }}</span>
                </div>
              </td>
              <td class="px-5 py-4 text-xs text-gray-600">{{ act.deadline }}</td>
              <td class="px-5 py-4">
                <span :class="`text-xs font-semibold px-2.5 py-1 rounded-full ${act.statusClass}`">{{ act.status }}</span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-2">
                  <div class="w-20 bg-gray-100 rounded-full h-1.5">
                    <div :class="`h-1.5 rounded-full ${act.barColor}`" :style="`width:${act.pct}%`"></div>
                  </div>
                  <span class="text-xs text-gray-500">{{ act.pct }}%</span>
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
  { label: 'Total activités', value: '47', color: 'text-gray-800' },
  { label: 'En cours', value: '28', color: 'text-blue-600' },
  { label: 'En retard', value: '5', color: 'text-orange-500' },
  { label: 'Terminées', value: '14', color: 'text-green-600' },
]

const activities = [
  { id:1, name: 'Forage puits N°1 - Village Kara', desc: 'Installation complète + test qualité', project: 'Eau Potable', assignee: 'Kofi A.', avatar: 'KA', deadline: '15 jan 2026', status: 'Terminé', statusClass: 'bg-green-100 text-green-700', pct: 100, barColor: 'bg-green-500' },
  { id:2, name: 'Formation comité de gestion', desc: 'Formation 3 jours pour 20 membres', project: 'Eau Potable', assignee: 'Afi D.', avatar: 'AD', deadline: '30 jan 2026', status: 'En cours', statusClass: 'bg-blue-100 text-blue-700', pct: 60, barColor: 'bg-blue-500' },
  { id:3, name: 'Installation panneaux solaires', desc: 'Pose + câblage 50 unités', project: 'Électrification', assignee: 'Yao M.', avatar: 'YM', deadline: '10 jan 2026', status: 'En retard', statusClass: 'bg-orange-100 text-orange-700', pct: 30, barColor: 'bg-orange-400' },
  { id:4, name: 'Réception matériaux construction', desc: 'Ciment, fer, sable pour centres santé', project: 'Santé', assignee: 'Sika T.', avatar: 'ST', deadline: '05 fév 2026', status: 'En cours', statusClass: 'bg-blue-100 text-blue-700', pct: 75, barColor: 'bg-blue-500' },
  { id:5, name: 'Inscription jeunes formation', desc: 'Sélection et inscription 500 candidats', project: 'Formation', assignee: 'Edem K.', avatar: 'EK', deadline: '01 mar 2026', status: 'En cours', statusClass: 'bg-blue-100 text-blue-700', pct: 45, barColor: 'bg-blue-500' },
  { id:6, name: 'Étude topographique - Pistes', desc: 'Relevé et cartographie 45 km', project: 'Routes', assignee: 'Nadia F.', avatar: 'NF', deadline: '20 fév 2026', status: 'Terminé', statusClass: 'bg-green-100 text-green-700', pct: 100, barColor: 'bg-green-500' },
  { id:7, name: 'Distribution semences améliorées', desc: 'Distribution à 800 familles cibles', project: 'Agriculture', assignee: 'Paulo A.', avatar: 'PA', deadline: '28 jan 2026', status: 'En cours', statusClass: 'bg-blue-100 text-blue-700', pct: 55, barColor: 'bg-blue-500' },
]

const filteredActivities = computed(() => {
  if (actFilter.value === 'Toutes') return activities
  if (actFilter.value === 'En retard') return activities.filter(a => a.status === 'En retard')
  if (actFilter.value === 'Terminées') return activities.filter(a => a.status === 'Terminé')
  return activities.filter(a => a.status === 'En cours')
})
</script>
