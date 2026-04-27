<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950 flex transition-colors duration-200">

    <!-- Left panel: branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-700 p-12 flex-col justify-between relative overflow-hidden">
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
          Commencez à planifier<br/>intelligemment
        </h2>
        <p class="text-indigo-200 text-sm leading-relaxed max-w-sm">
          Créez votre compte et laissez l'IA vous aider à atteindre vos objectifs.
        </p>
      </div>

      <div class="relative flex gap-4 flex-wrap">
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

        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Créer un compte</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Rejoignez Planify IA gratuitement</p>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label for="name" class="label">Nom complet</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              autofocus
              autocomplete="name"
              class="input"
              placeholder="Jean Dupont"
              :class="{ 'border-red-400': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-500">{{ form.errors.name }}</p>
          </div>

          <div>
            <label for="email" class="label">Adresse e-mail</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              autocomplete="username"
              class="input"
              placeholder="vous@exemple.com"
              :class="{ 'border-red-400': form.errors.email }"
            />
            <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-500">{{ form.errors.email }}</p>
          </div>

          <div>
            <label for="password" class="label">Mot de passe</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              autocomplete="new-password"
              class="input"
              placeholder="Minimum 8 caractères"
              :class="{ 'border-red-400': form.errors.password }"
            />
            <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-500">{{ form.errors.password }}</p>
          </div>

          <div>
            <label for="password_confirmation" class="label">Confirmer le mot de passe</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              required
              autocomplete="new-password"
              class="input"
              placeholder="••••••••"
              :class="{ 'border-red-400': form.errors.password_confirmation }"
            />
            <p v-if="form.errors.password_confirmation" class="mt-1.5 text-xs text-red-500">{{ form.errors.password_confirmation }}</p>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="btn-primary w-full justify-center py-2.5 mt-2"
          >
            {{ form.processing ? 'Création du compte…' : 'Créer mon compte' }}
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
          Déjà inscrit ?
          <Link :href="route('login')" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline ml-1">
            Se connecter
          </Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  name:                  '',
  email:                 '',
  password:              '',
  password_confirmation: '',
})

const features = ['Gratuit', 'IA intégrée', 'Objectifs trackés']

const submit = () => form.post(route('register'), {
  onFinish: () => form.reset('password', 'password_confirmation'),
})
</script>
