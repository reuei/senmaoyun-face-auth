<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 聚合数据活体检测驱动
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service\face;

use GuzzleHttp\Client;
use think\facade\Log;

class JuheDriver extends BaseDriver
{
    protected string $driverCode = 'juhe';
    protected string $driverName = '聚合数据活体检测';

    private ?Client $httpClient = null;
    private string $apiKey = '';

    public function initialize(array $config = []): bool
    {
        $this->config = $config;
        $this->apiKey = $config['api_key'] ?? '';

        if (empty($this->apiKey)) {
            return false;
        }

        $this->httpClient = new Client([
            'base_uri' => 'https://apis.juhe.cn',
            'timeout'  => 30,
        ]);

        $this->initialized = true;
        return true;
    }

    public function detectLiveness(string $imageBase64, array $actionFrames = [], array $options = []): array
    {
        if (!$this->initialized) {
            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '聚合数据驱动未初始化',
            ];
        }

        try {
            $response = $this->httpClient->post('/faceAnti/queryV2', [
                'form_params' => [
                    'key'   => $this->apiKey,
                    'image' => $imageBase64,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['error_code']) && $result['error_code'] !== 0) {
                Log::error('聚合数据错误: ' . json_encode($result));
                return [
                    'success' => false,
                    'liveness_score' => 0,
                    'message' => '聚合数据: ' . ($result['reason'] ?? '未知错误'),
                ];
            }

            $data  = $result['result'] ?? [];
            $score = $data['score'] ?? 0;
            $score = (float) $score;

            return [
                'success'        => $score >= 80,
                'liveness_score' => $score,
                'message'        => $score >= 80 ? '活体检测通过' : '活体检测未通过',
                'details'        => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('聚合数据异常: ' . $e->getMessage());
            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '聚合数据服务异常: ' . $e->getMessage(),
            ];
        }
    }

    public function compareFace(string $imageBase64, string $referenceImageBase64): array
    {
        return [
            'success' => false,
            'compare_score' => 0,
            'message' => '聚合数据驱动不支持直接人脸比对',
        ];
    }

    public function detect(array $params): array
    {
        $imageBase64 = $params['image'] ?? '';
        return $this->detectLiveness($imageBase64);
    }

    public function testConnection(): array
    {
        if (!$this->initialized) {
            return ['success' => false, 'message' => '请先配置API Key'];
        }

        try {
            $response = $this->httpClient->post('/faceAnti/queryV2', [
                'form_params' => [
                    'key'   => $this->apiKey,
                    'image' => 'test',
                ],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);

            // 聚合数据API Key无效时会返回错误码，但连接是通的
            if (isset($result['error_code'])) {
                return ['success' => true, 'message' => '聚合数据连接成功'];
            }

            return ['success' => true, 'message' => '聚合数据连接成功'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => '连接失败: ' . $e->getMessage()];
        }
    }
}