<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/encrypt.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? ''); $password = trim($_POST['password'] ?? '');
    if (empty($username) || empty($password)) { json_error('请输入用户名和密码'); }
    $user = db()->fetch("SELECT * FROM " . db()->table('user') . " WHERE username=? AND status=1", [$username]);
    if (!$user || !password_verify($password, $user['password'])) { json_error('用户名或密码错误'); }
    db()->update(db()->table('user'), ['last_login_ip' => get_client_ip(), 'last_login_time' => date('Y-m-d H:i:s')], 'id=?', [$user['id']]);
    session_start(); $_SESSION['user_id'] = $user['id']; $_SESSION['user_username'] = $user['username'];
    json_success(['redirect' => '/user/center'], '登录成功');
}

$isMobile = false; $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
foreach (['mobile','android','iphone','ipad','ipod'] as $m) { if (strpos($ua, $m) !== false) { $isMobile = true; break; } }
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>用户登录 - 森码云</title>
<?php if($isMobile): ?><link rel="stylesheet" href="https://unpkg.com/vant@4/lib/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/vant@4/lib/vant.min.js"></script>
<?php else: ?><link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script>
<?php endif; ?><script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:#F8FAFC;color:#1F2937}
.card{width:100%;max-width:400px;padding:40px;background:#fff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,.08);border:1px solid #E2E8F0;text-align:center}
.card h2{font-size:20px;margin-bottom:4px}.card .sub{color:#6B7280;font-size:13px;margin-bottom:28px}
.link{font-size:12px;color:#9CA3AF;text-decoration:none;display:block;margin-top:16px}</style></head><body>
<div id="app"><div class="card"><h2>用户登录</h2><p class="sub">森码云实人认证系统</p>
<?php if($isMobile): ?><van-form @submit="login"><van-cell-group inset><van-field v-model="form.username" label="用户名" placeholder="请输入用户名" :rules="[{required:true}]"/><van-field v-model="form.password" type="password" label="密码" placeholder="请输入密码" :rules="[{required:true}]"/></van-cell-group><div style="margin:20px 16px"><van-button round block type="primary" native-type="submit" :loading="loading">登录</van-button></div></van-form><div style="text-align:center"><van-button size="small" plain @click="goRegister">没有账号？立即注册</van-button></div>
<?php else: ?><el-form :model="form" :rules="rules" ref="f" @submit.prevent="login"><el-form-item prop="username"><el-input v-model="form.username" placeholder="用户名"/></el-form-item><el-form-item prop="password"><el-input v-model="form.password" type="password" placeholder="密码" show-password/></el-form-item><el-form-item><el-button type="primary" native-type="submit" :loading="loading" style="width:100%">登录</el-button></el-form-item></el-form><div style="text-align:center"><el-link type="primary" @click="goRegister">没有账号？立即注册</el-link></div>
<?php endif; ?><a href="/" class="link">返回首页</a></div></div>
<script>
const{createApp,ref,reactive}=Vue;const app=createApp({setup(){const form=reactive({username:'',password:''});const rules={username:[{required:true}],password:[{required:true}]};const f=ref(null);const loading=ref(false);
async function login(){<?php if(!$isMobile): ?>const v=await f.value.validate().catch(function(){return false});if(!v)return;<?php endif; ?>loading.value=true;try{const r=await axios.post('/user/login',form);if(r.data.code===200)window.location.href='/user/center';else <?php echo $isMobile?'vant.showToast':'ElementPlus.ElMessage.error'; ?>(r.data.msg||'登录失败')}catch(e){<?php echo $isMobile?'vant.showToast':'ElementPlus.ElMessage.error'; ?>('网络错误')}loading.value=false}
function goRegister(){window.location.href='/user/register'}return{form,rules,f,loading,login,goRegister}}});
<?php if($isMobile): ?>app.use(vant);<?php else: ?>app.use(ElementPlus);<?php endif; ?>app.mount('#app');
</script></body></html>