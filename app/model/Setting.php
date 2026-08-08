<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 系统配置模型
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\model;

class Setting extends BaseModel
{
    protected $name = 'setting';

    /**
     * 获取配置值
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->find();
        if (!$setting) {
            return $default;
        }

        $value = $setting->value;
        switch ($setting->type) {
            case 'number':
                return is_numeric($value) ? (float) $value : $default;
            case 'bool':
                return in_array($value, ['1', 'true', 'on'], true);
            case 'json':
                return json_decode($value, true) ?: $default;
            default:
                return $value ?? $default;
        }
    }

    /**
     * 设置配置值
     */
    public static function setValue(string $key, $value, string $type = 'string'): void
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            $type  = 'json';
        }

        $setting = self::where('key', $key)->find();
        if ($setting) {
            $setting->value = (string) $value;
            $setting->save();
        } else {
            self::create([
                'key'   => $key,
                'value' => (string) $value,
                'type'  => $type,
            ]);
        }
    }

    /**
     * 批量获取配置
     */
    public static function getGroup(string $group): array
    {
        $settings = self::where('group', $group)->select()->toArray();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key']] = self::getValue($setting['key']);
        }
        return $result;
    }
}