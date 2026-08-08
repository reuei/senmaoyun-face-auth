<?php
/**
 * 用户登录页 v1.0.4
 */
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');
    if (empty($username) || empty($password)) { json_error('请输入用户名和密码'); }
    $user = db()->fetch("SELECT * FROM " . db()->table('user') . " WHERE username=? AND status=1", [$username]);
    if (!$user || !password_verify($password, $user['password'])) { json_error('用户名或密码错误'); }
    db()->update(db()->table('user'), ['last_login_ip' => get_client_ip(), 'last_login_time' => date('Y-m-d H:i:s')], 'id=?', [$user['id']]);
    session_start(); $_SESSION['user_id'] = $user['id']; $_SESSION['user_username'] = $user['username'];
    json_success(['redirect' => '/user/center'], '登录成功');
}

$isMobile = false; $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
foreach (['mobile','android','iphone','ipad','ipod'] as $m) { if (strpos($ua, $m) !== false) { $isMobile = true; break; } }
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>用户登录 - 森码云 v1.0.4</title>
<?php if($isMobile): ?>
<link rel="stylesheet" href="https://unpkg.com/vant@4/lib/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/vant@4/lib/vant.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;min-height:100vh;background:linear-gradient(180deg,#EEF2FF 0%,#F8FAFC 100%);color:#1F2937}
.page{padding:20px;max-width:420px;margin:0 auto}
.logo-area{text-align:center;padding:40px 0 20px}.logo-area svg{width:60px;height:60px}.logo-area h1{font-size:22px;font-weight:700;margin-top:8px}.logo-area p{font-size:13px;color:#6B7280;margin-top:4px}
.card{background:#fff;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(0,0,0,.06)}
</style>
<?php else: ?>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:linear-gradient(135deg,#F8FAFC,#EEF2FF);color:#1F2937}
.card{width:100%;max-width:440px;padding:40px;background:#fff;border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,.08)}.card .logo{text-align:center;margin-bottom:20px}.card .logo svg{width:52px;height:52px}.card h2{text-align:center;font-size:22px;font-weight:700;margin-bottom:4px}.card .sub{text-align:center;color:#6B7280;font-size:13px;margin-bottom:28px}
</style>
<?php endif; ?>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script></head><body>
<div id="app">
<?php if($isMobile): ?>
<div class="page">
<div class="logo-area"><svg viewBox="0 0 60 60" fill="none"><rect width="60" height="60" rx="14" fill="#4F46E5"/><path d="M30 12l-15 7.5v15L30 49.5 45 34.5v-15L30 12z" stroke="#fff" stroke-width="3" fill="none"/><path d="M25 30l4 4 7-8" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg><h1>森码云</h1><p>企业级实人认证</p></div>
<div class="card">
<van-form @submit="onSubmit">
<van-cell-group inset>
<van-field v-model="form.username" name="username" label="用户名" placeholder="请输入用户名" :rules="[{required:true,message:'请输入用户名'}]"/>
<van-field v-model="form.password" name="password" type="password" label="密码" placeholder="请输入密码" :rules="[{required:true,message:'请输入密码'}]"/>
</van-cell-group>
<div style="margin:24px 16px"><van-button round block type="primary" native-type="submit" :loading="loading">登录</van-button></div>
</van-form>
<div style="text-align:center;margin-top:8px"><van-button size="small" plain @click="goRegister">没有账号？立即注册</van-button></div>
</div></div>
<?php else: ?>
<div class="card"><div class="logo"><svg viewBox="0 0 52 52" fill="none"><rect width="52" height="52" rx="12" fill="#4F46E5"/><path d="M26 10l-13 6.5v13L26 42.5 39 29.5v-13L26 10z" stroke="#fff" stroke-width="3" fill="none"/><path d="M22 26l3 3 6-7" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h2>用户登录</h2><p class="sub">森码云实人认证系统</p>
<el-form :model="form" :rules="rules" ref="f" @submit.prevent="onSubmit">
<el-form-item prop="username"><el-input v-model="form.username" placeholder="用户名" size="large"/></el-form-item>
<el-form-item prop="password"><el-input v-model="form.password" type="password" placeholder="密码" show-password size="large"/></el-form-item>
<el-form-item><el-button type="primary" native-type="submit" :loading="loading" size="large" style="width:100%">登录</el-button></el-form-item>
</el-form>
<div style="text-align:center"><el-link type="primary" @click="goRegister">没有账号？立即注册</el-link></div>
</div>
<?php endif; ?>
</div>
<script>
const{createApp,ref,reactive}=Vue;const app=createApp({setup(){
    const form=reactive({username:'',password:''});
    const rules={username:[{required:true,message:'请输入用户名'}],password:[{required:true,message:'请输入密码'}]};
    const f=ref(null);const loading=ref(false);
    async function onSubmit(){
        if(!form.username||!form.password){<?php echo $isMobile?'vant.showToast':'ElementPlus.ElMessage.warning'; ?>('请填写用户名和密码');return}
        loading.value=true;
        try{const r=await axios.post('/user/login',form);if(r.data.code===200)window.location.href=r.data.data.redirect||'/user/center';else <?php echo $isMobile?'vant.showToast':'ElementPlus.ElMessage.error'; ?>(r.data.msg||'登录失败')}catch(e){<?php echo $isMobile?'vant.showToast':'ElementPlus.ElMessage.error'; ?>('网络错误')}loading.value=false}
    function goRegister(){window.location.href='/user/register'}
    return{form,rules,f,loading,onSubmit,goRegister};
}});
<?php if($isMobile): ?>app.use(vant);<?php else: ?>app.use(ElementPlus);<?php endif; ?>app.mount('#app');
</script></body></html>