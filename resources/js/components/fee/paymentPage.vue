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
                  {{ fee.type }} 
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
              <th>Total Amount</th>
              <th>Paid</th>
              <th>Balance</th>
              <th>Due Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody v-if="feeData.length > 0">
            <tr v-for="fee in feeData" :key="fee.id">
              <td>{{ fee.type }}</td>
              <td>{{ fee.amount.toLocaleString() }}</td>
              <td>{{ paidAmounts[fee.id]?.paid.toLocaleString() }}</td>
              <td>{{ paidAmounts[fee.id]?.balance.toLocaleString() }}</td>
              <td>{{ fee.due ? fee.due : '-' }}</td>
              <td>
                <span v-if="parseFloat(paidAmounts[fee.id]?.balance) === 0" class="badge bg-success">
                  PAID
                </span>

                <span 
                  v-else-if="parseFloat(paidAmounts[fee.id]?.balance) > 0 && !fee.due" 
                  class="badge bg-secondary"
                >
                  PENDING
                </span>

                <span 
                  v-else-if="parseFloat(paidAmounts[fee.id]?.balance) > 0 && new Date(fee.due) >= new Date()" 
                  class="badge bg-warning text-dark"
                >
                  PENDING
                </span>

                <span 
                  v-else 
                  class="badge bg-danger"
                >
                  OVERDUE
                </span>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td class="p-4 fs-6 text-center fw-light" colspan="6">
                  No dues assigned for you / batch !!
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted,watch } from 'vue'
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
const feeData = ref([])
const feeSummary = ref([])

const paidAmounts = ref([]);

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
  let sum = 0
  paidAmounts.value.forEach(fee => {
    sum = sum + parseFloat(fee.balance)
  });
  return sum
})

const pendingFees = computed(() => {
  return assignedFees.value.filter(f => f.payment_status !== 'paid')
})


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
        setTimeout(() => {
          router.go(0)
        }, 2000);
    }
    else{
        showToast({title:response.data.message,icon:'error'})
    }
    
}

// const getPaymentDetails = async() =>{
//     const response = await api.get('/fees/paymentSummary/'+currentStudentId.value)
//     if(response.data.status === 1)
//     {
//       feeSummary.value = response.data.data   
//     }
//     else{
//       showToast({title:response.data.message,icon:'error'})
//     }
// }

const getPaidFeeDetails = async(feeId)=>{
    const req= {}
    req.userId = currentStudentId.value
    req.feeId = feeId
    const response = await api.post('/fees/paidFee/',req)
    console.log(response.data.data);
    
    return response.data.data
}

const loadPaidDetails = async (fees) => {
    for (const fee of fees) {
        const amount = await getPaidFeeDetails(fee.id);
        let obj = {}
        obj.paid = amount
        obj.balance = parseFloat(fee.amount) - parseFloat(obj.paid)
        paidAmounts.value[fee.id] = obj;
    }
};

onMounted(()=>{
    getStudentDetails();
    // getPaymentDetails();
})

watch(feeData,(feeData)=>{
  loadPaidDetails(feeData)
})
</script>