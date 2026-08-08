<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 人脸识别API
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\api;

use app\controller\Base;
use app\service\FaceManager;
use app\service\EncryptionService;
use app\model\CertifyRecord;
use app\model\CertifyToken;
use app\model\RateLimit;
use think\facade\Log;

class Face extends Base
{
    /**
     * 初始化认证会话
     * POST /api/face/init
     */
    public function init()
    {
        $token  = request()->post('token', '');
        $name   = request()->post('name', '');
        $idCard = request()->post('id_card', '');

        if (empty($token) || empty($name) || empty($idCard)) {
            return $this->error('参数不完整');
        }

        // 验证Token
        $tokenRecord = CertifyToken::verifyToken($token);
        if (!$tokenRecord) {
            return $this->error('Token无效或已过期');
        }

        // 速率限制
        $ip = request()->ip();
        if (!RateLimit::check($ip, 'face_init', 10)) {
            return $this->error('请求过于频繁，请稍后再试');
        }

        // 创建认证记录
        $encryption = new EncryptionService();
        $record = CertifyRecord::create([
            'record_no' => CertifyRecord::generateRecordNo(),
            'user_id'   => $tokenRecord->user_id,
            'name'      => $encryption->encrypt($name),
            'id_card'   => $encryption->encrypt($idCard),
            'status'    => CertifyRecord::STATUS_PROCESSING,
            'ip_address'=> $ip,
            'user_agent'=> request()->header('user-agent', ''),
        ]);

        return $this->success([
            'record_no' => $record->record_no,
            'session_id' => $record->id,
        ], '认证会话已创建');
    }

    /**
     * 上传人脸图片
     * POST /api/face/upload
     */
    public function upload()
    {
        $recordNo = request()->post('record_no', '');
        $image    = request()->post('image', ''); // Base64

        if (empty($recordNo) || empty($image)) {
            return $this->error('缺少必要参数');
        }

        $record = CertifyRecord::where('record_no', $recordNo)->find();
        if (!$record) {
            return $this->error('认证记录不存在');
        }

        // 限制重试次数
        $maxRetry = (int) \app\model\Setting::getValue('max_retry', 3);
        if ($record->retry_count >= $maxRetry) {
            $record->status = CertifyRecord::STATUS_AUDITING;
            $record->save();
            return $this->error('认证次数已达上限，已转人工审核');
        }

        // 保存图片
        $uploadDir = runtime_path() . 'face/' . date('Ymd') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageData = base64_decode($image);
        $imageName = $recordNo . '_' . time() . '.jpg';
        file_put_contents($uploadDir . $imageName, $imageData);

        // 更新记录
        $record->face_image = 'face/' . date('Ymd') . '/' . $imageName;
        $record->retry_count = $record->retry_count + 1;
        $record->save();

        return $this->success([
            'image_path' => $record->face_image,
        ], '图片上传成功');
    }

    /**
     * 执行活体检测动作
     * POST /api/face/action
     */
    public function action()
    {
        $recordNo    = request()->post('record_no', '');
        $actionType  = request()->post('action_type', ''); // blink/open_mouth/nod_head/shake_head
        $imageBase64 = request()->post('image', '');
        $actionFrames = request()->post('action_frames', []);

        if (empty($recordNo) || empty($imageBase64)) {
            return $this->error('缺少必要参数');
        }

        $record = CertifyRecord::where('record_no', $recordNo)->find();
        if (!$record) {
            return $this->error('认证记录不存在');
        }

        // 保存动作帧
        $uploadDir = runtime_path() . 'face/' . date('Ymd') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $actionImageName = $recordNo . '_' . $actionType . '_' . time() . '.jpg';
        file_put_contents($uploadDir . $actionImageName, base64_decode($imageBase64));

        // 执行活体检测
        $manager = new FaceManager();
        $result = $manager->detectLiveness(
            $imageBase64,
            $actionFrames,
            ['driver' => $record->driver_code ?: null]
        );

        return $this->success([
            'action'         => $actionType,
            'liveness_score' => $result['liveness_score'] ?? 0,
            'passed'         => $result['success'] ?? false,
            'message'        => $result['message'] ?? '',
        ]);
    }

    /**
     * 获取认证结果
     * POST /api/face/result
     */
    public function result()
    {
        $recordNo = request()->post('record_no', '');
        $finalImage = request()->post('image', '');

        if (empty($recordNo)) {
            return $this->error('缺少记录编号');
        }

        $record = CertifyRecord::where('record_no', $recordNo)->find();
        if (!$record) {
            return $this->error('认证记录不存在');
        }

        // 执行最终活体检测
        $manager = new FaceManager();
        $livenessResult = $manager->detectLiveness($finalImage, []);

        $livenessThreshold = (float) \app\model\Setting::getValue('liveness_threshold', 80);

        if ($livenessResult['success'] && $livenessResult['liveness_score'] >= $livenessThreshold) {
            $record->status = CertifyRecord::STATUS_SUCCESS;
            $record->liveness_score = $livenessResult['liveness_score'];
            $record->driver_code = $livenessResult['driver_used'] ?? '';
            $record->certify_time = date('Y-m-d H:i:s');

            // 生成回调Token
            $callbackToken = CertifyToken::generateCallbackToken($record->user_id, $record->id);

            $record->save();

            // 回调魔方财务
            $this->callbackMofang($record, $callbackToken);

            return $this->success([
                'status'  => 'success',
                'token'   => $callbackToken,
                'message' => '实人认证通过',
            ]);
        }

        // 失败处理
        $record->status = CertifyRecord::STATUS_FAILED;
        $record->liveness_score = $livenessResult['liveness_score'] ?? 0;
        $record->fail_reason = $livenessResult['message'] ?? '活体检测未通过';
        $record->save();

        $maxRetry = (int) \app\model\Setting::getValue('max_retry', 3);
        if ($record->retry_count >= $maxRetry) {
            $record->status = CertifyRecord::STATUS_AUDITING;
            $record->save();
            return $this->error('认证次数已达上限，已转人工审核');
        }

        return $this->error($record->fail_reason);
    }

    /**
     * 回调魔方财务系统
     */
    private function callbackMofang(CertifyRecord $record, string $callbackToken): void
    {
        $mofangUrl = \app\model\Setting::getValue('mofang_url', '');
        if (empty($mofangUrl)) {
            Log::warning('魔方财务回调地址未配置');
            return;
        }

        try {
            $encryption = new EncryptionService();
            $client = new \GuzzleHttp\Client(['timeout' => 10]);

            $response = $client->post($mofangUrl . '/api/certify/callback', [
                'json' => [
                    'token'      => $callbackToken,
                    'user_id'    => $record->user_id,
                    'status'     => 'success',
                    'timestamp'  => time(),
                    'sign'       => hash_hmac('sha256', $callbackToken . $record->user_id, env('app.api_secret', '')),
                ],
            ]);

            $result = $response->getBody()->getContents();
            $record->callback_status = 'success';
            $record->callback_response = $result;
        } catch (\Throwable $e) {
            Log::error('魔方财务回调失败: ' . $e->getMessage());
            $record->callback_status = 'failed';
            $record->callback_response = $e->getMessage();
        }

        $record->save();
    }
}