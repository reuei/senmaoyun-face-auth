<?php
/**
 * 管理后台共享布局
 */
function admin_header($title, $active = 'dashboard')
{
    $nav = [
        'dashboard' => ['icon' => '&#9632;', 'label' => '控制台'],
        'driver'    => ['icon' => '&#9881;', 'label' => '接口管理'],
        'record'    => ['icon' => '&#9776;', 'label' => '认证记录'],
        'audit'     => ['icon' => '&#9745;', 'label' => '人工审核'],
        'token'     => ['icon' => '&#128273;', 'label' => 'Token管理'],
        'setting'   => ['icon' => '&#9881;', 'label' => '系统设置'],
        'plugin'    => ['icon' => '&#128230;', 'label' => '插件中心'],
    ];
    $username = h($_SESSION['admin_username'] ?? '');
    echo '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">';
    echo '<title>' . h($title) . ' - ' . SITE_NAME . '</title>';
    echo '<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>';
    echo '<script src="https://unpkg.com/echarts@5/dist/echarts.min.js"></script>';
    echo '<script src="https://unpkg.com/axios/dist/axios.min.js"></script>';
    echo '<style>
:root{--p:#4F46E5;--ph:#4338CA;--pl:#EEF2FF;--s:#10B981;--sl:#D1FAE5;--e:#EF4444;--el:#FEE2E2;--w:#F59E0B;--wl:#FEF3C7;--i:#3B82F6;--il:#DBEAFE;--t:#1F2937;--ts:#6B7280;--tm:#9CA3AF;--bg:#F8FAFC;--bw:#FFF;--bd:#E2E8F0;--bl:#F1F5F9;--r:6px;--rl:10px;--rx:14px;--sh:0 1px 3px rgba(0,0,0,.06);--shm:0 4px 12px rgba(0,0,0,.06);--tr:.15s ease}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans SC","PingFang SC","Microsoft YaHei",sans-serif;background:var(--bg);color:var(--t);display:flex;min-height:100vh;line-height:1.5}
.sidebar{width:220px;background:var(--bw);border-right:1px solid var(--bd);position:fixed;top:0;left:0;bottom:0;display:flex;flex-direction:column;z-index:50;box-shadow:var(--sh)}
.side-h{padding:18px 20px;border-bottom:1px solid var(--bd);font-size:16px;font-weight:700;display:flex;align-items:center;gap:10px;color:var(--p)}
.side-h .s{font-size:20px}
.side-nav{flex:1;padding:12px 0;overflow-y:auto}
.side-nav a{display:flex;align-items:center;gap:10px;padding:11px 20px;color:var(--ts);text-decoration:none;font-size:13px;border-left:3px solid transparent;transition:all var(--tr)}
.side-nav a:hover{color:var(--t);background:var(--bl)}
.side-nav a.active{color:var(--p);background:var(--pl);border-left-color:var(--p);font-weight:500}
.side-foot{border-top:1px solid var(--bd);padding:8px 0}
.side-foot a{display:flex;align-items:center;gap:8px;padding:10px 20px;color:var(--ts);text-decoration:none;font-size:13px;transition:color var(--tr)}
.side-foot a:hover{color:var(--t)}
.main{margin-left:220px;flex:1;min-height:100vh;display:flex;flex-direction:column}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 28px;background:var(--bw);border-bottom:1px solid var(--bd);position:sticky;top:0;z-index:40;box-shadow:var(--sh)}
.topbar h2{font-size:17px;font-weight:600}
.topbar .user{font-size:13px;color:var(--ts);display:flex;align-items:center;gap:6px}
.content{flex:1;padding:28px}
.card{background:var(--bw);border:1px solid var(--bd);border-radius:var(--rx);padding:24px;box-shadow:var(--sh)}
.card-h{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.card-h h3{font-size:16px;font-weight:600}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
.stat-card{padding:20px 24px;transition:all var(--tr)}
.stat-card:hover{box-shadow:var(--shm);transform:translateY(-2px)}
.stat-card .l{font-size:12px;color:var(--ts);text-transform:uppercase;letter-spacing:.04em;font-weight:500}
.stat-card .v{font-size:28px;font-weight:700;letter-spacing:-.02em;margin:6px 0;line-height:1}
.stat-card .s{font-size:11px;color:var(--tm)}
.table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;font-size:13px;white-space:nowrap}
table th,table td{padding:11px 14px;text-align:left;border-bottom:1px solid var(--bd)}
table th{font-weight:600;color:var(--ts);background:var(--bl);font-size:12px;position:sticky;top:0}
table tbody tr{transition:background var(--tr)}
table tbody tr:hover{background:var(--bl)}
.badge{display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:500;white-space:nowrap}
.badge-s{background:var(--sl);color:var(--s)}.badge-e{background:var(--el);color:var(--e)}
.badge-w{background:var(--wl);color:var(--w)}.badge-i{background:var(--il);color:var(--i)}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--r);font-size:13px;font-weight:500;cursor:pointer;border:1px solid transparent;text-decoration:none;transition:all var(--tr);line-height:1.4;white-space:nowrap}
.btn-p{background:var(--p);color:#fff}.btn-p:hover{background:var(--ph);box-shadow:0 2px 8px rgba(79,70,229,.3)}
.btn-s{background:var(--bw);color:var(--t);border-color:var(--bd)}.btn-s:hover{background:var(--bl);border-color:var(--p)}
.btn-e{background:var(--e);color:#fff}.btn-e:hover{background:#DC2626}
.btn-sm{padding:5px 12px;font-size:12px}
.fg{margin-bottom:16px}
.fg label{display:block;font-size:13px;font-weight:500;margin-bottom:5px;color:var(--t)}
.fg input,.fg select,.fg textarea{width:100%;padding:10px 14px;border:1px solid var(--bd);border-radius:var(--r);font-size:13px;outline:none;font-family:inherit;transition:border-color var(--tr);background:var(--bw)}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--pl)}
.fg textarea{min-height:80px;resize:vertical}
.msg-ok{background:var(--sl);color:var(--s);padding:12px 16px;border-radius:var(--r);font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.msg-err{background:var(--el);color:var(--e);padding:12px 16px;border-radius:var(--r);font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.pagination{display:flex;align-items:center;justify-content:center;gap:12px;margin-top:20px;font-size:13px}
.pagination span{color:var(--ts)}
.empty-state{text-align:center;padding:60px 20px;color:var(--tm)}
.empty-state .icon{font-size:48px;margin-bottom:12px}
.empty-state p{font-size:14px}
.form-inline{display:flex;gap:8px;align-items:center}
.form-inline input{padding:6px 10px;font-size:12px;border:1px solid var(--bd);border-radius:var(--r);outline:none}
.form-inline input:focus{border-color:var(--p)}
.hr{border:none;border-top:1px solid var(--bd);margin:20px 0}
.flex-row{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
.flex-between{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
.mb-16{margin-bottom:16px}.mb-24{margin-bottom:24px}.mt-16{margin-top:16px}
@media(max-width:768px){
.sidebar{width:56px}.side-h span:last-child,.side-nav a span,.side-foot a span{display:none}
.side-h{justify-content:center;padding:14px 0}.side-nav a{justify-content:center;padding:12px 0}
.main{margin-left:56px}.content{padding:14px}.stats-grid{grid-template-columns:repeat(2,1fr)}
.topbar{padding:12px 16px}.topbar h2{font-size:15px}
}
</style></head><body>';
    echo '<aside class="sidebar"><div class="side-h"><span class="s">&#128737;</span><span>森码云</span></div><nav class="side-nav">';
    foreach ($nav as $k => $v) {
        $cls = ($active === $k) ? ' active' : '';
        echo "<a href=\"/admin/{$k}.php\" class=\"{$cls}\">{$v['icon']}<span>{$v['label']}</span></a>";
    }
    echo '</nav><div class="side-foot"><a href="/">&#8962; <span>返回首页</span></a><a href="/admin/logout.php">&#10149; <span>退出登录</span></a></div></aside>';
    echo "<main class=\"main\"><div class=\"topbar\"><h2>{$title}</h2><span class=\"user\">&#128100; {$username}</span></div><div class=\"content\">";
}

function admin_footer()
{
    echo '</div></main></body></html>';
}