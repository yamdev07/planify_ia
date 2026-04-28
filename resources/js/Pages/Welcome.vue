<template>
  <div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-white transition-colors duration-200">
    <Head title="Planify IA — Organisez votre vie avec l'intelligence artificielle" />

    <!-- Navbar -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md border-b border-gray-200/60 dark:border-gray-800/60">
      <div class="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
            </svg>
          </div>
          <span class="text-sm font-semibold tracking-tight">Planify <span class="text-indigo-500">IA</span></span>
        </div>

        <nav class="flex items-center gap-2">
          <template v-if="$page.props.auth?.user">
            <Link :href="route('dashboard')" class="btn-nav-primary">
              Tableau de bord
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
              </svg>
            </Link>
          </template>
          <template v-else>
            <Link :href="route('login')" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
              Connexion
            </Link>
            <Link v-if="canRegister" :href="route('register')" class="btn-nav-primary">
              Commencer gratuitement
            </Link>
          </template>
        </nav>
      </div>
    </header>

    <!-- Hero -->
    <section class="pt-32 pb-20 px-6 relative overflow-hidden">
      <!-- Background decorations -->
      <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-gradient-to-b from-indigo-50 dark:from-indigo-950/30 to-transparent rounded-full blur-3xl opacity-60" />
        <div class="absolute top-20 left-1/4 w-64 h-64 bg-violet-100 dark:bg-violet-950/20 rounded-full blur-3xl opacity-40" />
        <div class="absolute top-20 right-1/4 w-64 h-64 bg-indigo-100 dark:bg-indigo-950/20 rounded-full blur-3xl opacity-40" />
      </div>

      <div class="max-w-4xl mx-auto text-center">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 rounded-full mb-8">
          <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse" />
          <span class="text-xs font-medium text-indigo-700 dark:text-indigo-400">Propulsé par llama-3.3-70b · Groq AI</span>
        </div>

        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white leading-tight mb-6 tracking-tight">
          Planifiez smarter,<br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-violet-600">accomplissez plus</span>
        </h1>

        <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
          Planify IA transforme votre façon de travailler. Gérez vos tâches, objectifs et planning avec un assistant intelligent qui apprend de vos habitudes.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <Link :href="route('register')" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all hover:shadow-indigo-500/40 hover:-translate-y-0.5">
            Commencer gratuitement
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
          </Link>
          <Link :href="route('login')" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold rounded-xl border border-gray-200 dark:border-gray-700 transition-colors">
            Se connecter
          </Link>
        </div>

        <!-- Trust signals -->
        <div class="flex items-center justify-center gap-6 mt-12 text-xs text-gray-400 dark:text-gray-500">
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            </svg>
            Gratuit pour commencer
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            </svg>
            Aucune carte requise
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            </svg>
            IA intégrée
          </div>
        </div>
      </div>
    </section>

    <!-- App preview -->
    <section class="px-6 pb-24">
      <div class="max-w-5xl mx-auto">
        <div class="relative bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-950 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl shadow-gray-200/50 dark:shadow-black/50 overflow-hidden p-1">
          <!-- Fake browser bar -->
          <div class="bg-gray-100 dark:bg-gray-800 rounded-xl px-4 py-2.5 flex items-center gap-2 mb-1">
            <div class="flex gap-1.5">
              <div class="w-2.5 h-2.5 rounded-full bg-red-400" />
              <div class="w-2.5 h-2.5 rounded-full bg-amber-400" />
              <div class="w-2.5 h-2.5 rounded-full bg-emerald-400" />
            </div>
            <div class="flex-1 mx-3 bg-white dark:bg-gray-700 rounded-md h-5 flex items-center px-3">
              <span class="text-[10px] text-gray-400 dark:text-gray-500">127.0.0.1:8000/dashboard</span>
            </div>
          </div>
          <!-- Dashboard mockup -->
          <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 flex gap-3 min-h-[300px]">
            <!-- Sidebar mockup -->
            <div class="w-36 bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700 shrink-0">
              <div class="flex items-center gap-1.5 mb-4">
                <div class="w-5 h-5 rounded bg-gradient-to-br from-indigo-500 to-violet-600" />
                <div class="h-2.5 bg-gray-200 dark:bg-gray-600 rounded w-14" />
              </div>
              <div class="space-y-1.5">
                <div v-for="(item, i) in mockNav" :key="i"
                  class="flex items-center gap-2 px-2 py-1.5 rounded-md"
                  :class="i === 0 ? 'bg-indigo-50 dark:bg-indigo-950/50' : ''"
                >
                  <div class="w-3 h-3 rounded" :class="i === 0 ? 'bg-indigo-400' : 'bg-gray-200 dark:bg-gray-600'" />
                  <div class="h-2 rounded flex-1" :class="i === 0 ? 'bg-indigo-300 dark:bg-indigo-700' : 'bg-gray-200 dark:bg-gray-600'" />
                </div>
              </div>
            </div>
            <!-- Content mockup -->
            <div class="flex-1 space-y-3">
              <!-- Stats row -->
              <div class="grid grid-cols-3 gap-3">
                <div v-for="(stat, i) in mockStats" :key="i" class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700">
                  <div class="w-6 h-6 rounded-md mb-2" :class="stat.bg" />
                  <div class="h-4 font-bold text-sm text-gray-800 dark:text-white">{{ stat.val }}</div>
                  <div class="h-2 bg-gray-100 dark:bg-gray-600 rounded w-16 mt-1" />
                </div>
              </div>
              <!-- Two panels -->
              <div class="grid grid-cols-2 gap-3">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700">
                  <div class="h-2.5 bg-gray-200 dark:bg-gray-600 rounded w-20 mb-3" />
                  <div class="space-y-2">
                    <div v-for="j in 3" :key="j" class="flex items-center gap-2">
                      <div class="w-2 h-2 rounded-full" :class="['bg-red-400','bg-indigo-400','bg-amber-400'][j-1]" />
                      <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded flex-1" />
                      <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded w-10" />
                    </div>
                  </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-100 dark:border-gray-700">
                  <div class="h-2.5 bg-gray-200 dark:bg-gray-600 rounded w-20 mb-3" />
                  <div class="space-y-3">
                    <div v-for="j in 2" :key="j">
                      <div class="flex justify-between mb-1">
                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded w-24" />
                        <div class="h-2 bg-indigo-200 dark:bg-indigo-800 rounded w-8" />
                      </div>
                      <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-400 rounded-full" :style="{width: [65,40][j-1]+'%'}" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="py-24 px-6 bg-gray-50 dark:bg-gray-900/50">
      <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
          <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-3 uppercase tracking-widest">Fonctionnalités</p>
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Tout ce dont vous avez besoin</h2>
          <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">Un seul outil pour gérer votre productivité au quotidien, avec l'intelligence artificielle comme copilote.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          <div
            v-for="feat in features"
            :key="feat.title"
            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-800 transition-all group"
          >
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4 transition-colors" :class="feat.iconBg">
              <span class="w-5 h-5" :class="feat.iconColor" v-html="feat.icon" />
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ feat.title }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ feat.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- How it works -->
    <section class="py-24 px-6">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-16">
          <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-3 uppercase tracking-widest">Comment ça marche</p>
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">En 3 étapes simples</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
          <!-- Connecting line -->
          <div class="hidden md:block absolute top-5 left-1/4 right-1/4 h-px bg-gradient-to-r from-indigo-200 via-violet-200 to-indigo-200 dark:from-indigo-800 dark:via-violet-800 dark:to-indigo-800" />

          <div v-for="(step, i) in steps" :key="i" class="flex flex-col items-center text-center">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold text-sm mb-4 shadow-lg shadow-indigo-500/25 relative z-10">
              {{ i + 1 }}
            </div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ step.title }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-20 px-6">
      <div class="max-w-3xl mx-auto text-center">
        <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl p-12 relative overflow-hidden">
          <!-- Background decoration -->
          <div class="absolute inset-0 opacity-20">
            <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white" />
            <div class="absolute -bottom-8 -left-8 w-32 h-32 rounded-full bg-white" />
          </div>
          <div class="relative">
            <h2 class="text-3xl font-bold text-white mb-4">Prêt à être plus productif ?</h2>
            <p class="text-indigo-200 mb-8 max-w-md mx-auto">Rejoignez Planify IA aujourd'hui et laissez l'IA prendre soin de votre planning.</p>
            <Link :href="route('register')" class="inline-flex items-center gap-2 px-8 py-3 bg-white hover:bg-gray-50 text-indigo-700 font-semibold rounded-xl shadow-lg transition-all hover:-translate-y-0.5">
              Créer mon compte gratuitement
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
              </svg>
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-800 py-8 px-6">
      <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <div class="w-5 h-5 rounded bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
            </svg>
          </div>
          <span class="text-sm font-semibold text-gray-900 dark:text-white">Planify <span class="text-indigo-500">IA</span></span>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500">
          Propulsé par Laravel · Vue 3 · Groq AI
        </p>
        <div class="flex items-center gap-4">
          <Link :href="route('login')" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Connexion</Link>
          <Link :href="route('register')" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Inscription</Link>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  canLogin:    { type: Boolean },
  canRegister: { type: Boolean },
})

const mockNav   = Array(5).fill(null)
const mockStats = [
  { bg: 'bg-indigo-100 dark:bg-indigo-950/50', val: '12' },
  { bg: 'bg-violet-100 dark:bg-violet-950/50', val: '4' },
  { bg: 'bg-red-100 dark:bg-red-950/50',       val: '3' },
]

const features = [
  {
    title:     'Planning intelligent',
    desc:      'Visualisez votre semaine et votre mois en vue calendrier. Glissez-déposez vos tâches pour réorganiser votre agenda.',
    icon:      '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
    iconBg:    'bg-indigo-50 dark:bg-indigo-950/50',
    iconColor: 'text-indigo-600 dark:text-indigo-400',
  },
  {
    title:     'Gestion des tâches',
    desc:      'Créez, priorisez et organisez vos tâches par catégorie et objectif. Filtrage avancé pour retrouver ce qui compte.',
    icon:      '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    iconBg:    'bg-emerald-50 dark:bg-emerald-950/50',
    iconColor: 'text-emerald-600 dark:text-emerald-400',
  },
  {
    title:     'Suivi des objectifs',
    desc:      'Définissez vos objectifs à long terme et suivez leur progression grâce à des indicateurs visuels en temps réel.',
    icon:      '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5"/></svg>',
    iconBg:    'bg-violet-50 dark:bg-violet-950/50',
    iconColor: 'text-violet-600 dark:text-violet-400',
  },
  {
    title:     'Assistant IA',
    desc:      'Posez des questions, obtenez des recommandations et laissez l\'IA analyser votre semaine pour vous suggérer des améliorations.',
    icon:      '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/></svg>',
    iconBg:    'bg-amber-50 dark:bg-amber-950/50',
    iconColor: 'text-amber-600 dark:text-amber-400',
  },
  {
    title:     'Créneau automatique',
    desc:      'L\'IA suggère les meilleurs créneaux disponibles pour placer vos tâches selon vos habitudes de travail.',
    icon:      '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    iconBg:    'bg-sky-50 dark:bg-sky-950/50',
    iconColor: 'text-sky-600 dark:text-sky-400',
  },
  {
    title:     'Mode sombre',
    desc:      'Interface claire ou sombre selon vos préférences, avec persistance automatique de votre choix.',
    icon:      '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>',
    iconBg:    'bg-gray-100 dark:bg-gray-800',
    iconColor: 'text-gray-600 dark:text-gray-400',
  },
]

const steps = [
  {
    title: 'Créez votre compte',
    desc:  'Inscription en 30 secondes, aucune carte bancaire requise.',
  },
  {
    title: 'Ajoutez vos objectifs',
    desc:  'Définissez ce que vous voulez accomplir et décomposez-le en tâches.',
  },
  {
    title: 'Laissez l\'IA vous guider',
    desc:  'L\'assistant analyse votre charge de travail et optimise votre planning.',
  },
]
</script>

<style scoped>
.btn-nav-primary {
  @apply inline-flex items-center gap-1.5 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors;
}
</style>
