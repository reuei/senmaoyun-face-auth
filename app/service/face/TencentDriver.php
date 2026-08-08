<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 腾讯云慧眼驱动
// | 参考腾讯云慧眼 API 文档
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service\face;

use GuzzleHttp\Client;
use think\facade\Log;

class TencentDriver extends BaseDriver
{
    protected string $driverCode = 'tencent';
    protected string $driverName = '腾讯云慧眼';

    private ?Client $httpClient = null;
    private string $secretId  = '';
    private string $secretKey = '';
    private string $region    = 'ap-guangzhou';

    public function initialize(array $config = []): bool
    {
        $this->config    = $config;
        $this->secretId  = $config['secret_id'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
        $this->region    = $config['region'] ?? 'ap-guangzhou';

        if (empty($this->secretId) || empty($this->secretKey)) {
            return false;
        }

        $this->httpClient = new Client([
            'base_uri' => 'https://faceid.tencentcloudapi.com',
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
                'message' => '腾讯云慧眼驱动未初始化',
            ];
        }

        try {
            $action = 'LivenessRecognition';
            $payload = json_encode([
                'VideoBase64' => $imageBase64,
                'LivenessType' => 'ACTION',
                'ValidateData' => '',
            ], JSON_UNESCAPED_UNICODE);

            $response = $this->sendRequest($action, $payload);

            if (isset($response['Response']['Error'])) {
                Log::error('腾讯云慧眼错误: ' . json_encode($response['Response']['Error']));
                return [
                    'success' => false,
                    'liveness_score' => 0,
                    'message' => '腾讯云慧眼: ' . ($response['Response']['Error']['Message'] ?? '未知错误'),
                ];
            }

            $result = $response['Response'] ?? [];
            $score  = $result['BestFrameScore'] ?? $result['Score'] ?? 0;

            return [
                'success'        => $score >= 80,
                'liveness_score' => (float) $score,
                'message'        => $score >= 80 ? '活体检测通过' : '活体检测未通过',
                'details'        => [
                    'request_id' => $result['RequestId'] ?? '',
                    'raw_score'  => $score,
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('腾讯云慧眼异常: ' . $e->getMessage());
            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '腾讯云慧眼服务异常: ' . $e->getMessage(),
            ];
        }
    }

    public function compareFace(string $imageBase64, string $referenceImageBase64): array
    {
        if (!$this->initialized) {
            return [
                'success' => false,
                'compare_score' => 0,
                'message' => '腾讯云慧眼驱动未初始化',
            ];
        }

        try {
            $action = 'CompareFace';
            $payload = json_encode([
                'ImageA' => $imageBase64,
                'ImageB' => $referenceImageBase64,
            ], JSON_UNESCAPED_UNICODE);

            $response = $this->sendRequest($action, $payload);

            if (isset($response['Response']['Error'])) {
                return [
                    'success' => false,
                    'compare_score' => 0,
                    'message' => '腾讯云慧眼: ' . ($response['Response']['Error']['Message'] ?? '未知错误'),
                ];
            }

            $result = $response['Response'] ?? [];
            $score  = $result['Score'] ?? 0;

            return [
                'success'       => $score >= 80,
                'compare_score' => (float) $score,
                'message'       => $score >= 80 ? '人脸比对通过' : '人脸比对未通过',
                'details'       => [
                    'request_id' => $result['RequestId'] ?? '',
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('腾讯云慧眼比对异常: ' . $e->getMessage());
            return [
                'success' => false,
                'compare_score' => 0,
                'message' => '腾讯云慧眼服务异常: ' . $e->getMessage(),
            ];
        }
    }

    public function detect(array $params): array
    {
        $imageBase64 = $params['image'] ?? '';
        return $this->detectLiveness($imageBase64);
    }

    public function testConnection(): array
    {
        if (!$this->initialized) {
            return ['success' => false, 'message' => '请先配置 SecretId 和 SecretKey'];
        }

        try {
            $action = 'GetLivenessResult';
            $payload = json_encode(['RuleId' => 0], JSON_UNESCAPED_UNICODE);
            $this->sendRequest($action, $payload);

            return ['success' => true, 'message' => '腾讯云慧眼连接成功'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => '连接失败: ' . $e->getMessage()];
        }
    }

    /**
     * 发送腾讯云API请求
     */
    private function sendRequest(string $action, string $payload): array
    {
        $service     = 'faceid';
        $host        = 'faceid.tencentcloudapi.com';
        $version     = '2018-03-01';
        $algorithm   = 'TC3-HMAC-SHA256';
        $timestamp   = (string) time();
        $date        = gmdate('Y-m-d', (int) $timestamp);

        // 构造规范请求
        $httpRequestMethod = 'POST';
        $canonicalUri      = '/';
        $canonicalQueryString = '';
        $canonicalHeaders  = "content-type:application/json\nhost:{$host}\n";
        $signedHeaders     = 'content-type;host';
        $hashedRequestPayload = hash('sha256', $payload);
        $canonicalRequest  = "{$httpRequestMethod}\n{$canonicalUri}\n{$canonicalQueryString}\n{$canonicalHeaders}\n{$signedHeaders}\n{$hashedRequestPayload}";

        // 构造签名字符串
        $credentialScope   = "{$date}/{$service}/tc3_request";
        $hashedCanonicalRequest = hash('sha256', $canonicalRequest);
        $stringToSign      = "{$algorithm}\n{$timestamp}\n{$credentialScope}\n{$hashedCanonicalRequest}";

        // 计算签名
        $secretDate    = hash_hmac('sha256', $date, 'TC3' . $this->secretKey, true);
        $secretService = hash_hmac('sha256', $service, $secretDate, true);
        $secretSigning = hash_hmac('sha256', 'tc3_request', $secretService, true);
        $signature     = hash_hmac('sha256', $stringToSign, $secretSigning);

        // 构造 Authorization
        $authorization = "{$algorithm} Credential={$this->secretId}/{$credentialScope}, SignedHeaders={$signedHeaders}, Signature={$signature}";

        $response = $this->httpClient->post('/', [
            'headers' => [
                'Authorization'  => $authorization,
                'Content-Type'   => 'application/json',
                'Host'           => $host,
                'X-TC-Action'    => $action,
                'X-TC-Version'   => $version,
                'X-TC-Timestamp' => $timestamp,
                'X-TC-Region'    => $this->region,
            ],
            'body' => $payload,
        ]);

        return json_decode($response->getBody()->getContents(), true) ?: [];
    }
}