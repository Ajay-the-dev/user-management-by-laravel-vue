import axios from 'axios'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
const token = userStore.token

const docApi = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'multipart/form-data'
  }
})

export default docApi
