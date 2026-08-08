<?php
/**
 * 森码云实人认证插件 - 魔方财务系统
 * 插件类型: certification
 * 安装路径: /public/plugins/certification/senmaoyun/
 * 版本: 1.0.0
 */
class SenmaoyunPlugin
{
    public $name = '森码云实人认证';
    public $code = 'senmaoyun';
    public $version = '1.0.0';
    public $author = '森码云';
    public $description = '对接森码云实人认证系统，提供人脸识别+活体检测的实名认证能力';
    public $url = 'https://face.builds.codes';

    public function install() { return ['status' => 200, 'msg' => '安装成功']; }
    public function uninstall() { return ['status' => 200, 'msg' => '已卸载']; }
    public function enable() { return ['status' => 200, 'msg' => '已启用']; }
    public function disable() { return ['status' => 200, 'msg' => '已禁用']; }

    public function getConfig()
    {
        return [
            'api_url' => ['title' => '森码云系统地址', 'type' => 'text', 'value' => 'https://face.builds.codes', 'tip' => '森码云实人认证系统的部署地址', 'required' => true],
            'api_key' => ['title' => 'API Key', 'type' => 'password', 'value' => '', 'tip' => '在森码云后台生成的API密钥', 'required' => true],
            'certify_type' => ['title' => '认证类型', 'type' => 'select', 'value' => 'personal', 'options' => ['personal' => '个人认证', 'enterprise' => '企业认证']],
            'amount' => ['title' => '收费金额', 'type' => 'number', 'value' => 0, 'tip' => '0为免费'],
            'free_times' => ['title' => '免费次数', 'type' => 'number', 'value' => 0],
        ];
    }

    public function certify($user)
    {
        $config = $this->getConfig();
        $apiUrl = $config['api_url']['value'];
        $apiKey = $config['api_key']['value'];
        if (empty($apiUrl) || empty($apiKey)) return ['status' => 400, 'msg' => '请先配置API地址和Key'];

        $ch = curl_init($apiUrl . '/api/?action=token_generate');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['user_id' => (string)$user['id'], 'callback_url' => $this->getCallbackUrl(), 'api_key' => $apiKey]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10,
        ]);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (($result['code'] ?? 0) == 200) {
            return ['status' => 200, 'certify_url' => $apiUrl . '/verify?token=' . $result['data']['token'], 'token' => $result['data']['token']];
        }
        return ['status' => 500, 'msg' => $result['msg'] ?? 'Token生成失败'];
    }

    private function getCallbackUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        return $protocol . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/plugin/certification/senmaoyun/callback';
    }
}