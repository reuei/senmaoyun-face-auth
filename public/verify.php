<?php
/**
 * 实人认证页 v1.0.5 - 含音频提示 + 解除Token限制
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/encrypt.php';

$token = $_GET['token'] ?? '';
$tokenRecord = null;
$fromUserCenter = false;

// 用户中心入口：不强制要求Token
if (empty($token)) {
    session_start();
    if (empty($_SESSION['user_id'])) {
        redirect('/user/login');
    }
    $fromUserCenter = true;
} else {
    // 魔方财务入口：验证Token
    if (is_installed()) {
        try {
            $tokenRecord = db()->fetch(
                "SELECT * FROM " . db()->table('certify_token') . " WHERE token = ? AND type='request' AND expire_time > NOW() AND used = 0",
                [$token]
            );
        } catch (\Throwable $e) {}
    }
    if (!$tokenRecord) {
        redirect('/forbidden?reason=invalid_token');
    }
    try {
        db()->update(db()->table('certify_token'), ['used' => 1, 'used_time' => date('Y-m-d H:i:s')], 'token = ?', [$token]);
    } catch (\Throwable $e) {}
}

$userId = $tokenRecord['user_id'] ?? ($_SESSION['user_id'] ?? '');
$callbackUrl = $tokenRecord['callback_url'] ?? '';
?><!DOCTYPE html>
<html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>实人认证 - 森码云 v1.0.5</title>
<link rel="stylesheet" href="https://unpkg.com/element-plus/dist/index.css"><script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script><script src="https://unpkg.com/element-plus"></script><script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>:root{--el-color-primary:#4F46E5}*{margin:0;padding:0;box-sizing:border-box}body{font-family:system-ui,sans-serif;background:#F8FAFC;color:#1F2937;min-height:100vh;line-height:1.5}
.steps-bar{background:#fff;border-bottom:1px solid #E2E8F0;padding:18px 0}.steps-inner{max-width:760px;margin:0 auto;padding:0 20px;display:flex;align-items:center}
.step-item{display:flex;align-items:center;gap:8px;flex:1}.step-dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;border:2px solid #E2E8F0;background:#fff;color:#9CA3AF;flex-shrink:0;transition:all .2s}.step-dot.active{border-color:#4F46E5;background:#4F46E5;color:#fff}.step-dot.done{border-color:#10B981;background:#10B981;color:#fff}
.step-label{font-size:12px;color:#9CA3AF}.step-label.active{color:#4F46E5;font-weight:500}.step-label.done{color:#10B981}
.step-line{flex:1;height:2px;background:#E2E8F0;margin:0 6px}.step-line.done{background:#10B981}
.content{max-width:700px;margin:0 auto;padding:36px 20px}
.camera-wrap{position:relative;width:100%;aspect-ratio:4/3;background:#000;border-radius:16px;overflow:hidden;margin-bottom:12px}
.camera-wrap video{width:100%;height:100%;object-fit:cover}
.camera-overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.35)}
.camera-prompt{display:flex;flex-direction:column;align-items:center;gap:10px;color:#fff;font-size:18px;font-weight:600}
.name-bar{display:flex;align-items:center;gap:7px;padding:10px 14px;background:#fff;border:1px solid #E2E8F0;border-radius:8px;font-size:13px;font-weight:500;margin-bottom:16px}
.agreement{max-height:50vh;overflow-y:auto;font-size:12px;line-height:1.8;color:#6B7280;padding:16px;background:#F8FAFC;border-radius:8px;margin-bottom:18px}
.agreement h3{font-size:15px;color:#1F2937;margin-bottom:10px}.agreement h4{font-size:13px;color:#1F2937;margin:12px 0 6px}.agreement p{margin-bottom:6px}
.audio-indicator{display:flex;align-items:center;gap:6px;padding:8px 14px;background:#EEF2FF;border-radius:8px;font-size:12px;color:#4F46E5;margin-bottom:12px}
@media(max-width:640px){.step-label{display:none}.content{padding:20px 14px}}
</style></head><body>
<div id="app"><div class="steps-bar"><div class="steps-inner">
<template v-for="(s,i) in steps" :key="s.id"><div class="step-item"><div class="step-dot" :class="{active:step===s.id,done:step>s.id}"><span v-if="step>s.id">✓</span><span v-else>{{s.id}}</span></div><span class="step-label" :class="{active:step===s.id,done:step>s.id}">{{s.label}}</span></div><div v-if="i<steps.length-1" class="step-line" :class="{done:step>s.id}"></div></template></div></div>
<div class="content">
<div v-if="step===1"><el-card><h2>实人认证服务协议</h2><div class="agreement"><h3>森码云实人认证服务协议</h3><p>更新日期：2026年8月</p><h4>一、总则</h4><p>1.1本服务是由森码云向您提供的基于人脸识别技术的身份真实性核验服务。</p><p>1.2您在使用本服务前，请务必仔细阅读并充分理解本协议的全部内容。</p><h4>二、服务内容</h4><p>2.1本服务通过采集您的面部生物特征信息，结合身份证件信息进行核验。</p><p>2.2认证流程包括：签署协议、身份信息录入、活体人脸识别、结果判定。</p><p>2.3认证过程中，您的摄像头将全程开启，系统会实时采集您的面部视频帧用于活体检测分析。</p><h4>三、信息收集与使用</h4><p>3.1我们将收集您的姓名、身份证号码、面部生物特征信息。</p><p>3.2上述信息仅用于本次认证目的，认证完成后默认24小时后自动删除。</p><p>3.3我们承诺对您的个人信息采取严格的加密存储措施（AES-256-GCM）。</p><h4>四、用户权利与义务</h4><p>4.1您保证所提供的身份信息真实、准确、完整，且为本人信息。</p><p>4.2您同意在认证过程中配合完成活体检测动作。</p><h4>五、人脸识别授权书</h4><p>5.1本人知悉并同意，森码云将采集本人的面部生物特征信息用于身份核验。</p></div>
<el-checkbox v-model="agreed">我已阅读并同意《实人认证服务协议》《隐私政策》和《人脸识别授权书》</el-checkbox><el-button type="primary" :disabled="!agreed" @click="nextStep" style="width:100%;margin-top:16px">下一步，填写身份信息</el-button></el-card></div>
<div v-if="step===2"><el-card><h2>身份信息录入</h2><el-form :model="form" style="margin-top:20px"><el-form-item label="真实姓名"><el-input v-model="form.name" placeholder="请输入真实姓名"/></el-form-item><el-form-item label="身份证号码"><el-input v-model="form.idcard" placeholder="请输入18位身份证号码" @input="form.idcard=form.idcard.replace(/[^0-9Xx]/g,'')"/></el-form-item></el-form>
<div v-if="idcardOk" style="background:#D1FAE5;color:#10B981;padding:12px;border-radius:8px;margin-bottom:14px">身份证号校验通过</div>
<div v-if="idcardError" style="background:#FEE2E2;color:#EF4444;padding:12px;border-radius:8px;margin-bottom:14px">{{idcardError}}</div>
<div style="display:flex;gap:10px;justify-content:flex-end"><el-button @click="step=1">上一步</el-button><el-button type="primary" :disabled="!canProceed||verifying" @click="verifyIdcard" :loading="verifying">下一步，开始人脸识别</el-button></div></el-card></div>
<div v-if="step===3"><el-card><h2>人脸识别</h2>
<div class="audio-indicator"><el-icon><Bell /></el-icon> <span>提示音已开启 - 请按语音提示完成动作</span></div>
<div class="camera-wrap"><video ref="video" autoplay playsinline muted></video><canvas ref="canvas" style="display:none"></canvas><div class="camera-overlay" v-if="currentAction"><div class="camera-prompt">{{actionText}}</div></div></div>
<div class="name-bar">👤 {{form.name}}</div>
<el-card><h3 style="margin-bottom:12px">活体检测进度</h3><div v-for="a in actions" :key="a" style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:13px;color:#9CA3AF" :style="{color:doneActions.includes(a)?'#10B981':currentAction===a?'#4F46E5':''}"><span v-if="doneActions.includes(a)">✓</span><span v-else>○</span>{{actionLabels[a]}}</div><el-progress :percentage="Math.round(doneActions.length/actions.length*100)" style="margin-top:12px"/></el-card></el-card></div>
<div v-if="step===4"><el-card style="text-align:center;padding:40px"><div style="font-size:48px;margin-bottom:16px">{{resultStatus==='success'?'✅':'❌'}}</div><h2 :style="{color:resultStatus==='success'?'#10B981':'#EF4444'}">{{resultStatus==='success'?'实人认证通过':'认证未通过'}}</h2><p style="color:#6B7280;margin:12px 0">{{resultMsg}}</p></el-card></div>
</div></div>
<script>
const{createApp,ref,reactive,computed,onMounted,nextTick}=Vue;createApp({setup(){
    const step=ref(1);const steps=[{id:1,label:'协议签署'},{id:2,label:'身份信息'},{id:3,label:'人脸识别'},{id:4,label:'检测结果'}];
    const agreed=ref(false);const form=reactive({name:'',idcard:''});const idcardError=ref('');const idcardOk=ref(false);const verifying=ref(false);
    const canProceed=computed(()=>form.name.length>=2&&form.idcard.length===18&&idcardOk.value);
    const video=ref(null);const canvas=ref(null);const actions=['blink','open_mouth','nod_head','shake_head'];
    const actionLabels={blink:'请眨眨眼',open_mouth:'请张张嘴',nod_head:'请点点头',shake_head:'请摇摇头'};
    const currentAction=ref('');const doneActions=ref([]);const actionText=computed(()=>actionLabels[currentAction.value]||'');
    const resultStatus=ref('');const resultMsg=ref('');const recordNo=ref('');let stream=null;
    function nextStep(){if(step.value<4)step.value++}
    async function verifyIdcard(){idcardError.value='';verifying.value=true;try{const r=await axios.post('/api/?action=idcard_verify',form);if(r.data.code===200){idcardOk.value=true;const r2=await axios.post('/api/?action=face_init',{token:'<?php echo $token; ?>',name:form.name,id_card:form.idcard});if(r2.data.code===200){recordNo.value=r2.data.data.record_no;step.value=3;await nextTick();startCamera()}else idcardError.value=r2.data.msg}else idcardError.value=r.data.msg}catch(e){idcardError.value='网络错误'}verifying.value=false}
    async function startCamera(){try{stream=await navigator.mediaDevices.getUserMedia({video:{width:{ideal:640},height:{ideal:480},facingMode:'user'}});if(video.value)video.value.srcObject=stream;await new Promise(r=>setTimeout(r,1000));runActions()}catch(e){resultStatus.value='failed';resultMsg.value='无法访问摄像头: 请检查浏览器权限设置';step.value=4}}
    async function runActions(){for(let i=0;i<actions.length;i++){currentAction.value=actions[i];speakText(actionLabels[actions[i]]);playTone('start');await new Promise(r=>setTimeout(r,3000));const img=captureFrame();if(img){try{const r=await axios.post('/api/?action=face_action',{record_no:recordNo.value,action_type:actions[i],image:img});if(r.data.code===200){doneActions.value.push(actions[i]);playTone('success')}else playTone('fail')}catch(e){playTone('fail')}}await new Promise(r=>setTimeout(r,1000))}currentAction.value='';const finalImg=captureFrame();if(finalImg){try{const r=await axios.post('/api/?action=face_result',{record_no:recordNo.value,image:finalImg});resultStatus.value=r.data.code===200?'success':'failed';resultMsg.value=r.data.msg||'认证完成';if(r.data.code===200)playTone('complete')}catch(e){resultStatus.value='failed';resultMsg.value='网络错误'}}if(stream){stream.getTracks().forEach(t=>t.stop())}step.value=4}
    function captureFrame(){if(!video.value||!canvas.value)return null;const v=video.value,c=canvas.value;c.width=v.videoWidth||640;c.height=v.videoHeight||480;c.getContext('2d').drawImage(v,0,0,c.width,c.height);return c.toDataURL('image/jpeg',0.8)}
    function playTone(t){try{const ctx=new(window.AudioContext||window.webkitAudioContext)();const o=ctx.createOscillator(),g=ctx.createGain();o.connect(g);g.connect(ctx.destination);if(t==='start'){o.type='sine';o.frequency.setValueAtTime(880,ctx.currentTime);o.frequency.setValueAtTime(1100,ctx.currentTime+.05);g.gain.setValueAtTime(.3,ctx.currentTime);g.gain.exponentialRampToValueAtTime(.01,ctx.currentTime+.2);o.start();o.stop(ctx.currentTime+.2)}else if(t==='success'){o.type='sine';o.frequency.setValueAtTime(660,ctx.currentTime);o.frequency.setValueAtTime(880,ctx.currentTime+.1);g.gain.setValueAtTime(.3,ctx.currentTime);g.gain.exponentialRampToValueAtTime(.01,ctx.currentTime+.4);o.start();o.stop(ctx.currentTime+.4)}else if(t==='fail'){o.type='sawtooth';o.frequency.setValueAtTime(220,ctx.currentTime);g.gain.setValueAtTime(.2,ctx.currentTime);g.gain.exponentialRampToValueAtTime(.01,ctx.currentTime+.3);o.start();o.stop(ctx.currentTime+.3)}else if(t==='complete'){o.type='sine';o.frequency.setValueAtTime(523,ctx.currentTime);o.frequency.setValueAtTime(659,ctx.currentTime+.15);o.frequency.setValueAtTime(784,ctx.currentTime+.3);g.gain.setValueAtTime(.3,ctx.currentTime);g.gain.exponentialRampToValueAtTime(.01,ctx.currentTime+.6);o.start();o.stop(ctx.currentTime+.6)}}catch(e){}}
    function speakText(text){try{if('speechSynthesis' in window){window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(text);u.lang='zh-CN';u.rate=.9;u.pitch=1;window.speechSynthesis.speak(u)}}catch(e){}}
    return{step,steps,agreed,form,idcardError,idcardOk,verifying,canProceed,video,canvas,actions,actionLabels,currentAction,doneActions,actionText,resultStatus,resultMsg,nextStep,verifyIdcard};
}}).use(ElementPlus).mount('#app');
</script></body></html>