<template>
  <AppLayout>
    <!-- Header bar -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-white">Tâches</h2>
      <button
        @click="openCreate"
        class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors"
      >
        <span class="text-lg leading-none">+</span> Nouvelle tâche
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6">
      <select v-model="filters.status" @change="load" class="filter-select">
        <option value="">Tous les statuts</option>
        <option value="todo">À faire</option>
        <option value="in_progress">En cours</option>
        <option value="done">Terminé</option>
      </select>
      <select v-model="filters.priority" @change="load" class="filter-select">
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
        class="filter-select"
        placeholder="Catégorie…"
      />
      <button v-if="hasFilters" @click="clearFilters" class="text-xs text-gray-400 hover:text-white transition-colors px-2">
        ✕ Effacer filtres
      </button>
    </div>

    <!-- Task list grouped by goal -->
    <div v-if="loading" class="text-center py-16 text-gray-500">Chargement…</div>

    <div v-else-if="tasks.length === 0" class="text-center py-16">
      <p class="text-gray-500 text-sm">Aucune tâche trouvée</p>
      <button @click="openCreate" class="mt-3 text-indigo-400 hover:text-indigo-300 text-sm">Créer la première tâche →</button>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="task in tasks"
        :key="task.id"
        class="flex items-center gap-4 p-4 bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all group"
      >
        <!-- Completion toggle -->
        <button
          @click="toggleDone(task)"
          class="w-5 h-5 rounded-full border-2 shrink-0 flex items-center justify-center transition-all"
          :class="task.status === 'done'
            ? 'border-emerald-500 bg-emerald-500 text-white'
            : 'border-gray-500 hover:border-emerald-400'"
        >
          <span v-if="task.status === 'done'" class="text-xs leading-none">✓</span>
        </button>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <p
            class="text-sm font-medium text-white truncate"
            :class="task.status === 'done' ? 'line-through text-gray-500' : ''"
          >{{ task.title }}</p>
          <div class="flex items-center gap-2 mt-0.5">
            <span class="text-xs px-1.5 py-0.5 rounded" :class="priorityClass(task.priority)">
              {{ priorityLabel(task.priority) }}
            </span>
            <span v-if="task.goal" class="text-xs text-gray-400">{{ task.goal.title }}</span>
            <span v-if="task.category" class="text-xs text-gray-500">· {{ task.category }}</span>
            <span v-if="task.scheduled_at" class="text-xs text-gray-500">
              · {{ fmtDate(task.scheduled_at) }}
            </span>
          </div>
        </div>

        <!-- Goal progress bar -->
        <div v-if="task.goal" class="hidden md:block w-24">
          <div class="h-1 bg-gray-700 rounded-full overflow-hidden">
            <div
              class="h-full bg-indigo-500 rounded-full transition-all"
              :style="{ width: goalProgress(task.goal_id) + '%' }"
            />
          </div>
          <p class="text-xs text-gray-500 mt-0.5 text-right">{{ goalProgress(task.goal_id) }}%</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
          <button @click="openEdit(task)" class="p-1.5 rounded hover:bg-gray-700 text-gray-400 hover:text-white text-xs">✎</button>
          <button @click="deleteTask(task)" class="p-1.5 rounded hover:bg-red-900/40 text-gray-400 hover:text-red-400 text-xs">✕</button>
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

const filters = reactive({ status: '', priority: '', category: '' })
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
  } catch {
    error('Erreur de chargement')
  } finally {
    loading.value = false
  }
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
    success(newStatus === 'done' ? 'Tâche complétée ✓' : 'Tâche réouverte')
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
    urgent: 'bg-red-500/20 text-red-300',
    high:   'bg-orange-500/20 text-orange-300',
    medium: 'bg-blue-500/20 text-blue-300',
    low:    'bg-gray-500/20 text-gray-400',
  }[p] ?? 'bg-gray-500/20 text-gray-400'
}

function fmtDate(d) {
  return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}

onMounted(load)
</script>

<style scoped>
.filter-select {
  @apply bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-gray-300
         focus:outline-none focus:border-indigo-500 transition-colors;
}
</style>
