<template>
  <div class="container mt-4">
    <div class="card mb-4">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-0 text-capitalize">{{ student.name }}</h4>
          <p class="text-muted mb-0">{{ student.rollNo }} | {{ student?.batches?.name }}</p>
        </div>
        <div class="text-end">
          <p class="mb-0 text-muted">Total Balance</p>
          <h3 :class="totalDue > 0 ? 'text-danger' : 'text-success'">
             $ {{ totalDue.toLocaleString() }}
          </h3>
        </div>
      </div>
    </div>

    <div class="card mb-4 border-primary">
      <div class="card-header bg-primary text-white">Record New Payment</div>
      <div class="card-body">
        <form @submit.prevent="submitPayment">
          <div class="row g-3">
            <div class="col-md-5">
              <label class="form-label">Fee Type</label>
              <select v-model="entry.type" class="form-select" required>
                <option value="" disabled>Select Fee Type</option>
                <option v-for="fee in feeData" :key="fee.id" :value="fee">
                  {{ fee.type }} (Due:  ${{ fee.amount - (fee.paid_amount || 0) }})
                </option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Amount to Pay</label>
              <div class="input-group">
                <span class="input-group-text"> $</span>
                <input v-model.number="entry.amount" type="number" class="form-control" placeholder="0.00" required />
              </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100" :disabled="entry.amount === '' || entry.amount===null">Submit Payment</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Fee Status</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Fee Type</th>
              <th>Due Date</th>
              <th>Total Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="fee in feeData" :key="fee.id">
              <td>{{ fee.type }}</td>
              <td>{{ fee.due ? fee.due : '-' }}</td>
              <td> $ {{ fee.amount.toLocaleString() }}</td>
              <td>
                <span :class="getStatusClass(fee)">
                    NIL
                  <!-- {{ fee.payment_status.toUpperCase() }} -->
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
import { ref, computed, onMounted } from 'vue'
import { useRoute,useRouter } from 'vue-router'
import api from '@/utils/axios'

import { showToast } from '@/utils/toastr'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()
const currentStudentId = computed(()=>{
    return route.params.id
})

const student = ref({})
const feeData = ref({})

const props = defineProps({
  studentId:   { type: String, default: 'STU-7742' },
  studentName: { type: String, default: 'Sneha Patel' },
  batchName:   { type: String, default: 'Morning Batch' },
})

// Data
const assignedFees = ref([
  { id: 1, type: 'Tuition', amount: 5000, payment_status: 'paid', due: '2025-06-01' },
  { id: 2, type: 'Lab Fee', amount: 1200, payment_status: 'pending', due: '2025-07-15' },
  { id: 3, type: 'Exam Fee', amount: 800, payment_status: 'pending', due: '2025-04-10' },
])

const entry = ref({
  type: '',
  amount: null
})

// Computed
const totalDue = computed(() => {
  return assignedFees.value
    .filter(f => f.payment_status !== 'paid')
    .reduce((sum, f) => sum + f.amount, 0)
})

const pendingFees = computed(() => {
  return assignedFees.value.filter(f => f.payment_status !== 'paid')
})

// Methods
const getStatusClass = (fee) => {
  if (fee.payment_status === 'paid') return 'badge bg-success'
  const isOverdue = new Date(fee.due) < new Date()
  return isOverdue ? 'badge bg-danger' : 'badge bg-warning text-dark'
}

const submitPayment = async() => {
  if (!entry.value.amount || !entry.value.type) return

    console.log(entry.value);

    if(parseFloat(entry.value.type.amount) < parseFloat(entry.value.amount) )
    {
        showToast({title:'You cannot pay more than the assigned',icon:'error'})
        return 
    }
    
    if(parseFloat(entry.value.type.amount) > parseFloat(entry.value.amount) )
    {
        const result = await Swal.fire({
            title: 'Confirmation',
            text: 'You\'re currently submitting a partial payment',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            confirmButtonText: 'Contiue',
            cancelButtonText: "No",
        })

        if (result.isConfirmed) {
            payFee(entry)
        }
    }
    else{
          const result = await Swal.fire({
            title: 'Confirmation',
            text: 'You\'re submitting a payment, Which cannot be undone !',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            confirmButtonText: 'Contiue',
            cancelButtonText: "No",
        })

        if (result.isConfirmed) {
            payFee(entry)
        }
    }
  
   
}


const getStudentDetails = async()=>{
    const response = await api.post('/users/getById',{id:currentStudentId.value})
    student.value = response.data
    if(student?.value?.batchId)
    {
        getAssignedFee()
    }
}

const getAssignedFee = async()=>{
      const res = await api.get(`/fees/bybatch/${student.value.batchId}`)
      feeData.value = res.data.data
}

const payFee = async(entry) =>{
    
    const req = {}
    req.feeId = entry.value.type.id
    req.userId = currentStudentId.value
    req.paid_amount = entry.value.amount
    
    const response = await api.post('fees/feePayment',req);
    if(response.data.status === 1)
    {
        showToast({title:response.data.message,icon:'success'})
        entry.value.type = ''
        entry.value.amount = null
    }
    else{
        showToast({title:response.data.message,icon:'error'})
    }
    
}

const getPaymentDetails = async() =>{
    const response = await api.get('/fees/paymentSummary',{id:currentStudentId.value})
    console.log(response);
    
}

onMounted(()=>{
    getStudentDetails();
    getPaymentDetails();
})
</script>