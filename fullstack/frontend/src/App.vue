<template>
  <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAppStore } from '@/stores/app'

const appStore = useAppStore()

onMounted(() => {
  appStore.initialize()
})
</script>

<style lang="scss">
#app {
  min-height: 100vh;
}
</style>