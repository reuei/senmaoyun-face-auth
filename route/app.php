<?php
use think\facade\Route;

// 首页
Route::get('/', 'home.Index/index');

// 安装向导
Route::get('install', 'home.Install/index');
Route::get('install/check', 'home.Install/check');
Route::post('install/setup', 'home.Install/setup');

// 认证页（受Token保护）
Route::get('verify', 'home.Verify/index')->middleware(\app\middleware\VerifyToken::class);

// 禁止访问
Route::get('forbidden', 'home.Error/forbidden');

// API接口
Route::group('api', function () {
    Route::post('idcard/verify', 'api.Idcard/verify');
    Route::post('face/init', 'api.Face/init');
    Route::post('face/action', 'api.Face/action');
    Route::post('face/result', 'api.Face/result');
    Route::post('token/generate', 'api.Token/generate');
    Route::post('token/verify', 'api.Token/verify');
    Route::post('callback/mofang', 'api.Callback/mofang');
    Route::get('admin/stats', 'api.Admin/stats');
    Route::post('admin/records', 'api.Admin/records');
    Route::get('export/csv', 'api.Admin/exportCsv');
});

// 用户中心
Route::group('user', function () {
    Route::get('register', 'user.Auth/register');
    Route::post('register', 'user.Auth/doRegister');
    Route::get('login', 'user.Auth/login');
    Route::post('login', 'user.Auth/doLogin');
    Route::get('logout', 'user.Auth/logout');
    Route::get('center', 'user.Auth/center');
    Route::get('status', 'user.Auth/certifyStatus');
    Route::post('updateCertify', 'user.Auth/updateCertify');
});
Route::group('admin', function () {
    Route::get('login', 'admin.Auth/login');
    Route::post('login', 'admin.Auth/doLogin');
    Route::get('logout', 'admin.Auth/logout');
    Route::get('dashboard', 'admin.Dashboard/index');
    Route::get('driver', 'admin.Driver/index');
    Route::post('driver/save', 'admin.Driver/save');
    Route::get('record', 'admin.Record/index');
    Route::get('audit', 'admin.Audit/index');
    Route::post('audit/handle', 'admin.Audit/handle');
    Route::get('token', 'admin.Token/index');
    Route::post('token/revoke', 'admin.Token/revoke');
    Route::get('users', 'admin.UserManage/index');
    Route::post('users/list', 'admin.UserManage/list');
    Route::post('users/toggle', 'admin.UserManage/toggle');
    Route::get('setting', 'admin.Setting/index');
    Route::post('setting/save', 'admin.Setting/save');
    Route::get('plugin', 'admin.Plugin/index');
});