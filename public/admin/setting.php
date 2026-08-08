<?php
require_once __DIR__ . '/layout.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'set_') === 0) {
            $key = substr($k, 4);
            try {
                $existing = db()->fetch("SELECT * FROM " . db()->table('setting') . " WHERE `key`=?", [$key]);
                if ($existing) {
                    db()->update(db()->table('setting'), ['value' => $v], '`key`=?', [$key]);
                } else {
                    db()->insert(db()->table('setting'), ['key' => $key, 'value' => $v, 'type' => 'string', 'group' => 'system']);
                }
            } catch (\Throwable $e) {}
        }
    }
    audit_log('setting_save', 'system');
    $msg = '<div class="msg-ok">配置保存成功</div>';
}

// 获取当前配置
$settings = [];
$rows = db()->fetchAll("SELECT * FROM " . db()->table('setting'));
foreach ($rows as $r) { $settings[$r['key']] = $r['value']; }

admin_header('系统设置', 'setting');
echo $msg;
?>
<div class="card" style="max-width:600px">
<form method="post">
<div class="fg"><label>站点名称</label><input name="set_site_name" value="<?php echo h($settings['site_name'] ?? SITE_NAME); ?>"></div>
<div class="fg"><label>站点域名</label><input name="set_site_domain" value="<?php echo h($settings['site_domain'] ?? 'face.builds.codes'); ?>"></div>
<div class="fg"><label>魔方财务地址</label><input name="set_mofang_url" value="<?php echo h($settings['mofang_url'] ?? ''); ?>" placeholder="https://your-mofang.com"></div>
<div class="fg"><label>最大重试次数</label><input name="set_max_retry" type="number" value="<?php echo h($settings['max_retry'] ?? '3'); ?>"></div>
<div class="fg"><label>活体检测阈值</label><input name="set_liveness_threshold" type="number" value="<?php echo h($settings['liveness_threshold'] ?? '80'); ?>"></div>
<div class="fg"><label>数据保留时间(小时)</label><input name="set_data_retention" type="number" value="<?php echo h($settings['data_retention'] ?? '24'); ?>"></div>
<div class="fg"><label>速率限制(次/分钟)</label><input name="set_rate_limit" type="number" value="<?php echo h($settings['rate_limit'] ?? '10'); ?>"></div>
<button type="submit" class="btn btn-p">保存设置</button>
</form>
</div>
<?php admin_footer(); ?>