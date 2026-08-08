<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) { header('Location: /user/login'); exit; }
$user = db()->fetch("SELECT * FROM " . db()->table('user') . " WHERE id=?", [$userId]);
$isMobile = false; $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
foreach (['mobile','android','iphone','ipad','ipod'] as $m) { if (strpos($ua, $m) !== false) { $isMobile = true; break; } }
$userName = htmlspecialchars($user['nickname'] ?: $user['username']);
$certified = $user['certify_status'] ?? 0;
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>用户中心 - 森码云 v1.0.5</title>
<?php if($isMobile): ?>
<link rel="stylesheet" href="https://unpkg.com/vant@4/lib/index.css">
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/vant@4/lib/vant.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;background:#F5F6FA;color:#1F2937;min-height:100vh;padding-bottom:60px}
.header{background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff;padding:20px;position:relative}
.header-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.hamburger{background:none;border:none;color:#fff;font-size:24px;cursor:pointer;padding:4px}
.header-user{display:flex;align-items:center;gap:12px}
.avatar{width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:22px}
.header-user h2{font-size:17px;font-weight:600}.header-user p{font-size:12px;opacity:.8}
.tag{display:inline-block;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:500;margin-top:4px}
.tag-ok{background:rgba(16,185,129,.2);color:#6EE7B7}.tag-no{background:rgba(239,68,68,.2);color:#FCA5A5}
.content{padding:16px;display:flex;flex-direction:column;gap:14px}
.card{background:#fff;border-radius:14px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.card h3{font-size:14px;font-weight:600;margin-bottom:12px;display:flex;align-items:center;gap:8px}
.card h3 .icon{font-size:18px}
.stats-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.stat-item{text-align:center;padding:12px;background:#F8FAFC;border-radius:10px}
.stat-item .v{font-size:22px;font-weight:700;color:#4F46E5}.stat-item .l{font-size:11px;color:#6B7280;margin-top:2px}
.info-row{display:flex;justify-content:space-between;padding:8px 0;font-size:13px;border-bottom:1px solid #F1F5F9}
.info-row:last-child{border-bottom:none}.info-row .k{color:#6B7280}.info-row .v{font-weight:500}
.action-btn{display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;background:#4F46E5;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;width:100%}.action-btn:hover{background:#4338CA}
.action-btn.outline{background:transparent;color:#4F46E5;border:1px solid #4F46E5}
/* Drawer */
.drawer-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;display:none}.drawer-overlay.show{display:block}
.drawer{position:fixed;top:0;left:0;bottom:0;width:260px;background:#fff;z-index:201;transform:translateX(-100%);transition:transform .3s}.drawer.show{transform:translateX(0)}
.drawer-header{padding:24px 20px;background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff;font-size:16px;font-weight:600}
.drawer-nav{padding:12px 0}.drawer-nav a{display:flex;align-items:center;gap:12px;padding:12px 20px;color:#374151;text-decoration:none;font-size:14px;transition:background .15s}.drawer-nav a:hover{background:#F1F5F9}.drawer-nav a.active{color:#4F46E5;font-weight:500}
.bottom-nav{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #E2E8F0;display:flex;z-index:100}.bottom-nav a{flex:1;display:flex;flex-direction:column;align-items:center;padding:8px 0;color:#9CA3AF;text-decoration:none;font-size:10px;gap:2px}.bottom-nav a.active{color:#4F46E5}.bottom-nav a .icon{font-size:20px}
</style>
<?php else: ?>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css">
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/element-plus"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;background:#F8FAFC;color:#1F2937;display:flex;min-height:100vh;line-height:1.5}
.sidebar{width:200px;background:#fff;border-right:1px solid #E2E8F0;position:fixed;top:0;left:0;bottom:0;z-index:50;padding:20px 0}
.sidebar h2{padding:0 20px 16px;border-bottom:1px solid #E2E8F0;font-size:16px;color:#4F46E5}
.sidebar a{display:flex;align-items:center;gap:8px;padding:10px 20px;color:#6B7280;text-decoration:none;font-size:13px;transition:all .15s}
.sidebar a:hover,.sidebar a.active{color:#4F46E5;background:#EEF2FF}
.main{margin-left:200px;flex:1}.topbar{padding:14px 28px;background:#fff;border-bottom:1px solid #E2E8F0;display:flex;justify-content:space-between;align-items:center}.topbar h3{font-size:16px}.content{padding:28px;max-width:900px}
.card{background:#fff;border-radius:14px;padding:24px;border:1px solid #E2E8F0;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.04)}.card h3{font-size:15px;margin-bottom:12px;display:flex;align-items:center;gap:8px}
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px}
.stat-item{text-align:center;padding:16px;background:#F8FAFC;border-radius:10px}.stat-item .v{font-size:24px;font-weight:700;color:#4F46E5}.stat-item .l{font-size:12px;color:#6B7280;margin-top:4px}
</style>
<?php endif; ?>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script></head><body>
<?php if($isMobile): ?>
<div id="app">
<div class="drawer-overlay" :class="{show:drawerOpen}" @click="drawerOpen=false"></div>
<div class="drawer" :class="{show:drawerOpen}">
<div class="drawer-header">森码云</div>
<nav class="drawer-nav">
<a href="/user/center" class="active">🏠 工作台</a>
<a href="/verify" @click.prevent="goVerify">👁 实名认证</a>
<a href="/agreement">📄 服务协议</a>
<a href="/privacy">🔒 隐私政策</a>
<a href="/user/logout">🚪 退出登录</a>
</nav></div>
<div class="header"><div class="header-top">
<button class="hamburger" @click="drawerOpen=true">☰</button>
<div style="display:flex;gap:12px;font-size:18px">
<a href="/" style="color:#fff;text-decoration:none">🏠</a>
</div></div>
<div class="header-user"><div class="avatar">👤</div><div><h2><?php echo $userName; ?></h2><p>森码云实人认证</p><span class="tag <?php echo $certified?'tag-ok':'tag-no'; ?>"><?php echo $certified?'已认证':'未认证'; ?></span></div></div></div>
<div class="content">
<div class="card"><h3><span class="icon">📊</span>数据概览</h3><div class="stats-row"><div class="stat-item"><div class="v">0</div><div class="l">认证次数</div></div><div class="stat-item"><div class="v">0</div><div class="l">通过次数</div></div></div></div>
<div class="card"><h3><span class="icon">👤</span>个人信息</h3><div class="info-row"><span class="k">用户名</span><span class="v"><?php echo htmlspecialchars($user['username']); ?></span></div><div class="info-row"><span class="k">邮箱</span><span class="v"><?php echo htmlspecialchars($user['email'] ?: '未设置'); ?></span></div><div class="info-row"><span class="k">注册时间</span><span class="v"><?php echo htmlspecialchars($user['create_time'] ?? '-'); ?></span></div></div>
<button v-if="!certified" class="action-btn" @click="goVerify">👁 去实名认证</button>
<button v-else class="action-btn outline" disabled>✅ 已认证</button>
</div>
<div class="bottom-nav"><a href="/" class="active"><span class="icon">🏠</span>首页</a><a href="/user/center"><span class="icon">👤</span>我的</a></div></div>
<?php else: ?>
<div id="app"><div class="sidebar"><h2>用户中心</h2>
<a href="/user/center" class="active">🏠 工作台</a>
<a href="#" @click="goVerify">👁 实名认证</a>
<a href="/agreement">📄 服务协议</a>
<a href="/privacy">🔒 隐私政策</a>
<a href="/user/logout">🚪 退出</a></div>
<div class="main"><div class="topbar"><h3>工作台</h3><span style="font-size:13px;color:#6B7280">👤 <?php echo $userName; ?></span></div><div class="content">
<el-card class="card"><h3>📊 数据概览</h3><div class="stats-row"><div class="stat-item"><div class="v">0</div><div class="l">认证次数</div></div><div class="stat-item"><div class="v">0</div><div class="l">通过次数</div></div><div class="stat-item"><div class="v"><?php echo $certified?'✅':'❌'; ?></div><div class="l">认证状态</div></div></div></el-card>
<el-card class="card"><h3>👤 个人信息</h3><el-descriptions :column="2" border><el-descriptions-item label="用户名"><?php echo htmlspecialchars($user['username']); ?></el-descriptions-item><el-descriptions-item label="邮箱"><?php echo htmlspecialchars($user['email'] ?: '未设置'); ?></el-descriptions-item><el-descriptions-item label="认证状态"><el-tag :type="certified?'success':'danger'">{{certified?'已认证':'未认证'}}</el-tag></el-descriptions-item><el-descriptions-item label="注册时间"><?php echo htmlspecialchars($user['create_time'] ?? '-'); ?></el-descriptions-item></el-descriptions></el-card>
<el-button v-if="!certified" type="primary" @click="goVerify" style="width:100%">👁 去实名认证</el-button></div></div></div>
<?php endif; ?>
</div>
<script>
const{createApp,ref}=Vue;const app=createApp({setup(){
    const drawerOpen=ref(false);const certified=ref(<?php echo $certified; ?>);
    function goVerify(){window.location.href='/verify'}
    return{drawerOpen,certified,goVerify};
}});
<?php if($isMobile): ?>app.mount('#app');<?php else: ?>app.use(ElementPlus).mount('#app');<?php endif; ?>
</script></body></html>