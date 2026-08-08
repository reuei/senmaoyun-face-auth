<template>
  <div class="driver-page">
    <div class="page-header">
      <h3>接口管理</h3>
      <p class="text-muted">管理人脸识别接口驱动，配置密钥、启用/禁用、设置主备</p>
    </div>
    <div class="driver-list">
      <div v-for="driver in drivers" :key="driver.code" class="card driver-card">
        <div class="driver-header">
          <div class="driver-info">
            <h4>{{ driver.name }}</h4>
            <span class="driver-code">{{ driver.code }}</span>
          </div>
          <div class="driver-actions">
            <span class="badge" :class="driver.enabled ? 'badge-success' : 'badge-error'">
              {{ driver.enabled ? '已启用' : '已禁用' }}
            </span>
            <span v-if="driver.is_default" class="badge badge-info">默认</span>
          </div>
        </div>
        <div class="driver-config" v-if="driver.expanded">
          <div class="form-group" v-for="(val, key) in driver.config" :key="key">
            <label class="label">{{ configLabels[key] || key }}</label>
            <input v-model="driver.config[key]" type="password" class="input" :placeholder="'请输入' + (configLabels[key] || key)" />
          </div>
          <div class="driver-footer">
            <button class="btn btn-sm btn-primary" @click="testDriver(driver)">测试连接</button>
            <button class="btn btn-sm btn-success" @click="saveDriver(driver)">保存配置</button>
          </div>
        </div>
        <div class="driver-toggle" @click="driver.expanded = !driver.expanded">
          {{ driver.expanded ? '收起配置' : '展开配置' }}
          <ChevronDown :size="16" :style="{ transform: driver.expanded ? 'rotate(180deg)' : '' }" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'
import { ChevronDown } from 'lucide-vue-next'

const configLabels = {
  secret_id: 'SecretId',
  secret_key: 'SecretKey',
  region: '地域',
  api_key: 'API Key',
  app_id: 'App ID',
  app_code: 'AppCode',
  private_key: '应用私钥',
  alipay_public_key: '支付宝公钥',
}

const drivers = ref([
  { code: 'self', name: '自研活体检测', enabled: true, is_default: true, expanded: false, config: {} },
  { code: 'tencent', name: '腾讯云慧眼', enabled: false, is_default: false, expanded: false, config: { secret_id: '', secret_key: '', region: 'ap-guangzhou' } },
  { code: 'baidu', name: '百度智能云', enabled: false, is_default: false, expanded: false, config: { api_key: '', secret_key: '', app_id: '' } },
  { code: 'alipay', name: '支付宝活体检测', enabled: false, is_default: false, expanded: false, config: { app_id: '', private_key: '', alipay_public_key: '' } },
  { code: 'juhe', name: '聚合数据', enabled: false, is_default: false, expanded: false, config: { api_key: '' } },
  { code: 'aliyun_market', name: '阿里云市场', enabled: false, is_default: false, expanded: false, config: { app_code: '' } },
])

async function saveDriver(driver) {
  try {
    await api.post('/admin/driver/save', { driver_code: driver.code, config: driver.config, enabled: driver.enabled ? 1 : 0, is_default: driver.is_default ? 1 : 0 })
    alert('保存成功')
  } catch (e) {
    alert('保存失败')
  }
}

async function testDriver(driver) {
  try {
    const res = await api.post('/admin/driver/test', { driver_code: driver.code, config: driver.config })
    alert(res.data.msg || '测试完成')
  } catch (e) {
    alert('测试失败')
  }
}
</script>

<style scoped>
.page-header { margin-bottom: 24px; }
.page-header h3 { font-size: 20px; margin-bottom: 4px; }
.text-muted { color: var(--color-text-muted); font-size: 13px; }
.driver-list { display: flex; flex-direction: column; gap: 16px; }
.driver-card { padding: 20px 24px; }
.driver-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.driver-info h4 { font-size: 16px; margin-bottom: 2px; }
.driver-code { font-size: 12px; color: var(--color-text-muted); font-family: var(--font-mono); }
.driver-actions { display: flex; gap: 8px; }
.driver-config { border-top: 1px solid var(--color-border); padding-top: 16px; margin-top: 4px; }
.form-group { margin-bottom: 12px; }
.driver-footer { display: flex; gap: 8px; margin-top: 16px; }
.driver-toggle { display: flex; align-items: center; justify-content: center; gap: 4px; padding-top: 12px; color: var(--color-text-muted); font-size: 13px; cursor: pointer; user-select: none; }
</style>