<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 [完整版] - 入口文件
// | ThinkPHP 6 框架入口
// +----------------------------------------------------------------------
namespace think;

// 检测PHP版本
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    header('Content-Type: text/html; charset=utf-8');
    die('系统要求 PHP >= 8.1.0，当前版本: ' . PHP_VERSION);
}

// 检测是否已安装
$installLockFile = __DIR__ . '/../install.lock';
if (!file_exists($installLockFile)) {
    header('Location: /install/');
    exit;
}

// 加载Composer自动加载
require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用
$http = (new App())->http;
$response = $http->run();
$response->send();
$http->end($response);