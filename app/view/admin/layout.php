<?php
// 共享: 移动端检测
$isMobile = false;
$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
foreach (['mobile','android','iphone','ipad','ipod'] as $m) {
    if (strpos($ua, $m) !== false) { $isMobile = true; break; }
}

// 共享: 后台布局头部
function admin_layout_start($title, $active = 'dashboard') {
    global $isMobile;
    $nav = [
        'dashboard' => ['⏹', '控制台'],
        'driver' => ['⚙', '接口管理'],
        'record' => ['☰', '认证记录'],
        'audit' => ['☑', '人工审核'],
        'token' => ['🔑', 'Token管理'],
        'users' => ['👥', '用户管理'],
        'setting' => ['⚙', '系统设置'],
        'plugin' => ['📦', '插件中心'],
    ];
    $collapseClass = $isMobile ? ' collapsed' : '';
    echo '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>' . $title . ' - 森码云</title>';
    echo '<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css">';
    echo '<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>';
    echo '<script src="https://unpkg.com/element-plus"></script>';
    echo '<script src="https://unpkg.com/axios/dist/axios.min.js"></script>';
    echo '<script src="https://unpkg.com/echarts@5/dist/echarts.min.js"></script>';
    echo '<style>
:root{--el-color-primary:#4F46E5}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;background:#F8FAFC;color:#1F2937;display:flex;min-height:100vh;line-height:1.5}
.sidebar{width:220px;background:#fff;border-right:1px solid #E2E8F0;position:fixed;top:0;left:0;bottom:0;z-index:50;box-shadow:0 1px 3px rgba(0,0,0,.06);transition:width .3s}
.sidebar.collapsed{width:64px}
.side-h{padding:18px 20px;border-bottom:1px solid #E2E8F0;font-size:16px;font-weight:700;color:#4F46E5;display:flex;align-items:center;gap:10px;overflow:hidden;white-space:nowrap}
.sidebar.collapsed .side-h{justify-content:center;padding:18px 0}
.sidebar.collapsed .side-h span{display:none}
.side-nav{padding:12px 0;overflow-y:auto}
.side-nav a{display:flex;align-items:center;gap:10px;padding:11px 20px;color:#6B7280;text-decoration:none;font-size:13px;border-left:3px solid transparent;transition:all .15s;white-space:nowrap;overflow:hidden}
.sidebar.collapsed .side-nav a{justify-content:center;padding:12px 0}
.sidebar.collapsed .side-nav a span{display:none}
.side-nav a:hover{color:#1F2937;background:#F1F5F9}
.side-nav a.active{color:#4F46E5;background:#EEF2FF;border-left-color:#4F46E5;font-weight:500}
.side-foot{border-top:1px solid #E2E8F0;padding:8px 0}
.side-foot a{display:flex;align-items:center;gap:8px;padding:10px 20px;color:#6B7280;text-decoration:none;font-size:13px;white-space:nowrap;overflow:hidden}
.sidebar.collapsed .side-foot a{justify-content:center;padding:10px 0}
.sidebar.collapsed .side-foot a span{display:none}
.main{margin-left:220px;flex:1;min-height:100vh;display:flex;flex-direction:column;transition:margin-left .3s}
.sidebar.collapsed ~ .main, .main.collapsed{margin-left:64px}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 28px;background:#fff;border-bottom:1px solid #E2E8F0;position:sticky;top:0;z-index:40}
.topbar h2{font-size:17px;font-weight:600}
.topbar .hamburger{cursor:pointer;font-size:20px;color:#6B7280;padding:4px 8px;border:none;background:none}
.topbar .hamburger:hover{color:#4F46E5}
.content{flex:1;padding:28px}
.card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.06);margin-bottom:16px}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
.stat-card{padding:20px 24px;transition:all .15s}
.stat-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.06);transform:translateY(-2px)}
.stat-card .l{font-size:12px;color:#6B7280;font-weight:500}.stat-card .v{font-size:28px;font-weight:700;margin:6px 0;line-height:1}.stat-card .s{font-size:11px;color:#9CA3AF}
@media(max-width:768px){.sidebar{width:64px}.sidebar .side-h span,.sidebar .side-nav a span,.sidebar .side-foot a span{display:none}.sidebar .side-h{justify-content:center;padding:14px 0}.sidebar .side-nav a{justify-content:center;padding:12px 0}.sidebar .side-foot a{justify-content:center;padding:10px 0}.main{margin-left:64px}.content{padding:14px}.stats-grid{grid-template-columns:repeat(2,1fr)}}
</style></head><body>';
    echo '<aside class="sidebar' . $collapseClass . '" id="sidebar"><div class="side-h">🛡 <span>森码云</span></div><nav class="side-nav">';
    foreach ($nav as $k => $v) {
        $cls = ($active === $k) ? ' active' : '';
        echo '<a href="/admin/' . $k . '" class="' . $cls . '">' . $v[0] . ' <span>' . $v[1] . '</span></a>';
    }
    echo '</nav><div class="side-foot"><a href="/">🏠 <span>返回首页</span></a><a href="/admin/logout">➡ <span>退出登录</span></a></div></aside>';
    echo '<main class="main' . $collapseClass . '" id="main"><div class="topbar"><div style="display:flex;align-items:center;gap:12px"><button class="hamburger" onclick="toggleSidebar()">☰</button><h2>' . $title . '</h2></div><span style="font-size:13px;color:#6B7280">管理员</span></div><div class="content">';
}

function admin_layout_end() {
    echo '</div></main>';
    echo '<script>function toggleSidebar(){document.getElementById("sidebar").classList.toggle("collapsed");document.getElementById("main").classList.toggle("collapsed")}</script>';
    echo '</body></html>';
}