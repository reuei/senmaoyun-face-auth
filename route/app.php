<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 路由配置
// +----------------------------------------------------------------------

use think\facade\Route;

// ─────────────── 公开页面 ───────────────
Route::get('/', 'index/index');
Route::get('/verify', 'index/index')->middleware(\app\middleware\VerifyToken::class);
Route::get('/forbidden', 'index/forbidden');

// ─────────────── API 接口 ───────────────
Route::group('api', function () {
    // 认证相关
    Route::post('auth/login', 'api.Auth/login');
    Route::post('auth/logout', 'api.Auth/logout');

    // 身份证校验
    Route::post('idcard/verify', 'api.Idcard/verify');

    // 人脸识别
    Route::post('face/init', 'api.Face/init');
    Route::post('face/upload', 'api.Face/upload');
    Route::post('face/action', 'api.Face/action');
    Route::post('face/result', 'api.Face/result');

    // Token 验证
    Route::post('token/verify', 'api.Token/verify');

    // 魔方财务回调
    Route::post('callback/mofang', 'api.Callback/mofang');
})->middleware(\app\middleware\ApiAuth::class);

// ─────────────── V1 API（兼容魔方财务） ───────────────
Route::group('api/v1', function () {
    Route::post('certify/init', 'api.v1.Certify/init');
    Route::post('certify/callback', 'api.v1.Certify/callback');
    Route::post('certify/status', 'api.v1.Certify/status');
});

// ─────────────── 管理后台 ───────────────
Route::group('admin', function () {
    Route::get('/', 'admin.Index/index');
    Route::get('login', 'admin.Auth/login');
    Route::post('login', 'admin.Auth/doLogin');
    Route::get('logout', 'admin.Auth/logout');

    Route::group('', function () {
        // 控制台
        Route::get('dashboard', 'admin.Dashboard/index');
        Route::get('dashboard/stats', 'admin.Dashboard/stats');

        // 接口管理
        Route::get('driver', 'admin.Driver/index');
        Route::get('driver/detail/:id', 'admin.Driver/detail');
        Route::post('driver/save', 'admin.Driver/save');
        Route::post('driver/test', 'admin.Driver/test');
        Route::post('driver/toggle', 'admin.Driver/toggle');

        // 认证记录
        Route::get('record', 'admin.Record/index');
        Route::get('record/detail/:id', 'admin.Record/detail');
        Route::post('record/export', 'admin.Record/export');

        // 人工审核
        Route::get('audit', 'admin.Audit/index');
        Route::get('audit/detail/:id', 'admin.Audit/detail');
        Route::post('audit/pass', 'admin.Audit/pass');
        Route::post('audit/reject', 'admin.Audit/reject');

        // Token 管理
        Route::get('token', 'admin.Token/index');
        Route::post('token/revoke', 'admin.Token/revoke');
        Route::get('token/log', 'admin.Token/log');

        // 系统设置
        Route::get('setting', 'admin.Setting/index');
        Route::post('setting/save', 'admin.Setting/save');

        // 插件中心
        Route::get('plugin', 'admin.Plugin/index');
        Route::get('plugin/download', 'admin.Plugin/download');
    })->middleware(\app\middleware\AdminAuth::class);
})->middleware(\app\middleware\AdminAuth::class);

// ─────────────── 安装向导 ───────────────
Route::get('install', 'install.Index/index');
Route::get('install/step/:step', 'install.Index/step');
Route::post('install/check', 'install.Index/check');
Route::post('install/setup', 'install.Index/setup');

// ─────────────── 静态资源 ───────────────
Route::get('static/:path', function ($path) {
    $file = public_path() . 'static/' . $path;
    if (file_exists($file)) {
        return download($file, basename($file));
    }
    return response('', 404);
})->pattern(['path' => '.*']);