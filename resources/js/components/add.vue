<template>
<div class="mx-5 my-4">
    <h4 v-if="isStaff">Add staff</h4>
    <h4 v-else>Add student</h4>
    <p v-if="isStaff" class="text-secondary mb-4 mt-0">Onboard new faculty and administrative personnel</p>
    <p v-else class="text-secondary mb-4 mt-0">Onboard new students</p>
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm section-card mb-4">
                <div class="card-header bg-white border-bottom p-4 bg-primary-subtle">
                    <h5 class="mb-0">Personal Details</h5>
                </div>
                <div class="card-body p-5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <required/></label>
                            <input type="text" class="form-control" required v-model="name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender <required/></label>
                            <select class="form-select" v-model="gender">
                                <option value="">choose</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">DOB <required/></label>
                            <input type="date" class="form-control" required v-model="dob">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username <required/></label>
                            <input type="text" class="form-control" required v-model="username">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <required/></label>
                            <input type="password" class="form-control" required v-model="password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile </label>
                            <input type="phone" class="form-control" required v-model="mobile">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" required v-model="email">
                        </div>
                        <div class="col-md-6" v-if="isStaff">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" v-model="designation">
                        </div>
                        <div class="col-md-6" v-if="uploadedImage.length === 0 ">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" @change="handleFileUpload">
                        </div>
                        <div class="col-md-6" v-if="uploadedImage.length > 0">
                            <label class="form-label">Profile Picture Preview</label>
                            <div class="profile-preview-container card p-2 d-flex align-items-center w-50">
                                <img :src="uploadedImage" alt="Profile Picture" class="profile-preview-image p-1" style="width: 10rem;">
                                <button class="btn btn-sm btn-outline-danger remove-image-btn" @click="removeImage">
                                    <i class="fa fa-times"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm section-card mb-4">
                <div class="card-header bg-white border-bottom p-4 bg-primary-subtle">
                    <h5 class="mb-0">Address Details</h5>
                </div>
                <div class="card-body p-5">
                  <div class="row my-4">
                    <div class="col-3">
                        <label for="" class="form-label">Address type <required/></label>
                        <select name="" id="" class="form-select" v-model="officeType">
                            <option value="">choose</option>
                            <option value="home">Home</option>
                            <option value="office">Office</option>
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="" class="form-label">Street <required/></label>
                        <input type="text" class="form-control" v-model="street">
                    </div>
                    <div class="col-3">
                        <label for="" class="form-label">Landmark </label>
                        <input type="text" class="form-control" v-model="landmark">
                    </div>
                    <div class="col-3">
                        <label for="" class="form-label">City <required/></label>
                        <input type="text" class="form-control" v-model="city">
                    </div>
                </div>
                <div class="row my-4">
                    <div class="col-3">
                        <label for="" class="form-label">State <required/></label>
                        <input type="text" class="form-control" v-model="state">
                    </div>
                    <div class="col-3">
                        <label for="" class="form-label">Country <required/></label>
                        <input type="text" class="form-control" v-model="country">
                    </div>
                    <div class="align-content-end col-3">
                        <button class="btn btn-primary" @click="insertLocalAddress" :disabled="addresses.length == 2">
                            <i class="fa fa-bookmark mx-1" aria-hidden="true"></i>
                            save
                        </button>
                    </div>
                </div>
                </div>
            </div>
            <div class="card shadow-sm section-card mb-4" v-if="!isStaff">
                <div class="card-header bg-white border-bottom p-4 bg-primary-subtle">
                    <h5 class="mb-0">Academic Details</h5>
                </div>
                <div class="card-body p-5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <input type="text" class="form-control" v-model="course">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Roll No</label>
                            <input type="text" class="form-control" v-model="rollNo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">University</label>
                            <input type="text" class="form-control" v-model="university">
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm section-card mb-4">
                <div class="card-header bg-white border-bottom p-4 bg-primary-subtle">
                    <h5 class="mb-0">Visa Details</h5>
                </div>
                <div class="card-body p-5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" v-model="location">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Visa Type</label>
                            <select class="form-select" v-model="visaType">
                                <option value="EDUCATION">Education</option>
                                <option value="WORK">Work</option>
                                <option value="TOURIST">Tourist</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Visa Status</label>
                            <select class="form-select" v-model="visaStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Visa Expiry Date</label>
                            <input type="date" class="form-control" v-model="visaExpiryDate">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm section-card mb-4">
                <div class="card-header bg-white border-bottom p-4 bg-primary-subtle">
                    <h5 class="mb-0">Insurance Details</h5>
                </div>
                <div class="card-body p-5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Insurance Status</label>
                            <select class="form-select" v-model="insuranceStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Insurance Expiry Date</label>
                            <input type="date" class="form-control" v-model="insuranceExpiryDate">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-4" v-for="address,index in addresses">
                    <div class="card p-3">
                        <div class="card-container">
                            <span>Type : {{ address.officeType }}</span>
                            <br>
                            <span>Street : {{ address.street }}</span>
                            <br>
                            <span>Landmark : {{ address.landmark }}</span>
                            <br>
                            <span>City : {{ address.city }}</span>
                            <br>
                            <span>State : {{ address.state }}</span>
                            <br>
                            <span>Country : {{ address.country }}</span>  
                            <br>
                            <button class="btn my-2 btn-sm btn-secondary" @click="removeAddressFromList(index)">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-4">
        <div class="col-12">
            <button class="btn btn-sm btn-primary" @click="addAddress">
                <i class="fa fa-plus p-2" aria-hidden="true"></i>Add address
            </button>
            <button class="btn btn-sm btn-primary mx-2" @click="addUser">
               <i class="fa fa-user-plus p-2" aria-hidden="true"></i> Save user
            </button>
        </div>
    </div>
    
</div>
</template>

<script setup>
import { ref,onMounted,computed } from 'vue'
import { useRoute,useRouter } from 'vue-router';
import { showToast } from '../utils/toastr'
import api from '@/utils/axios'
import required from './required.vue';
const baseURL = import.meta.env.VITE_API_BASE_URL;
const imageURL = import.meta.env.VITE_IMAGE_BASE_URL;

const route = useRoute()
const router = useRouter()


const addresses = ref([])
const officeType = ref('')
const street = ref('')
const landmark = ref('')
const city = ref('')
const state = ref('')
const country = ref('')
const showAddress = ref(true)


const name = ref('')
const gender = ref('')
const dob = ref('')
const email = ref('')
const username = ref('')
const mobile = ref('')
const password = ref('')
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
const previewSrc = ref('')

const uploadedImage = ref('')
const imageName = ref('')


const resetAll = () =>{

addresses.value =  []
officeType.value =  ''
street.value =  ''
landmark.value =  ''
city.value =  ''
state.value =  ''
country.value =  ''
showAddress.value = false


name.value =  ''
    gender.value =  ''
    dob.value =  ''
    email.value =  ''
    username.value =  ''
    mobile.value =  ''
    password.value =  ''
    profile_picture.value = ''
    role.value = 'STUDENT'
    course.value = ''
    rollNo.value = ''
    university.value = 'OSH - IMF'
    location.value = 'active'
    visaType.value = 'EDUCATION'
    visaStatus.value = 'active'
    visaExpiryDate.value = ''
    insuranceStatus.value = 'active'
    insuranceExpiryDate.value = ''
    designation.value = ''
} 


const insertLocalAddress = () => {

    if(officeType.value.trim() === '')
    {
        showToast({title:'Choose office type',icon:'error'})
        return
    }

    if(street.value.trim() === '')
    {
        showToast({title:'Please enter a street',icon:'error'})
        return
    }

    // if(landmark.value.trim() === '')
    // {
    //     showToast({title:'Please enter a landmark',icon:'error'})
    //     return
    // }

     if(city.value.trim() === '')
    {
        showToast({title:'Please enter a city',icon:'error'})
        return
    }

     if(state.value.trim() === '')
    {
        showToast({title:'Please enter a state',icon:'error'})
        return
    }

    if(country.value.trim() === '')
    {
        showToast({title:'Please enter a country',icon:'error'})
        return
    }

    addresses.value.push(
        {
            officeType:officeType.value,
            street:street.value,
            landmark:landmark.value,
            city:city.value,
            state:state.value,
            country:country.value
        }
    )
    setTimeout(() => {
        clearLocalAddress()
        showAddress.value = false
    }, 1000);

}


const clearLocalAddress = () =>{

    officeType.value = ''
    street.value = ''
    landmark.value = ''
    city.value = ''
    state.value = ''
    country.value = ''
}
        
const addAddress = () =>{
    if(addresses.value.length >= 2)
    {
        showToast({title:'Maximum addresses are limited to 2',icon:'error'})
        return
    }
    showAddress.value = !showAddress.value
}

const addUser = () =>{
    
    var userData = {}
    userData.name = name.value
    userData.gender = gender.value
    userData.dob = dob.value
    userData.email = email.value
    userData.username = username.value
    userData.mobile = mobile.value
    userData.password = password.value
    userData.profile_picture = profile_picture.value
    userData.role = role.value
    userData.course = course.value
    userData.rollNo = rollNo.value
    userData.university = university.value
    userData.location = location.value
    userData.visaType = visaType.value
    userData.visaStatus = visaStatus.value
    userData.visaExpiryDate = visaExpiryDate.value
    userData.insuranceStatus = insuranceStatus.value
    userData.insuranceExpiryDate = insuranceExpiryDate.value
    userData.designation = designation.value

    var mappings = {}
    mappings.name = 'Name'
    mappings.gender = 'Gender'
    mappings.dob = 'Date of Birth'
    mappings.email = 'Email'
    mappings.username = 'Username'
    mappings.mobile = 'Mobile Number'
    mappings.password = 'Password'
    mappings.role = 'Role'

    var requiredFields = ['name', 'gender', 'dob', 'email', 'username', 'mobile', 'password', 'role']
    var error = false

    for (const key of requiredFields) {
        const element = userData[key];
        if (!element || element.toString().trim() === '') {
            var entry = mappings[key]
            showToast({title:entry+' is empty',icon:'error'})
            error = true
            break
        }

        if(key == 'name') {
            const isValidName = nameRegex.test(element)
            if (!isValidName) {
                showToast({title:'Invalid name',icon:'error'})
                error = true
                break
            }
        }

        if(key == 'dob') {
            const valid = isValidDOB(element)
            if(!valid) {
                showToast({title:'Invalid date of birth',icon:'error'})
                error = true
                break
            }
        }

        if(key == 'email') {
            const isValid = emailRegex.test(element)
            if (!isValid) {
                showToast({title:'Invalid email',icon:'error'})
                error = true
                break
            }
        }

        if(key == 'username') {
            const isValid = usernameRegex.test(element)
            if (!isValid) {
                showToast({title:'Invalid username ',icon:'error'})
                error = true
                break
            }
        }

        if(key == 'mobile') {
            const isValid = mobileRegex.test(element)
            if (!isValid) {
                showToast({title:'Invalid mobile number ',icon:'error'})
                error = true
                break
            }
        }

        if(key == 'password') {
            const isValid = passwordRegex.test(element)
            if (!isValid) {
                showToast({title:'Invalid password (min 8 characters)',icon:'error'})
                error = true
                break
            }
        }
    }

    if(addresses.value.length === 0)
    {
        showToast({title:'Address not found ',icon:'error'})
        error = true
    }


    if(!error)
    {
        const response = api.post(`${baseURL}/users/create`,
            {
                data : userData,
                address: addresses.value
            }
        ).then((response)=>{
            const data = response.data

            console.log(response);
            

            if(data.status === 1)
            {
                showToast({title:data.message,icon:'success'})
                resetAll()
            }
            else{
                showToast({title:data.message,icon:'error'})
            }

            
        }).catch((error)=>{
            console.log(error);
        })
    }



}

const nameRegex = /^[A-Za-z\s]{2,50}$/

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/

const usernameRegex = /^(?!.*[._]{2})[a-zA-Z][a-zA-Z0-9._]{2,29}$/

const mobileRegex = /^(\+91[\-\s]?|91[\-\s]?|0)?[6-9]\d{9}$/

const passwordRegex = /^.{8,}$/




const isValidDOB = (dob) => {
  const selectedDate = new Date(dob)
  const today = new Date()

  // Remove time part to compare only date
  selectedDate.setHours(0, 0, 0, 0)
  today.setHours(0, 0, 0, 0)

  return selectedDate <= today
}


const removeAddressFromList = (index) =>{
    addresses.value.splice(index,1)
}

onMounted(()=>{
    resetAll()
})

 const  modeSelected = computed(()=>{
        
        if(route.name === '/home/staff-add')
        {
            return 'STAFF'
        }
        else if(route.name === '/home/student-add')
        {
            return 'STUDENT'
        }
        else{
            return 'ADMIN'
        }
    })

const isStaff = computed(()=>{
    return modeSelected.value === 'STAFF' || modeSelected.value === 'ADMIN'
})

const handleFileUpload = async(event) => {
    const file = event.target.files[0];
    if (!file) return;
    const isValidType = file.type === 'image/jpeg' || file.type === 'image/jpg';
    const minSize = 100 * 1024; // 100KB in bytes
    const maxSize = 2048 * 1024; // 2MB in bytes
    const isValidSize = file.size >= minSize && file.size <= maxSize;
    if (!isValidType || !isValidSize) {
        showToast({title:'Invalid file type or size',icon:'error'})
        event.target.value = "";
        return;
    }
    profile_picture.value = file;
    if (!profile_picture.value) return;

    const formData = new FormData();
    formData.append('image', profile_picture.value);

    const response = await api.post('/images/upload-image',formData).catch((err)=>{
        return err;
    })
    uploadedImage.value = imageURL+response.data.url 
    imageName.value = response.data.fileName   
};


const removeImage = async() =>{
    uploadedImage.value = ''
    profile_picture.value = ''
    const req = {}
    req.fileName = imageName.value
    const response = await api.post('images/remove',req).catch((err)=>{
        return err;
    })
    setTimeout(() => {
        imageName.value = ''
    }, 500);
    
}

</script>

<style scoped>
.font-sm{
    font-size: small;
}

.section-card {
    border: 1px solid #e4e7eb;
    border-radius: 12px;
}

.section-card .card-header {
    background-color: #ffffff;
}

.section-card .card-title,
.section-card h5 {
    font-size: 1rem;
    font-weight: 700;
    color: #2c3e50;
}

.profile-preview-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    border: 2px dashed #007bff;
    border-radius: 10px;
    background-color: #f8f9fa;
    margin-top: 10px;
}

.profile-preview-image {
    /* width: 120px;
    height: 120px; */
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #007bff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.remove-image-btn {
    margin-top: 10px;
}
</style>