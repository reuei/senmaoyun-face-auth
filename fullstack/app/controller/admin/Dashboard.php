<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 后台控制台
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\CertifyRecord;
use app\model\FaceDriver;
use think\facade\Db;

class Dashboard extends Base
{
    /**
     * 控制台首页
     */
    public function index()
    {
        return view('admin/dashboard', [
            'site_name' => config('app.site_name'),
            'admin'     => $this->getAdmin(),
        ]);
    }

    /**
     * 获取统计数据
     */
    public function stats()
    {
        $today = date('Y-m-d');

        // 今日认证统计
        $todayStats = CertifyRecord::where('create_time', '>=', $today . ' 00:00:00')
            ->field('
                COUNT(*) as total,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count,
                SUM(CASE WHEN status = "auditing" THEN 1 ELSE 0 END) as auditing_count,
                AVG(CASE WHEN liveness_score > 0 THEN liveness_score ELSE NULL END) as avg_liveness_score
            ')
            ->find();

        // 总认证统计
        $totalStats = CertifyRecord::field('
            COUNT(*) as total,
            SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as total_success
        ')->find();

        $passRate = $totalStats['total'] > 0
            ? round(($totalStats['total_success'] / $totalStats['total']) * 100, 2)
            : 0;

        // 各接口调用统计
        $driverStats = CertifyRecord::where('create_time', '>=', $today . ' 00:00:00')
            ->field('driver_code, COUNT(*) as count, AVG(liveness_score) as avg_score')
            ->group('driver_code')
            ->select()
            ->toArray();

        // 最近7天趋势
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dayStats = CertifyRecord::where('create_time', '>=', $date . ' 00:00:00')
                ->where('create_time', '<', date('Y-m-d', strtotime("+1 day", strtotime($date))) . ' 00:00:00')
                ->field('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count
                ')
                ->find();

            $trend[] = [
                'date'    => $date,
                'total'   => $dayStats['total'] ?? 0,
                'success' => $dayStats['success_count'] ?? 0,
            ];
        }

        return $this->success([
            'today'        => [
                'total'         => (int) ($todayStats['total'] ?? 0),
                'success'       => (int) ($todayStats['success_count'] ?? 0),
                'failed'        => (int) ($todayStats['failed_count'] ?? 0),
                'auditing'      => (int) ($todayStats['auditing_count'] ?? 0),
                'avg_score'     => round((float) ($todayStats['avg_liveness_score'] ?? 0), 2),
            ],
            'total'        => [
                'total'   => (int) ($totalStats['total'] ?? 0),
                'pass_rate'=> $passRate,
            ],
            'driver_stats' => $driverStats,
            'trend'        => $trend,
        ]);
    }
}