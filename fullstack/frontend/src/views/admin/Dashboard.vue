<template>
  <div class="dashboard">
    <div class="stats-grid">
      <div class="stat-card card">
        <div class="stat-header">
          <span class="stat-label">今日认证</span>
          <ScanFace :size="20" color="var(--color-primary)" />
        </div>
        <div class="stat-value">{{ stats.today?.total || 0 }}</div>
        <div class="stat-sub">通过 {{ stats.today?.success || 0 }} 次</div>
      </div>
      <div class="stat-card card">
        <div class="stat-header">
          <span class="stat-label">通过率</span>
          <TrendingUp :size="20" color="var(--color-success)" />
        </div>
        <div class="stat-value">{{ stats.total?.pass_rate || 0 }}%</div>
        <div class="stat-sub">累计 {{ stats.total?.total || 0 }} 次</div>
      </div>
      <div class="stat-card card">
        <div class="stat-header">
          <span class="stat-label">待审核</span>
          <Clock :size="20" color="var(--color-warning)" />
        </div>
        <div class="stat-value">{{ stats.today?.auditing || 0 }}</div>
        <div class="stat-sub">人工审核队列</div>
      </div>
      <div class="stat-card card">
        <div class="stat-header">
          <span class="stat-label">平均活体分数</span>
          <Activity :size="20" color="var(--color-info)" />
        </div>
        <div class="stat-value">{{ stats.today?.avg_score || 0 }}</div>
        <div class="stat-sub">今日平均</div>
      </div>
    </div>

    <div class="charts-grid">
      <div class="card chart-card">
        <h3>最近7天认证趋势</h3>
        <div ref="trendChartRef" style="width:100%;height:300px"></div>
      </div>
      <div class="card chart-card">
        <h3>接口调用分布</h3>
        <div ref="driverChartRef" style="width:100%;height:300px"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import * as echarts from 'echarts'
import api from '@/utils/api'
import { ScanFace, TrendingUp, Clock, Activity } from 'lucide-vue-next'

const stats = ref({ today: {}, total: {}, driver_stats: [], trend: [] })
const trendChartRef = ref(null)
const driverChartRef = ref(null)

onMounted(async () => {
  try {
    const res = await api.get('/admin/dashboard/stats')
    if (res.data.code === 200) {
      stats.value = res.data.data
      await nextTick()
      renderCharts()
    }
  } catch (e) {
    // 使用默认数据
    stats.value = {
      today: { total: 0, success: 0, failed: 0, auditing: 0, avg_score: 0 },
      total: { total: 0, pass_rate: 0 },
      driver_stats: [],
      trend: [],
    }
  }
})

function renderCharts() {
  if (trendChartRef.value) {
    const chart = echarts.init(trendChartRef.value)
    chart.setOption({
      tooltip: { trigger: 'axis' },
      grid: { left: 40, right: 20, top: 20, bottom: 30 },
      xAxis: { type: 'category', data: stats.value.trend?.map(t => t.date) || [] },
      yAxis: { type: 'value' },
      series: [
        {
          name: '总认证',
          type: 'line',
          data: stats.value.trend?.map(t => t.total) || [],
          smooth: true,
          lineStyle: { color: '#4F46E5' },
          itemStyle: { color: '#4F46E5' },
        },
        {
          name: '通过',
          type: 'line',
          data: stats.value.trend?.map(t => t.success) || [],
          smooth: true,
          lineStyle: { color: '#10B981' },
          itemStyle: { color: '#10B981' },
        },
      ],
    })
  }

  if (driverChartRef.value) {
    const chart = echarts.init(driverChartRef.value)
    const driverData = stats.value.driver_stats || []
    chart.setOption({
      tooltip: { trigger: 'item' },
      series: [
        {
          type: 'pie',
          radius: ['40%', '70%'],
          data: driverData.map(d => ({
            name: d.driver_code || '未知',
            value: d.count || 0,
          })),
          label: { show: true, formatter: '{b}: {c}' },
        },
      ],
    })
  }
}
</script>

<style scoped>
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
.stat-card { padding: 20px 24px; }
.stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.stat-label { font-size: 13px; color: var(--color-text-secondary); }
.stat-value { font-size: 28px; font-weight: 700; letter-spacing: -0.02em; }
.stat-sub { font-size: 12px; color: var(--color-text-muted); margin-top: 4px; }
.charts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.chart-card { padding: 24px; }
.chart-card h3 { font-size: 16px; margin-bottom: 16px; }

@media (max-width: 1024px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .charts-grid { grid-template-columns: 1fr; }
}
</style>