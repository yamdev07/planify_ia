<template>
  <div class="flex flex-col h-full">
    <!-- Day names header -->
    <div class="grid grid-cols-7 border-b border-gray-700 bg-gray-800">
      <div
        v-for="name in dayNames"
        :key="name"
        class="py-2 text-center text-xs font-medium text-gray-400 uppercase tracking-wide border-r border-gray-700 last:border-r-0"
      >{{ name }}</div>
    </div>

    <!-- Calendar grid -->
    <div class="flex-1 grid grid-cols-7 auto-rows-fr">
      <div
        v-for="cell in cells"
        :key="cell.iso"
        class="border-r border-b border-gray-700 last:border-r-0 p-1 min-h-[90px] cursor-pointer transition-colors"
        :class="[
          cell.isCurrentMonth ? 'bg-gray-900' : 'bg-gray-800/40',
          cell.isToday ? 'ring-1 ring-inset ring-indigo-500' : 'hover:bg-gray-800/60',
        ]"
        @click="$emit('day-click', cell.iso)"
      >
        <p
          class="text-xs font-medium mb-1 w-6 h-6 flex items-center justify-center rounded-full"
          :class="cell.isToday ? 'bg-indigo-500 text-white' : 'text-gray-400'"
        >{{ cell.day }}</p>

        <div class="space-y-0.5 overflow-hidden">
          <div
            v-for="task in cellTasks(cell.iso).slice(0, 3)"
            :key="'t' + task.id"
            class="text-xs px-1 py-0.5 rounded truncate"
            :class="priorityClass(task.priority)"
            @click.stop="$emit('task-click', task)"
          >{{ task.title }}</div>

          <div
            v-for="event in cellEvents(cell.iso).slice(0, 2)"
            :key="'e' + event.id"
            class="text-xs px-1 py-0.5 rounded truncate bg-violet-600/70 text-white"
            @click.stop
          >{{ event.title }}</div>

          <div
            v-if="cellTasks(cell.iso).length + cellEvents(cell.iso).length > 3"
            class="text-xs text-gray-500 px-1"
          >+{{ cellTasks(cell.iso).length + cellEvents(cell.iso).length - 3 }} autres</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentMonth: { type: String, required: true }, // 'YYYY-MM'
  tasks:        { type: Array, default: () => [] },
  events:       { type: Array, default: () => [] },
})

defineEmits(['day-click', 'task-click'])

const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']

const cells = computed(() => {
  const [y, m]  = props.currentMonth.split('-').map(Number)
  const first   = new Date(y, m - 1, 1)
  const last    = new Date(y, m, 0)
  const today   = new Date().toISOString().slice(0, 10)

  // Monday-based: getDay() returns 0=Sun, so map to Mon=0
  const startOffset = (first.getDay() + 6) % 7
  const result = []

  for (let i = 0; i < startOffset; i++) {
    const d = new Date(first)
    d.setDate(d.getDate() - (startOffset - i))
    result.push(cell(d, false, today))
  }
  for (let d = 1; d <= last.getDate(); d++) {
    result.push(cell(new Date(y, m - 1, d), true, today))
  }
  const remaining = 42 - result.length
  for (let i = 1; i <= remaining; i++) {
    result.push(cell(new Date(y, m, i), false, today))
  }
  return result
})

function cell(date, isCurrentMonth, today) {
  const iso = date.toISOString().slice(0, 10)
  return { iso, day: date.getDate(), isCurrentMonth, isToday: iso === today }
}

function cellTasks(iso) {
  return props.tasks.filter(t => t.scheduled_at?.slice(0, 10) === iso)
}

function cellEvents(iso) {
  return props.events.filter(e => e.start_at?.slice(0, 10) === iso)
}

function priorityClass(p) {
  return {
    urgent: 'bg-red-500/70 text-white',
    high:   'bg-orange-500/70 text-white',
    medium: 'bg-blue-500/70 text-white',
    low:    'bg-gray-600/70 text-gray-200',
  }[p] ?? 'bg-blue-500/70 text-white'
}
</script>
