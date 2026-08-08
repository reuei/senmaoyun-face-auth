<?php
/**
 * 禁止访问页 - 非魔方财务入口
 */
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>访问受限 - <?php echo SITE_NAME; ?></title>
<style>
:root{--p:#4F46E5;--e:#EF4444;--t:#1F2937;--ts:#6B7280;--tm:#9CA3AF;--bg:#F9FAFB;--bw:#FFF;--bd:#E5E7EB;--r:8px;--rx:16px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans SC",sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:var(--bg);color:var(--t)}
.card{background:var(--bw);border-radius:var(--rx);box-shadow:0 10px 25px rgba(0,0,0,.08);border:1px solid var(--bd);max-width:460px;width:100%;padding:44px 36px;text-align:center}
.card svg{margin-bottom:20px}
.card h1{font-size:22px;font-weight:700;margin-bottom:10px}
.card p{color:var(--ts);font-size:14px;line-height:1.6;margin-bottom:24px}
.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;background:var(--p);color:#fff;border-radius:var(--r);font-size:14px;font-weight:500;text-decoration:none}
.btn:hover{background:#4338CA}
.footer{font-size:12px;color:var(--tm);margin-top:20px}
</style>
</head>
<body>
<div class="card">
<svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
<h1>访问受限</h1>
<p><?php
$reason = $_GET['reason'] ?? '';
$msgs = [
    'invalid_token' => '认证Token无效或已过期，请从魔方财务系统重新发起认证。',
    'expired' => '认证链接已过期，请重新发起认证。',
    'no_permission' => '人脸识别仅允许从魔方财务系统入口进入。',
];
echo h($msgs[$reason] ?? '人脸识别仅允许从魔方财务系统入口进入，请从魔方财务系统发起认证。');
?></p>
<a href="/" class="btn">返回首页</a>
<p class="footer">森码云实人认证系统 &mdash; face.builds.codes</p>
</div>
</body>
</html>