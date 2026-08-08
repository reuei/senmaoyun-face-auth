<?php
/**
 * 数据加密服务（AES-256-GCM）
 */
class Encrypt
{
    private $key;
    private $method = 'aes-256-gcm';

    public function __construct()
    {
        $key = FACE_ENCRYPTION_KEY;
        if (empty($key)) {
            $key = base64_encode(random_bytes(32));
            // 尝试保存到数据库
            try {
                $db = db();
                $existing = $db->fetch("SELECT `value` FROM {$db->table('setting')} WHERE `key` = 'face_encryption_key'");
                if ($existing) {
                    $key = $existing['value'];
                } else {
                    $db->insert($db->table('setting'), [
                        'key' => 'face_encryption_key',
                        'value' => $key,
                        'type' => 'string',
                        'group' => 'security',
                    ]);
                }
            } catch (\Throwable $e) {}
        }
        $this->key = substr(hash('sha256', $key, true), 0, 32);
    }

    public function encrypt($plaintext)
    {
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($plaintext, $this->method, $this->key, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
        return base64_encode($iv . $tag . $ciphertext);
    }

    public function decrypt($encrypted)
    {
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, 12);
        $tag = substr($data, 12, 16);
        $ciphertext = substr($data, 28);
        return openssl_decrypt($ciphertext, $this->method, $this->key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}