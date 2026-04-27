<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 flex transition-colors duration-200">

    <!-- Left panel: branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-700 p-12 flex-col justify-between relative overflow-hidden">
      <!-- Background pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white translate-x-1/3 -translate-y-1/3" />
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-white -translate-x-1/3 translate-y-1/3" />
      </div>

      <div class="relative">
        <div class="flex items-center gap-3 mb-12">
          <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
            </svg>
          </div>
          <span class="text-xl font-bold text-white tracking-tight">Planify IA</span>
        </div>

        <h2 class="text-3xl font-bold text-white mb-4 leading-tight">
          Organisez vos projets<br/>avec l'intelligence artificielle
        </h2>
        <p class="text-indigo-200 text-sm leading-relaxed max-w-sm">
          Planning intelligent, objectifs trackés, tâches priorisées automatiquement par l'IA.
        </p>
      </div>

      <div class="relative flex gap-4">
        <div v-for="feat in features" :key="feat" class="flex items-center gap-2">
          <svg class="w-4 h-4 text-indigo-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
          </svg>
          <span class="text-xs text-indigo-200">{{ feat }}</span>
        </div>
      </div>
    </div>

    <!-- Right panel: form -->
    <div class="flex-1 flex items-center justify-center p-8">
      <div class="w-full max-w-sm">

        <!-- Mobile logo -->
        <div class="flex items-center gap-2.5 mb-8 lg:hidden">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
            </svg>
          </div>
          <span class="text-sm font-semibold text-gray-900 dark:text-white">Planify <span class="text-indigo-500">IA</span></span>
        </div>

        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Bon retour</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Connectez-vous à votre compte</p>

        <div v-if="status" class="mb-5 p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs text-emerald-700 dark:text-emerald-400">
          {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label for="email" class="label">Adresse e-mail</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              autofocus
              autocomplete="username"
              class="input"
              placeholder="vous@exemple.com"
              :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.email }"
            />
            <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">{{ form.errors.email }}</p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="password" class="label !mb-0">Mot de passe</label>
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
              >Mot de passe oublié ?</Link>
            </div>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              autocomplete="current-password"
              class="input"
              placeholder="••••••••"
              :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': form.errors.password }"
            />
            <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">{{ form.errors.password }}</p>
          </div>

          <div class="flex items-center gap-2.5">
            <input
              id="remember"
              v-model="form.remember"
              type="checkbox"
              class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-800"
            />
            <label for="remember" class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer">Se souvenir de moi</label>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="btn-primary w-full justify-center py-2.5 mt-2"
          >
            {{ form.processing ? 'Connexion…' : 'Se connecter' }}
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
          Pas encore de compte ?
          <Link :href="route('register')" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline ml-1">
            Créer un compte
          </Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm, Head } from '@inertiajs/vue3'

defineProps({
  canResetPassword: { type: Boolean },
  status:           { type: String },
})

const form = useForm({
  email:    '',
  password: '',
  remember: false,
})

const features = ['Planning IA', 'Objectifs trackés', 'Tâches intelligentes']

const submit = () => form.post(route('login'), {
  onFinish: () => form.reset('password'),
})
</script>
