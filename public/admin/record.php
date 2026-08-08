<?php
require_once __DIR__ . '/layout.php';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$records = db()->fetchAll("SELECT * FROM " . db()->table('certify_record') . " ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
$total = db()->count(db()->table('certify_record'));
$statusMap = ['success'=>'<span class="badge badge-s">通过</span>','failed'=>'<span class="badge badge-e">失败</span>','auditing'=>'<span class="badge badge-w">审核中</span>','pending'=>'<span class="badge badge-i">待处理</span>','processing'=>'<span class="badge badge-i">处理中</span>'];

admin_header('认证记录', 'record');
?>
<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <span style="font-size:13px;color:var(--ts)">共 <?php echo $total; ?> 条记录</span>
    <a href="/api/?action=export_csv" class="btn btn-s">导出CSV</a>
</div>
<div class="table-wrap">
<table>
<thead><tr><th>记录编号</th><th>用户ID</th><th>状态</th><th>活体分数</th><th>接口</th><th>认证时间</th><th>IP</th></tr></thead>
<tbody>
<?php foreach ($records as $r): ?>
<tr>
    <td style="font-family:monospace;font-size:11px"><?php echo h(substr($r['record_no'], 0, 16)); ?>...</td>
    <td><?php echo h($r['user_id']); ?></td>
    <td><?php echo $statusMap[$r['status']] ?? $r['status']; ?></td>
    <td><?php echo $r['liveness_score'] ?: '-'; ?></td>
    <td><?php echo h($r['driver_code']); ?></td>
    <td style="font-size:12px"><?php echo $r['certify_time'] ?: '-'; ?></td>
    <td style="font-size:12px"><?php echo h($r['ip_address']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php if ($total > $limit): ?>
<div style="margin-top:14px;text-align:center">
    <?php if ($page > 1): ?><a href="?page=<?php echo $page-1; ?>" class="btn btn-s">&laquo; 上一页</a><?php endif; ?>
    <span style="margin:0 12px;font-size:13px">第 <?php echo $page; ?> 页</span>
    <?php if ($page * $limit < $total): ?><a href="?page=<?php echo $page+1; ?>" class="btn btn-s">下一页 &raquo;</a><?php endif; ?>
</div>
<?php endif; ?>
</div>
<?php admin_footer(); ?>