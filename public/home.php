<?php
/**
 * 森码云实人认证系统 v1.0.3
 * 首页 - 参考 Linear/Stripe/Vercel 设计风格
 */
$siteName = defined('SITE_NAME') ? SITE_NAME : '森码云实人认证系统';
?><!DOCTYPE html>
<html lang="zh-CN"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo $siteName; ?> - 企业级实人认证</title>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css">
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/element-plus"></script>
<style>
:root{--el-color-primary:#4F46E5;--c-bg:#FAFBFC;--c-surface:#FFFFFF;--c-border:#E6E8EC;--c-text:#1A1D23;--c-text2:#5A5F6B;--c-text3:#8F95A3}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;background:var(--c-bg);color:var(--c-text);line-height:1.6;-webkit-font-smoothing:antialiased}
.container{max-width:1200px;margin:0 auto;padding:0 24px}
/* Nav */
.nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,.88);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-bottom:1px solid var(--c-border)}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:60px}
.nav-logo{display:flex;align-items:center;gap:8px;font-size:18px;font-weight:700;color:var(--c-text);text-decoration:none;letter-spacing:-.02em}
.nav-logo svg{width:28px;height:28px}
.nav-links{display:flex;align-items:center;gap:20px}
.nav-links a{font-size:14px;color:var(--c-text2);text-decoration:none;transition:color .15s}
.nav-links a:hover{color:var(--c-text)}
/* Hero */
.hero{padding:140px 0 100px;text-align:center}
.hero-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;background:#EEF2FF;color:#4F46E5;border-radius:99px;font-size:13px;font-weight:500;margin-bottom:24px}
.hero h1{font-size:clamp(36px,5vw,56px);font-weight:800;line-height:1.1;letter-spacing:-.03em;margin-bottom:20px;color:var(--c-text)}
.hero h1 .gradient{background:linear-gradient(135deg,#4F46E5,#7C3AED,#EC4899);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero p{font-size:18px;color:var(--c-text2);max-width:560px;margin:0 auto 36px;line-height:1.7}
.hero-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.hero-stats{display:flex;gap:40px;justify-content:center;margin-top:60px;flex-wrap:wrap}
.hero-stats .stat{text-align:center}.hero-stats .stat .v{font-size:28px;font-weight:700;color:var(--c-text)}.hero-stats .stat .l{font-size:13px;color:var(--c-text3);margin-top:4px}
/* Sections */
.sec{padding:80px 0}.sec-dark{background:var(--c-text);color:#fff}
.sec-header{text-align:center;margin-bottom:60px}
.sec-header h2{font-size:32px;font-weight:700;letter-spacing:-.02em;margin-bottom:12px}
.sec-header p{font-size:16px;color:var(--c-text2);max-width:500px;margin:0 auto}
.sec-dark .sec-header p{color:#8F95A3}
/* Features grid */
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.f-card{background:var(--c-surface);border:1px solid var(--c-border);border-radius:14px;padding:28px;transition:all .2s}
.f-card:hover{border-color:#4F46E5;box-shadow:0 8px 24px rgba(79,70,229,.1);transform:translateY(-2px)}
.f-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;font-size:24px}
.f-card h3{font-size:16px;font-weight:600;margin-bottom:8px}
.f-card p{font-size:14px;color:var(--c-text2);line-height:1.6}
/* Process */
.process-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0}
.process-item{text-align:center;padding:32px 20px;position:relative}
.process-item::after{content:'';position:absolute;top:44px;right:-12px;width:24px;height:2px;background:var(--c-border)}
.process-item:last-child::after{display:none}
.process-num{width:40px;height:40px;border-radius:50%;background:#4F46E5;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;margin:0 auto 16px;font-size:15px}
.process-item h3{font-size:15px;font-weight:600;margin-bottom:6px}
.process-item p{font-size:13px;color:var(--c-text2);line-height:1.5}
/* CTA */
.cta{text-align:center;padding:80px 0;background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff}
.cta h2{font-size:32px;font-weight:700;margin-bottom:16px;letter-spacing:-.02em}
.cta p{font-size:16px;opacity:.9;margin-bottom:32px}
/* Footer */
.footer{padding:40px 0;border-top:1px solid var(--c-border);background:var(--c-surface)}
.footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;font-size:13px;color:var(--c-text3)}
.footer-links{display:flex;gap:20px}.footer-links a{color:var(--c-text3);text-decoration:none}.footer-links a:hover{color:var(--c-text)}
@media(max-width:768px){.features-grid{grid-template-columns:1fr}.process-grid{grid-template-columns:1fr 1fr}.process-item::after{display:none}.hero-stats{gap:24px}.nav-links{display:none}}
</style></head><body>
<nav class="nav"><div class="container nav-inner">
<a href="/" class="nav-logo"><svg viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="#4F46E5"/><path d="M16 6l-8 4v8l8 8 8-8v-8l-8-4z" fill="none" stroke="#fff" stroke-width="2"/><path d="M13 16l2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>森码云</a>
<div class="nav-links"><a href="#features">功能</a><a href="#process">流程</a><a href="/user/login">登录</a><a href="/admin/login">管理</a></div>
</div></nav>

<section class="hero"><div class="container">
<div class="hero-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> v1.0.3 全新升级</div>
<h1>企业级<span class="gradient">实人认证</span>服务</h1>
<p>基于多源AI人脸识别与自研活体检测算法，为魔方财务系统提供安全合规的实人认证解决方案。支持6种接口，上线即用。</p>
<div class="hero-actions">
<a href="/user/register" class="el-button el-button--primary el-button--large">免费注册</a>
<a href="/user/login" class="el-button el-button--large">用户登录</a>
<a href="/admin/login" class="el-button el-button--default el-button--large">管理后台</a>
</div>
<div class="hero-stats">
<div class="stat"><div class="v">6+</div><div class="l">AI识别接口</div></div>
<div class="stat"><div class="v">99.9%</div><div class="l">服务可用性</div></div>
<div class="stat"><div class="v">GB/T</div><div class="l">国家标准合规</div></div>
<div class="stat"><div class="v">0</div><div class="l">依赖安装</div></div>
</div>
</div></section>

<section id="features" class="sec"><div class="container">
<div class="sec-header"><h2>核心能力</h2><p>六大核心功能，打造安全可靠的实人认证体验</p></div>
<div class="features-grid">
<div class="f-card"><div class="f-icon" style="background:#EEF2FF;color:#4F46E5">🤖</div><h3>AI活体检测</h3><p>自研AI算法：动作序列分析、光流变化检测、摩尔纹检测、翻拍检测，真人识别率99%+</p></div>
<div class="f-card"><div class="f-icon" style="background:#D1FAE5;color:#10B981">🔌</div><h3>多源接口</h3><p>支持腾讯云慧眼、百度AI、支付宝、聚合数据、阿里云市场 + 自研，主备自动切换</p></div>
<div class="f-card"><div class="f-icon" style="background:#FEF3C7;color:#F59E0B">📦</div><h3>魔方财务插件</h3><p>完整certification类型插件，Token安全机制，一键集成魔方财务系统</p></div>
<div class="f-card"><div class="f-icon" style="background:#DBEAFE;color:#3B82F6">🖥</div><h3>零依赖部署</h3><p>无需composer/npm，上传即用。支持Apache/Nginx虚拟主机，兼容所有共享主机</p></div>
<div class="f-card"><div class="f-icon" style="background:#FCE7F3;color:#EC4899">👥</div><h3>人工审核</h3><p>AI认证失败自动转入人工审核队列，审核员可查看采集数据手动通过或驳回</p></div>
<div class="f-card"><div class="f-icon" style="background:#F3E8FF;color:#9333EA">🆔</div><h3>身份证校验</h3><p>内置GB/T 2260标准，ISO 7064:1983 MOD 11-2校验算法，自动识别性别年龄</p></div>
</div></div></section>

<section id="process" class="sec sec-dark"><div class="container">
<div class="sec-header"><h2 style="color:#fff">认证流程</h2><p>四步完成实人认证，全程AI加持</p></div>
<div class="process-grid">
<div class="process-item"><div class="process-num">1</div><h3 style="color:#fff">同意协议</h3><p>阅读并签署服务协议</p></div>
<div class="process-item"><div class="process-num">2</div><h3 style="color:#fff">身份录入</h3><p>AI自动校验身份证号</p></div>
<div class="process-item"><div class="process-num">3</div><h3 style="color:#fff">AI人脸识别</h3><p>摄像头活体检测</p></div>
<div class="process-item"><div class="process-num">4</div><h3 style="color:#fff">结果返回</h3><p>Token回调财务系统</p></div>
</div></div></section>

<section class="cta"><div class="container">
<h2>开始使用森码云实人认证</h2><p>零依赖部署，上传即用。支持6种AI识别接口。</p>
<div class="hero-actions">
<a href="/user/register" class="el-button el-button--large" style="background:#fff;color:#4F46E5;border:none">免费注册</a>
<a href="/install" class="el-button el-button--large" style="background:transparent;color:#fff;border:1px solid rgba(255,255,255,.3)">安装向导</a>
</div>
</div></section>

<footer class="footer"><div class="container footer-inner">
<div><strong>森码云实人认证系统</strong> · face.builds.codes</div>
<div class="footer-links"><a href="#">服务协议</a><a href="#">隐私政策</a><a href="/admin/login">管理后台</a></div>
<div>&copy; <?php echo date('Y'); ?> 森码云 v1.0.3</div>
</div></footer>
</body></html>