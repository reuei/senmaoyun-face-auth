<template>
  <div class="plugin-page">
    <div class="page-header"><h3>插件中心</h3><p class="text-muted">魔方财务认证插件下载与安装指引</p></div>
    <div class="card plugin-card">
      <Package :size="48" color="var(--color-primary)" />
      <h4>魔方财务实人认证插件</h4>
      <p>适用于魔方财务系统的 certification 类型认证插件，提供实人认证能力。</p>
      <div class="plugin-info">
        <span>版本: 1.0.0</span>
        <span>类型: certification</span>
        <span>路径: /public/plugins/certification/senmaoyun/</span>
      </div>
      <a href="/admin/plugin/download" class="btn btn-primary">
        <Download :size="16" />
        下载插件
      </a>
      <div class="plugin-guide">
        <h5>安装步骤</h5>
        <ol>
          <li>下载插件压缩包</li>
          <li>解压到魔方财务系统 /public/plugins/certification/senmaoyun/</li>
          <li>在魔方财务后台「客户 > 设置 > 实名设置 > 接口设置」中启用</li>
          <li>配置森码云系统地址：https://face.builds.codes</li>
          <li>配置API Key（在森码云后台生成）</li>
        </ol>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Package, Download } from 'lucide-vue-next'
</script>

<style scoped>
.page-header { margin-bottom: 24px; }
.page-header h3 { font-size: 20px; margin-bottom: 4px; }
.plugin-card { text-align: center; max-width: 600px; padding: 32px; }
.plugin-card h4 { font-size: 18px; margin: 16px 0 8px; }
.plugin-card p { color: var(--color-text-secondary); margin-bottom: 16px; }
.plugin-info { display: flex; gap: 16px; justify-content: center; font-size: 12px; color: var(--color-text-muted); margin-bottom: 20px; }
.plugin-guide { text-align: left; margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--color-border); }
.plugin-guide h5 { font-size: 14px; margin-bottom: 8px; }
.plugin-guide ol { padding-left: 20px; font-size: 13px; color: var(--color-text-secondary); line-height: 2; }
</style>