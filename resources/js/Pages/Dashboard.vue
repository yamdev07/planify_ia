<template>
  <AppLayout title="Dashboard" subtitle="Vue d'ensemble de vos projets">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="card">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm">Projets actifs</p>
            <h3 class="text-2xl font-bold mt-1">12</h3>
          </div>
          <div class="bg-primary-100 p-3 rounded-lg">
            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <a href="/projects" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            Voir tous les projets →
          </a>
        </div>
      </div>

      <div class="card">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm">Tâches en cours</p>
            <h3 class="text-2xl font-bold mt-1">24</h3>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <a href="/tasks" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            Voir les tâches →
          </a>
        </div>
      </div>

      <div class="card">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm">Échéances cette semaine</p>
            <h3 class="text-2xl font-bold mt-1">8</h3>
          </div>
          <div class="bg-yellow-100 p-3 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <a href="/calendar" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            Voir le calendrier →
          </a>
        </div>
      </div>

      <div class="card">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-500 text-sm">Membres d'équipe</p>
            <h3 class="text-2xl font-bold mt-1">6</h3>
          </div>
          <div class="bg-purple-100 p-3 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-7.644a21.027 21.027 0 01-3.432 8.362" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <button class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            Gérer l'équipe →
          </button>
        </div>
      </div>
    </div>

    <!-- Projets récents -->
    <div class="card mb-8">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 class="text-lg font-semibold">Projets récents</h3>
          <p class="text-gray-500 text-sm">Vos 5 derniers projets</p>
        </div>
        <a href="/projects" class="btn-secondary">
          Voir tous les projets
        </a>
      </div>

      <div class="space-y-4">
        <!-- Liste des projets -->
        <div v-for="project in recentProjects" :key="project.id" class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
          <div class="flex items-center space-x-4">
            <div :class="`p-3 rounded-lg ${project.color}`">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
              </svg>
            </div>
            <div>
              <h4 class="font-medium">{{ project.name }}</h4>
              <p class="text-gray-500 text-sm">{{ project.tasks }} tâches • {{ project.progress }}% complété</p>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <span class="badge" :class="project.statusClass">{{ project.status }}</span>
            <button class="p-2 hover:bg-gray-100 rounded-lg">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tâches à venir -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="card">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-lg font-semibold">Tâches à venir</h3>
            <p class="text-gray-500 text-sm">Échéances des 7 prochains jours</p>
          </div>
          <a href="/tasks" class="btn-secondary">
            Voir toutes les tâches
          </a>
        </div>

        <div class="space-y-4">
          <div v-for="task in upcomingTasks" :key="task.id" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
            <div class="flex items-center space-x-3">
              <input type="checkbox" class="rounded border-gray-300">
              <div>
                <h4 class="font-medium">{{ task.title }}</h4>
                <p class="text-gray-500 text-sm">{{ task.project }} • Échéance: {{ task.dueDate }}</p>
              </div>
            </div>
            <span class="badge" :class="task.priorityClass">{{ task.priority }}</span>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-lg font-semibold">Activité récente</h3>
        </div>

        <div class="space-y-4">
          <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start space-x-3">
            <img :src="activity.avatar" class="w-8 h-8 rounded-full" :alt="activity.user">
            <div class="flex-1">
              <p class="text-sm">
                <span class="font-medium">{{ activity.user }}</span> {{ activity.action }}
                <span class="font-medium">{{ activity.target }}</span>
              </p>
              <p class="text-gray-500 text-xs mt-1">{{ activity.time }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
// IMPORTANT: Vérifiez le bon chemin selon votre structure
// Si vous avez renommé en AppLayouts :
import AppLayout from '../AppLayouts/AppLayout.vue'
// Si vous avez gardé Layouts :
// import AppLayout from '../Layouts/AppLayout.vue'

// Données temporaires
const recentProjects = [
  { 
    id: 1, 
    name: 'Refonte site web', 
    tasks: 12, 
    progress: 75, 
    status: 'En cours', 
    statusClass: 'badge-primary', 
    color: 'bg-blue-500' 
  },
  { 
    id: 2, 
    name: 'Application mobile', 
    tasks: 8, 
    progress: 30, 
    status: 'En retard', 
    statusClass: 'badge-danger', 
    color: 'bg-red-500' 
  },
  { 
    id: 3, 
    name: 'Campagne marketing', 
    tasks: 15, 
    progress: 90, 
    status: 'Bientôt terminé', 
    statusClass: 'badge-success', 
    color: 'bg-green-500' 
  },
]

const upcomingTasks = [
  { 
    id: 1, 
    title: 'Design des interfaces', 
    project: 'Refonte site web', 
    dueDate: 'Demain', 
    priority: 'Haute', 
    priorityClass: 'badge-danger' 
  },
  { 
    id: 2, 
    title: 'Tests utilisateurs', 
    project: 'Application mobile', 
    dueDate: '3 jours', 
    priority: 'Moyenne', 
    priorityClass: 'badge-warning' 
  },
  { 
    id: 3, 
    title: 'Rapport mensuel', 
    project: 'Général', 
    dueDate: '5 jours', 
    priority: 'Basse', 
    priorityClass: 'badge-primary' 
  },
]

const recentActivities = [
  { 
    id: 1, 
    user: 'Marie Dupont', 
    action: 'a terminé la tâche', 
    target: '"Design logo"', 
    time: 'Il y a 2 heures', 
    avatar: 'https://ui-avatars.com/api/?name=Marie+Dupont&background=3b82f6&color=fff' 
  },
  { 
    id: 2, 
    user: 'Jean Martin', 
    action: 'a commenté sur', 
    target: '"Réunion équipe"', 
    time: 'Il y a 4 heures', 
    avatar: 'https://ui-avatars.com/api/?name=Jean+Martin&background=10b981&color=fff' 
  },
  { 
    id: 3, 
    user: 'Sophie Leroy', 
    action: 'a créé le projet', 
    target: '"Campagne Q4"', 
    time: 'Il y a 1 jour', 
    avatar: 'https://ui-avatars.com/api/?name=Sophie+Leroy&background=8b5cf6&color=fff' 
  },
]
</script>

<style scoped>
/* Styles pour les classes utilitaires personnalisées - SANS @apply */
.card {
  background-color: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  padding: 1.5rem;
}

.btn-primary {
  background-color: rgb(37 99 235);
  color: white;
  padding-left: 1rem;
  padding-right: 1rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: rgb(29 78 216);
}

.btn-secondary {
  background-color: rgb(229 231 235);
  color: rgb(31 41 55);
  padding-left: 1rem;
  padding-right: 1rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-secondary:hover {
  background-color: rgb(209 213 219);
}

.badge {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 500;
}

.badge-primary {
  background-color: rgb(219 234 254);
  color: rgb(30 64 175);
}

.badge-success {
  background-color: rgb(209 250 229);
  color: rgb(6 95 70);
}

.badge-warning {
  background-color: rgb(254 243 199);
  color: rgb(146 64 14);
}

.badge-danger {
  background-color: rgb(254 226 226);
  color: rgb(153 27 27);
}
</style>