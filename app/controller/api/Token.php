<?php
namespace app\controller\api;

use app\controller\Base;
use app\model\CertifyToken;

class Token extends Base
{
    public function generate()
    {
        $userId = request()->post('user_id', '');
        $callbackUrl = request()->post('callback_url', '');
        $apiKey = request()->post('api_key', '');
        if (empty($userId) || empty($callbackUrl)) return $this->error('参数不完整');
        if ($apiKey !== env('app.api_secret', '')) return $this->error('API Key无效', 403);

        $token = hash('sha256', random_bytes(32) . microtime(true) . $userId);
        CertifyToken::create([
            'token' => $token, 'type' => 'request', 'user_id' => $userId,
            'callback_url' => $callbackUrl, 'expire_time' => date('Y-m-d H:i:s', time() + 300),
        ]);
        return $this->success(['token' => $token, 'verify_url' => request()->domain() . '/verify?token=' . $token]);
    }

    public function verify()
    {
        $token = request()->post('token', '');
        if (empty($token)) return $this->error('Token不能为空');
        $row = CertifyToken::where('token', $token)->where('expire_time', '>', date('Y-m-d H:i:s'))->where('used', 0)->find();
        return $this->success(['valid' => (bool)$row, 'user_id' => $row->user_id ?? '']);
    }
}