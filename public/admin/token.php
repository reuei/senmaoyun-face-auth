<?php
require_once __DIR__ . '/layout.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $act = $_POST['act'] ?? '';
    if ($act === 'revoke' && $id > 0) {
        db()->update(db()->table('certify_token'), ['used' => 1], 'id=?', [$id]);
        audit_log('token_revoke', 'token', 'token', $id);
        $msg = '<div class="msg-ok">Token已强制失效</div>';
    }
}
$tokens = db()->fetchAll("SELECT * FROM " . db()->table('certify_token') . " WHERE used=0 AND expire_time > NOW() ORDER BY id DESC LIMIT 50");
admin_header('Token管理', 'token');
echo $msg;
?>
<div class="card">
<?php if (empty($tokens)): ?>
<p style="text-align:center;padding:40px;color:var(--tm)">暂无活跃Token</p>
<?php else: ?>
<div class="table-wrap">
<table>
<thead><tr><th>ID</th><th>Token</th><th>类型</th><th>用户ID</th><th>过期时间</th><th>操作</th></tr></thead>
<tbody>
<?php foreach ($tokens as $t): ?>
<tr>
    <td><?php echo $t['id']; ?></td>
    <td style="font-family:monospace;font-size:11px"><?php echo h(substr($t['token'],0,24)); ?>...</td>
    <td><span class="badge <?php echo $t['type']==='request'?'badge-i':'badge-s'; ?>"><?php echo $t['type']; ?></span></td>
    <td><?php echo h($t['user_id']); ?></td>
    <td style="font-size:12px"><?php echo h($t['expire_time']); ?></td>
    <td>
        <form method="post"><input type="hidden" name="id" value="<?php echo $t['id']; ?>"><input type="hidden" name="act" value="revoke"><button type="submit" class="btn btn-e">强制失效</button></form>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>
<?php admin_footer(); ?>