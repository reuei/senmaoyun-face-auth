<?php
namespace app\controller\admin;
use app\controller\Base;
use app\model\FaceDriver;

class Driver extends Base
{
    public function index() { return $this->fetch('admin/driver'); }
    public function save()
    {
        $code = request()->post('driver_code', '');
        $config = json_decode(request()->post('config', '{}'), true) ?: [];
        $enabled = (int)request()->post('enabled', 0);
        $isDefault = (int)request()->post('is_default', 0);
        $driver = FaceDriver::where('driver_code', $code)->first();
        if (!$driver) { $driver = new FaceDriver; $driver->driver_code = $code; $driver->driver_name = $code; }
        $driver->config = json_encode($config, JSON_UNESCAPED_UNICODE);
        $driver->enabled = $enabled; $driver->is_default = $isDefault; $driver->save();
        if ($isDefault) FaceDriver::where('driver_code', '<>', $code)->update(['is_default' => 0]);
        $this->auditLog('driver_save', 'face', 'driver', $code);
        return $this->success([], '保存成功');
    }
}