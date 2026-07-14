<template>
  <div class="max-w-7xl mx-auto p-6 md:p-8 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Gestion budgétaire</h1>
        <p class="text-sm text-gray-500 mt-1">Aperçu en temps réel de l'allocation des ressources de l'entreprise</p>
      </div>
      <div class="flex gap-2">
        <div class="bg-red-50 text-red-600 border border-red-100 px-4 py-2 rounded-xl flex items-center gap-2 text-xs font-semibold">
          <LucideIcon name="AlertTriangle" class="w-4 h-4 text-red-500 flex-shrink-0 animate-bounce" />
          3 Alertes Budgétaires Actives
        </div>
      </div>
    </div>

    <!-- Bento Grid Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Card 1 -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-44">
        <div class="space-y-2">
          <div class="flex justify-between items-start">
            <span class="text-xs font-semibold text-gray-500">Budget Total (Ex. 2024)</span>
            <div class="bg-[#007a82]/10 p-2 rounded-xl text-[#007a82] flex items-center justify-center">
              <LucideIcon name="Wallet" class="w-5 h-5" />
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900">$2,450,000.00</div>
        </div>
        <div class="flex items-center gap-1.5 text-emerald-600 text-xs font-semibold">
          <LucideIcon name="TrendingUp" class="w-4 h-4" />
          <span>+12.5% vs l'année dernière</span>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-44">
        <div class="space-y-2">
          <div class="flex justify-between items-start">
            <span class="text-xs font-semibold text-gray-500">Dépenses Actuelles</span>
            <div class="bg-blue-50 p-2 rounded-xl text-blue-600 flex items-center justify-center">
              <LucideIcon name="BarChart3" class="w-5 h-5" />
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900">$1,180,450.00</div>
        </div>
        <div class="space-y-1.5">
          <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
            <div class="bg-[#007a82] h-full rounded-full transition-all duration-500" style="width: 48.2%"></div>
          </div>
          <div class="text-xs text-gray-400">48.2% du total alloué</div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-44">
        <div class="space-y-2">
          <div class="flex justify-between items-start">
            <span class="text-xs font-semibold text-gray-500">Solde Restant</span>
            <div class="bg-purple-50 p-2 rounded-xl text-purple-600 flex items-center justify-center">
              <LucideIcon name="Landmark" class="w-5 h-5" />
            </div>
          </div>
          <div class="text-3xl font-bold text-gray-900">$1,269,550.00</div>
        </div>
        <div class="flex items-center gap-1.5 text-purple-600 text-xs font-semibold">
          <LucideIcon name="Clock" class="w-4 h-4" />
          <span>Projection : -2% d'ici le T4</span>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Spending Trends Line Chart -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-6">
        <div class="flex justify-between items-center">
          <h3 class="text-base font-bold text-gray-900">Tendances des dépenses</h3>
          <div class="flex gap-1.5 bg-gray-50 p-1 rounded-xl border border-gray-100">
            <button 
              @click="spendInterval = 'monthly'" 
              class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200"
              :class="spendInterval === 'monthly' ? 'bg-white text-gray-800 shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-800'"
            >
              Mensuel
            </button>
            <button 
              @click="spendInterval = 'quarterly'" 
              class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-200"
              :class="spendInterval === 'quarterly' ? 'bg-white text-gray-800 shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-800'"
            >
              Trimestriel
            </button>
          </div>
        </div>
        <div class="h-64 relative flex items-end">
          <!-- simulated background lines -->
          <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-5">
            <div class="border-b border-gray-950"></div>
            <div class="border-b border-gray-950"></div>
            <div class="border-b border-gray-950"></div>
            <div class="border-b border-gray-950"></div>
          </div>
          <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 400 200">
            <path 
              :d="spendInterval === 'monthly' ? 'M0,180 L80,140 L160,160 L240,80 L320,100 L400,40' : 'M0,160 L130,120 L270,140 L400,60'" 
              fill="none" 
              stroke="#007a82" 
              stroke-linecap="round" 
              stroke-width="3"
              class="transition-all duration-1000"
            ></path>
            <path 
              :d="spendInterval === 'monthly' ? 'M0,180 L80,140 L160,160 L240,80 L320,100 L400,40 V200 H0 Z' : 'M0,160 L130,120 L270,140 L400,60 V200 H0 Z'" 
              fill="url(#grad1)" 
              opacity="0.08"
              class="transition-all duration-1000"
            ></path>
            <defs>
              <linearGradient id="grad1" x1="0%" x2="0%" y1="0%" y2="100%">
                <stop offset="0%" style="stop-color:#007a82;stop-opacity:1"></stop>
                <stop offset="100%" style="stop-color:#007a82;stop-opacity:0"></stop>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div class="flex justify-between text-[10px] font-semibold text-gray-400 px-1">
          <span v-for="label in intervalLabels" :key="label">{{ label }}</span>
        </div>
      </div>

      <!-- Allocation by Project Bar Chart -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-6">
        <div class="flex justify-between items-center">
          <h3 class="text-base font-bold text-gray-900">Allocation par projet</h3>
          <button class="text-gray-400 hover:text-gray-600 transition-colors flex items-center justify-center">
            <LucideIcon name="Settings" class="w-5 h-5" />
          </button>
        </div>
        <div class="space-y-5">
          <div v-for="proj in projectAllocations" :key="proj.name" class="space-y-1.5">
            <div class="flex justify-between text-xs font-semibold">
              <span class="text-gray-800">{{ proj.name }}</span>
              <span class="text-[#007a82]">{{ proj.spent }}k$ / {{ proj.total }}k$</span>
            </div>
            <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
              <div class="bg-[#007a82] h-full rounded-full transition-all duration-500" :style="`width: ${proj.pct}%`"></div>
            </div>
            <div class="flex justify-between text-[10px] text-gray-400">
              <span>{{ proj.pct }}% consommé</span>
              <span :class="proj.pct > 75 ? 'text-red-500 font-semibold' : 'text-gray-400'">
                {{ proj.pct > 75 ? 'Dépassement proche' : 'Budget OK' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Transactions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="text-base font-bold text-gray-900">Transactions Récentes</h3>
        <div class="flex gap-3">
          <button class="flex items-center gap-1.5 px-4 py-2 border border-gray-200 bg-white hover:bg-gray-50 rounded-xl text-xs font-semibold transition-all">
            <LucideIcon name="Filter" class="w-4 h-4 text-gray-500" />
            Filtrer
          </button>
          <button class="flex items-center gap-1.5 px-4 py-2 border border-gray-200 bg-white hover:bg-gray-50 rounded-xl text-xs font-semibold transition-all">
            <LucideIcon name="Calendar" class="w-4 h-4 text-gray-500" />
            Tout temps
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 text-gray-400 text-[10px] font-bold uppercase tracking-wider border-b border-gray-100">
              <th class="px-6 py-4">Date</th>
              <th class="px-6 py-4">Description</th>
              <th class="px-6 py-4">Catégorie</th>
              <th class="px-6 py-4">Projet</th>
              <th class="px-6 py-4">Montant</th>
              <th class="px-6 py-4">Statut</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-sm">
            <tr v-for="tx in transactions" :key="tx.desc" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">{{ tx.date }}</td>
              <td class="px-6 py-4 text-gray-800 font-bold">{{ tx.desc }}</td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold">
                  {{ tx.category }}
                </span>
              </td>
              <td class="px-6 py-4 text-gray-600 font-semibold">{{ tx.project }}</td>
              <td class="px-6 py-4 font-bold text-red-500 whitespace-nowrap">{{ tx.amount }}</td>
              <td class="px-6 py-4">
                <span 
                  class="flex items-center gap-1.5 text-xs font-bold"
                  :class="tx.status === 'Terminé' ? 'text-emerald-600' : tx.status === 'En attente' ? 'text-blue-500' : 'text-red-500'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="tx.status === 'Terminé' ? 'bg-emerald-500' : tx.status === 'En attente' ? 'bg-blue-500' : 'bg-red-500'"></span>
                  {{ tx.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="p-4 bg-gray-50/50 flex justify-center border-t border-gray-100">
        <button class="text-[#007a82] font-semibold text-sm hover:underline">Voir toutes les transactions</button>
      </div>
    </div>

    <!-- Contextual FAB (Only for Budgets screen) -->
    <button class="fixed bottom-6 right-6 h-14 w-14 bg-[#007a82] text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-200 z-50">
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
