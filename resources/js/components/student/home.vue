<template>
  <div class="card border-0 shadow-sm rounded-4 mb-4 vh-100 content-scroll">

    <!-- Preview Modal -->
    <div class="modal fade" id="noticePreviewModal" tabindex="-1" aria-hidden="true" ref="previewModalRef">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header border-0 pb-0" v-if="selectedNotice">
            <div>
              <div class="d-flex align-items-center gap-2 mb-2">
                <!-- <span class="badge rounded-pill text-uppercase" :class="typeBadgeClass(selectedNotice.type)">
                  {{ selectedNotice.type }}
                </span>
                <span class="badge rounded-pill bg-secondary text-uppercase">
                  {{ selectedNotice.audience }}
                </span> -->
              </div>
              <h5 class="fw-bold text-dark mb-1">{{ selectedNotice.title }}</h5>
              <!-- <small class="text-muted">
                <i class="fa fa-clock me-1"></i>{{ formatDate(selectedNotice.created_at) }}
                &nbsp;·&nbsp;
                <i class="fa fa-hourglass-end me-1"></i>Expires {{ formatDate(selectedNotice.expiry) }}
              </small> -->
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body pt-2" v-if="selectedNotice">
            <hr class="mt-0">
            <div v-html="selectedNotice.html"></div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Card Header -->
    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;">
          <i class="fa fa-bullhorn"></i>
        </div>
        <h5 class="fw-bold m-0">Important Notices</h5>
        <span class="badge bg-secondary rounded-pill ms-1">{{ filteredNotices.length }}</span>
      </div>

      <!-- Filter Pills -->
      <div class="d-flex flex-wrap gap-1">
        <button
          v-for="f in filters"
          :key="f.value"
          class="btn btn-sm rounded-pill"
          :class="activeFilter === f.value ? typeBadgeClass(f.value === 'all' ? null : f.value) : 'btn-outline-secondary'"
          @click="activeFilter = f.value"
        >
          {{ f.label }}
        </button>
      </div>
    </div>

    <!-- Card Body -->
    <div class="card-body px-4 pb-4">

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5 text-muted">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        Loading notices...
      </div>

      <!-- Empty -->
      <div v-else-if="filteredNotices.length === 0" class="text-center py-5 text-muted">
        <i class="fa fa-bell-slash fa-2x mb-3 d-block"></i>
        No notices available.
      </div>

      <!-- Notice List -->
      <div v-else class="d-flex flex-column gap-2 mt-2">
        <div
          v-for="notice in filteredNotices"
          :key="notice.id"
          class="d-flex align-items-stretch rounded-3 border overflow-hidden cursor-pointer"
          :class="notice.type === 'urgent' ? 'border-danger' : 'border'"
          role="button"
          @click="openPreview(notice)"
          data-bs-toggle="modal"
          data-bs-target="#noticePreviewModal"
        >
          <!-- Accent Bar -->
          <div class="flex-shrink-0" :class="typeAccentClass(notice.type)" style="width:5px;"></div>

          <!-- Content -->
          <div class="flex-grow-1 px-3 py-2">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <p class="fw-semibold mb-1 text-dark small">{{ notice.title }}</p>
              <span class="badge rounded-pill text-uppercase flex-shrink-0" :class="typeBadgeClass(notice.type)">
                {{ notice.type }}
              </span>
            </div>
            <div class="d-flex gap-3 text-muted" style="font-size:0.75rem;">
              <span><i class="fa fa-calendar-alt me-1"></i>{{ formatDate(notice.created_at) }}</span>
              <!-- <span><i class="fa fa-hourglass-end me-1"></i>Expires {{ formatDate(notice.expiry) }}</span> -->
              <span class="ms-auto text-primary"><i class="fa fa-eye me-1"></i>Read more</span>
            </div>
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
import { showToast } from '@/utils/toastr'
import { useRouter } from "vue-router";

const router = useRouter()
const userStore = useUserStore()
const userId = computed(() => userStore.allData.id)

const notices = ref([])
const selectedNotice = ref(null)
const loading = ref(false)
const activeFilter = ref('all')

const previewModalRef = ref(null)
let modalInstance = null

const filters = [
  { value: 'all',     label: 'All'      },
  { value: 'general', label: 'General'  },
  { value: 'urgent',  label: 'Urgent'   },
  { value: 'event',   label: 'Events'   },
  { value: 'exam',    label: 'Exams'    },
]

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
    urgent:  'bg-danger text-white',
    general: 'bg-primary text-white',
    event:   'bg-success text-white',
    holiday: 'bg-warning text-dark',
    exam:    'bg-dark text-white',
  }
  return map[type?.toLowerCase()] ?? 'bg-secondary text-white'
}

const typeAccentClass = (type) => {
  const map = {
    urgent:  'bg-danger',
    general: 'bg-primary',
    event:   'bg-success',
    holiday: 'bg-warning',
    exam:    'bg-dark',
  }
  return map[type?.toLowerCase()] ?? 'bg-secondary'
}

const filteredNotices = computed(() => {
  const today = new Date()
  return notices.value.filter(notice => {
    const notExpired = new Date(notice.expiry) >= today
    if (!notExpired) return false

    const audienceMatch =
      notice.audience === 'ALL' ||
      notice.audience === 'STAFF' ||
      (notice.audience === 'STUDENT' && notice.customBatch === userStore.allData.batchId)

    if (!audienceMatch) return false

    return activeFilter.value === 'all' || notice.type === activeFilter.value
  })
})


</script>