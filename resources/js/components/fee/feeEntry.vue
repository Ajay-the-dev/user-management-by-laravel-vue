<template>
    <div>

        <div class="pay-fee-wrapper">
      
          <!-- ── Page Header ── -->
          <div class="page-header">
            <div>
              <h4 class="page-title">Fee Payment</h4>
              <p class="page-subtitle">Select a batch, choose a student, and record payment</p>
            </div>
            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle fw-semibold px-3 py-2">{{ finYear }}</span>
          </div>
      
          <!-- ── Step 1: Batch Selector ── -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
              <p class="eyebrow mb-3">
                <span class="step-num">1</span> Choose Batch
              </p>
              <div class="d-flex flex-wrap gap-2">
                  <div class="col-12 col-md-6">
                      <select name="selectedBatches" class="form-select" id="selectedBatches" v-model="selectedBatch">
                          <option value="">Select Batch</option>
                          <option v-for="batch in batches" :value="batch.id">{{ batch.name }}</option>
                      </select>
                  </div>
              </div>
            </div>
          </div>
      
          <!-- ── Step 2: Students ── -->
          <div class="card border-0 shadow-sm rounded-4 pb-5 mb-4">
            <div class="card-header bg-white border-0 px-3 px-md-4 pt-4 pb-0 d-flex flex-wrap align-items-center justify-content-between">
              <p class="eyebrow mb-3">
                <span class="step-num">2</span> Students
                <!-- <span v-if="selectedBatch" class="text-dark ms-1">— {{ selectedBatch }}</span> -->
              </p>
              <!-- Search -->
              <div v-if="filteredStudents.length|| searchQuery.trim()!==''" class="search-box mb-3">
                <i class="fa fa-search search-icon text-muted"></i>
                <input
                  v-model="searchQuery"
                  type="text"
                  class="form-control form-control-sm ps-4 rounded-3 border-light bg-light"
                  placeholder="Search students…"
                />
              </div>
            </div>
      
            <div class="card-body px-3 px-md-4 pb-4">
              <!-- Empty: no batch selected -->
              <div v-if="selectedBatch===''" class="empty-state text-center py-5">
                <div class="empty-icon mb-3">
                  <i class="fa fa-users fa-2x text-muted opacity-50"></i>
                </div>
                <p class="fw-semibold text-muted mb-1">No batch selected</p>
                <small class="text-muted">Pick a batch above to load students</small>
              </div>
      
              <!-- Loading -->
              <div v-else-if="loadingStudents" class="text-center py-5">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                <span class="text-muted small">Loading students…</span>
              </div>
      
              <!-- Student list -->
              <div v-else-if="filteredStudents.length" class="student-table-wrap">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th class="text-uppercase small fw-bold text-muted d-none d-md-table-cell">#</th>
                      <th class="text-uppercase small fw-bold text-muted">Student</th>
                      <th class="text-uppercase small fw-bold text-muted d-none d-md-table-cell">Roll No</th>
                      <th class="text-uppercase small fw-bold text-muted d-none d-md-table-cell">Course</th>
                      <th class="text-uppercase small fw-bold text-muted d-none d-md-table-cell">Status</th>
                      <!-- <th class="text-uppercase small fw-bold text-muted">Fees</th> -->
                      <!-- <th class="text-uppercase small fw-bold text-muted">Balance</th> -->
                      <!-- <th class="text-uppercase small fw-bold text-muted">Status</th> -->
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(student, index) in filteredStudents" :key="student.id">
                      <td class="text-muted small fw-semibold d-none d-md-table-cell">{{ index + 1 }}</td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="avatar avatar--square" :class="avatarTone(student.name)">
                            {{ initials(student.name) }}
                          </div>
                          <div>
                            <p class="mb-0 fw-semibold text-dark small">{{ student.name }}</p>
                            <small class="text-muted d-none d-md-inline">{{ student.email }}</small>
                            <span class="cell-sub d-md-none">{{ student.rollNo }}</span>
                          </div>
                        </div>
                      </td>
                      <td class="d-none d-md-table-cell"><code class="text-muted small">{{ student.rollNo }}</code></td>
                      <td class="d-none d-md-table-cell"><code class="text-muted small">{{ student.course }}</code></td>
                      <td class="d-none d-md-table-cell">
                        <span
                          class="badge rounded-pill px-2 py-1"
                          :class="{
                            'bg-success-subtle text-success': student.paymentStatus === 'paid',
                            'bg-warning-subtle text-warning': student.paymentStatus === 'partial',
                            'bg-danger-subtle text-danger':   student.paymentStatus === 'unpaid',
                          }"
                        >
                          {{ student.paymentStatus }}
                        </span>
                      </td>
                      <td class="text-end">
                        <button
                          class="btn btn-primary btn-sm rounded-3 px-3"
                          @click="router.push(`/home/fee/payment/${student.id}`)"
                        >
                          <i class="fa fa-credit-card me-md-1"></i><span class="d-none d-sm-inline"> Pay</span>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
      
              <!-- No students found -->
              <div v-else class="text-center py-5">
                <p class="text-muted fw-semibold mb-1">No students found</p>
                <small class="text-muted">Try a different search term</small>
              </div>
      
            </div>
          </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import api from '@/utils/axios'
import { useRoute,useRouter } from 'vue-router';

const route = useRoute()
const router = useRouter()
const chosenCurrency = import.meta.env.VITE_CURRENCY
const finYear = import.meta.env.VITE_FINYEAR


// import Swal from 'sweetalert2'

// ── State ────────────────────────────────────────────────────────────
const batches = ref([])
const selectedBatch = ref('')
const students = ref([])
const loadingStudents = ref(false)
const searchQuery = ref('')
const payingStudent = ref(null)
const submitting = ref(false)
const payModal = ref(null)
let bsModal = null

const paymentModes = [
  { value: 'cash',     label: 'Cash',     icon: 'fa fa-money-bill' },
  { value: 'upi',      label: 'UPI',      icon: 'fa fa-mobile-alt' },
  { value: 'card',     label: 'Card',     icon: 'fa fa-credit-card' },
  { value: 'transfer', label: 'Transfer', icon: 'fa fa-university' },
]

const form = ref({
  feeType: '',
  amount: '',
  paymentMode: 'cash',
  referenceId: '',
  paymentDate: new Date().toISOString().slice(0, 10),
  note: '',
})

// ── Bootstrap Modal init ─────────────────────────────────────────────
onMounted(async () => {
  loadBatches()
  await nextTick()
  if (window.bootstrap) {
    bsModal = new window.bootstrap.Modal(payModal.value)
  }
})

// ── Data Loaders ─────────────────────────────────────────────────────
const loadBatches = async() => {
  const res = await api.get('/batches/all'); 
  batches.value = res.data.data
}



// ── Computed ─────────────────────────────────────────────────────────
const filteredStudents = computed(() => {
  const q = searchQuery.value.toLowerCase()
  return students.value.filter(s =>
    s.name.toLowerCase().includes(q) ||
    s.rollNo.toLowerCase().includes(q) ||
    s.email.toLowerCase().includes(q)
  )
})

// ── Modal ────────────────────────────────────────────────────────────
const openPayModal = (student) => {
  payingStudent.value = student
  form.value = {
    feeType: '',
    amount: '',
    paymentMode: 'cash',
    referenceId: '',
    paymentDate: new Date().toISOString().slice(0, 10),
    note: '',
  }
  bsModal?.show()
}

const submitPayment = async () => {
  if (!form.value.feeType || !form.value.amount) return
  submitting.value = true

  // Replace with API call:
  // await api.post('/payments', {
  //   studentId: payingStudent.value.id,
  //   ...form.value
  // })

  await new Promise(r => setTimeout(r, 1000))

  // Update local state
  const stu = students.value.find(s => s.id === payingStudent.value.id)
  if (stu) {
    stu.balance = Math.max(0, stu.balance - form.value.amount)
    stu.paymentStatus = stu.balance === 0 ? 'paid' : 'partial'
  }

  submitting.value = false
  bsModal?.hide()

  // Swal.fire({ icon: 'success', title: 'Payment Recorded', timer: 1800, showConfirmButton: false })
  alert(`Payment of ${chosenCurrency} ${form.value.amount} recorded for ${payingStudent.value.name}`)
}

// ── Helpers ──────────────────────────────────────────────────────────
const initials = (name) => name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()

// Matches the .avatar--tone-* classes in scss/components/_data-display.scss
const AVATAR_TONES = 6
const avatarTone = (name) => `avatar--tone-${name.charCodeAt(0) % AVATAR_TONES}`

const getStudentsByBatch = async(id)=>{
    const response = await api.get(`users/bybatchId/${id}`)
    students.value = response.data.data
    
}

watch(selectedBatch,(batchId)=>{
    getStudentsByBatch(batchId)
})
</script>

<style scoped lang="scss">
@use "abstracts" as *;

.pay-fee-wrapper {
  padding: 0.25rem;

  @include below(md) {
    padding: 0;
  }
}

.step-num {
  @include square(1.25rem);
  @include flex-center(inline-flex);
  margin-right: 0.375rem;
  border-radius: 50%;
  background: $gradient-brand;
  color: #fff;
  font-size: $fs-2xs;
  font-weight: 800;
}

.search-box {
  position: relative;
  width: 220px;

  @include below(md) {
    width: 100%;
  }
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  z-index: 1;
  transform: translateY(-50%);
  font-size: $fs-xs;
}

.empty-state .empty-icon {
  @include square(3.75rem);
  @include flex-center;
  margin: 0 auto;
  border-radius: $radius-lg;
  background: $sky-50;
}

.student-table-wrap {
  overflow-x: auto;
}

.table tbody td {
  font-size: $fs-sm;
}
</style>