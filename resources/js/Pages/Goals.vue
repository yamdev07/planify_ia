<template>
  <AppLayout>

    <!-- Page header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Objectifs</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ goals.length }} objectif{{ goals.length !== 1 ? 's' : '' }}</p>
      </div>
      <button @click="showCreateGoal = true" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nouvel objectif
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div v-for="i in 3" :key="i" class="card p-5 space-y-4 animate-pulse">
        <div class="h-4 bg-gray-100 dark:bg-gray-800 rounded w-1/2" />
        <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-3/4" />
        <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded" />
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="goals.length === 0" class="card p-14 flex flex-col items-center justify-center text-center">
      <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center mb-4">
        <svg class="w-7 h-7 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/>
        </svg>
      </div>
      <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aucun objectif pour l'instant</p>
      <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Définissez vos objectifs pour mieux organiser vos tâches</p>
      <button @click="showCreateGoal = true" class="btn-primary text-xs">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Créer un objectif
      </button>
    </div>

    <!-- Goals grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div
        v-for="goal in goals"
        :key="goal.id"
        class="card p-5 flex flex-col gap-4 hover:shadow-md transition-shadow"
      >
        <!-- Top row: status badge + actions -->
        <div class="flex items-center justify-between">
          <span class="badge text-[11px] font-medium" :class="statusClass(goal.status)">
            {{ statusLabel(goal.status) }}
          </span>
          <div class="flex gap-1">
            <button
              @click="openEditGoal(goal)"
              class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
              title="Modifier"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
              </svg>
            </button>
            <button
              @click="deleteGoal(goal)"
              class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/40 text-gray-400 hover:text-red-500 transition-colors"
              title="Supprimer"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Title & description -->
        <div>
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-snug">{{ goal.title }}</h3>
          <p v-if="goal.description" class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">
            {{ goal.description }}
          </p>
        </div>

        <!-- Progress -->
        <div>
          <div class="flex justify-between text-xs mb-1.5">
            <span class="text-gray-500 dark:text-gray-400">{{ goal.completed_tasks_count ?? 0 }}/{{ goal.tasks_count ?? 0 }} tâches</span>
            <span class="font-semibold" :class="progressTextColor(progressOf(goal))">{{ progressOf(goal) }}%</span>
          </div>
          <div class="h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div
              class="h-full rounded-full transition-all duration-500"
              :class="progressBarClass(progressOf(goal))"
              :style="{ width: progressOf(goal) + '%' }"
            />
          </div>
        </div>

        <!-- Deadline -->
        <div v-if="goal.deadline" class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 shrink-0" :class="deadlineClass(goal.deadline)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
          </svg>
          <span class="text-xs" :class="deadlineClass(goal.deadline)">{{ deadlineLabel(goal.deadline) }}</span>
        </div>

        <!-- Add task -->
        <button
          @click="openAddTask(goal)"
          class="mt-auto w-full py-2 text-xs font-medium rounded-lg border border-dashed border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 hover:border-indigo-400 dark:hover:border-indigo-600 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all"
        >
          + Ajouter une tâche
        </button>
      </div>
    </div>

    <!-- Goal modal -->
    <Teleport to="body">
      <div v-if="showCreateGoal || editingGoal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm" @click="closeGoalModal" />
        <div class="relative bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl w-full max-w-md z-10">

          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
              {{ editingGoal ? 'Modifier l\'objectif' : 'Nouvel objectif' }}
            </h3>
            <button @click="closeGoalModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors rounded-lg p-1 hover:bg-gray-100 dark:hover:bg-gray-800">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <form @submit.prevent="saveGoal" class="px-6 py-5 space-y-4">
            <div>
              <label class="label">Titre *</label>
              <input v-model="goalForm.title" type="text" required autofocus class="input" placeholder="Mon objectif" />
            </div>
            <div>
              <label class="label">Description</label>
              <textarea v-model="goalForm.description" rows="2" class="input resize-none" placeholder="Description optionnelle…" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="label">Deadline</label>
                <input v-model="goalForm.deadline" type="date" class="input" />
              </div>
              <div>
                <label class="label">Statut</label>
                <select v-model="goalForm.status" class="select">
                  <option value="active">Actif</option>
                  <option value="completed">Terminé</option>
                  <option value="abandoned">Abandonné</option>
                </select>
              </div>
            </div>
            <div class="flex gap-3 pt-2">
              <button type="submit" :disabled="savingGoal" class="btn-primary flex-1 justify-center">
                {{ savingGoal ? 'Enregistrement…' : (editingGoal ? 'Mettre à jour' : 'Créer') }}
              </button>
              <button type="button" @click="closeGoalModal" class="btn-secondary">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

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
const goalForm       = reactive({ title: '', description: '', deadline: '', status: 'active' })

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

function openAddTask(goal) { taskPrefill.value = { goal_id: goal.id }; showTaskModal.value = true }
function onTaskSaved()     { showTaskModal.value = false; success('Tâche ajoutée'); load() }

function progressOf(g) {
  if (!g.tasks_count) return 0
  return Math.round((g.completed_tasks_count / g.tasks_count) * 100)
}

function progressBarClass(p) {
  if (p === 100) return 'bg-emerald-500'
  if (p >= 60)  return 'bg-indigo-500'
  if (p >= 30)  return 'bg-amber-500'
  return 'bg-red-400'
}

function progressTextColor(p) {
  if (p === 100) return 'text-emerald-600 dark:text-emerald-400'
  if (p >= 60)  return 'text-indigo-600 dark:text-indigo-400'
  if (p >= 30)  return 'text-amber-600 dark:text-amber-400'
  return 'text-red-500'
}

function statusLabel(s) {
  return { active: 'Actif', completed: 'Terminé', abandoned: 'Abandonné' }[s] ?? s
}

function statusClass(s) {
  return {
    active:    'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400',
    completed: 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400',
    abandoned: 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400',
  }[s] ?? ''
}

function deadlineLabel(d) {
  const diff = Math.ceil((new Date(d) - new Date()) / 86400000)
  if (diff < 0)   return `Expiré il y a ${Math.abs(diff)}j`
  if (diff === 0) return 'Expire aujourd\'hui'
  if (diff <= 7)  return `Dans ${diff} jour${diff > 1 ? 's' : ''}`
  return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
}

function deadlineClass(d) {
  const diff = Math.ceil((new Date(d) - new Date()) / 86400000)
  if (diff < 0)  return 'text-red-500 dark:text-red-400'
  if (diff <= 7) return 'text-amber-500 dark:text-amber-400'
  return 'text-gray-400 dark:text-gray-500'
}

onMounted(load)
</script>
