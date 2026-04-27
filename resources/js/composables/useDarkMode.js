import { ref } from 'vue'

const isDark = ref(false)

function apply(dark) {
  document.documentElement.classList.toggle('dark', dark)
  isDark.value = dark
}

export function useDarkMode() {
  // Sync ref with current DOM state (set by anti-flash script in <head>)
  isDark.value = document.documentElement.classList.contains('dark')

  function toggle() {
    const next = !isDark.value
    localStorage.setItem('planify-theme', next ? 'dark' : 'light')
    apply(next)
  }

  return { isDark, toggle }
}
