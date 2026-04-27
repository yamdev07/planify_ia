<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$emit('close')" />
      <div class="relative bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-md z-10">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
          <h3 class="text-base font-semibold text-white">{{ task ? 'Modifier la tâche' : 'Nouvelle tâche' }}</h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-white text-xl leading-none">&times;</button>
        </div>

        <form @submit.prevent="save" class="px-6 py-4 space-y-4">
          <div>
            <label class="block text-xs font-medium text-gray-400 mb-1">Titre *</label>
            <input
              v-model="form.title"
              type="text"
              class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
              placeholder="Nom de la tâche"
              required
              autofocus
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-400 mb-1">Description</label>
            <textarea
              v-model="form.description"
              rows="2"
              class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none"
              placeholder="Détails optionnels…"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-400 mb-1">Priorité</label>
              <select v-model="form.priority" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                <option value="low">Faible</option>
                <option value="medium">Moyenne</option>
                <option value="high">Haute</option>
                <option value="urgent">Urgente</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-400 mb-1">Statut</label>
              <select v-model="form.status" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                <option value="todo">À faire</option>
                <option value="in_progress">En cours</option>
                <option value="done">Terminé</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-400 mb-1">Date planifiée</label>
              <input
                v-model="form.scheduled_at"
                type="datetime-local"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-400 mb-1">Durée (min)</label>
              <input
                v-model.number="form.duration_minutes"
                type="number"
                min="5"
                step="5"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500"
                placeholder="30"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-400 mb-1">Objectif</label>
              <select v-model="form.goal_id" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                <option :value="null">Aucun</option>
                <option v-for="g in goals" :key="g.id" :value="g.id">{{ g.title }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-400 mb-1">Catégorie</label>
              <input
                v-model="form.category"
                type="text"
                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500"
                placeholder="Ex: Travail"
              />
            </div>
          </div>

          <div class="flex gap-3 pt-2">
            <button
              type="submit"
              :disabled="saving"
              class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
            >
              {{ saving ? 'Enregistrement…' : (task ? 'Mettre à jour' : 'Créer') }}
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="py-2 px-4 bg-gray-700 hover:bg-gray-600 text-gray-200 text-sm font-medium rounded-lg transition-colors"
            >Annuler</button>
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
  task:     { type: Object, default: null },
  prefill:  { type: Object, default: () => ({}) },
})

const emit = defineEmits(['saved', 'close'])

const saving = ref(false)
const goals  = ref([])

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
}, { immediate: true })

onMounted(async () => {
  const { data } = await axios.get('/api/v1/goals')
  goals.value = data
})

async function save() {
  saving.value = true
  try {
    const payload = { ...form, goal_id: form.goal_id || null, scheduled_at: form.scheduled_at || null }
    const { data } = props.task
      ? await axios.patch(`/api/v1/tasks/${props.task.id}`, payload)
      : await axios.post('/api/v1/tasks', payload)
    emit('saved', data)
  } finally {
    saving.value = false
  }
}
</script>
