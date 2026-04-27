<template>
  <div class="fees-dashboard px-3 py-4" style="background:#f4f6fb; min-height:100vh; font-family:'Plus Jakarta Sans',sans-serif;">

    <!-- TOP SUMMARY CARD -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
      <div class="card-header bg-white border-bottom border-light d-flex align-items-center justify-content-between rounded-top-4 px-4 py-3">
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
            <h2 class="mb-0 fw-bold" style="font-size:15px;color:#111827;letter-spacing:-0.2px;">Fee Collection</h2>
            <span class="badge rounded-pill text-secondary bg-light border" style="font-size:11px;font-weight:600;letter-spacing:0.3px;">FY 2025–26</span>
          </div>
        </div>
        <button class="btn btn-dark fw-bold d-flex align-items-center gap-2" style="font-size:13px;border-radius:10px;" @click="router.push('/home/fee/pay')">
          <i class="fa fa-hand-holding-dollar"></i>
          Pay fee
        </button>
      </div>

      <!-- Stats Row -->
      <div class="row g-0 border-bottom border-light px-4 py-3 align-items-center">
        <div class="col d-flex flex-column gap-1">
          <span class="text-uppercase text-secondary" style="font-size:11px;font-weight:500;letter-spacing:0.5px;">Total Collected</span>
          <span class="fw-bold" style="font-size:22px;color:#059669;letter-spacing:-0.5px;">$ 45,250</span>
          <span class="text-secondary" style="font-size:11px;font-weight:500;">↑ 12% this month</span>
        </div>
        <div class="col-auto"><div class="vr mx-3" style="height:48px;"></div></div>
        <div class="col d-flex flex-column gap-1">
          <span class="text-uppercase text-secondary" style="font-size:11px;font-weight:500;letter-spacing:0.5px;">Pending Dues</span>
          <span class="fw-bold" style="font-size:22px;color:#dc2626;letter-spacing:-0.5px;">$ 12,800</span>
          <span class="text-secondary" style="font-size:11px;font-weight:500;">↓ 3 students</span>
        </div>
        <div class="col-auto"><div class="vr mx-3" style="height:48px;"></div></div>
        <div class="col d-flex flex-column gap-1">
          <span class="text-uppercase text-secondary" style="font-size:11px;font-weight:500;letter-spacing:0.5px;">Total Students</span>
          <span class="fw-bold" style="font-size:22px;color:#4f46e5;letter-spacing:-0.5px;">{{ allFees.length || 48 }}</span>
          <span class="text-secondary" style="font-size:11px;font-weight:500;">Enrolled this FY</span>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="px-4 pt-3 pb-1 text-uppercase text-secondary fw-bold" style="font-size:12px;letter-spacing:0.6px;">Recent Transactions</div>
      <div class="px-3 pb-3 d-flex flex-column gap-1">
        <div
          v-for="(tx, index) in transactions"
          :key="index"
          class="d-flex align-items-center gap-3 px-3 py-2 rounded-3"
          style="cursor:default;transition:background 0.15s;"
          @mouseover="$event.currentTarget.style.background='#f8fafc'"
          @mouseleave="$event.currentTarget.style.background=''"
        >
          <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold flex-shrink-0"
            style="width:34px;height:34px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#6d28d9;font-size:13px;">
            {{ tx.studentName.charAt(0) }}
          </div>
          <div class="flex-grow-1 d-flex flex-column" style="gap:1px;">
            <span class="fw-semibold" style="font-size:13px;color:#111827;">{{ tx.studentName }}</span>
            <span class="text-secondary" style="font-size:11px;font-weight:500;">{{ tx.id }} · {{ tx.method }}</span>
          </div>
          <div class="d-flex flex-column align-items-end" style="gap:3px;">
            <span class="fw-bold" style="font-size:13px;color:#111827;">+${{ tx.amount }}</span>
            <span
              class="badge rounded-pill"
              :class="tx.status === 'Completed' ? 'text-success' : 'text-warning'"
              :style="tx.status === 'Completed' ? 'background:#d1fae5;font-size:10px;' : 'background:#fef3c7;font-size:10px;'"
            >
              {{ tx.status }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- FEES OVERVIEW TABLE -->
    <div class="card border-0 rounded-4 shadow-sm">
      <div class="card-header bg-white border-bottom border-light d-flex align-items-center justify-content-between rounded-top-4 px-4 py-3">
        <h2 class="mb-0 fw-bold" style="font-size:15px;color:#111827;letter-spacing:-0.2px;">Fees Overview</h2>
        <div class="d-flex gap-2">
          <span class="badge rounded-pill text-white bg-dark border" style="font-size:11px;font-weight:600;cursor:pointer;padding:5px 12px;">All</span>
          <span class="badge rounded-pill text-secondary bg-light border" style="font-size:11px;font-weight:600;cursor:pointer;padding:5px 12px;">Active</span>
          <span class="badge rounded-pill text-secondary bg-light border" style="font-size:11px;font-weight:600;cursor:pointer;padding:5px 12px;">Inactive</span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!allFees.length" class="text-center py-5 text-secondary">
        <div class="d-flex align-items-center justify-content-center rounded-3 mx-auto mb-3"
          style="width:56px;height:56px;background:#f3f4f6;color:#d1d5db;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <p class="mb-1 fw-semibold" style="font-size:14px;color:#374151;">No fee records found</p>
        <p class="mb-0" style="font-size:12px;">Add fee entries to see them listed here</p>
      </div>

      <!-- Table -->
      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="border-bottom">
            <tr>
              <th class="text-uppercase text-secondary px-4" style="font-size:11px;font-weight:700;letter-spacing:0.5px;">#</th>
              <th class="text-uppercase text-secondary" style="font-size:11px;font-weight:700;letter-spacing:0.5px;">Batch</th>
              <th class="text-uppercase text-secondary" style="font-size:11px;font-weight:700;letter-spacing:0.5px;">Type</th>
              <th class="text-uppercase text-secondary" style="font-size:11px;font-weight:700;letter-spacing:0.5px;">Amount</th>
              <th class="text-uppercase text-secondary" style="font-size:11px;font-weight:700;letter-spacing:0.5px;">Due date</th>
              <th class="text-uppercase text-secondary" style="font-size:11px;font-weight:700;letter-spacing:0.5px;">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(fee, index) in allFees" :key="fee.id || index">
              <td class="px-4 fw-bold text-secondary" style="font-size:12px;">{{ index + 1 }}</td>
              <td class="fw-semibold" style="font-size:13px;color:#111827;">{{ fee.batches?.name || 'N/A' }}</td>
              <td>
                <span class="badge rounded-pill" style="background:#eff6ff;color:#3b82f6;font-size:11px;font-weight:600;padding:4px 10px;">{{ fee.type }}</span>
              </td>
              <td class="fw-bold" style="font-size:14px;color:#059669;">$ {{ fee.amount }}</td>
              <td style="font-size:13px;color:#374151;">{{ fee.due ?? '-' }}</td>
              <td>
                <span
                  class="d-inline-flex align-items-center gap-2 fw-semibold text-capitalize"
                  :class="fee.status === 'active' ? 'text-success' : 'text-warning'"
                  style="font-size:12px;"
                >
                  <span
                    class="rounded-circle"
                    :style="fee.status === 'active'
                      ? 'width:7px;height:7px;background:#10b981;box-shadow:0 0 0 2px #d1fae5;flex-shrink:0;display:inline-block;'
                      : 'width:7px;height:7px;background:#f59e0b;box-shadow:0 0 0 2px #fef3c7;flex-shrink:0;display:inline-block;'"
                  ></span>
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
import { ref, onMounted } from 'vue'
import api from '@/utils/axios'
import { useRoute, useRouter } from 'vue-router'

const routes = useRoute()
const router = useRouter()
const baseURL = import.meta.env.VITE_API_BASE_URL

const transactions = ref([
  { studentName: 'Ajay C',      id: 'STU-9746', method: 'Bank Transfer', amount: '1,200', status: 'Completed' },
  { studentName: 'Rahul Sharma', id: 'STU-8821', method: 'Credit Card',   amount: '850',   status: 'Pending'   },
  { studentName: 'Sneha Patel',  id: 'STU-7742', method: 'UPI',           amount: '2,100', status: 'Completed' },
])

const allFees = ref([])

onMounted(() => {
  getAllFees()
})

const getAllFees = async () => {
  try {
    const response = await api.get(`${baseURL}/fees`)
    allFees.value = response.data.data
  } catch (err) {
    console.error(err)
  }
}
</script>