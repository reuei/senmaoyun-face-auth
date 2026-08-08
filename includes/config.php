<?php
/**
 * 森码云实人认证系统 - 配置文件
 */
define('SENMAO_VERSION', '1.0.0');
define('SENMAO_ROOT', dirname(__DIR__));

// 从 .env 或 install.lock 读取配置
$configFile = SENMAO_ROOT . '/.env';
if (file_exists($configFile)) {
    $env = parse_ini_file($configFile, true, INI_SCANNER_RAW);
} else {
    $env = [];
}

// 数据库配置
define('DB_HOST', $env['DATABASE']['HOSTNAME'] ?? '127.0.0.1');
define('DB_PORT', $env['DATABASE']['HOSTPORT'] ?? '3306');
define('DB_NAME', $env['DATABASE']['DATABASE'] ?? 'senmaoyun');
define('DB_USER', $env['DATABASE']['USERNAME'] ?? 'root');
define('DB_PASS', $env['DATABASE']['PASSWORD'] ?? '');
define('DB_PREFIX', $env['DATABASE']['PREFIX'] ?? 'smy_');
define('DB_CHARSET', 'utf8mb4');

// 站点配置
define('SITE_NAME', $env['APP']['SITE_NAME'] ?? '森码云实人认证系统');
define('SITE_DOMAIN', $env['APP']['SITE_DOMAIN'] ?? 'face.builds.codes');
define('APP_DEBUG', ($env['APP']['APP_DEBUG'] ?? 'false') === 'true');
define('API_SECRET', $env['APP']['API_SECRET'] ?? '');
define('MOFANG_URL', $env['APP']['MOFANG_URL'] ?? '');

// 人脸识别配置
define('FACE_DEFAULT_DRIVER', $env['FACE']['DEFAULT_DRIVER'] ?? 'self');
define('FACE_LIVENESS_THRESHOLD', (int)($env['FACE']['LIVENESS_THRESHOLD'] ?? 80));
define('FACE_MAX_RETRY', (int)($env['FACE']['MAX_RETRY'] ?? 3));
define('FACE_RATE_LIMIT', (int)($env['FACE']['RATE_LIMIT'] ?? 10));
define('FACE_DATA_RETENTION', (int)($env['FACE']['DATA_RETENTION'] ?? 24));
define('FACE_ENCRYPTION_KEY', $env['FACE']['ENCRYPTION_KEY'] ?? '');

// 时区
date_default_timezone_set('Asia/Shanghai');

// 错误报告
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 会话
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}