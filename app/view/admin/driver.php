<?php require __DIR__ . '/layout.php'; admin_layout_start('接口管理', 'driver'); ?>
<div class="card"><p style="color:#6B7280;margin-bottom:20px">管理人脸识别接口驱动。自研接口默认启用，第三方接口需配置密钥后方可使用。</p>
<div id="driverList"></div></div>
<script>
var drivers=[{code:'self',name:'自研活体检测',desc:'默认启用，无需配置。基于GD库实现图像质量检测、亮度分析、纹理复杂度分析。',fields:[]},{code:'tencent',name:'腾讯云慧眼',desc:'需开通腾讯云人脸核身服务。',fields:[{k:'secret_id',l:'SecretId'},{k:'secret_key',l:'SecretKey'},{k:'region',l:'地域',def:'ap-guangzhou'}]},{code:'baidu',name:'百度智能云',desc:'需开通百度智能云人脸识别服务。',fields:[{k:'api_key',l:'API Key'},{k:'secret_key',l:'Secret Key'},{k:'app_id',l:'App ID'}]},{code:'alipay',name:'支付宝活体检测',desc:'需开通支付宝开放平台。',fields:[{k:'app_id',l:'AppId'},{k:'private_key',l:'应用私钥'},{k:'alipay_public_key',l:'支付宝公钥'}]},{code:'juhe',name:'聚合数据',desc:'需注册聚合数据。',fields:[{k:'api_key',l:'API Key'}]},{code:'aliyun_market',name:'阿里云市场',desc:'需在阿里云市场订阅。',fields:[{k:'app_code',l:'AppCode'}]}];
function render(){
    var h='';
    drivers.forEach(function(d,i){
        h+='<div style="border:1px solid #E2E8F0;border-radius:12px;padding:20px;margin-bottom:14px"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px"><div><strong style="font-size:15px">'+d.name+'</strong><span style="font-size:11px;color:#9CA3AF;margin-left:8px;font-family:monospace">'+d.code+'</span></div><div><span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:500;background:'+(d.enabled?'#D1FAE5;color:#10B981':'#FEE2E2;color:#EF4444')+'">'+(d.enabled?'已启用':'已禁用')+'</span>'+(d.isDefault?'<span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:500;background:#DBEAFE;color:#3B82F6;margin-left:6px">默认</span>':'')+'</div></div><p style="font-size:12px;color:#6B7280;margin-bottom:14px">'+d.desc+'</p>';
        if(d.fields.length>0){
            h+='<form onsubmit="saveDriver(event,\''+d.code+'\')" style="border-top:1px solid #E2E8F0;padding-top:14px">';
            d.fields.forEach(function(f){h+='<div style="margin-bottom:12px"><label style="display:block;font-size:13px;font-weight:500;margin-bottom:4px">'+f.l+'</label><input type="password" name="'+f.k+'" placeholder="请输入'+f.l+'" value="'+(f.def||'')+'" style="width:100%;padding:9px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;outline:none"></div>'});
            h+='<div style="display:flex;gap:10px;align-items:center"><label style="font-size:12px;display:flex;align-items:center;gap:4px"><input type="checkbox" name="enabled" '+(d.enabled?'checked':'')+'> 启用</label><label style="font-size:12px;display:flex;align-items:center;gap:4px"><input type="checkbox" name="is_default" '+(d.isDefault?'checked':'')+'> 设为默认</label><button type="submit" style="padding:6px 14px;background:#4F46E5;color:#fff;border:none;border-radius:6px;font-size:12px;cursor:pointer">保存配置</button></div></form>';
        }else{h+='<div style="border-top:1px solid #E2E8F0;padding-top:14px"><p style="font-size:12px;color:#10B981;font-weight:500">此接口无需配置，始终可用</p></div>'}
        h+='</div>';
    });
    document.getElementById('driverList').innerHTML=h;
}
async function saveDriver(e,code){e.preventDefault();var f=new FormData(e.target),config={};for(var p of f.keys()){if(p!=='enabled'&&p!=='is_default')config[p]=f.get(p)}try{await axios.post('/admin/driver/save',new URLSearchParams({driver_code:code,config:JSON.stringify(config),enabled:f.get('enabled')?1:0,is_default:f.get('is_default')?1:0}));alert('保存成功');location.reload()}catch(e){alert('保存失败')}}
render();
</script>
<?php admin_layout_end(); ?>