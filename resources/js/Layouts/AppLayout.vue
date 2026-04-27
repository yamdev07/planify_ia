<template>
  <div class="flex h-screen bg-gray-900 text-white overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col fixed inset-y-0 left-0 z-30">
      <!-- Logo -->
      <div class="h-16 flex items-center px-6 border-b border-gray-700 shrink-0">
        <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-violet-400 bg-clip-text text-transparent">
          Planify IA
        </span>
      </div>

      <!-- Nav -->
      <nav class="flex-1 overflow-y-auto py-4 px-3">
        <Link
          v-for="item in navItems"
          :key="item.href"
          :href="item.href"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm font-medium transition-all duration-150"
          :class="isActive(item.href)
            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
            : 'text-gray-400 hover:text-white hover:bg-gray-700/60'"
        >
          <span class="text-lg w-5 text-center">{{ item.icon }}</span>
          {{ item.label }}
        </Link>
      </nav>

      <!-- User block -->
      <div class="shrink-0 p-4 border-t border-gray-700">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-xs font-bold shrink-0">
            {{ initials }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate text-white">{{ user?.name }}</p>
            <span class="inline-block text-xs px-1.5 py-0.5 rounded mt-0.5" :class="profileBadgeClass">
              {{ profileLabel }}
            </span>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div class="ml-64 flex-1 flex flex-col overflow-hidden">

      <!-- Header -->
      <header class="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6 shrink-0 z-20">
        <h1 class="text-base font-semibold text-gray-200">{{ pageTitle }}</h1>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-400">{{ user?.name }}</span>
          <span class="text-xs px-2 py-1 rounded-full font-medium" :class="profileBadgeClass">
            {{ profileLabel }}
          </span>
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="text-sm text-gray-400 hover:text-white transition-colors"
          >
            Déconnexion
          </Link>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 overflow-auto">
        <div class="p-6">
          <slot />
        </div>
      </main>
    </div>

    <Toast />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Toast from '@/Components/Toast.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const navItems = [
  { href: route('dashboard'),     icon: '⊞', label: 'Dashboard' },
  { href: route('planning'),      icon: '📅', label: 'Planning' },
  { href: route('tasks'),         icon: '✓',  label: 'Tâches' },
  { href: route('goals'),         icon: '🎯', label: 'Objectifs' },
  { href: route('ai-assistant'),  icon: '✦',  label: 'IA Assistant' },
]

const initials = computed(() =>
  user.value?.name?.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) ?? '?'
)

const profileLabel = computed(() => ({
  personal: 'Particulier',
  team:     'Équipe',
  student:  'Étudiant',
}[user.value?.profile_type] ?? 'Particulier'))

const profileBadgeClass = computed(() => ({
  personal: 'bg-indigo-500/20 text-indigo-300',
  team:     'bg-violet-500/20 text-violet-300',
  student:  'bg-emerald-500/20 text-emerald-300',
}[user.value?.profile_type] ?? 'bg-indigo-500/20 text-indigo-300'))

const pageTitle = computed(() => {
  const titles = {
    dashboard:    'Dashboard',
    planning:     'Planning',
    tasks:        'Tâches',
    goals:        'Objectifs',
    'ai-assistant': 'IA Assistant',
  }
  return titles[page.component?.toLowerCase()] ?? page.component ?? 'Planify IA'
})

function isActive(href) {
  return page.url.startsWith(new URL(href, window.location.origin).pathname)
}
</script>
