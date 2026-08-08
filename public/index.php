<?php
/**
 * 森码云实人认证系统 - 入口路由
 * 零依赖，纯PHP，直接上传即可运行
 */
define('PUBLIC_PATH', __DIR__);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/encrypt.php';
require_once __DIR__ . '/../includes/idcard.php';

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = rtrim($path, '/') ?: '/';

// ── 安装检测 ──
if (!is_installed() && $path !== '/install.php') {
    redirect('/install.php');
}

// ── 路由分发 ──
switch (true) {
    // 安装向导
    case $path === '/install.php':
        require __DIR__ . '/install.php';
        break;

    // 目录绑定提示
    case $path === '/bind-public.php':
        require __DIR__ . '/bind-public.php';
        break;

    // API 接口
    case strpos($path, '/api/') === 0:
        require __DIR__ . '/api/index.php';
        break;

    // 管理后台
    case strpos($path, '/admin') === 0:
        require __DIR__ . '/admin/index.php';
        break;

    // 认证页面（受Token保护）
    case $path === '/verify':
        require __DIR__ . '/verify.php';
        break;

    // 禁止访问页
    case $path === '/forbidden':
        require __DIR__ . '/forbidden.php';
        break;

    // 首页
    default:
        require __DIR__ . '/home.php';
        break;
}