<template>
  <div class="p-6 md:p-8 space-y-6 antialiased">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900" style="text-wrap: balance;">Logs d'Audit</h1>
        <p class="text-sm text-gray-500 mt-1" style="text-wrap: pretty;">Historique complet des actions système à des fins de traçabilité et de sécurité.</p>
      </div>
      <button class="flex items-center gap-2 bg-gray-100 text-gray-900 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors active:scale-[0.96] shadow-sm min-h-[40px] min-w-[40px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Exporter CSV
      </button>
    </div>

    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-transparent">
      <div class="px-6 py-4 border-b border-gray-100 flex gap-4">
        <input type="text" placeholder="Rechercher dans les logs..." class="flex-1 bg-gray-50 border-none rounded-xl px-4 py-2 text-sm focus:ring-0 text-gray-900 min-h-[40px]">
        <button class="bg-gray-50 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-100 active:scale-[0.96] transition-colors min-h-[40px]">
          Filtrer
        </button>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Heure</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Utilisateur</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Entité</th>
              <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Adresse IP</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(log, idx) in auditLogs" :key="log.id" 
                class="hover:bg-gray-50 transition-colors"
                :style="{ animationDelay: `${idx * 30}ms` }">
              <td class="px-6 py-4 text-sm text-gray-900 tabular-nums font-medium whitespace-nowrap">{{ log.timestamp }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">
                <span class="font-bold text-gray-900">{{ log.user }}</span>
                <span class="text-xs text-gray-400 block tabular-nums">{{ log.role }}</span>
              </td>
              <td class="px-6 py-4">
                <span :class="[
                  'text-xs font-bold px-2 py-1 rounded-md uppercase tracking-wide',
                  log.action === 'CREATE' ? 'bg-brand-teal/10 text-brand-teal' :
                  log.action === 'UPDATE' ? 'bg-blue-100 text-blue-700' :
                  log.action === 'DELETE' ? 'bg-[#ba1a1a]/10 text-[#ba1a1a]' :
                  'bg-gray-200 text-gray-700'
                ]">
                  {{ log.action }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-900" style="text-wrap: pretty;">
                {{ log.details }}
              </td>
              <td class="px-6 py-4 text-xs text-gray-400 tabular-nums font-mono">{{ log.ip }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const auditLogs = [
  { id: 101, timestamp: '2026-02-14 14:32:01', user: 'Admin System', role: 'Super Admin', action: 'UPDATE', details: 'Modification des paramètres du programme Rural', ip: '192.168.1.45' },
  { id: 102, timestamp: '2026-02-14 13:15:44', user: 'Kofi A.', role: 'Chef de Projet', action: 'CREATE', details: 'Création d\'une nouvelle activité : Forage puits N°2', ip: '10.0.0.12' },
  { id: 103, timestamp: '2026-02-14 11:05:22', user: 'Sarah T.', role: 'RH', action: 'DELETE', details: 'Suppression du compte utilisateur externe', ip: '192.168.1.88' },
  { id: 104, timestamp: '2026-02-14 09:45:10', user: 'Amina K.', role: 'Responsable', action: 'LOGIN', details: 'Connexion réussie', ip: '172.16.0.4' },
  { id: 105, timestamp: '2026-02-13 16:20:00', user: 'Admin System', role: 'Super Admin', action: 'EXPORT', details: 'Exportation du rapport Bilan Trimestriel Q1', ip: '192.168.1.45' },
]
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

tbody tr {
  animation: fadeIn 0.3s ease-out both;
}
</style>
