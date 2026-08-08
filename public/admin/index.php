<?php
/**
 * 管理后台路由 - 纯PHP模式
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

$adminPath = str_replace('/admin', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$adminPath = rtrim($adminPath, '/') ?: '/';

// 登录验证
$noAuth = ['/login', '/logout'];
if (!in_array($adminPath, $noAuth)) {
    session_start();
    if (empty($_SESSION['admin_id'])) {
        if (is_ajax()) { json_error('请先登录', 401); }
        header('Location: /admin/login'); exit;
    }
}

switch ($adminPath) {
    case '/':
    case '/dashboard':
        require __DIR__ . '/dashboard.php';
        break;
    case '/login':
        require __DIR__ . '/login.php';
        break;
    case '/logout':
        session_start(); session_destroy();
        header('Location: /admin/login'); exit;
    case '/driver':
        require __DIR__ . '/driver.php';
        break;
    case '/record':
        require __DIR__ . '/record.php';
        break;
    case '/audit':
        require __DIR__ . '/audit.php';
        break;
    case '/token':
        require __DIR__ . '/token.php';
        break;
    case '/users':
        require __DIR__ . '/users.php';
        break;
    case '/setting':
        require __DIR__ . '/setting.php';
        break;
    case '/plugin':
        require __DIR__ . '/plugin.php';
        break;
    default:
        http_response_code(404);
        echo '<h1>404</h1>'; exit;
}