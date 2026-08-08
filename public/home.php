<?php
/**
 * 森码云实人认证系统 v1.0.4 - 首页
 * 轮播Banner + 矢量图标 + 无后台入口
 */
$siteName = defined('SITE_NAME') ? SITE_NAME : '森码云实人认证系统';
?><!DOCTYPE html>
<html lang="zh-CN"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo $siteName; ?></title>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css">
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/element-plus"></script>
<style>
:root{--el-color-primary:#4F46E5;--c-bg:#FAFBFC;--c-surface:#FFFFFF;--c-border:#E6E8EC;--c-text:#1A1D23;--c-text2:#5A5F6B;--c-text3:#8F95A3}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;background:var(--c-bg);color:var(--c-text);line-height:1.6;-webkit-font-smoothing:antialiased}
.container{max-width:1200px;margin:0 auto;padding:0 24px}
.nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,.92);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-bottom:1px solid var(--c-border)}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:60px}
.nav-logo{display:flex;align-items:center;gap:8px;font-size:18px;font-weight:700;color:var(--c-text);text-decoration:none;letter-spacing:-.02em}
.nav-logo svg{width:28px;height:28px}
.nav-links{display:flex;align-items:center;gap:20px}
.nav-links a{font-size:14px;color:var(--c-text2);text-decoration:none;transition:color .15s}
.nav-links a:hover{color:var(--c-text)}
/* Banner */
.banner{width:100%;height:420px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;background:linear-gradient(135deg,#1e1b4b,#312e81,#4F46E5)}
.banner-content{text-align:center;color:#fff;max-width:700px;padding:0 20px;z-index:1}
.banner-content h2{font-size:clamp(28px,4vw,44px);font-weight:800;margin-bottom:16px;letter-spacing:-.02em}
.banner-content p{font-size:16px;opacity:.9;margin-bottom:28px;line-height:1.7}
.banner-illustration{position:absolute;right:5%;top:50%;transform:translateY(-50%);opacity:.15;pointer-events:none}
.banner-illustration svg{width:300px;height:300px}
/* Sections */
.sec{padding:80px 0}.sec-header{text-align:center;margin-bottom:60px}
.sec-header h2{font-size:32px;font-weight:700;letter-spacing:-.02em;margin-bottom:12px}
.sec-header p{font-size:16px;color:var(--c-text2);max-width:500px;margin:0 auto}
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.f-card{background:var(--c-surface);border:1px solid var(--c-border);border-radius:14px;padding:28px;transition:all .2s}
.f-card:hover{border-color:#4F46E5;box-shadow:0 8px 24px rgba(79,70,229,.1);transform:translateY(-2px)}
.f-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;font-size:0}
.f-icon svg{width:24px;height:24px}
.f-card h3{font-size:16px;font-weight:600;margin-bottom:8px}
.f-card p{font-size:14px;color:var(--c-text2);line-height:1.6}
.process-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0}
.process-item{text-align:center;padding:32px 20px;position:relative}
.process-item::after{content:'';position:absolute;top:44px;right:-12px;width:24px;height:2px;background:rgba(255,255,255,.2)}
.process-item:last-child::after{display:none}
.process-num{width:40px;height:40px;border-radius:50%;background:#4F46E5;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;margin:0 auto 16px;font-size:15px}
.process-item h3{font-size:15px;font-weight:600;margin-bottom:6px;color:#fff}
.process-item p{font-size:13px;color:rgba(255,255,255,.7);line-height:1.5}
.sec-dark{background:var(--c-text);color:#fff}
.cta{text-align:center;padding:80px 0;background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff}
.cta h2{font-size:32px;font-weight:700;margin-bottom:16px;letter-spacing:-.02em}
.cta p{font-size:16px;opacity:.9;margin-bottom:32px}
.footer{padding:40px 0;border-top:1px solid var(--c-border);background:var(--c-surface)}
.footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;font-size:13px;color:var(--c-text3)}
.footer-links{display:flex;gap:20px}.footer-links a{color:var(--c-text3);text-decoration:none}.footer-links a:hover{color:var(--c-text)}
@media(max-width:768px){.banner{height:340px}.features-grid{grid-template-columns:1fr}.process-grid{grid-template-columns:1fr 1fr}.process-item::after{display:none}.nav-links{display:none}}
</style></head><body>
<nav class="nav"><div class="container nav-inner">
<a href="/" class="nav-logo"><svg viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="#4F46E5"/><path d="M16 6l-8 4v8l8 8 8-8v-8l-8-4z" fill="none" stroke="#fff" stroke-width="2"/><path d="M13 16l2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>森码云</a>
<div class="nav-links"><a href="#features">核心能力</a><a href="#process">认证流程</a><a href="/user/login">登录</a><a href="/user/register" class="el-button el-button--primary el-button--small">免费注册</a></div>
</div></nav>

<section class="banner">
<div class="banner-content"><h2>企业级AI实人认证</h2><p>6种识别接口 · 自研算法 · 零依赖部署 · GB/T国标合规</p>
<div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap"><a href="/user/register" class="el-button el-button--large" style="background:#fff;color:#4F46E5;border:none">免费注册</a><a href="/user/login" class="el-button el-button--large" style="background:transparent;color:#fff;border:1px solid rgba(255,255,255,.3)">用户登录</a></div></div>
<div class="banner-illustration"><svg viewBox="0 0 300 300" fill="none"><circle cx="150" cy="120" r="80" fill="none" stroke="rgba(255,255,255,.3)" stroke-width="2"/><circle cx="150" cy="120" r="50" fill="none" stroke="rgba(255,255,255,.2)" stroke-width="2"/><circle cx="150" cy="120" r="20" fill="rgba(255,255,255,.3)"/><path d="M140 115 L148 123 L160 108" stroke="rgba(255,255,255,.6)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
</section>

<section id="features" class="sec"><div class="container">
<div class="sec-header"><h2>核心能力</h2><p>六大AI驱动功能，安全可靠的实人认证体验</p></div>
<div class="features-grid">
<div class="f-card"><div class="f-icon" style="background:#EEF2FF"><svg viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2"/></svg></div><h3>AI活体检测</h3><p>自研AI算法：动作序列分析、光流检测、摩尔纹检测、翻拍检测</p></div>
<div class="f-card"><div class="f-icon" style="background:#D1FAE5"><svg viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M4 7V4h16v3M9 21h6M12 17v4M22 7H2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7z"/></svg></div><h3>多源接口</h3><p>腾讯云慧眼、百度AI、支付宝、聚合数据、阿里云市场+自研</p></div>
<div class="f-card"><div class="f-icon" style="background:#FEF3C7"><svg viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><h3>魔方财务插件</h3><p>完整certification类型插件，Token安全机制，一键集成</p></div>
<div class="f-card"><div class="f-icon" style="background:#DBEAFE"><svg viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/></svg></div><h3>零依赖部署</h3><p>无需composer/npm，上传即用。Apache/Nginx虚拟主机全兼容</p></div>
<div class="f-card"><div class="f-icon" style="background:#FCE7F3"><svg viewBox="0 0 24 24" fill="none" stroke="#EC4899" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><h3>人工审核</h3><p>AI认证失败自动转入人工审核，审核员可手动通过或驳回</p></div>
<div class="f-card"><div class="f-icon" style="background:#F3E8FF"><svg viewBox="0 0 24 24" fill="none" stroke="#9333EA" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><h3>身份证校验</h3><p>GB/T 2260标准 + ISO 7064:1983 MOD 11-2校验算法</p></div>
</div></div></section>

<section id="process" class="sec sec-dark"><div class="container">
<div class="sec-header"><h2 style="color:#fff">认证流程</h2><p>四步完成实人认证，全程AI加持</p></div>
<div class="process-grid">
<div class="process-item"><div class="process-num">1</div><h3>同意协议</h3><p>阅读签署服务协议</p></div>
<div class="process-item"><div class="process-num">2</div><h3>身份录入</h3><p>AI自动校验身份证</p></div>
<div class="process-item"><div class="process-num">3</div><h3>AI人脸识别</h3><p>摄像头活体检测</p></div>
<div class="process-item"><div class="process-num">4</div><h3>结果返回</h3><p>Token回调财务系统</p></div>
</div></div></section>

<section class="cta"><div class="container"><h2>开始使用森码云</h2><p>零依赖部署，上传即用。支持6种AI识别接口</p>
<div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap"><a href="/user/register" class="el-button el-button--large" style="background:#fff;color:#4F46E5;border:none">免费注册</a><a href="/user/login" class="el-button el-button--large" style="background:transparent;color:#fff;border:1px solid rgba(255,255,255,.3)">用户登录</a></div>
</div></section>

<footer class="footer"><div class="container footer-inner">
<div><strong>森码云实人认证系统</strong> · face.builds.codes</div>
<div class="footer-links"><a href="#">服务协议</a><a href="#">隐私政策</a></div>
<div>&copy; <?php echo date('Y'); ?> 森码云 v1.0.4</div>
</div></footer>
</body></html>