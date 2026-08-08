<template>
  <div class="home-page">
    <!-- 导航栏 -->
    <header class="navbar">
      <div class="container navbar-inner">
        <a href="/" class="logo">
          <ShieldCheck class="logo-icon" :size="28" />
          <span class="logo-text">森码云</span>
        </a>
        <nav class="nav-links">
          <a href="#features">功能特性</a>
          <a href="#process">认证流程</a>
          <a href="#security">安全合规</a>
          <a href="/admin/login" class="btn btn-sm btn-secondary">管理后台</a>
        </nav>
      </div>
    </header>

    <!-- Hero区间 -->
    <section class="hero">
      <div class="container hero-inner">
        <div class="hero-content animate__animated animate__fadeInUp">
          <div class="hero-badge">
            <Zap :size="16" />
            <span>企业级服务</span>
          </div>
          <h1 class="hero-title">
            企业级<br /><span class="highlight">实人认证服务</span>
          </h1>
          <p class="hero-desc">
            基于多源人脸识别API与自研活体检测算法，为魔方财务系统提供安全、合规、高效的实人认证解决方案。
          </p>
          <div class="hero-actions">
            <a href="#features" class="btn btn-primary btn-lg">
              了解更多
              <ArrowRight :size="18" />
            </a>
            <a href="#process" class="btn btn-secondary btn-lg">
              认证流程
            </a>
          </div>
          <div class="hero-stats">
            <div class="stat">
              <span class="stat-value">6+</span>
              <span class="stat-label">人脸识别接口</span>
            </div>
            <div class="stat">
              <span class="stat-value">99.9%</span>
              <span class="stat-label">服务可用性</span>
            </div>
            <div class="stat">
              <span class="stat-value">GB/T</span>
              <span class="stat-label">国家标准合规</span>
            </div>
          </div>
        </div>
        <div class="hero-illustration animate__animated animate__fadeInRight">
          <svg viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="60" y="80" width="380" height="280" rx="24" fill="var(--color-bg-white)" stroke="var(--color-border)" stroke-width="2"/>
            <rect x="100" y="130" width="300" height="200" rx="12" fill="var(--color-primary-light)"/>
            <circle cx="250" cy="200" r="60" fill="var(--color-primary)" opacity="0.15"/>
            <circle cx="250" cy="200" r="40" fill="var(--color-primary)" opacity="0.3"/>
            <circle cx="250" cy="200" r="20" fill="var(--color-primary)"/>
            <path d="M235 195 L245 205 L265 185" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="200" y="345" width="100" height="8" rx="4" fill="var(--color-border)"/>
            <rect x="180" y="360" width="140" height="6" rx="3" fill="var(--color-border)"/>
            <circle cx="120" cy="60" r="8" fill="var(--color-success)" opacity="0.5"/>
            <circle cx="400" cy="70" r="6" fill="var(--color-primary)" opacity="0.3"/>
            <circle cx="450" cy="300" r="10" fill="var(--color-warning)" opacity="0.4"/>
          </svg>
        </div>
      </div>
    </section>

    <!-- 功能特性 -->
    <section id="features" class="features">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">核心功能特性</h2>
          <p class="section-desc">六大核心能力，打造安全可靠的实人认证体验</p>
        </div>
        <div class="features-grid">
          <div class="feature-card" v-for="feature in features" :key="feature.title">
            <div class="feature-icon" :style="{ background: feature.bg }">
              <component :is="feature.icon" :size="24" :color="feature.color" />
            </div>
            <h3 class="feature-title">{{ feature.title }}</h3>
            <p class="feature-desc">{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 认证流程 -->
    <section id="process" class="process">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">认证流程</h2>
          <p class="section-desc">四步完成实人认证，安全高效</p>
        </div>
        <div class="process-steps">
          <div class="process-step" v-for="(step, index) in processSteps" :key="index">
            <div class="process-step-num">{{ index + 1 }}</div>
            <div class="process-step-icon">
              <component :is="step.icon" :size="32" />
            </div>
            <h3 class="process-step-title">{{ step.title }}</h3>
            <p class="process-step-desc">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 安全合规 -->
    <section id="security" class="security">
      <div class="container">
        <div class="security-card card">
          <div class="security-content">
            <Shield :size="40" color="var(--color-primary)" />
            <h2>安全合规保障</h2>
            <ul>
              <li>符合《个人信息保护法》要求，明示告知、最小必要、撤回同意</li>
              <li>人脸数据加密存储（AES-256-GCM），默认24小时后自动清理</li>
              <li>全站HTTPS加密传输，CSRF防护，SQL注入防护</li>
              <li>完整的审计日志记录，所有操作可追溯</li>
              <li>速率限制，防止恶意攻击</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- 页脚 -->
    <footer class="footer">
      <div class="container footer-inner">
        <div class="footer-brand">
          <span class="footer-logo">森码云实人认证系统</span>
          <span class="footer-domain">face.builds.codes</span>
        </div>
        <div class="footer-links">
          <a href="#">服务协议</a>
          <a href="#">隐私政策</a>
          <a href="#">人脸识别授权书</a>
          <a href="/admin/login">管理后台</a>
        </div>
        <div class="footer-copy">
          &copy; {{ new Date().getFullYear() }} 森码云. All rights reserved.
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ShieldCheck, Zap, ArrowRight, ScanFace, Shield, Fingerprint, Database, Globe, Users, FileCheck } from 'lucide-vue-next'

const features = [
  {
    icon: ScanFace,
    title: '多源活体检测',
    desc: '支持腾讯云、百度、支付宝等6种人脸识别接口，主备自动切换，确保服务可用性',
    bg: '#EEF2FF',
    color: '#4F46E5',
  },
  {
    icon: Fingerprint,
    title: '自研检测算法',
    desc: '内置动作序列分析、光流变化检测、摩尔纹检测、翻拍检测等自研活体识别能力',
    bg: '#D1FAE5',
    color: '#10B981',
  },
  {
    icon: Database,
    title: '魔方财务对接',
    desc: '提供完整的certification类型插件，Token安全机制，一键集成到魔方财务系统',
    bg: '#FEF3C7',
    color: '#F59E0B',
  },
  {
    icon: Globe,
    title: '虚拟主机适配',
    desc: '支持Apache/Nginx，提供.htaccess和nginx伪静态配置，兼容各类共享主机环境',
    bg: '#DBEAFE',
    color: '#3B82F6',
  },
  {
    icon: Users,
    title: '人工审核队列',
    desc: '自动认证失败自动转入人工审核，审核员可查看采集数据，手动通过或驳回',
    bg: '#FCE7F3',
    color: '#EC4899',
  },
  {
    icon: FileCheck,
    title: '身份证校验',
    desc: '内置GB/T 2260行政区划码表，实现ISO 7064:1983 MOD 11-2校验算法，自动识别性别和年龄',
    bg: '#F3E8FF',
    color: '#9333EA',
  },
]

const processSteps = [
  { icon: FileCheck, title: '同意协议', desc: '阅读并签署《实人认证服务协议》《隐私政策》《人脸识别授权书》' },
  { icon: Users, title: '身份录入', desc: '输入姓名和身份证号，系统自动校验格式、行政区划、校验码合法性' },
  { icon: ScanFace, title: '人脸识别', desc: '开启摄像头，依次完成眨眼、张嘴、点头、摇头等活体检测动作' },
  { icon: Shield, title: '结果返回', desc: '检测结果通过Token安全回调至魔方财务系统，完成实名认证' },
]
</script>

<style scoped>
.home-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* 导航栏 */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--color-border);
}
.navbar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
}
.logo { display: flex; align-items: center; gap: 8px; color: var(--color-text); text-decoration: none; }
.logo-text { font-size: 20px; font-weight: 700; letter-spacing: -0.02em; }
.nav-links { display: flex; align-items: center; gap: 24px; }
.nav-links a { font-size: 14px; color: var(--color-text-secondary); text-decoration: none; transition: color var(--transition); }
.nav-links a:hover { color: var(--color-text); }

/* Hero */
.hero { padding: 120px 0 80px; }
.hero-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; }
.hero-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: var(--color-primary-light); color: var(--color-primary); border-radius: 999px; font-size: 13px; font-weight: 500; margin-bottom: 20px; }
.hero-title { font-size: 48px; font-weight: 800; line-height: 1.15; letter-spacing: -0.03em; margin-bottom: 20px; }
.hero-title .highlight { color: var(--color-primary); }
.hero-desc { font-size: 17px; color: var(--color-text-secondary); line-height: 1.7; margin-bottom: 32px; max-width: 480px; }
.hero-actions { display: flex; gap: 12px; margin-bottom: 40px; }
.hero-stats { display: flex; gap: 40px; }
.stat { display: flex; flex-direction: column; }
.stat-value { font-size: 24px; font-weight: 700; color: var(--color-text); }
.stat-label { font-size: 13px; color: var(--color-text-muted); margin-top: 2px; }
.hero-illustration { display: flex; justify-content: center; }

/* 功能特性 */
.features { padding: 80px 0; background: var(--color-bg-white); }
.section-header { text-align: center; margin-bottom: 48px; }
.section-title { font-size: 32px; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 12px; }
.section-desc { font-size: 16px; color: var(--color-text-secondary); }
.features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.feature-card { padding: 28px; border: 1px solid var(--color-border); border-radius: var(--radius-xl); transition: all var(--transition); }
.feature-card:hover { border-color: var(--color-primary); box-shadow: var(--shadow-md); transform: translateY(-2px); }
.feature-icon { width: 48px; height: 48px; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
.feature-title { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
.feature-desc { font-size: 14px; color: var(--color-text-secondary); line-height: 1.6; }

/* 认证流程 */
.process { padding: 80px 0; }
.process-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
.process-step { text-align: center; padding: 32px 20px; border: 1px solid var(--color-border); border-radius: var(--radius-xl); position: relative; }
.process-step-num { width: 36px; height: 36px; border-radius: 50%; background: var(--color-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 0 auto 16px; }
.process-step-icon { margin-bottom: 16px; color: var(--color-primary); }
.process-step-title { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
.process-step-desc { font-size: 13px; color: var(--color-text-secondary); line-height: 1.5; }

/* 安全合规 */
.security { padding: 80px 0; background: var(--color-bg-white); }
.security-card { max-width: 800px; margin: 0 auto; }
.security-content h2 { font-size: 24px; margin: 16px 0; }
.security-content ul { padding-left: 20px; }
.security-content li { font-size: 14px; color: var(--color-text-secondary); padding: 6px 0; line-height: 1.6; }

/* 页脚 */
.footer { padding: 40px 0; border-top: 1px solid var(--color-border); margin-top: auto; }
.footer-inner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.footer-brand { display: flex; flex-direction: column; }
.footer-logo { font-weight: 600; font-size: 14px; }
.footer-domain { font-size: 12px; color: var(--color-text-muted); }
.footer-links { display: flex; gap: 20px; }
.footer-links a { font-size: 13px; color: var(--color-text-secondary); }
.footer-copy { font-size: 12px; color: var(--color-text-muted); }

@media (max-width: 768px) {
  .hero-inner { grid-template-columns: 1fr; text-align: center; }
  .hero-title { font-size: 32px; }
  .hero-desc { max-width: 100%; }
  .hero-actions { justify-content: center; }
  .hero-stats { justify-content: center; }
  .hero-illustration { display: none; }
  .features-grid { grid-template-columns: 1fr; }
  .process-steps { grid-template-columns: 1fr 1fr; }
  .nav-links { display: none; }
}
</style>