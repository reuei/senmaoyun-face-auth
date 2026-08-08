<template>
  <div class="error-page">
    <div class="card error-card animate__animated animate__fadeInUp">
      <div class="error-code">404</div>
      <h1>页面未找到</h1>
      <p class="error-desc">您访问的页面不存在或已被移除</p>
      <a href="/" class="btn btn-primary">返回首页</a>
    </div>
  </div>
</template>

<style scoped>
.error-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
.error-card { text-align: center; max-width: 480px; padding: 48px 40px; }
.error-code { font-size: 72px; font-weight: 800; color: var(--color-primary); line-height: 1; margin-bottom: 8px; letter-spacing: -0.03em; }
h1 { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
.error-desc { color: var(--color-text-secondary); margin-bottom: 24px; }
</style>