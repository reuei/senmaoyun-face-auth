<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (empty($username) || empty($password)) { json_error('请输入用户名和密码'); }
    $admin = db()->fetch("SELECT * FROM " . db()->table('admin') . " WHERE username=? AND status=1", [$username]);
    if (!$admin || !password_verify($password, $admin['password'])) { json_error('用户名或密码错误'); }
    db()->update(db()->table('admin'), ['last_login_ip' => get_client_ip(), 'last_login_time' => date('Y-m-d H:i:s'), 'login_count' => $admin['login_count'] + 1], 'id=?', [$admin['id']]);
    session_start(); $_SESSION['admin_id'] = $admin['id']; $_SESSION['admin_username'] = $admin['username']; $_SESSION['admin_role'] = $admin['role'];
    json_success(['redirect' => '/admin/dashboard'], '登录成功');
}
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>管理员登录 - 森码云</title>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script><script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:linear-gradient(135deg,#F8FAFC,#EEF2FF)}
.card{width:100%;max-width:400px;padding:40px;background:#fff;border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,.08);text-align:center}
.card .logo{margin-bottom:16px}.card .logo svg{width:48px;height:48px}
.card h2{font-size:20px;margin-bottom:4px}.card .sub{color:#6B7280;font-size:13px;margin-bottom:28px}</style></head><body>
<div id="app"><div class="card"><div class="logo"><svg viewBox="0 0 48 48" fill="none"><rect width="48" height="48" rx="12" fill="#4F46E5"/><path d="M24 10l-12 6v12l12 12 12-12v-12l-12-6z" fill="none" stroke="#fff" stroke-width="3"/><path d="M20 24l3 3 5-6" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h2>森码云管理后台</h2><p class="sub">v1.0.3 · 请使用管理员账号登录</p>
<el-form :model="form" :rules="rules" ref="f" @submit.prevent="login"><el-form-item prop="username"><el-input v-model="form.username" placeholder="用户名" size="large"/></el-form-item><el-form-item prop="password"><el-input v-model="form.password" type="password" placeholder="密码" show-password size="large"/></el-form-item><el-form-item><el-button type="primary" native-type="submit" :loading="loading" size="large" style="width:100%">登录</el-button></el-form-item></el-form>
<a href="/" style="font-size:12px;color:#9CA3AF;text-decoration:none">返回首页</a></div></div>
<script>
const{createApp,ref,reactive}=Vue;createApp({setup(){const form=reactive({username:'',password:''});const rules={username:[{required:true,message:'请输入用户名'}],password:[{required:true,message:'请输入密码'}]};const f=ref(null);const loading=ref(false);
async function login(){const v=await f.value.validate().catch(function(){return false});if(!v)return;loading.value=true;try{const r=await axios.post('/admin/login',form);if(r.data.code===200)window.location.href=r.data.data.redirect;else ElementPlus.ElMessage.error(r.data.msg||'登录失败')}catch(e){ElementPlus.ElMessage.error('网络错误')}loading.value=false}
return{form,rules,f,loading,login}}}).use(ElementPlus).mount('#app');
</script></body></html>