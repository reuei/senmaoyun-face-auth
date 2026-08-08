<?php
/**
 * 管理后台共享布局 v1.0.5
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

function admin_header($title, $active = 'dashboard') {
    $nav = [
        'dashboard' => ['📊', '控制台'],
        'driver'    => ['🔌', '接口管理'],
        'record'    => ['📋', '认证记录'],
        'audit'     => ['✅', '人工审核'],
        'token'     => ['🔑', 'Token管理'],
        'users'     => ['👥', '用户管理'],
        'setting'   => ['⚙', '系统设置'],
        'plugin'    => ['📦', '插件中心'],
    ];
    $username = htmlspecialchars($_SESSION['admin_username'] ?? '');
    echo '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">';
    echo '<title>' . htmlspecialchars($title) . ' - 森码云</title>';
    echo '<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css">';
    echo '<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>';
    echo '<script src="https://unpkg.com/element-plus"></script>';
    echo '<script src="https://unpkg.com/axios/dist/axios.min.js"></script>';
    echo '<script src="https://unpkg.com/echarts@5/dist/echarts.min.js"></script>';
    echo '<style>
:root{--el-color-primary:#4F46E5}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;background:#F8FAFC;color:#1F2937;display:flex;min-height:100vh;line-height:1.5}
.sidebar{width:220px;background:#fff;border-right:1px solid #E2E8F0;position:fixed;top:0;left:0;bottom:0;z-index:50;box-shadow:0 1px 3px rgba(0,0,0,.06);transition:width .3s;overflow:hidden}
.sidebar.collapsed{width:64px}
.side-h{padding:18px 20px;border-bottom:1px solid #E2E8F0;font-size:16px;font-weight:700;color:#4F46E5;display:flex;align-items:center;gap:10px;white-space:nowrap}
.sidebar.collapsed .side-h{justify-content:center;padding:18px 0}
.sidebar.collapsed .side-h span{display:none}
.side-nav{padding:12px 0;overflow-y:auto;flex:1}
.side-nav a{display:flex;align-items:center;gap:10px;padding:11px 20px;color:#6B7280;text-decoration:none;font-size:13px;border-left:3px solid transparent;white-space:nowrap;transition:all .15s}
.sidebar.collapsed .side-nav a{justify-content:center;padding:12px 0}
.sidebar.collapsed .side-nav a span{display:none}
.side-nav a:hover{color:#1F2937;background:#F1F5F9}
.side-nav a.active{color:#4F46E5;background:#EEF2FF;border-left-color:#4F46E5;font-weight:500}
.side-foot{border-top:1px solid #E2E8F0;padding:8px 0}
.side-foot a{display:flex;align-items:center;gap:8px;padding:10px 20px;color:#6B7280;text-decoration:none;font-size:13px;white-space:nowrap}
.sidebar.collapsed .side-foot a{justify-content:center;padding:10px 0}
.sidebar.collapsed .side-foot a span{display:none}
.main{margin-left:220px;flex:1;min-height:100vh;display:flex;flex-direction:column;transition:margin-left .3s}
.main.expanded{margin-left:64px}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 24px;background:#fff;border-bottom:1px solid #E2E8F0;position:sticky;top:0;z-index:40;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.topbar h2{font-size:17px;font-weight:600}
.topbar-left{display:flex;align-items:center;gap:12px}
.hamburger{font-size:22px;cursor:pointer;color:#6B7280;background:none;border:none;padding:2px 6px;line-height:1;transition:color .15s}
.hamburger:hover{color:#4F46E5}
.content{flex:1;padding:24px}
.card{background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.04);margin-bottom:16px}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
.stat-card{padding:20px 24px;transition:all .15s}
.stat-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.06);transform:translateY(-2px)}
.stat-card .l{font-size:12px;color:#6B7280;font-weight:500}.stat-card .v{font-size:28px;font-weight:700;margin:6px 0;line-height:1}.stat-card .s{font-size:11px;color:#9CA3AF}
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:13px}
table th,table td{padding:10px 14px;text-align:left;border-bottom:1px solid #E2E8F0}
table th{font-weight:600;color:#6B7280;background:#F1F5F9;font-size:12px;white-space:nowrap}
table tbody tr:hover{background:#F8FAFC}
.badge{display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:500}
.badge-s{background:#D1FAE5;color:#10B981}.badge-e{background:#FEE2E2;color:#EF4444}.badge-w{background:#FEF3C7;color:#F59E0B}.badge-i{background:#DBEAFE;color:#3B82F6}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;border:1px solid transparent;text-decoration:none;transition:all .15s}
.btn-p{background:#4F46E5;color:#fff}.btn-p:hover{background:#4338CA}
.btn-s{background:#fff;color:#1F2937;border-color:#E2E8F0}.btn-s:hover{background:#F1F5F9}
.btn-sm{padding:5px 12px;font-size:12px}
.fg{margin-bottom:14px}.fg label{display:block;font-size:13px;font-weight:500;margin-bottom:4px}.fg input,.fg select{width:100%;padding:9px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;outline:none;transition:border-color .15s}.fg input:focus,.fg select:focus{border-color:#4F46E5;box-shadow:0 0 0 3px #EEF2FF}
.msg-ok{background:#D1FAE5;color:#10B981;padding:10px 14px;border-radius:6px;font-size:13px;margin-bottom:14px}
.msg-err{background:#FEE2E2;color:#EF4444;padding:10px 14px;border-radius:6px;font-size:13px;margin-bottom:14px}
.mb-16{margin-bottom:16px}.mt-16{margin-top:16px}
.flex-between{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.flex-row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
@media(max-width:768px){.sidebar{width:64px}.sidebar .side-h span,.sidebar .side-nav a span,.sidebar .side-foot a span{display:none}.sidebar .side-h{justify-content:center;padding:14px 0}.sidebar .side-nav a{justify-content:center;padding:12px 0}.main{margin-left:64px}.content{padding:14px}.stats-grid{grid-template-columns:repeat(2,1fr)}}
</style></head><body>';
    echo '<aside class="sidebar" id="sidebar"><div class="side-h">🛡 <span>森码云</span></div><nav class="side-nav">';
    foreach ($nav as $k => $v) {
        $cls = ($active === $k) ? ' active' : '';
        echo '<a href="/admin/' . $k . '" class="' . $cls . '">' . $v[0] . ' <span>' . $v[1] . '</span></a>';
    }
    echo '</nav><div class="side-foot"><a href="/">🏠 <span>返回首页</span></a><a href="/admin/logout">➡ <span>退出登录</span></a></div></aside>';
    echo '<main class="main" id="main"><div class="topbar"><div class="topbar-left"><button class="hamburger" onclick="toggleSidebar()">☰</button><h2>' . htmlspecialchars($title) . '</h2></div><span style="font-size:13px;color:#6B7280">👤 ' . $username . '</span></div><div class="content">';
}

function admin_footer() {
    echo '</div></main>';
    echo '<script>function toggleSidebar(){var s=document.getElementById("sidebar"),m=document.getElementById("main");s.classList.toggle("collapsed");m.classList.toggle("expanded")}</script>';
    echo '</body></html>';
}