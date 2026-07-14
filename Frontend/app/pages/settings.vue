<template>
  <div class="max-w-5xl mx-auto p-6 md:p-8 space-y-6">
    <header class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Paramètres de l'espace de travail</h1>
      <p class="text-sm text-gray-500 mt-1">Gérez vos préférences personnelles et les configurations de l'organisation.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
      <!-- Secondary Vertical Sub-Nav -->
      <aside class="md:col-span-3">
        <nav class="flex flex-col gap-1 sticky top-6">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="activeTab = tab.id"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 text-left w-full"
            :class="activeTab === tab.id ? 'bg-[#007a82]/10 text-[#007a82] font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800'"
          >
            <LucideIcon :name="tab.icon" class="w-5 h-5 flex-shrink-0" />
            <span>{{ tab.name }}</span>
          </button>
        </nav>
      </aside>

      <!-- Content Area -->
      <div class="md:col-span-9 space-y-6">
        <!-- Section: Profile -->
        <section v-if="activeTab === 'profile'" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Profil Personnel</h2>
            <button class="bg-[#007a82] text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-opacity-90 transition-all active:scale-95">
              Enregistrer
            </button>
          </div>
          
          <div class="flex items-center gap-6 pb-6 border-b border-gray-100">
            <div class="relative group cursor-pointer">
              <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-gray-100 bg-gray-50 flex items-center justify-center">
                <img v-if="profile.photo" :src="profile.photo" alt="Photo de profil" class="w-full h-full object-cover">
                <LucideIcon v-else name="User" class="w-8 h-8 text-gray-400" />
              </div>
              <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <LucideIcon name="Camera" class="w-5 h-5 text-white" />
              </div>
            </div>
            <div class="space-y-1">
              <h4 class="text-sm font-semibold text-gray-800">Photo de profil</h4>
              <p class="text-xs text-gray-400">JPG, GIF ou PNG. Taille max de 2 Mo.</p>
              <div class="flex gap-3 pt-1">
                <button class="text-[#007a82] text-xs font-semibold hover:underline">Importer</button>
                <button class="text-red-500 text-xs font-semibold hover:underline">Supprimer</button>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1.5">
              <label class="text-xs font-semibold text-gray-700">Nom complet</label>
              <input v-model="profile.name" type="text" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:border-[#007a82] focus:ring-2 focus:ring-[#007a82]/10 outline-none transition-all" />
            </div>
            <div class="space-y-1.5">
              <label class="text-xs font-semibold text-gray-700">Adresse e-mail</label>
              <input v-model="profile.email" type="email" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:border-[#007a82] focus:ring-2 focus:ring-[#007a82]/10 outline-none transition-all" />
            </div>
            <div class="col-span-1 md:col-span-2 space-y-1.5">
              <label class="text-xs font-semibold text-gray-700">Bio professionnelle</label>
              <textarea v-model="profile.bio" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:border-[#007a82] focus:ring-2 focus:ring-[#007a82]/10 outline-none transition-all"></textarea>
            </div>
          </div>
        </section>

        <!-- Section: Security -->
        <section v-if="activeTab === 'security'" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-6">
          <h2 class="text-lg font-bold text-gray-900">Sécurité & Authentification</h2>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
              <div class="flex items-center gap-4">
                <div class="bg-[#007a82]/10 text-[#007a82] p-2.5 rounded-xl flex items-center justify-center">
                  <LucideIcon name="Key" class="w-5 h-5" />
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-800">Mot de passe</p>
                  <p class="text-xs text-gray-400">Modifié il y a 4 mois</p>
                </div>
              </div>
              <button class="border border-gray-200 bg-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-gray-50 transition-colors">
                Mettre à jour
              </button>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
              <div class="flex items-center gap-4">
                <div class="bg-orange-50 text-orange-600 p-2.5 rounded-xl flex items-center justify-center">
                  <LucideIcon name="Smartphone" class="w-5 h-5" />
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-800">Authentification à deux facteurs (2FA)</p>
                  <p class="text-xs text-gray-400">Protégez votre compte avec une couche de sécurité supplémentaire</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-[#007a82]">Activé</span>
                <button 
                  @click="security.twoFactor = !security.twoFactor"
                  class="w-11 h-6 flex items-center rounded-full p-1 cursor-pointer transition-all duration-300"
                  :class="security.twoFactor ? 'bg-[#007a82]' : 'bg-gray-300'"
                >
                  <div class="bg-white w-4 h-4 rounded-full shadow-md transform transition-all duration-300" :class="{ 'translate-x-5': security.twoFactor }"></div>
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- Section: Notifications -->
        <section v-if="activeTab === 'notifications'" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-6">
          <h2 class="text-lg font-bold text-gray-900">Préférences de notifications</h2>
          <div class="space-y-4">
            <div v-for="notif in notifications" :key="notif.id" class="flex items-start justify-between py-3 border-b border-gray-100 last:border-0">
              <div class="space-y-1 pr-4">
                <p class="text-sm font-semibold text-gray-800">{{ notif.title }}</p>
                <p class="text-xs text-gray-400">{{ notif.description }}</p>
              </div>
              <button 
                @click="notif.enabled = !notif.enabled"
                class="w-11 h-6 flex items-center rounded-full p-1 cursor-pointer transition-all duration-300 flex-shrink-0"
                :class="notif.enabled ? 'bg-[#007a82]' : 'bg-gray-300'"
              >
                <div class="bg-white w-4 h-4 rounded-full shadow-md transform transition-all duration-300" :class="{ 'translate-x-5': notif.enabled }"></div>
              </button>
            </div>
          </div>
        </section>

        <!-- Section: Team & Permissions -->
        <section v-if="activeTab === 'team'" class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-6">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Membres de l'équipe</h2>
            <button class="bg-[#007a82] text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-opacity-90 transition-all flex items-center gap-2 active:scale-95">
              <LucideIcon name="UserPlus" class="w-4 h-4" />
              Inviter un membre
            </button>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="member in team" :key="member.email" class="p-4 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img :src="member.avatar" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                <div>
                  <p class="text-sm font-semibold text-gray-800">{{ member.name }}</p>
                  <p class="text-xs text-gray-400">{{ member.email }}</p>
                </div>
              </div>
              <span class="text-xs px-2.5 py-1 rounded-full font-semibold" :class="member.role === 'Admin' ? 'bg-[#007a82]/10 text-[#007a82]' : 'bg-gray-100 text-gray-600'">
                {{ member.role }}
              </span>
            </div>
          </div>
        </section>

        <!-- Section: Workspace -->
        <section v-if="activeTab === 'workspace'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="bg-[#007a82]/10 text-[#007a82] p-2.5 rounded-xl w-fit flex items-center justify-center">
              <LucideIcon name="Database" class="w-5 h-5" />
            </div>
            <h3 class="text-base font-bold text-gray-900">Résidence des données</h3>
            <p class="text-xs text-gray-400">Choisissez l'emplacement de stockage principal pour vos données d'entreprise.</p>
            <select v-model="workspace.residency" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:border-[#007a82] focus:ring-2 focus:ring-[#007a82]/10 outline-none transition-all">
              <option value="US">États-Unis (Est)</option>
              <option value="EU">Europe (Francfort)</option>
              <option value="APAC">Asie-Pacifique (Singapour)</option>
            </select>
          </div>
          <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
              <div class="bg-purple-50 text-purple-600 p-2.5 rounded-xl w-fit flex items-center justify-center">
                <LucideIcon name="FileImage" class="w-5 h-5" />
              </div>
              <h3 class="text-base font-bold text-gray-900">Marque blanche (Whitelabeling)</h3>
              <p class="text-xs text-gray-400">Personnalisez l'identité visuelle de vos rapports et tableaux de bord externes.</p>
            </div>
            <button class="w-full border border-[#007a82] text-[#007a82] px-4 py-2 rounded-xl text-xs font-semibold hover:bg-[#007a82] hover:text-white transition-all">
              Configurer les ressources
            </button>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

definePageMeta({ layout: 'default' })

const activeTab = ref('profile')

const tabs = [
  { id: 'profile', name: 'Profil', icon: 'User' },
  { id: 'security', name: 'Sécurité', icon: 'Shield' },
  { id: 'notifications', name: 'Notifications', icon: 'Bell' },
  { id: 'team', name: 'Équipe & Droits', icon: 'Users' },
  { id: 'workspace', name: 'Espace de travail', icon: 'Settings2' },
]

const profile = ref({
  name: 'Alex Rivera',
  email: 'alex.rivera@neostart.tech',
  bio: 'Responsable Produit Senior chez Néo Start Technology. Passionné par les solutions d\'entreprise et l\'expérience utilisateur.',
  photo: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAPNJpmEpiXfiUftqFqu0VpwNtsh0Y9bhF3T7nkFThKyA2LlBIFAJXmyOR7NczboBKToIwaNsD4QpOY6VPxcdzSi1albq-MQv1T01Tk7KlJI33V4yYBbwlzvvSzk50Bm8AxpTtv2fN6GBS57zne8UGfWszTSN8CTkFPj9B4gs4WEjjeoQ9-rPK3TtM50gGXOdDL9MTw53mBvZqJagcmclknp9G1eB8EasLoTcdnaSwDlkTO24X8NuMv83WJ9sNgr1mLC-DKG5Adg1a9'
})

const security = ref({
  twoFactor: true
})

const notifications = ref([
  { id: 'email_alerts', title: 'Alertes budgétaires par e-mail', description: 'Recevoir un e-mail lorsque les dépenses dépassent 80 % du budget d\'un projet.', enabled: true },
  { id: 'weekly_report', title: 'Rapport hebdomadaire', description: 'Recevoir un résumé hebdomadaire de l\'avancement des projets et des dépenses par e-mail.', enabled: true },
  { id: 'push_tasks', title: 'Mises à jour des tâches', description: 'Recevoir des notifications push lorsque de nouvelles tâches vous sont attribuées.', enabled: false },
])

const team = ref([
  { name: 'Sarah Chen', email: 'sarah@neostart.tech', role: 'Éditeur', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDPxD5Kb61uGYznSQ0bDMKF2fPUPI9MWuJknoZQ4l0igaZqvRHOg6RbBwg1cQ1cyJ2BGUPexhSEXydP8vLbyuQq5tC1W4aFHm5kcsvoBIitYeBDUWWTrqvBVy3mFDILR-98g1WQ5UJgZG8BmT4RyH8nNUEc_4z9FOe6FoY3dEmtIhTka6LUh3sJyoOv-U5GP2h3KX9pMU_PJOBRBF-5iAMuFcL4aK6ocFD5t3oMqV7rdpnzctkf2yg0JQGrDC0_qAUZhWGgHQRnLVc2' },
  { name: 'James Wilson', email: 'james@neostart.tech', role: 'Admin', avatar: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBrmaSvd0FvTMWBofobcZkFwlHfKSlN_D-YEx8Nqf2gyFw4p5VnnWEjUo7-rnBG3_l8o4YvOt8x_2z7xBHfyMrSiyOUeuGLjrpF74Pff3YM0AfjshMOKCWAxeLc6BZgX5jlZs5mHaleh1bp_3tyb1D0NCYzEoQc9W7uvnxvY6hSEPsxwRC_ULP8P3k3ewgDxCPN8HW1C4udx6ZXm3-b_iZuaaEn5JccIHSFhD-F-IveCkBNVgKhDNTQhFTMwTvcVZ_e2P3mB20VimmI' }
])

const workspace = ref({
  residency: 'EU'
})
</script>
