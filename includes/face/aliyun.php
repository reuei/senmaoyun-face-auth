<?php
class AliyunMarketDriver
{
    private $appCode;
    public function __construct($config = []) { $this->appCode = $config['app_code'] ?? ''; }
    public function detectLiveness($imageBase64, $actionFrames = [], $options = [])
    {
        if (empty($this->appCode)) return ['success' => false, 'liveness_score' => 0, 'message' => '阿里云市场未配置'];
        $ch = curl_init('https://faceverify.market.alicloudapi.com/verify');
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode(['image' => $imageBase64, 'type' => 'liveness']),
            CURLOPT_HTTPHEADER => ['Authorization: APPCODE ' . $this->appCode, 'Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30,
        ]);
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if (isset($r['status']) && $r['status'] == 200) {
            $score = (float)($r['liveness_score'] ?? $r['score'] ?? 0);
            return ['success' => $score >= 80, 'liveness_score' => $score, 'message' => $score >= 80 ? '活体检测通过' : '活体检测未通过'];
        }
        return ['success' => false, 'liveness_score' => 0, 'message' => '阿里云市场: ' . ($r['message'] ?? '未知错误')];
    }
}