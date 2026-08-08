<?php
namespace app\controller\api;

use app\controller\Base;
use extend\face\SelfDriver;
use extend\IdCardValidator;
use app\model\CertifyRecord;
use app\model\CertifyToken;

class Face extends Base
{
    public function init()
    {
        $token = request()->post('token', '');
        $name = request()->post('name', '');
        $idCard = request()->post('id_card', '');
        if (empty($token) || empty($name) || empty($idCard)) return $this->error('参数不完整');

        $tokenRecord = CertifyToken::where('token', $token)->where('type', 'request')
            ->where('expire_time', '>', date('Y-m-d H:i:s'))->find();
        if (!$tokenRecord) return $this->error('Token无效');

        $recordNo = date('YmdHis') . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
        CertifyRecord::create([
            'record_no' => $recordNo, 'user_id' => $tokenRecord->user_id,
            'name' => $name, 'id_card' => $idCard, 'status' => 'processing',
            'ip_address' => request()->ip(), 'user_agent' => request()->header('user-agent', ''),
        ]);
        return $this->success(['record_no' => $recordNo], '认证会话已创建');
    }

    public function action()
    {
        $recordNo = request()->post('record_no', '');
        $actionType = request()->post('action_type', '');
        $image = request()->post('image', '');
        if (empty($recordNo) || empty($image)) return $this->error('参数不完整');

        $record = CertifyRecord::where('record_no', $recordNo)->find();
        if (!$record) return $this->error('记录不存在');

        $dir = app()->getRuntimePath() . 'face/' . date('Ymd') . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
        $imgName = $recordNo . '_' . $actionType . '_' . time() . '.jpg';
        file_put_contents($dir . $imgName, $imgData);

        $record->face_image = 'face/' . date('Ymd') . '/' . $imgName;
        $record->retry_count = $record->retry_count + 1;
        $record->save();

        return $this->success(['action' => $actionType, 'liveness_score' => 85, 'passed' => true]);
    }

    public function result()
    {
        $recordNo = request()->post('record_no', '');
        $image = request()->post('image', '');
        if (empty($recordNo)) return $this->error('缺少记录编号');

        $record = CertifyRecord::where('record_no', $recordNo)->find();
        if (!$record) return $this->error('记录不存在');

        $driver = new SelfDriver();
        $result = $driver->detectLiveness($image);
        $score = $result['liveness_score'];
        $passed = $result['success'] && $score >= 80;
        $status = $passed ? 'success' : 'failed';

        if (!$passed && $record->retry_count >= 3) $status = 'auditing';

        $record->status = $status;
        $record->liveness_score = $score;
        $record->driver_code = 'self';
        $record->certify_time = $passed ? date('Y-m-d H:i:s') : null;
        $record->fail_reason = $passed ? '' : ($result['message'] ?? '活体检测未通过');
        $record->save();

        if ($passed) {
            $cbToken = hash('sha256', random_bytes(32) . microtime(true) . $record->user_id);
            CertifyToken::create([
                'token' => $cbToken, 'type' => 'callback', 'user_id' => $record->user_id,
                'expire_time' => date('Y-m-d H:i:s', time() + 600), 'record_id' => $record->id,
            ]);
            return $this->success(['callback_token' => $cbToken], '实人认证通过');
        }
        return $this->error($status === 'auditing' ? '已转人工审核' : ($result['message'] ?? '活体检测未通过'));
    }
}