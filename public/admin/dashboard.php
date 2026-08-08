<?php
require_once __DIR__ . '/layout.php';
admin_header('控制台', 'dashboard');
?>
<div class="stats-grid" id="statsBox">
    <div class="card stat-card"><div class="l">今日认证</div><div class="v">-</div><div class="s">加载中...</div></div>
    <div class="card stat-card"><div class="l">通过率</div><div class="v">-</div><div class="s">累计 - 次</div></div>
    <div class="card stat-card"><div class="l">待审核</div><div class="v">-</div><div class="s">人工审核队列</div></div>
    <div class="card stat-card"><div class="l">主接口</div><div class="v">自研</div><div class="s">默认驱动</div></div>
</div>
<div class="card"><h3 style="margin-bottom:16px">最近7天认证趋势</h3><div id="chart" style="height:300px"></div></div>

<script>
(async function(){
    try{
        const r=await axios.get('/api/?action=admin_stats');
        if(r.data.code===200){
            const s=r.data.data.stats,trend=r.data.data.trend;
            document.getElementById('statsBox').innerHTML=
                '<div class="card stat-card"><div class="l">今日认证</div><div class="v">'+s.today_total+'</div><div class="s">通过 '+s.today_success+' 次，失败 '+s.today_failed+' 次</div></div>'+
                '<div class="card stat-card"><div class="l">通过率</div><div class="v">'+s.pass_rate+'%</div><div class="s">累计 '+s.total+' 次</div></div>'+
                '<div class="card stat-card"><div class="l">待审核</div><div class="v">'+s.today_auditing+'</div><div class="s">人工审核队列</div></div>'+
                '<div class="card stat-card"><div class="l">主接口</div><div class="v">自研</div><div class="s">默认驱动</div></div>';
            var chart=echarts.init(document.getElementById('chart'));
            chart.setOption({
                tooltip:{trigger:'axis'},
                grid:{left:40,right:20,top:10,bottom:30},
                xAxis:{type:'category',data:trend.map(t=>t.date)},
                yAxis:{type:'value'},
                series:[
                    {name:'总认证',type:'line',data:trend.map(t=>t.total),smooth:true,lineStyle:{color:'#4F46E5'},itemStyle:{color:'#4F46E5'}},
                    {name:'通过',type:'line',data:trend.map(t=>t.success),smooth:true,lineStyle:{color:'#10B981'},itemStyle:{color:'#10B981'},areaStyle:{color:'rgba(16,185,129,.1)'}},
                ]
            });
        }
    }catch(e){}
})();
</script>
<?php admin_footer(); ?>