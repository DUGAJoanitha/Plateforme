<template>
  <div class="kpi-dashboard-wrapper text-charcoal bg-[#E9EEF2] min-h-screen">
    <!-- SideNavBar -->
    <aside class="bg-[#083C44] h-screen w-64 fixed left-0 top-0 shadow-md flex flex-col py-6 z-50">
      <div class="px-6 mb-8 flex flex-col">
        <span class="font-headline-sm text-[20px] leading-[28px] font-semibold text-white">Néo Start</span>
        <span class="font-label-md text-[14px] leading-[20px] font-medium text-white/60">Technology</span>
      </div>
      <nav class="flex-1">
        <a class="bg-[#22C7D6]/20 text-[#22C7D6] rounded-lg mx-2 my-1 px-4 py-3 flex items-center gap-3 transition-colors active:scale-95 duration-150" href="#">
          <span class="material-symbols-outlined">dashboard</span>
          <span class="font-label-md text-[14px] leading-[20px] font-medium">Dashboard</span>
        </a>
        <a class="text-white/70 hover:text-white hover:bg-white/10 rounded-lg mx-2 my-1 px-4 py-3 flex items-center gap-3 transition-colors active:scale-95 duration-150" href="#">
          <span class="material-symbols-outlined">monitoring</span>
          <span class="font-label-md text-[14px] leading-[20px] font-medium">Analytics</span>
        </a>
        <a class="text-white/70 hover:text-white hover:bg-white/10 rounded-lg mx-2 my-1 px-4 py-3 flex items-center gap-3 transition-colors active:scale-95 duration-150" href="#">
          <span class="material-symbols-outlined">description</span>
          <span class="font-label-md text-[14px] leading-[20px] font-medium">Reports</span>
        </a>
        <a class="text-white/70 hover:text-white hover:bg-white/10 rounded-lg mx-2 my-1 px-4 py-3 flex items-center gap-3 transition-colors active:scale-95 duration-150" href="#">
          <span class="material-symbols-outlined">group</span>
          <span class="font-label-md text-[14px] leading-[20px] font-medium">Team</span>
        </a>
        <a class="text-white/70 hover:text-white hover:bg-white/10 rounded-lg mx-2 my-1 px-4 py-3 flex items-center gap-3 transition-colors active:scale-95 duration-150" href="#">
          <span class="material-symbols-outlined">settings</span>
          <span class="font-label-md text-[14px] leading-[20px] font-medium">Settings</span>
        </a>
      </nav>
      <div class="px-4">
        <button class="w-full py-3 rounded-xl bg-brand-teal text-white font-button text-[14px] leading-[20px] font-semibold shadow-sm hover:opacity-90 transition-opacity">
          Upgrade Pro
        </button>
      </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="ml-64 min-h-screen flex flex-col">
      <!-- TopAppBar -->
      <header class="bg-white sticky top-0 h-16 px-6 border-b border-[#D9E1E7] flex justify-between items-center z-40">
        <div class="flex items-center gap-4 bg-[#E9EEF2] px-4 py-2 rounded-full w-96">
          <span class="material-symbols-outlined text-[#6B7280]">search</span>
          <input class="bg-transparent border-none focus:ring-0 w-full text-[14px] leading-[20px] text-[#1F2937]" placeholder="Search..." type="text">
        </div>
        <div class="flex items-center gap-6">
          <button class="text-[#6B7280] hover:bg-[#E9EEF2] p-2 rounded-full transition-all">
            <span class="material-symbols-outlined">notifications</span>
          </button>
          <button class="text-[#6B7280] hover:bg-[#E9EEF2] p-2 rounded-full transition-all">
            <span class="material-symbols-outlined">help</span>
          </button>
          <div class="w-8 h-8 rounded-full overflow-hidden border border-[#D9E1E7] cursor-pointer active:opacity-80">
            <img alt="Profile" data-alt="A professional headshot" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC-u7nR-WDoY5T9euLI3ywoQpwFcoXlsI2aqCQpLhzFBXYT6tbwOXLjn7ouZkLJSRSi6fIg4SDRY8TpwmgxmwknOWIzxyJyToPgQ0xEbvFJ9jlRIX9s7JiVYyIMkES-qcE4wnhrsHGhewtdgQg6P96owh3rAkzpdnI55ihBJdwaLAK88uNF7-NAbDRVNw1lItZs4KP3zWkcVP2gFP82Yvwt_UUfrVMtiUzMHZCEPcK_QmHhiVHN-fGN_9DZSOSnMxq0Q3tcLBzGrhfp">
          </div>
        </div>
      </header>

      <!-- Main Content Area -->
      <main class="p-6 flex-1">
        <!-- Top Filter Section -->
        <section class="flex flex-wrap items-center justify-between gap-4 mb-8">
          <div class="flex items-center gap-4">
            <div class="flex flex-col gap-1">
              <label class="text-[12px] leading-[16px] font-medium text-[#6B7280] uppercase tracking-wider">Projet</label>
              <select class="bg-white border border-[#D9E1E7] rounded-lg px-4 py-2 text-[14px] leading-[20px] text-[#1F2937] focus:border-[#22C7D6] focus:ring-1 focus:ring-[#22C7D6] outline-none">
                <option>Néo Infrastructure Cloud</option>
                <option>Audit Sécurité Alpha</option>
                <option>Migration Base de Données</option>
              </select>
            </div>
            <div class="flex flex-col gap-1">
              <label class="text-[12px] leading-[16px] font-medium text-[#6B7280] uppercase tracking-wider">Période</label>
              <select class="bg-white border border-[#D9E1E7] rounded-lg px-4 py-2 text-[14px] leading-[20px] text-[#1F2937] focus:border-[#22C7D6] focus:ring-1 focus:ring-[#22C7D6] outline-none">
                <option>Derniers 30 jours</option>
                <option>Dernier Trimestre</option>
                <option>Année en cours</option>
              </select>
            </div>
          </div>
          <button class="flex items-center gap-2 bg-brand-teal text-white px-6 py-2.5 rounded-lg font-semibold text-[14px] leading-[20px] shadow-sm active:scale-[0.96] transition-transform">
            <span class="material-symbols-outlined">download</span>
            Export
          </button>
        </section>

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
          <!-- Card 1: Disponibilité -->
          <div class="bg-white p-6 rounded-xl shadow-sm border border-[#D9E1E7] flex flex-col gap-4 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-[#1F2937] text-[20px] leading-[28px] font-semibold">Taux de Disponibilité</h3>
                <span class="inline-block mt-1 px-2 py-0.5 bg-[#E9EEF2] rounded text-[#6B7280] text-[12px] leading-[16px] font-medium">Quotidien</span>
              </div>
              <div class="text-[#22C7D6] opacity-80 group-hover:opacity-100 transition-opacity">
                <svg class="w-20 h-8" viewBox="0 0 100 40">
                  <path d="M0 35 Q 10 32, 20 28 T 40 30 T 60 15 T 80 20 T 100 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2.5"></path>
                </svg>
              </div>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
              <span class="text-[30px] leading-[38px] font-bold text-[#083C44]">99.98%</span>
              <span class="text-[14px] leading-[20px] font-medium text-[#6B7280]">/ Cible 99.9%</span>
            </div>
            <div class="space-y-1.5">
              <div class="flex justify-between text-[12px] leading-[16px] font-medium">
                <span class="text-emerald-600 font-bold">Stable</span>
                <span class="text-[#6B7280]">Progress: 100%</span>
              </div>
              <div class="h-2 w-full bg-[#E9EEF2] rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 ease-out progress-bar" data-width="100%"></div>
              </div>
            </div>
          </div>

          <!-- Card 2: Performance Serveur -->
          <div class="bg-white p-6 rounded-xl shadow-sm border border-[#D9E1E7] flex flex-col gap-4 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-[#1F2937] text-[20px] leading-[28px] font-semibold">Performance Serveur</h3>
                <span class="inline-block mt-1 px-2 py-0.5 bg-[#E9EEF2] rounded text-[#6B7280] text-[12px] leading-[16px] font-medium">Temps Réel</span>
              </div>
              <div class="text-[#22C7D6] opacity-80 group-hover:opacity-100 transition-opacity">
                <svg class="w-20 h-8" viewBox="0 0 100 40">
                  <path d="M0 10 L 20 25 L 40 15 L 60 30 L 80 5 L 100 20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2.5"></path>
                </svg>
              </div>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
              <span class="text-[30px] leading-[38px] font-bold text-[#083C44]">84%</span>
              <span class="text-[14px] leading-[20px] font-medium text-[#6B7280]">/ Cible 75%</span>
            </div>
            <div class="space-y-1.5">
              <div class="flex justify-between text-[12px] leading-[16px] font-medium">
                <span class="text-amber-600 font-bold">Intensif</span>
                <span class="text-[#6B7280]">Charge: 84%</span>
              </div>
              <div class="h-2 w-full bg-[#E9EEF2] rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full transition-all duration-1000 ease-out progress-bar" data-width="84%"></div>
              </div>
            </div>
          </div>

          <!-- Card 3: Budget Consommé -->
          <div class="bg-white p-6 rounded-xl shadow-sm border border-[#D9E1E7] flex flex-col gap-4 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-[#1F2937] text-[20px] leading-[28px] font-semibold">Budget Consommé</h3>
                <span class="inline-block mt-1 px-2 py-0.5 bg-[#E9EEF2] rounded text-[#6B7280] text-[12px] leading-[16px] font-medium">Hebdomadaire</span>
              </div>
              <div class="text-[#22C7D6] opacity-80 group-hover:opacity-100 transition-opacity">
                <svg class="w-20 h-8" viewBox="0 0 100 40">
                  <path d="M0 40 L 100 0" fill="none" stroke="currentColor" stroke-dasharray="4" stroke-linecap="round" stroke-width="2.5"></path>
                </svg>
              </div>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
              <span class="text-[30px] leading-[38px] font-bold text-[#083C44]">42k€</span>
              <span class="text-[14px] leading-[20px] font-medium text-[#6B7280]">/ Cible 50k€</span>
            </div>
            <div class="space-y-1.5">
              <div class="flex justify-between text-[12px] leading-[16px] font-medium">
                <span class="text-emerald-600 font-bold">Optimisé</span>
                <span class="text-[#6B7280]">Consommation: 84%</span>
              </div>
              <div class="h-2 w-full bg-[#E9EEF2] rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 ease-out progress-bar" data-width="84%"></div>
              </div>
            </div>
          </div>

          <!-- Card 4: Satisfaction Client -->
          <div class="bg-white p-6 rounded-xl shadow-sm border border-[#D9E1E7] flex flex-col gap-4 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-[#1F2937] text-[20px] leading-[28px] font-semibold">Satisfaction Client</h3>
                <span class="inline-block mt-1 px-2 py-0.5 bg-[#E9EEF2] rounded text-[#6B7280] text-[12px] leading-[16px] font-medium">Mensuel</span>
              </div>
              <div class="text-[#22C7D6] opacity-80 group-hover:opacity-100 transition-opacity">
                <svg class="w-20 h-8" viewBox="0 0 100 40">
                  <path d="M0 30 C 20 20, 40 35, 60 10 S 80 5, 100 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2.5"></path>
                </svg>
              </div>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
              <span class="text-[30px] leading-[38px] font-bold text-[#083C44]">4.8/5</span>
              <span class="text-[14px] leading-[20px] font-medium text-[#6B7280]">/ Cible 4.5</span>
            </div>
            <div class="space-y-1.5">
              <div class="flex justify-between text-[12px] leading-[16px] font-medium">
                <span class="text-emerald-600 font-bold">Excellent</span>
                <span class="text-[#6B7280]">Objectif: 96%</span>
              </div>
              <div class="h-2 w-full bg-[#E9EEF2] rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 ease-out progress-bar" data-width="96%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Dashboard Secondary Visualization -->
        <section class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 bg-white rounded-2xl p-8 shadow-sm border border-[#D9E1E7] min-h-[300px] flex flex-col">
            <h3 class="text-[24px] leading-[32px] font-semibold text-[#1F2937] mb-8">Répartition des Ressources Cloud</h3>
            <div class="flex-1 flex items-center justify-center relative bg-[#E9EEF2]/30 rounded-xl p-8">
              <div class="absolute inset-0 flex flex-col items-center justify-center opacity-5 pointer-events-none">
                <span class="material-symbols-outlined text-[120px]">cloud</span>
              </div>
              <div class="w-full h-48 flex items-end gap-4">
                <div class="flex-1 bg-[#083C44] rounded-t-lg transition-all hover:scale-105 cursor-pointer" style="height: 60%"></div>
                <div class="flex-1 bg-[#22C7D6] rounded-t-lg transition-all hover:scale-105 cursor-pointer" style="height: 40%"></div>
                <div class="flex-1 bg-[#6B7280] rounded-t-lg transition-all hover:scale-105 cursor-pointer" style="height: 85%"></div>
                <div class="flex-1 bg-[#083C44] rounded-t-lg transition-all hover:scale-105 cursor-pointer" style="height: 55%"></div>
                <div class="flex-1 bg-[#22C7D6] rounded-t-lg transition-all hover:scale-105 cursor-pointer" style="height: 95%"></div>
                <div class="flex-1 bg-[#6B7280] rounded-t-lg transition-all hover:scale-105 cursor-pointer" style="height: 30%"></div>
              </div>
            </div>
            <div class="flex gap-6 mt-6 justify-center">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#083C44]"></div>
                <span class="text-[14px] leading-[20px] font-medium text-[#6B7280]">Compute</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#22C7D6]"></div>
                <span class="text-[14px] leading-[20px] font-medium text-[#6B7280]">Storage</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#6B7280]"></div>
                <span class="text-[14px] leading-[20px] font-medium text-[#6B7280]">Network</span>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-2xl p-8 shadow-sm border border-[#D9E1E7] flex flex-col gap-6">
            <h3 class="text-[24px] leading-[32px] font-semibold text-[#1F2937]">Logs Récents</h3>
            <div class="space-y-4">
              <div class="flex items-center gap-4 border-b border-[#D9E1E7] pb-4">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                  <span class="material-symbols-outlined">check_circle</span>
                </div>
                <div class="flex-1">
                  <p class="text-[14px] leading-[20px] font-semibold text-[#1F2937]">Sauvegarde Automatisée</p>
                  <p class="text-[12px] leading-[16px] font-medium text-[#6B7280]">Terminé avec succès il y a 5 min</p>
                </div>
              </div>
              <div class="flex items-center gap-4 border-b border-[#D9E1E7] pb-4">
                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                  <span class="material-symbols-outlined">warning</span>
                </div>
                <div class="flex-1">
                  <p class="text-[14px] leading-[20px] font-semibold text-[#1F2937]">Pic de Latence</p>
                  <p class="text-[12px] leading-[16px] font-medium text-[#6B7280]">Serveur Cluster-02 (Paris)</p>
                </div>
              </div>
              <div class="flex items-center gap-4 pb-4">
                <div class="w-10 h-10 rounded-full bg-[#083C44]/10 text-[#083C44] flex items-center justify-center">
                  <span class="material-symbols-outlined">person_add</span>
                </div>
                <div class="flex-1">
                  <p class="text-[14px] leading-[20px] font-semibold text-[#1F2937]">Nouveau Rapport</p>
                  <p class="text-[12px] leading-[16px] font-medium text-[#6B7280]">Généré par Julien M.</p>
                </div>
              </div>
            </div>
            <button class="w-full py-2 border border-[#22C7D6] text-[#22C7D6] rounded-lg font-semibold text-[14px] leading-[20px] hover:bg-[#22C7D6]/5 transition-colors">
              Voir tout l'historique
            </button>
          </div>
        </section>
      </main>
    </div>

    <!-- Floating Action Button -->
    <button class="fixed bottom-8 right-8 bg-brand-teal text-white flex items-center gap-3 px-6 py-4 rounded-full shadow-md active:scale-[0.96] transition-transform z-50">
      <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add</span>
      <span class="font-semibold text-[14px] leading-[20px]">Ajouter mesure</span>
    </button>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

definePageMeta({ layout: false });

onMounted(() => {
  const bars = document.querySelectorAll('.progress-bar');
  bars.forEach(bar => {
    const targetWidth = bar.getAttribute('data-width');
    bar.style.width = '0%';
    setTimeout(() => {
      bar.style.width = targetWidth;
    }, 300);
  });
});
</script>

<style scoped>
.kpi-dashboard-wrapper { font-family: 'Inter', sans-serif; }
.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
.sidebar-active { background-color: #acbfff; color: #394c84; }
.glow-accent { box-shadow: 0 0 15px rgba(34, 199, 214, 0.3); }

</style>
