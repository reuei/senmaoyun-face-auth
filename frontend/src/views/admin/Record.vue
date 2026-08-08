<template>
  <div class="record-page">
    <div class="page-header">
      <h3>认证记录</h3>
      <p class="text-muted">查看所有认证记录，支持详情查看和CSV导出</p>
    </div>
    <div class="card">
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>记录编号</th>
              <th>用户ID</th>
              <th>姓名</th>
              <th>状态</th>
              <th>活体分数</th>
              <th>接口</th>
              <th>认证时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in records" :key="record.id">
              <td class="mono">{{ record.record_no?.substring(0, 16) }}...</td>
              <td>{{ record.user_id }}</td>
              <td>{{ record.masked_name || '***' }}</td>
              <td>
                <span class="badge" :class="statusClass(record.status)">{{ statusText(record.status) }}</span>
              </td>
              <td>{{ record.liveness_score || '-' }}</td>
              <td>{{ record.driver_code }}</td>
              <td>{{ record.certify_time || '-' }}</td>
              <td>
                <button class="btn btn-sm btn-secondary" @click="showDetail(record)">详情</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const records = ref([])

const statusClass = (status) => ({
  success: 'badge-success',
  failed: 'badge-error',
  auditing: 'badge-warning',
  pending: 'badge-info',
  processing: 'badge-info',
}[status] || 'badge-info')

const statusText = (status) => ({
  success: '通过',
  failed: '失败',
  auditing: '审核中',
  pending: '待处理',
  processing: '处理中',
}[status] || status)

function showDetail(record) {
  alert('记录详情: ' + record.record_no)
}
</script>

<style scoped>
.page-header { margin-bottom: 24px; }
.page-header h3 { font-size: 20px; margin-bottom: 4px; }
.table-container { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--color-border); }
.data-table th { font-weight: 600; color: var(--color-text-secondary); background: var(--color-bg); }
.data-table tr:hover td { background: var(--color-border-light); }
.mono { font-family: var(--font-mono); font-size: 12px; }
</style>