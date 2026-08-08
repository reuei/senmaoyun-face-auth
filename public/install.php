<?php
/**
 * 森码云实人认证系统 - 安装向导
 * 访问 /install.php 即可开始安装，无需任何依赖
 */
$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;
$error = '';
$success = '';

// 处理安装提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'setup') {
        $db_host   = trim($_POST['db_host'] ?? '127.0.0.1');
        $db_port   = trim($_POST['db_port'] ?? '3306');
        $db_name   = trim($_POST['db_name'] ?? 'senmaoyun');
        $db_user   = trim($_POST['db_user'] ?? '');
        $db_pass   = trim($_POST['db_pass'] ?? '');
        $db_prefix = trim($_POST['db_prefix'] ?? 'smy_');
        $admin_user = trim($_POST['admin_user'] ?? '');
        $admin_pass = trim($_POST['admin_pass'] ?? '');
        $admin_email = trim($_POST['admin_email'] ?? '');

        if (empty($db_user) || empty($admin_user) || empty($admin_pass)) {
            $error = '请填写必填字段';
        } else {
            try {
                // 连接数据库
                $dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
                $pdo = new PDO($dsn, $db_user, $db_pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$db_name}`");

                // 执行SQL
                $sqlFile = __DIR__ . '/../database/install.sql';
                if (!file_exists($sqlFile)) {
                    $error = '安装SQL文件不存在';
                } else {
                    $sql = file_get_contents($sqlFile);
                    $sql = str_replace('{prefix}', $db_prefix, $sql);
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    foreach ($statements as $stmt) {
                        if (!empty($stmt)) {
                            try { $pdo->exec($stmt); } catch (\PDOException $e) {
                                if (strpos($e->getMessage(), 'already exists') === false) {
                                    throw $e;
                                }
                            }
                        }
                    }

                    // 写入 .env
                    $env = "[APP]\nAPP_DEBUG = false\nSITE_NAME = 森码云实人认证系统\nSITE_DOMAIN = face.builds.codes\nAPI_SECRET = " . bin2hex(random_bytes(32)) . "\nMOFANG_URL =\n\n";
                    $env .= "[DATABASE]\nTYPE = mysql\nHOSTNAME = {$db_host}\nDATABASE = {$db_name}\nUSERNAME = {$db_user}\nPASSWORD = {$db_pass}\nHOSTPORT = {$db_port}\nPREFIX = {$db_prefix}\n\n";
                    $env .= "[FACE]\nDEFAULT_DRIVER = self\nLIVENESS_THRESHOLD = 80\nMAX_RETRY = 3\nRATE_LIMIT = 10\nDATA_RETENTION = 24\nENCRYPTION_KEY = " . base64_encode(random_bytes(32)) . "\n";
                    file_put_contents(__DIR__ . '/../.env', $env);

                    // 创建管理员
                    $hash = password_hash($admin_pass, PASSWORD_BCRYPT, ['cost' => 12]);
                    $pdo->prepare("INSERT INTO `{$db_prefix}admin` (username, password, nickname, email, role, status) VALUES (?, ?, ?, ?, 'super', 1)")
                        ->execute([$admin_user, $hash, '管理员', $admin_email]);

                    // 安装锁
                    file_put_contents(__DIR__ . '/../install.lock', date('Y-m-d H:i:s'));
                    $success = '安装成功！管理后台: /admin/ 用户名: ' . h($admin_user);
                }
            } catch (\PDOException $e) {
                $error = '数据库错误: ' . $e->getMessage();
            } catch (\Throwable $e) {
                $error = '安装失败: ' . $e->getMessage();
            }
        }
    }
}

// 环境检测
$checks = [];
if ($step === 1 || isset($_GET['check'])) {
    $checks = [
        ['name' => 'PHP版本 >= 8.1', 'pass' => version_compare(PHP_VERSION, '8.1.0', '>='), 'current' => PHP_VERSION],
        ['name' => 'cURL扩展', 'pass' => extension_loaded('curl'), 'current' => extension_loaded('curl') ? '已启用' : '未启用'],
        ['name' => 'OpenSSL扩展', 'pass' => extension_loaded('openssl'), 'current' => extension_loaded('openssl') ? '已启用' : '未启用'],
        ['name' => 'GD扩展', 'pass' => extension_loaded('gd'), 'current' => extension_loaded('gd') ? '已启用' : '未启用'],
        ['name' => 'Fileinfo扩展', 'pass' => extension_loaded('fileinfo'), 'current' => extension_loaded('fileinfo') ? '已启用' : '未启用'],
        ['name' => 'MBString扩展', 'pass' => extension_loaded('mbstring'), 'current' => extension_loaded('mbstring') ? '已启用' : '未启用'],
        ['name' => 'PDO MySQL扩展', 'pass' => extension_loaded('pdo_mysql'), 'current' => extension_loaded('pdo_mysql') ? '已启用' : '未启用'],
        ['name' => '目录写入权限', 'pass' => is_writable(__DIR__ . '/..'), 'current' => is_writable(__DIR__ . '/..') ? '可写' : '不可写'],
    ];
    if (isset($_GET['check'])) {
        header('Content-Type: application/json');
        echo json_encode(['checks' => $checks], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$allPass = count(array_filter($checks, fn($c) => $c['pass'])) === count($checks);
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>安装向导 - 森码云实人认证系统</title>
<style>
:root{--p:#4F46E5;--pl:#EEF2FF;--t:#1F2937;--ts:#6B7280;--tm:#9CA3AF;--bg:#F9FAFB;--bw:#FFF;--bd:#E5E7EB;--s:#10B981;--e:#EF4444;--r:12px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans SC",sans-serif;background:var(--bg);color:var(--t);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:var(--bw);border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,.08);border:1px solid var(--bd);max-width:600px;width:100%;padding:40px}
.steps{display:flex;gap:8px;margin-bottom:32px}
.step{flex:1;height:4px;background:var(--bd);border-radius:2px}
.step.active{background:var(--p)}.step.done{background:var(--s)}
h1{font-size:24px;margin-bottom:6px}
.sub{color:var(--ts);margin-bottom:24px;font-size:14px}
.fg{margin-bottom:16px}
.fg label{display:block;font-size:14px;font-weight:500;margin-bottom:5px;color:var(--t)}
.fg input{width:100%;padding:10px 14px;border:1px solid var(--bd);border-radius:8px;font-size:14px;outline:none;transition:border-color .15s}
.fg input:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--pl)}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:var(--p);color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:500;cursor:pointer;text-decoration:none}
.btn:hover{background:#4338CA}.btn:disabled{opacity:.5;cursor:not-allowed}
.check-item{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--bd);font-size:14px}
.check-pass{color:var(--s);font-weight:600;margin-left:auto}
.check-fail{color:var(--e);font-weight:600;margin-left:auto}
.msg-error{background:#FEE2E2;color:var(--e);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
.msg-success{background:#D1FAE5;color:var(--s);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:480px){.card{padding:24px}.form-row{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="card">
<h1>森码云实人认证系统</h1>
<div class="steps">
    <div class="step <?php echo $step>=1?'active':''; echo $allPass?'done':''; ?>"></div>
    <div class="step <?php echo $step>=2?'active':''; ?>"></div>
</div>

<?php if ($error): ?>
<div class="msg-error"><?php echo h($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="msg-success"><?php echo $success; ?></div>
<p style="margin-top:16px"><a href="/admin/" class="btn">进入管理后台</a></p>
<?php elseif ($step === 0): ?>
<p class="sub">欢迎使用森码云实人认证系统。请点击下方按钮开始环境检测。</p>
<button class="btn" onclick="checkEnv()">开始环境检测</button>
<div id="checkResult" style="margin-top:20px"></div>
<script>
async function checkEnv(){
    const btn=event.target;btn.disabled=true;btn.textContent='检测中...';
    try{
        const r=await fetch('?check=1');const d=await r.json();
        let html='',allPass=true;
        for(const c of d.checks){
            html+=`<div class="check-item"><span>${c.name}: ${c.current}</span><span class="${c.pass?'check-pass':'check-fail'}">${c.pass?'✓':'✗'}</span></div>`;
            if(!c.pass)allPass=false;
        }
        if(allPass){html+='<div class="msg-success">环境检测全部通过！</div>';
            html+='<a href="?step=1" class="btn" style="margin-top:16px">下一步，配置数据库</a>';}
        else{html+='<div class="msg-error">请根据提示修复后重试</div>';}
        document.getElementById('checkResult').innerHTML=html;
        btn.style.display='none';
    }catch(e){document.getElementById('checkResult').innerHTML='<div class="msg-error">检测失败: '+e.message+'</div>';btn.disabled=false;btn.textContent='重新检测';}
}
</script>

<?php elseif ($step === 1): ?>
<p class="sub">步骤 2/2: 配置数据库和管理员账号</p>
<form method="post">
<input type="hidden" name="action" value="setup">
<div class="form-row">
    <div class="fg"><label>数据库主机</label><input name="db_host" value="127.0.0.1" required></div>
    <div class="fg"><label>端口</label><input name="db_port" value="3306" required></div>
</div>
<div class="fg"><label>数据库名称</label><input name="db_name" value="senmaoyun" required></div>
<div class="form-row">
    <div class="fg"><label>数据库用户名</label><input name="db_user" required></div>
    <div class="fg"><label>数据库密码</label><input name="db_pass" type="password"></div>
</div>
<div class="fg"><label>表前缀</label><input name="db_prefix" value="smy_"></div>
<hr style="border:none;border-top:1px solid var(--bd);margin:20px 0">
<div class="form-row">
    <div class="fg"><label>管理员用户名</label><input name="admin_user" required></div>
    <div class="fg"><label>管理员密码</label><input name="admin_pass" type="password" required></div>
</div>
<div class="fg"><label>管理员邮箱</label><input name="admin_email" type="email"></div>
<button type="submit" class="btn">开始安装</button>
</form>
<?php endif; ?>
</div>
</body>
</html>