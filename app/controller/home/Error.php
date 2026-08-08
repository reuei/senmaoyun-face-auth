<?php
namespace app\controller\home;
use app\controller\Base;
class Error extends Base
{
    public function forbidden()
    {
        $reason = request()->get('reason', '');
        $msgs = [
            'invalid_token' => '认证Token无效或已过期，请从魔方财务系统重新发起认证。',
            'expired' => '认证链接已过期。',
            'no_permission' => '人脸识别仅允许从魔方财务系统入口进入。',
        ];
        return $this->fetch('error/forbidden', ['reason' => $msgs[$reason] ?? $msgs['no_permission']]);
    }
}