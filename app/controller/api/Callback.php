<?php
namespace app\controller\api;

use app\controller\Base;
use app\model\CertifyRecord;
use app\model\CertifyToken;
use think\facade\Db;

class Callback extends Base
{
    public function mofang()
    {
        $cbToken = request()->post('token', '');
        $userId = request()->post('user_id', '');
        $sign = request()->post('sign', '');
        $expected = hash_hmac('sha256', $cbToken . $userId, env('app.api_secret', ''));
        if (!hash_equals($expected, $sign)) return $this->error('签名验证失败', 403);
        $row = CertifyToken::where('token', $cbToken)->where('type', 'callback')->where('used', 0)->find();
        if (!$row) return $this->error('回调Token无效');
        $row->used = 1; $row->used_time = date('Y-m-d H:i:s'); $row->save();
        return $this->success([], '回调处理成功');
    }
}

class Admin extends Base
{
    public function stats()
    {
        $today = date('Y-m-d');
        $stats = [
            'today_total' => CertifyRecord::whereTime('create_time', '>=', $today)->count(),
            'today_success' => CertifyRecord::where('status', 'success')->whereTime('create_time', '>=', $today)->count(),
            'today_failed' => CertifyRecord::where('status', 'failed')->whereTime('create_time', '>=', $today)->count(),
            'today_auditing' => CertifyRecord::where('status', 'auditing')->whereTime('create_time', '>=', $today)->count(),
            'total' => CertifyRecord::count(),
            'total_success' => CertifyRecord::where('status', 'success')->count(),
        ];
        $stats['pass_rate'] = $stats['total'] > 0 ? round($stats['total_success'] / $stats['total'] * 100, 1) : 0;
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $trend[] = [
                'date' => substr($d, 5),
                'total' => CertifyRecord::whereTime('create_time', '>=', $d)->whereTime('create_time', '<', date('Y-m-d', strtotime("+1 day", strtotime($d))))->count(),
                'success' => CertifyRecord::where('status', 'success')->whereTime('create_time', '>=', $d)->whereTime('create_time', '<', date('Y-m-d', strtotime("+1 day", strtotime($d))))->count(),
            ];
        }
        return $this->success(['stats' => $stats, 'trend' => $trend]);
    }

    public function records()
    {
        $page = request()->post('page', 1);
        $list = CertifyRecord::order('id desc')->page($page, 20)->select();
        $total = CertifyRecord::count();
        return $this->success(['total' => $total, 'page' => $page, 'list' => $list]);
    }

    public function exportCsv()
    {
        $records = CertifyRecord::order('id desc')->limit(5000)->select();
        $csv = "\xEF\xBB\xBF记录编号,用户ID,状态,活体分数,接口,认证时间,IP\n";
        foreach ($records as $r) {
            $csv .= "{$r->record_no},{$r->user_id},{$r->status},{$r->liveness_score},{$r->driver_code},{$r->certify_time},{$r->ip_address}\n";
        }
        return response($csv, 200, ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => 'attachment; filename="records_' . date('YmdHis') . '.csv"']);
    }
}