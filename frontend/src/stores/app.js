import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAppStore = defineStore('app', () => {
  const siteName = ref('森码云实人认证系统')
  const siteDomain = ref('face.builds.codes')
  const loading = ref(false)
  const darkMode = ref(false)

  function initialize() {
    // 检测暗色模式
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    darkMode.value = localStorage.getItem('darkMode') === 'true' || prefersDark
    applyTheme()
  }

  function toggleDarkMode() {
    darkMode.value = !darkMode.value
    localStorage.setItem('darkMode', darkMode.value)
    applyTheme()
  }

  function applyTheme() {
    if (darkMode.value) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }

  return {
    siteName,
    siteDomain,
    loading,
    darkMode,
    initialize,
    toggleDarkMode,
  }
})