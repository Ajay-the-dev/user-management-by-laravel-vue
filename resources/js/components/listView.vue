<template>
<div class="row my-5 mx-2">
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="extraLargeModalLabel" aria-hidden="true" ref="myModalRef">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="extraLargeModalLabel" v-if="modeSelected === 'STAFF'">Staff Details</h5>
                <h5 class="modal-title" id="extraLargeModalLabel" v-else-if="modeSelected === 'ADMIN'">Staff Details</h5>
                <h5 class="modal-title" id="extraLargeModalLabel" v-else>Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-12" v-if="selectedUser!==null">
                        <div class="profile-view">
                            <div class="profile-section mb-4">
                                <div class="text-center mb-4">
                                    <img v-if="profile_picture" :src="profile_picture" alt="Profile Picture" class="round-thumb-pw">
                                    <img v-else src="http://localhost:8000/storage/images/profile.svg" alt="Profile Picture" class="round-thumb-pw">
                                </div>
                                <h6 class="section-title">Personal Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> {{ name }}</p>
                                        <p><strong>Username:</strong> {{ username }}</p>
                                        <p><strong>Email:</strong> {{ email }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Mobile:</strong> {{ mobile }}</p>
                                        <p><strong>Date of Birth:</strong> {{ dob }}</p>
                                        <p><strong>Gender:</strong> {{ gender }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-section mb-4" v-if="isStaff">
                                <h6 class="section-title">Profile & Role</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Role:</strong> {{ role }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Designation:</strong> {{ designation || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-section mb-4" v-if="!isStaff">
                                <h6 class="section-title">Academic Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Course:</strong> {{ course || 'N/A' }}</p>
                                        <p><strong>Roll Number:</strong> {{ rollNo || 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>University:</strong> {{ university }}</p>
                                        <p><strong>Batch:</strong> {{ batchName || '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-section mb-4" v-if="!isStaff">
                                <h6 class="section-title">Location & Visa</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Location:</strong> {{ location }}</p>
                                        <p><strong>Visa Type:</strong> {{ visaType }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Visa Status:</strong> {{ visaStatus }}</p>
                                        <p><strong>Visa Expiry Date:</strong> {{ visaExpiryDate || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-section mb-4" v-if="!isStaff">
                                <h6 class="section-title">Insurance</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Insurance Status:</strong> {{ insuranceStatus }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Insurance Expiry Date:</strong> {{ insuranceExpiryDate || 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="profile-section" v-if="!isStaff">
                                <h6 class="section-title">Address</h6>
                                <div class="row">
                                     <div class="col-md-4">
                                         <p><strong>Street:</strong> {{ street || '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>City:</strong> {{ city || '-' }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Country:</strong> {{ country || '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                         <button class="btn btn-primary btn-sm" v-if="isStaff" @click="navigateTo('/home/staff-add')">
                            <i class="fas fa-plus me-2"></i>Add New
                        </button>
                        <button class="btn btn-primary btn-sm" v-else @click="navigateTo('/home/student-add')">
                            <i class="fas fa-plus me-2"></i>Add New
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="5%" class="fw-bold text-muted text-center">#</th>
                                <th scope="col" width="30%" class="fw-bold text-muted">Name</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-if="isStaff">Designation</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-else>Batch</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-if="!isStaff">Dept.</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-if="isStaff">Phone</th>
                                <th scope="col" width="15%" class="fw-bold text-muted" v-else>Roll No</th>
                                <th scope="col" width="15%" class="fw-bold text-muted text-center">Actions</th>
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
                                         <button type="button" class="btn btn-outline-primary" v-if="user.role !== 'ADMIN' && !isStaff" title="Edit"   @click="navigateTo('/home/student-edit/'+user.id)">
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




    onMounted(()=>{

        getAllUsers()

        modalInstance = new Modal(myModalRef.value);
        myModalRef.value.addEventListener('hidden.bs.modal', () => {
            getAllUsers(lastInPage.value)
            selectedUser.value = null
            showBatch.value = false
            isEditMode.value = false
            selectedAddress.value = []
        })

    })



    const getAllUsers = (page = null) =>{
        console.log(isStaff.value);
        
        let staffFlag = isStaff.value ? 1:0
        const url = page ? page+`&isStaff=${staffFlag}` : `${baseURL}/users?isStaff=${staffFlag}` 
        lastInPage.value = url

        console.log(url);
        
        const response = api.get(url).then((response)=>{
            users.value = response.data.data
        }).catch((error)=>{
            console.log(error);
            showToast({title:error,icon:'error'})
        })
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
            console.log(newVal);
            
            // selectedAddress.value = newVal.address ? JSON.parse(newVal.address) : selectedAddress.value
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
                console.log(response)
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
                console.log(response);
                            
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
        
        if(routes.name === '/home/staff-home')
        {
            return 'STAFF'
        }
        else if(routes.name === '/home/student-home')
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