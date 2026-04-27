<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm" @click="$emit('close')" />

      <div class="relative bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl w-full max-w-md z-10">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
            {{ task ? 'Modifier la tâche' : 'Nouvelle tâche' }}
          </h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="save" class="px-6 py-5 space-y-4">
          <div>
            <label class="label">Titre *</label>
            <input
              v-model="form.title"
              type="text"
              class="input"
              placeholder="Nom de la tâche"
              required
              autofocus
            />
          </div>

          <div>
            <label class="label">Description</label>
            <textarea
              v-model="form.description"
              rows="2"
              class="input resize-none"
              placeholder="Détails optionnels…"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label">Priorité</label>
              <select v-model="form.priority" class="select">
                <option value="low">Faible</option>
                <option value="medium">Moyenne</option>
                <option value="high">Haute</option>
                <option value="urgent">Urgente</option>
              </select>
            </div>
            <div>
              <label class="label">Statut</label>
              <select v-model="form.status" class="select">
                <option value="todo">À faire</option>
                <option value="in_progress">En cours</option>
                <option value="done">Terminé</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label">Date planifiée</label>
              <input v-model="form.scheduled_at" type="datetime-local" class="input" />
            </div>
            <div>
              <label class="label">Durée (min)</label>
              <input
                v-model.number="form.duration_minutes"
                type="number"
                min="5"
                step="5"
                class="input"
                placeholder="30"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label">Objectif</label>
              <select v-model="form.goal_id" class="select">
                <option :value="null">Aucun</option>
                <option v-for="g in goals" :key="g.id" :value="g.id">{{ g.title }}</option>
              </select>
            </div>
            <div>
              <label class="label">Catégorie</label>
              <input
                v-model="form.category"
                type="text"
                class="input"
                placeholder="Ex: Travail"
              />
            </div>
          </div>

          <!-- Validation errors -->
          <div v-if="errors.length" class="p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-lg">
            <p v-for="e in errors" :key="e" class="text-xs text-red-600 dark:text-red-400">{{ e }}</p>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-2">
            <button type="submit" :disabled="saving" class="btn-primary flex-1 justify-center">
              {{ saving ? 'Enregistrement…' : (task ? 'Mettre à jour' : 'Créer') }}
            </button>
            <button type="button" @click="$emit('close')" class="btn-secondary">
              Annuler
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  task:    { type: Object, default: null },
  prefill: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['saved', 'close'])

const saving = ref(false)
const goals  = ref([])
const errors = ref([])

const form = reactive({
  title:            '',
  description:      '',
  priority:         'medium',
  status:           'todo',
  scheduled_at:     '',
  duration_minutes: null,
  goal_id:          null,
  category:         '',
})

watch(() => props.task, (t) => {
  if (t) {
    Object.assign(form, {
      title:            t.title ?? '',
      description:      t.description ?? '',
      priority:         t.priority ?? 'medium',
      status:           t.status ?? 'todo',
      scheduled_at:     t.scheduled_at ? t.scheduled_at.slice(0, 16) : '',
      duration_minutes: t.duration_minutes ?? null,
      goal_id:          t.goal_id ?? null,
      category:         t.category ?? '',
    })
  }
}, { immediate: true })

watch(() => props.prefill, (p) => {
  if (p?.scheduled_at) form.scheduled_at = p.scheduled_at.slice(0, 16)
  if (p?.goal_id)      form.goal_id      = p.goal_id
}, { immediate: true })

onMounted(async () => {
  const { data } = await axios.get('/api/v1/goals')
  goals.value = data
})

async function save() {
  saving.value = true
  errors.value = []
  try {
    const payload = { ...form, goal_id: form.goal_id || null, scheduled_at: form.scheduled_at || null }
    const { data } = props.task
      ? await axios.patch(`/api/v1/tasks/${props.task.id}`, payload)
      : await axios.post('/api/v1/tasks', payload)
    emit('saved', data)
  } catch (e) {
    const errs = e.response?.data?.errors
    if (errs) {
      errors.value = Object.values(errs).flat()
    } else {
      errors.value = [e.response?.data?.message ?? 'Une erreur est survenue']
    }
  } finally {
    saving.value = false
  }
}
</script>
