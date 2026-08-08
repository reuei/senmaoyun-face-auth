<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 阿里云市场活体检测驱动
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service\face;

use GuzzleHttp\Client;
use think\facade\Log;

class AliyunMarketDriver extends BaseDriver
{
    protected string $driverCode = 'aliyun_market';
    protected string $driverName = '阿里云市场活体检测';

    private ?Client $httpClient = null;
    private string $appCode = '';

    public function initialize(array $config = []): bool
    {
        $this->config  = $config;
        $this->appCode = $config['app_code'] ?? '';

        if (empty($this->appCode)) {
            return false;
        }

        $this->httpClient = new Client(['timeout' => 30]);
        $this->initialized = true;
        return true;
    }

    public function detectLiveness(string $imageBase64, array $actionFrames = [], array $options = []): array
    {
        if (!$this->initialized) {
            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '阿里云市场驱动未初始化',
            ];
        }

        try {
            // 阿里云市场API通用调用
            $response = $this->httpClient->post($options['api_url'] ?? 'https://faceverify.market.alicloudapi.com/verify', [
                'headers' => [
                    'Authorization' => 'APPCODE ' . $this->appCode,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'image' => $imageBase64,
                    'type'  => 'liveness',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['status']) && $result['status'] == 200) {
                $score = (float) ($result['liveness_score'] ?? $result['score'] ?? 0);
                return [
                    'success'        => $score >= 80,
                    'liveness_score' => $score,
                    'message'        => $score >= 80 ? '活体检测通过' : '活体检测未通过',
                    'details'        => $result,
                ];
            }

            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '阿里云市场: ' . ($result['message'] ?? '未知错误'),
            ];
        } catch (\Throwable $e) {
            Log::error('阿里云市场异常: ' . $e->getMessage());
            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '阿里云市场服务异常: ' . $e->getMessage(),
            ];
        }
    }

    public function compareFace(string $imageBase64, string $referenceImageBase64): array
    {
        if (!$this->initialized) {
            return [
                'success' => false,
                'compare_score' => 0,
                'message' => '阿里云市场驱动未初始化',
            ];
        }

        try {
            $response = $this->httpClient->post('https://facecompare.market.alicloudapi.com/compare', [
                'headers' => [
                    'Authorization' => 'APPCODE ' . $this->appCode,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'image_a' => $imageBase64,
                    'image_b' => $referenceImageBase64,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['status']) && $result['status'] == 200) {
                $score = (float) ($result['similarity'] ?? $result['score'] ?? 0);
                return [
                    'success'       => $score >= 80,
                    'compare_score' => $score,
                    'message'       => $score >= 80 ? '人脸比对通过' : '人脸比对未通过',
                ];
            }

            return [
                'success' => false,
                'compare_score' => 0,
                'message' => '阿里云市场: ' . ($result['message'] ?? '未知错误'),
            ];
        } catch (\Throwable $e) {
            Log::error('阿里云市场比对异常: ' . $e->getMessage());
            return [
                'success' => false,
                'compare_score' => 0,
                'message' => '阿里云市场服务异常: ' . $e->getMessage(),
            ];
        }
    }

    public function detect(array $params): array
    {
        $imageBase64 = $params['image'] ?? '';
        return $this->detectLiveness($imageBase64, [], $params);
    }

    public function testConnection(): array
    {
        if (!$this->initialized) {
            return ['success' => false, 'message' => '请先配置AppCode'];
        }
        return ['success' => true, 'message' => '阿里云市场连接就绪（需确认API地址）'];
    }
}