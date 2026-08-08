<template>
  <div class="verify-page">
    <!-- 顶部步骤条 -->
    <div class="verify-header">
      <div class="container">
        <div class="stepper">
          <template v-for="(step, index) in store.steps" :key="step.id">
            <div
              class="step"
              :class="{
                active: store.currentStep === step.id,
                completed: store.currentStep > step.id,
              }"
            >
              <div class="step-indicator">
                <Check v-if="store.currentStep > step.id" :size="16" />
                <span v-else>{{ step.id }}</span>
              </div>
              <span class="step-label">{{ step.title }}</span>
            </div>
            <div
              v-if="index < store.steps.length - 1"
              class="step-connector"
              :class="{ active: store.currentStep > step.id }"
            />
          </template>
        </div>
      </div>
    </div>

    <!-- 步骤内容 -->
    <div class="verify-body container">
      <!-- 步骤1: 协议签署 -->
      <div v-if="store.currentStep === 1" class="step-content animate__animated animate__fadeIn">
        <div class="card agreement-card">
          <h2 class="step-heading">实人认证服务协议</h2>
          <div class="agreement-content">
            <h3>森码云实人认证服务协议</h3>
            <p>更新日期：2026年8月8日</p>
            <p>生效日期：2026年8月8日</p>

            <h4>一、总则</h4>
            <p>1.1 森码云实人认证服务（以下简称"本服务"）是由森码云（以下简称"我方"）向用户（以下简称"您"）提供的基于人脸识别技术的身份真实性核验服务。本协议是您与我方之间关于使用本服务所订立的有效契约。</p>
            <p>1.2 您在使用本服务前，请务必仔细阅读并充分理解本协议的全部内容。特别是以加粗、下划线等方式显著提示的条款，您应重点阅读。如您对本协议有任何疑问，可向我方咨询。</p>
            <p>1.3 当您勾选"我已阅读并同意"并点击"下一步"按钮，即表示您已充分阅读、理解并接受本协议的全部内容，本协议即构成对双方具有约束力的法律文件。</p>

            <h4>二、服务内容</h4>
            <p>2.1 本服务通过采集您的面部生物特征信息，结合身份证件信息，利用人脸识别技术对您的身份真实性进行核验。</p>
            <p>2.2 认证流程包括：签署协议、身份信息录入、活体人脸识别、结果判定四个步骤。您需要按照系统提示完成眨眼、张嘴、点头、摇头等活体检测动作。</p>
            <p>2.3 认证结果将实时返回至发起认证请求的第三方平台（如魔方财务系统），用于完成实名认证流程。</p>
            <p>2.4 认证过程中，您的摄像头将全程开启，系统会实时采集您的面部视频帧用于活体检测分析。</p>

            <h4>三、信息收集与使用</h4>
            <p>3.1 在认证过程中，我们将收集您的姓名、身份证号码、面部生物特征信息（包括人脸图片和活体检测视频帧）。</p>
            <p>3.2 上述信息仅用于本次实人认证目的，不会用于其他任何用途。认证完成后，原始人脸数据默认在24小时后自动删除，您可在设置中调整保留时间。</p>
            <p>3.3 我们承诺对您的个人信息采取严格的加密存储措施（AES-256-GCM加密算法），确保数据安全。</p>
            <p>3.4 我们不会将您的个人信息出售、出租或与任何第三方共享，法律法规另有规定或经您明确同意的除外。</p>

            <h4>四、用户权利与义务</h4>
            <p>4.1 您保证所提供的姓名、身份证号码等身份信息真实、准确、完整，且为本人信息。</p>
            <p>4.2 您同意在认证过程中配合完成活体检测动作，确保是您本人亲自操作，不得使用照片、视频、面具等方式进行欺诈性认证。</p>
            <p>4.3 您有权在认证完成后要求我们删除您的个人生物特征信息，可通过联系客服或系统设置行使该权利。</p>
            <p>4.4 您不得利用本服务进行任何违法活动，包括但不限于身份冒用、欺诈、洗钱等。</p>

            <h4>五、免责条款</h4>
            <p>5.1 本服务的人脸识别技术基于当前技术水平，存在一定的误识率和拒识率，我方不对认证结果的绝对准确性做任何保证。</p>
            <p>5.2 因您提供虚假身份信息、使用他人信息、或采用技术手段规避活体检测而导致的认证结果错误，我方不承担责任。</p>
            <p>5.3 因不可抗力、网络故障、第三方API服务中断等原因导致的服务不可用，我方不承担责任，但将尽力恢复服务。</p>

            <h4>六、隐私政策</h4>
            <p>6.1 我们严格遵守《中华人民共和国个人信息保护法》等相关法律法规，遵循合法、正当、必要和诚信原则处理个人信息。</p>
            <p>6.2 我们采取数据最小化原则，仅收集实现认证功能所必需的最少个人信息。</p>
            <p>6.3 我们建立了完善的数据安全管理制度，采取加密、访问控制、审计日志等技术措施保护您的个人信息。</p>

            <h4>七、人脸识别授权书</h4>
            <p>7.1 本人知悉并同意，森码云实人认证系统将采集本人的面部生物特征信息用于身份核验。</p>
            <p>7.2 本人授权森码云在认证所需的合理期限内保存上述信息，并授权按本协议约定进行使用。</p>
            <p>7.3 本人理解可随时撤回本授权，撤回授权不影响撤回前已进行的认证结果的有效性。</p>

            <h4>八、其他</h4>
            <p>8.1 本协议的解释、效力及争议解决，适用中华人民共和国法律。</p>
            <p>8.2 如本协议的任何条款被认定为无效或不可执行，其余条款仍保持有效。</p>
            <p>8.3 我方有权根据法律法规变化或服务调整需要修改本协议，修改后的协议将在网站公布，继续使用本服务即视为接受修改后的协议。</p>

            <p class="agreement-info">如有疑问，请联系：face.builds.codes</p>
          </div>

          <div class="agreement-actions">
            <label class="checkbox-label">
              <input type="checkbox" v-model="store.agreementAccepted" />
              <span>我已阅读并同意《实人认证服务协议》《隐私政策》和《人脸识别授权书》</span>
            </label>
            <button
              class="btn btn-primary btn-lg"
              :disabled="!store.agreementAccepted"
              @click="goNext"
            >
              下一步，填写身份信息
              <ArrowRight :size="18" />
            </button>
          </div>
        </div>
      </div>

      <!-- 步骤2: 身份信息 -->
      <div v-if="store.currentStep === 2" class="step-content animate__animated animate__fadeIn">
        <div class="card identity-card">
          <h2 class="step-heading">身份信息录入</h2>
          <p class="step-subtitle">请输入您的真实姓名和身份证号码，系统将自动校验信息合法性</p>

          <div class="form-group">
            <label class="label">真实姓名</label>
            <input
              v-model="store.name"
              type="text"
              class="input"
              placeholder="请输入您的真实姓名"
              maxlength="20"
            />
          </div>

          <div class="form-group">
            <label class="label">身份证号码</label>
            <input
              v-model="store.idCard"
              type="text"
              class="input"
              placeholder="请输入18位身份证号码"
              maxlength="18"
              @input="store.idCard = store.idCard.replace(/[^0-9Xx]/g, '')"
            />
          </div>

          <div v-if="idCardError" class="error-msg">
            <AlertCircle :size="16" />
            {{ idCardError }}
          </div>

          <div v-if="store.idCardValid" class="success-msg">
            <CheckCircle2 :size="16" />
            身份证号校验通过
          </div>

          <div v-if="store.idCardValid" class="idcard-info">
            <div class="info-row">
              <span class="info-label">性别</span>
              <span class="info-value">{{ store.gender === 'male' ? '男' : '女' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">出生日期</span>
              <span class="info-value">{{ store.birthDate }}</span>
            </div>
          </div>

          <div class="form-actions">
            <button class="btn btn-secondary btn-lg" @click="store.setStep(1)">上一步</button>
            <button
              class="btn btn-primary btn-lg"
              :disabled="!canProceed"
              @click="verifyAndNext"
            >
              <Loader2 v-if="verifying" :size="18" class="loading-spinner" />
              下一步，开始人脸识别
              <ArrowRight v-else :size="18" />
            </button>
          </div>
        </div>
      </div>

      <!-- 步骤3: 人脸识别 -->
      <div v-if="store.currentStep === 3" class="step-content animate__animated animate__fadeIn">
        <div class="face-recognition">
          <div class="camera-section">
            <div class="camera-box">
              <video ref="videoRef" autoplay playsinline muted class="camera-video" />
              <canvas ref="canvasRef" style="display: none" />
              <div v-if="!cameraReady" class="camera-placeholder">
                <Camera :size="48" />
                <span>正在初始化摄像头...</span>
              </div>
              <div class="camera-overlay" v-if="store.currentAction">
                <div class="action-prompt">
                  <div class="action-icon" :class="actionIconClass">
                    <Eye v-if="store.currentAction === 'blink'" :size="40" />
                    <MessageCircle v-if="store.currentAction === 'open_mouth'" :size="40" />
                    <ArrowUp v-if="store.currentAction === 'nod_head'" :size="40" />
                    <ArrowLeftRight v-if="store.currentAction === 'shake_head'" :size="40" />
                  </div>
                  <span class="action-text">{{ store.actionLabels[store.currentAction] }}</span>
                </div>
              </div>
            </div>
            <div class="user-name-bar">
              <User :size="16" />
              <span>{{ store.name }}</span>
            </div>
          </div>

          <div class="action-progress">
            <h3>活体检测进度</h3>
            <div class="action-list">
              <div
                v-for="(action, index) in store.actions"
                :key="action"
                class="action-item"
                :class="{
                  active: store.actionIndex === index && !store.completedActions.includes(action),
                  completed: store.completedActions.includes(action),
                }"
              >
                <div class="action-status">
                  <CheckCircle2 v-if="store.completedActions.includes(action)" :size="18" />
                  <Loader2 v-else-if="store.actionIndex === index && !store.completedActions.includes(action)" :size="18" class="loading-spinner" />
                  <Circle v-else :size="18" />
                </div>
                <span>{{ store.actionLabels[action] }}</span>
              </div>
            </div>
            <div class="action-progress-bar">
              <div
                class="progress-fill"
                :style="{ width: (store.completedActions.length / store.actions.length * 100) + '%' }"
              />
            </div>
          </div>

          <div class="action-tip" v-if="actionTip">
            <Info :size="16" />
            {{ actionTip }}
          </div>
        </div>
      </div>

      <!-- 步骤4: 检测结果 -->
      <div v-if="store.currentStep === 4" class="step-content animate__animated animate__fadeIn">
        <div class="card result-card" :class="resultClass">
          <div class="result-icon">
            <CheckCircle2 v-if="store.verifyStatus === 'success'" :size="64" color="var(--color-success)" />
            <XCircle v-else-if="store.verifyStatus === 'failed'" :size="64" color="var(--color-error)" />
            <Loader2 v-else :size="64" class="loading-spinner" />
          </div>
          <h2 class="result-title">
            {{ store.verifyStatus === 'success' ? '实人认证通过' : store.verifyStatus === 'failed' ? '认证未通过' : '正在处理...' }}
          </h2>
          <p class="result-desc" v-if="store.verifyStatus === 'success'">
            您的身份信息已通过验证，结果将返回至魔方财务系统。
          </p>
          <p class="result-desc" v-else-if="store.verifyStatus === 'failed'">
            {{ store.verifyResult?.msg || '认证失败，请重试或联系客服。' }}
          </p>
          <div v-if="store.verifyStatus === 'success'" class="result-info">
            <div class="info-row">
              <span class="info-label">认证时间</span>
              <span class="info-value">{{ new Date().toLocaleString('zh-CN') }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">认证等级</span>
              <span class="info-value badge badge-success">L3 实人认证</span>
            </div>
            <div class="info-row">
              <span class="info-label">活体检测分数</span>
              <span class="info-value">{{ store.livenessScore }}%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useVerifyStore } from '@/stores/verify'
import { playSound, speak } from '@/utils/audio'
import api from '@/utils/api'
import {
  ArrowRight, Check, AlertCircle, CheckCircle2, Camera, Eye,
  MessageCircle, ArrowUp, ArrowLeftRight, User, Info, Circle,
  Loader2, XCircle,
} from 'lucide-vue-next'

const route = useRoute()
const store = useVerifyStore()

const videoRef = ref(null)
const canvasRef = ref(null)
const cameraReady = ref(false)
const idCardError = ref('')
const verifying = ref(false)
const actionTip = ref('')
const resultClass = computed(() => ({
  'result-success': store.verifyStatus === 'success',
  'result-failed': store.verifyStatus === 'failed',
}))

const actionIconClass = computed(() => ({
  'pulse-animation': store.currentAction,
}))

const canProceed = computed(() => {
  return store.name.length >= 2 && store.idCard.length === 18 && store.idCardValid
})

// 初始化
onMounted(() => {
  const token = route.query.token
  if (token) {
    store.setToken(token)
  }
})

// 当进入步骤3时初始化摄像头
watch(() => store.currentStep, async (step) => {
  if (step === 3) {
    await initCamera()
    startActionSequence()
  }
})

// 初始化摄像头
async function initCamera() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        width: { ideal: 1280 },
        height: { ideal: 720 },
        facingMode: 'user',
      },
    })
    if (videoRef.value) {
      videoRef.value.srcObject = stream
      cameraReady.value = true
    }
  } catch (e) {
    actionTip.value = '无法访问摄像头，请检查权限设置'
  }
}

// 开始动作序列
async function startActionSequence() {
  store.actionIndex = 0
  store.currentAction = store.actions[0]
  playSound('start')
  speak(store.actionLabels[store.currentAction])

  // 模拟每个动作的检测（实际项目中应结合人脸检测结果）
  for (let i = 0; i < store.actions.length; i++) {
    store.currentAction = store.actions[i]
    store.actionIndex = i
    playSound('start')
    speak(store.actionLabels[store.actions[i]])

    await new Promise(resolve => setTimeout(resolve, 3000))

    // 捕获当前帧
    const imageBase64 = captureFrame()
    if (imageBase64) {
      const result = await store.submitAction(imageBase64, store.actions[i])
      if (result.code === 200) {
        store.completedActions.push(store.actions[i])
        playSound('success')
      } else {
        playSound('fail')
      }
    }

    await new Promise(resolve => setTimeout(resolve, 1000))
  }

  // 所有动作完成，提交最终结果
  store.currentAction = ''
  const finalImage = captureFrame()
  if (finalImage) {
    const result = await store.submitResult(finalImage)
    if (result.code === 200) {
      store.verifyStatus = 'success'
      store.verifyResult = result
      playSound('complete')
      store.currentStep = 4
    } else {
      store.verifyStatus = 'failed'
      store.verifyResult = result
      store.currentStep = 4
    }
  }
}

// 从视频捕获帧
function captureFrame() {
  if (!videoRef.value || !canvasRef.value) return null
  const video = videoRef.value
  const canvas = canvasRef.value
  canvas.width = video.videoWidth || 640
  canvas.height = video.videoHeight || 480
  const ctx = canvas.getContext('2d')
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
  return canvas.toDataURL('image/jpeg', 0.8)
}

// 验证身份证并下一步
async function verifyAndNext() {
  idCardError.value = ''
  verifying.value = true

  try {
    const res = await api.post('/api/idcard/verify', {
      name: store.name,
      id_card: store.idCard,
    })
    if (res.data.code === 200) {
      store.idCardValid = true
      store.gender = res.data.data.gender
      store.birthDate = res.data.data.birth_date

      // 初始化会话
      await store.initSession()
      store.nextStep()
      playSound('success')
    } else {
      idCardError.value = res.data.msg
    }
  } catch (e) {
    idCardError.value = '网络错误，请重试'
  }

  verifying.value = false
}

function goNext() {
  store.nextStep()
}
</script>

<style scoped>
.verify-page { min-height: 100vh; background: var(--color-bg); }
.verify-header { background: var(--color-bg-white); border-bottom: 1px solid var(--color-border); padding: 20px 0; }
.verify-body { padding: 40px 0; max-width: 800px; }

.step-content { max-width: 700px; margin: 0 auto; }
.step-heading { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
.step-subtitle { color: var(--color-text-secondary); margin-bottom: 28px; font-size: 14px; }

/* 协议 */
.agreement-card { max-height: 70vh; overflow-y: auto; }
.agreement-content { font-size: 13px; line-height: 1.8; color: var(--color-text-secondary); padding: 20px; background: var(--color-bg); border-radius: var(--radius); margin-bottom: 24px; }
.agreement-content h3 { font-size: 16px; color: var(--color-text); margin-bottom: 12px; }
.agreement-content h4 { font-size: 14px; color: var(--color-text); margin: 16px 0 8px; }
.agreement-content p { margin-bottom: 8px; }
.agreement-info { color: var(--color-text-muted); font-size: 12px; margin-top: 16px; }
.agreement-actions { display: flex; flex-direction: column; gap: 16px; }
.checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; }

/* 身份信息 */
.identity-card { max-width: 500px; margin: 0 auto; }
.form-group { margin-bottom: 20px; }
.error-msg { display: flex; align-items: center; gap: 6px; color: var(--color-error); font-size: 13px; margin-bottom: 16px; }
.success-msg { display: flex; align-items: center; gap: 6px; color: var(--color-success); font-size: 13px; margin-bottom: 16px; }
.idcard-info { background: var(--color-bg); border-radius: var(--radius); padding: 16px; margin-bottom: 20px; }
.info-row { display: flex; justify-content: space-between; padding: 8px 0; }
.info-label { color: var(--color-text-secondary); font-size: 13px; }
.info-value { font-weight: 500; font-size: 13px; }
.form-actions { display: flex; gap: 12px; justify-content: flex-end; }

/* 人脸识别 */
.face-recognition { display: grid; grid-template-columns: 1fr 280px; gap: 24px; }
.camera-section { position: relative; }
.camera-box { position: relative; width: 100%; aspect-ratio: 4/3; background: #000; border-radius: var(--radius-xl); overflow: hidden; }
.camera-video { width: 100%; height: 100%; object-fit: cover; }
.camera-placeholder { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; gap: 12px; }
.camera-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); }
.action-prompt { display: flex; flex-direction: column; align-items: center; gap: 12px; color: #fff; }
.action-icon { animation: pulse 1.5s ease-in-out infinite; }
.action-text { font-size: 20px; font-weight: 600; }
.user-name-bar { display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: var(--color-bg-white); border: 1px solid var(--color-border); border-radius: var(--radius); margin-top: 12px; font-size: 14px; font-weight: 500; }

.action-progress { padding: 24px; background: var(--color-bg-white); border: 1px solid var(--color-border); border-radius: var(--radius-xl); }
.action-progress h3 { font-size: 16px; margin-bottom: 16px; }
.action-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
.action-item { display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--color-text-muted); }
.action-item.active { color: var(--color-primary); font-weight: 500; }
.action-item.completed { color: var(--color-success); }
.action-progress-bar { height: 4px; background: var(--color-border); border-radius: 2px; overflow: hidden; }
.progress-fill { height: 100%; background: var(--color-primary); transition: width 0.3s ease; border-radius: 2px; }

.action-tip { display: flex; align-items: center; gap: 8px; color: var(--color-text-secondary); font-size: 13px; margin-top: 16px; }

/* 结果 */
.result-card { text-align: center; max-width: 500px; margin: 0 auto; padding: 48px; }
.result-icon { margin-bottom: 20px; }
.result-title { font-size: 24px; font-weight: 700; margin-bottom: 12px; }
.result-desc { color: var(--color-text-secondary); margin-bottom: 24px; }
.result-info { text-align: left; background: var(--color-bg); border-radius: var(--radius); padding: 16px; }
.result-success .result-title { color: var(--color-success); }
.result-failed .result-title { color: var(--color-error); }

@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.1); opacity: 0.8; }
}

@media (max-width: 768px) {
  .face-recognition { grid-template-columns: 1fr; }
  .verify-body { padding: 20px 16px; }
}
</style>