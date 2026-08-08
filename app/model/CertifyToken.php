<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 认证Token模型
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\model;

class CertifyToken extends BaseModel
{
    protected $name = 'certify_token';

    /**
     * 生成请求Token（用于魔方财务跳转）
     */
    public static function generateRequestToken(string $userId, string $callbackUrl, int $expireMinutes = 5): array
    {
        // 生成64位以上随机Token
        $token = hash('sha256', random_bytes(32) . microtime(true) . $userId);

        $record = self::create([
            'token'        => $token,
            'type'         => 'request',
            'user_id'      => $userId,
            'callback_url' => $callbackUrl,
            'expire_time'  => date('Y-m-d H:i:s', time() + $expireMinutes * 60),
            'used'         => 0,
        ]);

        return [
            'token'       => $token,
            'expire_time' => $record->expire_time,
        ];
    }

    /**
     * 生成回调Token
     */
    public static function generateCallbackToken(string $userId, int $recordId): string
    {
        $token = hash('sha256', random_bytes(32) . microtime(true) . $userId . $recordId);

        self::create([
            'token'        => $token,
            'type'         => 'callback',
            'user_id'      => $userId,
            'expire_time'  => date('Y-m-d H:i:s', time() + 600),
            'used'         => 0,
            'record_id'    => $recordId,
        ]);

        return $token;
    }

    /**
     * 验证Token
     */
    public static function verifyToken(string $token, string $type = 'request'): ?self
    {
        return self::where('token', $token)
            ->where('type', $type)
            ->where('expire_time', '>', date('Y-m-d H:i:s'))
            ->where('used', 0)
            ->find();
    }
}