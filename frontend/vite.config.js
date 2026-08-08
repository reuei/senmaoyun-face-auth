import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
    },
  },
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost',
        changeOrigin: true,
      },
      '/admin': {
        target: 'http://localhost',
        changeOrigin: true,
      },
      '/install': {
        target: 'http://localhost',
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: '../public',
    emptyOutDir: true,
    assetsDir: 'static',
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router', 'pinia', 'axios'],
          echarts: ['echarts', 'vue-echarts'],
        },
      },
    },
  },
})