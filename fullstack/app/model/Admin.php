<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 管理员模型
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\model;

class Admin extends BaseModel
{
    protected $name = 'admin';

    /**
     * 隐藏字段
     */
    protected $hidden = ['password'];

    /**
     * 密码加密
     */
    public static function encryptPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * 验证密码
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->getData('password'));
    }

    /**
     * 更新登录信息
     */
    public function updateLoginInfo(string $ip): void
    {
        $this->last_login_ip   = $ip;
        $this->last_login_time = date('Y-m-d H:i:s');
        $this->login_count     = $this->login_count + 1;
        $this->save();
    }
}