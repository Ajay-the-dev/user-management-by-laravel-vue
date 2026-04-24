<template>
  <div class="main-wrapper">
    <div class="content-scroll">

      <div class="container-fluid form-container">

        <!-- HEADER -->
        <div class="mb-4">
          <h4 class="fw-semibold">
            {{ isStaff ? 'Add Staff' : 'Add Student' }}
          </h4>
          <p class="text-muted small">
            {{ isStaff 
              ? 'Onboard new faculty and administrative personnel' 
              : 'Onboard new students' }}
          </p>
        </div>

        <!-- PERSONAL -->
        <div class="form-section">
          <div class="section-header">
            <h6><i class="fa fa-user"></i> Personal Details</h6>
            <span class="section-sub">Basic information</span>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input class="form-control" v-model="name">
            </div>

            <div class="col-md-6">
              <label class="form-label">Gender</label>
              <select class="form-select" v-model="gender">
                <option value="">Choose</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">DOB</label>
              <input type="date" class="form-control" v-model="dob">
            </div>

            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input class="form-control" v-model="username">
            </div>

            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" v-model="password">
            </div>

            <div class="col-md-6">
              <label class="form-label">Mobile</label>
              <input class="form-control" v-model="mobile">
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input class="form-control" v-model="email">
            </div>

            <div class="col-md-6" v-if="isStaff">
              <label class="form-label">Designation</label>
              <input class="form-control" v-model="designation">
            </div>

            <!-- IMAGE -->
            <div class="col-md-12" v-if="uploadedImage.length === 0 ">
                <label class="form-label">Profile Picture</label>
                <input type="file" class="form-control" @change="handleFileUpload">
            </div>
            <div class="col-md-12" v-if="uploadedImage.length > 0">
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

        <!-- ADDRESS -->
        <div class="form-section">
          <div class="section-header">
            <h6><i class="fa fa-map-marker-alt"></i> Address</h6>
            <span class="section-sub">User address information</span>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-4">
              <input class="form-control" placeholder="Street" v-model="street">
            </div>
            <div class="col-md-4">
              <input class="form-control" placeholder="City" v-model="city">
            </div>
            <div class="col-md-4">
              <input class="form-control" placeholder="Country" v-model="country">
            </div>

            <div class="col-12">
              <button class="btn btn-outline-primary btn-sm" @click="setAddress">
                Set Address
              </button>
            </div>
          </div>

          <!-- ADDRESS DISPLAY -->
          <div class="row mt-3" v-if="addresses.street">
            <div class="col-md-4">
              <div class="address-card">
                <p>{{ addresses.street }}</p>
                <small>{{ addresses.city }}, {{ addresses.country }}</small>
                <button class="btn btn-sm text-danger" @click="clearAddress">
                  Remove
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ACADEMIC -->
        <div class="form-section" v-if="!isStaff">
          <div class="section-header">
            <h6><i class="fa fa-graduation-cap"></i> Academic Details</h6>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <input class="form-control" placeholder="Course" v-model="course">
            </div>
            <div class="col-md-6">
              <input class="form-control" placeholder="Roll No" v-model="rollNo">
            </div>
            <div class="col-md-6">
              <input class="form-control" placeholder="University" v-model="university">
            </div>
            <div class="col-md-6">
              <input class="form-control" placeholder="Department" v-model="department">
            </div>
          </div>
        </div>

        <div class="form-section">
          <div class="section-header">
            <h6><i class="fa fa-passport"></i> Visa Details</h6>
            <span class="section-sub">Immigration and visa information</span>
          </div>
  
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Visa Type</label>
              <select class="form-select" v-model="visaType">
                <option value="">Choose</option>
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
  
        <!-- PASSPORT DETAILS -->
        <div class="form-section">
          <div class="section-header">
            <h6><i class="fa fa-id-card"></i> Passport Details</h6>
            <span class="section-sub">Passport information</span>
          </div>
  
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Passport Number</label>
              <input class="form-control" v-model="passportNumber" placeholder="Enter passport number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Passport Issue Date</label>
              <input type="date" class="form-control" v-model="passportIssueDate">
            </div>
            <div class="col-md-6">
              <label class="form-label">Passport Expiry Date</label>
              <input type="date" class="form-control" v-model="passportExpiryDate">
            </div>
            <div class="col-md-6">
              <label class="form-label">Issuing Country</label>
              <input class="form-control" v-model="passportIssuingCountry" placeholder="Enter issuing country">
            </div>
          </div>
        </div>
  
        <!-- PARENT DETAILS -->
        <div class="form-section" v-if="!isStaff">
          <div class="section-header">
            <h6><i class="fa fa-users"></i> Parent/Guardian Details</h6>
            <span class="section-sub">Primary contact information</span>
          </div>
  
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Parent/Guardian Name</label>
              <input class="form-control" v-model="parentName" placeholder="Enter parent/guardian name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Relation</label>
              <select class="form-select" v-model="parentRelation">
                <option value="">Choose</option>
                <option value="Father">Father</option>
                <option value="Mother">Mother</option>
                <option value="Guardian">Guardian</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Parent Email</label>
              <input type="email" class="form-control" v-model="parentEmail" placeholder="Enter parent email">
            </div>
            <div class="col-md-6">
              <label class="form-label">Parent Mobile</label>
              <input class="form-control" v-model="parentMobile" placeholder="Enter parent mobile number">
            </div>
          </div>
        </div>
      </div>


      <!-- VISA DETAILS -->

      <div class="action-bar bg-body-secondary d-flex justify-content-end p-4">
        <button class="btn btn-primary" @click="addUser">
          Save User
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


const addresses = ref({})
const street = ref('')
const city = ref('')
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

addresses.value =  {}
street.value =  ''
city.value =  ''
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
    department.value = ''
    location.value = 'active'
    visaType.value = 'EDUCATION'
    visaStatus.value = 'active'
    visaExpiryDate.value = ''
    insuranceStatus.value = 'active'
    insuranceExpiryDate.value = ''
    designation.value = ''
} 


const setAddress = () => {

    if(street.value.trim() === '')
    {
        showToast({title:'Please enter a street',icon:'error'})
        return
    }

     if(city.value.trim() === '')
    {
        showToast({title:'Please enter a city',icon:'error'})
        return
    }

    if(country.value.trim() === '')
    {
        showToast({title:'Please enter a country',icon:'error'})
        return
    }

    addresses.value = {
        street: street.value,
        city: city.value,
        country: country.value
    }

}


const clearAddressFields = () =>{

    street.value = ''
    city.value = ''
    country.value = ''
}
        
const clearAddress = () =>{
    addresses.value = {}
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

    if(!addresses.value.street || !addresses.value.city || !addresses.value.country)
    {
        showToast({title:'Address not found ',icon:'error'})
        error = true
    }


    if(!error)
    {
        const response = api.post(`${baseURL}/users/create`,
            {
                data : userData,
                address: [addresses.value]
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
.main-wrapper {
  height: 100vh;
  overflow: hidden;
  background: #f8fafc;
}

.content-scroll {
  height: 100%;
  overflow-y: auto;
}

.form-container {
  max-width: 1100px;
  margin-left: 5rem;
  padding: 20px;
}

/* Sections */
.form-section {
  background: white;
  padding: 24px;
  border-radius: 14px;
  border: 1px solid #e5e7eb;
  margin-bottom: 20px;
}

.section-header h6 {
  font-weight: 600;
}

.section-sub {
  font-size: 12px;
  color: #94a3b8;
}

/* Inputs */
.form-control,
.form-select {
  border-radius: 10px;
  padding: 10px;
  border: 1px solid #e5e7eb;
}

.form-control:focus,
.form-select:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}

/* Upload */
.upload-box {
  border: 2px dashed #c7d2fe;
  padding: 20px;
  text-align: center;
  border-radius: 10px;
  cursor: pointer;
}

.preview-box img {
  width: 100px;
  border-radius: 50%;
}

/* Address */
.address-card {
  background: #f1f5f9;
  padding: 12px;
  border-radius: 10px;
  font-size: 14px;
}

/* Sticky Action Bar */
.action-bar {
  position: sticky;
  bottom: 2rem;
  background: white;
  padding: 16px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  border-top: 1px solid #e5e7eb;
}
</style>