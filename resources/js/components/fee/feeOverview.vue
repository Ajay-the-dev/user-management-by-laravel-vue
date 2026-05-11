<template>
  <div class="card border rounded-3 my-4">
    <slider :showSidebar="sliderToggle" @close="closeSlider">
      <template #heading>
        <h5 class="offcanvas-title fw-semibold">
          {{ Object.keys(selectedFee).length > 0 ? 'Edit Fee' : 'Add Fee' }}
        </h5>
      </template>

      <template #content>
        <div class="p-4">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-medium">Batch <span class="text-danger">*</span></label>
              <select class="form-select" v-model="selectedBatchId">
                <option value="">Choose</option>
                <option
                  :value="batch.id"
                  :key="batch.id"
                  v-for="batch in batches"
                  :selected="selectedFee?.batches?.id === batch.id"
                >{{ batch.name }}</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">Type <span class="text-danger">*</span></label>
              <select class="form-select" v-model="selectedType">
                <option value="">Choose</option>
                <option
                  :value="feeType.value"
                  v-for="feeType in feeTypes"
                  :selected="feeType.value === selectedFee.type"
                >{{ feeType.name }}</option>
              </select>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-medium">Amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">{{ chosenCurrency }}</span>
                <input type="number" class="form-control" min="0" v-model="amount" />
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
              <select class="form-select" v-model="status">
                <option
                  :value="fee.value"
                  v-for="fee in feeStatus"
                  :selected="fee.value === selectedFee.status"
                >{{ fee.name }}</option>
              </select>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-medium">Due Date</label>
              <flat-pickr v-model="dueDate" :config="config" class="form-control" />
              <button
                type="button"
                class="btn btn-link btn-sm text-danger ps-0 mt-1"
                @click="dueDate = ''"
              >Remove date</button>
            </div>
          </div>

          <div class="d-flex gap-2 justify-content-end border-top pt-3">
            <button type="button" class="btn btn-outline-secondary" @click.prevent="closeSlider">
              Cancel
            </button>
            <button type="button" class="btn btn-primary" @click.prevent="insertUpdate">
              Save changes
            </button>
          </div>
        </div>
      </template>
    </slider>

    <!-- Card Header -->
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4">
      <h6 class="fw-semibold mb-0 text-capitalize">{{ heading }}</h6>
      <div class="align-items-center d-flex py-3 row" v-if="interactive">
        <div class="col-2">
            <label for="">Status</label>
        </div>
        <div class="col-4">
          <select class="form-select" aria-label="Default select example" v-model="filterStatus">
            <option value="">All</option>
            <option :value="fstatus.value" v-for="fstatus in feeStatus">{{ fstatus.name }}</option>
          </select>
        </div>
        <div class="col-2">
          <label for="">Type</label>
        </div>
        <div class="col-4">
          <select class="form-select" aria-label="Default select example" v-model="filterType">
            <option value="">All</option>
            <option :value="ftype.value" v-for="ftype in feeTypes">{{ ftype.name }}</option>
          </select>
        </div>
      </div>
      <button v-if="interactive" class="btn btn-primary btn-sm d-flex align-items-center gap-1" @click.prevent="assignFee">
        <i class="fa fa-plus"></i> Assign fee
      </button>
    </div>

    <!-- Empty State -->
    <div v-if="!allFees.length" class="text-center py-5 px-4">
      <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3" style="width:52px;height:52px;">
        <i class="fa fa-file-invoice text-secondary fs-5"></i>
      </div>
      <p class="fw-semibold mb-1 text-dark" style="font-size:14px;">No fee records found</p>
      <p class="text-muted mb-0 small">Add fee entries to see them listed here</p>
    </div>

    <!-- Table -->
    <div v-else class="table-responsive mb-5">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="text-muted small fw-semibold text-uppercase px-4 py-3" style="letter-spacing:.4px;">#</th>
            <th class="text-muted small fw-semibold text-uppercase px-4 py-3" style="letter-spacing:.4px;">Batch</th>
            <th class="text-muted small fw-semibold text-uppercase px-4 py-3" style="letter-spacing:.4px;">Type</th>
            <th class="text-muted small fw-semibold text-uppercase px-4 py-3" style="letter-spacing:.4px;">Amount</th>
            <th class="text-muted small fw-semibold text-uppercase px-4 py-3" style="letter-spacing:.4px;">Due date</th>
            <th class="text-muted small fw-semibold text-uppercase px-4 py-3" style="letter-spacing:.4px;">Status</th>
            <th v-if="interactive" class="text-muted small fw-semibold text-uppercase px-4 py-3" style="letter-spacing:.4px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(fee, index) in filteredFees" :key="fee.id || index">
            <td class="px-4 py-3 text-muted small fw-semibold">{{ index + 1 }}</td>
            <td class="px-4 py-3 fw-semibold">{{ fee.batches?.name || 'N/A' }}</td>
            <td class="px-4 py-3">
              <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                {{ fee.type }}
              </span>
            </td>
            <td class="px-4 py-3 fw-bold text-success">{{ chosenCurrency }} {{ fee.amount }}</td>
            <td class="px-4 py-3 text-secondary small">{{ fee.due ?? '—' }}</td>
            <td class="px-4 py-3">
              <span
                class="badge rounded-pill px-3 py-1 fw-medium"
                :class="fee.status === 'ACTIVE' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning'"
              >
                <span
                  class="rounded-circle d-inline-block me-1"
                  style="width:6px;height:6px;vertical-align:middle;"
                  :class="fee.status === 'ACTIVE' ? 'bg-success' : 'bg-warning'"
                ></span>
                {{ fee.status }}
              </span>
            </td>
            <td class="px-4 py-3" v-if="interactive">
              <button class="btn btn-outline-secondary btn-sm" @click.prevent="editFee(fee)">
                Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import slider from '@/components/utils/slider.vue'
import api from '@/utils/axios'
import Swal from 'sweetalert2'
import { showToast } from '@/utils/toastr'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

const filterType = ref('')
const filterStatus = ref('')
const emit = defineEmits(['close'])

const config = { dateFormat: 'Y-m-d' }

const selectedBatchId = ref('')
const selectedType = ref('')
const amount = ref(0)
const dueDate = ref('')
const status = ref('ACTIVE')

const chosenCurrency = import.meta.env.VITE_CURRENCY

const feeTypes = [
  { name: "Tuition",   value: "TUTION"    },
  { name: "Hostel",    value: "HOSTEL"    },
  { name: "Library",   value: "LIBRARY"   },
  { name: "Transport", value: "TRANSPORT" },
  { name: "Exam",      value: "EXAM"      },
  { name: "Other",     value: "OTHER"     },
]

const feeStatus = [
  { name: "Active",     value: "ACTIVE"   },
  { name: "Not Active", value: "INACTIVE" },
  { name: "Archive",     value: "ARCHIVE"  },
]

const selectedFee = ref({})
const sliderToggle = ref(false)
const batches = ref([])

const props = defineProps({
  allFees:     { type: Array,   default: () => [] },
  heading:     { type: String,  default: ''       },
  interactive: { type: Boolean, default: false    },
})

const hasFilled = computed(() =>
  selectedBatchId.value !== '' ||
  selectedType.value !== ''    ||
  amount.value !== 0           ||
  dueDate.value !== ''
)

const filteredFees = computed(()=>{
  if(filterType.value === '' && filterStatus.value === '')
  {
    return props.allFees
  }

  let all = props.allFees
  var res = []
  res = all.filter((entry) => entry.type === filterType.value)
})

const editFee = (fee) => {
  selectedFee.value     = fee
  selectedBatchId.value = fee.batchId
  amount.value          = fee.amount
  selectedType.value    = fee.type
  dueDate.value         = fee.due
  status.value          = fee.status
  setTimeout(() => { sliderToggle.value = true }, 200)
}

const assignFee = () => {
  setTimeout(() => { sliderToggle.value = true }, 200)
}

const getAllBatches = () => {
  api.get('/batches/all')
    .then(res  => { batches.value = res.data.data })
    .catch(err => { showToast({ title: err, icon: 'error' }) })
}

const closeSlider = async (success = false) => {
  
  if (hasFilled.value) {
    const result = await Swal.fire({
      title: 'Confirm',
      text: 'Changes will not be saved',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Proceed',
      cancelButtonText: 'Cancel',
    })
    if (result.isConfirmed) {
      selectedBatchId.value = ''
      selectedType.value    = ''
      amount.value          = 0
      dueDate.value         = ''
      status.value          = 'ACTIVE'
      sliderToggle.value    = false
    }
  } else {
    selectedBatchId.value = ''
    selectedType.value    = ''
    amount.value          = 0
    dueDate.value         = ''
    status.value          = 'ACTIVE'
    sliderToggle.value    = false
  }

  selectedFee.value.due = null
  selectedFee.value = {}
  emit('close')
}

onMounted(() => { getAllBatches() })

const insertUpdate = () => {
  if (!selectedBatchId.value) { showToast({ title: 'Select a batch',    icon: 'error' }); return }
  if (!selectedType.value)    { showToast({ title: 'Select a fee type', icon: 'error' }); return }
  if (!amount.value)          { showToast({ title: 'Amount cannot be 0',icon: 'error' }); return }

  Object.keys(selectedFee.value).length > 0 ? updateFee() : submitFee()
}

const buildPayload = () => ({
  batchId: selectedBatchId.value,
  type:    selectedType.value,
  amount:  amount.value,
  due:     dueDate.value,
  status:  status.value,
})

const submitFee = async () => {
  await api.post('/fees/assign', buildPayload())
    .then(res => {
      if (res.data.status === 1) {
        showToast({ title: res.data.message, icon: 'success' })
        cleanUp()
      }
    })
    .catch(e => { showToast({ title: e?.response?.data?.message ?? 'Error', icon: 'error' }) })
}

const updateFee = async () => {
  await api.put(`/fees/${selectedFee.value.id}`, buildPayload())
    .then(res => {
      if (res.data.status === 1) {
        showToast({ title: res.data.message, icon: 'success' })
        cleanUp()
      }
    })
    .catch(e => { showToast({ title: e?.response?.data?.message ?? 'Error', icon: 'error' }) })
}


const cleanUp = () => {
    selectedBatchId.value = ''
    selectedType.value    = ''
    amount.value          = 0
    dueDate.value         = ''
    status.value          = 'ACTIVE'
    sliderToggle.value    = false
    emit('close')
}
</script>