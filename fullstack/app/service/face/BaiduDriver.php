<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 百度智能云人脸识别驱动
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service\face;

use GuzzleHttp\Client;
use think\facade\Log;

class BaiduDriver extends BaseDriver
{
    protected string $driverCode = 'baidu';
    protected string $driverName = '百度智能云人脸识别';

    private ?Client $httpClient = null;
    private string $apiKey    = '';
    private string $secretKey = '';
    private string $appId     = '';
    private string $accessToken = '';

    public function initialize(array $config = []): bool
    {
        $this->config    = $config;
        $this->apiKey    = $config['api_key'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
        $this->appId     = $config['app_id'] ?? '';

        if (empty($this->apiKey) || empty($this->secretKey)) {
            return false;
        }

        $this->httpClient = new Client([
            'base_uri' => 'https://aip.baidubce.com',
            'timeout'  => 30,
        ]);

        // 获取Access Token
        $token = $this->getAccessToken();
        if (empty($token)) {
            return false;
        }
        $this->accessToken = $token;

        $this->initialized = true;
        return true;
    }

    private function getAccessToken(): string
    {
        try {
            $response = $this->httpClient->post('/oauth/2.0/token', [
                'form_params' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->apiKey,
                    'client_secret' => $this->secretKey,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'] ?? '';
        } catch (\Throwable $e) {
            Log::error('百度获取AccessToken失败: ' . $e->getMessage());
            return '';
        }
    }

    public function detectLiveness(string $imageBase64, array $actionFrames = [], array $options = []): array
    {
        if (!$this->initialized) {
            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '百度智能云驱动未初始化',
            ];
        }

        try {
            $response = $this->httpClient->post('/rest/2.0/face/v1/faceverify', [
                'form_params' => [
                    'access_token'     => $this->accessToken,
                    'image'            => $imageBase64,
                    'image_type'       => 'BASE64',
                    'quality_control'  => 'NORMAL',
                    'liveness_control' => $options['liveness_control'] ?? 'NORMAL',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['error_code']) && $result['error_code'] !== 0) {
                Log::error('百度活体检测错误: ' . json_encode($result));
                return [
                    'success' => false,
                    'liveness_score' => 0,
                    'message' => '百度: ' . ($result['error_msg'] ?? '未知错误'),
                ];
            }

            $score = $result['result']['face_liveness'] ?? 0;
            $score = (float) $score * 100;

            return [
                'success'        => $score >= 80,
                'liveness_score' => round($score, 2),
                'message'        => $score >= 80 ? '活体检测通过' : '活体检测未通过',
                'details'        => $result['result'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error('百度活体检测异常: ' . $e->getMessage());
            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '百度服务异常: ' . $e->getMessage(),
            ];
        }
    }

    public function compareFace(string $imageBase64, string $referenceImageBase64): array
    {
        if (!$this->initialized) {
            return [
                'success' => false,
                'compare_score' => 0,
                'message' => '百度智能云驱动未初始化',
            ];
        }

        try {
            $response = $this->httpClient->post('/rest/2.0/face/v3/match', [
                'form_params' => [
                    [
                        'access_token' => $this->accessToken,
                        'image'        => $imageBase64,
                        'image_type'   => 'BASE64',
                        'quality_control' => 'LOW',
                    ],
                    [
                        'access_token' => $this->accessToken,
                        'image'        => $referenceImageBase64,
                        'image_type'   => 'BASE64',
                        'quality_control' => 'LOW',
                    ],
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['error_code']) && $result['error_code'] !== 0) {
                return [
                    'success' => false,
                    'compare_score' => 0,
                    'message' => '百度: ' . ($result['error_msg'] ?? '未知错误'),
                ];
            }

            $score = $result['result']['score'] ?? 0;

            return [
                'success'       => $score >= 80,
                'compare_score' => (float) $score,
                'message'       => $score >= 80 ? '人脸比对通过' : '人脸比对未通过',
            ];
        } catch (\Throwable $e) {
            Log::error('百度人脸比对异常: ' . $e->getMessage());
            return [
                'success' => false,
                'compare_score' => 0,
                'message' => '百度服务异常: ' . $e->getMessage(),
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
        $token = $this->getAccessToken();
        if (empty($token)) {
            return ['success' => false, 'message' => '无法获取Access Token，请检查API Key和Secret Key'];
        }
        return ['success' => true, 'message' => '百度智能云连接成功'];
    }
}