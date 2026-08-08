<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 后台管理认证中间件
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\middleware;

class AdminAuth
{
    /**
     * 处理请求
     */
    public function handle($request, \Closure $next)
    {
        $path = $request->pathinfo();

        // 登录页面和API放行
        $skipPaths = ['admin/login', 'admin/logout', 'api/', 'install/'];
        foreach ($skipPaths as $skipPath) {
            if (strpos($path, $skipPath) === 0) {
                return $next($request);
            }
        }

        // 检查登录状态
        $adminInfo = session('admin_info');
        if (empty($adminInfo)) {
            // AJAX请求返回JSON
            if ($request->isAjax() || $request->isPost()) {
                return json([
                    'code' => 401,
                    'msg'  => '请先登录',
                    'time' => time(),
                ]);
            }

            return redirect('/admin/login');
        }

        return $next($request);
    }
}