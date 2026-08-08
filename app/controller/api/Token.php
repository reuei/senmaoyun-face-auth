<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - Token API
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\api;

use app\controller\Base;
use app\model\CertifyToken;

class Token extends Base
{
    /**
     * 验证Token有效性
     * POST /api/token/verify
     */
    public function verify()
    {
        $token = request()->post('token', '');
        $type  = request()->post('type', 'request');

        if (empty($token)) {
            return $this->error('Token不能为空');
        }

        $tokenRecord = CertifyToken::verifyToken($token, $type);

        if (!$tokenRecord) {
            return $this->error('Token无效或已过期');
        }

        return $this->success([
            'valid'       => true,
            'user_id'     => $tokenRecord->user_id,
            'expire_time' => $tokenRecord->expire_time,
            'type'        => $tokenRecord->type,
        ], 'Token有效');
    }

    /**
     * 生成魔方财务请求Token
     * POST /api/token/generate
     */
    public function generate()
    {
        $userId      = request()->post('user_id', '');
        $callbackUrl = request()->post('callback_url', '');
        $apiKey      = request()->post('api_key', '');

        if (empty($userId) || empty($callbackUrl)) {
            return $this->error('参数不完整');
        }

        // 验证API Key
        $validApiKey = env('app.api_key', '');
        if (empty($validApiKey)) {
            $validApiKey = \app\model\Setting::getValue('mofang_api_key', '');
        }

        if ($apiKey !== $validApiKey) {
            return $this->error('API Key无效');
        }

        $result = CertifyToken::generateRequestToken($userId, $callbackUrl, 5);

        return $this->success([
            'token'       => $result['token'],
            'expire_time' => $result['expire_time'],
            'verify_url'  => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://')
                . $_SERVER['HTTP_HOST'] . '/verify?token=' . $result['token'],
        ], 'Token生成成功');
    }
}