<?php
/**
 * 管理后台路由
 */
$adminPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$adminPath = str_replace('/admin', '', $adminPath);
$adminPath = rtrim($adminPath, '/') ?: '/';

// 登录/退出不需要验证
$noAuth = ['/login.php', '/do_login.php', '/logout.php'];
if (!in_array($adminPath, $noAuth)) {
    require_login();
}

switch ($adminPath) {
    case '/':
    case '/index.php':
        redirect('/admin/dashboard.php');
        break;
    case '/login.php':
        require __DIR__ . '/login.php';
        break;
    case '/do_login.php':
        require __DIR__ . '/do_login.php';
        break;
    case '/logout.php':
        session_destroy();
        redirect('/admin/login.php');
        break;
    case '/dashboard.php':
        require __DIR__ . '/dashboard.php';
        break;
    case '/driver.php':
        require __DIR__ . '/driver.php';
        break;
    case '/record.php':
        require __DIR__ . '/record.php';
        break;
    case '/audit.php':
        require __DIR__ . '/audit.php';
        break;
    case '/token.php':
        require __DIR__ . '/token.php';
        break;
    case '/setting.php':
        require __DIR__ . '/setting.php';
        break;
    case '/plugin.php':
        require __DIR__ . '/plugin.php';
        break;
    default:
        http_response_code(404);
        echo '<h1>404 - 页面不存在</h1>';
}