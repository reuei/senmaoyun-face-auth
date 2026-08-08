import { createRouter, createWebHistory } from 'vue-router'
import NProgress from 'nprogress'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/home/Index.vue'),
    meta: { title: '森码云实人认证系统' },
  },
  {
    path: '/verify',
    name: 'Verify',
    component: () => import('@/views/verify/Index.vue'),
    meta: { title: '实人认证 - 森码云' },
  },
  {
    path: '/forbidden',
    name: 'Forbidden',
    component: () => import('@/views/error/Forbidden.vue'),
    meta: { title: '访问受限 - 森码云' },
  },
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: () => import('@/views/admin/Login.vue'),
    meta: { title: '管理员登录 - 森码云' },
  },
  {
    path: '/admin',
    name: 'Admin',
    component: () => import('@/views/admin/Layout.vue'),
    meta: { title: '管理后台 - 森码云', requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard',
      },
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('@/views/admin/Dashboard.vue'),
        meta: { title: '控制台 - 森码云' },
      },
      {
        path: 'driver',
        name: 'Driver',
        component: () => import('@/views/admin/Driver.vue'),
        meta: { title: '接口管理 - 森码云' },
      },
      {
        path: 'record',
        name: 'Record',
        component: () => import('@/views/admin/Record.vue'),
        meta: { title: '认证记录 - 森码云' },
      },
      {
        path: 'audit',
        name: 'Audit',
        component: () => import('@/views/admin/Audit.vue'),
        meta: { title: '人工审核 - 森码云' },
      },
      {
        path: 'token',
        name: 'Token',
        component: () => import('@/views/admin/Token.vue'),
        meta: { title: 'Token管理 - 森码云' },
      },
      {
        path: 'setting',
        name: 'Setting',
        component: () => import('@/views/admin/Setting.vue'),
        meta: { title: '系统设置 - 森码云' },
      },
      {
        path: 'plugin',
        name: 'Plugin',
        component: () => import('@/views/admin/Plugin.vue'),
        meta: { title: '插件中心 - 森码云' },
      },
    ],
  },
  {
    path: '/install',
    name: 'Install',
    component: () => import('@/views/install/Index.vue'),
    meta: { title: '安装向导 - 森码云' },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/error/NotFound.vue'),
    meta: { title: '页面未找到 - 森码云' },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

router.beforeEach((to, _from, next) => {
  NProgress.start()
  document.title = to.meta.title || '森码云实人认证系统'
  next()
})

router.afterEach(() => {
  NProgress.done()
})

export default router