<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - Token管理控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\CertifyToken;

class Token extends Base
{
    public function index()
    {
        return view('admin/token', [
            'site_name' => config('app.site_name'),
            'admin'     => $this->getAdmin(),
        ]);
    }

    public function revoke()
    {
        $id = request()->post('id', 0);
        $token = CertifyToken::find($id);
        if (!$token) {
            return $this->error('Token不存在');
        }

        $token->used = 1;
        $token->save();

        $this->auditLog('token_revoke', 'token', 'token', (string) $token->id);
        return $this->success([], 'Token已强制失效');
    }

    public function log()
    {
        $page  = (int) request()->param('page', 1);
        $limit = (int) request()->param('limit', 20);

        $result = CertifyToken::getPageList($page, $limit, [], 'id desc');
        return $this->success($result);
    }
}