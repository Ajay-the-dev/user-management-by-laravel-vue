<template>
     <div>
        <div class="pay-fee-wrapper">
            <div class="page-header">
                <div>
                <h4 class="page-title">Fee Management</h4>
                <p class="page-subtitle">Manage your full fee related things here</p>
                </div>
                <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle fw-semibold px-3 py-2">{{ finYear }}</span>
            </div>
            <Overview :allFees="allFees" heading="Fee Headers" :interactive="1===1" @close="refresh"/>
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
    allFees.value = []
    const response = await api.get(`/fees`)
    allFees.value = response.data.data
  } catch (err) {
    console.error(err)
  }
}

const refresh = () => {
  getAllFees()
}
</script>
<style scoped lang="scss">
.pay-fee-wrapper {
  padding: 0.25rem;
}
</style>