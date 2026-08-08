<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 接口驱动模型
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\model;

class FaceDriver extends BaseModel
{
    protected $name = 'face_driver';

    /**
     * 获取启用的驱动列表
     */
    public static function getEnabledDrivers(): array
    {
        return self::where('enabled', 1)
            ->order('is_default desc, sort asc')
            ->select()
            ->toArray();
    }

    /**
     * 获取默认驱动
     */
    public static function getDefaultDriver(): ?self
    {
        return self::where('enabled', 1)
            ->where('is_default', 1)
            ->find();
    }

    /**
     * 获取驱动配置
     */
    public function getConfig(): array
    {
        $config = $this->getData('config');
        if (is_string($config)) {
            $config = json_decode($config, true) ?: [];
        }
        return $config;
    }
}