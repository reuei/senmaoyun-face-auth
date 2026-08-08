<?php
require_once __DIR__ . '/layout.php';
admin_header('控制台', 'dashboard');

// 统计数据
$today = date('Y-m-d');
$stats = [
    'today_total' => db()->count(db()->table('certify_record'), "create_time >= ?", [$today . ' 00:00:00']),
    'today_success' => db()->count(db()->table('certify_record'), "status='success' AND create_time >= ?", [$today . ' 00:00:00']),
    'today_auditing' => db()->count(db()->table('certify_record'), "status='auditing' AND create_time >= ?", [$today . ' 00:00:00']),
    'total' => db()->count(db()->table('certify_record')),
    'total_success' => db()->count(db()->table('certify_record'), "status='success'"),
];
$passRate = $stats['total'] > 0 ? round($stats['total_success'] / $stats['total'] * 100, 1) : 0;

// 7天趋势
$trend = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $trend[] = [
        'date' => substr($d, 5),
        'total' => db()->count(db()->table('certify_record'), "create_time >= ? AND create_time < ?", [$d . ' 00:00:00', date('Y-m-d', strtotime("+1 day", strtotime($d))) . ' 00:00:00']),
        'success' => db()->count(db()->table('certify_record'), "status='success' AND create_time >= ? AND create_time < ?", [$d . ' 00:00:00', date('Y-m-d', strtotime("+1 day", strtotime($d))) . ' 00:00:00']),
    ];
}
?>
<div class="stats-grid">
    <div class="card stat-card"><div class="l">今日认证</div><div class="v"><?php echo $stats['today_total']; ?></div><div class="s">通过 <?php echo $stats['today_success']; ?> 次</div></div>
    <div class="card stat-card"><div class="l">通过率</div><div class="v"><?php echo $passRate; ?>%</div><div class="s">累计 <?php echo $stats['total']; ?> 次</div></div>
    <div class="card stat-card"><div class="l">待审核</div><div class="v"><?php echo $stats['today_auditing']; ?></div><div class="s">人工审核队列</div></div>
    <div class="card stat-card"><div class="l">主接口</div><div class="v">自研</div><div class="s">默认驱动</div></div>
</div>
<div class="card"><h3 style="margin-bottom:16px">最近7天认证趋势</h3><div id="chart" style="height:300px"></div></div>
<script>
var chart=echarts.init(document.getElementById('chart'));
chart.setOption({
    tooltip:{trigger:'axis'},
    grid:{left:40,right:20,top:10,bottom:30},
    xAxis:{type:'category',data:<?php echo json_encode(array_column($trend,'date')); ?>},
    yAxis:{type:'value'},
    series:[
        {name:'总认证',type:'line',data:<?php echo json_encode(array_column($trend,'total')); ?>,smooth:true,lineStyle:{color:'#4F46E5'},itemStyle:{color:'#4F46E5'}},
        {name:'通过',type:'line',data:<?php echo json_encode(array_column($trend,'success')); ?>,smooth:true,lineStyle:{color:'#10B981'},itemStyle:{color:'#10B981'}},
    ]
});
</script>
<?php admin_footer(); ?>