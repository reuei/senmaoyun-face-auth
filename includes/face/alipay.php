<?php
class AlipayDriver
{
    private $appId, $privateKey, $alipayPublicKey;
    public function __construct($config = [])
    {
        $this->appId = $config['app_id'] ?? '';
        $this->privateKey = $config['private_key'] ?? '';
        $this->alipayPublicKey = $config['alipay_public_key'] ?? '';
    }
    public function detectLiveness($imageBase64, $actionFrames = [], $options = [])
    {
        if (empty($this->appId)) return ['success' => false, 'liveness_score' => 0, 'message' => '支付宝未配置'];
        $biz = ['biz_code' => 'FACE_CHECK', 'face_contrast_picture' => $imageBase64];
        $params = [
            'app_id' => $this->appId, 'method' => 'datadigital.fincloud.generalsaas.face.check',
            'format' => 'JSON', 'charset' => 'utf-8', 'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'), 'version' => '1.0',
            'biz_content' => json_encode($biz, JSON_UNESCAPED_UNICODE),
        ];
        ksort($params);
        $str = '';
        foreach ($params as $k => $v) { if ($k !== 'sign' && $v !== '') $str .= $k . '=' . $v . '&'; }
        $str = rtrim($str, '&');
        $priv = openssl_pkey_get_private($this->privateKey);
        openssl_sign($str, $sig, $priv, OPENSSL_ALGO_SHA256);
        $params['sign'] = base64_encode($sig);
        $ch = curl_init('https://openapi.alipay.com/gateway.do');
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($params), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30]);
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $data = $r['alipay_base_face_check_response'] ?? $r;
        if (($data['code'] ?? '') == '10000') {
            $passed = ($data['passed'] ?? '') === 'T';
            return ['success' => $passed, 'liveness_score' => $passed ? 95.0 : 50.0, 'message' => $passed ? '活体检测通过' : '活体检测未通过'];
        }
        return ['success' => false, 'liveness_score' => 0, 'message' => '支付宝: ' . ($data['sub_msg'] ?? $data['msg'] ?? '未知错误')];
    }
}