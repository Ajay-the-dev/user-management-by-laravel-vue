<template>
  <div class="main-wrapper">
    <div class="content-scroll">

      <div class="container-fluid form-container">

        <!-- HEADER -->
        <div class="mb-4" v-if="!isEditing">
          <h4 class="fw-semibold">
            {{ isStaff ? 'Add Staff' : 'Add Student' }}
          </h4>
          <p class="text-muted small">
            {{ isStaff 
              ? 'Onboard new faculty and administrative personnel' 
              : 'Onboard new students' }}
          </p>
        </div>
        <div class="mb-4" v-else>
          <h4 class="fw-semibold">
            {{ isStaff ? 'Edit Staff' : 'Edit Student' }}
          </h4>
          <p class="text-muted small">
            {{ isStaff 
              ? 'Edit onboarded faculties and administrative personnel' 
              : 'Edit onboard students' }}
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
              <required/>
              <input :class="['form-control',{ 'is-invalid border-danger-subtle': !isValidName(name) && name?.length > 0}]" v-model="name">
            </div>

            <div class="col-md-6">
              <label class="form-label">Gender</label>
              <required/>
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

            <div class="col-md-6" v-if="!isEditing">
              <label class="form-label">Username</label>
              <required/>
              <small class="text-danger mx-2" v-if="usernameExists&&username?.length ">( Already exists )</small>
              <input type="text" :class="['form-control', { 'is-invalid border-danger-subtle': usernameExists ,'is-valid':!usernameExists && username?.length>0}]" v-model="username" @change="checkUsername">
            </div>

            <div class="col-md-6" v-if="!isEditing">
              <label class="form-label">Password</label>
              <required/>
              
              <div class="input-group">
                 <input :type="showPassword ? 'text' : 'password'" :class="['form-control']" v-model="password">
                 <button class="btn btn-outline-primary" type="button" @click="showPassword = !showPassword">
                    <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                 </button>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Mobile</label>
              <input :class="['form-control', { 'is-invalid border-danger-subtle': !isValidPhoneNumber(mobile) && mobile?.length > 0 }]" v-model="mobile">
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input :class="['form-control', { 'is-invalid border-danger-subtle': !isValidEmail(email) && email?.length > 0 }]" v-model="email">
            </div>
             <div class="col-md-6">
              <label class="form-label">Location</label>
              <select name="location" class="form-control" id="location" v-model="location">
                <option value="">Choose Location</option>
                <option value="HA">Hostel A</option>
                <option value="HB">Hostel B</option>
                <option value="HC">Hostel C</option>
              </select>
            </div>

            <div class="col-md-6" v-if="isStaff">
              <label class="form-label">Designation</label>
              <input class="form-control" v-model="designation">
            </div>

            <!-- IMAGE -->
            <div class="col-md-12" v-if="uploadedImage?.length === 0 ">
                <label class="form-label">Profile Picture</label>
                <small class="mx-2 text-muted">(100KB - 2MB)</small>
                <input type="file" class="form-control" @change="handleFileUpload">
            </div>
            <div class="col-md-12" v-if="uploadedImage?.length > 0">
                <label class="form-label">Profile Picture Preview</label>
                <div class="profile-preview-container card p-2 d-flex align-items-center w-50" >
                    <img :src="uploadedImage" alt="Profile Picture" class="profile-preview-image p-1" style="width: 10rem;">
                    <button class="btn btn-sm btn-outline-danger remove-image-btn" @click="removeImage">
                        <i class="fa fa-times"></i> Remove
                    </button>
                </div>
            </div>

          </div>
        </div>

        <!-- ADDRESS -->
        <div class="form-section" v-if="!isStaff">
          <div class="section-header">
            <h6><i class="fa fa-map-marker-alt"></i> Address</h6>
            <span class="section-sub">User address information</span>
          </div>

          <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label">Street<required/></label>
              <input type="text" :class="['form-control', { 'is-invalid border-danger-subtle': !isAddress(street) && street?.length > 0 }]" placeholder="Enter street here..." v-model="street">
            </div>
            <div class="col-md-4">
              <label class="form-label">City<required/></label>
              <input :class="['form-control', { 'is-invalid border-danger-subtle': !isAddress(city) && city?.length > 0 }]" placeholder="Enter city here..." v-model="city">
            </div>
            <div class="col-md-4">
              <label class="form-label">Country<required/></label>
              <select name="country" id="country" class="form-select" v-model="country">
                <option value="">Choose Country</option>
                <option v-for="country in countries" :key="country.code" :value="country.code">
                  {{ country.name }}
                </option>
              </select>
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
              <label class="form-label">Roll No<required/></label>
              <input class="form-control" placeholder="Roll No" v-model="rollNo">
            </div>
            <div class="col-md-6">
              <label class="form-label">University<required/></label>
              <select name="selecteduniversity" id="selectedUniversity" class="form-select" v-model="university">
                <option value="">Choose University</option>
                <option v-for="univ in medicalUniversities" :key="univ.code" :value="univ.code">
                  {{ univ.name }}
                </option>
              </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Course<required/></label>
               <select name="course" id="course" class="form-select" v-model="course">
                <option value="">Choose Course</option>
                <option v-for="medicalCourse in universityCourses[university]" :key="medicalCourse.code" :value="medicalCourse.code">
                  {{ medicalCourse.name }}
                </option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Department<required/></label>
              <select name="department" id="department" class="form-select" v-model="department">
                <option value="">Choose Department</option>
                <option v-for="dept in medicalDepartments" :key="dept.code" :value="dept.code">
                  {{ dept.name }}
                </option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Batch<required/></label>
              <select name="selectedBatch" id="selectedBatch" class="form-select" v-model="selectedBatch">
                <option value="">Choose Batch</option>
                <option v-for="batch in batches" :key="batch.id" :value="batch.id">
                  {{ batch.name }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-section" v-if="!isStaff">
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
        <div class="form-section" v-if="!isStaff">
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
              <select name="passportIssuingCountry" id="passportIssuingCountry" class="form-select" v-model="passportIssuingCountry">
                <option value="">Choose Country</option>
                <option v-for="country in countries" :key="country.code" :value="country.code">
                  {{ country.name }}
                </option>
              </select>
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
              <required/>
              <input type="text" :class="['form-control',{ 'is-invalid border-danger-subtle': !isValidName(parentName) && parentName?.length > 0}]" v-model="parentName" placeholder="Enter parent/guardian name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Relation</label>
              <required/>
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
              <input type="email" :class="['form-control', { 'is-invalid border-danger-subtle': !isValidEmail(parentEmail) && parentEmail?.length > 0 }]" v-model="parentEmail" placeholder="Enter parent email">
            </div>
            <div class="col-md-6">
              <label class="form-label">Parent Mobile</label>
              <required/>
              <input type="text" :class="['form-control', { 'is-invalid border-danger-subtle': !isValidPhoneNumber(parentMobile) && parentMobile?.length > 0 }]" v-model="parentMobile" placeholder="Enter parent mobile number">
            </div>
          </div>
        </div>
        <!-- INSURANCE DETAILS -->
        <div class="form-section" v-if="!isStaff">
          <div class="section-header">
            <h6><i class="fa fa-shield-alt"></i> Insurance Details</h6>
            <span class="section-sub">Health and travel insurance information</span>
          </div>

          <div class="row g-3 mt-2">
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



      <!-- VISA DETAILS -->

      <div class="action-bar bg-body-secondary d-flex justify-content-end p-4">
        <button class="btn btn-primary" @click="addOrUpdateUser" v-if="!isEditing">
          <i class="fa fa-save mx-2"></i>Save 
        </button>
        <button class="btn btn-primary" @click="addOrUpdateUser" v-else>
          <i class="fa fa-save mx-2"></i>Update 
        </button>
        <button class="btn btn-primary" @click="goBack">
          Back 
        </button>
        <button class="btn btn-primary" @click="cancelStudent" v-if="!isEditing">
          Reset 
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref,onMounted,computed, watch } from 'vue'
import { useRoute,useRouter } from 'vue-router';
import { showToast } from '../utils/toastr'
import api from '@/utils/axios'
import required from './required.vue';
import { useValidators } from "../utils/validator";
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2'



const {isValidPhoneNumber, isValidEmail, isValidName, isAddress, isValidUsername,isValidPassword} = useValidators();



import { getMedicalUniversities, getMedicalCourses, getUniversityCourses,getMedicalDepartments,getCountries } from "../utils/dataHelper";

const medicalUniversities = getMedicalUniversities()
const universityCourses = getUniversityCourses()
const medicalDepartments = getMedicalDepartments()
const countries = getCountries()

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
const showPassword = ref(false)
const password = ref('')
const profile_picture = ref('')
const role = ref('')
const course = ref('')
const rollNo = ref('')
const university = ref('')
const location = ref('')

const visaType = ref('')
const visaStatus = ref('active')
const visaExpiryDate = ref('')

const insuranceStatus = ref('active')
const insuranceExpiryDate = ref('')

const passportNumber = ref('')
const passportIssueDate = ref('')
const passportExpiryDate = ref('')
const passportIssuingCountry = ref('')


const parentName = ref('')
const parentRelation = ref('')
const parentEmail = ref('')
const parentMobile = ref('')

const designation = ref('')
const previewSrc = ref('')
const department = ref('')


const uploadedImage = ref('')
const imageName = ref('')

const usernameExists = ref(false)

const batches = ref([])
const selectedBatch = ref('')


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
  role.value = ''
  course.value = ''
  rollNo.value = ''
  university.value = ''
  department.value = ''
  location.value = ''
  visaType.value = 'EDUCATION'
  visaStatus.value = 'active'
  visaExpiryDate.value = ''
  insuranceStatus.value = 'active'
  insuranceExpiryDate.value = ''
  designation.value = ''
  passportExpiryDate.value = ''
  passportIssueDate.value = ''
  passportNumber.value = ''
  passportIssuingCountry.value = ''
  parentEmail.value = ''
  parentName.value = ''
  parentRelation.value = ''
  parentMobile.value = ''
  uploadedImage.value = ''
  imageName.value = ''
  usernameExists.value = false
} 




const addOrUpdateUser = () =>{
    
    var userData = {}
    userData.name = name.value
    userData.gender = gender.value
    userData.dob = dob.value
    userData.email = email.value
    userData.username = username.value
    userData.mobile = mobile.value
    userData.password = password.value
    userData.profile_picture = uploadedImage.value
    userData.course = course.value
    userData.rollNo = rollNo.value
    userData.university = university.value

    userData.location = location.value
    

    userData.visaType = visaType.value
    userData.visaStatus = visaStatus.value
    userData.visaExpiryDate = visaExpiryDate.value

    userData.insuranceStatus = insuranceStatus.value
    userData.insuranceExpiryDate = insuranceExpiryDate.value
    
    userData.passportNumber = passportNumber.value
    userData.passportIssueDate = passportIssueDate.value
    userData.passportExpiryDate = passportExpiryDate.value
    userData.passportIssuingCountry = passportIssuingCountry.value

    userData.batchId = selectedBatch.value
    
    
    if(isStaff.value)
    {
      userData.role = "STAFF"
      userData.designation = designation.value
    }
    else{
      userData.department = department.value
      userData.role = "STUDENT"
    }

    console.log(userData);
  
    

    var mappings = {}
    mappings.name = 'Name'
    mappings.gender = 'Gender'
    mappings.dob = 'Date of Birth'
    mappings.email = 'Email'
    mappings.username = 'Username'
    mappings.mobile = 'Mobile Number'
    mappings.password = 'Password'
    mappings.role = 'Role'

    if(isStaff)
    {
      if(isEditing.value)
      {
        var requiredFields = ['name', 'gender']
      }
      else{
        var requiredFields = ['name', 'gender', 'username', 'password']
      }
    }
    else{
      var requiredFields = ['name', 'gender', 'username', 'password']
    }
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

    if(error)
    {
        return
    }

    if(usernameExists.value)
    {
      showToast({title:'Username already exists',icon:'error'})
        return
    }

    
    
    if(isStaff.value)
    {
      //staff
      if(designation.value.trim() === '')
      {
        showToast({title:'Please enter designation for staff',icon:'error'})
          return
      }
    }
    else{

      if(!street.value || !city.value || !country.value)
      {
        showToast({title:'Incompleted address ',icon:'error'})
          return
      }
      //student
      if(rollNo.value.trim() === '')
      {
        showToast({title:'Please enter roll number for student',icon:'error'})
          return
      }

      if(university.value.trim() === '')
      {
        showToast({title:'Please select university for student',icon:'error'})
          return
      }

       if(department.value.trim() === '')
      {
        showToast({title:'Please select department for student',icon:'error'})
          return
      }

       if(course.value.trim() === '')
      {
        showToast({title:'Please select course for student',icon:'error'})
          return
      }


      if(parentName.value.trim() === ''  || parentMobile.value.trim() === '' || parentRelation.value.trim() === '')
      {
        showToast({title:'Please enter parent/guardian details completely for student',icon:'error'})
          return
      }

       if(!isValidPhoneNumber(parentMobile.value))
      {
        showToast({title:'Invalid parent mobile number',icon:'error'})
          return
      }

      if(selectedBatch.value==='')
      {
         showToast({title:'Choose a batch for the student',icon:'error'})
          return
      }
    }



    if(!error)
    {
      if(isEditing.value)
      {
        const response = api.put(`${baseURL}/users/${route.params.id}`,{
          data : userData,
            address: [{
                street: street.value,
                city: city.value,
                country: country.value,
              }],
              parentDetails:[{
                name: parentName.value,
                relation: parentRelation.value,
                email: parentEmail.value,
                mobile: parentMobile.value
              }],
              isStaff: isStaff.value?1:0
        }).then((response)=>{
          const data = response.data
          if(data.status === 1)
          {
              showToast({title:data.message,icon:'success'})
              resetAll()
          }
          else{
              showToast({title:data.message,icon:'error'})
          }
        })
      }
      else{
        const response = api.post(`${baseURL}/users/create`,
        {
            data : userData,
            address: [{
                street: street.value,
                city: city.value,
                country: country.value,
            }],
            parentDetails:[{
              name: parentName.value,
              relation: parentRelation.value,
              email: parentEmail.value,
              mobile: parentMobile.value
            }],
            isStaff: isStaff.value?1:0
        }).then((response)=>{
          const data = response.data
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
    setTimeout(() => {
      getAllBatches()
    }, 500);
    getUserDetails();
})

 const  modeSelected = computed(()=>{
        
        if(route.name === '/home/staff-add' ||  route.name === '/home/staff-edit')
        {
            return 'STAFF'
        }
        else if(route.name === '/home/student-add' || route.name === '/home/student-edit')
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
    const minSize = 100 * 1024; // 10KB in bytes
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

watch(isStaff, (newVal) => {
    if(newVal)
    {
        role.value = 'STAFF'
        visaType.value = 'WORK'
    }
    else{
        role.value = 'STUDENT'
        visaType.value = 'EDUCATION'
    }
})

const checkUsername = debounce(async() =>{
    usernameExists.value = false
    if(username.value.trim()?.length < 3) return
    const req = {}
    req.username = username.value
    const response = await api.post('/users/checking',req).catch((err)=>{
        return err;
    })
    if(response.data.exists)
    {
        usernameExists.value = true
    }
    else{
        usernameExists.value = false
    }
    
},500)

  const getAllBatches = () =>{
        const url =  `${baseURL}/batches/all` 
        const response = api.get(url).then((response)=>{
            batches.value = response.data.data
        }).catch((error)=>{
            console.log(error);
            showToast({title:error,icon:'error'})
        })

    }


    const getUserDetails = () =>{
    if(route.name === '/home/student-edit' || route.name === '/home/student-edit')
    {
      const id = route.params.id
      const url =  `${baseURL}/users/getById`
      const req = {}
      req.id = id 
      const response = api.post(url,req).then((response)=>{
        let userData = response.data
        name.value = userData.name,
        gender.value = userData.gender,
        dob.value = userData.dob.split('T')[0],
        email.value = userData.email,
        username.value = userData.username,
        mobile.value = userData.mobile,
        password.value = userData.password,
        profile_picture.value = userData.profile_picture,
        course.value = userData.course,
        rollNo.value = userData.rollNo,
        university.value = userData.university,
        location.value = userData.location,
        street.value = userData.address[0].street
        city.value = userData.address[0].city
        country.value = userData.address[0].country

        parentName.value = userData.parentDetails[0].name
        parentRelation.value = userData.parentDetails[0].relation
        parentEmail.value = userData.parentDetails[0].email
        parentMobile.value = userData.parentDetails[0].mobile

        visaType.value = userData.visaType,
        visaStatus.value = userData.visaStatus,
        visaExpiryDate.value = userData.visaExpiryDate,
        insuranceStatus.value = userData.insuranceStatus,
        insuranceExpiryDate.value = userData.insuranceExpiryDate,
        passportNumber.value = userData.passportNumber,
        passportIssueDate.value = userData.passportIssueDate,
        passportExpiryDate.value = userData.passportExpiryDate,
        passportIssuingCountry.value = userData.passportIssuingCountry,
        department.value = userData.department,
        designation.value = userData.designation,
        
        uploadedImage.value = userData.profile_picture,
        selectedBatch.value = userData.batchId
      }).catch((error)=>{
          console.log(error);
          showToast({title:error,icon:'error'})
      })
      
    }
      
    }

    const isEditing = computed(()=>{
      return route.name === '/home/student-edit' || route.name === '/home/student-edit'
    })

    const cancelStudent = async() =>{
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
          router.go(0)
        }
    }

    const goBack = async() =>{
      const result = await Swal.fire({
            title: 'Confirm',
            text: 'Changes will not be saved',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Go back',
            cancelButtonText: 'Cancel',
        })

        if(result.isConfirmed)
        {
          router.go(-1)
        }
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
  margin-left: 2rem;
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