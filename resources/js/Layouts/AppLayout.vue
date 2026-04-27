<template>
  <div class="flex h-screen bg-gray-50 dark:bg-gray-950 overflow-hidden transition-colors duration-200">

    <!-- Sidebar -->
    <aside class="w-60 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col fixed inset-y-0 left-0 z-30 transition-colors duration-200">

      <!-- Logo -->
      <div class="h-14 flex items-center px-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
        <Link :href="route('dashboard')" class="flex items-center gap-2.5 hover:opacity-80 transition-opacity">
          <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-sm shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
            </svg>
          </div>
          <span class="text-sm font-semibold text-gray-900 dark:text-white tracking-tight">
            Planify <span class="text-indigo-500">IA</span>
          </span>
        </Link>
      </div>

      <!-- Nav -->
      <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
        <p class="px-3 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-600">Menu</p>
        <Link
          v-for="item in navItems"
          :key="item.href"
          :href="item.href"
          class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-150 group"
          :class="isActive(item.href)
            ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-400'
            : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60'"
        >
          <span
            class="w-4 h-4 shrink-0 transition-colors"
            :class="isActive(item.href) ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300'"
            v-html="item.icon"
          />
          {{ item.label }}
        </Link>
      </nav>

      <!-- Bottom -->
      <div class="shrink-0 border-t border-gray-100 dark:border-gray-800 p-2 space-y-0.5">

        <!-- Dark mode toggle -->
        <button
          @click="toggle"
          class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800/60 transition-all duration-150"
        >
          <span class="w-4 h-4 shrink-0 text-gray-400 dark:text-gray-500" v-html="isDark ? sunIcon : moonIcon" />
          {{ isDark ? 'Mode clair' : 'Mode sombre' }}
        </button>

        <!-- Divider -->
        <div class="h-px bg-gray-100 dark:bg-gray-800 mx-1 my-1" />

        <!-- User row -->
        <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors group">
          <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-xs font-bold text-white shrink-0 shadow-sm">
            {{ initials }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate leading-tight">{{ user?.name }}</p>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 truncate leading-tight mt-0.5">{{ profileLabel }}</p>
          </div>
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            title="Déconnexion"
            class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-all shrink-0"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
            </svg>
          </Link>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div class="ml-60 flex-1 flex flex-col overflow-hidden min-w-0">

      <!-- Header -->
      <header class="h-14 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-6 shrink-0 transition-colors duration-200">
        <div class="flex items-center gap-2">
          <h1 class="text-sm font-semibold text-gray-900 dark:text-white">{{ pageTitle }}</h1>
        </div>
        <div class="flex items-center gap-3">
          <span class="badge" :class="profileBadgeClass">{{ profileLabel }}</span>
          <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-xs font-bold text-white shadow-sm cursor-default" :title="user?.name">
            {{ initials }}
          </div>
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
import { useDarkMode } from '@/composables/useDarkMode'

const { isDark, toggle } = useDarkMode()
const page = usePage()
const user = computed(() => page.props.auth?.user)

const navItems = [
  {
    href: route('dashboard'),
    label: 'Dashboard',
    icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>',
  },
  {
    href: route('planning'),
    label: 'Planning',
    icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
  },
  {
    href: route('tasks'),
    label: 'Tâches',
    icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
  },
  {
    href: route('goals'),
    label: 'Objectifs',
    icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/></svg>',
  },
  {
    href: route('ai-assistant'),
    label: 'IA Assistant',
    icon: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/></svg>',
  },
]

const moonIcon = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>'
const sunIcon  = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>'

const initials = computed(() =>
  user.value?.name?.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) ?? '?'
)

const profileLabel = computed(() => ({
  personal: 'Particulier',
  team:     'Équipe',
  student:  'Étudiant',
}[user.value?.profile_type] ?? 'Particulier'))

const profileBadgeClass = computed(() => ({
  personal: 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400',
  team:     'bg-violet-50 dark:bg-violet-950/60 text-violet-700 dark:text-violet-400',
  student:  'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400',
}[user.value?.profile_type] ?? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400'))

const pageTitle = computed(() => ({
  dashboard:    'Dashboard',
  planning:     'Planning',
  tasks:        'Tâches',
  goals:        'Objectifs',
  'ai-assistant': 'IA Assistant',
}[page.component?.toLowerCase()] ?? page.component ?? 'Planify IA'))

function isActive(href) {
  return page.url.startsWith(new URL(href, window.location.origin).pathname)
}
</script>
