<?php
namespace app\controller\admin;
use app\controller\Base;
use app\model\CertifyToken;

class Token extends Base
{
    public function index() { return $this->fetch('admin/token'); }
    public function revoke()
    {
        $id = (int)request()->post('id', 0);
        $token = CertifyToken::find($id);
        if ($token) { $token->used = 1; $token->save(); }
        $this->auditLog('token_revoke', 'token', 'token', $id);
        return $this->success([], 'Token已失效');
    }
}