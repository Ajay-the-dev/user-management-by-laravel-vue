<template>
  <div class="card shadow-sm border-0 rounded-4 mt-5 mx-4 w-75">
    
    <!-- Header -->
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-semibold">Fees Overview</h5>
    </div>

    <!-- Body -->
    <div class="card-body">

      <!-- Empty State -->
      <div v-if="!allFees.length" class="text-center text-muted py-5">
        <p class="mb-1">No fees available</p>
        <small>Add new fee records to get started</small>
      </div>

      <!-- Table -->
      <div v-else class="table-responsive">
        <table class="table align-middle table-hover">
          
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Batch</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(fee, index) in allFees" :key="fee.id || index">
              
              <td class="fw-semibold">{{ index + 1 }}</td>

              <td>
                <span class="fw-medium">
                  {{ fee.batches?.name || 'N/A' }}
                </span>
              </td>

              <td>
                <span class="badge bg-info-subtle text-info px-2 py-1">
                  {{ fee.type }}
                </span>
              </td>

              <td class="fw-semibold text-success">
                ₹ {{ fee.amount }}
              </td>

              <td>
                <span 
                  class="badge px-2 py-1"
                  :class="{
                    'bg-success-subtle text-success': fee.status === 'active',
                    'bg-warning-subtle text-warning': fee.status === 'inactive',
                  }"
                >
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
import { ref,onMounted } from 'vue';
import api from '@/utils/axios'
// import { showToast } from '../utils/toastr'
import Swal from 'sweetalert2'


import { useRoute,useRouter } from 'vue-router';
const routes = useRoute();
const router = useRouter();

const baseURL = import.meta.env.VITE_API_BASE_URL;

const allFees = ref([])

onMounted(()=>{
    getAllFees()
})

const getAllFees = async()=>{
    const response = api.get(`${baseURL}/fees`).then((response)=>{
        allFees.value = response.data.data
    }).catch((err)=>{
        console.log();
        
    })
    
}
</script>
<style scoped>
</style>