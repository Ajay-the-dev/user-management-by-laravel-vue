import { ref, readonly } from 'vue'
import cloudinaryApi from './cloudinary'

const UPLOAD_IMAGE_PRESET = import.meta.env.VITE_CLOUDINARY_IMAGE_UPLOAD_PRESET
const UPLOAD_DOC_PRESET = import.meta.env.VITE_CLOUDINARY_DOC_UPLOAD_PRESET


export function useCloudinary() {

  const isUploading = ref(false)
  const progress = ref(0)
  const error = ref(null)

  // ✅ Upload Image
  async function uploadImage(file, folder = 'uploads/images') {
    isUploading.value = true
    error.value = null
    progress.value = 0

    try {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('upload_preset', UPLOAD_IMAGE_PRESET)
      formData.append('folder', folder)

      const res = await cloudinaryApi.post('/image/upload', formData, {
        onUploadProgress: (e) => {
          progress.value = Math.round((e.loaded * 100) / e.total)
        }
      })
      
      let response = {}
      response.url = `${res.data.secure_url}`
      response.fileName = `${res.data.original_filename}.${res.data.format}`
      response.deleteToken = `${res.data.delete_token}`
      return response

    } catch (err) {
      error.value = err.response?.data?.error?.message || err.message
      return null
    } finally {
      isUploading.value = false
    }
  }

  // ✅ Upload Any File
  async function uploadFile(file, folder = 'uploads/files') {
    isUploading.value = true
    error.value = null

    try {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('upload_preset', UPLOAD_DOC_PRESET)
      formData.append('folder', folder)

      const res = await cloudinaryApi.post('/auto/upload', formData)
      let response = {}
      response.url = `${res.data.secure_url}`
      response.fileName = `${res.data.original_filename}.${res.data.format}`
      response.deleteToken = `${res.data.delete_token}`
      return response

    } catch (err) {
      error.value = err.message
      return null
    } finally {
      isUploading.value = false
    }
  }

  async function deleteByToken(deleteToken) {
        isUploading.value = true // Reusing your loading state
        error.value = null

        try {
            const formData = new FormData()
            formData.append('token', deleteToken)

            const res = await cloudinaryApi.post('/delete_by_token', formData)

            if (res.data.result === 'ok') {
            return true
            }
            
            return false
        } catch (err) {
            // Cloudinary returns errors in err.response.data.error.message
            error.value = err.response?.data?.error?.message || 'Failed to delete image'
            return false
        } finally {
            isUploading.value = false
        }
    }

  return {
    uploadImage,
    uploadFile,
    deleteByToken,
    isUploading: readonly(isUploading),
    progress: readonly(progress),
    error: readonly(error)
  }
}