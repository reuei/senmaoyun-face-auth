<?php
return [
    'app_debug'        => env('app.app_debug', false),
    'app_trace'        => env('app.app_trace', false),
    'default_timezone' => 'Asia/Shanghai',
    'default_lang'     => 'zh-cn',
    'default_filter'   => 'htmlspecialchars',
    'auto_multi_app'   => true,
    'error_message'    => '页面错误，请稍后再试',
    'show_error_msg'   => false,
    'site_name'        => env('app.site_name', '森码云实人认证系统'),
    'site_domain'      => env('app.site_domain', 'face.builds.codes'),
];