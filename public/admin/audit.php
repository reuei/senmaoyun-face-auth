<?php
require_once __DIR__ . '/layout.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['act'] ?? '';
    $record = db()->fetch("SELECT * FROM " . db()->table('certify_record') . " WHERE id=?", [$id]);
    if ($record && $record['status'] === 'auditing') {
        if ($act === 'pass') {
            db()->update(db()->table('certify_record'), ['status' => 'success', 'certify_time' => date('Y-m-d H:i:s')], 'id=?', [$id]);
            audit_log('audit_pass', 'audit', 'record', $id);
            $msg = '<div class="msg-ok">审核通过</div>';
        } elseif ($act === 'reject') {
            $reason = trim($_POST['reason'] ?? '人工审核未通过');
            db()->update(db()->table('certify_record'), ['status' => 'failed', 'fail_reason' => $reason], 'id=?', [$id]);
            audit_log('audit_reject', 'audit', 'record', $id);
            $msg = '<div class="msg-ok">已驳回</div>';
        }
    }
}

$records = db()->fetchAll("SELECT * FROM " . db()->table('certify_record') . " WHERE status='auditing' ORDER BY id DESC LIMIT 50");
admin_header('人工审核', 'audit');
echo $msg;
?>
<div class="card">
<?php if (empty($records)): ?>
<p style="text-align:center;padding:40px;color:var(--tm)">暂无待审核记录</p>
<?php else: ?>
<div class="table-wrap">
<table>
<thead><tr><th>编号</th><th>用户ID</th><th>活体分数</th><th>失败原因</th><th>时间</th><th>操作</th></tr></thead>
<tbody>
<?php foreach ($records as $r): ?>
<tr>
    <td style="font-family:monospace;font-size:11px"><?php echo h(substr($r['record_no'],0,14)); ?>...</td>
    <td><?php echo h($r['user_id']); ?></td>
    <td><?php echo $r['liveness_score'] ?: '-'; ?></td>
    <td style="font-size:12px;color:var(--e)"><?php echo h($r['fail_reason']); ?></td>
    <td style="font-size:12px"><?php echo h($r['create_time']); ?></td>
    <td>
        <form method="post" style="display:flex;gap:6px">
            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <input type="hidden" name="act" value="pass">
            <button type="submit" class="btn btn-s" style="color:var(--s)">通过</button>
        </form>
        <form method="post" style="display:flex;gap:6px;margin-top:4px">
            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <input type="hidden" name="act" value="reject">
            <input type="text" name="reason" placeholder="驳回原因" style="width:100px;padding:4px 8px;font-size:11px;border:1px solid var(--bd);border-radius:var(--r)">
            <button type="submit" class="btn btn-e">驳回</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>
<?php admin_footer(); ?>