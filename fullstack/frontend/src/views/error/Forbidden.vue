<template>
  <div class="error-page">
    <div class="card error-card animate__animated animate__fadeInUp">
      <div class="error-icon">
        <ShieldOff :size="64" color="var(--color-error)" />
      </div>
      <h1>访问受限</h1>
      <p class="error-desc">
        {{ reasonText }}
      </p>
      <div class="error-actions">
        <a href="/" class="btn btn-primary">返回首页</a>
      </div>
      <p class="error-footer">
        森码云实人认证系统 &mdash; face.builds.codes
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { ShieldOff } from 'lucide-vue-next'

const route = useRoute()

const reasonText = computed(() => {
  const reason = route.query.reason || ''
  const messages = {
    invalid_token: '认证Token无效或已过期，请从魔方财务系统重新发起认证。',
    expired: '认证链接已过期，请重新发起认证。',
    no_permission: '人脸识别仅允许从魔方财务系统入口进入，请从魔方财务系统发起认证。',
  }
  return messages[reason] || '人脸识别仅允许从魔方财务系统入口进入，请从魔方财务系统发起认证。'
})
</script>

<style scoped>
.error-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
.error-card { text-align: center; max-width: 480px; padding: 48px 40px; }
.error-icon { margin-bottom: 24px; }
h1 { font-size: 24px; font-weight: 700; margin-bottom: 12px; }
.error-desc { color: var(--color-text-secondary); font-size: 15px; line-height: 1.6; margin-bottom: 28px; }
.error-actions { margin-bottom: 24px; }
.error-footer { font-size: 12px; color: var(--color-text-muted); }
</style>