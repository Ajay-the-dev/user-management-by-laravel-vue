import axios from 'axios'
import { useUserStore } from '@/stores/userStore'
import router from '@/router'
const userStore = useUserStore()

    // 'Authorization': `Bearer ${token}`,

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

api.interceptors.request.use((config)=>{
  const userStore = useUserStore()
  if (userStore.token) {
    config.headers.Authorization = `Bearer ${userStore.token}`
  }
  return config
})

// handling 401

api.interceptors.response.use((response) => response,(error)=>{
  if (error.response && error.response.status === 401) {
    const userStore = useUserStore()    
    userStore.logout() 
    router.push({ name: 'login' })
  }
})

export default api
