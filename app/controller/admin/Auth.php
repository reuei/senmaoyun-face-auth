<?php
namespace app\controller\admin;

use app\controller\Base;
use app\model\Admin as AdminModel;

class Auth extends Base
{
    public function login()
    {
        return $this->fetch('admin/login');
    }

    public function doLogin()
    {
        $username = request()->post('username', '');
        $password = request()->post('password', '');
        if (empty($username) || empty($password)) return $this->error('请输入用户名和密码');

        $admin = AdminModel::where('username', $username)->where('status', 1)->find();
        if (!$admin || !password_verify($password, $admin->password)) {
            return $this->error('用户名或密码错误');
        }

        $admin->last_login_ip = request()->ip();
        $admin->last_login_time = date('Y-m-d H:i:s');
        $admin->login_count = $admin->login_count + 1;
        $admin->save();

        $info = $admin->toArray();
        unset($info['password']);
        session('admin_info', $info);

        $this->auditLog('login', 'admin', 'admin', $admin->id);
        return $this->success([], '登录成功');
    }

    public function logout()
    {
        session('admin_info', null);
        return redirect('/admin/login');
    }
}