<template>
  <AppLayout>
    <!-- Stats row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div v-for="s in statCards" :key="s.label" class="card p-5 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="s.iconBg">
          <span class="w-5 h-5" :class="s.iconColor" v-html="s.icon" />
        </div>
        <div>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ s.value }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ s.label }}</p>
        </div>
      </div>
    </div>

    <!-- Two columns -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

      <!-- Upcoming tasks -->
      <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Tâches à venir</h3>
          <Link :href="route('tasks')" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Voir tout</Link>
        </div>
        <div v-if="loading" class="space-y-3">
          <div v-for="i in 4" :key="i" class="h-10 bg-gray-100 dark:bg-gray-800 rounded-lg animate-pulse" />
        </div>
        <div v-else-if="!upcomingTasks.length" class="flex flex-col items-center justify-center py-10 text-center">
          <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <p class="text-sm text-gray-500 dark:text-gray-400">Aucune tâche planifiée</p>
        </div>
        <div v-else class="space-y-1">
          <div
            v-for="task in upcomingTasks"
            :key="task.id"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors"
          >
            <span class="w-2 h-2 rounded-full shrink-0" :class="priorityDot(task.priority)" />
            <span class="flex-1 text-sm text-gray-800 dark:text-gray-200 truncate">{{ task.title }}</span>
            <span v-if="task.scheduled_at" class="text-xs text-gray-400 dark:text-gray-500 shrink-0">{{ fmtDate(task.scheduled_at) }}</span>
          </div>
        </div>
      </div>

      <!-- Active goals -->
      <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Objectifs actifs</h3>
          <Link :href="route('goals')" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Voir tout</Link>
        </div>
        <div v-if="loading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="space-y-2">
            <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded animate-pulse w-3/4" />
            <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded animate-pulse" />
          </div>
        </div>
        <div v-else-if="!activeGoals.length" class="flex flex-col items-center justify-center py-10 text-center">
          <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/>
            </svg>
          </div>
          <p class="text-sm text-gray-500 dark:text-gray-400">Aucun objectif actif</p>
        </div>
        <div v-else class="space-y-4">
          <div v-for="goal in activeGoals" :key="goal.id">
            <div class="flex justify-between items-center mb-1.5">
              <span class="text-sm text-gray-800 dark:text-gray-200 font-medium truncate mr-2">{{ goal.title }}</span>
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 shrink-0">{{ progress(goal) }}%</span>
            </div>
            <div class="h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="progressColor(progress(goal))"
                :style="{ width: progress(goal) + '%' }"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const loading       = ref(true)
const upcomingTasks = ref([])
const activeGoals   = ref([])

const taskIcon = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
const goalIcon = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/></svg>'
const fireIcon = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>'

const statCards = computed(() => [
  {
    label:   'Tâches du jour',
    value:   upcomingTasks.value.filter(t => t.scheduled_at?.slice(0, 10) === new Date().toISOString().slice(0, 10)).length,
    icon:    taskIcon,
    iconBg:  'bg-indigo-50 dark:bg-indigo-950/50',
    iconColor: 'text-indigo-600 dark:text-indigo-400',
  },
  {
    label:   'Objectifs actifs',
    value:   activeGoals.value.length,
    icon:    goalIcon,
    iconBg:  'bg-violet-50 dark:bg-violet-950/50',
    iconColor: 'text-violet-600 dark:text-violet-400',
  },
  {
    label:   'Tâches urgentes',
    value:   upcomingTasks.value.filter(t => t.priority === 'urgent').length,
    icon:    fireIcon,
    iconBg:  'bg-red-50 dark:bg-red-950/50',
    iconColor: 'text-red-600 dark:text-red-400',
  },
])

onMounted(async () => {
  const [tasksRes, goalsRes] = await Promise.all([
    axios.get('/api/v1/tasks', { params: { status: 'todo' } }),
    axios.get('/api/v1/goals'),
  ])
  upcomingTasks.value = tasksRes.data.slice(0, 8)
  activeGoals.value   = goalsRes.data.filter(g => g.status === 'active').slice(0, 5)
  loading.value       = false
})

function progress(g) {
  if (!g.tasks_count) return 0
  return Math.round((g.completed_tasks_count / g.tasks_count) * 100)
}

function progressColor(p) {
  if (p === 100) return 'bg-emerald-500'
  if (p >= 60)  return 'bg-indigo-500'
  if (p >= 30)  return 'bg-amber-500'
  return 'bg-red-400'
}

function fmtDate(d) {
  return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
}

function priorityDot(p) {
  return { urgent: 'bg-red-500', high: 'bg-orange-400', medium: 'bg-indigo-400', low: 'bg-gray-300 dark:bg-gray-600' }[p] ?? 'bg-gray-300'
}
</script>
