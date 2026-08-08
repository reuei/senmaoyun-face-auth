<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 审计日志模型
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\model;

class AuditLog extends BaseModel
{
    protected $name = 'audit_log';

    protected $autoWriteTimestamp = false;

    /**
     * 记录审计日志
     */
    public static function record(
        ?int $adminId,
        string $action,
        string $module = '',
        string $targetType = '',
        string $targetId = '',
        $content = null
    ): void {
        try {
            self::create([
                'admin_id'    => $adminId,
                'action'      => $action,
                'module'      => $module,
                'target_type' => $targetType,
                'target_id'   => $targetId,
                'content'     => is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE),
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->header('user-agent', ''),
            ]);
        } catch (\Throwable $e) {
            // 记录失败不影响主流程
        }
    }
}