<?php
/**
 * 安装向导 - 纯PHP模式
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'setup') {
        $db_host = trim($_POST['db_host'] ?? '127.0.0.1');
        $db_port = trim($_POST['db_port'] ?? '3306');
        $db_name = trim($_POST['db_name'] ?? 'senmaoyun');
        $db_user = trim($_POST['db_user'] ?? '');
        $db_pass = trim($_POST['db_pass'] ?? '');
        $db_prefix = trim($_POST['db_prefix'] ?? 'smy_');
        $admin_user = trim($_POST['admin_user'] ?? '');
        $admin_pass = trim($_POST['admin_pass'] ?? '');

        if (empty($db_user) || empty($admin_user) || empty($admin_pass)) {
            $error = '请填写必填字段';
        } else {
            try {
                $dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
                $pdo = new PDO($dsn, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$db_name}`");

                $sqlFile = __DIR__ . '/../database/install.sql';
                $sql = str_replace('{prefix}', $db_prefix, file_get_contents($sqlFile));
                foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
                    try { $pdo->exec($stmt); } catch (PDOException $e) {
                        if (strpos($e->getMessage(), 'already exists') === false) throw $e;
                    }
                }

                $env = "[APP]\nAPP_DEBUG=false\nSITE_NAME=森码云实人认证系统\nSITE_DOMAIN=face.builds.codes\nAPI_SECRET=" . bin2hex(random_bytes(32)) . "\n\n";
                $env .= "[DATABASE]\nDRIVER=mysql\nHOSTNAME={$db_host}\nDATABASE={$db_name}\nUSERNAME={$db_user}\nPASSWORD={$db_pass}\nHOSTPORT={$db_port}\nPREFIX={$db_prefix}\n";
                file_put_contents(__DIR__ . '/../.env', $env);

                $hash = password_hash($admin_pass, PASSWORD_BCRYPT, ['cost' => 12]);
                $pdo->prepare("INSERT INTO `{$db_prefix}admin` (username, password, nickname, role, status) VALUES (?, ?, '管理员', 'super', 1)")->execute([$admin_user, $hash]);

                file_put_contents(__DIR__ . '/../install.lock', date('Y-m-d H:i:s'));
                $success = '安装成功！管理后台: /admin/ 用户名: ' . htmlspecialchars($admin_user);
            } catch (PDOException $e) {
                $error = '数据库错误: ' . $e->getMessage();
            } catch (Throwable $e) {
                $error = '安装失败: ' . $e->getMessage();
            }
        }
    }
}

$checks = [];
if (isset($_GET['check'])) {
    $checks = [
        ['name' => 'PHP版本 >= 8.1', 'pass' => version_compare(PHP_VERSION, '8.1.0', '>='), 'current' => PHP_VERSION],
        ['name' => 'cURL扩展', 'pass' => extension_loaded('curl'), 'current' => extension_loaded('curl') ? '已启用' : '未启用'],
        ['name' => 'OpenSSL扩展', 'pass' => extension_loaded('openssl'), 'current' => extension_loaded('openssl') ? '已启用' : '未启用'],
        ['name' => 'GD扩展', 'pass' => extension_loaded('gd'), 'current' => extension_loaded('gd') ? '已启用' : '未启用'],
        ['name' => 'PDO MySQL', 'pass' => extension_loaded('pdo_mysql'), 'current' => extension_loaded('pdo_mysql') ? '已启用' : '未启用'],
        ['name' => '目录写入', 'pass' => is_writable(__DIR__ . '/..'), 'current' => is_writable(__DIR__ . '/..') ? '可写' : '不可写'],
    ];
    header('Content-Type: application/json');
    echo json_encode(['checks' => $checks], JSON_UNESCAPED_UNICODE);
    exit;
}
?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>安装向导 - 森码云 v2.0.1</title>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script><script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:#F8FAFC}</style></head><body><div id="app">
<el-card style="width:100%;max-width:600px;padding:20px"><h2 style="text-align:center;margin-bottom:4px">森码云实人认证系统 v2.0.1</h2><p style="text-align:center;color:#6B7280;margin-bottom:24px">安装向导 - 无需composer，上传即用</p>
<el-steps :active="activeStep" finish-status="success" align-center style="margin-bottom:24px"><el-step title="环境检测"/><el-step title="数据库配置"/></el-steps>
<div v-if="activeStep===0"><el-table :data="checks" stripe><el-table-column prop="name" label="检测项"/><el-table-column prop="current" label="当前值"/><el-table-column label="结果" width="60"><template #default="{row}"><el-tag :type="row.pass?'success':'danger'">{{row.pass?'通过':'失败'}}</el-tag></template></el-table-column></el-table>
<el-button type="primary" @click="checkEnv" :loading="checking" style="margin-top:16px;width:100%">{{checking?'检测中...':'开始检测'}}</el-button>
<el-button v-if="allPass" @click="activeStep=1" style="margin-top:12px;width:100%">下一步</el-button></div>
<div v-if="activeStep===1"><el-form :model="form" label-width="100px">
<el-form-item label="数据库主机"><el-input v-model="form.db_host"/></el-form-item><el-form-item label="端口"><el-input v-model="form.db_port"/></el-form-item>
<el-form-item label="数据库名称"><el-input v-model="form.db_name"/></el-form-item><el-form-item label="用户名"><el-input v-model="form.db_user"/></el-form-item>
<el-form-item label="密码"><el-input v-model="form.db_pass" type="password" show-password/></el-form-item><el-form-item label="表前缀"><el-input v-model="form.db_prefix"/></el-form-item>
<el-divider/><el-form-item label="管理员用户名"><el-input v-model="form.admin_user"/></el-form-item><el-form-item label="管理员密码"><el-input v-model="form.admin_pass" type="password" show-password/></el-form-item>
<el-form-item><el-button type="primary" @click="install" :loading="installing" style="width:100%">{{installing?'安装中...':'开始安装'}}</el-button></el-form-item></el-form></div>
<div v-if="installed" style="text-align:center;padding:20px"><el-result icon="success" title="安装成功"><template #extra><el-button type="primary" @click="goAdmin">进入管理后台</el-button></template></el-result></div>
</el-card></div>
<script>
const{createApp,ref,reactive}=Vue;createApp({setup(){const activeStep=ref(0),checking=ref(false),installing=ref(false),installed=ref(false),checks=ref([]),allPass=ref(false);const form=reactive({db_host:'127.0.0.1',db_port:'3306',db_name:'senmaoyun',db_user:'',db_pass:'',db_prefix:'smy_',admin_user:'',admin_pass:''});async function checkEnv(){checking.value=true;try{const r=await axios.get('?check=1');if(r.data&&r.data.checks){checks.value=r.data.checks;allPass.value=checks.value.every(function(c){return c.pass})}}catch(e){}checking.value=false}async function install(){installing.value=true;try{var d=new FormData();for(var k in form)d.append(k,form[k]);d.append('action','setup');const r=await axios.post('',d);if(r.data.code===200)installed.value=true;else ElementPlus.ElMessage.error(r.data.msg)}catch(e){ElementPlus.ElMessage.error('安装失败')}installing.value=false}function goAdmin(){window.location.href='/admin/login'}return{activeStep,checking,installing,installed,checks,allPass,form,checkEnv,install,goAdmin}}}).use(ElementPlus).mount('#app');
</script></body></html>