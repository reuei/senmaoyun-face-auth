<?php
namespace app\controller\admin;

use app\controller\Base;
use app\model\User;

class UserManage extends Base
{
    public function index()
    {
        return $this->fetch('admin/users');
    }

    public function list()
    {
        $page = request()->post('page', 1);
        $users = User::order('id desc')->page($page, 20)->select();
        $total = User::count();
        return $this->success(['total' => $total, 'page' => $page, 'list' => $users]);
    }

    public function toggle()
    {
        $id = (int)request()->post('id', 0);
        $status = (int)request()->post('status', 0);
        $user = User::find($id);
        if ($user) { $user->status = $status; $user->save(); }
        $this->auditLog('user_toggle', 'user', 'user', $id);
        return $this->success([], $status ? '用户已启用' : '用户已禁用');
    }
}