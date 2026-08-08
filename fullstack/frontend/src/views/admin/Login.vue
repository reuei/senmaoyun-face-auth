<template>
  <div class="login-page">
    <div class="card login-card animate__animated animate__fadeInUp">
      <div class="login-header">
        <ShieldCheck :size="40" color="var(--color-primary)" />
        <h1>森码云管理后台</h1>
        <p>请使用管理员账号登录</p>
      </div>
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label class="label">用户名</label>
          <input v-model="username" type="text" class="input" placeholder="请输入用户名" autocomplete="username" />
        </div>
        <div class="form-group">
          <label class="label">密码</label>
          <input v-model="password" type="password" class="input" placeholder="请输入密码" autocomplete="current-password" />
        </div>
        <div v-if="error" class="error-msg">
          <AlertCircle :size="16" />
          {{ error }}
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block" :disabled="loading">
          <Loader2 v-if="loading" :size="18" class="loading-spinner" />
          {{ loading ? '登录中...' : '登录' }}
        </button>
      </form>
      <div class="login-footer">
        <a href="/">返回首页</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/utils/api'
import { ShieldCheck, AlertCircle, Loader2 } from 'lucide-vue-next'

const router = useRouter()
const username = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  if (!username.value || !password.value) {
    error.value = '请输入用户名和密码'
    return
  }

  loading.value = true
  try {
    const res = await api.post('/admin/login', {
      username: username.value,
      password: password.value,
    })
    if (res.data.code === 200) {
      router.push('/admin/dashboard')
    } else {
      error.value = res.data.msg || '登录失败'
    }
  } catch (e) {
    error.value = '网络错误，请重试'
  }
  loading.value = false
}
</script>

<style scoped>
.login-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; background: var(--color-bg); }
.login-card { width: 100%; max-width: 400px; padding: 40px; }
.login-header { text-align: center; margin-bottom: 32px; }
.login-header h1 { font-size: 22px; font-weight: 700; margin: 12px 0 4px; }
.login-header p { color: var(--color-text-secondary); font-size: 14px; }
.form-group { margin-bottom: 20px; }
.error-msg { display: flex; align-items: center; gap: 6px; color: var(--color-error); font-size: 13px; margin-bottom: 16px; }
.login-footer { text-align: center; margin-top: 24px; }
.login-footer a { font-size: 13px; color: var(--color-text-muted); }
</style>