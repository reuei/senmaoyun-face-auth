<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 数据加密服务
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service;

/**
 * 数据加密服务（AES-256-GCM）
 */
class EncryptionService
{
    /**
     * 加密密钥
     */
    private string $key;

    /**
     * 加密方法
     */
    private string $method = 'aes-256-gcm';

    /**
     * 构造函数
     */
    public function __construct()
    {
        // 从环境变量或配置获取密钥
        $key = env('security.encryption_key', '');
        if (empty($key)) {
            $key = \app\model\Setting::getValue('face_encryption_key', '');
        }
        if (empty($key)) {
            // 自动生成密钥并保存
            $key = base64_encode(random_bytes(32));
            \app\model\Setting::setValue('face_encryption_key', $key);
        }

        // 确保密钥长度为32字节
        $this->key = substr(hash('sha256', $key, true), 0, 32);
    }

    /**
     * 加密
     */
    public function encrypt(string $plaintext): string
    {
        $iv = random_bytes(12); // GCM 推荐 12 字节 IV
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            $this->method,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('加密失败');
        }

        // 返回 base64(iv + tag + ciphertext)
        return base64_encode($iv . $tag . $ciphertext);
    }

    /**
     * 解密
     */
    public function decrypt(string $encrypted): string
    {
        $data = base64_decode($encrypted);
        if ($data === false || strlen($data) < 28) {
            throw new \RuntimeException('无效的加密数据');
        }

        $iv         = substr($data, 0, 12);
        $tag        = substr($data, 12, 16);
        $ciphertext = substr($data, 28);

        $plaintext = openssl_decrypt(
            $ciphertext,
            $this->method,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new \RuntimeException('解密失败');
        }

        return $plaintext;
    }

    /**
     * 加密API密钥（用于存储第三方接口密钥）
     */
    public function encryptApiKey(string $apiKey): string
    {
        return $this->encrypt($apiKey);
    }

    /**
     * 解密API密钥
     */
    public function decryptApiKey(string $encryptedApiKey): string
    {
        return $this->decrypt($encryptedApiKey);
    }
}