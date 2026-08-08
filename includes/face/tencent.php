<?php
/**
 * 腾讯云慧眼驱动
 * 需要配置 SecretId 和 SecretKey
 */
class TencentDriver
{
    private $secretId, $secretKey, $region;
    public function __construct($config = [])
    {
        $this->secretId = $config['secret_id'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
        $this->region = $config['region'] ?? 'ap-guangzhou';
    }

    public function detectLiveness($imageBase64, $actionFrames = [], $options = [])
    {
        if (empty($this->secretId) || empty($this->secretKey)) {
            return ['success' => false, 'liveness_score' => 0, 'message' => '腾讯云未配置密钥'];
        }
        $payload = json_encode(['VideoBase64' => $imageBase64, 'LivenessType' => 'ACTION']);
        $result = $this->call('LivenessRecognition', $payload);
        if (isset($result['Response']['Error'])) {
            return ['success' => false, 'liveness_score' => 0, 'message' => '腾讯云: ' . ($result['Response']['Error']['Message'] ?? '未知错误')];
        }
        $score = $result['Response']['BestFrameScore'] ?? $result['Response']['Score'] ?? 0;
        return ['success' => $score >= 80, 'liveness_score' => (float)$score, 'message' => $score >= 80 ? '活体检测通过' : '活体检测未通过'];
    }

    private function call($action, $payload)
    {
        $host = 'faceid.tencentcloudapi.com';
        $service = 'faceid';
        $timestamp = time();
        $date = gmdate('Y-m-d', $timestamp);
        $algorithm = 'TC3-HMAC-SHA256';

        $canonicalRequest = "POST\n/\n\ncontent-type:application/json\nhost:{$host}\n\ncontent-type;host\n" . hash('sha256', $payload);
        $stringToSign = "{$algorithm}\n{$timestamp}\n{$date}/{$service}/tc3_request\n" . hash('sha256', $canonicalRequest);

        $secretDate = hash_hmac('sha256', $date, 'TC3' . $this->secretKey, true);
        $secretService = hash_hmac('sha256', $service, $secretDate, true);
        $secretSigning = hash_hmac('sha256', 'tc3_request', $secretService, true);
        $signature = hash_hmac('sha256', $stringToSign, $secretSigning);

        $auth = "{$algorithm} Credential={$this->secretId}/{$date}/{$service}/tc3_request, SignedHeaders=content-type;host, Signature={$signature}";

        $ch = curl_init("https://{$host}/");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Authorization: {$auth}",
                "Content-Type: application/json",
                "Host: {$host}",
                "X-TC-Action: {$action}",
                "X-TC-Version: 2018-03-01",
                "X-TC-Timestamp: {$timestamp}",
                "X-TC-Region: {$this->region}",
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true) ?: [];
    }
}