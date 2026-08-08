<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 应用配置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host'              => env('app.app_host', ''),
    // 应用调试模式
    'app_debug'             => env('app.app_debug', false),
    // 应用Trace
    'app_trace'             => env('app.app_trace', false),
    // 应用命名空间
    'app_namespace'         => 'app',
    // 默认时区
    'default_timezone'      => env('app.default_timezone', 'Asia/Shanghai'),
    // 默认语言
    'default_lang'          => 'zh-cn',
    // 默认全局过滤方法
    'default_filter'        => 'htmlspecialchars',
    // 开启多应用模式
    'auto_multi_app'        => true,
    // 错误显示信息
    'error_message'         => '页面错误，请稍后再试',
    // 显示错误信息
    'show_error_msg'        => false,
    // 异常处理类
    'exception_handle'      => '',
    // 站点名称
    'site_name'             => env('app.site_name', '森码云实人认证系统'),
    // 站点域名
    'site_domain'           => env('app.site_domain', 'face.builds.codes'),
    // 备案号
    'icp_number'            => env('app.icp_number', ''),
    // 魔方财务系统地址
    'mofang_url'            => env('app.mofang_url', ''),
];