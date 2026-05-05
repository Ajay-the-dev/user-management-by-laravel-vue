<template>
<div class="row my-5 mx-2">
    <!-- View Modal -->
    <div class="modal fade" id="noticeViewModal" tabindex="-1" aria-labelledby="noticeViewModalLabel" aria-hidden="true" ref="noticeViewModalRef">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticeViewModalLabel">Notice Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" v-if="selectedNotice">
                    <div class="row mx-3">
                        <div class="col-12 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span :class="typeBadgeClass(selectedNotice.type)" class="badge text-capitalize">
                                    <i :class="typeIcon(selectedNotice.type)" class="me-1"></i>{{ selectedNotice.type }}
                                </span>
                                <span class="badge bg-secondary text-capitalize">
                                    <i class="fas fa-users me-1"></i>{{ selectedNotice.audience }}
                                </span>
                                <span v-if="selectedNotice.customBatch" class="badge bg-info text-dark text-capitalize">
                                    <i class="fas fa-graduation-cap me-1"></i>Batch Specific ({{ selectedNotice.batches.name }})
                                </span>
                            </div>
                            <h4 class="fw-bold">{{ selectedNotice.title }}</h4>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ formatDate(selectedNotice.created_at) }}
                            </small>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light notice-html-content" v-html="selectedNotice.html"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 w-75">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">Notices</h5>
                    <div class="d-flex">
                        <input
                            type="text"
                            class="form-control w-auto mx-3"
                            placeholder="Search notices here ..."
                            v-model="searchQuery"
                        >
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="5%" class="fw-bold text-muted text-center">#</th>
                                <th scope="col" width="20%" class="fw-bold text-muted">Title</th>
                                <th scope="col" width="10%" class="fw-bold text-muted">Visbility</th>
                                <th scope="col" width="15%" class="fw-bold text-muted">Type</th>
                                <th scope="col" width="15%" class="fw-bold text-muted">Audience</th>
                                <th scope="col" width="25%" class="fw-bold text-muted">Date</th>
                                <th scope="col" width="10%" class="fw-bold text-muted text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody v-if="notices?.data?.length === 0">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2 d-block"></i>
                                    <div class="fw-bold text-muted">No notices found.</div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr v-for="(notice, index) in notices?.data" :key="notice.id" class="align-middle border-bottom">
                                <td class="fw-bold text-center">
                                    {{ notices.per_page * (notices.current_page - 1) + index + 1 }}
                                </td>
                                <td class="fw-500">{{ notice.title }}</td>
                                <td class="fw-500">
                                    <span class="badge px py-1 bg-secondary text-capitalize">
                                        <i class="fa" :class="notice.show === 1 ? ' fa-eye':'fa-eye-slash'"></i> {{ notice.show === 1 ? 'Visible':'Hidden' }}
                                    </span>
                                </td>
                                <td>
                                    <span :class="typeBadgeClass(notice.type)" class="badge px-2 py-1 text-capitalize">
                                        <i :class="typeIcon(notice.type)" class="me-1"></i>{{ notice.type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-capitalize">
                                        <i class="fas fa-users me-1"></i>{{ notice.audience }}
                                    </span>
                                    <span v-if="notice.customBatch" class="badge bg-info text-dark ms-1 text-capitalize">
                                        <i class="fas fa-graduation-cap me-1"></i>Batch
                                    </span>
                                </td>
                                <td class="text-muted small">{{ formatDate(notice.created_at) }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button
                                            class="btn btn-outline-primary"
                                            @click="viewNotice(notice)"
                                            title="View"
                                            data-bs-toggle="modal"
                                            data-bs-target="#noticeViewModal"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                         <button
                                            class="btn btn-outline-primary" title="update and publish"
                                            @click="router.push(`notice-edit/${notice.id}`)">
                                            <i class="fa fa-rotate"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-light p-3" v-if="notices?.links">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        <li
                            class="page-item"
                            v-for="link in notices.links"
                            :key="link.label + '_notice'"
                            :class="link.active ? 'active' : ''"
                            v-if="searchQuery.trim() === ''"
                        >
                            <a class="page-link" href="#" @click.prevent="link.url && getAllNotices(link.url)" :disabled="!link.url">
                                {{ decodeHtmlEntities(link.label) }}
                            </a>
                        </li>
                        <li
                            class="page-item"
                            v-for="link in notices.links"
                            :key="link.label + '_filterNotice'"
                            :class="link.active ? 'active' : ''"
                            v-else
                        >
                            <a class="page-link" href="#" @click.prevent="link.url && getNoticesByTitle(link.url)" :disabled="!link.url">
                                {{ decodeHtmlEntities(link.label) }}
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '@/utils/axios'
import { Modal } from 'bootstrap'
import debounce from 'lodash/debounce'
import { useRoute,useRouter } from 'vue-router'
import { showToast } from '@/utils/toastr'


const baseURL = import.meta.env.VITE_API_BASE_URL

const notices = ref([])
const selectedNotice = ref(null)
const searchQuery = ref('')
const lastInPage = ref('')

const noticeViewModalRef = ref(null)
let viewModalInstance = null

const router = useRouter()

onMounted(() => {
    getAllNotices()
    viewModalInstance = new Modal(noticeViewModalRef.value)
    noticeViewModalRef.value.addEventListener('hidden.bs.modal', () => {
        selectedNotice.value = null
    });
    listenForNotifications()
})

const getAllNotices = (page = null) => {
    const url = page ? page : `${baseURL}/notice/all`
    lastInPage.value = url
    api.get(url).then((response) => {
        notices.value = response.data.data
    }).catch((error) => {
        showToast({ title: error?.response?.data?.message || error, icon: 'error' })
    })
}

const getNoticesByTitle = (page = null) => {
    const title = searchQuery.value.trim()
    if (title === '') {
        getAllNotices()
        return
    }
    const url = page ? page : `${baseURL}/notices/find`
    api.post(url, { title }).then((response) => {
        if (response.data.status === 1) {
            notices.value = response.data.data
        } else {
            showToast({ title: response.data.message, icon: 'error' })
        }
    }).catch((error) => {
        showToast({ title: error?.response?.data?.message || error, icon: 'error' })
    })
}

const handleSearch = debounce(() => {
    getNoticesByTitle()
}, 500)

watch(searchQuery, () => {
    handleSearch()
})

const viewNotice = (notice) => {
    selectedNotice.value = notice
}

const decodeHtmlEntities = (html) => {
    const textarea = document.createElement('textarea')
    textarea.innerHTML = html
    return textarea.value
}

const formatDate = (dateStr) => {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString('en-GB', {
        day: '2-digit', month: 'short', year: 'numeric', hour:'numeric',minute:'numeric'
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
    return map[type?.toLowerCase()] || 'bg-secondary'
}

const typeIcon = (type) => {
    const map = {
        urgent: 'fas fa-exclamation-circle',
        general: 'fas fa-info-circle',
        event: 'fas fa-calendar-alt',
        holiday: 'fas fa-umbrella-beach',
        exam: 'fas fa-pen',
    }
    return map[type?.toLowerCase()] || 'fas fa-bell'
}



</script>

<style scoped>
.modal:not(.show) {
    display: none !important;
}
.notice-html-content {
    min-height: 100px;
}
</style>