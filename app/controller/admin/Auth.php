<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 后台首页控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\Admin;
use think\facade\Session;

class Auth extends Base
{
    /**
     * 登录页
     */
    public function login()
    {
        return view('admin/login', [
            'site_name' => config('app.site_name'),
        ]);
    }

    /**
     * 执行登录
     */
    public function doLogin()
    {
        $username = request()->post('username', '');
        $password = request()->post('password', '');

        if (empty($username) || empty($password)) {
            return $this->error('请输入用户名和密码');
        }

        $admin = Admin::where('username', $username)
            ->where('status', 1)
            ->find();

        if (!$admin) {
            return $this->error('用户名或密码错误');
        }

        if (!$admin->verifyPassword($password)) {
            return $this->error('用户名或密码错误');
        }

        $admin->updateLoginInfo(request()->ip());

        // 设置会话
        $adminInfo = $admin->toArray();
        unset($adminInfo['password']);
        Session::set('admin_info', $adminInfo);

        $this->auditLog('login', 'admin', 'admin', (string) $admin->id);

        return $this->success([
            'redirect' => '/admin/dashboard',
        ], '登录成功');
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $admin = $this->getAdmin();
        if ($admin) {
            $this->auditLog('logout', 'admin', 'admin', (string) $admin['id']);
        }

        Session::delete('admin_info');
        return redirect('/admin/login');
    }
}