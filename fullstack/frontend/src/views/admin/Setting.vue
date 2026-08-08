<template>
  <div class="setting-page">
    <div class="page-header"><h3>系统设置</h3><p class="text-muted">配置站点信息、认证参数和安全选项</p></div>
    <div class="card">
      <div class="form-group"><label class="label">站点名称</label><input v-model="settings.site_name" class="input" /></div>
      <div class="form-group"><label class="label">站点域名</label><input v-model="settings.site_domain" class="input" /></div>
      <div class="form-group"><label class="label">魔方财务地址</label><input v-model="settings.mofang_url" class="input" placeholder="https://your-mofang.com" /></div>
      <div class="form-group"><label class="label">最大重试次数</label><input v-model="settings.max_retry" type="number" class="input" /></div>
      <div class="form-group"><label class="label">活体检测阈值</label><input v-model="settings.liveness_threshold" type="number" class="input" /></div>
      <div class="form-group"><label class="label">数据保留时间(小时)</label><input v-model="settings.data_retention" type="number" class="input" /></div>
      <button class="btn btn-primary" @click="saveSettings">保存设置</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/utils/api'

const settings = ref({
  site_name: '森码云实人认证系统',
  site_domain: 'face.builds.codes',
  mofang_url: '',
  max_retry: '3',
  liveness_threshold: '80',
  data_retention: '24',
})

async function saveSettings() {
  try {
    await api.post('/admin/setting/save', { settings: settings.value })
    alert('保存成功')
  } catch (e) {
    alert('保存失败')
  }
}
</script>

<style scoped>
.page-header { margin-bottom: 24px; }
.page-header h3 { font-size: 20px; margin-bottom: 4px; }
.card { max-width: 600px; }
.form-group { margin-bottom: 16px; }
</style>