<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 接口管理控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\FaceDriver;
use app\service\FaceManager;
use app\service\EncryptionService;

class Driver extends Base
{
    /**
     * 接口列表
     */
    public function index()
    {
        return view('admin/driver', [
            'site_name' => config('app.site_name'),
            'admin'     => $this->getAdmin(),
        ]);
    }

    /**
     * 获取接口详情
     */
    public function detail($id)
    {
        $driver = FaceDriver::find($id);
        if (!$driver) {
            return $this->error('接口不存在');
        }

        return $this->success($driver->toArray());
    }

    /**
     * 保存接口配置
     */
    public function save()
    {
        $id         = request()->post('id', 0);
        $driverCode = request()->post('driver_code', '');
        $config     = request()->post('config', []);
        $enabled    = request()->post('enabled', 0);
        $isDefault  = request()->post('is_default', 0);

        if (empty($driverCode)) {
            return $this->error('驱动代码不能为空');
        }

        // 加密敏感配置
        $encryption = new EncryptionService();
        $encryptedConfig = [];
        foreach ($config as $key => $value) {
            if (!empty($value) && in_array($key, ['secret_id', 'secret_key', 'api_key', 'app_code', 'private_key', 'alipay_public_key'])) {
                $encryptedConfig[$key] = $encryption->encrypt($value);
            } else {
                $encryptedConfig[$key] = $value;
            }
        }

        $dbDriver = $id > 0 ? FaceDriver::find($id) : FaceDriver::where('driver_code', $driverCode)->find();

        if ($dbDriver) {
            $dbDriver->config   = json_encode($encryptedConfig, JSON_UNESCAPED_UNICODE);
            $dbDriver->enabled  = $enabled;
            $dbDriver->is_default = $isDefault;
            $dbDriver->save();
        } else {
            $dbDriver = FaceDriver::create([
                'driver_code' => $driverCode,
                'config'      => json_encode($encryptedConfig, JSON_UNESCAPED_UNICODE),
                'enabled'     => $enabled,
                'is_default'  => $isDefault,
            ]);
        }

        // 如果设为默认，取消其他默认
        if ($isDefault) {
            FaceDriver::where('driver_code', '<>', $driverCode)->update(['is_default' => 0]);
        }

        $this->auditLog('driver_save', 'face', 'driver', $driverCode);

        return $this->success([], '保存成功');
    }

    /**
     * 测试接口连接
     */
    public function test()
    {
        $driverCode = request()->post('driver_code', '');
        $config     = request()->post('config', []);

        if (empty($driverCode)) {
            return $this->error('请选择驱动');
        }

        $manager = new FaceManager();
        $result  = $manager->testDriver($driverCode, $config);

        return $result['success']
            ? $this->success($result, $result['message'])
            : $this->error($result['message']);
    }

    /**
     * 切换接口状态
     */
    public function toggle()
    {
        $id = request()->post('id', 0);
        $enabled = request()->post('enabled', 0);

        $driver = FaceDriver::find($id);
        if (!$driver) {
            return $this->error('接口不存在');
        }

        $driver->enabled = $enabled;
        $driver->save();

        $this->auditLog($enabled ? 'driver_enable' : 'driver_disable', 'face', 'driver', $driver->driver_code);

        return $this->success([], $enabled ? '接口已启用' : '接口已禁用');
    }
}