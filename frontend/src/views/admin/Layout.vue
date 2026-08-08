<template>
  <div class="admin-layout">
    <!-- 侧边栏 -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <ShieldCheck :size="24" color="var(--color-primary)" />
        <span class="sidebar-title">森码云</span>
      </div>
      <nav class="sidebar-nav">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="nav-item"
          :class="{ active: isActive(item.path) }"
        >
          <component :is="item.icon" :size="18" />
          <span>{{ item.label }}</span>
        </router-link>
      </nav>
      <div class="sidebar-footer">
        <a href="/" class="nav-item">
          <Home :size="18" />
          <span>返回首页</span>
        </a>
      </div>
    </aside>

    <!-- 主内容区 -->
    <main class="main-content">
      <header class="topbar">
        <h2 class="page-title">{{ pageTitle }}</h2>
        <div class="topbar-actions">
          <button class="btn btn-sm btn-secondary" @click="toggleDarkMode">
            <Moon v-if="!isDark" :size="16" />
            <Sun v-else :size="16" />
          </button>
          <a href="/admin/logout" class="btn btn-sm btn-secondary">
            <LogOut :size="16" />
            退出
          </a>
        </div>
      </header>
      <div class="page-content">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import {
  ShieldCheck, Home, LayoutDashboard, Cpu, FileText,
  UserCheck, Key, Settings, Package, LogOut, Moon, Sun,
} from 'lucide-vue-next'

const route = useRoute()
const isDark = ref(false)

const menuItems = [
  { path: '/admin/dashboard', label: '控制台', icon: LayoutDashboard },
  { path: '/admin/driver', label: '接口管理', icon: Cpu },
  { path: '/admin/record', label: '认证记录', icon: FileText },
  { path: '/admin/audit', label: '人工审核', icon: UserCheck },
  { path: '/admin/token', label: 'Token管理', icon: Key },
  { path: '/admin/setting', label: '系统设置', icon: Settings },
  { path: '/admin/plugin', label: '插件中心', icon: Package },
]

const pageTitle = computed(() => {
  const item = menuItems.find(i => route.path.startsWith(i.path))
  return item ? item.label : '管理后台'
})

function isActive(path) {
  return route.path === path || route.path.startsWith(path + '/')
}

function toggleDarkMode() {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
}
</script>

<style scoped>
.admin-layout { display: flex; min-height: 100vh; }
.sidebar { width: 240px; background: var(--color-bg-white); border-right: 1px solid var(--color-border); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; }
.sidebar-header { display: flex; align-items: center; gap: 10px; padding: 20px 20px 16px; border-bottom: 1px solid var(--color-border); }
.sidebar-title { font-size: 18px; font-weight: 700; }
.sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 20px; color: var(--color-text-secondary); text-decoration: none; font-size: 14px; transition: all var(--transition); border-left: 3px solid transparent; }
.nav-item:hover { color: var(--color-text); background: var(--color-border-light); }
.nav-item.active { color: var(--color-primary); background: var(--color-primary-light); border-left-color: var(--color-primary); font-weight: 500; }
.sidebar-footer { border-top: 1px solid var(--color-border); padding: 8px 0; }
.main-content { flex: 1; margin-left: 240px; min-height: 100vh; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 16px 32px; background: var(--color-bg-white); border-bottom: 1px solid var(--color-border); position: sticky; top: 0; z-index: 40; }
.page-title { font-size: 18px; font-weight: 600; }
.topbar-actions { display: flex; gap: 8px; }
.page-content { padding: 32px; }

@media (max-width: 768px) {
  .sidebar { width: 64px; }
  .sidebar-title, .nav-item span { display: none; }
  .main-content { margin-left: 64px; }
  .page-content { padding: 16px; }
}
</style>