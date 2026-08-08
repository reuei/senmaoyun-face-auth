<?php
require_once __DIR__ . '/layout.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $code = $_POST['driver_code'] ?? '';
    $config = $_POST['config'] ?? [];
    $enabled = isset($_POST['enabled']) ? 1 : 0;
    $isDefault = isset($_POST['is_default']) ? 1 : 0;

    $existing = db()->fetch("SELECT * FROM " . db()->table('face_driver') . " WHERE driver_code=?", [$code]);
    $enc = new Encrypt();
    $encConfig = [];
    foreach ($config as $k => $v) {
        $encConfig[$k] = (!empty($v) && in_array($k, ['secret_id','secret_key','api_key','app_code','private_key','alipay_public_key'])) ? $enc->encrypt($v) : $v;
    }
    $cfgJson = json_encode($encConfig, JSON_UNESCAPED_UNICODE);
    if ($existing) {
        db()->update(db()->table('face_driver'), ['config' => $cfgJson, 'enabled' => $enabled, 'is_default' => $isDefault], 'driver_code=?', [$code]);
    } else {
        db()->insert(db()->table('face_driver'), ['driver_code' => $code, 'driver_name' => $code, 'config' => $cfgJson, 'enabled' => $enabled, 'is_default' => $isDefault]);
    }
    if ($isDefault) {
        db()->query("UPDATE " . db()->table('face_driver') . " SET is_default=0 WHERE driver_code!=?", [$code]);
    }
    audit_log('driver_save', 'face', 'driver', $code);
    $msg = '<div class="msg-ok">配置保存成功</div>';
}

$drivers = db()->fetchAll("SELECT * FROM " . db()->table('face_driver') . " ORDER BY sort ASC");
$driverNames = [
    'self' => '自研活体检测',
    'tencent' => '腾讯云慧眼',
    'baidu' => '百度智能云人脸识别',
    'alipay' => '支付宝活体检测',
    'juhe' => '聚合数据活体检测',
    'aliyun_market' => '阿里云市场活体检测',
];
$configFields = [
    'self' => [],
    'tencent' => ['secret_id' => 'SecretId', 'secret_key' => 'SecretKey', 'region' => '地域'],
    'baidu' => ['api_key' => 'API Key', 'secret_key' => 'Secret Key', 'app_id' => 'App ID'],
    'alipay' => ['app_id' => 'AppId', 'private_key' => '应用私钥', 'alipay_public_key' => '支付宝公钥'],
    'juhe' => ['api_key' => 'API Key'],
    'aliyun_market' => ['app_code' => 'AppCode'],
];
$driverDescs = [
    'self' => '默认启用，无需配置。基于GD库实现图像质量检测、亮度分析、纹理复杂度分析（翻拍检测）。',
    'tencent' => '需开通腾讯云人脸核身服务。支持动作活体+光线活体+远近活体。',
    'baidu' => '需开通百度智能云人脸识别服务。支持liveness_control参数LOW/NORMAL/HIGH。',
    'alipay' => '需开通支付宝开放平台活体检测。支持initialize+query两阶段调用。',
    'juhe' => '需注册聚合数据。在线图片活体检测V2接口。',
    'aliyun_market' => '需在阿里云市场订阅活体检测+人像比对服务。',
];

admin_header('接口管理', 'driver');
echo $msg;
?>
<div class="card" style="margin-bottom:16px">
<p style="color:var(--ts);font-size:13px;margin-bottom:20px">管理人脸识别接口驱动。自研接口默认启用，第三方接口需配置密钥后方可使用。主接口失败时自动降级到备用接口。</p>

<?php foreach ($driverNames as $code => $name):
    $row = null;
    foreach ($drivers as $d) { if ($d['driver_code'] === $code) { $row = $d; break; } }
    $rowConfig = $row ? json_decode($row['config'], true) ?: [] : [];
    $rowEnabled = $row ? $row['enabled'] : ($code === 'self' ? 1 : 0);
    $rowDefault = $row ? $row['is_default'] : ($code === 'self' ? 1 : 0);
?>
<div style="border:1px solid var(--bd);border-radius:var(--rl);padding:20px;margin-bottom:14px;background:var(--bw)">
    <div class="flex-between mb-16">
        <div>
            <strong style="font-size:15px"><?php echo h($name); ?></strong>
            <span style="font-size:11px;color:var(--tm);font-family:monospace;margin-left:8px"><?php echo $code; ?></span>
        </div>
        <div style="display:flex;gap:6px">
            <span class="badge <?php echo $rowEnabled?'badge-s':'badge-e'; ?>"><?php echo $rowEnabled?'已启用':'已禁用'; ?></span>
            <?php if($rowDefault): ?><span class="badge badge-i">默认接口</span><?php endif; ?>
        </div>
    </div>
    <p style="font-size:12px;color:var(--ts);margin-bottom:14px"><?php echo $driverDescs[$code] ?? ''; ?></p>
    <?php if (!empty($configFields[$code])): ?>
    <form method="post" style="border-top:1px solid var(--bd);padding-top:14px">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="driver_code" value="<?php echo $code; ?>">
        <?php foreach ($configFields[$code] as $field => $label): ?>
        <div class="fg"><label><?php echo $label; ?></label>
            <input type="password" name="config[<?php echo $field; ?>]" value="<?php echo h($rowConfig[$field] ?? ''); ?>" placeholder="请输入<?php echo $label; ?>">
        </div>
        <?php endforeach; ?>
        <div class="flex-row">
            <label style="font-size:12px;display:flex;align-items:center;gap:4px;cursor:pointer">
                <input type="checkbox" name="enabled" <?php echo $rowEnabled?'checked':''; ?>> 启用此接口
            </label>
            <label style="font-size:12px;display:flex;align-items:center;gap:4px;cursor:pointer">
                <input type="checkbox" name="is_default" <?php echo $rowDefault?'checked':''; ?>> 设为默认
            </label>
            <button type="submit" class="btn btn-p btn-sm">保存配置</button>
        </div>
    </form>
    <?php else: ?>
    <div style="border-top:1px solid var(--bd);padding-top:14px">
        <p style="font-size:12px;color:var(--s);font-weight:500">此接口无需配置，始终可用</p>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</div>
<?php admin_footer(); ?>