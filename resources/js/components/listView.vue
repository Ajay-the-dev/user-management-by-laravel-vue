<template>
<div class="row my-5 mx-2">
    <div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true" ref="myModalRef">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      <div class="modal-header bg-dark text-white border-0">
        <h5 class="modal-title fw-semibold p-1">
          <span v-if="modeSelected === 'STAFF'">Staff Details</span>
          <span v-else-if="modeSelected === 'ADMIN'">Staff Details</span>
          <span v-else>Student Details</span>
        </h5>
        <button type="button" class="btn-close btn-close-white px-4" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body bg-light" style="max-height:70vh; overflow-y:auto;">
        <div class="row">
          <div class="col-12" v-if="selectedUser!==null">

            <div class="d-flex justify-content-around mb-4 text-center">
              <img v-if="profile_picture" :src="profile_picture" class="rounded-circle shadow-sm border" width="90" height="90">
              <img v-else src="http://localhost:8000/storage/images/profile.svg" class="rounded-circle shadow-sm border" width="90" height="90">
            </div>

            <div class="card p-2 border-0 shadow-sm rounded-4 mb-3">
              <div class="card-body">
                <h6 class="text-uppercase text-primary fw-bold small mb-3">Personal Information</h6>
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-2"><span class="text-muted small">Name</span><br><span class="fw-semibold">{{ name }}</span></p>
                    <p class="mb-2"><span class="text-muted small">Username</span><br><span class="fw-semibold">{{ username }}</span></p>
                    <p class="mb-0"><span class="text-muted small">Email</span><br><span class="fw-semibold">{{ email }}</span></p>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-2"><span class="text-muted small">Mobile</span><br><span class="fw-semibold">{{ mobile }}</span></p>
                    <p class="mb-2"><span class="text-muted small">Date of Birth</span><br><span class="fw-semibold">{{ dob ? new Date(dob).toLocaleDateString('en-GB').replace(/\//g, '-') : "-" }}</span></p>
                    <p class="mb-0"><span class="text-muted small">Gender</span><br><span class="fw-semibold text-capitalize">{{ gender }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="isStaff" class="card p-2 border-0 shadow-sm rounded-4 mb-3">
              <div class="card-body">
                <h6 class="text-uppercase text-primary fw-bold small mb-3">Profile & Role</h6>
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-0"><span class="text-muted small">Role</span><br><span class="fw-semibold">{{ role }}</span></p>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-0"><span class="text-muted small">Designation</span><br><span class="fw-semibold">{{ designation || 'N/A' }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="!isStaff" class="card p-2 border-0 shadow-sm rounded-4 mb-3">
              <div class="card-body">
                <h6 class="text-uppercase text-primary fw-bold small mb-3">Academic Information</h6>
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-2"><span class="text-muted small">Course</span><br><span class="fw-semibold">{{ course || 'N/A' }}</span></p>
                    <p class="mb-0"><span class="text-muted small">Roll Number</span><br><span class="fw-semibold">{{ rollNo || 'N/A' }}</span></p>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-2"><span class="text-muted small">University</span><br><span class="fw-semibold">{{ university }}</span></p>
                    <p class="mb-0"><span class="text-muted small">Batch</span><br><span class="fw-semibold">{{ batchName || '-' }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="!isStaff" class="card p-2 border-0 shadow-sm rounded-4 mb-3">
              <div class="card-body">
                <h6 class="text-uppercase text-primary fw-bold small mb-3">Location & Visa</h6>
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-2"><span class="text-muted small">Location</span><br><span class="fw-semibold">{{ location }}</span></p>
                    <p class="mb-0"><span class="text-muted small">Visa Type</span><br><span class="fw-semibold">{{ visaType }}</span></p>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-2"><span class="text-muted small">Visa Status</span><br><span class="fw-semibold">{{ visaStatus }}</span></p>
                    <p class="mb-0"><span class="text-muted small">Expiry</span><br><span class="fw-semibold">{{ visaExpiryDate || 'N/A' }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="!isStaff" class="card p-2 border-0 shadow-sm rounded-4 mb-3">
              <div class="card-body">
                <h6 class="text-uppercase text-primary fw-bold small mb-3">Insurance</h6>
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-0"><span class="text-muted small">Status</span><br><span class="fw-semibold">{{ insuranceStatus }}</span></p>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-0"><span class="text-muted small">Expiry</span><br><span class="fw-semibold">{{ insuranceExpiryDate || 'N/A' }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="!isStaff" class="card p-2 border-0 shadow-sm rounded-4 mb-3">
              <div class="card-body">
                <h6 class="text-uppercase text-primary fw-bold small mb-3">Address</h6>
                <div class="row">
                  <div class="col-md-4">
                    <p class="mb-0"><span class="text-muted small">Street</span><br><span class="fw-semibold">{{ street || '-' }}</span></p>
                  </div>
                  <div class="col-md-4">
                    <p class="mb-0"><span class="text-muted small">City</span><br><span class="fw-semibold">{{ city || '-' }}</span></p>
                  </div>
                  <div class="col-md-4">
                    <p class="mb-0"><span class="text-muted small">Country</span><br><span class="fw-semibold">{{ country || '-' }}</span></p>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="!isStaff" class="card p-2 border-0 shadow-sm rounded-4">
              <div class="card-body" v-if="fileUploads.length === 0">
                <h6 class="text-uppercase text-primary fw-bold small mb-3">Uploads</h6>
                <div class="text-center text-muted small">No uploads available</div>
              </div>
              <div class="card-body" v-else>
                    <h6 class="text-uppercase text-primary fw-bold small mb-3">Uploads</h6>
                    <button class="btn btn-sm btn-outline-dark m-2" v-for="file in fileUploads" @click="openInNewTab(file.url)">
                        <i class="fa fa-arrow-circle-down mx-2"></i> {{ file.title }}
                    </button>
                </div>    
            </div>

          </div>
        </div>
      </div>

      <div class="modal-footer bg-white border-0">
        <button type="button" class="btn btn-dark rounded-3 px-4" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

    <div class="col-12 w-75">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient p-4">
                    <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">{{isStaff ? 'Staffs' : 'Students'}}</h5>
                    <div class="d-flex">
                        <input type="text" class="form-control w-auto mx-3" placeholder="Search " v-model="searchQuery"
                        @change="handleSearch">
                         <button class="btn btn-primary btn-sm" v-if="isStaff" @click="navigateTo('/home/staff/add')">
                            <i class="fas fa-plus me-2"></i>Add New
                        </button>
                        <button class="btn btn-primary btn-sm" v-else @click="navigateTo('/home/student/add')">
                            <i class="fas fa-plus me-2"></i>Add New
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" v-if="!isLoading">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="5%" class="fw-bold text-muted text-center">#</th>
                                <th scope="col" width="25%" class="fw-bold text-muted">Name</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-if="isStaff">Designation</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-else>Batch</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-if="!isStaff">Dept.</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-if="isStaff">Phone</th>
                                <th scope="col" width="10%" class="fw-bold text-muted" v-else>Roll No</th>
                                <th scope="col" width="20%" class="fw-bold text-muted text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody v-if="users?.data?.length === 0">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                    <div class="fw-bold text-muted">No {{ isStaff ? 'staff' : 'student' }} found.</div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr v-for="user,index in users.data" :key="user.id" class="align-middle border-bottom">
                                <td class="fw-bold text-center">{{ users.per_page * (users.current_page-1)+index+1 }}</td>
                                <td class="fw-500">
                                  <img :src="user.profile_picture" alt="Profile Picture" class="round-thumb" v-if="user.profile_picture">
                                  <img src="http://localhost:8000/storage/images/profile.svg" alt="Profile Picture" class="round-thumb" v-else> {{ user.name }}
                                </td>
                                <td v-if="isStaff">{{ user.designation }}</td>
                                <td v-else>{{ user.batches?.name ?? 'NIL' }}</td>
                                <td v-if="!isStaff">{{ user.department }}</td>
                                <!-- <td>{{ user.dob ? new Date(user.dob).toISOString().split('T')[0].split('-').reverse().join('-') : '' }}</td> -->
                                <td  v-if="isStaff">{{ user.mobile }}</td>
                                <td  v-else>{{ user.rollNo }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#myModal" @click="editUser(user)" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button  type="button" class="btn btn-outline-primary" v-if="user.role !== 'ADMIN'"  @click="deleteUser(user)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                         <button type="button" class="btn btn-outline-primary" v-if="user.role !== 'ADMIN' && !isStaff" title="Edit"   @click="navigateTo('/home/student/edit/'+user.id)">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" v-if="user.role !== 'ADMIN'" title="Reset Password"   @click="resetPassword(user)">
                                            <i class="fa fa-key"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="d-flex justify-content-center p-3">
                     <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light p-3" v-if="users?.links">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item" v-for="link in users.links" :key="link.label+'_filtered'" :class="link.active ? 'active' : ''"
                        v-if="searchQuery.trim()=== ''">
                            <a class="page-link" href="#" @click.prevent="link.url && getAllUsers(link.url)" :disabled="!link.url">
                                {{ decodeHtmlEntities(link.label) }}
                            </a>
                        </li>
                        <li class="page-item" v-for="link in users.links" :key="link.label+'_search'" :class="link.active ? 'active' : ''"
                        v-else>
                            <a class="page-link" href="#" @click.prevent="link.url && getUsersByName(link.url)" :disabled="!link.url">
                                {{ decodeHtmlEntities(link.label) }}
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
    import { ref,onMounted,watch,computed} from 'vue'
    import api from '@/utils/axios'
    import { showToast } from '../utils/toastr'
    import Swal from 'sweetalert2'

    import { useRoute,useRouter } from 'vue-router';
    const routes = useRoute();
    const router = useRouter();

    import { Modal } from 'bootstrap'
    import required from './required.vue'
    import debounce from 'lodash/debounce';

    const emit = defineEmits(['trigger']);

    const isLoading = ref(true)

    const users = ref([])

    const baseURL = import.meta.env.VITE_API_BASE_URL;

    const selectedUser = ref(null)
    const selectedAddress = ref([

    ])

    const lastInPage = ref('')

    let modalInstance = null;
    const myModalRef = ref(null);

    const name = ref('')
    const username = ref('')
    const email = ref('')
    const dob = ref('')
    const mobile = ref('')
    const gender = ref('')
    const profile_picture = ref('')
    const role = ref('STUDENT')
    const course = ref('')
    const rollNo = ref('')
    const university = ref('OSH - IMF')
    const location = ref('active')
    const visaType = ref('EDUCATION')
    const visaStatus = ref('active')
    const visaExpiryDate = ref('')
    const insuranceStatus = ref('active')
    const insuranceExpiryDate = ref('')
    const designation = ref('')
    const batchName = ref('')
    const street = ref('')
    const city = ref('')
    const country = ref('')
    
    const isEditMode = ref(false)
    const showBatch = ref(false)
    const searchQuery = ref('')

    const fileUploads = ref([])




    onMounted(()=>{

        getAllUsers()

        modalInstance = new Modal(myModalRef.value);
        myModalRef.value.addEventListener('hidden.bs.modal', () => {
            getAllUsers(lastInPage.value)
            selectedUser.value = null
            showBatch.value = false
            isEditMode.value = false
            selectedAddress.value = []
            fileUploads.value = []
        })

    })



    const getAllUsers = (page = null) =>{
        
        let staffFlag = isStaff.value ? 1:0
        const url = page ? page+`&isStaff=${staffFlag}` : `${baseURL}/users?isStaff=${staffFlag}` 
        lastInPage.value = url        
        const response = api.get(url).then((response)=>{
            users.value = response.data.data
        }).catch((error)=>{
            console.log(error);
            showToast({title:error,icon:'error'})
        })
        setTimeout(() => {
            isLoading.value = false
        }, 1000);
    }

    const showAddModal = () => {
        isEditMode.value = false
        selectedUser.value = { name: '', username: '', email: '', mobile: '', dob: '', gender: '', address: [], profile_picture: '', role: 'STUDENT', course: '', rollNo: '', university: 'OSH - IMF', location: 'active', visaType: 'EDUCATION', visaStatus: 'active', visaExpiryDate: '', insuranceStatus: 'active', insuranceExpiryDate: '', designation: '' }
        selectedAddress.value = [{ city: '', state: '', street: '', country: '', landmark: '', officeType: '' }]
        showBatch.value = false
        modalInstance.show()
    }

    const  decodeHtmlEntities = (html) =>  {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = html;
    return textarea.value;
    }

    const editUser = (user) =>{
        selectedUser.value = null
        selectedUser.value = user
        isEditMode.value = true
        showBatch.value = routes.name === 'list'
    }

    const addAddress = () => {
        if(selectedAddress.value.length <= 1)
        {

            selectedAddress.value.push({
                city: '',
                state: '',
                street: '',
                country: '',
                landmark: '',
                officeType: '',
            })
        }
        else{
            showToast({title:'Maximum addresses are limited to 2',icon:'error'})
        }
    }

    const removeAddress = (index) =>{
        if(selectedAddress.value.length > 1)
        {
            selectedAddress.value.splice(index,1)
        }
        else{
            showToast({title:'Min one address is required',icon:'error'})
        }
    }

 
    watch(selectedUser,(newVal)=>{
        
        if(newVal)
        {
            name.value = newVal.name
            username.value = newVal.username
            email.value = newVal.email
            dob.value = newVal.dob
            mobile.value = newVal.mobile
            gender.value = newVal.gender
            profile_picture.value = newVal.profile_picture || ''
            role.value = newVal.role 
            course.value = newVal.course || ''
            rollNo.value = newVal.rollNo || ''
            university.value = newVal.university || ''
            location.value = newVal.location || 'active'
            visaType.value = newVal.visaType || 'EDUCATION'
            visaStatus.value = newVal.visaStatus || 'active'
            visaExpiryDate.value = newVal.visaExpiryDate || ''
            insuranceStatus.value = newVal.insuranceStatus || 'active'
            insuranceExpiryDate.value = newVal.insuranceExpiryDate || ''
            designation.value = newVal.designation || ''
            batchName.value = newVal?.batches?.name || ''
            street.value = newVal.address[0].street || ''
            city.value = newVal.address[0].city || ''
            country.value = newVal.address[0].country || ''
            fileUploads.value = newVal.student_doc

        }
        else{
            selectedAddress.value = []
            name.value = ''
            username.value = ''
            email.value = ''
            dob.value = ''
            mobile.value = ''
            gender.value = ''
            profile_picture.value = ''
            role.value = ''
            course.value = ''
            rollNo.value = ''
            university.value = ''
            location.value = ''
            visaType.value = ''
            visaStatus.value = ''
            visaExpiryDate.value = ''
            insuranceStatus.value = ''
            insuranceExpiryDate.value = ''
            designation.value = ''
            batchName.value = ''
            street.value = ''
            city.value = ''
            country.value = ''
            fileUploads.value = []
        }
    })

    watch(searchQuery,(newVal)=>{
        handleSearch();
    })

   


    const nameRegex = /^[A-Za-z\s]{2,50}$/

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/

    const usernameRegex = /^(?!.*[._]{2})[a-zA-Z][a-zA-Z0-9._]{2,29}$/

    const mobileRegex = /^(\+91[\-\s]?|91[\-\s]?|0)?[6-9]\d{9}$/

    const isValidDOB = (dob) => {
        const selectedDate = new Date(dob)
        const today = new Date()

        // Remove time part to compare only date
        selectedDate.setHours(0, 0, 0, 0)
        today.setHours(0, 0, 0, 0)

        return selectedDate <= today
    }

    const deleteUser = async(user) =>
    {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'You won’t be able to undo this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
        })

        if(result.isConfirmed)
        {
            const response = api.delete(`${baseURL}/users/${user.id}`).then((response)=>{
                if(response.data.status === 1)
                {
                    showToast({title:response.data.message, icon:'success'})
                    getAllUsers(lastInPage.value)
                }
                else{
                    showToast({title:response.data.message, icon:'error'})    
                }
            })
            
        }
    }

    const emitPagination = (url) =>{
        emit("trigger",url)
    }

    const resetPassword = async(user) =>{
        const result = await Swal.fire({
            title: 'Confirm',
            text: 'You are going reset password to username',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Reset',
            cancelButtonText: 'Cancel',
        })

        if(result.isConfirmed)
        {
            const response = api.get(`${baseURL}/users/${user.id}`).then((response)=>{
            if(response.data.status === 1)
            {
                showToast({title:response.data.message, icon:'success'})
                getAllUsers(lastInPage.value)
            }
            else{
                showToast({title:response.data.message, icon:'error'})    
            }
            }).catch((error)=>{
                console.log(error)
            })
            
        }
    }

    const getUsersByName = async (page=null)=>{        
        const request = {}
        request.name = searchQuery.value
        request.isStaff = isStaff.value ? 1 : 0
        
        if(request.name.trim() === '')
        {
            getAllUsers()
            return
        }
        else{
            request.name = request.name.trim()
            const url = page ? page : `${baseURL}/users/find` 
            const response = await api.post(url,request).then((response) =>{
                            
                if(response.data.status === 1)
                {
                    users.value = response.data.data
                }
                else{
                    showToast({title:response.data.message, icon:'error'})    
                }
            })
        }
    }

    const handleSearch = debounce(() => {
        getUsersByName();
    }, 500);

    const  modeSelected = computed(()=>{
        
        if(routes.name === 'staff.list')
        {
            return 'STAFF'
        }
        else if(routes.name === 'student.list')
        {
            return 'STUDENT'
        }
        else{
            return 'ADMIN'
        }
    })

    const navigateTo = (url) => {
        router.push(url)
    }

    const isStaff = computed(()=>{
        return modeSelected.value === 'STAFF' || modeSelected.value === 'ADMIN'
    })

    watch(isStaff,()=>{
        getAllUsers();
    })

    const openInNewTab = (url) => {
         window.open(url, '_blank', 'noreferrer');
    };
</script>

<style scoped>
.modal:not(.show) {
    display: none !important;
}

.font-sm{
    font-size: small;
}

.border{
    border: 1px solid #cccccc86;
}

.profile-view {
    padding: 20px;
}

.section-title {
    color: #007bff;
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
    margin-bottom: 15px;
    font-weight: bold;
}

.profile-section p {
    margin-bottom: 8px;
    line-height: 1.5;
}

.address-card {
    background-color: #f8f9fa;
    border-radius: 5px;
}

.address-card h6 {
    margin-bottom: 10px;
    color: #495057;
}

.round-thumb {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
    display: inline-block;
}

.round-thumb-pw {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
    display: inline-block;
}
</style>