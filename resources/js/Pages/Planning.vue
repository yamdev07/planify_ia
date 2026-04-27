<template>
  <AppLayout>
    <div class="flex flex-col h-[calc(100vh-112px)] -m-6">

      <!-- Toolbar -->
      <div class="flex items-center justify-between px-6 py-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shrink-0">
        <div class="flex items-center gap-1.5">
          <button
            @click="prev"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
          </button>
          <button
            @click="today"
            class="btn-ghost text-xs py-1 px-3"
          >Aujourd'hui</button>
          <button
            @click="next"
            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
          </button>
          <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ periodLabel }}</span>
        </div>

        <div class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-lg">
          <button
            v-for="v in views"
            :key="v.key"
            @click="currentView = v.key"
            class="px-3 py-1 text-xs font-medium rounded-md transition-all"
            :class="currentView === v.key
              ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
              : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
          >{{ v.label }}</button>
        </div>
      </div>

      <!-- Calendar -->
      <div class="flex-1 overflow-hidden">
        <WeekView
          v-if="currentView === 'week'"
          :week-start="weekStart"
          :tasks="tasks"
          :events="events"
          @task-moved="handleTaskMoved"
          @slot-click="handleSlotClick"
          @task-click="openTask"
        />
        <MonthView
          v-else
          :current-month="currentMonth"
          :tasks="tasks"
          :events="events"
          @day-click="goToWeekOf"
          @task-click="openTask"
        />
      </div>
    </div>

    <TaskModal
      v-if="showModal"
      :task="editingTask"
      :prefill="modalPrefill"
      @saved="onTaskSaved"
      @close="showModal = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import WeekView  from '@/Components/Agenda/WeekView.vue'
import MonthView from '@/Components/Agenda/MonthView.vue'
import TaskModal from '@/Components/Tasks/TaskModal.vue'
import { useToast } from '@/composables/useToast'

const { success, error } = useToast()

const currentView  = ref('week')
const cursor       = ref(new Date())
const tasks        = ref([])
const events       = ref([])
const showModal    = ref(false)
const editingTask  = ref(null)
const modalPrefill = ref({})

const views = [
  { key: 'week',  label: 'Semaine' },
  { key: 'month', label: 'Mois' },
]

const weekStart = computed(() => {
  const d   = new Date(cursor.value)
  const day = d.getDay()
  d.setDate(d.getDate() + (day === 0 ? -6 : 1 - day))
  return d.toISOString().slice(0, 10)
})

const currentMonth = computed(() => cursor.value.toISOString().slice(0, 7))

const periodLabel = computed(() => {
  if (currentView.value === 'week') {
    const start = new Date(weekStart.value)
    const end   = new Date(start)
    end.setDate(end.getDate() + 6)
    return `${fmt(start)} – ${fmt(end)}`
  }
  return cursor.value.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })
})

function fmt(d) {
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
}

function prev() {
  const d = new Date(cursor.value)
  currentView.value === 'week' ? d.setDate(d.getDate() - 7) : d.setMonth(d.getMonth() - 1)
  cursor.value = d
  load()
}
function next() {
  const d = new Date(cursor.value)
  currentView.value === 'week' ? d.setDate(d.getDate() + 7) : d.setMonth(d.getMonth() + 1)
  cursor.value = d
  load()
}
function today() { cursor.value = new Date(); load() }

async function load() {
  try {
    const { data } = await axios.get('/api/v1/planning/week', { params: { date: weekStart.value } })
    tasks.value  = data.tasks
    events.value = data.events
  } catch { error('Impossible de charger le planning') }
}

async function handleTaskMoved({ task, scheduled_at }) {
  try {
    await axios.patch(`/api/v1/tasks/${task.id}`, { scheduled_at })
    const t = tasks.value.find(t => t.id === task.id)
    if (t) t.scheduled_at = scheduled_at
    success('Tâche déplacée')
  } catch { error('Erreur lors du déplacement') }
}

function handleSlotClick({ date }) {
  editingTask.value  = null
  modalPrefill.value = { scheduled_at: date }
  showModal.value    = true
}

function openTask(task) {
  editingTask.value  = task
  modalPrefill.value = {}
  showModal.value    = true
}

function goToWeekOf(iso) {
  cursor.value      = new Date(iso)
  currentView.value = 'week'
  load()
}

function onTaskSaved(task) {
  const idx = tasks.value.findIndex(t => t.id === task.id)
  idx >= 0 ? tasks.value.splice(idx, 1, task) : tasks.value.push(task)
  showModal.value = false
  success('Tâche enregistrée')
}

onMounted(load)
</script>
