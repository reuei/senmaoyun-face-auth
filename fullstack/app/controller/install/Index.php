<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 安装向导控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\install;

use app\controller\Base;
use app\model\Admin;
use app\model\Setting;
use think\facade\Db;

class Index extends Base
{
    protected function initialize(): void
    {
        // 安装向导不使用模板布局
    }

    /**
     * 安装首页
     */
    public function index()
    {
        // 检测是否已安装
        $lockFile = root_path() . 'install.lock';
        if (file_exists($lockFile)) {
            return '系统已安装，如需重新安装请删除 install.lock 文件';
        }

        return view('install/index');
    }

    /**
     * 获取安装步骤
     */
    public function step($step)
    {
        $lockFile = root_path() . 'install.lock';
        if (file_exists($lockFile)) {
            return $this->error('系统已安装');
        }

        $step = (int) $step;

        switch ($step) {
            case 1:
                return $this->checkEnvironment();
            case 2:
                return view('install/step2');
            case 3:
                return view('install/step3');
            case 4:
                return view('install/step4');
            default:
                return redirect('/install');
        }
    }

    /**
     * 环境检测
     */
    private function checkEnvironment()
    {
        $checks = [
            'php_version' => [
                'name'    => 'PHP版本 >= 8.1',
                'pass'    => version_compare(PHP_VERSION, '8.1.0', '>='),
                'current' => PHP_VERSION,
            ],
            'curl' => [
                'name'    => 'cURL扩展',
                'pass'    => extension_loaded('curl'),
                'current' => extension_loaded('curl') ? '已启用' : '未启用',
            ],
            'openssl' => [
                'name'    => 'OpenSSL扩展',
                'pass'    => extension_loaded('openssl'),
                'current' => extension_loaded('openssl') ? '已启用' : '未启用',
            ],
            'gd' => [
                'name'    => 'GD扩展',
                'pass'    => extension_loaded('gd'),
                'current' => extension_loaded('gd') ? '已启用' : '未启用',
            ],
            'fileinfo' => [
                'name'    => 'Fileinfo扩展',
                'pass'    => extension_loaded('fileinfo'),
                'current' => extension_loaded('fileinfo') ? '已启用' : '未启用',
            ],
            'mbstring' => [
                'name'    => 'MBString扩展',
                'pass'    => extension_loaded('mbstring'),
                'current' => extension_loaded('mbstring') ? '已启用' : '未启用',
            ],
            'pdo_mysql' => [
                'name'    => 'PDO MySQL扩展',
                'pass'    => extension_loaded('pdo_mysql'),
                'current' => extension_loaded('pdo_mysql') ? '已启用' : '未启用',
            ],
            'mod_rewrite' => [
                'name'    => '伪静态(mod_rewrite)',
                'pass'    => function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : true,
                'current' => '若为Nginx请手动配置伪静态',
            ],
            'storage_write' => [
                'name'    => '目录写入权限',
                'pass'    => is_writable(runtime_path()),
                'current' => is_writable(runtime_path()) ? '可写' : '不可写，请执行: chmod -R 755 runtime/',
            ],
        ];

        return $this->success(['checks' => $checks]);
    }

    /**
     * 执行安装
     */
    public function setup()
    {
        $lockFile = root_path() . 'install.lock';
        if (file_exists($lockFile)) {
            return $this->error('系统已安装');
        }

        $dbHost     = request()->post('db_host', '127.0.0.1');
        $dbPort     = request()->post('db_port', '3306');
        $dbName     = request()->post('db_name', 'senmaoyun');
        $dbUser     = request()->post('db_user', '');
        $dbPass     = request()->post('db_pass', '');
        $dbPrefix   = request()->post('db_prefix', 'smy_');
        $adminUser  = request()->post('admin_user', '');
        $adminPass  = request()->post('admin_pass', '');
        $adminEmail = request()->post('admin_email', '');

        if (empty($dbUser) || empty($adminUser) || empty($adminPass)) {
            return $this->error('请填写必填字段');
        }

        // 测试数据库连接
        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
            $pdo = new \PDO($dsn, $dbUser, $dbPass);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // 创建数据库
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbName}`");
        } catch (\PDOException $e) {
            return $this->error('数据库连接失败: ' . $e->getMessage());
        }

        // 执行SQL
        $sqlFile = root_path() . 'database/install.sql';
        if (!file_exists($sqlFile)) {
            return $this->error('安装SQL文件不存在');
        }

        $sql = file_get_contents($sqlFile);
        $sql = str_replace('{prefix}', $dbPrefix, $sql);

        // 拆分SQL语句执行
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (\PDOException $e) {
                    // 跳过已存在的表
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        return $this->error('SQL执行失败: ' . $e->getMessage() . ' [SQL: ' . substr($statement, 0, 100) . ']');
                    }
                }
            }
        }

        // 写入.env配置
        $envContent = "# 森码云实人认证系统环境配置\n";
        $envContent .= "# 生成时间: " . date('Y-m-d H:i:s') . "\n\n";
        $envContent .= "[APP]\n";
        $envContent .= "APP_DEBUG = false\n";
        $envContent .= "SITE_NAME = 森码云实人认证系统\n";
        $envContent .= "SITE_DOMAIN = face.builds.codes\n\n";
        $envContent .= "[DATABASE]\n";
        $envContent .= "TYPE = mysql\n";
        $envContent .= "HOSTNAME = {$dbHost}\n";
        $envContent .= "DATABASE = {$dbName}\n";
        $envContent .= "USERNAME = {$dbUser}\n";
        $envContent .= "PASSWORD = {$dbPass}\n";
        $envContent .= "HOSTPORT = {$dbPort}\n";
        $envContent .= "PREFIX = {$dbPrefix}\n\n";
        $envContent .= "[SECURITY]\n";
        $envContent .= "API_SECRET = " . bin2hex(random_bytes(32)) . "\n";
        $envContent .= "ENCRYPTION_KEY = " . base64_encode(random_bytes(32)) . "\n";

        file_put_contents(root_path() . '.env', $envContent);

        // 创建管理员
        $admin = new Admin();
        $admin->username = $adminUser;
        $admin->password = Admin::encryptPassword($adminPass);
        $admin->nickname = '管理员';
        $admin->email    = $adminEmail;
        $admin->role     = 'super';
        $admin->status   = 1;
        $admin->save();

        // 生成安装锁
        file_put_contents($lockFile, date('Y-m-d H:i:s'));

        return $this->success([
            'admin_url' => '/admin/login',
        ], '安装成功！请保存好管理员账号信息。');
    }
}