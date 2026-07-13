<template>
  <div class="p-6 md:p-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Finance</h1>
        <p class="text-sm text-gray-500 mt-0.5">Gestion des budgets et dépenses</p>
      </div>
      <button class="flex items-center gap-2 bg-brand-teal text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-opacity-90 transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouvelle dépense
      </button>
    </div>

    <!-- Financial KPIs -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="card in finCards" :key="card.label" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 mb-2">{{ card.label }}</p>
        <p :class="`text-xl font-bold ${card.color}`">{{ card.value }}</p>
        <p class="text-[10px] text-gray-400 mt-1">{{ card.sub }}</p>
      </div>
    </div>

    <!-- Two columns -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Budgets par projet -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Budgets par projet</h2>
        <div class="space-y-4">
          <div v-for="proj in projectBudgets" :key="proj.name">
            <div class="flex justify-between items-center mb-1.5">
              <span class="text-xs font-semibold text-gray-700 truncate pr-4">{{ proj.name }}</span>
              <span class="text-xs text-gray-500 flex-shrink-0">{{ proj.spent }} / {{ proj.total }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 relative overflow-hidden">
              <div :class="`h-2.5 rounded-full transition-all ${proj.barColor}`" :style="`width: ${proj.pct}%`"></div>
            </div>
            <div class="flex justify-between mt-1">
              <span class="text-[10px] text-gray-400">{{ proj.pct }}% consommé</span>
              <span :class="`text-[10px] font-semibold ${proj.pct > 90 ? 'text-red-500' : proj.pct > 70 ? 'text-orange-500' : 'text-green-600'}`">
                {{ proj.pct > 90 ? '⚠ Alerte budget' : proj.pct > 70 ? 'Attention' : 'OK' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent expenses -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Dépenses récentes</h2>
        <div class="space-y-3">
          <div v-for="exp in expenses" :key="exp.id" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
            <div :class="`w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 ${exp.iconBg}`">
              <span v-html="exp.icon"></span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-semibold text-gray-800 truncate">{{ exp.label }}</p>
              <p class="text-[10px] text-gray-400">{{ exp.project }} · {{ exp.date }}</p>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="text-sm font-bold text-gray-800">{{ exp.amount }}</p>
              <span :class="`text-[10px] font-semibold ${exp.validClass}`">{{ exp.validStatus }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Expenses validation table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Dépenses en attente de validation</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Description</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Projet</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Montant</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Soumis par</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Date</th>
              <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="pending in pendingExpenses" :key="pending.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3 text-sm text-gray-800 font-medium">{{ pending.desc }}</td>
              <td class="px-5 py-3"><span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">{{ pending.project }}</span></td>
              <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ pending.amount }}</td>
              <td class="px-5 py-3 text-xs text-gray-600">{{ pending.by }}</td>
              <td class="px-5 py-3 text-xs text-gray-500">{{ pending.date }}</td>
              <td class="px-5 py-3">
                <div class="flex gap-2">
                  <button class="text-xs font-semibold text-green-600 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors">Valider</button>
                  <button class="text-xs font-semibold text-red-500 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors">Rejeter</button>
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

const finCards = [
  { label: 'Budget total', value: '85 000 000 FCFA', color: 'text-gray-900', sub: 'Tous projets confondus' },
  { label: 'Dépenses validées', value: '38 700 000 FCFA', color: 'text-blue-600', sub: '45.5% du budget' },
  { label: 'En attente', value: '13 600 000 FCFA', color: 'text-orange-500', sub: '4 dépenses à valider' },
  { label: 'Solde disponible', value: '32 700 000 FCFA', color: 'text-green-600', sub: '38.5% restant' },
]

const projectBudgets = [
  { name: 'Eau Potable - Nord', spent: '17.2M', total: '22M', pct: 78, barColor: 'bg-blue-500' },
  { name: 'Électrification - Sud', spent: '32.4M', total: '35M', pct: 93, barColor: 'bg-red-500' },
  { name: 'Santé - Lomé Est', spent: '18M', total: '18M', pct: 100, barColor: 'bg-green-500' },
  { name: 'Formation Jeunes', spent: '2.4M', total: '8M', pct: 30, barColor: 'bg-purple-500' },
  { name: 'Agriculture - Plateaux', spent: '7.4M', total: '12M', pct: 62, barColor: 'bg-teal-500' },
]

const expenses = [
  { id:1, label: 'Achat pompes immergées ×5', project: 'Eau Potable', date: '08 jan 2026', amount: '3 500 000 FCFA', validStatus: '✓ Validé', validClass: 'text-green-600', iconBg: 'bg-blue-50', icon: `<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>` },
  { id:2, label: 'Salaires techniciens - jan', project: 'Électrification', date: '05 jan 2026', amount: '1 800 000 FCFA', validStatus: '✓ Validé', validClass: 'text-green-600', iconBg: 'bg-green-50', icon: `<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>` },
  { id:3, label: 'Carburant mission terrain - S1', project: 'Agriculture', date: '03 jan 2026', amount: '450 000 FCFA', validStatus: '⏳ En attente', validClass: 'text-orange-500', iconBg: 'bg-orange-50', icon: `<svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>` },
  { id:4, label: 'Matériaux construction centre', project: 'Santé', date: '02 jan 2026', amount: '5 200 000 FCFA', validStatus: '✓ Validé', validClass: 'text-green-600', iconBg: 'bg-teal-50', icon: `<svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>` },
]

const pendingExpenses = [
  { id:1, desc: 'Carburant mission terrain S1', project: 'Agriculture', amount: '450 000 FCFA', by: 'Paulo A.', date: '03 jan 2026' },
  { id:2, desc: 'Achat outils formation menuiserie', project: 'Formation', amount: '1 200 000 FCFA', by: 'Edem K.', date: '06 jan 2026' },
  { id:3, desc: 'Per diem équipe terrain - semaine 1', project: 'Eau Potable', amount: '380 000 FCFA', by: 'Kofi A.', date: '07 jan 2026' },
  { id:4, desc: 'Réparation véhicule mission #3', project: 'Routes', amount: '760 000 FCFA', by: 'Nadia F.', date: '08 jan 2026' },
]
</script>
