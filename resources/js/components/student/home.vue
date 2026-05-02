<template>
  <div class="card border-0 shadow-sm rounded-4 mb-4 vh-100 content-scroll">

    <!-- Preview Modal -->
    <div class="modal fade" id="noticePreviewModal" tabindex="-1" aria-hidden="true" ref="previewModalRef">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header border-0 pb-0" v-if="selectedNotice">
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill text-uppercase" :class="typeBadgeClass(selectedNotice.type)">
                  {{ selectedNotice.type }}
                </span>
                <span class="badge rounded-pill bg-secondary text-uppercase">
                  {{ selectedNotice.audience }}
                </span>
              </div>
              <h5 class="fw-bold text-dark mb-0">{{ selectedNotice.title }}</h5>
              <small class="text-muted">
                <i class="fa fa-clock me-1"></i>{{ formatDate(selectedNotice.created_at) }}
                <!-- <span v-if="selectedNotice.expiry" class="ms-3">
                  <i class="fa fa-calendar-times me-1"></i>Expires: {{ formatDate(selectedNotice.expiry) }}
                </span> -->
              </small>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pt-3" v-if="selectedNotice">
            <hr class="mt-0">
            <div class="p-2" v-html="selectedNotice.html"></div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Card Header -->
    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center">
        <div class="icon-box bg-primary-subtle text-primary rounded-3 p-2 me-3">
          <i class="fa fa-bullhorn"></i>
        </div>
        <h5 class="fw-bold m-0 text-dark">Important Notices</h5>
      </div>
    </div>

    <!-- Card Body -->
    <div class="card-body px-4 pb-4">

      <!-- Loading -->
      <div v-if="loading" class="text-center py-4 text-muted">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        Loading notices...
      </div>

      <!-- Empty -->
      <div v-else-if="filteredNotices.length === 0" class="text-center py-4 text-muted">
        <i class="fa fa-bell-slash fa-2x mb-2 d-block"></i>
        No notices available.
      </div>

      <!-- Notice List -->
      <div v-else class="notice-list">
        <div
          v-for="(notice, index) in filteredNotices"
          :key="notice.id"
          class="notice-item p-3 mb-3 border-start border-4 rounded-3 cursor-pointer"
          :class="notice.type === 'urgent' ? 'border-danger bg-danger-subtle' : 'border-primary bg-light'"
          @click="openPreview(notice)"
          data-bs-toggle="modal"
          data-bs-target="#noticePreviewModal"
          role="button"
        >
          <div class="d-flex justify-content-between align-items-start mb-1">
            <h6 class="fw-bold mb-0 text-dark">{{ notice.title }}</h6>
            <span
              class="badge rounded-pill text-uppercase ms-2 flex-shrink-0"
              :class="typeBadgeClass(notice.type)"
            >
              {{ notice.type }}
            </span>
          </div>
          <div class="d-flex align-items-center justify-content-between text-muted small mt-2">
            <span><i class="fa fa-calendar-alt me-1"></i>{{ formatDate(notice.created_at) }}</span>
            <span v-if="notice.expiry" class="text-danger">
              <i class="fa fa-hourglass-end me-1"></i>Expires {{ formatDate(notice.expiry) }}
            </span>
            <span class="text-primary"><i class="fa fa-eye me-1"></i>Read more</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Modal } from 'bootstrap'
import api from '@/utils/axios'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
const userId = computed(() => userStore.allData.id)

const notices = ref([])
const selectedNotice = ref(null)
const loading = ref(false)

const previewModalRef = ref(null)
let modalInstance = null

onMounted(() => {
  fetchNotices()
  modalInstance = new Modal(previewModalRef.value)
  previewModalRef.value.addEventListener('hidden.bs.modal', () => {
    selectedNotice.value = null
  })
})

const fetchNotices = async () => {
  loading.value = true
  try {
    const response = await api.get('/notice/getForUser/' + userId.value)
    notices.value = response.data.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const openPreview = (notice) => {
  selectedNotice.value = notice
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('en-GB', {
    day: '2-digit', month: 'short', year: 'numeric'
  })
}

const typeBadgeClass = (type) => {
  const map = {
    urgent: 'bg-danger',
    general: 'bg-primary',
    event: 'bg-success',
    holiday: 'bg-warning text-dark',
    exam: 'bg-dark',
  }
  return map[type?.toLowerCase()] ?? 'bg-secondary'
}

const filteredNotices = computed(()=>{
  if (notices.value.length===0) return []
  return notices.value.filter(notice => {
    const expiryDate = new Date(notice.expiry);
    const today = new Date();
    const isNotExpired = expiryDate >= today;
    
    if (notice.audience === "ALL" && isNotExpired) return true;
    if (notice.audience === "STAFF" && isNotExpired) return true;
    if (notice.audience === "STUDENT" && isNotExpired && notice.customBatch === userStore.allData.batchId) return true;
    return false;

  })})
</script>

<style scoped>
.icon-box {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.notice-item {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.notice-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
}
</style>