<template>
  <AppLayout>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-white">Objectifs</h2>
      <button
        @click="showCreateGoal = true"
        class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors"
      >
        <span class="text-lg leading-none">+</span> Nouvel objectif
      </button>
    </div>

    <div v-if="loading" class="text-center py-16 text-gray-500">Chargement…</div>

    <div v-else-if="goals.length === 0" class="text-center py-16">
      <p class="text-4xl mb-3">🎯</p>
      <p class="text-gray-400 font-medium">Aucun objectif pour l'instant</p>
      <button @click="showCreateGoal = true" class="mt-3 text-indigo-400 hover:text-indigo-300 text-sm">
        Créer votre premier objectif →
      </button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div
        v-for="goal in goals"
        :key="goal.id"
        class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex flex-col gap-4 hover:border-gray-600 transition-all"
      >
        <!-- Status badge + actions -->
        <div class="flex items-start justify-between gap-2">
          <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="statusClass(goal.status)">
            {{ statusLabel(goal.status) }}
          </span>
          <div class="flex gap-1">
            <button @click="openEditGoal(goal)" class="p-1.5 rounded hover:bg-gray-700 text-gray-500 hover:text-white text-xs transition-colors">✎</button>
            <button @click="deleteGoal(goal)" class="p-1.5 rounded hover:bg-red-900/40 text-gray-500 hover:text-red-400 text-xs transition-colors">✕</button>
          </div>
        </div>

        <!-- Title + description -->
        <div>
          <h3 class="text-base font-semibold text-white">{{ goal.title }}</h3>
          <p v-if="goal.description" class="text-sm text-gray-400 mt-1 line-clamp-2">{{ goal.description }}</p>
        </div>

        <!-- Progress -->
        <div>
          <div class="flex justify-between text-xs text-gray-400 mb-1">
            <span>{{ goal.completed_tasks_count ?? 0 }} / {{ goal.tasks_count ?? 0 }} tâches</span>
            <span class="font-medium text-white">{{ progressOf(goal) }}%</span>
          </div>
          <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-500"
              :class="progressBarClass(progressOf(goal))"
              :style="{ width: progressOf(goal) + '%' }"
            />
          </div>
        </div>

        <!-- Deadline countdown -->
        <div v-if="goal.deadline" class="flex items-center gap-2">
          <span class="text-xs" :class="deadlineClass(goal.deadline)">
            {{ deadlineLabel(goal.deadline) }}
          </span>
        </div>

        <!-- Add task button -->
        <button
          @click="openAddTask(goal)"
          class="w-full py-2 text-xs font-medium rounded-lg border border-dashed border-gray-600 text-gray-400 hover:border-indigo-500 hover:text-indigo-400 transition-all mt-auto"
        >
          + Ajouter une tâche
        </button>
      </div>
    </div>

    <!-- Goal form modal -->
    <Teleport to="body">
      <div v-if="showCreateGoal || editingGoal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeGoalModal" />
        <div class="relative bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-md z-10">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
            <h3 class="text-base font-semibold text-white">{{ editingGoal ? 'Modifier l\'objectif' : 'Nouvel objectif' }}</h3>
            <button @click="closeGoalModal" class="text-gray-400 hover:text-white text-xl leading-none">&times;</button>
          </div>
          <form @submit.prevent="saveGoal" class="px-6 py-4 space-y-4">
            <div>
              <label class="block text-xs font-medium text-gray-400 mb-1">Titre *</label>
              <input v-model="goalForm.title" type="text" required autofocus
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                placeholder="Mon objectif" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-400 mb-1">Description</label>
              <textarea v-model="goalForm.description" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 resize-none" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Deadline</label>
                <input v-model="goalForm.deadline" type="date" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500" />
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-400 mb-1">Statut</label>
                <select v-model="goalForm.status" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                  <option value="active">Actif</option>
                  <option value="completed">Terminé</option>
                  <option value="abandoned">Abandonné</option>
                </select>
              </div>
            </div>
            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="savingGoal"
                class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors">
                {{ savingGoal ? 'Enregistrement…' : (editingGoal ? 'Mettre à jour' : 'Créer') }}
              </button>
              <button type="button" @click="closeGoalModal" class="py-2 px-4 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Add task to goal modal -->
    <TaskModal
      v-if="showTaskModal"
      :prefill="taskPrefill"
      @saved="onTaskSaved"
      @close="showTaskModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import TaskModal from '@/Components/Tasks/TaskModal.vue'
import { useToast } from '@/composables/useToast'

const { success, error } = useToast()

const goals          = ref([])
const loading        = ref(true)
const showCreateGoal = ref(false)
const editingGoal    = ref(null)
const savingGoal     = ref(false)
const showTaskModal  = ref(false)
const taskPrefill    = ref({})

const goalForm = reactive({ title: '', description: '', deadline: '', status: 'active' })

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/v1/goals')
    goals.value = data
  } catch { error('Erreur de chargement') }
  finally { loading.value = false }
}

function openEditGoal(g) {
  editingGoal.value = g
  Object.assign(goalForm, { title: g.title, description: g.description ?? '', deadline: g.deadline ?? '', status: g.status })
}

function closeGoalModal() {
  showCreateGoal.value = false
  editingGoal.value    = null
  Object.assign(goalForm, { title: '', description: '', deadline: '', status: 'active' })
}

async function saveGoal() {
  savingGoal.value = true
  try {
    const payload = { ...goalForm, deadline: goalForm.deadline || null }
    if (editingGoal.value) {
      const { data } = await axios.patch(`/api/v1/goals/${editingGoal.value.id}`, payload)
      const idx = goals.value.findIndex(g => g.id === data.id)
      if (idx >= 0) goals.value.splice(idx, 1, { ...goals.value[idx], ...data })
      success('Objectif mis à jour')
    } else {
      const { data } = await axios.post('/api/v1/goals', payload)
      goals.value.unshift({ ...data, tasks_count: 0, completed_tasks_count: 0 })
      success('Objectif créé')
    }
    closeGoalModal()
  } catch { error('Erreur lors de la sauvegarde') }
  finally { savingGoal.value = false }
}

async function deleteGoal(g) {
  if (!confirm(`Supprimer « ${g.title} » et ses tâches ?`)) return
  try {
    await axios.delete(`/api/v1/goals/${g.id}`)
    goals.value = goals.value.filter(x => x.id !== g.id)
    success('Objectif supprimé')
  } catch { error('Erreur lors de la suppression') }
}

function openAddTask(goal) {
  taskPrefill.value = { goal_id: goal.id }
  showTaskModal.value = true
}

function onTaskSaved() {
  showTaskModal.value = false
  success('Tâche ajoutée')
  load()
}

function progressOf(g) {
  if (!g.tasks_count) return 0
  return Math.round((g.completed_tasks_count / g.tasks_count) * 100)
}

function progressBarClass(p) {
  if (p === 100) return 'bg-emerald-500'
  if (p >= 60)  return 'bg-indigo-500'
  if (p >= 30)  return 'bg-amber-500'
  return 'bg-red-500'
}

function statusLabel(s) {
  return { active: 'Actif', completed: 'Terminé', abandoned: 'Abandonné' }[s] ?? s
}
function statusClass(s) {
  return {
    active:    'bg-indigo-500/20 text-indigo-300',
    completed: 'bg-emerald-500/20 text-emerald-300',
    abandoned: 'bg-gray-500/20 text-gray-400',
  }[s] ?? ''
}

function deadlineLabel(d) {
  const diff = Math.ceil((new Date(d) - new Date()) / 86400000)
  if (diff < 0)  return `Expiré il y a ${Math.abs(diff)}j`
  if (diff === 0) return 'Expire aujourd\'hui !'
  if (diff <= 7) return `Dans ${diff} jour${diff > 1 ? 's' : ''}`
  return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

function deadlineClass(d) {
  const diff = Math.ceil((new Date(d) - new Date()) / 86400000)
  if (diff < 0)  return 'text-red-400'
  if (diff <= 7) return 'text-amber-400'
  return 'text-gray-400'
}

onMounted(load)
</script>
