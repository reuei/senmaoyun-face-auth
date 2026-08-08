<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 人脸识别驱动基类
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service\face;

/**
 * 人脸识别驱动抽象基类
 * 所有第三方和自研驱动均需继承此类
 */
abstract class BaseDriver
{
    /**
     * 驱动代码
     */
    protected string $driverCode = '';

    /**
     * 驱动名称
     */
    protected string $driverName = '';

    /**
     * 驱动配置
     */
    protected array $config = [];

    /**
     * 是否已初始化
     */
    protected bool $initialized = false;

    /**
     * 获取驱动代码
     */
    public function getDriverCode(): string
    {
        return $this->driverCode;
    }

    /**
     * 获取驱动名称
     */
    public function getDriverName(): string
    {
        return $this->driverName;
    }

    /**
     * 获取配置
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * 是否已初始化
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * 初始化驱动
     * @param array $config 配置参数
     * @return bool
     */
    abstract public function initialize(array $config = []): bool;

    /**
     * 执行活体检测
     * @param string $imageBase64 人脸图片 Base64
     * @param array $actionFrames 动作帧数据
     * @param array $options 额外参数
     * @return array ['success'=>bool, 'liveness_score'=>float, 'message'=>string, 'details'=>array]
     */
    abstract public function detectLiveness(string $imageBase64, array $actionFrames = [], array $options = []): array;

    /**
     * 执行人脸比对
     * @param string $imageBase64 人脸图片 Base64
     * @param string $referenceImageBase64 参考图片 Base64
     * @return array ['success'=>bool, 'compare_score'=>float, 'message'=>string]
     */
    abstract public function compareFace(string $imageBase64, string $referenceImageBase64): array;

    /**
     * 综合检测（活体 + 比对）
     * @param array $params 参数
     * @return array
     */
    abstract public function detect(array $params): array;

    /**
     * 测试连接
     * @return array ['success'=>bool, 'message'=>string]
     */
    abstract public function testConnection(): array;
}