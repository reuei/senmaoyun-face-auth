<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 人脸识别管理器
// | 负责驱动管理、主备切换、统一调用
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service;

use app\model\FaceDriver;
use app\service\face\BaseDriver;
use think\facade\Log;

class FaceManager
{
    /**
     * 驱动实例缓存
     */
    private array $drivers = [];

    /**
     * 驱动类映射
     */
    private const DRIVER_CLASS_MAP = [
        'self'          => \app\service\face\SelfDriver::class,
        'tencent'       => \app\service\face\TencentDriver::class,
        'baidu'         => \app\service\face\BaiduDriver::class,
        'alipay'        => \app\service\face\AlipayDriver::class,
        'juhe'          => \app\service\face\JuheDriver::class,
        'aliyun_market' => \app\service\face\AliyunMarketDriver::class,
    ];

    /**
     * 获取驱动实例
     */
    public function getDriver(string $driverCode): ?BaseDriver
    {
        if (isset($this->drivers[$driverCode])) {
            return $this->drivers[$driverCode];
        }

        if (!isset(self::DRIVER_CLASS_MAP[$driverCode])) {
            return null;
        }

        $className = self::DRIVER_CLASS_MAP[$driverCode];
        $driver = new $className();

        // 从数据库获取配置
        $dbDriver = FaceDriver::where('driver_code', $driverCode)->find();
        $config = [];
        if ($dbDriver) {
            $config = $dbDriver->getConfig();
        }

        // 初始化
        $driver->initialize($config);

        $this->drivers[$driverCode] = $driver;
        return $driver;
    }

    /**
     * 获取所有可用驱动
     */
    public function getAvailableDrivers(): array
    {
        $drivers = [];
        foreach (self::DRIVER_CLASS_MAP as $code => $class) {
            $driver = $this->getDriver($code);
            if ($driver && $driver->isInitialized()) {
                $drivers[$code] = $driver;
            }
        }
        return $drivers;
    }

    /**
     * 执行活体检测（自动主备切换）
     */
    public function detectLiveness(string $imageBase64, array $actionFrames = [], array $options = []): array
    {
        // 获取主驱动
        $primaryCode = $options['driver'] ?? null;
        if (!$primaryCode) {
            $defaultDriver = FaceDriver::getDefaultDriver();
            $primaryCode = $defaultDriver ? $defaultDriver->driver_code : 'self';
        }

        $primaryDriver = $this->getDriver($primaryCode);

        if ($primaryDriver && $primaryDriver->isInitialized()) {
            $result = $primaryDriver->detectLiveness($imageBase64, $actionFrames, $options);

            if ($result['success']) {
                $result['driver_used'] = $primaryCode;
                return $result;
            }

            Log::warning("主驱动 {$primaryCode} 活体检测失败: " . ($result['message'] ?? ''));
        }

        // 主驱动失败，尝试备用驱动
        $fallbackCode = $options['fallback'] ?? null;
        if (!$fallbackCode) {
            $enabledDrivers = FaceDriver::getEnabledDrivers();
            foreach ($enabledDrivers as $dbDriver) {
                if ($dbDriver['driver_code'] !== $primaryCode) {
                    $fallbackCode = $dbDriver['driver_code'];
                    break;
                }
            }
        }

        if ($fallbackCode) {
            $fallbackDriver = $this->getDriver($fallbackCode);
            if ($fallbackDriver && $fallbackDriver->isInitialized()) {
                $result = $fallbackDriver->detectLiveness($imageBase64, $actionFrames, $options);
                $result['driver_used'] = $fallbackCode;
                $result['fallback'] = true;
                return $result;
            }
        }

        return [
            'success' => false,
            'liveness_score' => 0,
            'message' => '所有可用驱动均检测失败',
            'driver_used' => $primaryCode,
        ];
    }

    /**
     * 执行人脸比对
     */
    public function compareFace(string $imageBase64, string $referenceBase64, array $options = []): array
    {
        $primaryCode = $options['driver'] ?? 'self';
        $driver = $this->getDriver($primaryCode);

        if ($driver && $driver->isInitialized()) {
            return $driver->compareFace($imageBase64, $referenceBase64);
        }

        return [
            'success' => false,
            'compare_score' => 0,
            'message' => '没有可用的驱动',
        ];
    }

    /**
     * 测试驱动连接
     */
    public function testDriver(string $driverCode, array $config = []): array
    {
        if (!isset(self::DRIVER_CLASS_MAP[$driverCode])) {
            return ['success' => false, 'message' => '未知驱动: ' . $driverCode];
        }

        $className = self::DRIVER_CLASS_MAP[$driverCode];
        $driver = new $className();
        $driver->initialize($config);

        return $driver->testConnection();
    }

    /**
     * 获取所有支持的驱动列表
     */
    public static function getDriverList(): array
    {
        $list = [];
        foreach (self::DRIVER_CLASS_MAP as $code => $class) {
            $driver = new $class();
            $list[] = [
                'code' => $code,
                'name' => $driver->getDriverName(),
            ];
        }
        return $list;
    }
}