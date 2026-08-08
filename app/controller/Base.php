<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 基础控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller;

use think\facade\App;
use think\facade\View;

/**
 * 控制器基类
 */
abstract class Base
{
    /**
     * 应用实例
     */
    protected $app;

    /**
     * 构造方法
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize(): void
    {
        // 子类可重写
    }

    /**
     * 成功响应
     */
    protected function success($data = [], string $msg = '操作成功', int $code = 200): \think\response\Json
    {
        return json([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
            'time' => time(),
        ]);
    }

    /**
     * 失败响应
     */
    protected function error(string $msg = '操作失败', int $code = 400, $data = []): \think\response\Json
    {
        return json([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
            'time' => time(),
        ]);
    }

    /**
     * 渲染模板
     */
    protected function fetch(string $template = '', array $vars = []): string
    {
        return View::fetch($template, $vars);
    }

    /**
     * 获取当前登录管理员
     */
    protected function getAdmin(): ?array
    {
        return session('admin_info');
    }

    /**
     * 记录审计日志
     */
    protected function auditLog(string $action, string $module = '', $targetType = '', $targetId = '', $content = null): void
    {
        try {
            $admin = $this->getAdmin();
            \app\model\AuditLog::create([
                'admin_id'    => $admin['id'] ?? null,
                'action'      => $action,
                'module'      => $module,
                'target_type' => (string) $targetType,
                'target_id'   => (string) $targetId,
                'content'     => is_string($content) ? $content : json_encode($content, JSON_UNESCAPED_UNICODE),
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->header('user-agent', ''),
            ]);
        } catch (\Throwable $e) {
            // 日志记录失败不影响主流程
        }
    }
}