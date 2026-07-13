<template>
  <div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Projets</h1>
        <p class="text-sm text-gray-500 mt-0.5">Gérez et suivez tous vos projets</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-opacity-90 transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouveau projet
      </button>
    </div>

    <!-- Filters -->
    <div class="flex items-center gap-3 flex-wrap">
      <button v-for="filter in filters" :key="filter" @click="activeFilter = filter"
        :class="`text-xs font-semibold px-4 py-2 rounded-full transition-all ${activeFilter === filter ? 'bg-brand-teal text-white shadow-sm' : 'bg-white text-gray-500 border border-gray-200 hover:border-brand-teal hover:text-brand-teal'}`">
        {{ filter }}
      </button>
      <div class="ml-auto relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
        </svg>
        <input type="text" placeholder="Rechercher un projet..." class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-teal w-64" />
      </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      <div v-for="project in filteredProjects" :key="project.id"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all cursor-pointer group">
        <!-- Project Header -->
        <div class="flex items-start justify-between mb-4">
          <div :class="`w-10 h-10 rounded-xl flex items-center justify-center ${project.iconBg}`">
            <span v-html="project.icon"></span>
          </div>
          <span :class="`text-xs font-semibold px-2.5 py-1 rounded-full ${project.statusClass}`">{{ project.status }}</span>
        </div>

        <!-- Project Info -->
        <h3 class="text-sm font-bold text-gray-800 mb-1 group-hover:text-brand-teal transition-colors">{{ project.name }}</h3>
        <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ project.description }}</p>

        <!-- Progress -->
        <div class="mb-4">
          <div class="flex justify-between text-xs mb-1.5">
            <span class="text-gray-500">Progression</span>
            <span class="font-semibold text-gray-800">{{ project.progress }}%</span>
          </div>
          <div class="w-full bg-gray-100 rounded-full h-2">
            <div :class="`h-2 rounded-full ${project.barColor} transition-all`" :style="`width: ${project.progress}%`"></div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
          <div class="flex items-center gap-1 text-xs text-gray-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ project.deadline }}
          </div>
          <div class="flex items-center gap-1 text-xs text-gray-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ project.budget }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const activeFilter = ref('Tous')
const filters = ['Tous', 'En cours', 'Planifié', 'En retard', 'Terminé']

const projects = [
  {
    id: 1, name: 'Accès Eau Potable - Région Nord', description: 'Installation de 15 points d\'eau et formation de comités de gestion dans 8 villages.',
    progress: 78, status: 'En cours', statusClass: 'bg-blue-100 text-blue-700', iconBg: 'bg-blue-50', barColor: 'bg-blue-500',
    deadline: 'Mars 2026', budget: '22M FCFA',
    icon: `<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>`
  },
  {
    id: 2, name: 'Électrification Rurale - Zone Sud', description: 'Déploiement de micro-centrales solaires pour 12 communautés rurales isolées.',
    progress: 45, status: 'En retard', statusClass: 'bg-orange-100 text-orange-700', iconBg: 'bg-orange-50', barColor: 'bg-orange-400',
    deadline: 'Jan. 2026', budget: '35M FCFA',
    icon: `<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`
  },
  {
    id: 3, name: 'Santé Communautaire - Lomé Est', description: 'Construction et équipement de 3 centres de santé de quartier avec personnel formé.',
    progress: 100, status: 'Terminé', statusClass: 'bg-green-100 text-green-700', iconBg: 'bg-green-50', barColor: 'bg-green-500',
    deadline: 'Clôturé', budget: '18M FCFA',
    icon: `<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>`
  },
  {
    id: 4, name: 'Formation Professionnelle - Jeunes', description: 'Programme de formation en agriculture, couture, menuiserie pour 500 jeunes.',
    progress: 30, status: 'En cours', statusClass: 'bg-blue-100 text-blue-700', iconBg: 'bg-purple-50', barColor: 'bg-purple-500',
    deadline: 'Juin 2026', budget: '8M FCFA',
    icon: `<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>`
  },
  {
    id: 5, name: 'Routes Rurales - Préfecture Oti', description: 'Réhabilitation de 45 km de pistes rurales pour désenclaver 20 villages.',
    progress: 10, status: 'Planifié', statusClass: 'bg-gray-100 text-gray-600', iconBg: 'bg-gray-50', barColor: 'bg-gray-400',
    deadline: 'Déc. 2026', budget: '2M FCFA',
    icon: `<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>`
  },
  {
    id: 6, name: 'Agriculture Durable - Plateaux', description: 'Distribution de semences améliorées et formation à l\'agroécologie pour 800 familles.',
    progress: 62, status: 'En cours', statusClass: 'bg-blue-100 text-blue-700', iconBg: 'bg-teal-50', barColor: 'bg-teal-500',
    deadline: 'Avr. 2026', budget: '12M FCFA',
    icon: `<svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>`
  },
]

const filteredProjects = computed(() => {
  if (activeFilter.value === 'Tous') return projects
  return projects.filter(p => p.status === activeFilter.value)
})
</script>
