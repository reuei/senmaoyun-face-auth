<?php
/**
 * API 路由处理器
 */
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// 速率限制检查
function rate_limit_check($action, $max = 10)
{
    try {
        $ip = get_client_ip();
        $db = db();
        $tbl = $db->table('rate_limit');
        $window = date('Y-m-d H:i:s', time() - 60);
        $db->query("DELETE FROM `{$tbl}` WHERE window_start < ?", [$window]);
        $row = $db->fetch("SELECT `count` FROM `{$tbl}` WHERE ip_address=? AND action=? AND window_start>=?", [$ip, $action, $window]);
        if ($row && $row['count'] >= $max) return false;
        if ($row) {
            $db->update($tbl, ['count' => $row['count'] + 1], 'ip_address=? AND action=?', [$ip, $action]);
        } else {
            $db->insert($tbl, ['ip_address' => $ip, 'action' => $action, 'count' => 1, 'window_start' => date('Y-m-d H:i:s')]);
        }
        return true;
    } catch (\Throwable $e) { return true; }
}

switch ($action) {
    // ─── 身份证校验 ───
    case 'idcard_verify':
        if (!rate_limit_check('idcard', 20)) json_error('请求过于频繁');
        $name = $input['name'] ?? '';
        $idCard = $input['id_card'] ?? '';
        if (empty($name) || mb_strlen($name) < 2) json_error('请输入有效姓名');
        if (empty($idCard)) json_error('请输入身份证号');
        $validator = new IdCardValidator();
        $result = $validator->verify($idCard);
        if (!$result['valid']) json_error($result['message']);
        json_success([
            'name' => $name,
            'gender' => $result['gender'],
            'gender_text' => $result['gender_text'],
            'birth_date' => $result['birth_date'],
            'age' => $result['age'],
        ], '身份证号校验通过');
        break;

    // ─── 初始化认证会话 ───
    case 'face_init':
        if (!rate_limit_check('face_init', 10)) json_error('请求过于频繁');
        $token = $input['token'] ?? '';
        $name = $input['name'] ?? '';
        $idCard = $input['id_card'] ?? '';
        if (empty($token) || empty($name) || empty($idCard)) json_error('参数不完整');
        $tokenRecord = db()->fetch(
            "SELECT * FROM " . db()->table('certify_token') . " WHERE token=? AND type='request' AND expire_time>NOW()", [$token]
        );
        if (!$tokenRecord) json_error('Token无效');
        $enc = new Encrypt();
        $recordNo = date('YmdHis') . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
        db()->insert(db()->table('certify_record'), [
            'record_no' => $recordNo,
            'user_id' => $tokenRecord['user_id'],
            'name' => $enc->encrypt($name),
            'id_card' => $enc->encrypt($idCard),
            'status' => 'processing',
            'ip_address' => get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
        json_success(['record_no' => $recordNo, 'session_id' => db()->getPdo()->lastInsertId()], '认证会话已创建');
        break;

    // ─── 活体检测动作 ───
    case 'face_action':
        if (!rate_limit_check('face_action', 30)) json_error('请求过于频繁');
        $recordNo = $input['record_no'] ?? '';
        $actionType = $input['action_type'] ?? '';
        $imageBase64 = $input['image'] ?? '';
        if (empty($recordNo) || empty($imageBase64)) json_error('参数不完整');
        $record = db()->fetch("SELECT * FROM " . db()->table('certify_record') . " WHERE record_no=?", [$recordNo]);
        if (!$record) json_error('记录不存在');
        // 保存图片
        $dir = SENMAO_ROOT . '/runtime/face/' . date('Ymd') . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageBase64));
        $imgName = $recordNo . '_' . $actionType . '_' . time() . '.jpg';
        file_put_contents($dir . $imgName, $imgData);
        db()->update(db()->table('certify_record'), [
            'face_image' => 'face/' . date('Ymd') . '/' . $imgName,
            'retry_count' => $record['retry_count'] + 1,
        ], 'record_no=?', [$recordNo]);
        json_success(['action' => $actionType, 'liveness_score' => 85, 'passed' => true]);
        break;

    // ─── 最终结果 ───
    case 'face_result':
        if (!rate_limit_check('face_result', 10)) json_error('请求过于频繁');
        $recordNo = $input['record_no'] ?? '';
        $imageBase64 = $input['image'] ?? '';
        if (empty($recordNo)) json_error('缺少记录编号');
        $record = db()->fetch("SELECT * FROM " . db()->table('certify_record') . " WHERE record_no=?", [$recordNo]);
        if (!$record) json_error('记录不存在');
        // 执行自研活体检测
        require_once SENMAO_ROOT . '/includes/face/self.php';
        $driver = new SelfDriver();
        $result = $driver->detectLiveness($imageBase64);
        $score = $result['liveness_score'];
        $passed = $result['success'] && $score >= FACE_LIVENESS_THRESHOLD;
        $status = $passed ? 'success' : 'failed';
        $maxRetry = FACE_MAX_RETRY;
        if (!$passed && $record['retry_count'] >= $maxRetry) {
            $status = 'auditing';
        }
        $data = [
            'status' => $status,
            'liveness_score' => $score,
            'driver_code' => 'self',
            'certify_time' => $passed ? date('Y-m-d H:i:s') : null,
            'fail_reason' => $passed ? '' : ($result['message'] ?? '活体检测未通过'),
        ];
        db()->update(db()->table('certify_record'), $data, 'record_no=?', [$recordNo]);
        if ($passed) {
            // 生成回调Token
            $cbToken = hash('sha256', random_bytes(32) . microtime(true) . $record['user_id']);
            db()->insert(db()->table('certify_token'), [
                'token' => $cbToken, 'type' => 'callback', 'user_id' => $record['user_id'],
                'expire_time' => date('Y-m-d H:i:s', time() + 600), 'used' => 0, 'record_id' => $record['id'],
            ]);
            json_success(['status' => 'success', 'callback_token' => $cbToken], '实人认证通过');
        }
        json_error($status === 'auditing' ? '认证次数已达上限，已转人工审核' : ($result['message'] ?? '活体检测未通过'));
        break;

    // ─── Token生成（魔方财务调用） ───
    case 'token_generate':
        $userId = $input['user_id'] ?? '';
        $callbackUrl = $input['callback_url'] ?? '';
        $apiKey = $input['api_key'] ?? '';
        if (empty($userId) || empty($callbackUrl)) json_error('参数不完整');
        $validKey = API_SECRET;
        if ($apiKey !== $validKey) json_error('API Key无效');
        $token = hash('sha256', random_bytes(32) . microtime(true) . $userId);
        db()->insert(db()->table('certify_token'), [
            'token' => $token, 'type' => 'request', 'user_id' => $userId,
            'callback_url' => $callbackUrl, 'expire_time' => date('Y-m-d H:i:s', time() + 300), 'used' => 0,
        ]);
        json_success([
            'token' => $token,
            'verify_url' => get_site_url() . '/verify?token=' . $token,
        ], 'Token生成成功');
        break;

    // ─── Token验证 ───
    case 'token_verify':
        $t = $input['token'] ?? '';
        if (empty($t)) json_error('Token不能为空');
        $row = db()->fetch("SELECT * FROM " . db()->table('certify_token') . " WHERE token=? AND expire_time>NOW() AND used=0", [$t]);
        json_success(['valid' => (bool)$row, 'user_id' => $row['user_id'] ?? ''], $row ? 'Token有效' : 'Token无效');
        break;

    default:
        json_error('未知API操作', 404);
}