<?php
/**
 * 森码云实人认证插件 - 回调控制器
 */
namespace plugin\certification\senmaoyun\controller;

class CertifyController
{
    /**
     * 认证回调处理
     */
    public function callback()
    {
        $token     = input('post.token', '');
        $userId    = input('post.user_id', '');
        $status    = input('post.status', '');
        $signature = input('post.sign', '');

        // 验证签名
        $apiKey = config('senmaoyun.api_key');
        $expectedSign = hash_hmac('sha256', $token . $userId, $apiKey);

        if (!hash_equals($expectedSign, $signature)) {
            return json(['status' => 400, 'msg' => '签名验证失败']);
        }

        if ($status === 'success') {
            // 更新用户实名状态
            \app\model\User::where('id', $userId)->update([
                'certify_status' => 1,
                'certify_time'   => date('Y-m-d H:i:s'),
            ]);

            return json([
                'status' => 200,
                'msg'    => '认证成功，用户实名状态已更新',
            ]);
        }

        return json([
            'status' => 200,
            'msg'    => '认证失败回调已接收',
        ]);
    }
}