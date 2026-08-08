<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? ''); $password = trim($_POST['password'] ?? ''); $email = trim($_POST['email'] ?? '');
    if (empty($username) || empty($password)) { json_error('请填写用户名和密码'); }
    if (mb_strlen($username) < 3) { json_error('用户名至少3个字符'); }
    if (mb_strlen($password) < 6) { json_error('密码至少6个字符'); }
    if (db()->fetch("SELECT id FROM " . db()->table('user') . " WHERE username=?", [$username])) { json_error('用户名已存在'); }
    if (!empty($email) && db()->fetch("SELECT id FROM " . db()->table('user') . " WHERE email=?", [$email])) { json_error('邮箱已被注册'); }
    db()->insert(db()->table('user'), ['username' => $username, 'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), 'nickname' => $username, 'email' => $email, 'last_login_ip' => get_client_ip(), 'last_login_time' => date('Y-m-d H:i:s')]);
    $user = db()->fetch("SELECT id,username FROM " . db()->table('user') . " WHERE username=?", [$username]);
    session_start(); $_SESSION['user_id'] = $user['id']; $_SESSION['user_username'] = $user['username'];
    json_success([], '注册成功');
}

$isMobile = false; $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
foreach (['mobile','android','iphone','ipad','ipod'] as $m) { if (strpos($ua, $m) !== false) { $isMobile = true; break; } }
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>用户注册 - 森码云</title>
<?php if($isMobile): ?><link rel="stylesheet" href="https://unpkg.com/vant@4/lib/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/vant@4/lib/vant.min.js"></script>
<?php else: ?><link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script>
<?php endif; ?><script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:#F8FAFC;color:#1F2937}
.card{width:100%;max-width:400px;padding:40px;background:#fff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,.08);border:1px solid #E2E8F0;text-align:center}
.card h2{font-size:20px;margin-bottom:4px}.card .sub{color:#6B7280;font-size:13px;margin-bottom:28px}.link{font-size:12px;color:#9CA3AF;text-decoration:none;display:block;margin-top:16px}</style></head><body>
<div id="app"><div class="card"><h2>用户注册</h2><p class="sub">森码云实人认证系统</p>
<?php if($isMobile): ?><van-form @submit="register"><van-cell-group inset><van-field v-model="form.username" label="用户名" placeholder="至少3个字符" :rules="[{required:true}]"/><van-field v-model="form.password" type="password" label="密码" placeholder="至少6个字符" :rules="[{required:true}]"/><van-field v-model="form.email" label="邮箱" placeholder="选填"/></van-cell-group><div style="margin:20px 16px"><van-button round block type="primary" native-type="submit" :loading="loading">注册</van-button></div></van-form><div style="text-align:center"><van-button size="small" plain @click="goLogin">已有账号？立即登录</van-button></div>
<?php else: ?><el-form :model="form" :rules="rules" ref="f" @submit.prevent="register"><el-form-item prop="username"><el-input v-model="form.username" placeholder="用户名(至少3个字符)"/></el-form-item><el-form-item prop="password"><el-input v-model="form.password" type="password" placeholder="密码(至少6个字符)" show-password/></el-form-item><el-form-item><el-input v-model="form.email" placeholder="邮箱(选填)"/></el-form-item><el-form-item><el-button type="primary" native-type="submit" :loading="loading" style="width:100%">注册</el-button></el-form-item></el-form><div style="text-align:center"><el-link type="primary" @click="goLogin">已有账号？立即登录</el-link></div>
<?php endif; ?><a href="/" class="link">返回首页</a></div></div>
<script>
const{createApp,ref,reactive}=Vue;const app=createApp({setup(){const form=reactive({username:'',password:'',email:''});const rules={username:[{required:true},{min:3}],password:[{required:true},{min:6}]};const f=ref(null);const loading=ref(false);
async function register(){<?php if(!$isMobile): ?>const v=await f.value.validate().catch(function(){return false});if(!v)return;<?php endif; ?>loading.value=true;try{const r=await axios.post('/user/register',form);if(r.data.code===200)window.location.href='/user/center';else <?php echo $isMobile?'vant.showToast':'ElementPlus.ElMessage.error'; ?>(r.data.msg||'注册失败')}catch(e){<?php echo $isMobile?'vant.showToast':'ElementPlus.ElMessage.error'; ?>('网络错误')}loading.value=false}
function goLogin(){window.location.href='/user/login'}return{form,rules,f,loading,register,goLogin}}});
<?php if($isMobile): ?>app.use(vant);<?php else: ?>app.use(ElementPlus);<?php endif; ?>app.mount('#app');
</script></body></html>