<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>管理员登录 - 森码云</title>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script><script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:#F8FAFC;color:#1F2937}
.card{width:100%;max-width:400px;padding:40px;background:#fff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,.08);border:1px solid #E2E8F0;text-align:center}
.card h2{font-size:20px;margin-bottom:4px}.card .sub{color:#6B7280;font-size:13px;margin-bottom:28px}
</style></head><body>
<div id="app"><div class="card"><h2>森码云管理后台</h2><p class="sub">请使用管理员账号登录</p>
<el-form :model="form" :rules="rules" ref="formRef" @submit.prevent="login">
<el-form-item prop="username"><el-input v-model="form.username" placeholder="用户名" /></el-form-item>
<el-form-item prop="password"><el-input v-model="form.password" type="password" placeholder="密码" show-password /></el-form-item>
<el-form-item><el-button type="primary" native-type="submit" :loading="loading" style="width:100%">登录</el-button></el-form-item>
</el-form>
<a href="/" style="font-size:12px;color:#9CA3AF;text-decoration:none">返回首页</a>
</div></div>
<script>
const{createApp,ref,reactive}=Vue;
createApp({setup(){
    const form=reactive({username:'',password:''});const rules={username:[{required:true,message:'请输入用户名'}],password:[{required:true,message:'请输入密码'}]};const formRef=ref(null);const loading=ref(false);
    async function login(){const valid=await formRef.value.validate().catch(()=>false);if(!valid)return;loading.value=true;try{const r=await axios.post('/admin/login',form);if(r.data.code===200)window.location.href='/admin/dashboard';else ElementPlus.ElMessage.error(r.data.msg||'登录失败')}catch(e){ElementPlus.ElMessage.error('网络错误')}loading.value=false}
    return{form,rules,formRef,loading,login};
}}).use(ElementPlus).mount('#app');
</script></body></html>