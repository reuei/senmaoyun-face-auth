<?php
require_once __DIR__ . '/layout.php';
admin_header('插件中心', 'plugin');
?>
<div class="card" style="max-width:600px;text-align:center;padding:32px">
    <div style="font-size:48px;margin-bottom:16px">&#128230;</div>
    <h3>魔方财务实人认证插件</h3>
    <p style="color:var(--ts);margin:8px 0 16px">适用于魔方财务系统的 certification 类型认证插件，提供实人认证能力。</p>
    <div style="display:flex;gap:16px;justify-content:center;font-size:12px;color:var(--tm);margin-bottom:20px">
        <span>版本: 1.0.0</span><span>类型: certification</span>
    </div>
    <a href="/plugin-senmaoyun/SenmaoyunPlugin.php" class="btn btn-p" download>下载插件</a>
    <div style="text-align:left;margin-top:24px;padding-top:24px;border-top:1px solid var(--bd)">
        <h4 style="margin-bottom:10px">安装步骤</h4>
        <ol style="padding-left:18px;font-size:13px;color:var(--ts);line-height:2">
            <li>下载插件文件</li>
            <li>放置到魔方财务系统 /public/plugins/certification/senmaoyun/</li>
            <li>在魔方财务后台「客户 &gt; 设置 &gt; 实名设置」中启用</li>
            <li>配置系统地址：<?php echo get_site_url(); ?></li>
            <li>配置API Key：在森码云后台 <code style="background:var(--bl);padding:2px 6px;border-radius:3px"><?php echo API_SECRET ? substr(API_SECRET,0,16).'...' : '（安装时自动生成）'; ?></code></li>
        </ol>
    </div>
</div>
<?php admin_footer(); ?>