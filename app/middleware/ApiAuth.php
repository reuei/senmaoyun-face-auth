<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - API认证中间件
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\middleware;

use think\facade\Log;

class ApiAuth
{
    /**
     * 处理请求
     */
    public function handle($request, \Closure $next)
    {
        // API签名验证
        $timestamp = $request->header('X-Timestamp', '');
        $signature = $request->header('X-Signature', '');
        $nonce     = $request->header('X-Nonce', '');

        // 检查必要参数
        if (empty($timestamp) || empty($signature) || empty($nonce)) {
            // 允许安装向导和部分公开接口
            $path = $request->pathinfo();
            $publicPaths = ['install/', 'api/auth/login', 'api/token/verify'];
            foreach ($publicPaths as $publicPath) {
                if (strpos($path, $publicPath) === 0) {
                    return $next($request);
                }
            }

            return json([
                'code' => 401,
                'msg'  => '缺少API认证参数',
                'time' => time(),
            ]);
        }

        // 验证时间戳（5分钟有效）
        $timeDiff = abs(time() - intval($timestamp));
        if ($timeDiff > 300) {
            return json([
                'code' => 401,
                'msg'  => '请求已过期',
                'time' => time(),
            ]);
        }

        // 验证Nonce（防重放）
        $nonceKey = 'api_nonce_' . $nonce;
        if (cache($nonceKey)) {
            return json([
                'code' => 401,
                'msg'  => '请求重复',
                'time' => time(),
            ]);
        }
        cache($nonceKey, 1, 300);

        // 验证签名
        $apiSecret = env('app.api_secret', '');
        $expectedSign = hash_hmac('sha256', $timestamp . $nonce . $request->url(), $apiSecret);

        if (!hash_equals($expectedSign, $signature)) {
            Log::warning('API签名验证失败: ' . $request->url());
            return json([
                'code' => 401,
                'msg'  => '签名验证失败',
                'time' => time(),
            ]);
        }

        return $next($request);
    }
}