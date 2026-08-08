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
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>用户中心 - 森码云 v1.0.4</title>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css">
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/element-plus"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;background:#F8FAFC;color:#1F2937;min-height:100vh;line-height:1.5}
/* Desktop sidebar */
.d-layout{display:flex;min-height:100vh}
.d-sidebar{width:200px;background:#fff;border-right:1px solid #E2E8F0;position:fixed;top:0;left:0;bottom:0;z-index:50;padding:20px 0}
.d-sidebar h2{padding:0 20px 16px;border-bottom:1px solid #E2E8F0;font-size:16px;color:#4F46E5}
.d-sidebar a{display:flex;align-items:center;gap:8px;padding:10px 20px;color:#6B7280;text-decoration:none;font-size:13px;transition:all .15s}
.d-sidebar a:hover,.d-sidebar a.active{color:#4F46E5;background:#EEF2FF}
.d-main{margin-left:200px;flex:1}.d-topbar{padding:14px 28px;background:#fff;border-bottom:1px solid #E2E8F0;display:flex;justify-content:space-between;align-items:center}.d-topbar h3{font-size:16px}.d-content{padding:28px;max-width:800px}
/* Mobile */
.m-page{min-height:100vh;padding-bottom:60px}.m-header{background:linear-gradient(135deg,#4F46E5,#7C3AED);color:#fff;padding:36px 20px 28px;text-align:center}.m-header .avatar{width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:28px}.m-header h2{font-size:18px;margin-bottom:4px}.m-header p{font-size:12px;opacity:.8}.m-content{padding:16px}.m-tabs{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #E2E8F0;display:flex;z-index:100}.m-tabs a{flex:1;display:flex;flex-direction:column;align-items:center;padding:8px 0;color:#9CA3AF;text-decoration:none;font-size:10px;gap:2px}.m-tabs a.active{color:#4F46E5}.m-tabs a .icon{font-size:20px}
/* Shared */
.card{background:#fff;border-radius:14px;padding:20px;border:1px solid #E2E8F0;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.04)}.card h3{font-size:15px;margin-bottom:12px}
</style></head><body>
<?php if($isMobile): ?>
<div id="app"><div class="m-page"><div class="m-header"><div class="avatar">👤</div><h2><?php echo $userName; ?></h2><p>森码云实人认证</p></div><div class="m-content">
<el-card class="card"><h3>个人信息</h3><el-descriptions :column="1" border size="small"><el-descriptions-item label="用户名"><?php echo htmlspecialchars($user['username']); ?></el-descriptions-item><el-descriptions-item label="邮箱"><?php echo htmlspecialchars($user['email'] ?: '未设置'); ?></el-descriptions-item></el-descriptions></el-card>
<el-card class="card"><h3>实名认证</h3><div style="display:flex;align-items:center;gap:12px"><el-tag :type="certifyStatus?'success':'danger'">{{certifyStatus?'已认证':'未认证'}}</el-tag><el-button v-if="!certifyStatus" type="primary" @click="goVerify">去认证</el-button></div></el-card>
<el-button style="width:100%;margin-top:16px" @click="logout">退出登录</el-button></div>
<div class="m-tabs"><a href="/" class="active"><span class="icon">🏠</span>首页</a><a href="/user/center"><span class="icon">👤</span>我的</a></div></div></div>
<?php else: ?>
<div id="app"><div class="d-layout"><div class="d-sidebar"><h2>用户中心</h2><a href="/user/center" class="active">🏠 概览</a><a href="#" @click="goVerify">👁 实名认证</a><a href="/user/logout">➡ 退出</a></div><div class="d-main"><div class="d-topbar"><h3>用户中心</h3><span style="font-size:13px;color:#6B7280"><?php echo $userName; ?></span></div><div class="d-content">
<el-card class="card"><h3>个人信息</h3><el-descriptions :column="2" border><el-descriptions-item label="用户名"><?php echo htmlspecialchars($user['username']); ?></el-descriptions-item><el-descriptions-item label="邮箱"><?php echo htmlspecialchars($user['email'] ?: '未设置'); ?></el-descriptions-item></el-descriptions></el-card>
<el-card class="card"><h3>实名认证</h3><div style="display:flex;align-items:center;gap:12px"><el-tag :type="certifyStatus?'success':'danger'">{{certifyStatus?'已认证':'未认证'}}</el-tag><el-button v-if="!certifyStatus" type="primary" @click="goVerify">去认证</el-button></div></el-card></div></div></div></div>
<?php endif; ?>
</div>
<script>
const{createApp,ref,onMounted}=Vue;createApp({setup(){
    const certifyStatus=ref(<?php echo $user['certify_status'] ?? 0; ?>);
    function goVerify(){window.location.href='/verify'}
    function logout(){window.location.href='/user/logout'}
    return{certifyStatus,goVerify,logout};
}}).use(ElementPlus).mount('#app');
</script></body></html>