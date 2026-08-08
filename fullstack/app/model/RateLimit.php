<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 速率限制模型
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\model;

class RateLimit extends BaseModel
{
    protected $name = 'rate_limit';

    protected $autoWriteTimestamp = false;

    /**
     * 检查并增加计数
     * @return bool true=通过 false=超限
     */
    public static function check(string $ip, string $action, int $maxCount = 10, int $windowMinutes = 1): bool
    {
        $windowStart = date('Y-m-d H:i:s', time() - $windowMinutes * 60);

        // 清理过期记录
        self::where('window_start', '<', $windowStart)->delete();

        // 查询当前窗口计数
        $record = self::where('ip_address', $ip)
            ->where('action', $action)
            ->where('window_start', '>=', $windowStart)
            ->find();

        if ($record) {
            if ($record->count >= $maxCount) {
                return false;
            }
            $record->count = $record->count + 1;
            $record->save();
        } else {
            self::create([
                'ip_address'   => $ip,
                'action'       => $action,
                'count'        => 1,
                'window_start' => date('Y-m-d H:i:s'),
            ]);
        }

        return true;
    }
}