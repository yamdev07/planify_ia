<template>
  <div class="flex flex-col h-full select-none">
    <!-- Day headers -->
    <div class="grid grid-cols-[56px_repeat(7,1fr)] border-b border-gray-700 bg-gray-800 sticky top-0 z-10">
      <div class="border-r border-gray-700" />
      <div
        v-for="day in weekDays"
        :key="day.iso"
        class="py-2 px-1 text-center border-r border-gray-700 last:border-r-0"
      >
        <p class="text-xs text-gray-400 uppercase tracking-wide">{{ day.dayName }}</p>
        <p
          class="text-sm font-semibold mt-0.5"
          :class="day.isToday ? 'text-indigo-400' : 'text-gray-200'"
        >{{ day.dayNum }}</p>
      </div>
    </div>

    <!-- Time grid -->
    <div class="flex-1 overflow-y-auto">
      <div class="grid grid-cols-[56px_repeat(7,1fr)]" :style="{ height: totalHeight + 'px' }">
        <!-- Hour labels -->
        <div class="relative border-r border-gray-700">
          <div
            v-for="hour in hours"
            :key="hour"
            class="absolute w-full text-right pr-2 text-xs text-gray-500"
            :style="{ top: hourToTop(hour) + 'px' }"
          >{{ pad(hour) }}:00</div>
        </div>

        <!-- Day columns -->
        <div
          v-for="day in weekDays"
          :key="day.iso"
          class="relative border-r border-gray-700 last:border-r-0"
          @dragover.prevent
          @drop="onDrop($event, day.iso)"
          @click="onSlotClick($event, day.iso)"
        >
          <!-- Hour grid lines -->
          <div
            v-for="hour in hours"
            :key="hour"
            class="absolute w-full border-t border-gray-700/50"
            :style="{ top: hourToTop(hour) + 'px' }"
          />
          <div
            v-for="hour in hours"
            :key="'h' + hour"
            class="absolute w-full border-t border-gray-700/20"
            :style="{ top: (hourToTop(hour) + slotHeight / 2) + 'px' }"
          />

          <!-- Events -->
          <div
            v-for="event in dayEvents(day.iso)"
            :key="'e' + event.id"
            class="absolute left-0.5 right-0.5 rounded px-1 py-0.5 text-xs font-medium cursor-pointer overflow-hidden z-10"
            :style="eventStyle(event)"
            :class="event.color ? '' : 'bg-violet-600/80 text-white'"
            :draggable="false"
            @click.stop
          >
            {{ event.title }}
          </div>

          <!-- Tasks -->
          <div
            v-for="task in dayTasks(day.iso)"
            :key="'t' + task.id"
            draggable="true"
            class="absolute left-0.5 right-0.5 rounded px-1 py-0.5 text-xs font-medium cursor-grab active:cursor-grabbing overflow-hidden z-10 border border-black/10"
            :style="taskStyle(task)"
            :class="priorityClass(task.priority)"
            @dragstart="onDragStart($event, task)"
            @click.stop="$emit('task-click', task)"
          >
            <span class="truncate block">{{ task.title }}</span>
          </div>

          <!-- Current time indicator -->
          <div
            v-if="day.isToday && currentTimeTop !== null"
            class="absolute left-0 right-0 border-t-2 border-indigo-400 z-20 pointer-events-none"
            :style="{ top: currentTimeTop + 'px' }"
          >
            <div class="w-2 h-2 rounded-full bg-indigo-400 -mt-1 -ml-1" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  weekStart: { type: String, required: true },
  tasks:     { type: Array, default: () => [] },
  events:    { type: Array, default: () => [] },
})

const emit = defineEmits(['task-moved', 'slot-click', 'task-click'])

const START_HOUR  = 6
const END_HOUR    = 23
const SLOT_HEIGHT = 30      // px per 30 min
const slotHeight  = SLOT_HEIGHT
const totalHeight = (END_HOUR - START_HOUR) * SLOT_HEIGHT * 2  // 2 slots/hour
const hours       = Array.from({ length: END_HOUR - START_HOUR + 1 }, (_, i) => START_HOUR + i)

const currentTimeTop = ref(null)
let timer = null

onMounted(() => {
  updateCurrentTime()
  timer = setInterval(updateCurrentTime, 60_000)
})

onUnmounted(() => clearInterval(timer))

function updateCurrentTime() {
  const now = new Date()
  const h = now.getHours()
  const m = now.getMinutes()
  if (h >= START_HOUR && h <= END_HOUR) {
    currentTimeTop.value = ((h - START_HOUR) * 60 + m) / 30 * SLOT_HEIGHT
  } else {
    currentTimeTop.value = null
  }
}

const weekDays = computed(() => {
  const start = new Date(props.weekStart)
  return Array.from({ length: 7 }, (_, i) => {
    const d = new Date(start)
    d.setDate(d.getDate() + i)
    const iso = d.toISOString().slice(0, 10)
    const today = new Date().toISOString().slice(0, 10)
    return {
      iso,
      dayName: d.toLocaleDateString('fr-FR', { weekday: 'short' }),
      dayNum:  d.getDate(),
      isToday: iso === today,
    }
  })
})

function hourToTop(h) { return (h - START_HOUR) * SLOT_HEIGHT * 2 }
function pad(n)        { return String(n).padStart(2, '0') }

function timeToTop(dateStr) {
  if (!dateStr) return 0
  const d = new Date(dateStr)
  const h = d.getHours()
  const m = d.getMinutes()
  return Math.max(0, ((h - START_HOUR) * 60 + m) / 30 * SLOT_HEIGHT)
}

function durationToHeight(minutes) {
  return Math.max(SLOT_HEIGHT, (minutes ?? 30) / 30 * SLOT_HEIGHT)
}

function dayTasks(iso) {
  return props.tasks.filter(t => t.scheduled_at?.slice(0, 10) === iso)
}

function dayEvents(iso) {
  return props.events.filter(e => e.start_at?.slice(0, 10) === iso)
}

function taskStyle(task) {
  return {
    top:    timeToTop(task.scheduled_at) + 'px',
    height: durationToHeight(task.duration_minutes) + 'px',
  }
}

function eventStyle(event) {
  const start = new Date(event.start_at)
  const end   = new Date(event.end_at)
  const mins  = (end - start) / 60000
  return {
    top:    timeToTop(event.start_at) + 'px',
    height: durationToHeight(mins) + 'px',
    backgroundColor: event.color ?? undefined,
  }
}

function priorityClass(p) {
  return {
    urgent: 'bg-red-500/90 text-white',
    high:   'bg-orange-500/90 text-white',
    medium: 'bg-blue-500/90 text-white',
    low:    'bg-gray-500/80 text-gray-100',
  }[p] ?? 'bg-blue-500/90 text-white'
}

let draggedTask = null
function onDragStart(e, task) {
  draggedTask = task
  e.dataTransfer.effectAllowed = 'move'
}

function onDrop(e, iso) {
  if (!draggedTask) return
  const rect    = e.currentTarget.getBoundingClientRect()
  const relY    = e.clientY - rect.top
  const slot    = Math.floor(relY / SLOT_HEIGHT)
  const hour    = START_HOUR + Math.floor(slot / 2)
  const minutes = (slot % 2) * 30
  const newDate = `${iso}T${pad(hour)}:${pad(minutes)}:00`
  emit('task-moved', { task: draggedTask, scheduled_at: newDate })
  draggedTask = null
}

function onSlotClick(e, iso) {
  const rect    = e.currentTarget.getBoundingClientRect()
  const relY    = e.clientY - rect.top
  const slot    = Math.floor(relY / SLOT_HEIGHT)
  const hour    = START_HOUR + Math.floor(slot / 2)
  const minutes = (slot % 2) * 30
  emit('slot-click', { date: `${iso}T${pad(hour)}:${pad(minutes)}:00` })
}
</script>
