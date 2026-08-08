import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/utils/api'

export const useVerifyStore = defineStore('verify', () => {
  // 认证步骤
  const steps = [
    { id: 1, title: '协议签署', icon: 'FileText' },
    { id: 2, title: '身份信息', icon: 'IdCard' },
    { id: 3, title: '人脸识别', icon: 'ScanFace' },
    { id: 4, title: '检测结果', icon: 'CheckCircle' },
  ]

  const currentStep = ref(1)
  const recordNo = ref('')
  const sessionId = ref('')
  const token = ref('')

  // 身份信息
  const name = ref('')
  const idCard = ref('')
  const gender = ref('')
  const birthDate = ref('')
  const idCardValid = ref(false)

  // 协议
  const agreementAccepted = ref(false)

  // 人脸识别
  const cameraReady = ref(false)
  const currentAction = ref('')
  const actionIndex = ref(0)
  const completedActions = ref([])
  const livenessScore = ref(0)

  // 结果
  const verifyResult = ref(null)
  const verifyStatus = ref('pending') // pending/processing/success/failed

  const actions = ['blink', 'open_mouth', 'nod_head', 'shake_head']
  const actionLabels = {
    blink: '请眨眨眼',
    open_mouth: '请张张嘴',
    nod_head: '请点点头',
    shake_head: '请摇摇头',
  }

  const isLastAction = computed(() => actionIndex.value >= actions.length - 1)

  function setStep(step) {
    currentStep.value = step
  }

  function nextStep() {
    if (currentStep.value < 4) {
      currentStep.value++
    }
  }

  function setToken(t) {
    token.value = t
  }

  async function initSession() {
    try {
      const res = await api.post('/api/face/init', {
        token: token.value,
        name: name.value,
        id_card: idCard.value,
      })
      if (res.data.code === 200) {
        recordNo.value = res.data.data.record_no
        sessionId.value = res.data.data.session_id
        return true
      }
      return false
    } catch (e) {
      return false
    }
  }

  async function verifyIdCard() {
    try {
      const res = await api.post('/api/idcard/verify', {
        name: name.value,
        id_card: idCard.value,
      })
      if (res.data.code === 200) {
        idCardValid.value = true
        gender.value = res.data.data.gender
        birthDate.value = res.data.data.birth_date
        return true
      }
      return false
    } catch (e) {
      return false
    }
  }

  async function submitAction(imageBase64, actionType) {
    try {
      const res = await api.post('/api/face/action', {
        record_no: recordNo.value,
        action_type: actionType,
        image: imageBase64,
      })
      return res.data
    } catch (e) {
      return { code: 500, msg: '请求失败' }
    }
  }

  async function submitResult(imageBase64) {
    try {
      const res = await api.post('/api/face/result', {
        record_no: recordNo.value,
        image: imageBase64,
      })
      return res.data
    } catch (e) {
      return { code: 500, msg: '请求失败' }
    }
  }

  function reset() {
    currentStep.value = 1
    recordNo.value = ''
    sessionId.value = ''
    name.value = ''
    idCard.value = ''
    gender.value = ''
    birthDate.value = ''
    idCardValid.value = false
    agreementAccepted.value = false
    cameraReady.value = false
    currentAction.value = ''
    actionIndex.value = 0
    completedActions.value = []
    livenessScore.value = 0
    verifyResult.value = null
    verifyStatus.value = 'pending'
  }

  return {
    steps,
    currentStep,
    recordNo,
    sessionId,
    token,
    name,
    idCard,
    gender,
    birthDate,
    idCardValid,
    agreementAccepted,
    cameraReady,
    currentAction,
    actionIndex,
    completedActions,
    livenessScore,
    verifyResult,
    verifyStatus,
    actions,
    actionLabels,
    isLastAction,
    setStep,
    nextStep,
    setToken,
    initSession,
    verifyIdCard,
    submitAction,
    submitResult,
    reset,
  }
})