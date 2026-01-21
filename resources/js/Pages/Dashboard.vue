<!-- resources/js/Pages/Dashboard.vue -->
<template>
  <AppLayout title="Dashboard" subtitle="Vue d'ensemble de vos projets">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Tâches du jour</p>
            <p class="text-2xl font-bold text-gray-800">8</p>
          </div>
          <div class="p-3 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">En retard</p>
            <p class="text-2xl font-bold text-red-600">2</p>
          </div>
          <div class="p-3 bg-red-100 rounded-lg">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Projets actifs</p>
            <p class="text-2xl font-bold text-gray-800">5</p>
          </div>
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Productivité</p>
            <p class="text-2xl font-bold text-gray-800">78%</p>
          </div>
          <div class="p-3 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Grille principale -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Tâches récentes -->
      <div class="lg:col-span-2">
        <div class="card">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">📋 Tâches récentes</h3>
            <a :href="route('tasks.index')" class="text-sm text-primary-600 font-medium">Voir tout</a>
          </div>
          
          <div class="space-y-3">
            <div v-for="task in tasks" :key="task.id" 
                 class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-primary-300 transition-colors">
              
              <div class="flex items-start space-x-3 w-full sm:w-auto">
                <input type="checkbox" class="mt-1 rounded text-primary-600">
                <div>
                  <p class="font-medium text-gray-800">{{ task.title }}</p>
                  <div class="flex items-center space-x-2 mt-1">
                    <span class="text-xs px-2 py-1 rounded-full" :class="task.project.color">{{ task.project.name }}</span>
                    <span class="text-xs text-gray-500">• {{ task.time }}</span>
                  </div>
                </div>
              </div>
              
              <div class="flex items-center justify-between w-full sm:w-auto mt-3 sm:mt-0">
                <div class="flex items-center space-x-2">
                  <span class="text-xs px-3 py-1 rounded-full" :class="task.priorityClass">
                    {{ task.priority }}
                  </span>
                  <img class="w-6 h-6 rounded-full" :src="task.assignee.avatar" :alt="task.assignee.name">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar droite -->
      <div class="space-y-6">
        <!-- Projets -->
        <div class="card">
          <h3 class="text-lg font-semibold mb-4">📂 Projets actifs</h3>
          <div class="space-y-3">
            <div v-for="project in projects" :key="project.id"
                 class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 cursor-pointer">
              <div class="flex items-center">
                <div class="w-3 h-3 rounded-full mr-3" :class="project.color"></div>
                <div>
                  <p class="font-medium">{{ project.name }}</p>
                  <p class="text-sm text-gray-500">{{ project.progress }}% complété</p>
                </div>
              </div>
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Suggestions IA -->
        <div class="card bg-gradient-to-r from-primary-50 to-blue-50 border border-primary-100">
          <div class="flex">
            <div class="bg-primary-100 p-3 rounded-lg mr-4">
              <span class="text-2xl">🤖</span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800 mb-2">Suggestion IA</h3>
              <p class="text-gray-700 text-sm mb-3">Commencez par "Réunion client" à 9h pour optimiser votre journée.</p>
              <button class="text-primary-600 text-sm font-medium hover:text-primary-700">
                Appliquer →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const tasks = [
  {
    id: 1,
    title: "Réunion avec l'équipe design",
    project: { name: "Site Web", color: "bg-blue-100 text-blue-800" },
    time: "9:00 - 10:00",
    priority: "Haute",
    priorityClass: "bg-red-100 text-red-800",
    assignee: { name: "Sarah", avatar: "https://ui-avatars.com/api/?name=Sarah+Chen" }
  },
  {
    id: 2,
    title: "Développer API user",
    project: { name: "Mobile App", color: "bg-green-100 text-green-800" },
    time: "10:30 - 12:30",
    priority: "Moyenne",
    priorityClass: "bg-yellow-100 text-yellow-800",
    assignee: { name: "Vous", avatar: "https://ui-avatars.com/api/?name=John+Doe" }
  },
  {
    id: 3,
    title: "Rédiger documentation",
    project: { name: "Site Web", color: "bg-blue-100 text-blue-800" },
    time: "14:00 - 15:30",
    priority: "Basse",
    priorityClass: "bg-gray-100 text-gray-800",
    assignee: { name: "Marie", avatar: "https://ui-avatars.com/api/?name=Marie+L" }
  }
]

const projects = [
  { id: 1, name: "Site Web Client", color: "bg-blue-500", progress: 75 },
  { id: 2, name: "Application Mobile", color: "bg-green-500", progress: 40 },
  { id: 3, name: "Refonte Dashboard", color: "bg-purple-500", progress: 90 },
]
</script>