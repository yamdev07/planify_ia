<template>
  <AppLayout>

    <!-- Page header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tâches</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ tasks.length }} tâche{{ tasks.length !== 1 ? 's' : '' }}</p>
      </div>
      <button @click="openCreate" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nouvelle tâche
      </button>
    </div>

    <!-- Filters -->
    <div class="card p-3 mb-5 flex flex-wrap gap-2 items-center">
      <select v-model="filters.status" @change="load" class="select !w-auto text-xs py-1.5">
        <option value="">Tous les statuts</option>
        <option value="todo">À faire</option>
        <option value="in_progress">En cours</option>
        <option value="done">Terminé</option>
      </select>
      <select v-model="filters.priority" @change="load" class="select !w-auto text-xs py-1.5">
        <option value="">Toutes priorités</option>
        <option value="urgent">Urgente</option>
        <option value="high">Haute</option>
        <option value="medium">Moyenne</option>
        <option value="low">Faible</option>
      </select>
      <input
        v-model="filters.category"
        @input="load"
        type="text"
        class="input !w-40 text-xs py-1.5"
        placeholder="Catégorie…"
      />
      <button
        v-if="hasFilters"
        @click="clearFilters"
        class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors px-2 py-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-800"
      >
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Effacer filtres
      </button>
    </div>

    <!-- Loading skeleton -->
    <div v-if="loading" class="space-y-2">
      <div v-for="i in 5" :key="i" class="card p-4 flex items-center gap-4">
        <div class="w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-800 animate-pulse shrink-0" />
        <div class="flex-1 space-y-2">
          <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded animate-pulse w-2/3" />
          <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded animate-pulse w-1/3" />
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="tasks.length === 0" class="card p-12 flex flex-col items-center justify-center text-center">
      <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center mb-4">
        <svg class="w-7 h-7 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aucune tâche trouvée</p>
      <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Créez votre première tâche pour commencer</p>
      <button @click="openCreate" class="btn-primary text-xs">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Créer une tâche
      </button>
    </div>

    <!-- Task list -->
    <div v-else class="space-y-2">
      <div
        v-for="task in tasks"
        :key="task.id"
        class="card px-4 py-3 flex items-center gap-4 hover:shadow-md transition-shadow group"
      >
        <!-- Checkbox -->
        <button
          @click="toggleDone(task)"
          class="w-5 h-5 rounded-full border-2 shrink-0 flex items-center justify-center transition-all duration-150"
          :class="task.status === 'done'
            ? 'border-emerald-500 bg-emerald-500'
            : 'border-gray-300 dark:border-gray-600 hover:border-emerald-400'"
        >
          <svg v-if="task.status === 'done'" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
          </svg>
        </button>

        <!-- Content -->
        <div class="flex-1 min-w-0">
          <p
            class="text-sm font-medium truncate transition-colors"
            :class="task.status === 'done'
              ? 'line-through text-gray-400 dark:text-gray-500'
              : 'text-gray-900 dark:text-white'"
          >{{ task.title }}</p>
          <div class="flex items-center flex-wrap gap-x-2 gap-y-0.5 mt-1">
            <span class="badge text-[11px] font-medium" :class="priorityClass(task.priority)">
              {{ priorityLabel(task.priority) }}
            </span>
            <span v-if="task.goal" class="text-[11px] text-gray-400 dark:text-gray-500">{{ task.goal.title }}</span>
            <span v-if="task.category" class="text-[11px] text-gray-400 dark:text-gray-500">· {{ task.category }}</span>
            <span v-if="task.scheduled_at" class="text-[11px] text-gray-400 dark:text-gray-500">· {{ fmtDate(task.scheduled_at) }}</span>
          </div>
        </div>

        <!-- Goal progress -->
        <div v-if="task.goal" class="hidden md:flex flex-col gap-1 w-20 shrink-0">
          <div class="h-1 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="h-full bg-indigo-500 rounded-full" :style="{ width: goalProgress(task.goal_id) + '%' }" />
          </div>
          <p class="text-[10px] text-gray-400 dark:text-gray-500 text-right">{{ goalProgress(task.goal_id) }}%</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
          <button
            @click="openEdit(task)"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
            title="Modifier"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
            </svg>
          </button>
          <button
            @click="deleteTask(task)"
            class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/40 text-gray-400 hover:text-red-500 transition-colors"
            title="Supprimer"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <TaskModal
      v-if="showModal"
      :task="editingTask"
      @saved="onSaved"
      @close="showModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import TaskModal from '@/Components/Tasks/TaskModal.vue'
import { useToast } from '@/composables/useToast'

const { success, error } = useToast()

const tasks       = ref([])
const goals       = ref([])
const loading     = ref(true)
const showModal   = ref(false)
const editingTask = ref(null)

const filters    = reactive({ status: '', priority: '', category: '' })
const hasFilters = computed(() => filters.status || filters.priority || filters.category)

async function load() {
  loading.value = true
  try {
    const [tasksRes, goalsRes] = await Promise.all([
      axios.get('/api/v1/tasks', { params: filters }),
      axios.get('/api/v1/goals'),
    ])
    tasks.value = tasksRes.data
    goals.value = goalsRes.data
  } catch { error('Erreur de chargement') }
  finally { loading.value = false }
}

function clearFilters() {
  filters.status = ''
  filters.priority = ''
  filters.category = ''
  load()
}

function openCreate() { editingTask.value = null; showModal.value = true }
function openEdit(t)  { editingTask.value = t;    showModal.value = true }

function onSaved(task) {
  const idx = tasks.value.findIndex(t => t.id === task.id)
  idx >= 0 ? tasks.value.splice(idx, 1, task) : tasks.value.unshift(task)
  showModal.value = false
  success(idx >= 0 ? 'Tâche mise à jour' : 'Tâche créée')
}

async function toggleDone(task) {
  const newStatus = task.status === 'done' ? 'todo' : 'done'
  try {
    const { data } = await axios.patch(`/api/v1/tasks/${task.id}`, { status: newStatus })
    Object.assign(task, data)
    success(newStatus === 'done' ? 'Tâche complétée' : 'Tâche réouverte')
  } catch { error('Erreur') }
}

async function deleteTask(task) {
  if (!confirm(`Supprimer « ${task.title} » ?`)) return
  try {
    await axios.delete(`/api/v1/tasks/${task.id}`)
    tasks.value = tasks.value.filter(t => t.id !== task.id)
    success('Tâche supprimée')
  } catch { error('Erreur lors de la suppression') }
}

function goalProgress(goalId) {
  const g = goals.value.find(g => g.id === goalId)
  if (!g || !g.tasks_count) return 0
  return Math.round((g.completed_tasks_count / g.tasks_count) * 100)
}

function priorityLabel(p) {
  return { urgent: 'Urgent', high: 'Haute', medium: 'Moyenne', low: 'Faible' }[p] ?? p
}

function priorityClass(p) {
  return {
    urgent: 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400',
    high:   'bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400',
    medium: 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400',
    low:    'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400',
  }[p] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-500'
}

function fmtDate(d) {
  return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}

onMounted(load)
</script>

