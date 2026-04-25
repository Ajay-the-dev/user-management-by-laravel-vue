<template>
  <div class="fees-dashboard">

    <!-- ─── TOP SUMMARY CARD ─── -->
    <div class="summary-card">
      <div class="summary-card__header">
        <div class="header-left">
          <div class="icon-pill">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
          </div>
          <div>
            <h2 class="card-title">Fee Collection</h2>
            <span class="fy-badge">FY 2025–26</span>
          </div>
        </div>
        <button class="fw-bold btn btn-primary" @click="router.push('/home/fee/pay')">
         <i class="fa fa-hand-holding-dollar"></i>
          Pay fee
        </button>
      </div>

      <!-- Stats Row -->
      <div class="stats-row">
        <div class="stat-block stat-block--green">
          <span class="stat-label">Total Collected</span>
          <span class="stat-value"> $ 45,250</span>
          <span class="stat-trend">↑ 12% this month</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-block stat-block--red">
          <span class="stat-label">Pending Dues</span>
          <span class="stat-value"> $ 12,800</span>
          <span class="stat-trend">↓ 3 students</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-block stat-block--blue">
          <span class="stat-label">Total Students</span>
          <span class="stat-value">{{ allFees.length || 48 }}</span>
          <span class="stat-trend">Enrolled this FY</span>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="section-label">Recent Transactions</div>
      <div class="tx-list">
        <div
          v-for="(tx, index) in transactions"
          :key="index"
          class="tx-row"
        >
          <div class="tx-avatar">
            {{ tx.studentName.charAt(0) }}
          </div>
          <div class="tx-info">
            <span class="tx-name">{{ tx.studentName }}</span>
            <span class="tx-meta">{{ tx.id }} · {{ tx.method }}</span>
          </div>
          <div class="tx-right">
            <span class="tx-amount">+  ${{ tx.amount }}</span>
            <span class="tx-badge" :class="tx.status === 'Completed' ? 'tx-badge--green' : 'tx-badge--amber'">
              {{ tx.status }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- ─── FEES OVERVIEW TABLE ─── -->
    <div class="overview-card">
      <div class="overview-card__header">
        <h2 class="card-title">Fees Overview</h2>
        <div class="header-chips">
          <span class="chip chip--active">All</span>
          <span class="chip">Active</span>
          <span class="chip">Inactive</span>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!allFees.length" class="empty-state">
        <div class="empty-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <p class="empty-title">No fee records found</p>
        <p class="empty-sub">Add fee entries to see them listed here</p>
      </div>

      <!-- Table -->
      <div v-else class="table-wrap">
        <table class="fees-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Batch</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Due date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(fee, index) in allFees" :key="fee.id || index" class="fees-table__row">
              <td class="row-num">{{ index + 1 }}</td>
              <td class="row-batch">{{ fee.batches?.name || 'N/A' }}</td>
              <td>
                <span class="type-badge">{{ fee.type }}</span>
              </td>
              <td class="row-amount"> $ {{ fee.amount }}</td>
              <td>{{fee.due ?? '-'}}</td>
              <td>
                <span
                  class="status-dot"
                  :class="fee.status === 'active' ? 'status-dot--green' : 'status-dot--amber'"
                >
                  <span class="dot-indicator"></span>
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
// import Swal from 'sweetalert2'
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
  // Uncomment below and remove mock data when API is ready
  try {
    const response = await api.get(`${baseURL}/fees`)
    console.log(response);
    
    allFees.value = response.data.data
  } catch (err) {
    console.error(err)
  }

  // Mock data for preview
  // allFees.value = [
  //   { id: 1, batches: { name: 'Batch A – Morning' }, type: 'Tuition',      amount: '5,000', status: 'active'   },
  //   { id: 2, batches: { name: 'Batch B – Evening' }, type: 'Lab Fee',      amount: '1,200', status: 'active'   },
  //   { id: 3, batches: { name: 'Batch C – Weekend' }, type: 'Exam Fee',     amount: '800',   status: 'inactive' },
  //   { id: 4, batches: { name: 'Batch A – Morning' }, type: 'Library Fee',  amount: '500',   status: 'active'   },
  //   { id: 5, batches: { name: 'Batch D – Online'  }, type: 'Tuition',      amount: '4,500', status: 'active'   },
  // ]
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

/* ─── ROOT ─── */
.fees-dashboard {
  font-family: 'Plus Jakarta Sans', sans-serif;
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 24px;
  background: #f4f6fb;
  min-height: 100vh;
}

/* ─── SHARED CARD BASE ─── */
.summary-card,
.overview-card {
  background: #ffffff;
  border-radius: 20px;
  border: 1px solid #eaecf0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
  overflow: hidden;
}

.card-title {
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  margin: 0;
  letter-spacing: -0.2px;
}

/* ─── SUMMARY CARD HEADER ─── */
.summary-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f0f2f5;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.icon-pill {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, #e8f5ee, #d1fae5);
  color: #059669;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.fy-badge {
  display: inline-block;
  font-size: 11px;
  font-weight: 600;
  color: #6b7280;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 20px;
  padding: 2px 10px;
  margin-top: 3px;
  letter-spacing: 0.3px;
}

.report-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 8px 14px;
  cursor: pointer;
  transition: all 0.15s ease;
}
.report-btn:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
}

/* ─── STATS ROW ─── */
.stats-row {
  display: flex;
  align-items: stretch;
  padding: 20px 24px;
  gap: 0;
  border-bottom: 1px solid #f0f2f5;
}

.stat-divider {
  width: 1px;
  background: #e5e7eb;
  margin: 4px 20px;
}

.stat-block {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.stat-label {
  font-size: 11px;
  font-weight: 500;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value {
  font-size: 22px;
  font-weight: 800;
  letter-spacing: -0.5px;
  margin: 2px 0;
}

.stat-block--green .stat-value { color: #059669; }
.stat-block--red   .stat-value { color: #dc2626; }
.stat-block--blue  .stat-value { color: #4f46e5; }

.stat-trend {
  font-size: 11px;
  font-weight: 500;
  color: #9ca3af;
}

/* ─── TRANSACTIONS ─── */
.section-label {
  padding: 16px 24px 10px;
  font-size: 12px;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

.tx-list {
  padding: 0 16px 16px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.tx-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 12px;
  transition: background 0.15s ease;
  cursor: default;
}
.tx-row:hover {
  background: #f8fafc;
}

.tx-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ede9fe, #ddd6fe);
  color: #6d28d9;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  flex-shrink: 0;
}

.tx-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.tx-name {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
}

.tx-meta {
  font-size: 11px;
  color: #9ca3af;
  font-weight: 500;
}

.tx-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 3px;
}

.tx-amount {
  font-size: 13px;
  font-weight: 700;
  color: #111827;
}

.tx-badge {
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 20px;
  letter-spacing: 0.2px;
}

.tx-badge--green {
  background: #d1fae5;
  color: #059669;
}

.tx-badge--amber {
  background: #fef3c7;
  color: #d97706;
}

/* ─── OVERVIEW CARD ─── */
.overview-card__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #f0f2f5;
}

.header-chips {
  display: flex;
  gap: 6px;
}

.chip {
  font-size: 11px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 20px;
  border: 1px solid #e5e7eb;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.15s;
}

.chip--active {
  background: #111827;
  border-color: #111827;
  color: #ffffff;
}

/* ─── EMPTY STATE ─── */
.empty-state {
  text-align: center;
  padding: 48px 24px;
  color: #9ca3af;
}

.empty-icon {
  width: 56px;
  height: 56px;
  background: #f3f4f6;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  color: #d1d5db;
}

.empty-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin: 0 0 4px;
}

.empty-sub {
  font-size: 12px;
  margin: 0;
}

/* ─── TABLE ─── */
.table-wrap {
  padding: 8px 0 8px;
  overflow-x: auto;
}

.fees-table {
  width: 100%;
  border-collapse: collapse;
}

.fees-table thead tr {
  border-bottom: 1px solid #f0f2f5;
}

.fees-table th {
  padding: 10px 20px;
  font-size: 11px;
  font-weight: 700;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  text-align: left;
}

.fees-table__row {
  transition: background 0.15s ease;
}

.fees-table__row:hover {
  background: #f9fafb;
}

.fees-table td {
  padding: 13px 20px;
  font-size: 13px;
  color: #374151;
  border-bottom: 1px solid #f8f9fa;
}

.fees-table__row:last-child td {
  border-bottom: none;
}

.row-num {
  font-weight: 700;
  color: #9ca3af;
  font-size: 12px;
}

.row-batch {
  font-weight: 600;
  color: #111827;
}

.type-badge {
  display: inline-block;
  background: #eff6ff;
  color: #3b82f6;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 20px;
  letter-spacing: 0.2px;
}

.row-amount {
  font-weight: 700;
  color: #059669;
  font-size: 14px;
}

.status-dot {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.dot-indicator {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-dot--green {
  color: #059669;
}
.status-dot--green .dot-indicator {
  background: #10b981;
  box-shadow: 0 0 0 2px #d1fae5;
}

.status-dot--amber {
  color: #d97706;
}
.status-dot--amber .dot-indicator {
  background: #f59e0b;
  box-shadow: 0 0 0 2px #fef3c7;
}
</style>