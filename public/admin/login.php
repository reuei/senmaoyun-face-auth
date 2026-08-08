<?php
/**
 * 后台登录页
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        $admin = db()->fetch("SELECT * FROM " . db()->table('admin') . " WHERE username=? AND status=1", [$username]);
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            db()->update(db()->table('admin'), [
                'last_login_ip' => get_client_ip(),
                'last_login_time' => date('Y-m-d H:i:s'),
                'login_count' => $admin['login_count'] + 1,
            ], 'id=?', [$admin['id']]);
            audit_log('login', 'admin', 'admin', $admin['id']);
            redirect('/admin/dashboard.php');
        }
        $error = '用户名或密码错误';
    }
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>管理员登录 - <?php echo SITE_NAME; ?></title>
<style>
:root{--p:#4F46E5;--ph:#4338CA;--e:#EF4444;--el:#FEE2E2;--t:#1F2937;--ts:#6B7280;--tm:#9CA3AF;--bg:#F9FAFB;--bw:#FFF;--bd:#E5E7EB;--r:8px;--rx:16px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans SC",sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background:var(--bg);color:var(--t)}
.card{background:var(--bw);border-radius:var(--rx);box-shadow:0 10px 25px rgba(0,0,0,.08);border:1px solid var(--bd);width:100%;max-width:380px;padding:40px}
.card h1{font-size:20px;font-weight:700;text-align:center;margin-bottom:6px}
.card .sub{text-align:center;color:var(--ts);font-size:13px;margin-bottom:28px}
.fg{margin-bottom:16px}
.fg label{display:block;font-size:13px;font-weight:500;margin-bottom:5px}
.fg input{width:100%;padding:10px 14px;border:1px solid var(--bd);border-radius:var(--r);font-size:14px;outline:none}
.fg input:focus{border-color:var(--p);box-shadow:0 0 0 3px #EEF2FF}
.btn{width:100%;padding:11px;background:var(--p);color:#fff;border:none;border-radius:var(--r);font-size:14px;font-weight:500;cursor:pointer}
.btn:hover{background:var(--ph)}
.msg{background:var(--el);color:var(--e);padding:10px 14px;border-radius:var(--r);font-size:13px;margin-bottom:14px}
.footer{text-align:center;margin-top:20px}
.footer a{font-size:12px;color:var(--tm);text-decoration:none}
</style>
</head>
<body>
<div class="card">
<h1>森码云管理后台</h1>
<p class="sub">请使用管理员账号登录</p>
<?php if(isset($error)): ?><div class="msg"><?php echo h($error); ?></div><?php endif; ?>
<form method="post">
<div class="fg"><label>用户名</label><input name="username" autocomplete="username" required></div>
<div class="fg"><label>密码</label><input name="password" type="password" autocomplete="current-password" required></div>
<button type="submit" class="btn">登录</button>
</form>
<div class="footer"><a href="/">返回首页</a></div>
</div>
</body>
</html>