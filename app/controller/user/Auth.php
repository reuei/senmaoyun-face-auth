<?php
namespace app\controller\user;

use app\controller\Base;
use app\model\User;
use app\model\CertifyRecord;

class Auth extends Base
{
    // 注册页
    public function register() { return $this->fetch('user/register'); }

    // 登录页
    public function login() { return $this->fetch('user/login'); }

    // 用户中心
    public function center()
    {
        $userId = session('user_id');
        if (!$userId) return redirect('/user/login');
        $user = User::find($userId);
        return $this->fetch('user/center', ['user' => $user]);
    }

    // 处理注册
    public function doRegister()
    {
        $username = request()->post('username', '');
        $password = request()->post('password', '');
        $email = request()->post('email', '');
        if (empty($username) || empty($password)) return $this->error('请填写用户名和密码');
        if (mb_strlen($username) < 3) return $this->error('用户名至少3个字符');
        if (mb_strlen($password) < 6) return $this->error('密码至少6个字符');

        $exists = User::where('username', $username)->find();
        if ($exists) return $this->error('用户名已存在');

        if (!empty($email)) {
            $emailExists = User::where('email', $email)->find();
            if ($emailExists) return $this->error('邮箱已被注册');
        }

        $user = User::create([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            'nickname' => $username,
            'email' => $email,
            'last_login_ip' => request()->ip(),
            'last_login_time' => date('Y-m-d H:i:s'),
        ]);

        session('user_id', $user->id);
        session('user_username', $user->username);
        return $this->success([], '注册成功');
    }

    // 处理登录
    public function doLogin()
    {
        $username = request()->post('username', '');
        $password = request()->post('password', '');
        if (empty($username) || empty($password)) return $this->error('请填写用户名和密码');

        $user = User::where('username', $username)->where('status', 1)->find();
        if (!$user || !password_verify($password, $user->password)) {
            return $this->error('用户名或密码错误');
        }

        $user->last_login_ip = request()->ip();
        $user->last_login_time = date('Y-m-d H:i:s');
        $user->save();

        session('user_id', $user->id);
        session('user_username', $user->username);
        return $this->success(['redirect' => '/user/center'], '登录成功');
    }

    // 退出
    public function logout()
    {
        session('user_id', null);
        session('user_username', null);
        return redirect('/user/login');
    }

    // 获取认证状态
    public function certifyStatus()
    {
        $userId = session('user_id');
        if (!$userId) return $this->error('请先登录', 401);
        $user = User::find($userId);
        return $this->success([
            'certify_status' => $user->certify_status,
            'certify_time' => $user->certify_time,
            'real_name' => $user->real_name ?: '',
        ]);
    }

    // 更新认证状态(由认证回调触发)
    public function updateCertify()
    {
        $userId = request()->post('user_id', '');
        $status = request()->post('status', '');
        $sign = request()->post('sign', '');
        $expected = hash_hmac('sha256', $userId . $status, env('app.api_secret', ''));
        if (!hash_equals($expected, $sign)) return $this->error('签名验证失败', 403);

        $user = User::find($userId);
        if ($user) {
            $user->certify_status = $status === 'success' ? 1 : 0;
            $user->certify_time = $status === 'success' ? date('Y-m-d H:i:s') : null;
            $user->save();
        }
        return $this->success([], '状态更新成功');
    }
}