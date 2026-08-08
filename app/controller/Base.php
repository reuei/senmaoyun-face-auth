<?php
namespace app\controller;

use think\facade\View;
use think\facade\App;

class Base
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    protected function success($data = [], $msg = '操作成功', $code = 200)
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data, 'time' => time()]);
    }

    protected function error($msg = '操作失败', $code = 400, $data = [])
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data, 'time' => time()]);
    }

    protected function fetch($template = '', $vars = [])
    {
        $vars['site_name'] = config('app.site_name');
        $vars['site_domain'] = config('app.site_domain');
        return View::fetch($template, $vars);
    }

    protected function getAdmin()
    {
        return session('admin_info');
    }

    protected function isLogin()
    {
        return (bool) session('admin_info');
    }

    protected function auditLog($action, $module = '', $targetType = '', $targetId = '')
    {
        try {
            \app\model\AuditLog::create([
                'admin_id' => session('admin_info.id') ?? null,
                'action' => $action, 'module' => $module,
                'target_type' => $targetType, 'target_id' => (string)$targetId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('user-agent', ''),
            ]);
        } catch (\Throwable $e) {}
    }
}