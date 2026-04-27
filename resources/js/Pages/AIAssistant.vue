<template>
  <AppLayout>
    <div class="flex gap-5 h-[calc(100vh-112px)] -my-6">

      <!-- Chat panel -->
      <div class="flex-1 flex flex-col card overflow-hidden min-w-0">

        <!-- Chat header -->
        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 shrink-0">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-sm shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Planify IA</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Assistant intelligent · llama-3.3-70b</p>
          </div>
          <button
            @click="clearChat"
            class="btn-ghost text-xs gap-1.5 py-1"
          >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
            Nouveau
          </button>
        </div>

        <!-- Messages -->
        <div ref="messagesEl" class="flex-1 overflow-y-auto px-5 py-5 space-y-5">

          <!-- Empty state with suggestions -->
          <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-950/40 dark:to-violet-950/40 flex items-center justify-center mb-5">
              <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
              </svg>
            </div>
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Comment puis-je vous aider ?</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-6 max-w-xs">Posez-moi une question sur votre planning, vos objectifs ou vos tâches.</p>
            <div class="flex flex-wrap gap-2 justify-center max-w-sm">
              <button
                v-for="s in suggestions"
                :key="s"
                @click="sendSuggestion(s)"
                class="text-xs px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-gray-600 dark:text-gray-400 hover:text-indigo-700 dark:hover:text-indigo-400 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700 transition-all"
              >{{ s }}</button>
            </div>
          </div>

          <!-- Messages list -->
          <div
            v-for="(msg, i) in messages"
            :key="i"
            class="flex gap-3"
            :class="msg.role === 'user' ? 'flex-row-reverse' : ''"
          >
            <div
              class="w-7 h-7 rounded-full shrink-0 flex items-center justify-center text-xs font-bold mt-0.5"
              :class="msg.role === 'user'
                ? 'bg-indigo-600 text-white'
                : 'bg-gradient-to-br from-indigo-500 to-violet-600 text-white'"
            >
              <template v-if="msg.role === 'user'">{{ userInitial }}</template>
              <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
              </svg>
            </div>
            <div
              class="max-w-[75%] px-4 py-2.5 rounded-2xl text-sm leading-relaxed whitespace-pre-wrap"
              :class="msg.role === 'user'
                ? 'bg-indigo-600 text-white rounded-tr-sm'
                : 'bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-700 rounded-tl-sm'"
            >{{ msg.content }}</div>
          </div>

          <!-- Typing indicator -->
          <div v-if="thinking" class="flex gap-3">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shrink-0">
              <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
              </svg>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl rounded-tl-sm px-4 py-3">
              <div class="flex gap-1 items-center">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:0ms" />
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:150ms" />
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:300ms" />
              </div>
            </div>
          </div>
        </div>

        <!-- Input -->
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 shrink-0">
          <form @submit.prevent="send" class="flex gap-2.5">
            <input
              v-model="input"
              type="text"
              :disabled="thinking"
              class="input flex-1"
              placeholder="Posez votre question…"
            />
            <button
              type="submit"
              :disabled="!input.trim() || thinking"
              class="btn-primary px-4 shrink-0"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
              </svg>
            </button>
          </form>
        </div>
      </div>

      <!-- Right panel -->
      <div class="w-72 flex flex-col gap-4 shrink-0">

        <!-- Analyze week -->
        <div class="card p-5">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Analyser la semaine</h3>
          <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Obtenez des recommandations IA sur votre planning.</p>
          <button
            @click="analyzeWeek"
            :disabled="analyzing"
            class="w-full btn-primary justify-center"
          >
            <svg v-if="!analyzing" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
            {{ analyzing ? 'Analyse en cours…' : 'Analyser et optimiser' }}
          </button>

          <div v-if="analysis" class="mt-4 space-y-3">
            <div v-if="analysis.recommendations?.length">
              <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Recommandations</p>
              <ul class="space-y-1.5">
                <li
                  v-for="r in analysis.recommendations"
                  :key="r"
                  class="text-xs text-gray-600 dark:text-gray-400 flex gap-2"
                >
                  <span class="text-indigo-500 shrink-0 mt-0.5">→</span>
                  <span>{{ r }}</span>
                </li>
              </ul>
            </div>
            <div v-if="analysis.overloaded_days?.length">
              <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Jours surchargés</p>
              <div class="flex flex-wrap gap-1.5">
                <span
                  v-for="d in analysis.overloaded_days"
                  :key="d"
                  class="badge bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-[11px]"
                >{{ fmtDay(d) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Suggest time slots -->
        <div class="card p-5 flex-1">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Créneaux suggérés</h3>
          <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Entrez l'ID d'une tâche pour trouver le meilleur créneau.</p>
          <div class="flex gap-2 mb-4">
            <input
              v-model="slotTaskId"
              type="number"
              placeholder="ID tâche"
              class="input flex-1"
            />
            <button
              @click="suggestSlots"
              :disabled="!slotTaskId || suggestingSlots"
              class="btn-primary px-3 shrink-0"
            >Go</button>
          </div>
          <div v-if="slots.length" class="space-y-2">
            <div
              v-for="(s, i) in slots"
              :key="i"
              class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700"
            >
              <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">{{ fmtSlot(s.start) }} – {{ fmtSlot(s.end) }}</p>
              <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">{{ s.reason }}</p>
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

const messages        = ref([])
const input           = ref('')
const thinking        = ref(false)
const messagesEl      = ref(null)
const analysis        = ref(null)
const analyzing       = ref(false)
const slots           = ref([])
const slotTaskId      = ref('')
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

async function sendSuggestion(s) { input.value = s; await send() }

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
  } catch { error('Erreur lors de la suggestion') }
  finally { suggestingSlots.value = false }
}

async function scrollBottom() {
  await nextTick()
  if (messagesEl.value) messagesEl.value.scrollTop = messagesEl.value.scrollHeight
}

function fmtDay(d)  { return new Date(d).toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short' }) }
function fmtSlot(d) { return new Date(d).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }) }
</script>
