<template>
  <div class="p-4 content-scroll" style="background:#f4f6fb; height:100vh;">

    <!-- TOP SUMMARY CARD -->
    <div class="card border-0 rounded-4 shadow-sm">
      <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between rounded-top-4 px-4 py-3">
        <div class="d-flex align-items-center gap-3">
          <div class="d-flex align-items-center justify-content-center rounded-3" style="width:36px;height:36px;background:linear-gradient(135deg,#e8f5ee,#d1fae5);color:#059669;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
          </div>
          <div>
            <h2 class="fw-bold mb-0" style="font-size:15px;color:#111827;letter-spacing:-0.2px;">Fee Collection</h2>
            <span class="badge rounded-pill text-secondary border mt-1" style="font-size:11px;background:#f3f4f6;">{{ finYear }}</span>
          </div>
        </div>
        <button class="fw-bold btn btn-sm btn-outline-secondary" @click="refreshHelper">
          <i class="fa fa-refresh slow-rotation" id="rotater"></i>
           Refresh
        </button>
        <button class="fw-bold btn btn-sm btn-primary" @click="router.push('/home/fee/pay')">
          <i class="fa fa-hand-holding-dollar"></i>
          Pay fee
        </button>
      </div>

      <!-- Stats Row -->
      <div class="d-flex align-items-stretch px-4 py-3 border-bottom">
        <div class="flex-fill d-flex flex-column gap-1">
          <span class="text-uppercase fw-500" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Total Collected</span>
          <span class="fw-bold" style="font-size:22px;color:#059669;letter-spacing:-.5px;">{{ chosenCurrency }} {{ totalCollected }}</span>
          <!-- <span style="font-size:11px;color:#9ca3af;">↑ 12% this month</span> -->
        </div>
        <div class="vr mx-4 my-1"></div>
        <div class="flex-fill d-flex flex-column gap-1">
          <span class="text-uppercase fw-500" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Pending Dues</span>
          <span class="fw-bold" style="font-size:22px;color:#dc2626;letter-spacing:-.5px;">{{ chosenCurrency }} {{ totalPending }}</span>
          <!-- <span style="font-size:11px;color:#9ca3af;">↓ 3 students</span> -->
        </div>
        <div class="vr mx-4 my-1"></div>
        <div class="flex-fill d-flex flex-column gap-1">
          <span class="text-uppercase fw-500" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Fee forms active</span>
          <span class="fw-bold" style="font-size:22px;color:#4f46e5;letter-spacing:-.5px;">{{ allFees.length || 48 }}</span>
          <!-- <span style="font-size:11px;color:#9ca3af;">Enrolled this FY</span> -->
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="px-4 pt-3 pb-1">
        <span class="text-uppercase fw-bold" style="font-size:12px;color:#6b7280;letter-spacing:.6px;">Recent Transactions</span>
      </div>
      <div class="px-3 pb-3 d-flex flex-column gap-1">
        <div
          v-for="(tx, index) in transactions"
          :key="index"
          class="d-flex align-items-center gap-3 px-3 py-2 rounded-3"
          style="cursor:default;transition:background .15s;"
          @mouseenter="e => e.currentTarget.style.background='#f8fafc'"
          @mouseleave="e => e.currentTarget.style.background=''"
        >
          <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold flex-shrink-0" style="width:34px;height:34px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#6d28d9;font-size:13px;">
            <!-- {{ tx.studentName.charAt(0) }} -->{{ tx.student.name.charAt(0) }}
          </div>
          <div class="flex-fill d-flex flex-column" style="gap:1px;">
            <span class="fw-semibold" style="font-size:13px;color:#111827;">{{ tx.student.name }}</span>
            <span style="font-size:11px;color:#9ca3af;">{{ JSON.parse(tx.payment).paymentReference }} · {{ JSON.parse(tx.payment).selectedPaymentMode }}</span>
          </div>
          <div class="d-flex flex-column align-items-end" style="gap:3px;">
            <span class="fw-bold" style="font-size:13px;color:#111827;">+{{chosenCurrency}}{{ tx.paid_amount }}</span>
            <span
              class="badge rounded-pill"
              :style="parseFloat(tx.fee.amount) === parseFloat(tx.paid_amount)
                ? 'background:#d1fae5;color:#059669;font-size:10px;'
                : 'background:#fef3c7;color:#d97706;font-size:10px;'"
            >
              {{ parseFloat(tx.fee.amount) === parseFloat(tx.paid_amount)  ? 'Fully paid' : 'Partially paid'}}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- FEES OVERVIEW TABLE -->
    <div class="card border-0 rounded-4 shadow-sm p-2 my-5">
      <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between rounded-top-4 px-4 py-3">
        <h2 class="fw-bold mb-0" style="font-size:15px;color:#111827;">Fees Overview</h2>
        <div class="d-flex gap-2" v-if="false">
          <span class="badge rounded-pill px-3 py-2" style="font-size:11px;background:#111827;color:#fff;cursor:pointer;">All</span>
          <span class="badge rounded-pill border px-3 py-2" style="font-size:11px;color:#6b7280;cursor:pointer;">Active</span>
          <span class="badge rounded-pill border px-3 py-2" style="font-size:11px;color:#6b7280;cursor:pointer;">Inactive</span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!allFees.length" class="text-center py-5 px-4">
        <div class="d-flex align-items-center justify-content-center rounded-3 mx-auto mb-3" style="width:56px;height:56px;background:#f3f4f6;color:#d1d5db;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <p class="fw-semibold mb-1" style="font-size:14px;color:#374151;">No fee records found</p>
        <p class="mb-0" style="font-size:12px;color:#9ca3af;">Add fee entries to see them listed here</p>
      </div>

      <!-- Table -->
      <div v-else class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr class="border-bottom">
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">#</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Batch</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Type</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Amount</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Due date</th>
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(fee, index) in allFees" :key="fee.id || index">
              <td class="px-4 py-3 fw-bold" style="font-size:12px;color:#9ca3af;">{{ index + 1 }}</td>
              <td class="px-4 py-3 fw-semibold" style="font-size:13px;color:#111827;">{{ fee.batches?.name || 'N/A' }}</td>
              <td class="px-4 py-3">
                <span class="badge rounded-pill px-3 py-1" style="background:#eff6ff;color:#3b82f6;font-size:11px;">{{ fee.type }}</span>
              </td>
              <td class="px-4 py-3 fw-bold" style="font-size:14px;color:#059669;">$ {{ fee.amount }}</td>
              <td class="px-4 py-3" style="font-size:13px;color:#374151;">{{ fee.due ?? '-' }}</td>
              <td class="px-4 py-3">
                <span class="d-inline-flex align-items-center gap-2 fw-semibold text-capitalize" style="font-size:12px;" :style="fee.status === 'active' ? 'color:#059669;' : 'color:#d97706;'">
                  <span class="rounded-circle d-inline-block" style="width:7px;height:7px;flex-shrink:0;" :style="fee.status === 'active' ? 'background:#10b981;box-shadow:0 0 0 2px #d1fae5;' : 'background:#f59e0b;box-shadow:0 0 0 2px #fef3c7;'"></span>
                  {{ fee.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/utils/axios'
import { useRoute, useRouter } from 'vue-router'

const routes = useRoute()
const router = useRouter()
const baseURL = import.meta.env.VITE_API_BASE_URL
const chosenCurrency = import.meta.env.VITE_CURRENCY
const finYear = import.meta.env.VITE_FINYEAR
const transactions = ref([])

const totalCollected = ref(0)
const totalAssigned = ref(0)

const totalPending = computed(()=>totalAssigned.value - totalCollected.value)


const allFees = ref([])

onMounted(() => {
  getAllFees()
  getYearlyReportWithRecents()
})

const refreshHelper = () =>{
  const el = document.getElementById('rotater');
  el.classList.add('active');
  getAllFees()
  getYearlyReportWithRecents()
  setTimeout(() => {
      el.classList.remove('active');
  }, 5000);
}

const getAllFees = async () => {
  try {
    const response = await api.get(`${baseURL}/fees`)
    allFees.value = response.data.data
  } catch (err) {
    console.error(err)
  }
}

const getYearlyReportWithRecents = async () =>{
  try {
      const response = await api.get('/fees/yearly')
      console.log(response.data.data);
      let money = response.data.data
      totalAssigned.value = money.totalAssigned
      totalCollected.value = money.totalAmount
      transactions.value = money.recentRecords

  } catch (error) {
        console.error(err)
  }
}
</script>
<style scoped>
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.slow-rotation {
  /* 10s = very slow, linear = constant speed */
  animation: spin 5s linear infinite;
  animation-play-state: paused; /* Start paused */
}

.slow-rotation.active {
  animation-play-state: running;
}

</style>