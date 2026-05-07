<template>
    <div class="card border-0 rounded-4 shadow-sm p-2 my-5">
        <slider :showSidebar="sliderToggle" @close="closeSlider">
          <template #heading>
            <h5 class="offcanvas-title">Add Fee</h5>
          </template>
          <template #content>
              <div class="container">
                <div class="shadow-sm">
                  <div class="card-body vh-100">
                    <form>

                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label for="batch-id" class="form-label">Batch <span class="text-danger">*</span></label>
                          <select class="form-control" v-model="selectedBatchId">
                            <option value="">Choose</option>
                            <option :value="batch.id" :key="batch.id" v-for="batch in batches" :selected="selectedFee?.batches?.id === batch.id">{{ batch.name }}</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label for="fee-type" class="form-label">Type <span class="text-danger">*</span></label>
                          <select class="form-select" id="fee-type" v-model="selectedType">
                            <option value="">Choose</option>
                            <option :value="feeType.value" v-for="feeType in feeTypes" :selected="feeType.value === selectedFee.type">{{ feeType.name }}</option>
                          </select>
                        </div>
                      </div>

                      <!-- Amount -->
                      <div class="row">
                        <div class="col-6">
                          <div class="mb-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                              <span class="input-group-text">{{ chosenCurrency }}</span>
                              <input type="number" class="form-control" id="amount" min="0" v-model="amount">
                            </div>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" v-model="status">
                              <option :value="fee.value" v-for="fee in feeStatus" :selected="fee.value===selectedFee.status">{{ fee.name }}</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <!-- Status -->

                      <!-- Due Date -->
                      <div class="mb-3">
                        <label for="due" class="form-label">Due Date</label>
                         <flat-pickr  v-model="dueDate" :config="config" class="form-control"/>
                        <div class="form-text">Leave blank if no due date.</div>
                      </div>
                      <!-- Actions -->
                      <div class="d-flex gap-2 justify-content-end bottom">
                        <button type="button" class="btn btn-secondary" @click.prevent="closeSlider">Cancel</button>
                        <button type="submit" class="btn btn-primary" @click.prevent="submitFee">Save</button>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
          </template>
        </slider>
      <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between rounded-top-4 px-4 py-3">
        <h2 class="fw-bold mb-0 text-capitalize" style="font-size:15px;color:#111827;">{{ heading }}</h2>
        <div>
          <button class="btn btn-sm btn-primary" @click.prevent="assignFee">
           <i class="fa fa-plus"></i> Assign fee
          </button>
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
              <th class="text-uppercase fw-bold px-4 py-3" style="font-size:11px;color:#9ca3af;letter-spacing:.5px;" v-if="interactive">Action</th>
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
                <span class="d-inline-flex align-items-center gap-2 fw-semibold text-capitalize" style="font-size:12px;" :style="fee.status === 'ACTIVE' ? 'color:#059669;' : 'color:#d97706;'">
                  <span class="rounded-circle d-inline-block" style="width:7px;height:7px;flex-shrink:0;" :style="fee.status === 'ACTIVE' ? 'background:#10b981;box-shadow:0 0 0 2px #d1fae5;' : 'background:#f59e0b;box-shadow:0 0 0 2px #fef3c7;'"></span>
                  {{ fee.status }}
                </span>
              </td>
               <td class="px-4 py-3" v-if="interactive">
                <button class="btn btn-outline-secondary" @click.prevent="editFee(fee)">Edit</button>
               </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</template>
<script setup>
import { ref,onMounted,computed } from "vue";
import slider from '@/components/utils/slider.vue'
import api from '@/utils/axios'
import Swal from 'sweetalert2'


import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
const config = {
  dateFormat: 'Y-m-d'
}

const selectedBatchId = ref('')
const selectedType = ref('')
const amount = ref(0)
const dueDate = ref('')
const status = ref('ACTIVE')


const chosenCurrency = import.meta.env.VITE_CURRENCY
const feeTypes = [
  {name:"Tuition",value:"TUTION"},
  {name:"Hostel",value:"HOSTEL"},
  {name:"Library",value:"LIBRARY"},
  {name:"Transport",value:"TRANSPORT"},
  {name:"Exam",value:"EXAM"},
  {name:"Other",value:"OTHER"},
]

const feeStatus = [
  {name:"Active",value:"ACTIVE"},
  {name:"Not Active",value:"INACTIVE"},
  {name:"Delete",value:"ARCHIVE"}
]


const selectedFee = ref({})
const sliderToggle = ref(false)
const batches = ref([])


const props = defineProps({
    allFees:{
        type : Array,
        default:[]
    },
    heading:{
        type:String,
        default:''
    },
    interactive:{
        type:Boolean,
        default:false
    }
})


const hasFilled = computed(()=>{
    if(selectedBatchId.value !== '' || selectedType.value !== '' || amount.value !== 0 || dueDate.value !== '')
    {
      return true
    }
    else{
      return false
    }
})

const editFee = (fee) =>{
  selectedFee.value = fee
  setTimeout(() => {
    sliderToggle.value = true
  }, 200);
}

const assignFee = () =>{
  setTimeout(() => {
    sliderToggle.value = true
  }, 200);
}

const getAllBatches = () =>{
  const url =  `/batches/all` 
  const response = api.get(url).then((response)=>{
      batches.value = response.data.data
  }).catch((error)=>{
      showToast({title:error,icon:'error'})
  })
}

const closeSlider = async() =>{
  if(hasFilled.value)
  {
    const result = await Swal.fire({
        title: 'Confirm',
        text: 'Changes will not be saved',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Proceed',
        cancelButtonText: 'Cancel',
    })

    if(result.isConfirmed)
    {
      selectedBatchId.value = ''
      selectedType.value = ''
      amount.value = 0
      dueDate.value = ''
      status.value = 'ACTIVE'
      sliderToggle.value = false
    }
  }
  else{
    sliderToggle.value = false
  }

  selectedFee.value.due = null
  selectedFee.value = {}
}

onMounted(()=>{
  getAllBatches()
})



const submitFee = async() =>{
  let req= {}
  req.batchId = selectedBatchId.value
  req.type = selectedType.value
  req.amount = amount.value
  req.due = dueDate.value
  req.status = status.value
  console.log(req);
  
}
</script>
<style scoped>
.bottom{
  position: absolute;
  bottom: 5rem;
}
</style>