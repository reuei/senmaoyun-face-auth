<?php
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
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>用户登录 - 森码云 v1.0.5</title>
<?php if($isMobile): ?>
<link rel="stylesheet" href="https://unpkg.com/vant@4/lib/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/vant@4/lib/vant.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;min-height:100vh;background:#F5F6FA;color:#1F2937}
.page{padding:16px;max-width:420px;margin:0 auto}.logo-area{text-align:center;padding:30px 0 16px}.logo-area svg{width:56px;height:56px}.logo-area h1{font-size:20px;font-weight:700;margin-top:8px;color:#1F2937}.logo-area p{font-size:13px;color:#6B7280;margin-top:4px}
.card{background:#fff;border-radius:16px;padding:20px;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.fg{margin-bottom:14px}.fg label{display:block;font-size:13px;font-weight:500;margin-bottom:4px;color:#374151}
.fg input{width:100%;padding:12px 14px;border:1px solid #E2E8F0;border-radius:8px;font-size:14px;outline:none;transition:border-color .15s;background:#F9FAFB}
.fg input:focus{border-color:#4F46E5;background:#fff;box-shadow:0 0 0 3px #EEF2FF}
.pwd-wrap{position:relative}.pwd-wrap .toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;font-size:16px;cursor:pointer;color:#9CA3AF;padding:4px}
.btn{width:100%;padding:14px;background:#4F46E5;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s}.btn:hover{background:#4338CA}.btn:disabled{opacity:.5}
.check-row{display:flex;align-items:center;gap:8px;margin:12px 0;font-size:12px;color:#6B7280}.check-row input{width:auto}
.link-row{text-align:center;margin-top:12px}.link-row a{font-size:13px;color:#6B7280;text-decoration:none}
</style>
<?php else: ?>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:linear-gradient(135deg,#F8FAFC,#EEF2FF);color:#1F2937}
.card{width:100%;max-width:440px;padding:40px;background:#fff;border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,.08)}.card .logo{text-align:center;margin-bottom:20px}.card .logo svg{width:52px;height:52px}.card h2{text-align:center;font-size:22px;font-weight:700;margin-bottom:4px}.card .sub{text-align:center;color:#6B7280;font-size:13px;margin-bottom:28px}
</style>
<?php endif; ?>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script></head><body><div id="app">
<?php if($isMobile): ?>
<div class="page"><div class="logo-area"><svg viewBox="0 0 56 56" fill="none"><rect width="56" height="56" rx="14" fill="#4F46E5"/><path d="M28 12l-14 7v14l14 14 14-14v-14l-14-7z" stroke="#fff" stroke-width="2.5" fill="none"/><path d="M23 28l3 3 6-7" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg><h1>森码云</h1><p>企业级实人认证</p></div>
<div class="card">
<div class="fg"><label>用户名</label><input type="text" v-model="form.username" placeholder="请输入用户名" /></div>
<div class="fg"><label>密码</label><div class="pwd-wrap"><input :type="pwdType" v-model="form.password" placeholder="请输入密码" /><button type="button" class="toggle" @click="togglePwd">{{pwdType==='password'?'👁':'🙈'}}</button></div></div>
<div class="check-row"><input type="checkbox" v-model="remember" id="rem" /><label for="rem">记住密码</label></div>
<button class="btn" @click="onSubmit" :disabled="loading">{{loading?'登录中...':'登录'}}</button>
<div class="link-row"><a href="/user/register">没有账号？立即注册</a></div></div></div>
<?php else: ?>
<div class="card"><div class="logo"><svg viewBox="0 0 52 52" fill="none"><rect width="52" height="52" rx="12" fill="#4F46E5"/><path d="M26 10l-13 6.5v13L26 42.5 39 29.5v-13L26 10z" stroke="#fff" stroke-width="3" fill="none"/><path d="M22 26l3 3 6-7" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h2>用户登录</h2><p class="sub">森码云实人认证系统</p>
<el-form :model="form" :rules="rules" ref="f" @submit.prevent="onSubmit">
<el-form-item prop="username"><el-input v-model="form.username" placeholder="用户名" size="large" :prefix-icon="User"/></el-form-item>
<el-form-item prop="password"><el-input v-model="form.password" type="password" placeholder="密码" show-password size="large" :prefix-icon="Lock"/></el-form-item>
<el-form-item><el-checkbox v-model="remember">记住密码</el-checkbox></el-form-item>
<el-form-item><el-button type="primary" native-type="submit" :loading="loading" size="large" style="width:100%">登录</el-button></el-form-item>
</el-form>
<div style="text-align:center"><el-link type="primary" @click="goRegister">没有账号？立即注册</el-link></div></div>
<?php endif; ?>
</div>
<script>
const{createApp,ref,reactive,onMounted}=Vue;const app=createApp({setup(){
    const form=reactive({username:'',password:''});const remember=ref(false);const pwdType=ref('password');const loading=ref(false);
    function togglePwd(){pwdType.value=pwdType.value==='password'?'text':'password'}
    const rules={username:[{required:true,message:'请输入用户名'}],password:[{required:true,message:'请输入密码'}]};const f=ref(null);
    onMounted(function(){var u=localStorage.getItem('smy_username');var p=localStorage.getItem('smy_password');if(u&&p){form.username=u;form.password=p;remember.value=true}});
    async function onSubmit(){
        if(!form.username||!form.password){alert('请填写用户名和密码');return}
        loading.value=true;try{const r=await axios.post('/user/login',{username:form.username,password:form.password});if(r.data&&r.data.code===200){if(remember.value){localStorage.setItem('smy_username',form.username);localStorage.setItem('smy_password',form.password)}else{localStorage.removeItem('smy_username');localStorage.removeItem('smy_password')}window.location.href=r.data.data.redirect||'/user/center'}else alert(r.data&&r.data.msg?r.data.msg:'登录失败')}catch(e){alert('网络错误: '+e.message)}loading.value=false}
    function goRegister(){window.location.href='/user/register'}return{form,remember,pwdType,togglePwd,rules,f,loading,onSubmit,goRegister}}});
<?php if($isMobile): ?>app.mount('#app');<?php else: ?>app.use(ElementPlus).mount('#app');<?php endif; ?>
</script></body></html>