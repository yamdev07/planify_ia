<template>
  <AppLayout>
    <div class="flex gap-6 h-[calc(100vh-112px)] -my-6">

      <!-- Chat panel -->
      <div class="flex-1 flex flex-col bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <!-- Header -->
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-700 shrink-0">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-sm">✦</div>
          <div>
            <p class="text-sm font-semibold text-white">Planify IA</p>
            <p class="text-xs text-gray-400">Assistant intelligent</p>
          </div>
          <button @click="clearChat" class="ml-auto text-xs text-gray-500 hover:text-gray-300 transition-colors">
            Nouvelle conversation
          </button>
        </div>

        <!-- Messages -->
        <div ref="messagesEl" class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
          <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500/20 to-violet-500/20 flex items-center justify-center text-3xl mb-4">✦</div>
            <p class="text-gray-300 font-medium">Comment puis-je vous aider ?</p>
            <p class="text-gray-500 text-sm mt-1">Posez-moi une question sur votre planning, vos objectifs ou vos tâches.</p>
            <div class="flex flex-wrap gap-2 mt-4 justify-center">
              <button
                v-for="s in suggestions"
                :key="s"
                @click="sendSuggestion(s)"
                class="text-xs px-3 py-1.5 rounded-full bg-gray-700 hover:bg-indigo-600/40 text-gray-300 hover:text-indigo-300 transition-all border border-gray-600 hover:border-indigo-500"
              >{{ s }}</button>
            </div>
          </div>

          <div
            v-for="(msg, i) in messages"
            :key="i"
            class="flex gap-3"
            :class="msg.role === 'user' ? 'flex-row-reverse' : ''"
          >
            <div
              class="w-7 h-7 rounded-full shrink-0 flex items-center justify-center text-xs font-bold mt-0.5"
              :class="msg.role === 'user'
                ? 'bg-indigo-500 text-white'
                : 'bg-gradient-to-br from-indigo-500 to-violet-500 text-white'"
            >
              {{ msg.role === 'user' ? userInitial : '✦' }}
            </div>
            <div
              class="max-w-[75%] px-4 py-2.5 rounded-2xl text-sm leading-relaxed whitespace-pre-wrap"
              :class="msg.role === 'user'
                ? 'bg-indigo-600 text-white rounded-tr-sm'
                : 'bg-gray-700 text-gray-100 rounded-tl-sm'"
            >{{ msg.content }}</div>
          </div>

          <div v-if="thinking" class="flex gap-3">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-xs shrink-0">✦</div>
            <div class="bg-gray-700 rounded-2xl rounded-tl-sm px-4 py-3">
              <div class="flex gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:0ms" />
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:150ms" />
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:300ms" />
              </div>
            </div>
          </div>
        </div>

        <!-- Input -->
        <div class="px-5 py-4 border-t border-gray-700 shrink-0">
          <form @submit.prevent="send" class="flex gap-3">
            <input
              v-model="input"
              type="text"
              :disabled="thinking"
              class="flex-1 bg-gray-700 border border-gray-600 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 disabled:opacity-50"
              placeholder="Posez votre question…"
            />
            <button
              type="submit"
              :disabled="!input.trim() || thinking"
              class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-40 text-white rounded-xl text-sm font-medium transition-colors"
            >Envoyer</button>
          </form>
        </div>
      </div>

      <!-- Right panel: AI tools -->
      <div class="w-72 flex flex-col gap-4">

        <!-- Analyze week -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
          <h3 class="text-sm font-semibold text-white mb-3">Analyser la semaine</h3>
          <button
            @click="analyzeWeek"
            :disabled="analyzing"
            class="w-full py-2 bg-violet-600 hover:bg-violet-500 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
          >
            {{ analyzing ? 'Analyse en cours…' : '✦ Analyser et optimiser' }}
          </button>

          <div v-if="analysis" class="mt-3 space-y-3 text-xs">
            <div v-if="analysis.recommendations?.length">
              <p class="text-gray-400 font-medium mb-1">Recommandations</p>
              <ul class="space-y-1">
                <li v-for="r in analysis.recommendations" :key="r" class="text-gray-300 flex gap-1.5">
                  <span class="text-indigo-400 shrink-0">→</span>{{ r }}
                </li>
              </ul>
            </div>
            <div v-if="analysis.overloaded_days?.length">
              <p class="text-gray-400 font-medium mb-1">Jours surchargés</p>
              <div class="flex flex-wrap gap-1">
                <span v-for="d in analysis.overloaded_days" :key="d" class="px-2 py-0.5 bg-red-500/20 text-red-300 rounded">{{ fmtDay(d) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Suggest for task -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex-1">
          <h3 class="text-sm font-semibold text-white mb-3">Créneaux suggérés</h3>
          <p class="text-xs text-gray-500 mb-3">Entrez l'ID d'une tâche pour obtenir les meilleurs créneaux disponibles cette semaine.</p>
          <div class="flex gap-2">
            <input v-model="slotTaskId" type="number" placeholder="ID tâche" class="flex-1 bg-gray-700 border border-gray-600 rounded-lg px-3 py-1.5 text-sm text-white focus:outline-none focus:border-indigo-500" />
            <button @click="suggestSlots" :disabled="!slotTaskId || suggestingSlots" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white text-sm rounded-lg transition-colors">
              Go
            </button>
          </div>
          <div v-if="slots.length" class="mt-3 space-y-2">
            <div v-for="(s, i) in slots" :key="i" class="p-2 bg-gray-700 rounded-lg text-xs">
              <p class="text-indigo-300 font-medium">{{ fmtSlot(s.start) }} – {{ fmtSlot(s.end) }}</p>
              <p class="text-gray-400 mt-0.5">{{ s.reason }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useToast } from '@/composables/useToast'

const { error } = useToast()
const page = usePage()

const messages   = ref([])
const input      = ref('')
const thinking   = ref(false)
const messagesEl = ref(null)
const analysis   = ref(null)
const analyzing  = ref(false)
const slots      = ref([])
const slotTaskId = ref('')
const suggestingSlots = ref(false)

const userInitial = computed(() =>
  page.props.auth?.user?.name?.charAt(0)?.toUpperCase() ?? 'U'
)

const suggestions = [
  'Quelles sont mes tâches urgentes ?',
  'Comment optimiser ma semaine ?',
  'Résume mes objectifs en retard',
  'Aide-moi à planifier demain',
]

function clearChat() { messages.value = [] }

async function send() {
  const text = input.value.trim()
  if (!text || thinking.value) return
  input.value = ''
  messages.value.push({ role: 'user', content: text })
  await scrollBottom()

  thinking.value = true
  try {
    const { data } = await axios.post('/api/v1/ai/chat', {
      messages: messages.value.map(m => ({ role: m.role, content: m.content })),
    })
    messages.value.push({ role: 'assistant', content: data.reply })
    await scrollBottom()
  } catch {
    error('Erreur de communication avec l\'IA')
  } finally {
    thinking.value = false
  }
}

async function sendSuggestion(s) {
  input.value = s
  await send()
}

async function analyzeWeek() {
  analyzing.value = true
  analysis.value  = null
  try {
    const { data } = await axios.post('/api/v1/ai/analyze-optimize')
    analysis.value = data
  } catch { error('Erreur lors de l\'analyse') }
  finally { analyzing.value = false }
}

async function suggestSlots() {
  suggestingSlots.value = true
  slots.value = []
  try {
    const { data } = await axios.post('/api/v1/ai/suggest-time-slots', { task_id: Number(slotTaskId.value) })
    slots.value = data.slots ?? []
  } catch { error('Erreur lors de la suggestion de créneaux') }
  finally { suggestingSlots.value = false }
}

async function scrollBottom() {
  await nextTick()
  if (messagesEl.value) messagesEl.value.scrollTop = messagesEl.value.scrollHeight
}

function fmtDay(d) {
  return new Date(d).toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' })
}
function fmtSlot(d) {
  return new Date(d).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}
</script>
