<?php
// 森码云实人认证系统 - ThinkPHP 6 入口
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    header('Content-Type: text/html; charset=utf-8');
    die('系统要求 PHP >= 8.1.0，当前版本: ' . PHP_VERSION);
}

// 安装检测
$installLock = __DIR__ . '/../install.lock';
if (!file_exists($installLock)) {
    $installUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://')
        . $_SERVER['HTTP_HOST'] . '/install';
    header('Location: ' . $installUrl);
    exit;
}

// 加载Composer自动加载
require __DIR__ . '/../vendor/autoload.php';

// 执行应用
$http = (new think\App())->http;
$response = $http->run();
$response->send();
$http->end($response);