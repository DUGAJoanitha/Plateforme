<template>
  <div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Projets</h1>
        <p class="page-subtitle">Gérez et suivez tous vos projets</p>
      </div>
      <button class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouveau projet
      </button>
    </div>

    <!-- Filters -->
    <div class="flex items-center gap-2 flex-wrap">
      <button v-for="filter in filters" :key="filter" @click="activeFilter = filter"
        class="font-mono-label text-label-sm px-4 py-2 rounded-full transition-all duration-200"
        :style="activeFilter === filter
          ? 'background:linear-gradient(135deg,#22C7D6,#8EF2FC);color:#083C44;font-weight:600;box-shadow:0 2px 10px rgba(34,199,214,0.3)'
          : 'background:#fff;color:#6E797A;border:1px solid #D9E1E7'">
        {{ filter }}
      </button>
      <div class="ml-auto relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" fill="none" stroke="#6E797A" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
        </svg>
        <input type="text" placeholder="Rechercher un projet..."
               class="input-field pl-9 w-64 text-sm" style="padding-top:8px;padding-bottom:8px" />
      </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      <div v-for="project in filteredProjects" :key="project.id"
           class="card group cursor-pointer hover:shadow-card-hover transition-all duration-200">

        <!-- Project header -->
        <div class="flex items-start justify-between mb-4">
          <div class="kpi-icon">
            <span v-html="project.icon"></span>
          </div>
          <span class="badge" :class="project.badgeClass">{{ project.status }}</span>
        </div>

        <!-- Info -->
        <h3 class="font-display font-bold text-sm mb-1 transition-colors duration-200"
            style="color:#083C44"
            @mouseenter="e => e.target.style.color='#22C7D6'"
            @mouseleave="e => e.target.style.color='#083C44'">
          {{ project.name }}
        </h3>
        <p class="text-xs mb-4 line-clamp-2" style="color:#6E797A">{{ project.description }}</p>

        <!-- Progress -->
        <div class="mb-4">
          <div class="flex justify-between text-xs mb-1.5">
            <span style="color:#6E797A">Progression</span>
            <span class="font-mono-label font-semibold tabular-nums" style="color:#083C44">{{ project.progress }}%</span>
          </div>
          <div class="progress-track h-2">
            <div class="progress-fill h-2" :style="`width:${project.progress}%`"></div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-3" style="border-top:1px solid #D9E1E7">
          <div class="flex items-center gap-1 text-xs" style="color:#6E797A">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ project.deadline }}
          </div>
          <span class="font-mono-label text-xs font-semibold" style="color:#22C7D6">{{ project.budget }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const activeFilter = ref('Tous')
const filters = ['Tous', 'En cours', 'Planifié', 'En retard', 'Terminé']

const projectIcon = (path) => `<svg class="w-5 h-5" style="color:#22C7D6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${path}"/></svg>`

const projects = [
  { id:1, name:'Accès Eau Potable - Région Nord',
    description:'Installation de 15 points d\'eau et formation de comités de gestion dans 8 villages.',
    progress:78, status:'En cours', badgeClass:'badge-brand', deadline:'Mars 2026', budget:'22M FCFA',
    icon: projectIcon('M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z') },
  { id:2, name:'Électrification Rurale - Zone Sud',
    description:'Déploiement de micro-centrales solaires pour 12 communautés rurales isolées.',
    progress:45, status:'En retard', badgeClass:'badge-warning', deadline:'Jan. 2026', budget:'35M FCFA',
    icon: projectIcon('M13 10V3L4 14h7v7l9-11h-7z') },
  { id:3, name:'Santé Communautaire - Lomé Est',
    description:'Construction et équipement de 3 centres de santé de quartier avec personnel formé.',
    progress:100, status:'Terminé', badgeClass:'badge-success', deadline:'Clôturé', budget:'18M FCFA',
    icon: projectIcon('M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z') },
  { id:4, name:'Formation Professionnelle - Jeunes',
    description:'Programme de formation en agriculture, couture, menuiserie pour 500 jeunes.',
    progress:30, status:'En cours', badgeClass:'badge-brand', deadline:'Juin 2026', budget:'8M FCFA',
    icon: projectIcon('M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z') },
  { id:5, name:'Routes Rurales - Préfecture Oti',
    description:'Réhabilitation de 45 km de pistes rurales pour désenclaver 20 villages.',
    progress:10, status:'Planifié', badgeClass:'badge-planned', deadline:'Déc. 2026', budget:'2M FCFA',
    icon: projectIcon('M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7') },
  { id:6, name:'Agriculture Durable - Plateaux',
    description:'Distribution de semences améliorées et formation à l\'agroécologie pour 800 familles.',
    progress:62, status:'En cours', badgeClass:'badge-brand', deadline:'Avr. 2026', budget:'12M FCFA',
    icon: projectIcon('M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064') },
]

const filteredProjects = computed(() => {
  if (activeFilter.value === 'Tous') return projects
  return projects.filter(p => p.status === activeFilter.value)
})
</script>
