<?php
namespace app\controller\admin;
use app\controller\Base;
use app\model\CertifyRecord;

class Audit extends Base
{
    public function index() { return $this->fetch('admin/audit'); }
    public function handle()
    {
        $id = (int)request()->post('id', 0);
        $act = request()->post('act', '');
        $record = CertifyRecord::find($id);
        if (!$record || $record->status !== 'auditing') return $this->error('记录不在审核队列');
        if ($act === 'pass') {
            $record->status = 'success'; $record->certify_time = date('Y-m-d H:i:s'); $record->save();
            $this->auditLog('audit_pass', 'audit', 'record', $id);
            return $this->success([], '审核通过');
        }
        $record->status = 'failed'; $record->fail_reason = request()->post('reason', '人工审核未通过'); $record->save();
        $this->auditLog('audit_reject', 'audit', 'record', $id);
        return $this->success([], '已驳回');
    }
}