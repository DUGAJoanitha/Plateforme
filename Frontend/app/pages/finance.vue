<template>
  <div class="max-w-7xl mx-auto p-6 md:p-8 space-y-6">
    <!-- Header Section -->
    <div class="page-header flex-col md:flex-row items-start md:items-end gap-4 mb-2">
      <div>
        <h1 class="page-title">Gestion budgétaire</h1>
        <p class="page-subtitle mt-1">Aperçu en temps réel de l'allocation des ressources de l'entreprise</p>
      </div>
      <div class="flex gap-2">
        <div class="badge badge-error px-4 py-2 flex items-center gap-2">
          <LucideIcon name="AlertTriangle" class="w-4 h-4 flex-shrink-0 animate-bounce" />
          3 Alertes Budgétaires Actives
        </div>
      </div>
    </div>

    <!-- Bento Grid Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Card 1 -->
      <div class="card flex flex-col justify-between h-44 hover:shadow-card-hover">
        <div class="space-y-2">
          <div class="flex justify-between items-start">
            <span class="font-mono-label text-xs" style="color:#6E797A">Budget Total (Ex. 2024)</span>
            <div class="kpi-icon">
              <LucideIcon name="Wallet" class="w-5 h-5" />
            </div>
          </div>
          <div class="font-display text-3xl font-bold" style="color:#083C44">$2,450,000.00</div>
        </div>
        <div class="flex items-center gap-1.5 font-mono-label text-xs font-semibold" style="color:#16A34A">
          <LucideIcon name="TrendingUp" class="w-4 h-4" />
          <span>+12.5% vs l'année dernière</span>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="card flex flex-col justify-between h-44 hover:shadow-card-hover">
        <div class="space-y-2">
          <div class="flex justify-between items-start">
            <span class="font-mono-label text-xs" style="color:#6E797A">Dépenses Actuelles</span>
            <div class="kpi-icon">
              <LucideIcon name="BarChart3" class="w-5 h-5" />
            </div>
          </div>
          <div class="font-display text-3xl font-bold" style="color:#083C44">$1,180,450.00</div>
        </div>
        <div class="space-y-1.5">
          <div class="progress-track h-2">
            <div class="progress-fill h-2" style="width: 48.2%"></div>
          </div>
          <div class="font-mono-label text-xs" style="color:#6E797A">48.2% du total alloué</div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="card flex flex-col justify-between h-44 hover:shadow-card-hover">
        <div class="space-y-2">
          <div class="flex justify-between items-start">
            <span class="font-mono-label text-xs" style="color:#6E797A">Solde Restant</span>
            <div class="kpi-icon">
              <LucideIcon name="Landmark" class="w-5 h-5" />
            </div>
          </div>
          <div class="font-display text-3xl font-bold" style="color:#083C44">$1,269,550.00</div>
        </div>
        <div class="flex items-center gap-1.5 font-mono-label text-xs font-semibold" style="color:#D97706">
          <LucideIcon name="Clock" class="w-4 h-4" />
          <span>Projection : -2% d'ici le T4</span>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Spending Trends Line Chart -->
      <div class="card space-y-6">
        <div class="flex justify-between items-center">
          <h3 class="font-display text-base font-bold" style="color:#083C44">Tendances des dépenses</h3>
          <div class="flex gap-1.5 p-1 rounded-lg" style="background:#F8F9FF;border:1px solid #D9E1E7">
            <button 
              @click="spendInterval = 'monthly'" 
              class="font-mono-label px-3 py-1 rounded text-xs transition-all duration-200"
              :style="spendInterval === 'monthly' ? 'background:#fff;color:#083C44;box-shadow:0 1px 3px rgba(8,60,68,0.1)' : 'color:#6E797A'"
            >
              Mensuel
            </button>
            <button 
              @click="spendInterval = 'quarterly'" 
              class="font-mono-label px-3 py-1 rounded text-xs transition-all duration-200"
              :style="spendInterval === 'quarterly' ? 'background:#fff;color:#083C44;box-shadow:0 1px 3px rgba(8,60,68,0.1)' : 'color:#6E797A'"
            >
              Trimestriel
            </button>
          </div>
        </div>
        <div class="h-64 relative flex items-end">
          <!-- simulated background lines -->
          <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-5">
            <div class="border-b" style="border-color:#121C2A"></div>
            <div class="border-b" style="border-color:#121C2A"></div>
            <div class="border-b" style="border-color:#121C2A"></div>
            <div class="border-b" style="border-color:#121C2A"></div>
          </div>
          <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 400 200">
            <path 
              :d="spendInterval === 'monthly' ? 'M0,180 L80,140 L160,160 L240,80 L320,100 L400,40' : 'M0,160 L130,120 L270,140 L400,60'" 
              fill="none" 
              stroke="#22C7D6" 
              stroke-linecap="round" 
              stroke-width="3"
              class="transition-all duration-1000"
            ></path>
            <path 
              :d="spendInterval === 'monthly' ? 'M0,180 L80,140 L160,160 L240,80 L320,100 L400,40 V200 H0 Z' : 'M0,160 L130,120 L270,140 L400,60 V200 H0 Z'" 
              fill="url(#grad1)" 
              opacity="0.15"
              class="transition-all duration-1000"
            ></path>
            <defs>
              <linearGradient id="grad1" x1="0%" x2="0%" y1="0%" y2="100%">
                <stop offset="0%" style="stop-color:#22C7D6;stop-opacity:1"></stop>
                <stop offset="100%" style="stop-color:#22C7D6;stop-opacity:0"></stop>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div class="flex justify-between font-mono-label text-[10px] px-1" style="color:#6E797A">
          <span v-for="label in intervalLabels" :key="label">{{ label }}</span>
        </div>
      </div>

      <!-- Allocation by Project Bar Chart -->
      <div class="card space-y-6">
        <div class="flex justify-between items-center">
          <h3 class="font-display text-base font-bold" style="color:#083C44">Allocation par projet</h3>
          <button class="transition-colors flex items-center justify-center" style="color:#6E797A" @mouseenter="e=>e.target.style.color='#22C7D6'" @mouseleave="e=>e.target.style.color='#6E797A'">
            <LucideIcon name="Settings" class="w-5 h-5" />
          </button>
        </div>
        <div class="space-y-5">
          <div v-for="proj in projectAllocations" :key="proj.name" class="space-y-1.5">
            <div class="flex justify-between font-mono-label text-xs font-semibold">
              <span style="color:#121C2A">{{ proj.name }}</span>
              <span style="color:#22C7D6">{{ proj.spent }}k$ / {{ proj.total }}k$</span>
            </div>
            <div class="progress-track h-2.5">
              <div class="progress-fill h-2.5" :style="`width: ${proj.pct}%`"></div>
            </div>
            <div class="flex justify-between font-mono-label text-[10px]">
              <span style="color:#6E797A">{{ proj.pct }}% consommé</span>
              <span :style="proj.pct > 75 ? 'color:#DC2626;font-weight:600' : 'color:#6E797A'">
                {{ proj.pct > 75 ? 'Dépassement proche' : 'Budget OK' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Transactions Table -->
    <div class="table-container">
      <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4" style="border-bottom:1px solid #D9E1E7">
        <h3 class="font-display text-base font-bold" style="color:#083C44">Transactions Récentes</h3>
        <div class="flex gap-3">
          <button class="btn-secondary text-xs">
            <LucideIcon name="Filter" class="w-4 h-4" style="color:#6E797A" />
            Filtrer
          </button>
          <button class="btn-secondary text-xs">
            <LucideIcon name="Calendar" class="w-4 h-4" style="color:#6E797A" />
            Tout temps
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr>
              <th class="table-header px-6 py-4">Date</th>
              <th class="table-header px-6 py-4">Description</th>
              <th class="table-header px-6 py-4">Catégorie</th>
              <th class="table-header px-6 py-4">Projet</th>
              <th class="table-header px-6 py-4">Montant</th>
              <th class="table-header px-6 py-4">Statut</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="tx in transactions" :key="tx.desc" class="table-row">
              <td class="px-6 py-4 font-mono-label text-xs whitespace-nowrap" style="color:#6E797A">{{ tx.date }}</td>
              <td class="px-6 py-4 font-semibold text-sm" style="color:#121C2A">{{ tx.desc }}</td>
              <td class="px-6 py-4">
                <span class="badge badge-neutral">
                  {{ tx.category }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm" style="color:#3E494A">{{ tx.project }}</td>
              <td class="px-6 py-4 font-mono-label font-bold whitespace-nowrap" style="color:#DC2626">{{ tx.amount }}</td>
              <td class="px-6 py-4">
                <span class="badge" :class="tx.status === 'Terminé' ? 'badge-success' : tx.status === 'En attente' ? 'badge-brand' : 'badge-error'">
                  {{ tx.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="p-4 flex justify-center" style="background:#F8F9FF;border-top:1px solid #D9E1E7">
        <button class="font-mono-label text-xs font-semibold hover:underline transition-colors" style="color:#22C7D6">Voir toutes les transactions</button>
      </div>
    </div>

    <!-- Contextual FAB (Only for Budgets screen) -->
    <button class="fixed bottom-6 right-6 h-14 w-14 rounded-full shadow-glow-lg flex items-center justify-center transition-all duration-200 z-50 hover:scale-110 active:scale-95" style="background:linear-gradient(135deg,#22C7D6,#8EF2FC);color:#083C44">
      <LucideIcon name="Plus" class="w-6 h-6" />
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

definePageMeta({ layout: 'default' })

const spendInterval = ref('monthly')

const intervalLabels = computed(() => {
  if (spendInterval.value === 'monthly') {
    return ['Jan', 'Mar', 'Mai', 'Jui', 'Sep', 'Nov']
  } else {
    return ['T1', 'T2', 'T3', 'T4']
  }
})

const projectAllocations = [
  { name: 'Initiative Alpha', spent: 450, total: 600, pct: 75 },
  { name: 'R&D Flux de Données', spent: 280, total: 350, pct: 80 },
  { name: 'Expansion Globale', spent: 120, total: 900, pct: 13 },
  { name: 'Marketing Principal', spent: 330, total: 600, pct: 55 }
]

const transactions = [
  { date: '24 Oct 2023', desc: 'Infrastructure d\'hébergement cloud', category: 'Infrastructure', project: 'R&D Flux de Données', amount: '-$12,400.00', status: 'Terminé' },
  { date: '23 Oct 2023', desc: 'Agence de design stratégique', category: 'Services pro', project: 'Initiative Alpha', amount: '-$8,250.00', status: 'En attente' },
  { date: '22 Oct 2023', desc: 'Licences logicielles - Pack SaaS', category: 'Logiciel', project: 'Expansion Globale', amount: '-$15,000.00', status: 'Terminé' },
  { date: '20 Oct 2023', desc: 'Renouvellement matériel T3', category: 'Équipement', project: 'Initiative Alpha', amount: '-$45,200.00', status: 'Signalé' }
]
</script>
