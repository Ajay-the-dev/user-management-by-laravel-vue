<template>
     <div class=" content-scroll">
        <div class="pay-fee-wrapper">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                <h4 class="fw-bold mb-1 text-dark">Fee Management</h4>
                <p class="text-muted small mb-0">Manage your full fee related things here</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-3 py-2">{{ finYear }}</span>
            </div>
            <Overview :allFees="allFees" heading="Fee Headers" :interactive="1===1"/>
        </div>
     </div>
</template>
<script setup>

import { onMounted,watch,ref } from "vue";
import Overview from "@/components/fee/feeOverview.vue";
import api from '@/utils/axios'
const finYear = import.meta.env.VITE_FINYEAR


const allFees = ref([])
onMounted(()=>{
    getAllFees()
})

const getAllFees = async () => {
  try {
    const response = await api.get(`/fees`)
    allFees.value = response.data.data
  } catch (err) {
    console.error(err)
  }
}
</script>
<style scoped>
.pay-fee-wrapper {
  padding: 28px;
  background: #f4f6fb;
  height: 100vh;
}
</style>