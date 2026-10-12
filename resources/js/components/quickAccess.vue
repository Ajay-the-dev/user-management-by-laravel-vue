<template>
  <div class="dashboard">

    <!-- TOP SUMMARY CARD -->
    <div class="card border-0 rounded-4 shadow-sm" v-if="!isLoading">
      <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2 rounded-top-4 px-3 px-md-4 py-3">
        <div class="d-flex align-items-center gap-3 me-auto">
          <div class="icon-tile icon-tile--primary">
            <i class="fa fa-file-invoice-dollar"></i>
          </div>
          <div>
            <h2 class="card-heading">Fee Collection</h2>
            <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis fs-xs mt-1">{{ finYear }}</span>
          </div>
        </div>
        <button class="btn btn-sm btn-outline-secondary" @click="refreshHelper">
          <i class="fa fa-refresh slow-rotation me-1" id="rotater"></i>
          Refresh
        </button>
        <button class="btn btn-sm btn-primary" @click="router.push('/home/fee/pay')">
          <i class="fa fa-hand-holding-dollar me-1"></i>
          Pay fee
        </button>
      </div>

      <!-- Stats Row -->
      <div class="stat-row">
        <div class="stat">
          <span class="stat__label">Total Collected</span>
          <span class="stat__value stat__value--success">{{ chosenCurrency }} {{ totalCollected }}</span>
        </div>
        <div class="vr"></div>
        <div class="stat">
          <span class="stat__label">Pending Dues</span>
          <span class="stat__value stat__value--danger">{{ chosenCurrency }} {{ totalPending }}</span>
        </div>
        <div class="vr"></div>
        <div class="stat">
          <span class="stat__label">Fee forms active</span>
          <span class="stat__value stat__value--primary">{{ allFees.length || 48 }}</span>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="px-4 pt-3 pb-1">
        <span class="eyebrow">Recent Transactions</span>
      </div>
      <div class="px-3 pb-3 d-flex flex-column gap-1">
        <div
          v-for="(tx, index) in transactions"
          :key="index"
          class="tx-row"
        >
          <div class="avatar">
            {{ tx.student.name.charAt(0) }}
          </div>
          <div class="tx-row__body">
            <span class="tx-row__name">{{ tx.student.name }}</span>
            <span class="tx-row__meta">{{ JSON.parse(tx.payment).paymentReference }} · {{ JSON.parse(tx.payment).selectedPaymentMode }}</span>
          </div>
          <div class="tx-row__end">
            <span class="tx-row__amount">+{{chosenCurrency}}{{ tx.paid_amount }}</span>
            <span
              class="badge rounded-pill fs-2xs"
              :class="parseFloat(tx.fee.amount) === parseFloat(tx.paid_amount)
                ? 'bg-success-subtle text-success-emphasis'
                : 'bg-warning-subtle text-warning-emphasis'"
            >
              {{ parseFloat(tx.fee.amount) === parseFloat(tx.paid_amount)  ? 'Fully paid' : 'Partially paid'}}
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="card border-0 rounded-4 shadow-sm" v-else>
        <div class="d-flex justify-content-center p-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
    </div>

    <!-- FEES OVERVIEW TABLE -->
    <feesOverview :allFees="allFees" heading="fees overview"/>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/utils/axios'
import { useRoute, useRouter } from 'vue-router'
import feesOverview from "@/components/fee/feeOverview.vue";

const routes = useRoute()
const router = useRouter()
const baseURL = import.meta.env.VITE_API_BASE_URL
const chosenCurrency = import.meta.env.VITE_CURRENCY
const finYear = import.meta.env.VITE_FINYEAR
const transactions = ref([])

const totalCollected = ref(0)
const totalAssigned = ref(0)
const isLoading = ref(true)

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
    const response = await api.get(`${baseURL}/fees/getActive`)
    allFees.value = response.data.data
    allFees.value = allFees?.value?.slice(0,20)
  } catch (err) {
    console.error(err)
  }
}

const getYearlyReportWithRecents = async () =>{
  try {
      const response = await api.get('/fees/yearly')
      let money = response.data.data
      totalAssigned.value = money.totalAssigned
      totalCollected.value = money.totalAmount
      transactions.value = money.recentRecords
      setTimeout(() => {
        isLoading.value =false
      }, 2000);

  } catch (error) {
        console.error(err)
  }
}
</script>
<style scoped lang="scss">
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