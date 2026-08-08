<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 支付宝活体检测驱动
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service\face;

use GuzzleHttp\Client;
use think\facade\Log;

class AlipayDriver extends BaseDriver
{
    protected string $driverCode = 'alipay';
    protected string $driverName = '支付宝活体检测';

    private ?Client $httpClient = null;
    private string $appId          = '';
    private string $privateKey     = '';
    private string $alipayPublicKey = '';
    private string $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    public function initialize(array $config = []): bool
    {
        $this->config          = $config;
        $this->appId           = $config['app_id'] ?? '';
        $this->privateKey      = $config['private_key'] ?? '';
        $this->alipayPublicKey = $config['alipay_public_key'] ?? '';

        if (empty($this->appId) || empty($this->privateKey) || empty($this->alipayPublicKey)) {
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
                'message' => '支付宝驱动未初始化',
            ];
        }

        try {
            // 支付宝活体检测需要两步：初始化 + 查询
            $bizContent = [
                'biz_code'          => 'FACE_CHECK',
                'certify_id'        => $options['certify_id'] ?? '',
                'scene_id'          => $options['scene_id'] ?? '',
                'face_contrast_picture' => $imageBase64,
            ];

            $params = $this->buildParams('datadigital.fincloud.generalsaas.face.check', $bizContent);

            $response = $this->httpClient->post($this->gatewayUrl, [
                'form_params' => $params,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            $responseData = $result['alipay_base_face_check_response'] ?? $result;

            if (isset($responseData['code']) && $responseData['code'] == '10000') {
                $passed = ($responseData['passed'] ?? '') === 'T';
                $score  = $passed ? 95.0 : 50.0;

                return [
                    'success'        => $passed,
                    'liveness_score' => $score,
                    'message'        => $passed ? '活体检测通过' : '活体检测未通过',
                    'details'        => $responseData,
                ];
            }

            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '支付宝: ' . ($responseData['sub_msg'] ?? $responseData['msg'] ?? '未知错误'),
            ];
        } catch (\Throwable $e) {
            Log::error('支付宝活体检测异常: ' . $e->getMessage());
            return [
                'success' => false,
                'liveness_score' => 0,
                'message' => '支付宝服务异常: ' . $e->getMessage(),
            ];
        }
    }

    public function compareFace(string $imageBase64, string $referenceImageBase64): array
    {
        return [
            'success' => false,
            'compare_score' => 0,
            'message' => '支付宝驱动不支持直接人脸比对，请使用活体检测',
        ];
    }

    public function detect(array $params): array
    {
        $imageBase64 = $params['image'] ?? '';
        return $this->detectLiveness($imageBase64, [], $params);
    }

    public function testConnection(): array
    {
        if (!$this->initialized) {
            return ['success' => false, 'message' => '请先配置支付宝AppId和密钥'];
        }

        try {
            $params = $this->buildParams('alipay.system.oauth.token', ['grant_type' => 'test']);
            $response = $this->httpClient->post($this->gatewayUrl, ['form_params' => $params]);
            $result = json_decode($response->getBody()->getContents(), true);

            return ['success' => true, 'message' => '支付宝连接成功'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => '连接失败: ' . $e->getMessage()];
        }
    }

    private function buildParams(string $method, array $bizContent): array
    {
        $params = [
            'app_id'      => $this->appId,
            'method'      => $method,
            'format'      => 'JSON',
            'charset'     => 'utf-8',
            'sign_type'   => 'RSA2',
            'timestamp'   => date('Y-m-d H:i:s'),
            'version'     => '1.0',
            'biz_content' => json_encode($bizContent, JSON_UNESCAPED_UNICODE),
        ];

        $params['sign'] = $this->generateSign($params);
        return $params;
    }

    private function generateSign(array $params): string
    {
        ksort($params);
        $stringToSign = '';
        foreach ($params as $k => $v) {
            if ($k !== 'sign' && $v !== '' && !is_array($v) && !is_null($v)) {
                $stringToSign .= $k . '=' . $v . '&';
            }
        }
        $stringToSign = rtrim($stringToSign, '&');

        $privateKey = openssl_pkey_get_private($this->privateKey);
        if (!$privateKey) {
            throw new \RuntimeException('支付宝私钥格式错误');
        }

        openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKey);

        return base64_encode($signature);
    }
}