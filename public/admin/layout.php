<?php
/**
 * 管理后台共享布局
 */
function admin_header($title, $active = 'dashboard')
{
    $nav = [
        'dashboard' => ['Dashboard', '控制台'],
        'driver' => ['Driver', '接口管理'],
        'record' => ['Record', '认证记录'],
        'audit' => ['Audit', '人工审核'],
        'token' => ['Token', 'Token管理'],
        'setting' => ['Setting', '系统设置'],
        'plugin' => ['Plugin', '插件中心'],
    ];
    $username = h($_SESSION['admin_username'] ?? '');
    echo '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">';
    echo '<title>' . h($title) . ' - ' . SITE_NAME . '</title>';
    echo '<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>';
    echo '<script src="https://unpkg.com/echarts@5/dist/echarts.min.js"></script>';
    echo '<script src="https://unpkg.com/axios/dist/axios.min.js"></script>';
    echo '<style>
:root{--p:#4F46E5;--ph:#4338CA;--pl:#EEF2FF;--s:#10B981;--e:#EF4444;--w:#F59E0B;--t:#1F2937;--ts:#6B7280;--tm:#9CA3AF;--bg:#F9FAFB;--bw:#FFF;--bd:#E5E7EB;--bl:#F3F4F6;--r:6px;--rl:10px;--rx:14px;--sh:0 1px 3px rgba(0,0,0,.06);--tr:.15s}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans SC",sans-serif;background:var(--bg);color:var(--t);display:flex;min-height:100vh}
.sidebar{width:220px;background:var(--bw);border-right:1px solid var(--bd);position:fixed;top:0;left:0;bottom:0;display:flex;flex-direction:column;z-index:50}
.side-h{padding:16px 18px;border-bottom:1px solid var(--bd);font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px}
.side-h span{color:var(--p)}
.side-nav{flex:1;padding:10px 0;overflow-y:auto}
.side-nav a{display:flex;align-items:center;gap:10px;padding:10px 18px;color:var(--ts);text-decoration:none;font-size:13px;border-left:3px solid transparent;transition:all var(--tr)}
.side-nav a:hover{color:var(--t);background:var(--bl)}
.side-nav a.active{color:var(--p);background:var(--pl);border-left-color:var(--p);font-weight:500}
.side-foot{border-top:1px solid var(--bd);padding:10px 0}
.side-foot a{padding:10px 18px;display:flex;align-items:center;gap:10px;color:var(--ts);text-decoration:none;font-size:13px}
.main{margin-left:220px;flex:1;min-height:100vh}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:14px 28px;background:var(--bw);border-bottom:1px solid var(--bd);position:sticky;top:0;z-index:40}
.topbar h2{font-size:16px;font-weight:600}
.topbar .user{font-size:13px;color:var(--ts)}
.content{padding:28px}
.card{background:var(--bw);border:1px solid var(--bd);border-radius:var(--rx);padding:22px;box-shadow:var(--sh)}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}
.stat-card{padding:18px 20px}
.stat-card .l{font-size:12px;color:var(--ts)}.stat-card .v{font-size:26px;font-weight:700;letter-spacing:-.02em;margin:4px 0}.stat-card .s{font-size:11px;color:var(--tm)}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:13px}
table th,table td{padding:10px 14px;text-align:left;border-bottom:1px solid var(--bd)}
table th{font-weight:600;color:var(--ts);background:var(--bg);font-size:12px}
table tr:hover td{background:var(--bl)}
.badge{display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:500}
.badge-s{background:#D1FAE5;color:#10B981}.badge-e{background:#FEE2E2;color:#EF4444}.badge-w{background:#FEF3C7;color:#F59E0B}.badge-i{background:#DBEAFE;color:#3B82F6}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--r);font-size:13px;font-weight:500;cursor:pointer;border:1px solid transparent;text-decoration:none;transition:all var(--tr)}
.btn-p{background:var(--p);color:#fff}.btn-p:hover{background:var(--ph)}
.btn-s{background:var(--bw);color:var(--t);border-color:var(--bd)}.btn-s:hover{background:var(--bl)}
.btn-e{background:var(--e);color:#fff}.btn-e:hover{background:#DC2626}
.fg{margin-bottom:14px}
.fg label{display:block;font-size:13px;font-weight:500;margin-bottom:4px}
.fg input,.fg select{width:100%;padding:9px 12px;border:1px solid var(--bd);border-radius:var(--r);font-size:13px;outline:none}
.fg input:focus,.fg select:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--pl)}
.msg-ok{background:#D1FAE5;color:var(--s);padding:10px 14px;border-radius:var(--r);font-size:13px;margin-bottom:14px}
.msg-err{background:#FEE2E2;color:var(--e);padding:10px 14px;border-radius:var(--r);font-size:13px;margin-bottom:14px}
@media(max-width:768px){.sidebar{width:56px}.sidebar .side-nav a span,.sidebar .side-foot a span,.side-h span:last-child{display:none}.main{margin-left:56px}.content{padding:14px}.stats-grid{grid-template-columns:repeat(2,1fr)}}
</style></head><body>';
    echo '<aside class="sidebar"><div class="side-h"><span>&#128737;</span><span>森码云</span></div><nav class="side-nav">';
    foreach ($nav as $k => $v) {
        $cls = ($active === $k) ? ' active' : '';
        echo "<a href=\"/admin/{$k}.php\" class=\"{$cls}\">{$v[0]}<span>{$v[1]}</span></a>";
    }
    echo '</nav><div class="side-foot"><a href="/">&#8962; <span>返回首页</span></a><a href="/admin/logout.php">&#10149; <span>退出</span></a></div></aside>';
    echo "<main class=\"main\"><div class=\"topbar\"><h2>{$title}</h2><span class=\"user\">{$username}</span></div><div class=\"content\">";
}

function admin_footer()
{
    echo '</div></main></body></html>';
}