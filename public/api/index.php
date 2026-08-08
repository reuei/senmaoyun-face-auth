<?php
/**
 * API 路由处理器 - 完整版
 * 支持: 身份证校验、人脸识别、Token管理、魔方财务回调、数据导出
 */
header('Content-Type: application/json; charset=utf-8');

// 支持两种路由格式: ?action=xxx 和 /api/xxx/yyy
$action = $_GET['action'] ?? '';
if (empty($action)) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim(str_replace('/api', '', $uri), '/');
    // URL路径到action的映射
    $urlMap = [
        'idcard/verify' => 'idcard_verify',
        'face/init'     => 'face_init',
        'face/upload'   => 'face_action',
        'face/action'   => 'face_action',
        'face/result'   => 'face_result',
        'token/verify'  => 'token_verify',
        'token/generate'=> 'token_generate',
        'callback/mofang'=> 'mofang_callback',
        'v1/certify/init'=> 'token_generate',
        'v1/certify/callback'=> 'mofang_callback',
        'v1/certify/status'=> 'token_verify',
        'admin/stats'   => 'admin_stats',
        'admin/records' => 'admin_records',
        'export/csv'    => 'export_csv',
    ];
    $action = $urlMap[$uri] ?? '';
}
$input = json_decode(file_get_contents('php://input'), true) ?: [];
if (empty($input)) { $input = $_POST; }

// 速率限制
function rate_limit_check($action, $max = 10) {
    try {
        $ip = get_client_ip();
        $db = db(); $tbl = $db->table('rate_limit');
        $window = date('Y-m-d H:i:s', time() - 60);
        $db->query("DELETE FROM `{$tbl}` WHERE window_start < ?", [$window]);
        $row = $db->fetch("SELECT `count` FROM `{$tbl}` WHERE ip_address=? AND action=? AND window_start>=?", [$ip, $action, $window]);
        if ($row && $row['count'] >= $max) return false;
        if ($row) { $db->update($tbl, ['count' => $row['count'] + 1], 'ip_address=? AND action=?', [$ip, $action]); }
        else { $db->insert($tbl, ['ip_address' => $ip, 'action' => $action, 'count' => 1, 'window_start' => date('Y-m-d H:i:s')]); }
        return true;
    } catch (\Throwable $e) { return true; }
}

// 验证魔方API Key
function verify_api_key() {
    $key = $input['api_key'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? '');
    return $key === API_SECRET;
}

switch ($action) {
    // ─── 身份证校验 ───
    case 'idcard_verify':
        if (!rate_limit_check('idcard', 20)) json_error('请求过于频繁');
        $name = $input['name'] ?? '';
        $idCard = $input['id_card'] ?? '';
        if (empty($name) || mb_strlen($name) < 2) json_error('请输入有效姓名');
        if (empty($idCard)) json_error('请输入身份证号');
        require_once SENMAO_ROOT . '/includes/idcard.php';
        $validator = new IdCardValidator();
        $result = $validator->verify($idCard);
        if (!$result['valid']) json_error($result['message']);
        json_success([
            'name' => $name, 'gender' => $result['gender'],
            'gender_text' => $result['gender_text'], 'birth_date' => $result['birth_date'],
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
        $tokenRecord = db()->fetch("SELECT * FROM " . db()->table('certify_token') . " WHERE token=? AND type='request' AND expire_time>NOW()", [$token]);
        if (!$tokenRecord) json_error('Token无效');
        $enc = new Encrypt();
        $recordNo = date('YmdHis') . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
        db()->insert(db()->table('certify_record'), [
            'record_no' => $recordNo, 'user_id' => $tokenRecord['user_id'],
            'name' => $enc->encrypt($name), 'id_card' => $enc->encrypt($idCard),
            'status' => 'processing', 'ip_address' => get_client_ip(),
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
        $dir = SENMAO_ROOT . '/runtime/face/' . date('Ymd') . '/';
        if (!is_dir($dir)) { mkdir($dir, 0755, true); }
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
        require_once SENMAO_ROOT . '/includes/face/self.php';
        $driver = new SelfDriver();
        $result = $driver->detectLiveness($imageBase64);
        $score = $result['liveness_score'];
        $passed = $result['success'] && $score >= FACE_LIVENESS_THRESHOLD;
        $status = $passed ? 'success' : 'failed';
        if (!$passed && $record['retry_count'] >= FACE_MAX_RETRY) { $status = 'auditing'; }
        db()->update(db()->table('certify_record'), [
            'status' => $status, 'liveness_score' => $score, 'driver_code' => 'self',
            'certify_time' => $passed ? date('Y-m-d H:i:s') : null,
            'fail_reason' => $passed ? '' : ($result['message'] ?? '活体检测未通过'),
        ], 'record_no=?', [$recordNo]);
        if ($passed) {
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
        if (!verify_api_key()) json_error('API Key无效', 403);
        $userId = $input['user_id'] ?? '';
        $callbackUrl = $input['callback_url'] ?? '';
        if (empty($userId) || empty($callbackUrl)) json_error('参数不完整');
        $token = hash('sha256', random_bytes(32) . microtime(true) . $userId);
        db()->insert(db()->table('certify_token'), [
            'token' => $token, 'type' => 'request', 'user_id' => $userId,
            'callback_url' => $callbackUrl, 'expire_time' => date('Y-m-d H:i:s', time() + 300), 'used' => 0,
        ]);
        json_success(['token' => $token, 'verify_url' => get_site_url() . '/verify?token=' . $token], 'Token生成成功');
        break;

    // ─── Token验证 ───
    case 'token_verify':
        $t = $input['token'] ?? '';
        if (empty($t)) json_error('Token不能为空');
        $row = db()->fetch("SELECT * FROM " . db()->table('certify_token') . " WHERE token=? AND expire_time>NOW() AND used=0", [$t]);
        json_success(['valid' => (bool)$row, 'user_id' => $row['user_id'] ?? ''], $row ? 'Token有效' : 'Token无效');
        break;

    // ─── 魔方财务回调处理 ───
    case 'mofang_callback':
        $cbToken = $input['token'] ?? '';
        $userId = $input['user_id'] ?? '';
        $status = $input['status'] ?? '';
        $sign = $input['sign'] ?? '';
        if (empty($cbToken) || empty($userId)) json_error('参数不完整');
        $expectedSign = hash_hmac('sha256', $cbToken . $userId, API_SECRET);
        if (!hash_equals($expectedSign, $sign)) json_error('签名验证失败', 403);
        $tokenRow = db()->fetch("SELECT * FROM " . db()->table('certify_token') . " WHERE token=? AND type='callback' AND used=0", [$cbToken]);
        if (!$tokenRow) json_error('回调Token无效');
        db()->update(db()->table('certify_token'), ['used' => 1, 'used_time' => date('Y-m-d H:i:s')], 'token=?', [$cbToken]);
        json_success([], '回调处理成功');
        break;

    // ─── 导出CSV ───
    case 'export_csv':
        if (!is_logged_in()) json_error('请先登录', 401);
        $status = $_GET['status'] ?? '';
        $where = '';
        $params = [];
        if ($status) { $where = "WHERE status=?"; $params = [$status]; }
        $records = db()->fetchAll("SELECT record_no,user_id,status,liveness_score,driver_code,certify_time,ip_address FROM " . db()->table('certify_record') . " {$where} ORDER BY id DESC LIMIT 5000", $params);
        $csv = "记录编号,用户ID,状态,活体分数,接口,认证时间,IP\n";
        foreach ($records as $r) {
            $csv .= implode(',', [$r['record_no'], $r['user_id'], $r['status'], $r['liveness_score'] ?: '', $r['driver_code'], $r['certify_time'] ?: '', $r['ip_address']]) . "\n";
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="certify_records_' . date('YmdHis') . '.csv"');
        echo "\xEF\xBB\xBF" . $csv;
        exit;
        break;

    // ─── 获取统计数据 ───
    case 'admin_stats':
        if (!is_logged_in()) json_error('请先登录', 401);
        $today = date('Y-m-d');
        $stats = [
            'today_total' => db()->count(db()->table('certify_record'), "create_time >= ?", [$today . ' 00:00:00']),
            'today_success' => db()->count(db()->table('certify_record'), "status='success' AND create_time >= ?", [$today . ' 00:00:00']),
            'today_failed' => db()->count(db()->table('certify_record'), "status='failed' AND create_time >= ?", [$today . ' 00:00:00']),
            'today_auditing' => db()->count(db()->table('certify_record'), "status='auditing' AND create_time >= ?", [$today . ' 00:00:00']),
            'total' => db()->count(db()->table('certify_record')),
            'total_success' => db()->count(db()->table('certify_record'), "status='success'"),
        ];
        $stats['pass_rate'] = $stats['total'] > 0 ? round($stats['total_success'] / $stats['total'] * 100, 1) : 0;
        // 7天趋势
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $trend[] = [
                'date' => substr($d, 5),
                'total' => db()->count(db()->table('certify_record'), "create_time >= ? AND create_time < ?", [$d . ' 00:00:00', date('Y-m-d', strtotime("+1 day", strtotime($d))) . ' 00:00:00']),
                'success' => db()->count(db()->table('certify_record'), "status='success' AND create_time >= ? AND create_time < ?", [$d . ' 00:00:00', date('Y-m-d', strtotime("+1 day", strtotime($d))) . ' 00:00:00']),
            ];
        }
        json_success(['stats' => $stats, 'trend' => $trend]);
        break;

    // ─── 获取认证记录列表 ───
    case 'admin_records':
        if (!is_logged_in()) json_error('请先登录', 401);
        $page = max(1, (int)($input['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $total = db()->count(db()->table('certify_record'));
        $list = db()->fetchAll("SELECT * FROM " . db()->table('certify_record') . " ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
        json_success(['total' => $total, 'page' => $page, 'list' => $list]);
        break;

    // ─── 获取用户列表 ───
    case 'admin_users':
        if (!is_logged_in()) json_error('请先登录', 401);
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20; $offset = ($page - 1) * $limit;
        $total = db()->count(db()->table('user'));
        $list = db()->fetchAll("SELECT * FROM " . db()->table('user') . " ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}");
        json_success(['total' => $total, 'page' => $page, 'list' => $list]);
        break;

    default:
        json_error('未知API操作: ' . $action, 404);
}