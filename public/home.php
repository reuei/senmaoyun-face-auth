<?php
/**
 * 森码云实人认证系统 - 首页
 */
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo SITE_NAME; ?></title>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
:root{--p:#4F46E5;--ph:#4338CA;--pl:#EEF2FF;--s:#10B981;--sl:#D1FAE5;--w:#F59E0B;--wl:#FEF3C7;--e:#EF4444;--el:#FEE2E2;--i:#3B82F6;--il:#DBEAFE;--t:#1F2937;--ts:#6B7280;--tm:#9CA3AF;--bg:#F9FAFB;--bw:#FFF;--bd:#E5E7EB;--bl:#F3F4F6;--r:8px;--rl:12px;--rx:16px;--sh:0 1px 3px rgba(0,0,0,.08);--shm:0 4px 6px rgba(0,0,0,.06);--shl:0 10px 25px rgba(0,0,0,.08);--tr:.15s}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans SC",sans-serif;color:var(--t);background:var(--bg);line-height:1.6}
.container{max-width:1200px;margin:0 auto;padding:0 24px}
a{color:var(--p);text-decoration:none}
/* Navbar */
.nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--bd)}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:60px}
.nav-logo{display:flex;align-items:center;gap:8px;font-size:18px;font-weight:700;color:var(--t)}
.nav-links{display:flex;align-items:center;gap:20px}
.nav-links a{font-size:14px;color:var(--ts);transition:color var(--tr)}
.nav-links a:hover{color:var(--t)}
/* Hero */
.hero{padding:120px 0 80px}
.hero-inner{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}
.hero-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;background:var(--pl);color:var(--p);border-radius:99px;font-size:13px;font-weight:500;margin-bottom:18px}
.hero h1{font-size:44px;font-weight:800;line-height:1.15;letter-spacing:-.03em;margin-bottom:18px}
.hero h1 span{color:var(--p)}
.hero p{font-size:16px;color:var(--ts);line-height:1.7;margin-bottom:28px;max-width:460px}
.hero-actions{display:flex;gap:10px;margin-bottom:36px}
.hero-stats{display:flex;gap:36px}
.stat-val{font-size:22px;font-weight:700}
.stat-label{font-size:12px;color:var(--tm);margin-top:2px}
.hero-svg{display:flex;justify-content:center}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;border-radius:var(--r);font-size:14px;font-weight:500;cursor:pointer;border:1px solid transparent;transition:all var(--tr);text-decoration:none}
.btn-p{background:var(--p);color:#fff}.btn-p:hover{background:var(--ph)}
.btn-s{background:var(--bw);color:var(--t);border-color:var(--bd)}.btn-s:hover{background:var(--bl)}
.btn-lg{padding:12px 28px;font-size:16px;border-radius:var(--rl)}
/* Sections */
.sec{padding:80px 0}
.sec-alt{background:var(--bw)}
.sec-header{text-align:center;margin-bottom:48px}
.sec-header h2{font-size:30px;font-weight:700;letter-spacing:-.02em;margin-bottom:10px}
.sec-header p{color:var(--ts);font-size:15px}
/* Features */
.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.f-card{padding:26px;border:1px solid var(--bd);border-radius:var(--rx);transition:all var(--tr)}
.f-card:hover{border-color:var(--p);box-shadow:var(--shm);transform:translateY(-2px)}
.f-icon{width:44px;height:44px;border-radius:var(--r);display:flex;align-items:center;justify-content:center;margin-bottom:14px}
.f-card h3{font-size:15px;font-weight:600;margin-bottom:6px}
.f-card p{font-size:13px;color:var(--ts);line-height:1.5}
/* Process */
.process-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.p-step{text-align:center;padding:30px 18px;border:1px solid var(--bd);border-radius:var(--rx)}
.p-num{width:34px;height:34px;border-radius:50%;background:var(--p);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;margin:0 auto 14px}
.p-icon{color:var(--p);margin-bottom:12px}
.p-step h3{font-size:15px;font-weight:600;margin-bottom:6px}
.p-step p{font-size:12px;color:var(--ts);line-height:1.5}
/* Security */
.security{max-width:800px;margin:0 auto;padding:40px;background:var(--bw);border:1px solid var(--bd);border-radius:var(--rx)}
.security h2{font-size:22px;margin:14px 0}
.security ul{padding-left:18px}
.security li{font-size:14px;color:var(--ts);padding:5px 0}
/* Footer */
.footer{padding:40px 0;border-top:1px solid var(--bd);margin-top:auto}
.footer-inner{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px}
.footer-brand{font-weight:600;font-size:14px}
.footer-domain{font-size:12px;color:var(--tm)}
.footer-links{display:flex;gap:18px}
.footer-links a{font-size:13px;color:var(--ts)}
.footer-copy{font-size:12px;color:var(--tm)}
@media(max-width:768px){
.hero-inner{grid-template-columns:1fr;text-align:center}
.hero h1{font-size:30px}.hero p{max-width:100%}.hero-actions,.hero-stats{justify-content:center}
.hero-svg{display:none}
.features-grid{grid-template-columns:1fr}
.process-steps{grid-template-columns:1fr 1fr}
.nav-links{display:none}
}
</style>
</head>
<body>
<div id="app">
<nav class="nav">
    <div class="container nav-inner">
        <a href="/" class="nav-logo">
            <i data-lucide="shield-check" style="width:24px;height:24px;color:var(--p)"></i>
            森码云
        </a>
        <div class="nav-links">
            <a href="#features">功能特性</a>
            <a href="#process">认证流程</a>
            <a href="#security">安全合规</a>
            <a href="/admin/" class="btn btn-s">管理后台</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container hero-inner">
        <div class="animate__animated animate__fadeInUp">
            <div class="hero-badge">
                <i data-lucide="zap" style="width:14px;height:14px"></i> 企业级服务
            </div>
            <h1>企业级<br><span>实人认证服务</span></h1>
            <p>基于多源人脸识别API与自研活体检测算法，为魔方财务系统提供安全、合规、高效的实人认证解决方案。</p>
            <div class="hero-actions">
                <a href="#features" class="btn btn-p btn-lg">了解更多</a>
                <a href="#process" class="btn btn-s btn-lg">认证流程</a>
            </div>
            <div class="hero-stats">
                <div><div class="stat-val">6+</div><div class="stat-label">人脸识别接口</div></div>
                <div><div class="stat-val">99.9%</div><div class="stat-label">服务可用性</div></div>
                <div><div class="stat-val">GB/T</div><div class="stat-label">国家标准合规</div></div>
            </div>
        </div>
        <div class="hero-svg animate__animated animate__fadeInRight">
            <svg viewBox="0 0 400 320" xmlns="http://www.w3.org/2000/svg">
                <rect x="40" y="50" width="320" height="240" rx="20" fill="var(--bw)" stroke="var(--bd)" stroke-width="2"/>
                <rect x="80" y="95" width="240" height="160" rx="10" fill="var(--pl)"/>
                <circle cx="200" cy="155" r="50" fill="var(--p)" opacity=".12"/>
                <circle cx="200" cy="155" r="32" fill="var(--p)" opacity=".25"/>
                <circle cx="200" cy="155" r="16" fill="var(--p)"/>
                <path d="M188 151 L196 159 L212 143" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                <rect x="160" y="275" width="80" height="6" rx="3" fill="var(--bd)"/>
                <rect x="145" y="288" width="110" height="5" rx="3" fill="var(--bd)"/>
            </svg>
        </div>
    </div>
</section>

<section id="features" class="sec sec-alt">
    <div class="container">
        <div class="sec-header"><h2>核心功能特性</h2><p>六大核心能力，打造安全可靠的实人认证体验</p></div>
        <div class="features-grid">
            <div class="f-card"><div class="f-icon" style="background:#EEF2FF"><i data-lucide="scan-face" style="width:22px;height:22px;color:#4F46E5"></i></div><h3>多源活体检测</h3><p>支持腾讯云、百度、支付宝等6种人脸识别接口，主备自动切换</p></div>
            <div class="f-card"><div class="f-icon" style="background:#D1FAE5"><i data-lucide="fingerprint" style="width:22px;height:22px;color:#10B981"></i></div><h3>自研检测算法</h3><p>内置动作序列分析、光流变化检测、摩尔纹检测、翻拍检测</p></div>
            <div class="f-card"><div class="f-icon" style="background:#FEF3C7"><i data-lucide="database" style="width:22px;height:22px;color:#F59E0B"></i></div><h3>魔方财务对接</h3><p>提供完整certification类型插件，Token安全机制，一键集成</p></div>
            <div class="f-card"><div class="f-icon" style="background:#DBEAFE"><i data-lucide="globe" style="width:22px;height:22px;color:#3B82F6"></i></div><h3>虚拟主机适配</h3><p>支持Apache/Nginx，兼容各类共享主机环境，零依赖部署</p></div>
            <div class="f-card"><div class="f-icon" style="background:#FCE7F3"><i data-lucide="users" style="width:22px;height:22px;color:#EC4899"></i></div><h3>人工审核队列</h3><p>自动认证失败转入人工审核，审核员可查看数据手动处理</p></div>
            <div class="f-card"><div class="f-icon" style="background:#F3E8FF"><i data-lucide="file-check" style="width:22px;height:22px;color:#9333EA"></i></div><h3>身份证校验</h3><p>内置GB/T 2260行政区划码表，ISO 7064:1983 MOD 11-2校验算法</p></div>
        </div>
    </div>
</section>

<section id="process" class="sec">
    <div class="container">
        <div class="sec-header"><h2>认证流程</h2><p>四步完成实人认证，安全高效</p></div>
        <div class="process-steps">
            <div class="p-step"><div class="p-num">1</div><div class="p-icon"><i data-lucide="file-check" style="width:28px;height:28px"></i></div><h3>同意协议</h3><p>阅读并签署《实人认证服务协议》《隐私政策》</p></div>
            <div class="p-step"><div class="p-num">2</div><div class="p-icon"><i data-lucide="users" style="width:28px;height:28px"></i></div><h3>身份录入</h3><p>输入姓名和身份证号，系统自动校验合法性</p></div>
            <div class="p-step"><div class="p-num">3</div><div class="p-icon"><i data-lucide="scan-face" style="width:28px;height:28px"></i></div><h3>人脸识别</h3><p>开启摄像头，依次完成活体检测动作</p></div>
            <div class="p-step"><div class="p-num">4</div><div class="p-icon"><i data-lucide="shield" style="width:28px;height:28px"></i></div><h3>结果返回</h3><p>检测结果通过Token安全回调至魔方财务系统</p></div>
        </div>
    </div>
</section>

<section id="security" class="sec sec-alt">
    <div class="container">
        <div class="security">
            <i data-lucide="shield" style="width:36px;height:36px;color:var(--p)"></i>
            <h2>安全合规保障</h2>
            <ul>
                <li>符合《个人信息保护法》要求，明示告知、最小必要、撤回同意</li>
                <li>人脸数据加密存储（AES-256-GCM），默认24小时后自动清理</li>
                <li>全站HTTPS加密传输，CSRF防护，SQL预处理防注入</li>
                <li>完整的审计日志记录，所有操作可追溯</li>
                <li>速率限制，防止恶意攻击</li>
            </ul>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container footer-inner">
        <div><span class="footer-brand">森码云实人认证系统</span><br><span class="footer-domain">face.builds.codes</span></div>
        <div class="footer-links">
            <a href="#">服务协议</a><a href="#">隐私政策</a><a href="/admin/">管理后台</a>
        </div>
        <div class="footer-copy">&copy; <?php echo date('Y'); ?> 森码云. All rights reserved.</div>
    </div>
</footer>
</div>
<script>lucide.createIcons();</script>
</body>
</html>