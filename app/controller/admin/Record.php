<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 认证记录控制器
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\CertifyRecord;

class Record extends Base
{
    /**
     * 认证记录列表
     */
    public function index()
    {
        return view('admin/record', [
            'site_name' => config('app.site_name'),
            'admin'     => $this->getAdmin(),
        ]);
    }

    /**
     * 获取记录详情
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
     * 导出CSV
     */
    public function export()
    {
        $status   = request()->post('status', '');
        $dateFrom = request()->post('date_from', '');
        $dateTo   = request()->post('date_to', '');

        $query = CertifyRecord::order('id desc');

        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($dateFrom)) {
            $query->where('create_time', '>=', $dateFrom . ' 00:00:00');
        }
        if (!empty($dateTo)) {
            $query->where('create_time', '<=', $dateTo . ' 23:59:59');
        }

        $list = $query->select();

        // 生成CSV
        $csvData = "记录编号,用户ID,姓名(脱敏),身份证号(脱敏),性别,活体分数,比对分数,状态,接口,认证时间,IP地址\n";
        foreach ($list as $record) {
            $csvData .= implode(',', [
                $record->record_no,
                $record->user_id,
                $record->getMaskedName(),
                $record->getMaskedIdCard(),
                $record->gender,
                $record->liveness_score,
                $record->compare_score,
                $record->status,
                $record->driver_code,
                $record->certify_time,
                $record->ip_address,
            ]) . "\n";
        }

        $filename = 'certify_records_' . date('YmdHis') . '.csv';
        $filepath = runtime_path() . $filename;
        file_put_contents($filepath, "\xEF\xBB\xBF" . $csvData); // BOM for Excel

        $this->auditLog('record_export', 'record', 'export', '');

        return download($filepath, $filename);
    }
}