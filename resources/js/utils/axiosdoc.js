import axios from 'axios'

const docApi = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'multipart/form-data'
  }
})

export default docApi
