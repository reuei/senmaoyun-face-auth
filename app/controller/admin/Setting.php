<?php
namespace app\controller\admin;
use app\controller\Base;
use app\model\Setting as SettingModel;

class Setting extends Base
{
    public function index() { return $this->fetch('admin/setting'); }
    public function save()
    {
        foreach (request()->post() as $k => $v) {
            if (strpos($k, 'set_') === 0) {
                $key = substr($k, 4);
                $row = SettingModel::where('key', $key)->first();
                if ($row) { $row->value = $v; $row->save(); }
                else { SettingModel::create(['key' => $key, 'value' => $v, 'type' => 'string', 'group' => 'system']); }
            }
        }
        return $this->success([], '保存成功');
    }
}