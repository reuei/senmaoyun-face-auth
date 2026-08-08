<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 人工审核控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\CertifyRecord;
use app\model\CertifyToken;

class Audit extends Base
{
    /**
     * 审核列表
     */
    public function index()
    {
        return view('admin/audit', [
            'site_name' => config('app.site_name'),
            'admin'     => $this->getAdmin(),
        ]);
    }

    /**
     * 审核详情
     */
    public function detail($id)
    {
        $record = CertifyRecord::find($id);
        if (!$record) {
            return $this->error('记录不存在');
        }

        $data = $record->toArray();
        $data['masked_name']   = $record->getMaskedName();
        $data['masked_idcard'] = $record->getMaskedIdCard();

        return $this->success($data);
    }

    /**
     * 审核通过
     */
    public function pass()
    {
        $id = request()->post('id', 0);

        $record = CertifyRecord::find($id);
        if (!$record) {
            return $this->error('记录不存在');
        }
        if ($record->status !== CertifyRecord::STATUS_AUDITING) {
            return $this->error('该记录不在审核队列中');
        }

        $record->status = CertifyRecord::STATUS_SUCCESS;
        $record->certify_time = date('Y-m-d H:i:s');
        $record->save();

        // 生成回调Token
        $callbackToken = CertifyToken::generateCallbackToken($record->user_id, $record->id);

        $this->auditLog('audit_pass', 'audit', 'record', (string) $record->id);

        return $this->success([
            'callback_token' => $callbackToken,
        ], '审核通过');
    }

    /**
     * 审核驳回
     */
    public function reject()
    {
        $id     = request()->post('id', 0);
        $reason = request()->post('reason', '人工审核未通过');

        $record = CertifyRecord::find($id);
        if (!$record) {
            return $this->error('记录不存在');
        }
        if ($record->status !== CertifyRecord::STATUS_AUDITING) {
            return $this->error('该记录不在审核队列中');
        }

        $record->status = CertifyRecord::STATUS_FAILED;
        $record->fail_reason = $reason;
        $record->save();

        $this->auditLog('audit_reject', 'audit', 'record', (string) $record->id, ['reason' => $reason]);

        return $this->success([], '已驳回');
    }
}