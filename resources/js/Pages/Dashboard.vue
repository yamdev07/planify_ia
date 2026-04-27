<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <StatCard title="Tâches du jour"  :value="stats.todayTasks"   icon="📋" color="indigo" />
      <StatCard title="Objectifs actifs" :value="stats.activeGoals"  icon="🎯" color="violet" />
      <StatCard title="Tâches urgentes" :value="stats.urgentTasks"  icon="🔴" color="red" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <!-- Upcoming tasks -->
      <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-white mb-4">Tâches à venir</h3>
        <div v-if="loading" class="text-gray-500 text-sm">Chargement…</div>
        <div v-else-if="!upcomingTasks.length" class="text-gray-500 text-sm">Aucune tâche planifiée</div>
        <div v-else class="space-y-2">
          <div v-for="task in upcomingTasks" :key="task.id" class="flex items-center gap-3 py-2 border-b border-gray-700/60 last:border-0">
            <span class="w-2 h-2 rounded-full shrink-0" :class="priorityDot(task.priority)" />
            <span class="flex-1 text-sm text-gray-200 truncate">{{ task.title }}</span>
            <span class="text-xs text-gray-500">{{ fmtDate(task.scheduled_at) }}</span>
          </div>
        </div>
      </div>

      <!-- Active goals -->
      <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-white mb-4">Objectifs actifs</h3>
        <div v-if="loading" class="text-gray-500 text-sm">Chargement…</div>
        <div v-else-if="!activeGoals.length" class="text-gray-500 text-sm">Aucun objectif actif</div>
        <div v-else class="space-y-3">
          <div v-for="goal in activeGoals" :key="goal.id">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-200 truncate">{{ goal.title }}</span>
              <span class="text-gray-400 ml-2 shrink-0">{{ progress(goal) }}%</span>
            </div>
            <div class="h-1.5 bg-gray-700 rounded-full overflow-hidden">
              <div class="h-full bg-indigo-500 rounded-full transition-all" :style="{ width: progress(goal) + '%' }" />
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
import AppLayout from '@/Layouts/AppLayout.vue'

const loading      = ref(true)
const upcomingTasks = ref([])
const activeGoals   = ref([])

const stats = computed(() => ({
  todayTasks:  upcomingTasks.value.filter(t => t.scheduled_at?.slice(0, 10) === new Date().toISOString().slice(0, 10)).length,
  activeGoals: activeGoals.value.length,
  urgentTasks: upcomingTasks.value.filter(t => t.priority === 'urgent').length,
}))

onMounted(async () => {
  const [tasksRes, goalsRes] = await Promise.all([
    axios.get('/api/v1/tasks', { params: { status: 'todo' } }),
    axios.get('/api/v1/goals'),
  ])
  upcomingTasks.value = tasksRes.data.slice(0, 8)
  activeGoals.value   = goalsRes.data.filter(g => g.status === 'active').slice(0, 5)
  loading.value = false
})

function progress(g) {
  if (!g.tasks_count) return 0
  return Math.round((g.completed_tasks_count / g.tasks_count) * 100)
}
function fmtDate(d) {
  if (!d) return ''
  return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
}
function priorityDot(p) {
  return { urgent: 'bg-red-500', high: 'bg-orange-400', medium: 'bg-blue-400', low: 'bg-gray-500' }[p] ?? 'bg-gray-500'
}
</script>

<script>
// Inline stat card to keep file self-contained
import { defineComponent, h } from 'vue'
const StatCard = defineComponent({
  props: { title: String, value: Number, icon: String, color: String },
  setup(p) {
    const colorMap = {
      indigo: 'from-indigo-500/20 to-indigo-600/10 border-indigo-500/30 text-indigo-400',
      violet: 'from-violet-500/20 to-violet-600/10 border-violet-500/30 text-violet-400',
      red:    'from-red-500/20 to-red-600/10 border-red-500/30 text-red-400',
    }
    return () => h('div', {
      class: `bg-gradient-to-br ${colorMap[p.color]} border rounded-xl p-5 flex items-center gap-4`,
    }, [
      h('span', { class: 'text-3xl' }, p.icon),
      h('div', {}, [
        h('p', { class: 'text-2xl font-bold text-white' }, p.value ?? 0),
        h('p', { class: 'text-sm text-gray-400 mt-0.5' }, p.title),
      ]),
    ])
  },
})
export default { components: { StatCard } }
</script>
