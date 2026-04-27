<template>
  <Teleport to="body">
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm font-medium min-w-[280px] max-w-sm"
          :class="typeClass(toast.type)"
        >
          <span class="w-4 h-4 shrink-0" v-html="typeIcon(toast.type)" />
          <span class="flex-1">{{ toast.message }}</span>
          <button @click="remove(toast.id)" class="opacity-50 hover:opacity-100 transition-opacity ml-1 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
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
    success: 'bg-white dark:bg-gray-900 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400',
    error:   'bg-white dark:bg-gray-900 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400',
    warning: 'bg-white dark:bg-gray-900 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400',
    info:    'bg-white dark:bg-gray-900 border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-400',
  }[type] ?? 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300'
}

function typeIcon(type) {
  const icons = {
    success: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    error:   '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>',
    warning: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>',
    info:    '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>',
  }
  return icons[type] ?? icons.info
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all .2s ease; }
.toast-enter-from { opacity: 0; transform: translateY(8px) scale(0.97); }
.toast-leave-to   { opacity: 0; transform: translateX(20px); }
</style>
