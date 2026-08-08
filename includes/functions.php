<?php
/**
 * 通用函数库
 */

function json_success($data = [], $msg = '操作成功', $code = 200)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => $code, 'msg' => $msg, 'data' => $data, 'time' => time()], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error($msg = '操作失败', $code = 400, $data = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => $code, 'msg' => $msg, 'data' => $data, 'time' => time()], JSON_UNESCAPED_UNICODE);
    exit;
}

function get_post($key, $default = '')
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function get_param($key, $default = '')
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

function get_input()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return $data ?: [];
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function is_installed()
{
    return file_exists(SENMAO_ROOT . '/install.lock');
}

function is_logged_in()
{
    return isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0;
}

function require_login()
{
    if (!is_logged_in()) {
        if (is_ajax()) {
            json_error('请先登录', 401);
        }
        redirect('/admin/login.php');
    }
}

function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function get_client_ip()
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function get_site_url()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    return $protocol . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check()
{
    $token = get_post('csrf_token', $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function h($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function audit_log($action, $module = '', $targetType = '', $targetId = '', $content = '')
{
    try {
        db()->insert(db()->table('audit_log'), [
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'action' => $action,
            'module' => $module,
            'target_type' => $targetType,
            'target_id' => (string)$targetId,
            'content' => is_array($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : (string)$content,
            'ip_address' => get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
    } catch (\Throwable $e) {}
}