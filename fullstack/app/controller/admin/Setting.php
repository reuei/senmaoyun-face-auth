<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 系统设置控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\Setting;

class Setting extends Base
{
    public function index()
    {
        return view('admin/setting', [
            'site_name' => config('app.site_name'),
            'admin'     => $this->getAdmin(),
        ]);
    }

    public function save()
    {
        $settings = request()->post('settings', []);

        if (empty($settings) || !is_array($settings)) {
            return $this->error('配置数据为空');
        }

        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value);
        }

        $this->auditLog('setting_save', 'system', 'setting', '');
        return $this->success([], '配置保存成功');
    }
}