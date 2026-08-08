<?php
/**
 * 实人认证页 - 4步流程
 * 需要有效Token才能访问
 */
$token = $_GET['token'] ?? '';
if (empty($token)) {
    redirect('/forbidden?reason=no_permission');
}
// 验证Token
$tokenRecord = null;
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
// 标记Token已使用
try {
    db()->update(db()->table('certify_token'), ['used' => 1, 'used_time' => date('Y-m-d H:i:s')], 'token = ?', [$token]);
} catch (\Throwable $e) {}

$userId = $tokenRecord['user_id'];
$callbackUrl = $tokenRecord['callback_url'];
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>实人认证 - <?php echo SITE_NAME; ?></title>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>
:root{--p:#4F46E5;--ph:#4338CA;--pl:#EEF2FF;--s:#10B981;--sl:#D1FAE5;--e:#EF4444;--el:#FEE2E2;--w:#F59E0B;--t:#1F2937;--ts:#6B7280;--tm:#9CA3AF;--bg:#F9FAFB;--bw:#FFF;--bd:#E5E7EB;--r:8px;--rl:12px;--rx:16px;--sh:0 1px 3px rgba(0,0,0,.08);--tr:.15s}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans SC",sans-serif;background:var(--bg);color:var(--t);min-height:100vh}
/* Steps */
.steps-bar{background:var(--bw);border-bottom:1px solid var(--bd);padding:18px 0}
.steps-inner{max-width:760px;margin:0 auto;padding:0 20px;display:flex;align-items:center}
.step-item{display:flex;align-items:center;gap:8px;flex:1}
.step-dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;border:2px solid var(--bd);background:var(--bw);color:var(--tm);flex-shrink:0;transition:all var(--tr)}
.step-dot.active{border-color:var(--p);background:var(--p);color:#fff}
.step-dot.done{border-color:var(--s);background:var(--s);color:#fff}
.step-label{font-size:12px;color:var(--tm);white-space:nowrap}
.step-label.active{color:var(--p);font-weight:500}
.step-label.done{color:var(--s)}
.step-line{flex:1;height:2px;background:var(--bd);margin:0 6px}
.step-line.active{background:var(--p)}
.step-line.done{background:var(--s)}
/* Content */
.content{max-width:700px;margin:0 auto;padding:36px 20px}
.card{background:var(--bw);border:1px solid var(--bd);border-radius:var(--rx);padding:28px;box-shadow:var(--sh)}
.card h2{font-size:22px;font-weight:700;margin-bottom:6px}
.card .sub{color:var(--ts);font-size:13px;margin-bottom:22px}
/* Form */
.fg{margin-bottom:16px}
.fg label{display:block;font-size:13px;font-weight:500;margin-bottom:5px}
.fg input{width:100%;padding:10px 14px;border:1px solid var(--bd);border-radius:var(--r);font-size:14px;outline:none;transition:border-color var(--tr)}
.fg input:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--pl)}
.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;border-radius:var(--r);font-size:14px;font-weight:500;cursor:pointer;border:1px solid transparent;transition:all var(--tr)}
.btn-p{background:var(--p);color:#fff}.btn-p:hover{background:var(--ph)}.btn-p:disabled{opacity:.5;cursor:not-allowed}
.btn-s{background:var(--bw);color:var(--t);border-color:var(--bd)}.btn-s:hover{background:var(--bg)}
.btn-lg{padding:12px 28px;font-size:15px;border-radius:var(--rl)}
.btn-block{width:100%;justify-content:center}
.msg-ok{display:flex;align-items:center;gap:6px;color:var(--s);font-size:13px;margin-bottom:14px}
.msg-err{display:flex;align-items:center;gap:6px;color:var(--e);font-size:13px;margin-bottom:14px}
.info-row{display:flex;justify-content:space-between;padding:7px 0;font-size:13px}
.info-row .k{color:var(--ts)}.info-row .v{font-weight:500}
.info-box{background:var(--bg);border-radius:var(--r);padding:14px;margin-bottom:16px}
.actions{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}
/* Agreement */
.agreement{max-height:55vh;overflow-y:auto;font-size:12px;line-height:1.8;color:var(--ts);padding:16px;background:var(--bg);border-radius:var(--r);margin-bottom:18px}
.agreement h3{font-size:15px;color:var(--t);margin-bottom:10px}
.agreement h4{font-size:13px;color:var(--t);margin:12px 0 6px}
.agreement p{margin-bottom:6px}
.checkbox{display:flex;align-items:center;gap:7px;font-size:13px;cursor:pointer;margin-bottom:16px}
/* Camera */
.camera-wrap{position:relative;width:100%;aspect-ratio:4/3;background:#000;border-radius:var(--rx);overflow:hidden;margin-bottom:12px}
.camera-wrap video{width:100%;height:100%;object-fit:cover}
.camera-overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.35)}
.camera-prompt{display:flex;flex-direction:column;align-items:center;gap:10px;color:#fff}
.camera-prompt .icon{animation:pulse 1.5s ease-in-out infinite}
.camera-prompt .text{font-size:18px;font-weight:600}
.name-bar{display:flex;align-items:center;gap:7px;padding:10px 14px;background:var(--bw);border:1px solid var(--bd);border-radius:var(--r);font-size:13px;font-weight:500}
.action-panel{padding:20px;background:var(--bw);border:1px solid var(--bd);border-radius:var(--rx)}
.action-panel h3{font-size:15px;margin-bottom:12px}
.action-list{display:flex;flex-direction:column;gap:10px;margin-bottom:14px}
.action-item{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--tm)}
.action-item.active{color:var(--p);font-weight:500}
.action-item.done{color:var(--s)}
.progress-bar{height:4px;background:var(--bd);border-radius:2px;overflow:hidden}
.progress-fill{height:100%;background:var(--p);transition:width .3s;border-radius:2px}
/* Result */
.result{text-align:center;padding:40px}
.result-icon{margin-bottom:18px}
.result h2{font-size:22px;margin-bottom:8px}
.result .desc{color:var(--ts);margin-bottom:20px}
.result .info{text-align:left;background:var(--bg);border-radius:var(--r);padding:14px;max-width:400px;margin:0 auto}
.spinner{width:22px;height:22px;border:3px solid var(--bd);border-top-color:var(--p);border-radius:50%;animation:spin .6s linear infinite;display:inline-block}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.1);opacity:.7}}
@media(max-width:640px){.step-label{display:none}.content{padding:20px 14px}}
</style>
</head>
<body>
<div id="app">
<div class="steps-bar">
    <div class="steps-inner">
        <template v-for="(s,i) in steps" :key="s.id">
            <div class="step-item">
                <div class="step-dot" :class="{active:step===s.id,done:step>s.id}">
                    <span v-if="step>s.id">&#10003;</span><span v-else>{{s.id}}</span>
                </div>
                <span class="step-label" :class="{active:step===s.id,done:step>s.id}">{{s.label}}</span>
            </div>
            <div v-if="i<steps.length-1" class="step-line" :class="{active:step>s.id+1,done:step>s.id}"></div>
        </template>
    </div>
</div>

<div class="content">
    <!-- Step 1: Agreement -->
    <div v-if="step===1" class="card">
        <h2>实人认证服务协议</h2>
        <div class="agreement">
            <h3>森码云实人认证服务协议</h3>
            <p>更新日期：2026年8月8日 | 生效日期：2026年8月8日</p>
            <h4>一、总则</h4>
            <p>1.1 森码云实人认证服务（以下简称"本服务"）是由森码云向您提供的基于人脸识别技术的身份真实性核验服务。</p>
            <p>1.2 您在使用本服务前，请务必仔细阅读并充分理解本协议的全部内容。</p>
            <p>1.3 当您勾选"我已阅读并同意"并点击"下一步"，即表示您已充分阅读、理解并接受本协议的全部内容。</p>
            <h4>二、服务内容</h4>
            <p>2.1 本服务通过采集您的面部生物特征信息，结合身份证件信息，利用人脸识别技术对您的身份真实性进行核验。</p>
            <p>2.2 认证流程包括：签署协议、身份信息录入、活体人脸识别、结果判定。您需要按照系统提示完成眨眼、张嘴、点头、摇头等活体检测动作。</p>
            <p>2.3 认证过程中，您的摄像头将全程开启，系统会实时采集您的面部视频帧用于活体检测分析。</p>
            <h4>三、信息收集与使用</h4>
            <p>3.1 在认证过程中，我们将收集您的姓名、身份证号码、面部生物特征信息。</p>
            <p>3.2 上述信息仅用于本次实人认证目的，认证完成后原始人脸数据默认在24小时后自动删除。</p>
            <p>3.3 我们承诺对您的个人信息采取严格的加密存储措施（AES-256-GCM加密算法）。</p>
            <h4>四、用户权利与义务</h4>
            <p>4.1 您保证所提供的姓名、身份证号码等身份信息真实、准确、完整，且为本人信息。</p>
            <p>4.2 您同意在认证过程中配合完成活体检测动作，不得使用照片、视频、面具等方式进行欺诈性认证。</p>
            <p>4.3 您有权在认证完成后要求我们删除您的个人生物特征信息。</p>
            <h4>五、隐私政策</h4>
            <p>5.1 我们严格遵守《中华人民共和国个人信息保护法》等相关法律法规。</p>
            <p>5.2 我们采取数据最小化原则，仅收集实现认证功能所必需的最少个人信息。</p>
            <h4>六、人脸识别授权书</h4>
            <p>6.1 本人知悉并同意，森码云实人认证系统将采集本人的面部生物特征信息用于身份核验。</p>
            <p>6.2 本人授权森码云在认证所需的合理期限内保存上述信息。</p>
            <p>6.3 本人理解可随时撤回本授权。</p>
            <h4>七、其他</h4>
            <p>7.1 本协议适用中华人民共和国法律。</p>
            <p>7.2 如有疑问请联系：face.builds.codes</p>
        </div>
        <label class="checkbox"><input type="checkbox" v-model="agreed"> 我已阅读并同意《实人认证服务协议》《隐私政策》和《人脸识别授权书》</label>
        <button class="btn btn-p btn-lg btn-block" :disabled="!agreed" @click="nextStep">下一步，填写身份信息</button>
    </div>

    <!-- Step 2: Identity -->
    <div v-if="step===2" class="card">
        <h2>身份信息录入</h2>
        <p class="sub">请输入您的真实姓名和身份证号码，系统将自动校验信息合法性</p>
        <div class="fg"><label>真实姓名</label><input v-model="name" placeholder="请输入您的真实姓名" maxlength="20"></div>
        <div class="fg"><label>身份证号码</label><input v-model="idcard" placeholder="请输入18位身份证号码" maxlength="18" @input="idcard=idcard.replace(/[^0-9Xx]/g,'')"></div>
        <div v-if="idcardError" class="msg-err">{{idcardError}}</div>
        <div v-if="idcardOk" class="msg-ok">身份证号校验通过</div>
        <div v-if="idcardOk" class="info-box">
            <div class="info-row"><span class="k">性别</span><span class="v">{{genderText}}</span></div>
            <div class="info-row"><span class="k">出生日期</span><span class="v">{{birthDate}}</span></div>
        </div>
        <div class="actions">
            <button class="btn btn-s btn-lg" @click="step=1">上一步</button>
            <button class="btn btn-p btn-lg" :disabled="!canProceed||verifying" @click="doVerifyIdcard">
                <span v-if="verifying" class="spinner"></span>
                {{verifying?'验证中...':'下一步，开始人脸识别'}}
            </button>
        </div>
    </div>

    <!-- Step 3: Face -->
    <div v-if="step===3" class="card">
        <h2>人脸识别</h2>
        <p class="sub">请将面部对准摄像头，按提示完成活体检测动作</p>
        <div class="camera-wrap">
            <video ref="video" autoplay playsinline muted></video>
            <canvas ref="canvas" style="display:none"></canvas>
            <div class="camera-overlay" v-if="currentAction">
                <div class="camera-prompt">
                    <div class="icon">
                        <svg v-if="currentAction==='blink'" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg v-else-if="currentAction==='open_mouth'" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        <svg v-else-if="currentAction==='nod_head'" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
                        <svg v-else width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M18 8 6 16"/><path d="m6 8 12 8"/></svg>
                    </div>
                    <span class="text">{{actionText}}</span>
                </div>
            </div>
        </div>
        <div class="name-bar">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            {{name}}
        </div>
        <div class="action-panel" style="margin-top:16px">
            <h3>活体检测进度</h3>
            <div class="action-list">
                <div v-for="a in actions" :key="a" class="action-item" :class="{active:currentAction===a&&!doneActions.includes(a),done:doneActions.includes(a)}">
                    <span v-if="doneActions.includes(a)">&#10003;</span><span v-else-if="currentAction===a&&!doneActions.includes(a)">&#9711;</span><span v-else>&#9711;</span>
                    {{actionLabels[a]}}
                </div>
            </div>
            <div class="progress-bar"><div class="progress-fill" :style="{width:(doneActions.length/actions.length*100)+'%'}"></div></div>
        </div>
    </div>

    <!-- Step 4: Result -->
    <div v-if="step===4" class="card result">
        <div class="result-icon">
            <svg v-if="resultStatus==='success'" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <svg v-else-if="resultStatus==='failed'" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <span v-else class="spinner" style="width:56px;height:56px;border-width:4px"></span>
        </div>
        <h2 :style="{color:resultStatus==='success'?'#10B981':resultStatus==='failed'?'#EF4444':''}">
            {{resultStatus==='success'?'实人认证通过':resultStatus==='failed'?'认证未通过':'正在处理...'}}
        </h2>
        <p class="desc">{{resultMsg}}</p>
        <div v-if="resultStatus==='success'" class="info">
            <div class="info-row"><span class="k">认证时间</span><span class="v">{{new Date().toLocaleString('zh-CN')}}</span></div>
            <div class="info-row"><span class="k">认证等级</span><span class="v" style="color:#10B981">L3 实人认证</span></div>
        </div>
    </div>
</div>
</div>

<script>
const {createApp,ref,computed,onMounted,nextTick} = Vue;
createApp({
    setup(){
        const step=ref(1);
        const steps=[{id:1,label:'协议签署'},{id:2,label:'身份信息'},{id:3,label:'人脸识别'},{id:4,label:'检测结果'}];
        const agreed=ref(false);
        const name=ref('');const idcard=ref('');const idcardError=ref('');const idcardOk=ref(false);
        const genderText=ref('');const birthDate=ref('');const verifying=ref(false);
        const canProceed=computed(()=>name.value.length>=2&&idcard.value.length===18&&idcardOk.value);
        const video=ref(null);const canvas=ref(null);
        const actions=['blink','open_mouth','nod_head','shake_head'];
        const actionLabels={blink:'请眨眨眼',open_mouth:'请张张嘴',nod_head:'请点点头',shake_head:'请摇摇头'};
        const currentAction=ref('');const doneActions=ref([]);
        const actionText=computed(()=>actionLabels[currentAction.value]||'');
        const resultStatus=ref('');const resultMsg=ref('');
        const recordNo=ref('');let stream=null;

        function nextStep(){if(step.value<4)step.value++}

        async function doVerifyIdcard(){
            idcardError.value='';verifying.value=true;
            try{
                const r=await axios.post('/api/?action=idcard_verify',{name:name.value,id_card:idcard.value});
                if(r.data.code===200){
                    idcardOk.value=true;genderText.value=r.data.data.gender_text;birthDate.value=r.data.data.birth_date;
                    // init session
                    const r2=await axios.post('/api/?action=face_init',{token:'<?php echo $token; ?>',name:name.value,id_card:idcard.value});
                    if(r2.data.code===200){recordNo.value=r2.data.data.record_no;step.value=3;await nextTick();startCamera();}
                    else{idcardError.value=r2.data.msg}
                }else{idcardError.value=r.data.msg}
            }catch(e){idcardError.value='网络错误'}
            verifying.value=false;
        }

        async function startCamera(){
            try{
                stream=await navigator.mediaDevices.getUserMedia({video:{width:{ideal:640},height:{ideal:480},facingMode:'user'}});
                if(video.value)video.value.srcObject=stream;
                await new Promise(r=>setTimeout(r,1000));
                runActions();
            }catch(e){resultStatus.value='failed';resultMsg.value='无法访问摄像头';step.value=4}
        }

        async function runActions(){
            for(let i=0;i<actions.length;i++){
                currentAction.value=actions[i];playTone('start');
                await new Promise(r=>setTimeout(r,2500));
                const img=captureFrame();
                if(img){
                    try{
                        const r=await axios.post('/api/?action=face_action',{record_no:recordNo.value,action_type:actions[i],image:img});
                        if(r.data.code===200){doneActions.value.push(actions[i]);playTone('success')}
                        else{playTone('fail')}
                    }catch(e){playTone('fail')}
                }
                await new Promise(r=>setTimeout(r,800));
            }
            currentAction.value='';
            const finalImg=captureFrame();
            if(finalImg){
                try{
                    const r=await axios.post('/api/?action=face_result',{record_no:recordNo.value,image:finalImg});
                    resultStatus.value=r.data.code===200?'success':'failed';
                    resultMsg.value=r.data.msg||'';
                    if(r.data.code===200)playTone('complete');
                }catch(e){resultStatus.value='failed';resultMsg.value='网络错误'}
            }
            if(stream){stream.getTracks().forEach(t=>t.stop())}
            step.value=4;
        }

        function captureFrame(){
            if(!video.value||!canvas.value)return null;
            const v=video.value,c=canvas.value;
            c.width=v.videoWidth||640;c.height=v.videoHeight||480;
            c.getContext('2d').drawImage(v,0,0,c.width,c.height);
            return c.toDataURL('image/jpeg',0.8);
        }

        function playTone(type){
            try{
                const ctx=new(window.AudioContext||window.webkitAudioContext)();
                const o=ctx.createOscillator(),g=ctx.createGain();
                o.connect(g);g.connect(ctx.destination);
                if(type==='start'){o.type='sine';o.frequency.setValueAtTime(880,ctx.currentTime);g.gain.setValueAtTime(.3,ctx.currentTime);g.gain.exponentialRampToValueAtTime(.01,ctx.currentTime+.2);o.start();o.stop(ctx.currentTime+.2)}
                else if(type==='success'){o.type='sine';o.frequency.setValueAtTime(660,ctx.currentTime);o.frequency.setValueAtTime(880,ctx.currentTime+.1);g.gain.setValueAtTime(.3,ctx.currentTime);g.gain.exponentialRampToValueAtTime(.01,ctx.currentTime+.4);o.start();o.stop(ctx.currentTime+.4)}
                else if(type==='fail'){o.type='sawtooth';o.frequency.setValueAtTime(220,ctx.currentTime);g.gain.setValueAtTime(.2,ctx.currentTime);g.gain.exponentialRampToValueAtTime(.01,ctx.currentTime+.3);o.start();o.stop(ctx.currentTime+.3)}
                else if(type==='complete'){o.type='sine';o.frequency.setValueAtTime(523,ctx.currentTime);o.frequency.setValueAtTime(659,ctx.currentTime+.15);o.frequency.setValueAtTime(784,ctx.currentTime+.3);g.gain.setValueAtTime(.3,ctx.currentTime);g.gain.exponentialRampToValueAtTime(.01,ctx.currentTime+.6);o.start();o.stop(ctx.currentTime+.6)}
            }catch(e){}
        }

        return{step,steps,agreed,name,idcard,idcardError,idcardOk,genderText,birthDate,verifying,canProceed,video,canvas,actions,actionLabels,currentAction,doneActions,actionText,resultStatus,resultMsg,nextStep,doVerifyIdcard};
    }
}).mount('#app');
</script>
</body>
</html>