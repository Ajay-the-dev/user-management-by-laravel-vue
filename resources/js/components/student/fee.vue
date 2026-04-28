<template>
  <div class="fee-page container-fluid py-4 px-4">

    <!-- STUDENT BAR -->
    <div class="student-bar rounded-4 p-4 mb-3 text-white position-relative overflow-hidden">
      <div class="student-bar__glow position-absolute top-0 end-0 w-50 h-100"></div>
      <div class="d-flex align-items-center gap-3">

        <div class="student-avatar rounded-3 d-flex align-items-center justify-content-center fw-bold flex-shrink-0 text-white">
          {{ initials }}
        </div>

        <div class="flex-grow-1">
          <h6 class="fw-bold mb-1 text-white text-capitalize">{{ userData.name }}</h6>
          <code class="student-meta">{{ userData.rollNo }} · {{ userData?.batches?.name }}</code>
        </div>

        <span class="balance-pill badge rounded-pill px-3 py-2 fw-semibold" :class="totalDue > 0 ? 'balance-pill--due' : 'balance-pill--paid'">
          {{ totalDue > 0 ? `${chosenCurrency} ` + totalDue.toLocaleString() + ' Due' : '✓ Fully Paid' }}
        </span>

      </div>
    </div>

    <!-- PROGRESS BAND -->
    <div class="card border rounded-4 mb-3 progress-card">
      <div class="card-body px-4 py-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="progress-label d-flex align-items-center gap-2">
            <span class="dot dot--green"></span>
            Paid &nbsp;<strong class="text-dark">{{ chosenCurrency }} {{ totalPaid.toLocaleString() }}</strong>
          </span>
          <span class="progress-percent fw-bold">{{ paidPercent }}%</span>
          <span class="progress-label">
            <strong class="text-dark">{{ chosenCurrency }} {{ totalAssigned.toLocaleString() }}</strong> &nbsp;Total
          </span>
        </div>
        <div class="progress rounded-pill progress-track">
          <div
            class="progress-bar progress-fill rounded-pill"
            role="progressbar"
            :style="{ width: paidPercent + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <!-- FEE CARDS GRID -->
    <div class="row g-3 mb-3" v-if="assignedFees.length && paidAmountsFetched">
      <div
        v-for="(fee, index) in assignedFees"
        :key="fee.id || index"
        class="col-12 col-md-6">
        <div class="fee-card card border rounded-4 h-100 position-relative overflow-hidden" :class="feeCardClass(fee)">

          <div class="fee-card__strip position-absolute top-0 start-0 end-0" :class="stripClass(fee)"></div>

          <div class="card-body px-4 pt-4 pb-3">

            <div class="d-flex align-items-center gap-2 mb-3">
              <div class="fee-icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" :class="iconBgClass(fee.type)">
                <i :class="iconClass(fee.type)"></i>
              </div>
              <span class="fw-semibold flex-grow-1 text-dark fee-type-label">{{ fee.type }}</span>
              <span class="badge rounded-pill fee-status-badge">
                <span v-if="parseFloat(paidAmounts[fee.id]?.balance) === 0" class="badge bg-success">PAID</span>
                <span v-else-if="parseFloat(paidAmounts[fee.id]?.balance) > 0 && !fee.due" class="badge bg-secondary">PENDING</span>
                <span v-else-if="parseFloat(paidAmounts[fee.id]?.balance) > 0 && new Date(fee.due) >= new Date()" class="badge bg-warning text-dark">PENDING</span>
                <span v-else class="badge bg-danger">OVERDUE</span>
              </span>
            </div>

            <div class="fee-amount fw-bold mb-3 text-dark">
              {{ chosenCurrency }} {{ Number(fee.amount).toLocaleString() }}
            </div>

            <div class="d-flex gap-3 flex-wrap">
              <span class="fee-meta text-muted d-flex align-items-center gap-1">
                <i class="fa fa-calendar-alt fee-meta__icon"></i>
                Due {{ formatDate(fee.due) }}
              </span>
            </div>

          </div>

          <div v-if="fee.payment_status === 'paid'" class="paid-stamp position-absolute bottom-0 end-0 mb-2 me-3">
            PAID
          </div>

        </div>
      </div>
    </div>

    <!-- EMPTY STATE -->
    <div v-if="!assignedFees.length" class="card border rounded-4 text-center py-5 empty-card">
      <div class="card-body">
        <div class="empty-icon rounded-3 d-inline-flex align-items-center justify-content-center mb-3 text-secondary">
          <i class="fa fa-question-circle fa-lg"></i>
        </div>
        <p class="fw-bold text-dark mb-1 empty-title">No fees assigned</p>
        <p class="text-muted mb-0 empty-sub">This student has no fee records yet</p>
      </div>
    </div>

    <!-- FOOTER SUMMARY -->
    <div v-if="assignedFees.length" class="card border rounded-4 mt-3 footer-summary">
      <div class="card-body py-3">
        <div class="row text-center g-0">

          <div class="col">
            <div class="summary-count fw-bold">
              {{ paidAmounts.filter(f => parseFloat(f?.balance) === 0).length }}
            </div>
            <div class="summary-label text-uppercase text-muted fw-semibold">Paid</div>
          </div>

          <div class="col border-start border-end">
            <div class="summary-count summary-count--pending fw-bold">
              {{ paidAmounts.filter(f => parseFloat(f?.balance) !== 0).length }}
            </div>
            <div class="summary-label text-uppercase text-muted fw-semibold">Pending</div>
          </div>

          <div class="col">
            <div class="summary-count summary-count--overdue fw-bold">
              {{ paidAmounts.filter(f => parseFloat(f?.balance) !== 0 && f.hasFeeDue).length }}
            </div>
            <div class="summary-label text-uppercase text-muted fw-semibold">Overdue</div>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/utils/axios'
import { useUserStore } from '@/stores/userStore'
const chosenCurrency = import.meta.env.VITE_CURRENCY


const userStore = useUserStore()
const userData = ref({})

const assignedFees = ref([])
const paidAmounts = ref([])
const paidAmountsFetched = ref(false)

const initials = computed(() =>
  userData?.value?.name?.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
)

onMounted(() => {
  userData.value = userStore.allData
})

watch(initials, () => {
  fetchAssignedFees()
})

const fetchAssignedFees = async () => {
  const res = await api.get(`/fees/bybatch/${userData?.value?.batchId}`)
  assignedFees.value = res.data.data
}

const totalAssigned = computed(() => assignedFees.value.reduce((s, f) => s + Number(f.amount), 0))

const totalPaid = computed(() => {
  let sum = 0
  paidAmounts.value.forEach(fee => { sum += parseFloat(fee.paid) })
  return sum
})

const totalDue    = computed(() => totalAssigned.value - totalPaid.value)
const paidPercent = computed(() => totalAssigned.value ? Math.round((totalPaid.value / totalAssigned.value) * 100) : 0)

const isOverdue = (fee) => fee.payment_status !== 'paid' && fee.due && new Date(fee.due) < new Date()

const feeCardClass = (fee) => isOverdue(fee) ? 'fee-card--overdue' : 'fee-card--default'

const stripClass = (fee) => {
  const balance = parseFloat(paidAmounts.value[fee.id]?.balance)
  if (balance === 0) return 'fee-card__strip--paid'
  if (balance > 0)   return 'fee-card__strip--pending'
  return 'fee-card__strip--warning'
}

const iconBgClass = (type) => {
  const map = {
    'Tuition':     'icon-bg--purple',
    'Lab Fee':     'icon-bg--blue',
    'Exam Fee':    'icon-bg--amber',
    'Library Fee': 'icon-bg--green',
  }
  return map[type] || 'icon-bg--default'
}

const iconClass = (type) => {
  const map = {
    'Tuition':     'fa fa-book',
    'Lab Fee':     'fa fa-flask',
    'Exam Fee':    'fa fa-file-alt',
    'Library Fee': 'fa fa-bookmark',
  }
  return map[type] || 'fa fa-tag'
}

const formatDate = (d) => {
  if (!d) return '— NIL'
  return new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })
}

const getPaidFeeDetails = async (feeId) => {
  const req = { userId: userData.value.id, feeId }
  const response = await api.post('/fees/paidFee/', req)
  return response.data.data
}

const loadPaidDetails = async (fees) => {
  paidAmounts.value = []
  for (const fee of fees) {    
    const amount = await getPaidFeeDetails(fee.id)
    paidAmounts.value[fee.id] = {
      paid: amount,
      balance: parseFloat(fee.amount) - parseFloat(amount),
      hasFeeDue: !fee.due === null ? true : false 
    }
  }
  paidAmountsFetched.value = true
}

watch(assignedFees, (feeData) => {
  loadPaidDetails(feeData)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap');

.fee-page {
  font-family: 'DM Sans', sans-serif;
  background: #f4f6fb;
  min-height: 100vh;
}

/* ── STUDENT BAR ── */
.student-bar {
  background: #111827;
}

.student-bar__glow {
  background: radial-gradient(ellipse at top right, rgba(99, 102, 241, 0.25), transparent 70%);
  pointer-events: none;
}

.student-avatar {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  font-size: 15px;
  letter-spacing: 1px;
}

.student-meta {
  font-size: 11px;
  color: #9ca3af;
}

.balance-pill--due {
  background: rgba(239, 68, 68, 0.15);
  color: #fca5a5;
  border: 1px solid rgba(239, 68, 68, 0.25);
}

.balance-pill--paid {
  background: rgba(16, 185, 129, 0.15);
  color: #6ee7b7;
  border: 1px solid rgba(16, 185, 129, 0.25);
}

/* ── PROGRESS BAND ── */
.progress-card {
  border-color: #eaecf0 !important;
}

.progress-label {
  font-size: 12px;
  color: #6b7280;
}

.progress-percent {
  font-size: 13px;
  font-family: monospace;
}

.dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
  flex-shrink: 0;
}

.dot--green { background: #10b981; }

.progress-track {
  height: 7px;
  background: #f1f5f9;
}

.progress-fill {
  background: linear-gradient(90deg, #10b981, #34d399) !important;
}

/* ── FEE CARD ── */
.fee-card {
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.fee-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.07) !important;
}

.fee-card--default {
  border-color: #eaecf0 !important;
}

.fee-card--overdue {
  border-color: #fecaca !important;
  background: #fff9f9;
}

.fee-card__strip {
  height: 3px;
  border-radius: 16px 16px 0 0;
}

.fee-card__strip--paid    { background: linear-gradient(90deg, #10b981, #34d399); }
.fee-card__strip--pending { background: linear-gradient(90deg, #ef4444, #f87171); }
.fee-card__strip--warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

/* ── FEE ICON ── */
.fee-icon {
  width: 32px;
  height: 32px;
}

.icon-bg--purple { background: #ede9fe; color: #7c3aed; }
.icon-bg--blue   { background: #dbeafe; color: #2563eb; }
.icon-bg--amber  { background: #fef3c7; color: #d97706; }
.icon-bg--green  { background: #dcfce7; color: #16a34a; }
.icon-bg--default{ background: #f3f4f6; color: #6b7280; }

.fee-type-label { font-size: 13px; }

.fee-amount {
  font-size: 22px;
  font-family: monospace;
  letter-spacing: -0.5px;
}

.fee-meta { font-size: 11px; }
.fee-meta__icon { font-size: 10px; }

/* ── PAID STAMP ── */
.paid-stamp {
  font-size: 9px;
  letter-spacing: 2px;
  background: #059669;
  color: #d1fae5;
  transform: rotate(-2deg);
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: 700;
}

/* ── EMPTY STATE ── */
.empty-card {
  border-style: dashed !important;
  border-color: #e5e7eb !important;
}

.empty-icon {
  width: 58px;
  height: 58px;
  background: #f3f4f6;
}

.empty-title { font-size: 14px; }
.empty-sub   { font-size: 12px; }

/* ── FOOTER SUMMARY ── */
.footer-summary {
  border-color: #eaecf0 !important;
  background: #f8fafc;
}

.summary-count {
  font-size: 20px;
  font-family: monospace;
  color: #0f172a;
}

.summary-count--pending { color: #d97706; }
.summary-count--overdue { color: #dc2626; }

.summary-label {
  font-size: 10px;
  letter-spacing: 0.5px;
}
</style>