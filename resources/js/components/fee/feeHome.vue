<template>
  <div class="fees-dashboard">

    <!-- TOP SUMMARY CARD -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
      <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2 rounded-top-4 px-3 px-md-4 py-3">
        <div class="d-flex align-items-center gap-3">
          <div class="icon-tile icon-tile--primary">
            <i class="fa fa-file-invoice-dollar"></i>
          </div>
          <div>
            <h2 class="card-heading">Fee Collection</h2>
            <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis fs-xs">{{ finYear }}</span>
          </div>
        </div>
        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2" @click="router.push('/home/fee/pay')">
          <i class="fa fa-hand-holding-dollar"></i>
          Pay fee
        </button>
      </div>

      <!-- Stats Row -->
      <div class="stat-row">
        <div class="stat">
          <span class="stat__label">Total Collected</span>
          <span class="stat__value stat__value--success">$ 45,250</span>
          <span class="stat__hint">↑ 12% this month</span>
        </div>
        <div class="vr"></div>
        <div class="stat">
          <span class="stat__label">Pending Dues</span>
          <span class="stat__value stat__value--danger">$ 12,800</span>
          <span class="stat__hint">↓ 3 students</span>
        </div>
        <div class="vr"></div>
        <div class="stat">
          <span class="stat__label">Total Students</span>
          <span class="stat__value stat__value--primary">{{ allFees.length || 48 }}</span>
          <span class="stat__hint">Enrolled this FY</span>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="px-4 pt-3 pb-1 eyebrow">Recent Transactions</div>
      <div class="px-3 pb-3 d-flex flex-column gap-1">
        <div
          v-for="(tx, index) in transactions"
          :key="index"
          class="tx-row"
        >
          <div class="avatar">
            {{ tx.studentName.charAt(0) }}
          </div>
          <div class="tx-row__body">
            <span class="tx-row__name">{{ tx.studentName }}</span>
            <span class="tx-row__meta">{{ tx.id }} · {{ tx.method }}</span>
          </div>
          <div class="tx-row__end">
            <span class="tx-row__amount">+${{ tx.amount }}</span>
            <span
              class="badge rounded-pill fs-2xs"
              :class="tx.status === 'Completed' ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis'"
            >
              {{ tx.status }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- FEES OVERVIEW TABLE -->
    <div class="card border-0 rounded-4 shadow-sm">
      <div class="card-header bg-white list-header rounded-top-4 px-3 px-md-4 py-3">
        <h2 class="card-heading">Fees Overview</h2>
        <div class="d-flex gap-2">
          <span class="chip chip--active">All</span>
          <span class="chip">Active</span>
          <span class="chip">Inactive</span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!allFees.length" class="empty-state">
        <div class="icon-tile icon-tile--lg icon-tile--muted">
          <i class="fa fa-circle-exclamation fa-lg"></i>
        </div>
        <p class="empty-state__title">No fee records found</p>
        <p class="empty-state__sub">Add fee entries to see them listed here</p>
      </div>

      <!-- Table -->
      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th class="px-4 d-none d-md-table-cell">#</th>
              <th>Batch</th>
              <th class="d-none d-md-table-cell">Type</th>
              <th>Amount</th>
              <th class="d-none d-md-table-cell">Due date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(fee, index) in allFees" :key="fee.id || index">
              <td class="px-4 fw-bold text-secondary fs-xs d-none d-md-table-cell">{{ index + 1 }}</td>
              <td class="fw-semibold fs-sm">
                {{ fee.batches?.name || 'N/A' }}
                <span class="cell-sub d-md-none">{{ fee.type }}</span>
              </td>
              <td class="d-none d-md-table-cell">
                <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis fs-xs px-2">{{ fee.type }}</span>
              </td>
              <td class="fw-bold text-success">$ {{ fee.amount }}</td>
              <td class="fs-sm d-none d-md-table-cell">{{ fee.due ?? '-' }}</td>
              <td>
                <span
                  class="d-inline-flex align-items-center gap-2 fw-semibold text-capitalize fs-xs"
                  :class="fee.status === 'active' ? 'text-success' : 'text-warning-emphasis'"
                >
                  <span class="status-dot" :class="fee.status === 'active' ? 'status-dot--success' : 'status-dot--warning'"></span>
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
const finYear = import.meta.env.VITE_FINYEAR

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