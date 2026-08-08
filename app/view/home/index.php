<?php
$siteName = config('app.site_name');
$siteDomain = config('app.site_domain');
?><!DOCTYPE html>
<html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo $siteName; ?></title>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css">
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/element-plus"></script>
<style>
:root{--el-color-primary:#4F46E5}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;background:#F8FAFC;color:#1F2937;line-height:1.6}
.container{max-width:1200px;margin:0 auto;padding:0 20px}
.nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,.85);backdrop-filter:blur(12px);border-bottom:1px solid #E2E8F0}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:60px}
.nav-logo{display:flex;align-items:center;gap:8px;font-size:18px;font-weight:700;color:#1F2937;text-decoration:none}
.nav-links{display:flex;align-items:center;gap:16px}
.nav-links a{font-size:14px;color:#6B7280;text-decoration:none;transition:color .15s}
.nav-links a:hover{color:#1F2937}
.hero{padding:120px 0 80px}
.hero-inner{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}
.hero h1{font-size:44px;font-weight:800;line-height:1.15;letter-spacing:-.03em;margin-bottom:18px}
.hero h1 span{color:#4F46E5}
.hero p{font-size:16px;color:#6B7280;line-height:1.7;margin-bottom:28px}
.hero-actions{display:flex;gap:10px;margin-bottom:36px}
.hero-stats{display:flex;gap:36px}
.stat-val{font-size:22px;font-weight:700}.stat-label{font-size:12px;color:#9CA3AF}
.sec{padding:80px 0}.sec-alt{background:#fff}
.sec-header{text-align:center;margin-bottom:48px}
.sec-header h2{font-size:30px;font-weight:700;margin-bottom:10px}
.sec-header p{color:#6B7280;font-size:15px}
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.f-card{padding:26px;border:1px solid #E2E8F0;border-radius:16px;transition:all .15s}
.f-card:hover{border-color:#4F46E5;box-shadow:0 4px 12px rgba(0,0,0,.06);transform:translateY(-2px)}
.f-icon{width:44px;height:44px;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;font-size:22px}
.f-card h3{font-size:15px;font-weight:600;margin-bottom:6px}
.f-card p{font-size:13px;color:#6B7280;line-height:1.5}
.process-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.p-step{text-align:center;padding:30px 18px;border:1px solid #E2E8F0;border-radius:16px}
.p-num{width:34px;height:34px;border-radius:50%;background:#4F46E5;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;margin:0 auto 14px;font-size:14px}
.p-step h3{font-size:15px;font-weight:600;margin:10px 0 6px}
.p-step p{font-size:12px;color:#6B7280;line-height:1.5}
.footer{padding:40px 0;border-top:1px solid #E2E8F0;margin-top:auto}
.footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px}
.footer-copy{font-size:12px;color:#9CA3AF}
@media(max-width:768px){.hero-inner{grid-template-columns:1fr;text-align:center}.hero h1{font-size:30px}.features-grid{grid-template-columns:1fr}.process-steps{grid-template-columns:1fr 1fr}.nav-links{display:none}}
</style></head><body>
<nav class="nav"><div class="container nav-inner"><a href="/" class="nav-logo">🛡 森码云</a><div class="nav-links"><a href="#features">功能特性</a><a href="#process">认证流程</a><a href="/user/login">用户登录</a><a href="/user/register" class="el-button el-button--primary el-button--small">免费注册</a><a href="/admin/login">管理后台</a></div></div></nav>
<section class="hero"><div class="container hero-inner"><div><h1>企业级<br><span>实人认证服务</span></h1><p>基于多源人脸识别API与自研活体检测算法，为魔方财务系统提供安全、合规、高效的实人认证解决方案。</p><div class="hero-actions"><a href="/user/register" class="el-button el-button--primary el-button--large">免费注册</a><a href="/user/login" class="el-button el-button--large">用户登录</a></div><div class="hero-stats"><div><div class="stat-val">6+</div><div class="stat-label">人脸识别接口</div></div><div><div class="stat-val">99.9%</div><div class="stat-label">服务可用性</div></div><div><div class="stat-val">GB/T</div><div class="stat-label">国家标准合规</div></div></div></div><div class="hero-svg" style="display:flex;justify-content:center"><svg viewBox="0 0 400 320"><rect x="40" y="50" width="320" height="240" rx="20" fill="#fff" stroke="#E2E8F0" stroke-width="2"/><rect x="80" y="95" width="240" height="160" rx="10" fill="#EEF2FF"/><circle cx="200" cy="155" r="50" fill="#4F46E5" opacity=".12"/><circle cx="200" cy="155" r="32" fill="#4F46E5" opacity=".25"/><circle cx="200" cy="155" r="16" fill="#4F46E5"/><path d="M188 151 L196 159 L212 143" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div></div></section>
<section id="features" class="sec sec-alt"><div class="container"><div class="sec-header"><h2>核心功能特性</h2><p>六大核心能力，打造安全可靠的实人认证体验</p></div><div class="features-grid">
<div class="f-card"><div class="f-icon" style="background:#EEF2FF;color:#4F46E5">👁</div><h3>多源活体检测</h3><p>支持腾讯云、百度、支付宝等6种人脸识别接口，主备自动切换</p></div>
<div class="f-card"><div class="f-icon" style="background:#D1FAE5;color:#10B981">🖐</div><h3>自研检测算法</h3><p>内置动作序列分析、光流变化检测、摩尔纹检测、翻拍检测</p></div>
<div class="f-card"><div class="f-icon" style="background:#FEF3C7;color:#F59E0B">📑</div><h3>魔方财务对接</h3><p>提供完整certification类型插件，Token安全机制，一键集成</p></div>
<div class="f-card"><div class="f-icon" style="background:#DBEAFE;color:#3B82F6">🌐</div><h3>虚拟主机适配</h3><p>支持Apache/Nginx，兼容各类共享主机环境</p></div>
<div class="f-card"><div class="f-icon" style="background:#FCE7F3;color:#EC4899">👥</div><h3>人工审核队列</h3><p>自动认证失败转入人工审核，审核员可查看数据手动处理</p></div>
<div class="f-card"><div class="f-icon" style="background:#F3E8FF;color:#9333EA">📄</div><h3>身份证校验</h3><p>内置GB/T 2260行政区划码表，ISO 7064:1983 MOD 11-2校验算法</p></div>
</div></div></section>
<section id="process" class="sec"><div class="container"><div class="sec-header"><h2>认证流程</h2><p>四步完成实人认证，安全高效</p></div><div class="process-steps">
<div class="p-step"><div class="p-num">1</div><h3>同意协议</h3><p>阅读并签署《实人认证服务协议》</p></div>
<div class="p-step"><div class="p-num">2</div><h3>身份录入</h3><p>输入姓名和身份证号，系统自动校验</p></div>
<div class="p-step"><div class="p-num">3</div><h3>人脸识别</h3><p>开启摄像头，完成活体检测动作</p></div>
<div class="p-step"><div class="p-num">4</div><h3>结果返回</h3><p>检测结果通过Token回调至魔方财务</p></div>
</div></div></section>
<section id="security" class="sec sec-alt"><div class="container" style="max-width:800px;margin:0 auto;padding:40px;background:#fff;border:1px solid #E2E8F0;border-radius:16px"><h2 style="font-size:22px;margin-bottom:14px">🛡 安全合规保障</h2><ul style="padding-left:18px;font-size:14px;color:#6B7280"><li style="padding:5px 0">符合《个人信息保护法》要求</li><li style="padding:5px 0">人脸数据加密存储（AES-256-GCM）</li><li style="padding:5px 0">全站HTTPS加密传输，CSRF防护</li><li style="padding:5px 0">完整审计日志，所有操作可追溯</li></ul></div></section>
<footer class="footer"><div class="container footer-inner"><div><strong>森码云实人认证系统</strong><br><span style="font-size:12px;color:#9CA3AF">face.builds.codes</span></div><div class="footer-copy">&copy; 2026 森码云 v2.0.1</div></div></footer>
</body></html>