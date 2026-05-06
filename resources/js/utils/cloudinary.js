import axios from 'axios'

const CLOUD_NAME = import.meta.env.VITE_CLOUDINARY_CLOUD_NAME

const cloudinaryApi = axios.create({
  baseURL: `https://api.cloudinary.com/v1_1/${CLOUD_NAME}`,
  withCredentials: false 
})

export default cloudinaryApi