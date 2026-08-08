<?php
require_once __DIR__ . '/layout.php';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$statusFilter = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($statusFilter) { $where = "WHERE status=?"; $params = [$statusFilter]; }
$total = db()->count(db()->table('certify_record'), $where ? substr($where, 6) : '1=1', $params);
$records = db()->fetchAll("SELECT * FROM " . db()->table('certify_record') . " {$where} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}", $params);
$statusMap = [
    'success' => '<span class="badge badge-s">通过</span>',
    'failed' => '<span class="badge badge-e">失败</span>',
    'auditing' => '<span class="badge badge-w">审核中</span>',
    'pending' => '<span class="badge badge-i">待处理</span>',
    'processing' => '<span class="badge badge-i">处理中</span>',
];

admin_header('认证记录', 'record');
?>
<div class="card">
<div class="card-h">
    <h3>认证记录</h3>
    <div class="flex-row">
        <a href="?<?php echo $statusFilter?'':'status=success'; ?>" class="btn btn-sm <?php echo $statusFilter==='success'?'btn-p':'btn-s'; ?>">通过</a>
        <a href="?<?php echo $statusFilter?'':'status=failed'; ?>" class="btn btn-sm <?php echo $statusFilter==='failed'?'btn-p':'btn-s'; ?>">失败</a>
        <a href="?<?php echo $statusFilter?'':'status=auditing'; ?>" class="btn btn-sm <?php echo $statusFilter==='auditing'?'btn-p':'btn-s'; ?>">审核中</a>
        <a href="?" class="btn btn-sm btn-s">全部</a>
        <a href="/api/?action=export_csv<?php echo $statusFilter?'&status='.$statusFilter:''; ?>" class="btn btn-sm btn-s">导出CSV</a>
    </div>
</div>
<div class="table-wrap">
<table>
<thead><tr><th>记录编号</th><th>用户ID</th><th>状态</th><th>活体分数</th><th>接口</th><th>认证时间</th><th>IP</th></tr></thead>
<tbody>
<?php if (empty($records)): ?>
<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--tm)">暂无认证记录</td></tr>
<?php else: ?>
<?php foreach ($records as $r): ?>
<tr>
    <td style="font-family:monospace;font-size:11px"><?php echo h(substr($r['record_no'], 0, 16)); ?>...</td>
    <td><?php echo h($r['user_id']); ?></td>
    <td><?php echo $statusMap[$r['status']] ?? $r['status']; ?></td>
    <td><?php echo $r['liveness_score'] ?: '-'; ?></td>
    <td><?php echo h($r['driver_code'] ?: '-'); ?></td>
    <td style="font-size:12px"><?php echo $r['certify_time'] ?: $r['create_time']; ?></td>
    <td style="font-size:12px"><?php echo h($r['ip_address']); ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
<?php if ($total > $limit): ?>
<div class="pagination">
    <?php if ($page > 1): $q = $statusFilter ? "&status={$statusFilter}" : ''; ?>
    <a href="?page=<?php echo $page-1 . $q; ?>" class="btn btn-sm btn-s">上一页</a>
    <?php endif; ?>
    <span>第 <?php echo $page; ?> 页 / 共 <?php echo ceil($total / $limit); ?> 页 (<?php echo $total; ?> 条)</span>
    <?php if ($page * $limit < $total): ?>
    <a href="?page=<?php echo $page+1 . $q; ?>" class="btn btn-sm btn-s">下一页</a>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>
<?php admin_footer(); ?>