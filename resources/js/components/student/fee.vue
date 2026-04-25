<template>
  <div class="container-fluid py-4 px-4">

    <!-- ─── STUDENT BAR ─── -->
    <div class="rounded-4 p-4 mb-3 text-white position-relative overflow-hidden bg-dark" >
      <div class="position-absolute top-0 end-0 w-50 h-100" style="background: radial-gradient(ellipse at top right, rgba(99,102,241,0.25), transparent 70%); pointer-events:none;"></div>
      <div class="d-flex align-items-center gap-3">

        <!-- Avatar -->
        <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold flex-shrink-0 text-white"
             style="width:48px; height:48px; background: linear-gradient(135deg,#6366f1,#8b5cf6); font-size:15px; letter-spacing:1px;">
          {{ initials }}
        </div>

        <!-- Info -->
        <div class="flex-grow-1">
          <h6 class="fw-bold mb-1 text-white text-capitalize">{{ userData.name }}</h6>
          <code class="text-secondary" style="font-size:11px;">{{ userData.rollNo }} · {{ userData?.batches?.name }}</code>
        </div>

        <!-- Balance Pill -->
        <span
          class="badge rounded-pill px-3 py-2 fw-semibold"
          :style="totalDue > 0
            ? 'background:rgba(239,68,68,0.15); color:#fca5a5; border:1px solid rgba(239,68,68,0.25)'
            : 'background:rgba(16,185,129,0.15); color:#6ee7b7; border:1px solid rgba(16,185,129,0.25)'"
        >
          {{ totalDue > 0 ? ' $ ' + totalDue.toLocaleString() + ' Due' : '✓ Fully Paid' }}
        </span>

      </div>
    </div>

    <!-- ─── PROGRESS BAND ─── -->
    <div class="card border rounded-4 mb-3" style="border-color:#eaecf0 !important;">
      <div class="card-body px-4 py-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted d-flex align-items-center gap-2" style="font-size:12px;">
            <span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#10b981;"></span>
            Paid &nbsp;<strong class="text-dark"> $ {{ totalPaid.toLocaleString() }}</strong>
          </span>
          <span class="fw-bold" style="font-size:13px; font-family: monospace;">{{ paidPercent }}%</span>
          <span class="text-muted" style="font-size:12px;">
            <strong class="text-dark"> $ {{ totalAssigned.toLocaleString() }}</strong> &nbsp;Total
          </span>
        </div>
        <div class="progress rounded-pill" style="height:7px; background:#f1f5f9;">
          <div
            class="progress-bar rounded-pill"
            role="progressbar"
            :style="{ width: paidPercent + '%', background: 'linear-gradient(90deg,#10b981,#34d399)' }"
          ></div>
        </div>
      </div>
    </div>

    <!-- ─── FEE CARDS GRID ─── -->
    <div class="row g-3 mb-3" v-if="assignedFees.length">
      <div
        v-for="(fee, index) in assignedFees"
        :key="fee.id || index"
        class="col-12 col-md-6"
      >
        <div
          class="card border rounded-4 h-100 position-relative overflow-hidden fee-card"
          :style="cardStyle(fee)"
        >
          <!-- Colored top strip -->
          <div class="position-absolute top-0 start-0 end-0" :style="'height:3px; border-radius:16px 16px 0 0; background:' + stripColor(fee)"></div>

          <div class="card-body px-4 pt-4 pb-3">

            <!-- Top Row -->
            <div class="d-flex align-items-center gap-2 mb-3">
              <div
                class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                :style="'width:32px;height:32px;' + iconStyle(fee.type)"
              >
                <i :class="iconClass(fee.type)" style="font-size:13px;"></i>
              </div>

              <span class="fw-semibold flex-grow-1 text-dark" style="font-size:13px;">{{ fee.type }}</span>

              <span
                class="badge rounded-pill d-flex align-items-center gap-1 px-2 py-1"
                :style="badgeStyle(fee)"
                style="font-size:11px; font-weight:600;"
              >
                <span class="rounded-circle" :style="'width:5px;height:5px;display:inline-block;background:' + dotColor(fee)"></span>
                {{ statusLabel(fee) }}
              </span>
            </div>

            <!-- Amount -->
            <div class="fw-bold mb-3 text-dark" style="font-size:22px; font-family:monospace; letter-spacing:-0.5px;">
               $ {{ Number(fee.amount).toLocaleString() }}
            </div>

            <!-- Meta -->
            <div class="d-flex gap-3 flex-wrap">
              <span class="text-muted d-flex align-items-center gap-1" style="font-size:11px;">
                <i class="fa fa-calendar-alt" style="font-size:10px;"></i>
                Due {{ formatDate(fee.due) }}
              </span>
            </div>

          </div>

          <!-- Paid stamp -->
          <div
            v-if="fee.payment_status === 'paid'"
            class="position-absolute bottom-0 end-0 mb-2 me-3"
            style="font-size:9px; letter-spacing:2px; background:#059669; color:#d1fae5; transform:rotate(-2deg); padding: 3px 8px; border-radius:4px; font-weight:700;"
          >
            PAID
          </div>

        </div>
      </div>
    </div>

    <!-- ─── EMPTY STATE ─── -->
    <div v-if="!assignedFees.length" class="card border rounded-4 text-center py-5" style="border-style:dashed !important; border-color:#e5e7eb !important;">
      <div class="card-body">
        <div class="rounded-3 d-inline-flex align-items-center justify-content-center mb-3 text-secondary"
             style="width:58px;height:58px;background:#f3f4f6;">
          <i class="fa fa-question-circle fa-lg"></i>
        </div>
        <p class="fw-bold text-dark mb-1" style="font-size:14px;">No fees assigned</p>
        <p class="text-muted mb-0" style="font-size:12px;">This student has no fee records yet</p>
      </div>
    </div>

    <!-- ─── FOOTER SUMMARY ─── -->
    <div v-if="assignedFees.length" class="card border rounded-4 mt-3" style="border-color:#eaecf0 !important; background:#f8fafc;">
      <div class="card-body py-3">
        <div class="row text-center g-0">

          <div class="col">
            <div class="fw-bold" style="font-size:20px; font-family:monospace; color:#0f172a;">
              {{ assignedFees.filter(f => f.payment_status === 'paid').length }}
            </div>
            <div class="text-uppercase text-muted fw-semibold" style="font-size:10px; letter-spacing:0.5px;">Paid</div>
          </div>

          <div class="col border-start border-end">
            <div class="fw-bold" style="font-size:20px; font-family:monospace; color:#d97706;">
              {{ assignedFees.filter(f => f.payment_status !== 'paid' && !isOverdue(f)).length }}
            </div>
            <div class="text-uppercase text-muted fw-semibold" style="font-size:10px; letter-spacing:0.5px;">Pending</div>
          </div>

          <div class="col">
            <div class="fw-bold" style="font-size:20px; font-family:monospace; color:#dc2626;">
              {{ assignedFees.filter(f => isOverdue(f)).length }}
            </div>
            <div class="text-uppercase text-muted fw-semibold" style="font-size:10px; letter-spacing:0.5px;">Overdue</div>
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

const userStore = useUserStore()

const userData = ref({})

const props = defineProps({
  studentId:   { type: String, default: 'STU-7742' },
  studentName: { type: String, default: 'Sneha Patel' },
  batchName:   { type: String, default: 'Batch A – Morning' },
})

const { studentId, studentName, batchName } = props

const assignedFees = ref([])

const initials = computed(() =>
  userData?.value?.name?.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()
)

onMounted(() =>{
      userData.value = userStore.allData
})


watch(initials,(newInitials)=>{
    fetchAssignedFees();
})
const fetchAssignedFees = async () => {
  const res = await api.get(`/fees/bybatch/${userData?.value?.batchId}`)
  
  assignedFees.value = res.data.data

//   assignedFees.value = [
//     { id: 1, type: 'Tuition',     amount: 5000, status: 'active', payment_status: 'paid',    due: '2025-06-01', batches: { name: 'Batch A – Morning' } },
//     { id: 2, type: 'Lab Fee',     amount: 1200, status: 'active', payment_status: 'pending',  due: '2025-07-15', batches: { name: 'Batch A – Morning' } },
//     { id: 3, type: 'Exam Fee',    amount: 800,  status: 'active', payment_status: 'pending',  due: '2025-04-10', batches: { name: 'Batch A – Morning' } },
//     { id: 4, type: 'Library Fee', amount: 500,  status: 'active', payment_status: 'paid',     due: '2025-05-01', batches: { name: 'Batch A – Morning' } },
//   ]

}

// ── Computed ─────────────────────────────────────────────────────────
const totalAssigned = computed(() => assignedFees.value.reduce((s, f) => s + Number(f.amount), 0))
const totalPaid     = computed(() => assignedFees.value.filter(f => f.payment_status === 'paid').reduce((s, f) => s + Number(f.amount), 0))
const totalDue      = computed(() => totalAssigned.value - totalPaid.value)
const paidPercent   = computed(() => totalAssigned.value ? Math.round((totalPaid.value / totalAssigned.value) * 100) : 0)

// ── Helpers ──────────────────────────────────────────────────────────
const isOverdue = (fee) => fee.payment_status !== 'paid' && fee.due && new Date(fee.due) < new Date()

const statusLabel = (fee) => {
  if (fee.payment_status === 'paid') return 'Paid'
  if (isOverdue(fee)) return 'Overdue'
  return 'Pending'
}

const cardStyle = (fee) => {
  if (isOverdue(fee)) return 'border-color:#fecaca !important; background:#fff9f9;'
  return 'border-color:#eaecf0 !important;'
}

const stripColor = (fee) => {
  if (fee.payment_status === 'paid') return 'linear-gradient(90deg,#10b981,#34d399)'
  if (isOverdue(fee))                return 'linear-gradient(90deg,#ef4444,#f87171)'
  return 'linear-gradient(90deg,#f59e0b,#fbbf24)'
}

const badgeStyle = (fee) => {
  if (fee.payment_status === 'paid') return 'background:#d1fae5; color:#059669;'
  if (isOverdue(fee))                return 'background:#fee2e2; color:#dc2626;'
  return 'background:#fef3c7; color:#d97706;'
}

const dotColor = (fee) => {
  if (fee.payment_status === 'paid') return '#10b981'
  if (isOverdue(fee))                return '#ef4444'
  return '#f59e0b'
}

const iconStyle = (type) => {
  const map = {
    'Tuition':     'background:#ede9fe; color:#7c3aed;',
    'Lab Fee':     'background:#dbeafe; color:#2563eb;',
    'Exam Fee':    'background:#fef3c7; color:#d97706;',
    'Library Fee': 'background:#dcfce7; color:#16a34a;',
  }
  return map[type] || 'background:#f3f4f6; color:#6b7280;'
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
  if (!d) return '- NIL'
  return new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap');

.fee-card {
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.fee-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.07) !important;
}
</style>