<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 入口文件
// | 域名: face.builds.codes
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
    // 未安装，跳转到安装向导
    $installUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://')
        . $_SERVER['HTTP_HOST']
        . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')
        . '/install/';
    header('Location: ' . $installUrl);
    exit;
}

// 加载基础文件
require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;
$response = $http->run();
$response->send();
$http->end($response);