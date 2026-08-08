import axios from 'axios'

const api = axios.create({
  baseURL: '/',
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

// 请求拦截器
api.interceptors.request.use(
  (config) => {
    const timestamp = Math.floor(Date.now() / 1000)
    const nonce = Math.random().toString(36).substring(2) + Date.now().toString(36)

    config.headers['X-Timestamp'] = timestamp
    config.headers['X-Nonce'] = nonce

    return config
  },
  (error) => Promise.reject(error)
)

// 响应拦截器
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token过期或未登录
      if (window.location.pathname.startsWith('/admin')) {
        window.location.href = '/admin/login'
      }
    }
    return Promise.reject(error)
  }
)

export default api