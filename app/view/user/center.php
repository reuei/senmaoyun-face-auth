<?php
$isMobile = false;
$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
foreach (['mobile','android','iphone','ipad','ipod'] as $m) { if (strpos($ua, $m) !== false) { $isMobile = true; break; } }
$userName = $user->nickname ?: $user->username;
$userEmail = $user->email ?: '未设置';
$userPhone = $user->phone ?: '未设置';
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>用户中心 - 森码云</title>
<?php if($isMobile): ?>
<link rel="stylesheet" href="https://unpkg.com/vant@4/lib/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/vant@4/lib/vant.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;background:#F8FAFC;color:#1F2937;min-height:100vh;padding-bottom:60px}
.m-header{background:#4F46E5;color:#fff;padding:40px 20px 30px;text-align:center}.m-header .avatar{width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:28px}.m-header h2{font-size:18px;margin-bottom:4px}.m-header p{font-size:12px;opacity:.8}.m-content{padding:16px}.m-tabs{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #E2E8F0;display:flex;z-index:100}.m-tabs a{flex:1;display:flex;flex-direction:column;align-items:center;padding:8px 0;color:#9CA3AF;text-decoration:none;font-size:10px;gap:2px}.m-tabs a.active{color:#4F46E5}.m-tabs a .icon{font-size:20px}
</style>
<?php else: ?>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;background:#F8FAFC;color:#1F2937;display:flex;min-height:100vh;line-height:1.5}
.d-sidebar{width:200px;background:#fff;border-right:1px solid #E2E8F0;position:fixed;top:0;left:0;bottom:0;z-index:50;padding:20px 0}.d-sidebar h2{padding:0 20px 16px;border-bottom:1px solid #E2E8F0;font-size:16px;color:#4F46E5}.d-sidebar a{display:flex;align-items:center;gap:8px;padding:10px 20px;color:#6B7280;text-decoration:none;font-size:13px;transition:all .15s}.d-sidebar a:hover,.d-sidebar a.active{color:#4F46E5;background:#EEF2FF}.d-main{margin-left:200px;flex:1}.d-topbar{padding:14px 28px;background:#fff;border-bottom:1px solid #E2E8F0;display:flex;justify-content:space-between;align-items:center}.d-topbar h3{font-size:16px}.d-content{padding:28px;max-width:800px}
.card{background:#fff;border-radius:14px;padding:20px;border:1px solid #E2E8F0;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.04)}.card h3{font-size:15px;margin-bottom:12px}
</style>
<?php endif; ?>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script></head><body>
<?php if($isMobile): ?>
<div id="app"><div class="m-header"><div class="avatar">👤</div><h2><?php echo htmlspecialchars($userName); ?></h2><p>欢迎使用森码云实人认证</p></div><div class="m-content"><van-cell-group inset><van-cell title="用户名" value="<?php echo htmlspecialchars($user->username); ?>"/><van-cell title="邮箱" value="<?php echo htmlspecialchars($userEmail); ?>"/><van-cell title="手机" value="<?php echo htmlspecialchars($userPhone); ?>"/></van-cell-group>
<div style="margin:16px"><van-cell-group inset><van-cell title="实名认证状态"><template #value><span :style="{display:'inline-flex',alignItems:'center',gap:'6px',padding:'6px 14px',borderRadius:'99px',fontSize:'13px',fontWeight:'500',background:certifyStatus?'#D1FAE5':'#FEE2E2',color:certifyStatus?'#10B981':'#EF4444'}">{{certifyStatus?'已认证':'未认证'}}</span></template></van-cell></van-cell-group></div>
<div style="margin:16px"><van-button v-if="!certifyStatus" round block type="primary" @click="goVerify">去实名认证</van-button><van-button v-else round block type="success">已认证</van-button></div>
<div style="margin:16px"><van-button round block plain @click="logout">退出登录</van-button></div></div>
<div class="m-tabs"><a href="/" class="active"><span class="icon">🏠</span>首页</a><a href="/user/center"><span class="icon">👤</span>我的</a></div></div>
<?php else: ?>
<div id="app"><div class="d-sidebar"><h2>用户中心</h2><a href="/user/center" class="active">🏠 概览</a><a href="#" @click="goVerify">👁 实名认证</a><a href="/user/logout">➡ 退出登录</a></div><div class="d-main"><div class="d-topbar"><h3>用户中心</h3><span style="font-size:13px;color:#6B7280"><?php echo htmlspecialchars($userName); ?></span></div><div class="d-content">
<el-card class="card"><h3>个人信息</h3><el-descriptions :column="2" border><el-descriptions-item label="用户名"><?php echo htmlspecialchars($user->username); ?></el-descriptions-item><el-descriptions-item label="昵称"><?php echo htmlspecialchars($userName); ?></el-descriptions-item><el-descriptions-item label="邮箱"><?php echo htmlspecialchars($userEmail); ?></el-descriptions-item><el-descriptions-item label="手机"><?php echo htmlspecialchars($userPhone); ?></el-descriptions-item></el-descriptions></el-card>
<el-card class="card" style="margin-top:16px"><h3>实名认证</h3><div style="display:flex;align-items:center;gap:16px"><span :style="{display:'inline-flex',alignItems:'center',gap:'6px',padding:'6px 14px',borderRadius:'99px',fontSize:'13px',fontWeight:'500',background:certifyStatus?'#D1FAE5':'#FEE2E2',color:certifyStatus?'#10B981':'#EF4444'}">{{certifyStatus?'已认证':'未认证'}}</span><el-button v-if="!certifyStatus" type="primary" @click="goVerify">去认证</el-button></div></el-card></div></div></div>
<?php endif; ?></div>
<script>
const{createApp,ref,onMounted}=Vue;const app=createApp({setup(){const certifyStatus=ref(false);onMounted(async()=>{try{const r=await axios.get('/user/status');if(r.data.code===200)certifyStatus.value=r.data.data.certify_status}catch(e){}});function goVerify(){window.location.href='/verify'}function logout(){window.location.href='/user/logout'}return{certifyStatus,goVerify,logout}}});
<?php if($isMobile): ?>app.use(vant);<?php else: ?>app.use(ElementPlus);<?php endif; ?>app.mount('#app');
</script></body></html>