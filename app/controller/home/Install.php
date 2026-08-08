<?php
namespace app\controller\home;

use app\controller\Base;

class Install extends Base
{
    public function index()
    {
        if (file_exists(app()->getRootPath() . 'install.lock')) {
            return '系统已安装，如需重新安装请删除 install.lock 文件';
        }
        return $this->fetch('install/index');
    }

    public function check()
    {
        $checks = [
            ['name' => 'PHP版本 >= 8.1', 'pass' => version_compare(PHP_VERSION, '8.1.0', '>='), 'current' => PHP_VERSION],
            ['name' => 'cURL扩展', 'pass' => extension_loaded('curl'), 'current' => extension_loaded('curl') ? '已启用' : '未启用'],
            ['name' => 'OpenSSL扩展', 'pass' => extension_loaded('openssl'), 'current' => extension_loaded('openssl') ? '已启用' : '未启用'],
            ['name' => 'GD扩展', 'pass' => extension_loaded('gd'), 'current' => extension_loaded('gd') ? '已启用' : '未启用'],
            ['name' => 'Fileinfo扩展', 'pass' => extension_loaded('fileinfo'), 'current' => extension_loaded('fileinfo') ? '已启用' : '未启用'],
            ['name' => 'PDO MySQL', 'pass' => extension_loaded('pdo_mysql'), 'current' => extension_loaded('pdo_mysql') ? '已启用' : '未启用'],
            ['name' => '目录写入', 'pass' => is_writable(app()->getRootPath()), 'current' => is_writable(app()->getRootPath()) ? '可写' : '不可写'],
        ];
        return $this->success(['checks' => $checks]);
    }

    public function setup()
    {
        $data = request()->post();
        $dbHost = $data['db_host'] ?? '127.0.0.1';
        $dbPort = $data['db_port'] ?? '3306';
        $dbName = $data['db_name'] ?? 'senmaoyun';
        $dbUser = $data['db_user'] ?? '';
        $dbPass = $data['db_pass'] ?? '';
        $dbPrefix = $data['db_prefix'] ?? 'smy_';
        $adminUser = $data['admin_user'] ?? '';
        $adminPass = $data['admin_pass'] ?? '';

        if (empty($dbUser) || empty($adminUser) || empty($adminPass)) {
            return $this->error('请填写必填字段');
        }

        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
            $pdo = new \PDO($dsn, $dbUser, $dbPass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbName}`");

            $sqlFile = app()->getRootPath() . 'database/install.sql';
            $sql = str_replace('{prefix}', $dbPrefix, file_get_contents($sqlFile));
            foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
                try { $pdo->exec($stmt); } catch (\PDOException $e) {
                    if (strpos($e->getMessage(), 'already exists') === false) throw $e;
                }
            }

            // 写入 .env
            $env = "[APP]\nAPP_DEBUG=false\nSITE_NAME=森码云实人认证系统\nSITE_DOMAIN=face.builds.codes\n\n";
            $env .= "[DATABASE]\nDRIVER=mysql\nHOSTNAME={$dbHost}\nDATABASE={$dbName}\nUSERNAME={$dbUser}\nPASSWORD={$dbPass}\nHOSTPORT={$dbPort}\nPREFIX={$dbPrefix}\n";
            file_put_contents(app()->getRootPath() . '.env', $env);

            // 创建管理员
            $hash = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare("INSERT INTO `{$dbPrefix}admin` (username, password, nickname, role, status) VALUES (?, ?, '管理员', 'super', 1)")
                ->execute([$adminUser, $hash]);

            file_put_contents(app()->getRootPath() . 'install.lock', date('Y-m-d H:i:s'));
            return $this->success([], '安装成功！管理后台: /admin/login');
        } catch (\PDOException $e) {
            return $this->error('数据库错误: ' . $e->getMessage());
        }
    }
}