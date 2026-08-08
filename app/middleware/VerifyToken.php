<?php
namespace app\middleware;

class VerifyToken
{
    public function handle($request, \Closure $next)
    {
        $token = $request->param('token', '');
        if (empty($token)) return redirect('/forbidden?reason=no_permission');

        $record = \app\model\CertifyToken::where('token', $token)
            ->where('type', 'request')->where('expire_time', '>', date('Y-m-d H:i:s'))
            ->where('used', 0)->find();

        if (!$record) return redirect('/forbidden?reason=invalid_token');

        $record->used = 1;
        $record->used_time = date('Y-m-d H:i:s');
        $record->save();

        $request->certifyToken = $record;
        return $next($request);
    }
}