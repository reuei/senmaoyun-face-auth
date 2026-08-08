<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 首页控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller;

class Index extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        return view('index', [
            'site_name'   => config('app.site_name'),
            'site_domain' => config('app.site_domain'),
        ]);
    }

    /**
     * 认证页（受Token保护）
     */
    public function verify()
    {
        $tokenRecord = request()->certifyToken ?? null;
        if (!$tokenRecord) {
            return redirect('/forbidden');
        }

        return view('verify', [
            'token'      => $tokenRecord->token,
            'user_id'    => $tokenRecord->user_id,
            'callback'   => $tokenRecord->callback_url,
            'site_name'  => config('app.site_name'),
        ]);
    }

    /**
     * 禁止访问页
     */
    public function forbidden()
    {
        $reason = request()->param('reason', '');
        return view('forbidden', [
            'reason'     => $reason,
            'site_name'  => config('app.site_name'),
        ]);
    }
}