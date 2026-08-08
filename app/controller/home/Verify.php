<?php
namespace app\controller\home;

use app\controller\Base;

class Verify extends Base
{
    public function index()
    {
        $token = request()->get('token', '');
        if (empty($token)) return redirect('/forbidden?reason=no_permission');

        $record = \app\model\CertifyToken::where('token', $token)
            ->where('type', 'request')->where('expire_time', '>', date('Y-m-d H:i:s'))
            ->where('used', 0)->find();

        if (!$record) return redirect('/forbidden?reason=invalid_token');

        $record->used = 1;
        $record->used_time = date('Y-m-d H:i:s');
        $record->save();

        return $this->fetch('verify/index', [
            'token' => $token,
            'user_id' => $record->user_id,
            'callback_url' => $record->callback_url,
        ]);
    }
}