<?php
/**
 * 百度智能云人脸识别驱动
 */
class BaiduDriver
{
    private $apiKey, $secretKey, $appId, $accessToken = '';
    public function __construct($config = [])
    {
        $this->apiKey = $config['api_key'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
        $this->appId = $config['app_id'] ?? '';
    }
    private function getToken()
    {
        if ($this->accessToken) return $this->accessToken;
        $ch = curl_init('https://aip.baidubce.com/oauth/2.0/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(['grant_type' => 'client_credentials', 'client_id' => $this->apiKey, 'client_secret' => $this->secretKey]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $this->accessToken = $r['access_token'] ?? '';
    }
    public function detectLiveness($imageBase64, $actionFrames = [], $options = [])
    {
        $token = $this->getToken();
        if (!$token) return ['success' => false, 'liveness_score' => 0, 'message' => '百度获取Token失败'];
        $ch = curl_init('https://aip.baidubce.com/rest/2.0/face/v1/faceverify');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(['access_token' => $token, 'image' => $imageBase64, 'image_type' => 'BASE64', 'liveness_control' => 'NORMAL']),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if (isset($r['error_code']) && $r['error_code'] !== 0) {
            return ['success' => false, 'liveness_score' => 0, 'message' => '百度: ' . ($r['error_msg'] ?? '未知错误')];
        }
        $score = ($r['result']['face_liveness'] ?? 0) * 100;
        return ['success' => $score >= 80, 'liveness_score' => round($score, 1), 'message' => $score >= 80 ? '活体检测通过' : '活体检测未通过'];
    }
}