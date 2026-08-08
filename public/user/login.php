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
.login-box{display:flex;width:100%;max-width:900px;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.1)}
.login-left{flex:1;background:linear-gradient(135deg,#1e1b4b,#312e81,#4F46E5);padding:60px 40px;display:flex;flex-direction:column;justify-content:center;align-items:center;color:#fff;text-align:center;position:relative;overflow:hidden}
.login-left::after{content:'';position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,.05);top:-50px;right:-80px}
.login-left .logo{width:64px;height:64px;background:rgba(255,255,255,.15);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:24px}
.login-left .logo svg{width:36px;height:36px}
.login-left h1{font-size:26px;font-weight:700;margin-bottom:8px;letter-spacing:-.02em}
.login-left p{font-size:14px;opacity:.8;line-height:1.6;max-width:260px}
.login-left .illustration{margin-top:30px;opacity:.3}
.login-left .illustration svg{width:200px;height:160px}
.login-right{flex:1;padding:60px 50px;display:flex;flex-direction:column;justify-content:center}
.login-right h2{font-size:22px;font-weight:700;margin-bottom:4px;color:#1F2937}
.login-right .sub{font-size:13px;color:#6B7280;margin-bottom:32px}
.login-right .form{display:flex;flex-direction:column;gap:18px}
.login-right .fg{display:flex;flex-direction:column;gap:6px}
.login-right .fg .input-wrap{position:relative;display:flex;align-items:center}
.login-right .fg .input-wrap .icon{position:absolute;left:14px;color:#9CA3AF;font-size:16px;z-index:1}
.login-right .fg input{width:100%;padding:12px 14px 12px 42px;border:1px solid #E2E8F0;border-radius:8px;font-size:14px;outline:none;transition:border-color .15s;background:#F9FAFB}
.login-right .fg input:focus{border-color:#4F46E5;background:#fff;box-shadow:0 0 0 3px #EEF2FF}
.login-right .fg input::placeholder{color:#C4C9D0}
.login-right .row{display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#6B7280}
.login-right .row label{display:flex;align-items:center;gap:6px;cursor:pointer}
.login-right .row a{color:#4F46E5;text-decoration:none}
.login-right .btn{width:100%;padding:14px;background:#4F46E5;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background .15s}.login-right .btn:hover{background:#4338CA}.login-right .btn:disabled{opacity:.5}
.login-right .bottom{text-align:center;margin-top:20px;font-size:13px;color:#6B7280}.login-right .bottom a{color:#4F46E5;text-decoration:none;font-weight:500}
@media(max-width:768px){.login-box{flex-direction:column;max-width:420px}.login-left{padding:40px 30px}.login-left .illustration{display:none}.login-right{padding:36px 30px}}
</style>
<?php endif; ?>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script></head><body><div id="app">
<?php if($isMobile): ?>
<div class="page"><div class="logo-area"><svg viewBox="0 0 56 56" fill="none"><rect width="56" height="56" rx="14" fill="#4F46E5"/><path d="M28 12l-14 7v14l14 14 14-14v-14l-14-7z" stroke="#fff" stroke-width="2.5" fill="none"/><path d="M23 28l3 3 6-7" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg><h1>森码云</h1><p>企业级实人认证</p></div>
<div class="card"><div class="fg"><label>用户名</label><input type="text" v-model="form.username" placeholder="请输入用户名" /></div><div class="fg"><label>密码</label><div class="pwd-wrap"><input :type="pwdType" v-model="form.password" placeholder="请输入密码" /><button type="button" class="toggle" @click="togglePwd">{{pwdType==='password'?'👁':'🙈'}}</button></div></div><div class="check-row"><input type="checkbox" v-model="remember" id="rem" /><label for="rem">记住密码</label></div><button class="btn" @click="onSubmit" :disabled="loading">{{loading?'登录中...':'登录'}}</button><div class="link-row"><a href="/user/register">没有账号？立即注册</a></div></div></div>
<?php else: ?>
<div class="login-box">
<div class="login-left"><div class="logo"><svg viewBox="0 0 36 36" fill="none"><path d="M18 4l-12 6v12l12 12 12-12v-12l-12-6z" fill="none" stroke="#fff" stroke-width="3"/><path d="M14 18l3 3 6-7" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h1>森码云</h1><p>企业级实人认证服务平台<br>安全 · 稳定 · 高效</p><div class="illustration"><svg viewBox="0 0 200 160" fill="none"><circle cx="100" cy="60" r="45" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="2"/><circle cx="100" cy="60" r="25" fill="none" stroke="rgba(255,255,255,.3)" stroke-width="2"/><circle cx="100" cy="60" r="10" fill="rgba(255,255,255,.4)"/><path d="M94 56 L99 61 L107 52" stroke="rgba(255,255,255,.7)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="60" y="120" width="80" height="6" rx="3" fill="rgba(255,255,255,.2)"/><rect x="50" y="134" width="100" height="5" rx="3" fill="rgba(255,255,255,.15)"/></svg></div></div>
<div class="login-right"><h2>欢迎登录</h2><p class="sub">请输入您的账号信息以继续</p>
<div class="form"><div class="fg"><div class="input-wrap"><span class="icon">👤</span><input type="text" v-model="form.username" placeholder="请使用用户名/手机号/邮箱" /></div></div><div class="fg"><div class="input-wrap"><span class="icon">🔒</span><input :type="pwdType" v-model="form.password" placeholder="请输入密码" /></div></div>
<div class="row"><label><input type="checkbox" v-model="remember" /> 记住密码</label><a href="#">忘记密码？</a></div>
<button class="btn" @click="onSubmit" :disabled="loading">{{loading?'登录中...':'立即登录'}}</button>
<div class="bottom">还没有账号？<a href="/user/register">注册新账号</a></div></div></div></div>
<?php endif; ?>
</div>
<script>
const{createApp,ref,reactive,onMounted}=Vue;const app=createApp({setup(){
    const form=reactive({username:'',password:''});const remember=ref(false);const pwdType=ref('password');const loading=ref(false);
    function togglePwd(){pwdType.value=pwdType.value==='password'?'text':'password'}
    onMounted(function(){var u=localStorage.getItem('smy_username');var p=localStorage.getItem('smy_password');if(u&&p){form.username=u;form.password=p;remember.value=true}});
    async function onSubmit(){
        if(!form.username||!form.password){alert('请填写用户名和密码');return}
        loading.value=true;try{const r=await axios.post('/user/login',{username:form.username,password:form.password});if(r.data&&r.data.code===200){if(remember.value){localStorage.setItem('smy_username',form.username);localStorage.setItem('smy_password',form.password)}else{localStorage.removeItem('smy_username');localStorage.removeItem('smy_password')}window.location.href=r.data.data.redirect||'/user/center'}else alert(r.data&&r.data.msg?r.data.msg:'登录失败')}catch(e){alert('网络错误: '+e.message)}loading.value=false}
    function goRegister(){window.location.href='/user/register'}return{form,remember,pwdType,togglePwd,loading,onSubmit,goRegister}}});
<?php if($isMobile): ?>app.mount('#app');<?php else: ?>app.mount('#app');<?php endif; ?>
</script></body></html>