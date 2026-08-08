<?php
class JuheDriver
{
    private $apiKey;
    public function __construct($config = []) { $this->apiKey = $config['api_key'] ?? ''; }
    public function detectLiveness($imageBase64, $actionFrames = [], $options = [])
    {
        if (empty($this->apiKey)) return ['success' => false, 'liveness_score' => 0, 'message' => '聚合数据未配置'];
        $ch = curl_init('https://apis.juhe.cn/faceAnti/queryV2');
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query(['key' => $this->apiKey, 'image' => $imageBase64]),
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30,
        ]);
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if (isset($r['error_code']) && $r['error_code'] !== 0) {
            return ['success' => false, 'liveness_score' => 0, 'message' => '聚合数据: ' . ($r['reason'] ?? '未知错误')];
        }
        $score = (float)($r['result']['score'] ?? 0);
        return ['success' => $score >= 80, 'liveness_score' => $score, 'message' => $score >= 80 ? '活体检测通过' : '活体检测未通过'];
    }
}