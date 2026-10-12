<template>
  <div class="doc-page container-fluid px-1">
    <!-- ─── STUDENT BAR ─── -->
    <div class="hero-bar">
      <div class="d-flex align-items-center gap-3">
        <div class="hero-bar__avatar" v-if="userData.profile_picture !== null">
          <img :src="userData.profile_picture"/>
        </div>
         <div class="hero-bar__avatar" v-else>
          {{ initials }}
        </div>
        <div class="flex-grow-1">
          <h6 class="fw-bold mb-1 text-white text-capitalize">{{ userData.name }}</h6>
          <code class="hero-bar__meta">{{ userData.rollNo }} · {{ userData?.batches?.name }}</code>
        </div>
        <span class="hero-bar__pill badge rounded-pill px-3 py-2 fw-semibold">
          {{ documents?.length }} Document{{ documents?.length !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <!-- ─── UPLOAD ZONE ─── -->
    <div
      class="upload-zone rounded-4 mb-3 text-center position-relative"
      :class="{ 'upload-zone--dragging': isDragging }"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="onDrop"
      v-if="documents?.length < 5">
      <input
        ref="fileInput"
        type="file"
        multiple
        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
        class="d-none"
        @change="onFileChange"
      />
      <div class="upload-icon mx-auto mb-3 rounded-3 d-flex align-items-center justify-content-center">
        <i class="fa fa-cloud-upload-alt fa-lg"></i>
      </div>
      <p class="upload-title fw-bold mb-1">Drop files here or click to browse</p>
      <p class="upload-sub mb-3">PDF, DOCX, JPG, JPEG, PNG up to 10 MB each</p>
      <button class="btn btn-upload fw-semibold px-md-4" @click="fileInput.click()">
        <i class="fa fa-plus me-2"></i>Choose Files
      </button>
    </div>
    <div class="upload-zone rounded-4 mb-3 text-center position-relative" v-else>
        <p class="text-muted fw-light fst-italic">Upload limited upto 5 files</p>
    </div>

    <!-- ─── UPLOADING PROGRESS ─── -->
    <div v-if="uploadQueue.length" class="card border-0 rounded-4 shadow-sm mb-3">
      <div class="card-body px-4 py-3">
        <div class="eyebrow mb-3">Uploading</div> 
        <div class="d-flex flex-column gap-2">
          <div v-for="(item, i) in uploadQueue" :key="i" class="upload-progress-row d-flex align-items-center gap-3 p-2">
            <div class="file-type-icon rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" :class="fileIconClass(item.name)">
              <i :class="fileIcon(item.name)"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between mb-1">
                <span class="upload-file-name fw-semibold">{{ item.name }} <span class="upload-pct px-2" v-if="item.progress > 0 && item.progress < 100">( {{ item.progress }}% )</span></span>
                <button class="btn btn-close btn-sm p-2" @click="removeFromQueue(item)" :disabled="item.progress !== 100">
                </button>
              </div>
              <div class="progress rounded-pill upload-bar">
                <div class="progress-bar rounded-pill" role="progressbar" :style="{ width: item.progress + '%' }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ─── DOCUMENT LIST ─── -->
    <div class="card border-0 rounded-4 shadow-sm mb-5">
      <div class="card-header bg-white border-bottom px-3 px-md-4 py-3 d-flex align-items-center justify-content-between rounded-top-4">
        <h2 class="card-heading">My Documents</h2>
        <!-- <div class="d-flex gap-2">
          <span
            v-for="filter in filters"
            :key="filter"
            class="chip badge rounded-pill"
            :class="activeFilter === filter ? 'chip--active' : 'chip--idle'"
            @click="activeFilter = filter"
          >
            {{ filter }}
          </span>
        </div> -->
      </div>

      <!-- Empty State -->
      <div v-if="!filteredDocs?.length" class="empty-state">
        <div class="icon-tile icon-tile--lg icon-tile--muted">
          <i class="fa fa-folder-open fa-lg"></i>
        </div>
        <p class="empty-state__title">No documents found</p>
        <p class="empty-state__sub">Upload your first document above</p>
      </div>

      <!-- Document Rows -->
      <div v-else class="doc-list px-2 px-md-3 pb-3 pt-2">
        <div
          v-for="(doc, index) in filteredDocs"
          :key="doc.id || index"
          class="doc-row d-flex align-items-center gap-2 gap-md-3 px-2 px-md-3 py-3 rounded-3"
        >
        <!-- {{ doc }} -->
          <div class="file-type-icon rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" :class="fileIconClass(doc.name)">
            <i :class="fileIcon(doc.title)"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <p class="doc-name fw-semibold mb-0 text-truncate">{{ doc.title }}</p>
            <span class="doc-meta d-none d-sm-inline">{{ doc.size }} · {{ formatDate(doc.created_at) }}</span>
          </div>
          <div class="d-flex align-items-center gap-1 flex-shrink-0">
            <button class="doc-action-btn btn rounded-2" title="Preview" @click="openPreview(doc)">
              <i class="fa fa-eye"></i>
            </button>
            <button class="doc-action-btn btn rounded-2" title="Download" @click="downloadDoc(doc)">
              <i class="fa fa-download"></i>
            </button>
            <button class="doc-action-btn doc-action-btn--danger btn rounded-2" title="Delete" @click="deleteDoc(doc)">
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ─── PREVIEW MODAL ─── -->
    <Teleport to="body">
      <div v-if="previewDoc" class="preview-overlay d-flex align-items-center justify-content-center" @click.self="previewDoc = null">
        <div class="preview-modal rounded-4 overflow-hidden shadow-lg">
          <div class="preview-modal__header d-flex align-items-center gap-3 px-4 py-3">
            <div class="file-type-icon rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" :class="fileIconClass(previewDoc.name)">
              <i :class="fileIcon(previewDoc.title)"></i>
            </div>
            <div class="flex-grow-1 overflow-hidden">
              <p class="fw-bold mb-0 text-truncate preview-doc-name">{{ previewDoc.title }}</p>
              <span class="doc-meta">{{ previewDoc.size }} · {{ formatDate(previewDoc.created_at) }}</span>
            </div>
            <button class="btn preview-close-btn rounded-2 ms-2" @click="previewDoc = null">
              <i class="fa fa-times"></i>
            </button>
          </div>
          <div class="preview-body d-flex align-items-center justify-content-center">
            <img v-if="isImage(previewDoc.name)" :src="previewDoc.url" :alt="previewDoc.name" class="preview-img" />
            <iframe v-else-if="isPdf(previewDoc.title)" :src="previewDoc.url" class="preview-frame" frameborder="0"></iframe>
            <div v-else class="preview-unsupported text-center text-muted py-5">
              <i class="fa fa-file-alt fa-3x mb-3 d-block"></i>
              <p class="fw-semibold mb-1">Preview not available</p>
              <p class="mb-3 small">Download the file to view it</p>
              <button class="btn btn-upload fw-semibold px-md-4" @click="downloadDoc(previewDoc)">
                <i class="fa fa-download me-2"></i>Download
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/utils/axios'
import docApi from '@/utils/axiosdoc'
import { useUserStore } from '@/stores/userStore'
import {useCloudinary} from '@/utils/uploader'
import Swal from 'sweetalert2'


const docURL = import.meta.env.VITE_DOC_BASE_URL;

import { showToast } from '@/utils/toastr'


const userStore = useUserStore()
const userData = ref({})
const fileInput = ref(null)
const isDragging = ref(false)
const uploadQueue = ref([])
const documents = ref([])
const previewDoc = ref(null)
const activeFilter = ref('All')
const filters = ['All', 'PDF', 'Image', 'Other']

const pendingUploads = ref(0);
const uploadedFiles = ref([])
const uploadCompleted = ref(false)

const {uploadFile, deleteByToken} = useCloudinary();

const initials = computed(() =>
  userData?.value?.name?.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
)

onMounted(() => {
  userData.value = userStore.allData
})

watch(initials, () => {
  fetchDocuments()
})

const fetchDocuments = async () => {
  try {
    let req = {}
    req.id = userData.value.id
    const res = await api.get(`/documents/${userData.value.id}`)    
    documents.value = res.data.data
  } catch (err) {
    console.error(err)
  }
}

const filteredDocs = computed(() => {
  if (activeFilter.value === 'All') return documents.value
  if (activeFilter.value === 'PDF') return documents.value.filter(d => isPdf(d.name))
  if (activeFilter.value === 'Image') return documents.value.filter(d => isImage(d.name))
  return documents.value.filter(d => !isPdf(d.name) && !isImage(d.name))
})

const onFileChange = (e) => handleFiles(Array.from(e.target.files))
const onDrop = (e) => handleFiles(Array.from(e.dataTransfer.files))

const handleFiles = async (files) => {
  if(files?.length+documents?.value?.length > 5)
  {
    showToast({title:'File upload limit restricted to 5 ',icon:'error'})
    return
  } 
  const uploadPromises = files.map(file => uploadSingleFile(file))
  const results = await Promise.all(uploadPromises)

  uploadedFiles.value = results.filter(Boolean)

  if (uploadedFiles.value.length) {
        const result = await Swal.fire({
            title: 'Confirm',
            text: `You are uploading ${uploadedFiles.value.length} files ` ,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Upload',
            cancelButtonText: 'Cancel',
        })

        if(result.isConfirmed)
        {
          makeDocEntries()
        }
  }
}

const deleteDoc = async (doc) => {
  try {
    const result = await Swal.fire({
            title: 'Confirm',
            text: `You are deleting an uploaded file, continue ? ` ,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'No',
        })

        if(result.isConfirmed)
        {
            await api.delete(`/documents/${doc.id}`)
            documents.value = documents.value.filter(d => d.id !== doc.id)
            if (previewDoc.value?.id === doc.id) previewDoc.value = null
        }
  } catch (err) {
    console.error(err)
  }
}

const removeFromQueue = async (item) => {
  try {
    deleteByToken(item.deleteToken)
    uploadQueue.value = uploadQueue.value.filter(d => d.deleteToken !== item.deleteToken)
  } catch (err) {
    console.error(err)
  }
}

const downloadDoc = (doc) => {
  const a = document.createElement('a')
  a.href = doc.url
  a.download = doc.name
  a.click()
}

const openPreview = (doc) => {
  previewDoc.value = doc
}

const isPdf = (name) => name?.toLowerCase().endsWith('.pdf')
const isImage = (name) => /\.(jpg|jpeg|png)$/i.test(name || '')

const fileIcon = (name) => {
  if (isPdf(name)) return 'fa fa-file-pdf'
  if (isImage(name)) return 'fa fa-file-image'
  if (/\.(doc|docx)$/i.test(name)) return 'fa fa-file-word'
  return 'fa fa-file-alt'
}

const fileIconClass = (name) => {
  if (isPdf(name)) return 'icon--pdf'
  if (isImage(name)) return 'icon--image'
  if (/\.(doc|docx)$/i.test(name)) return 'icon--word'
  return 'icon--default'
}

const formatDate = (d) => {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })
}

const makeDocEntries = async () => {
  try {
    const requests = uploadedFiles.value.map(file => {
      return api.post('/documents/userdocs', {
        url: file.url,
        userId: userData.value.id,
        title: file.fileName
      })
    })

    await Promise.all(requests)

    uploadedFiles.value = [] // reset
    fetchDocuments() // refresh list
    uploadQueue.value=[]
    showToast({title:'File(s) uploaded ',icon:'success'})
  } catch (err) {
    console.error(err)
  }
}


const uploadSingleFile = (file) => {
  return new Promise(async(resolve, reject) => {
    const item = { name: file.name, progress: 0, deleteToken:'', file }
    uploadQueue.value.push(item)

    const formData = new FormData()
    formData.append('file', file)
    formData.append('userId', userData.value.id)

    const interval = setInterval(() => {
      if (item.progress < 90) item.progress += 10
    }, 150)

    const res = await uploadFile(file)
    clearInterval(interval)
    item.progress = 100
    const fileData = {
        url: docURL + res.url,
        fileName: res.fileName
      }
    item.deleteToken = res.deleteToken
    

        setTimeout(() => {
          uploadQueue.value = uploadQueue.value.filter(q => q !== item)
        }, 600)
    resolve(fileData)

    

    // docApi.post('/documents/upload', formData)
    //   .then(res => {
    //     clearInterval(interval)
    //     item.progress = 100

    //     const fileData = {
    //       url: docURL + res.data.url,
    //       fileName: file.name
    //     }

    //     setTimeout(() => {
    //       uploadQueue.value = uploadQueue.value.filter(q => q !== item)
    //     }, 600)

    //     resolve(fileData)
    //   })
    //   .catch(err => {
    //     clearInterval(interval)
    //     uploadQueue.value = uploadQueue.value.filter(q => q !== item)
    //     console.error(err)
    //     resolve(null) // prevent Promise.all crash
    //   })
  })
}
</script>

<style scoped lang="scss">
@use "sass:color";
@use "abstracts" as *;

// ─── UPLOAD ZONE ───
.upload-zone {
  padding: 2.5rem 1.5rem;
  border: 2px dashed $sky-200;
  background: $surface;
  cursor: pointer;
  transition: border-color $transition-base, background $transition-base;

  @include below(md) {
    padding: 1.75rem 1rem;
  }

  &--dragging,
  &:hover {
    border-color: $sky-500;
    background: $sky-50;
  }
}

.upload-icon {
  @include square(3.25rem);
  background: $sky-100;
  color: $sky-600;
}

.upload-title {
  font-size: 0.875rem;
  color: $text-strong;
}

.upload-sub {
  font-size: 0.75rem;
  color: $text-faint;
}

.btn-upload {
  border-radius: $radius-sm;
  background: $gradient-brand;
  color: #fff;
  font-size: $fs-sm;
  box-shadow: 0 6px 18px color.change($sky-500, $alpha: 0.3);
  transition: filter $transition-fast;

  &:hover {
    filter: brightness(1.05);
    color: #fff;
  }
}

// ─── UPLOAD PROGRESS ───
.upload-file-name {
  font-size: $fs-sm;
  color: $text-strong;
}

.upload-pct {
  font-size: 0.75rem;
  color: $text-muted;
}

.upload-bar {
  height: 5px;

  .progress-bar {
    background: linear-gradient(90deg, $sky-500, $sky-300);
  }
}

// ─── DOCUMENT ROWS ───
.doc-row {
  cursor: default;
  transition: background $transition-fast;

  &:hover {
    background: $surface-hover;
  }
}

.doc-name {
  font-size: $fs-sm;
  color: $text-strong;
}

.doc-meta {
  font-size: $fs-xs;
  font-weight: 500;
  color: $text-faint;
}

// ─── FILE TYPE ICONS ───
.file-type-icon {
  @include square(2.125rem);
  font-size: $fs-sm;
}

.icon--pdf     { background: $red-100;   color: $red-600; }
.icon--image   { background: $sky-100;   color: $sky-600; }
.icon--word    { background: $sky-100;   color: $sky-800; }
.icon--default { background: $slate-100; color: $slate-500; }

// ─── ACTION BUTTONS ───
%square-action {
  @include flex-center;
  padding: 0;
  background: $slate-100;
  color: $slate-500;
  border: 1px solid $border-soft;
  transition: all $transition-fast;
}

.doc-action-btn {
  @extend %square-action;
  @include square(1.875rem);
  font-size: 0.75rem;

  &:hover {
    background: $sky-100;
    border-color: $sky-200;
    color: $sky-700;
  }

  &--danger:hover {
    background: $red-100;
    border-color: #fecaca;
    color: $red-600;
  }
}

// ─── PREVIEW MODAL ───
.preview-overlay {
  position: fixed;
  inset: 0;
  z-index: 1055;
  background: color.change($sky-950, $alpha: 0.65);
  backdrop-filter: blur(4px);
}

.preview-modal {
  display: flex;
  flex-direction: column;
  width: 90vw;
  max-width: 860px;
  max-height: 90vh;
  background: $surface;

  @include below(md) {
    width: 100vw;
    max-height: 100dvh;
    border-radius: 0 !important;
  }

  &__header {
    border-bottom: 1px solid $border-soft;
    background: $surface;
  }
}

.preview-doc-name {
  font-size: 0.875rem;
  color: $text-strong;
}

.preview-close-btn {
  @extend %square-action;
  @include square(2rem);
  font-size: $fs-sm;

  &:hover {
    background: $red-100;
    border-color: #fecaca;
    color: $red-600;
  }
}

.preview-body {
  flex: 1;
  min-height: 400px;
  overflow: auto;
  background: $surface-muted;
}

.preview-img {
  display: block;
  max-width: 100%;
  max-height: 75vh;
  margin: auto;
  object-fit: contain;
}

.preview-frame {
  display: block;
  width: 100%;
  height: 75vh;
  border: none;
}

.preview-unsupported {
  width: 100%;
  color: $text-muted;
}
</style>