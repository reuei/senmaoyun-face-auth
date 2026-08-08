<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 插件中心控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;

class Plugin extends Base
{
    public function index()
    {
        return view('admin/plugin', [
            'site_name' => config('app.site_name'),
            'admin'     => $this->getAdmin(),
        ]);
    }

    public function download()
    {
        $pluginDir = root_path() . 'plugin/senmaoyun';
        $zipFile   = runtime_path() . 'senmaoyun_plugin.zip';

        // 打包插件
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addDirToZip($zip, $pluginDir, 'senmaoyun');
            $zip->close();
        } else {
            return $this->error('打包失败');
        }

        $this->auditLog('plugin_download', 'plugin', 'download', '');
        return download($zipFile, 'senmaoyun_plugin.zip');
    }

    private function addDirToZip(\ZipArchive $zip, string $dir, string $base): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $base . '/' . substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}