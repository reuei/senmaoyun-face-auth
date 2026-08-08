<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - Token验证中间件
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\middleware;

use think\facade\Log;
use app\model\CertifyToken;

class VerifyToken
{
    /**
     * 处理请求
     */
    public function handle($request, \Closure $next)
    {
        // 获取Token参数
        $token = $request->param('token', '');

        if (empty($token)) {
            // 无Token，检查是否来自魔方财务
            $referer = $request->header('referer', '');
            // 允许API请求通过
            if (strpos($request->pathinfo(), 'api/') === 0) {
                return $next($request);
            }
            // 直接访问拒绝
            return redirect('/forbidden');
        }

        // 验证Token
        $tokenRecord = CertifyToken::where('token', $token)
            ->where('type', 'request')
            ->where('expire_time', '>', date('Y-m-d H:i:s'))
            ->where('used', 0)
            ->find();

        if (!$tokenRecord) {
            Log::warning('无效的认证Token: ' . substr($token, 0, 16) . '...');
            return redirect('/forbidden?reason=invalid_token');
        }

        // 标记Token已被使用
        $tokenRecord->used = 1;
        $tokenRecord->used_time = date('Y-m-d H:i:s');
        $tokenRecord->save();

        // 将Token信息传递到请求中
        $request->certifyToken = $tokenRecord;

        return $next($request);
    }
}