<?php
/**
 * 森码云实人认证系统 v2.0.1
 * 支持 ThinkPHP 6 和 纯PHP 两种模式
 */

// 检测PHP版本
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    header('Content-Type: text/html; charset=utf-8');
    die('系统要求 PHP >= 8.1.0，当前版本: ' . PHP_VERSION);
}

// 安装检测
$installLock = __DIR__ . '/../install.lock';
if (!file_exists($installLock)) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    header('Location: ' . $protocol . $_SERVER['HTTP_HOST'] . '/install');
    exit;
}

// ── 路由 ──
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

// 尝试加载ThinkPHP（如果vendor存在）
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require $vendorAutoload;
    $http = (new think\App())->http;
    $response = $http->run();
    $response->send();
    $http->end($response);
    exit;
}

// ── 纯PHP模式（无vendor时使用） ──
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/encrypt.php';
require_once __DIR__ . '/../includes/idcard.php';

// 简单路由分发
switch (true) {
    case $uri === '/install':
        require __DIR__ . '/install.php';
        break;

    case strpos($uri, '/api/') === 0:
        require __DIR__ . '/api/index.php';
        break;

    case strpos($uri, '/admin/') === 0:
        require __DIR__ . '/admin/index.php';
        break;

    case $uri === '/verify':
        // 用户中心入口：允许已登录用户直接访问
        require __DIR__ . '/verify.php';
        break;

    case $uri === '/forbidden':
        require __DIR__ . '/forbidden.php';
        break;

    case $uri === '/user/login':
        require __DIR__ . '/user/login.php';
        break;

    case $uri === '/user/register':
        require __DIR__ . '/user/register.php';
        break;

    case $uri === '/user/center':
        require __DIR__ . '/user/center.php';
        break;

    case $uri === '/user/logout':
        session_start();
        session_destroy();
        header('Location: /');
        exit;

    case $uri === '/agreement':
        require __DIR__ . '/agreement.php';
        break;

    case $uri === '/privacy':
        require __DIR__ . '/privacy.php';
        break;

    default:
        require __DIR__ . '/home.php';
}