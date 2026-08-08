<?php
/**
 * 森码云实人认证插件 - 魔方财务系统
 * 插件类型: certification
 * 安装路径: /public/plugins/certification/senmaoyun/
 * 版本: 1.0.0
 */
namespace plugin\certification\senmaoyun;

class SenmaoyunPlugin
{
    /**
     * 插件名称
     */
    public $name = '森码云实人认证';

    /**
     * 插件标识
     */
    public $code = 'senmaoyun';

    /**
     * 插件版本
     */
    public $version = '1.0.0';

    /**
     * 插件作者
     */
    public $author = '森码云';

    /**
     * 插件描述
     */
    public $description = '对接森码云实人认证系统，提供人脸识别+活体检测的实名认证能力';

    /**
     * 系统地址
     */
    public $url = 'https://face.builds.codes';

    /**
     * 插件初始化
     */
    public function install()
    {
        // 插件安装时执行的逻辑
        return [
            'status' => 200,
            'msg'    => '森码云实人认证插件安装成功',
        ];
    }

    /**
     * 插件卸载
     */
    public function uninstall()
    {
        return [
            'status' => 200,
            'msg'    => '插件已卸载',
        ];
    }

    /**
     * 插件启用
     */
    public function enable()
    {
        return [
            'status' => 200,
            'msg'    => '插件已启用',
        ];
    }

    /**
     * 插件禁用
     */
    public function disable()
    {
        return [
            'status' => 200,
            'msg'    => '插件已禁用',
        ];
    }

    /**
     * 获取配置项
     */
    public function getConfig()
    {
        return [
            'api_url' => [
                'title'       => '森码云系统地址',
                'type'        => 'text',
                'value'       => 'https://face.builds.codes',
                'tip'         => '森码云实人认证系统的部署地址',
                'required'    => true,
            ],
            'api_key' => [
                'title'       => 'API Key',
                'type'        => 'password',
                'value'       => '',
                'tip'         => '在森码云后台生成的API密钥',
                'required'    => true,
            ],
            'certify_type' => [
                'title'       => '认证类型',
                'type'        => 'select',
                'value'       => 'personal',
                'options'     => [
                    'personal' => '个人认证',
                    'enterprise' => '企业认证',
                ],
                'tip'         => '选择认证类型',
            ],
            'amount' => [
                'title'       => '收费金额',
                'type'        => 'number',
                'value'       => 0,
                'tip'         => '认证费用，0为免费',
            ],
            'free_times' => [
                'title'       => '免费次数',
                'type'        => 'number',
                'value'       => 0,
                'tip'         => '每个用户免费认证次数',
            ],
            'liveness_level' => [
                'title'       => '活体检测等级',
                'type'        => 'select',
                'value'       => 'normal',
                'options'     => [
                    'low'    => '低（宽松）',
                    'normal' => '标准',
                    'high'   => '高（严格）',
                ],
                'tip'         => '活体检测严格程度',
            ],
        ];
    }

    /**
     * 发起认证（魔方财务调用）
     * @param array $user 用户信息
     * @return array
     */
    public function certify($user)
    {
        $config = $this->getConfig();
        $apiUrl = $config['api_url']['value'];
        $apiKey = $config['api_key']['value'];

        if (empty($apiUrl) || empty($apiKey)) {
            return [
                'status' => 400,
                'msg'    => '请先配置森码云系统地址和API Key',
            ];
        }

        // 生成请求Token
        $tokenResult = $this->generateToken($user['id'], $apiUrl, $apiKey);

        if (!$tokenResult['success']) {
            return [
                'status' => 500,
                'msg'    => $tokenResult['msg'],
            ];
        }

        // 返回认证URL
        return [
            'status'    => 200,
            'certify_url' => $apiUrl . '/verify?token=' . $tokenResult['token'],
            'token'     => $tokenResult['token'],
        ];
    }

    /**
     * 生成Token
     */
    private function generateToken($userId, $apiUrl, $apiKey)
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 10]);
            $response = $client->post($apiUrl . '/api/v1/certify/init', [
                'json' => [
                    'user_id'      => (string) $userId,
                    'callback_url' => $this->getCallbackUrl(),
                    'api_key'      => $apiKey,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $result['code'] == 200,
                'token'   => $result['data']['token'] ?? '',
                'msg'     => $result['msg'] ?? '',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'msg'     => 'Token生成失败: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * 获取回调地址
     */
    private function getCallbackUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . '/plugin/certification/senmaoyun/callback';
    }
}