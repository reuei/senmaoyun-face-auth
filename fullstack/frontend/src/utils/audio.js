/**
 * 提示音系统 - 使用 Web Audio API 生成提示音
 */
const AudioContext = window.AudioContext || window.webkitAudioContext
let audioCtx = null

function getContext() {
  if (!audioCtx) {
    audioCtx = new AudioContext()
  }
  return audioCtx
}

/**
 * 播放提示音
 * @param {'start'|'success'|'fail'|'complete'} type
 */
export function playSound(type = 'start') {
  try {
    const ctx = getContext()
    const oscillator = ctx.createOscillator()
    const gainNode = ctx.createGain()

    oscillator.connect(gainNode)
    gainNode.connect(ctx.destination)

    switch (type) {
      case 'start':
        // 叮 - 动作开始
        oscillator.type = 'sine'
        oscillator.frequency.setValueAtTime(880, ctx.currentTime)
        oscillator.frequency.setValueAtTime(1100, ctx.currentTime + 0.05)
        gainNode.gain.setValueAtTime(0.3, ctx.currentTime)
        gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2)
        oscillator.start(ctx.currentTime)
        oscillator.stop(ctx.currentTime + 0.2)
        break

      case 'success':
        // 咚 - 动作成功
        oscillator.type = 'sine'
        oscillator.frequency.setValueAtTime(660, ctx.currentTime)
        oscillator.frequency.setValueAtTime(880, ctx.currentTime + 0.1)
        gainNode.gain.setValueAtTime(0.3, ctx.currentTime)
        gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4)
        oscillator.start(ctx.currentTime)
        oscillator.stop(ctx.currentTime + 0.4)
        break

      case 'fail':
        // 嗡 - 动作失败
        oscillator.type = 'sawtooth'
        oscillator.frequency.setValueAtTime(220, ctx.currentTime)
        oscillator.frequency.setValueAtTime(180, ctx.currentTime + 0.15)
        gainNode.gain.setValueAtTime(0.2, ctx.currentTime)
        gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3)
        oscillator.start(ctx.currentTime)
        oscillator.stop(ctx.currentTime + 0.3)
        break

      case 'complete':
        // 完成提示
        oscillator.type = 'sine'
        oscillator.frequency.setValueAtTime(523, ctx.currentTime)
        oscillator.frequency.setValueAtTime(659, ctx.currentTime + 0.15)
        oscillator.frequency.setValueAtTime(784, ctx.currentTime + 0.3)
        gainNode.gain.setValueAtTime(0.3, ctx.currentTime)
        gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.6)
        oscillator.start(ctx.currentTime)
        oscillator.stop(ctx.currentTime + 0.6)
        break
    }
  } catch (e) {
    // 音频播放失败不影响主流程
  }
}

/**
 * 语音播报（使用 Web Speech API）
 */
export function speak(text) {
  try {
    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel()
      const utterance = new SpeechSynthesisUtterance(text)
      utterance.lang = 'zh-CN'
      utterance.rate = 0.9
      utterance.pitch = 1.0
      window.speechSynthesis.speak(utterance)
    }
  } catch (e) {
    // 语音播报失败不影响主流程
  }
}