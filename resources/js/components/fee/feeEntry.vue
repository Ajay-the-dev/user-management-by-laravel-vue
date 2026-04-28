<template>
    <div class=" content-scroll">

        <div class="pay-fee-wrapper">
      
          <!-- ── Page Header ── -->
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
              <h4 class="fw-bold mb-1 text-dark">Fee Payment</h4>
              <p class="text-muted small mb-0">Select a batch, choose a student, and record payment</p>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-3 py-2">FY 2025–26</span>
          </div>
      
          <!-- ── Step 1: Batch Selector ── -->
          <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
              <p class="text-uppercase text-muted fw-bold small letter-wide mb-3">
                <span class="step-num">1</span> Choose Batch
              </p>
              <div class="d-flex flex-wrap gap-2">
                  <div class="col-6">
                      <select name="selectedBatches" class="form-control" id="selectedBatches" v-model="selectedBatch">
                          <option value="">Select Batch</option>
                          <option v-for="batch in batches" :value="batch.id">{{ batch.name }}</option>
                      </select>
                  </div>
              </div>
            </div>
          </div>
      
          <!-- ── Step 2: Students ── -->
          <div class="card border-0 shadow-sm rounded-4 pb-5 mb-4">
            <div class="card-header bg-white border-0 px-4 pt-4 pb-0 d-flex align-items-center justify-content-between">
              <p class="text-uppercase text-muted fw-bold small letter-wide mb-3">
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
      
            <div class="card-body px-4 pb-4">
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
                      <th class="text-uppercase small fw-bold text-muted">#</th>
                      <th class="text-uppercase small fw-bold text-muted">Student</th>
                      <th class="text-uppercase small fw-bold text-muted">Roll No</th>
                      <th class="text-uppercase small fw-bold text-muted">Course</th>    
                      <!-- <th class="text-uppercase small fw-bold text-muted">Fees</th> -->
                      <!-- <th class="text-uppercase small fw-bold text-muted">Balance</th> -->
                      <!-- <th class="text-uppercase small fw-bold text-muted">Status</th> -->
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(student, index) in filteredStudents" :key="student.id">
                      <td class="text-muted small fw-semibold">{{ index + 1 }}</td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="stu-avatar" :style="{ background: avatarGradient(student.name) }">
                            {{ initials(student.name) }}
                          </div>
                          <div>
                            <p class="mb-0 fw-semibold text-dark small">{{ student.name }}</p>
                            <small class="text-muted">{{ student.email }}</small>
                          </div>
                        </div>
                      </td>
                      <td><code class="text-muted small">{{ student.rollNo }}</code></td>
                      <td><code class="text-muted small">{{ student.course }}</code></td>
                      <td>
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
                          class="btn btn-dark btn-sm rounded-3 px-3"
                          @click="router.push(`/home/fee/payment/${student.id}`)"
                        >
                          <i class="fa fa-credit-card me-1"></i> Pay
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

const gradients = [
  ['#6366f1','#8b5cf6'], ['#f59e0b','#ef4444'], ['#10b981','#06b6d4'],
  ['#ec4899','#f43f5e'], ['#3b82f6','#6366f1'], ['#14b8a6','#10b981'],
]
const avatarGradient = (name) => {
  const idx = name.charCodeAt(0) % gradients.length
  return `linear-gradient(135deg, ${gradients[idx][0]}, ${gradients[idx][1]})`
}

const getStudentsByBatch = async(id)=>{
    const response = await api.get(`users/bybatchId/${id}`)
    students.value = response.data.data
    
}

watch(selectedBatch,(batchId)=>{
    getStudentsByBatch(batchId)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.pay-fee-wrapper {
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 28px;
  background: #f4f6fb;
  height: 100vh;
}

/* ── Step label ── */
.letter-wide { letter-spacing: 0.5px; }

.step-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  background: #0f172a;
  color: #fff;
  border-radius: 50%;
  font-size: 10px;
  font-weight: 800;
  margin-right: 6px;
}

/* ── Batch Chips ── */
.batch-chip {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 10px !important;
  padding: 8px 14px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  transition: all 0.15s ease;
}

.batch-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

/* ── Search ── */
.search-box {
  position: relative;
  width: 220px;
}
.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 11px;
  z-index: 1;
}

/* ── Student Avatar ── */
.stu-avatar {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 12px;
  font-weight: 800;
  flex-shrink: 0;
  letter-spacing: 0.5px;
}

/* ── Empty state ── */
.empty-state .empty-icon {
  width: 60px;
  height: 60px;
  background: #f1f5f9;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

/* ── Table ── */
.student-table-wrap {
  overflow-x: auto;
}

.table thead th {
  font-size: 11px;
  padding: 10px 14px;
  border-bottom: 1px solid #f0f2f5;
}

.table tbody td {
  padding: 12px 14px;
  border-bottom: 1px solid #f8f9fa;
  font-size: 13px;
}

.table tbody tr:last-child td { border-bottom: none; }

/* ── Modal ── */
.modal-avatar {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 14px;
  font-weight: 800;
  flex-shrink: 0;
  letter-spacing: 0.5px;
}

.balance-ribbon {
  background: #fff5f5;
  border: 1px solid #fecaca;
}

/* ── Payment Mode Chips ── */
.pay-mode-chip {
  display: inline-flex;
  align-items: center;
  padding: 7px 14px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.15s ease;
  user-select: none;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

.pay-mode-chip:hover {
  border-color: #111827;
  color: #111827;
}

.pay-mode-chip--active {
  background: #0f172a;
  border-color: #0f172a;
  color: #ffffff;
}

/* ── Subtle badge utilities ── */
.bg-success-subtle { background-color: rgba(16,185,129,0.12) !important; }
.bg-warning-subtle { background-color: rgba(245,158,11,0.12) !important; }
.bg-danger-subtle  { background-color: rgba(239,68,68,0.12)  !important; }
.bg-primary-subtle { background-color: rgba(99,102,241,0.12) !important; }

.text-success { color: #059669 !important; }
.text-warning { color: #d97706 !important; }
.text-danger  { color: #dc2626 !important; }
.text-primary { color: #4f46e5 !important; }

.border-primary-subtle { border-color: rgba(99,102,241,0.3) !important; }

/* ── modal font override ── */
.modal { font-family: 'Plus Jakarta Sans', sans-serif; }
.modal .form-label  { font-size: 12px; margin-bottom: 5px; }
.modal .form-control,
.modal .form-select { font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; }
.modal .input-group-text { font-size: 13px; }
</style>