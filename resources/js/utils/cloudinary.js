import axios from 'axios'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
const token = userStore.token

const CLOUD_NAME = import.meta.env.VITE_CLOUDINARY_CLOUD_NAME

const cloudinaryApi = axios.create({
  baseURL: `https://api.cloudinary.com/v1_1/${CLOUD_NAME}`,
  headers: {
    'Content-Type': 'multipart/form-data'
  }
})

export default cloudinaryApi