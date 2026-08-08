<?php
/**
 * 目录绑定提示页
 */
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>目录绑定提示 - 森码云</title>
<style>
:root{--p:#4F46E5;--pl:#EEF2FF;--w:#F59E0B;--wl:#FEF3C7;--t:#1F2937;--ts:#6B7280;--tm:#9CA3AF;--bg:#F9FAFB;--bw:#FFF;--bd:#E5E7EB;--r:8px;--rx:16px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans SC",sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:var(--bg);color:var(--t);line-height:1.6}
.card{background:var(--bw);border-radius:var(--rx);box-shadow:0 10px 25px rgba(0,0,0,.08);border:1px solid var(--bd);max-width:540px;width:100%;padding:44px 36px;text-align:center}
.icon{width:64px;height:64px;border-radius:50%;background:var(--wl);display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
.icon svg{width:30px;height:30px;color:#D97706}
h1{font-size:20px;font-weight:700;margin-bottom:10px}
p{color:var(--ts);font-size:14px;margin-bottom:6px}
.code{background:#1F2937;color:#E5E7EB;border-radius:var(--r);padding:14px 18px;margin:20px 0;text-align:left;font-family:monospace;font-size:12px;line-height:1.8;overflow-x:auto}
.code .c{color:#6B7280}.code .p{color:#FBBF24}.code .b{color:#60A5FA}
.steps{text-align:left;list-style:none;margin:20px 0}
.steps li{display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid var(--bd);font-size:13px;color:var(--ts)}
.steps li:last-child{border:none}
.num{width:26px;height:26px;border-radius:50%;background:var(--pl);color:var(--p);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0}
.foot{font-size:12px;color:var(--tm);margin-top:20px}
</style>
</head>
<body>
<div class="card">
<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
<h1>请将域名绑定到 public 目录</h1>
<p>森码云实人认证系统的 Web 根目录应指向 <strong>public</strong> 文件夹</p>
<div class="code">
<span class="c"># 宝塔面板设置</span><br>
<span class="b">网站 → 设置 → 网站目录 → 运行目录</span><br>
<span class="p">/www/wwwroot/face.builds.codes/public</span>
</div>
<ol class="steps">
<li><span class="num">1</span>登录服务器管理面板（宝塔/cPanel等）</li>
<li><span class="num">2</span>找到域名 <strong>face.builds.codes</strong> 的网站设置</li>
<li><span class="num">3</span>将「运行目录」设置为 <strong>public</strong> 文件夹</li>
<li><span class="num">4</span>保存后刷新本页面</li>
</ol>
<p class="foot">森码云实人认证系统 &copy; <?php echo date('Y'); ?> &mdash; face.builds.codes</p>
</div>
</body>
</html>