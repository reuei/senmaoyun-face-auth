<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 人脸识别配置
// +----------------------------------------------------------------------

return [
    // 默认主接口
    'default_driver'    => env('face.default_driver', 'self'),

    // 备用接口（主接口失败时自动降级）
    'fallback_driver'   => env('face.fallback_driver', 'tencent'),

    // 活体检测阈值（0-100）
    'liveness_threshold' => env('face.liveness_threshold', 80),

    // 人脸比对阈值（0-100）
    'compare_threshold'  => env('face.compare_threshold', 80),

    // 最大重试次数
    'max_retry'          => env('face.max_retry', 3),

    // 每分钟每IP最大请求数
    'rate_limit'         => env('face.rate_limit', 10),

    // 原始数据保留时间（小时），0 表示永久保留
    'data_retention'     => env('face.data_retention', 24),

    // 活体检测动作组合
    'actions'            => ['blink', 'open_mouth', 'nod_head', 'shake_head'],

    // 接口配置
    'drivers'            => [
        // 自研接口（默认启用，无需第三方密钥）
        'self' => [
            'name'       => '自研活体检测',
            'enabled'    => true,
            'class'      => \app\service\face\SelfDriver::class,
            'config'     => [],
        ],

        // 腾讯云慧眼
        'tencent' => [
            'name'       => '腾讯云慧眼',
            'enabled'    => false,
            'class'      => \app\service\face\TencentDriver::class,
            'config'     => [
                'secret_id'  => env('face.tencent.secret_id', ''),
                'secret_key' => env('face.tencent.secret_key', ''),
                'region'     => env('face.tencent.region', 'ap-guangzhou'),
            ],
        ],

        // 百度智能云
        'baidu' => [
            'name'       => '百度智能云人脸识别',
            'enabled'    => false,
            'class'      => \app\service\face\BaiduDriver::class,
            'config'     => [
                'api_key'    => env('face.baidu.api_key', ''),
                'secret_key' => env('face.baidu.secret_key', ''),
                'app_id'     => env('face.baidu.app_id', ''),
            ],
        ],

        // 支付宝
        'alipay' => [
            'name'       => '支付宝活体检测',
            'enabled'    => false,
            'class'      => \app\service\face\AlipayDriver::class,
            'config'     => [
                'app_id'            => env('face.alipay.app_id', ''),
                'private_key'       => env('face.alipay.private_key', ''),
                'alipay_public_key' => env('face.alipay.alipay_public_key', ''),
            ],
        ],

        // 聚合数据
        'juhe' => [
            'name'       => '聚合数据活体检测',
            'enabled'    => false,
            'class'      => \app\service\face\JuheDriver::class,
            'config'     => [
                'api_key' => env('face.juhe.api_key', ''),
            ],
        ],

        // 阿里云市场
        'aliyun_market' => [
            'name'       => '阿里云市场活体检测',
            'enabled'    => false,
            'class'      => \app\service\face\AliyunMarketDriver::class,
            'config'     => [
                'app_code' => env('face.aliyun_market.app_code', ''),
            ],
        ],
    ],

    // 加密配置
    'encryption' => [
        'method' => 'AES-256-GCM',
        'key'    => env('face.encryption_key', ''),
    ],
];