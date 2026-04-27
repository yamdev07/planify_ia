<template>
  <Teleport to="body">
    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium min-w-[260px] max-w-xs"
          :class="typeClass(toast.type)"
        >
          <span class="text-lg">{{ typeIcon(toast.type) }}</span>
          <span class="flex-1">{{ toast.message }}</span>
          <button @click="remove(toast.id)" class="opacity-60 hover:opacity-100 text-base leading-none">&times;</button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '@/composables/useToast'

const { toasts, remove } = useToast()

function typeClass(type) {
  return {
    success: 'bg-emerald-600 text-white',
    error:   'bg-red-600 text-white',
    warning: 'bg-amber-500 text-white',
    info:    'bg-indigo-600 text-white',
  }[type] ?? 'bg-gray-700 text-white'
}

function typeIcon(type) {
  return { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' }[type] ?? '•'
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all .25s ease; }
.toast-enter-from { opacity: 0; transform: translateX(60px); }
.toast-leave-to   { opacity: 0; transform: translateX(60px); }
</style>
