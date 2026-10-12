<template>
  <div class="fee-page container-fluid px-1">

    <!-- STUDENT BAR -->
    <div class="hero-bar">
      <div class="d-flex align-items-center gap-3">

        <div class="hero-bar__avatar" v-if="userData.profile_picture !== null">
          <img :src="userData.profile_picture" alt="">
        </div>
        <div class="hero-bar__avatar" v-else>
          {{ initials }}
        </div>
        <div class="flex-grow-1">
          <h6 class="fw-bold mb-1 text-white text-capitalize">{{ userData.name }}</h6>
          <code class="hero-bar__meta">{{ userData.rollNo }} · {{ userData?.batches?.name }}</code>
        </div>

        <span class="balance-pill badge rounded-pill px-3 py-2 fw-semibold" :class="totalDue > 0 ? 'balance-pill--due' : 'balance-pill--paid'">
          {{ totalDue > 0 ? `${chosenCurrency} ` + totalDue.toLocaleString() + ' Due' : '✓ Fully Paid' }}
        </span>

      </div>
    </div>

    <!-- PROGRESS BAND -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
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
    <div v-if="!assignedFees.length" class="card rounded-4 empty-card">
      <div class="empty-state">
        <div class="icon-tile icon-tile--lg icon-tile--muted">
          <i class="fa fa-question-circle fa-lg"></i>
        </div>
        <p class="empty-state__title">No fees assigned</p>
        <p class="empty-state__sub">This student has no fee records yet</p>
      </div>
    </div>

    <!-- FOOTER SUMMARY -->
    <div v-if="assignedFees.length" class="card rounded-4 mt-3 footer-summary">
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

<style scoped lang="scss">
@use "sass:color";
@use "abstracts" as *;

// ── BALANCE PILL (inside .hero-bar) ──
.balance-pill--due {
  background: color.change($red-500, $alpha: 0.18);
  color: #fecaca;
  border: 1px solid color.change($red-500, $alpha: 0.3);
}

.balance-pill--paid {
  background: color.change($emerald-500, $alpha: 0.18);
  color: #a7f3d0;
  border: 1px solid color.change($emerald-500, $alpha: 0.3);
}

// ── PROGRESS BAND ──
.progress-label {
  font-size: 0.75rem;
  color: $text-muted;
}

.progress-percent {
  font-family: $font-display;
  font-variant-numeric: tabular-nums;
  font-size: $fs-sm;
  color: $sky-700;
}

.dot {
  @include square(0.5rem);
  display: inline-block;
  flex-shrink: 0;
  border-radius: 50%;

  &--green { background: $emerald-500; }
}

.progress-track {
  height: 0.5rem;
}

.progress-fill {
  background: linear-gradient(90deg, $sky-500, $emerald-500) !important;
}

// ── FEE CARD ──
.fee-card {
  @include hover-lift;

  &--default {
    border-color: $border-soft !important;
  }

  &--overdue {
    border-color: #fecaca !important;
    background: #fff9f9;
  }

  &__strip {
    height: 3px;
    border-radius: $radius-lg $radius-lg 0 0;

    &--paid    { background: linear-gradient(90deg, $emerald-500, #34d399); }
    &--pending { background: linear-gradient(90deg, $red-500, #f87171); }
    &--warning { background: linear-gradient(90deg, $amber-500, #fbbf24); }
  }
}

// ── FEE ICON ──
.fee-icon {
  @include square(2rem);
}

.icon-bg--purple  { background: $violet-100;  color: $violet-600; }
.icon-bg--blue    { background: $sky-100;     color: $sky-600; }
.icon-bg--amber   { background: $amber-100;   color: $amber-600; }
.icon-bg--green   { background: #dcfce7;      color: #16a34a; }
.icon-bg--default { background: $slate-100;   color: $slate-500; }

.fee-type-label { font-size: $fs-sm; }

.fee-amount {
  font-family: $font-display;
  font-variant-numeric: tabular-nums;
  font-size: $fs-lg;
  letter-spacing: -0.5px;
}

.fee-meta { font-size: $fs-xs; }
.fee-meta__icon { font-size: $fs-2xs; }

// ── PAID STAMP ──
.paid-stamp {
  padding: 0.1875rem 0.5rem;
  border-radius: 4px;
  background: $emerald-600;
  color: $emerald-100;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 2px;
  transform: rotate(-2deg);
}

// ── EMPTY STATE ──
.empty-card {
  border-style: dashed !important;
  border-color: $sky-200 !important;
}

// ── FOOTER SUMMARY ──
.footer-summary {
  background: $sky-50;
  border-color: $border-soft !important;
}

.summary-count {
  font-family: $font-display;
  font-variant-numeric: tabular-nums;
  font-size: 1.25rem;
  color: $sky-900;

  &--pending { color: $amber-600; }
  &--overdue { color: $red-600; }
}

.summary-label {
  font-size: $fs-2xs;
  letter-spacing: $tracking-wide;
}
</style>