<template>
<div class="mx-5 my-4">
    <h4>Add Users</h4>
    <br>
    <div class="row">
        <div class="col-4">
                <label for="" class="form-label">First Name <required/></label>
                <input type="text" class="form-control" required v-model="name">
        </div>
        <div class="col-4">
                <label for="" class="form-label">Last Name <required/></label>
                <input type="text" class="form-control" required v-model="lname">
        </div>
    </div>
    <div class="row my-4">
        <div class="col-4">
                <label for="" class="form-label">Gender <required/></label>
                <select name="" id="" class="form-select" v-model="gender">
                    <option value="">choose</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
        </div>
        <div class="col-4">
                <label for="" class="form-label">DOB <required/></label>
                <input type="date" class="form-control" required v-model="dob">
        </div>
    </div>
    <div class="row my-4">
        <div class="col-4">
                <label for="" class="form-label">Email <required/></label>
                <input type="email" class="form-control" required v-model="email">

                
        </div>
        <div class="col-4">
                <label for="" class="form-label">Username <required/></label>
                <input type="text" class="form-control" required v-model="username">
        </div>
    </div>
    <div class="row my-4">
        <div class="col-4">
                <label for="" class="form-label">Mobile <required/></label>
                <input type="phone" class="form-control" required v-model="mobile">

                
        </div>
        <div class="col-4">
                <label for="" class="form-label">Password <required/></label>
                <input type="email" class="form-control" required v-model="password">
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
    <div class="row my-4 font-sm" v-if="showAddress">
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
    <div class="row my-4 font-sm" v-if="showAddress">
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
</template>

<script setup>
import { ref,onMounted } from 'vue'
import { showToast } from '../utils/toastr'
import api from '@/utils/axios'
import required from './required.vue';
const baseURL = import.meta.env.VITE_API_BASE_URL;




const addresses = ref([])
const officeType = ref('')
const street = ref('')
const landmark = ref('')
const city = ref('')
const state = ref('')
const country = ref('')
const showAddress = ref(false)


const name = ref('')
const lname = ref('')
const gender = ref('')
const dob = ref('')
const email = ref('')
const username = ref('')
const mobile = ref('')
const password = ref('')


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
lname.value =  ''
gender.value =  ''
dob.value =  ''
email.value =  ''
username.value =  ''
mobile.value =  ''
password.value =  ''

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
    userData.lname = lname.value
    userData.gender = gender.value
    userData.dob = dob.value
    userData.email = email.value
    userData.username = username.value
    userData.mobile = mobile.value
    userData.password = password.value
    userData.name = name.value+' '+lname.value

    var mappings = {}
    mappings.name = 'First Name'
    mappings.lname = 'Last Name'
    mappings.gender = 'Gender'
    mappings.dob = 'Date of Birth'
    mappings.email = 'Email'
    mappings.username = 'Username'
    mappings.mobile = 'Mobile Number'
    mappings.password = 'Password'

    var error = false

    for (const key in userData) {
        if (Object.prototype.hasOwnProperty.call(userData, key)) {
            const element = userData[key];
            if(element.trim() === '')
            {
                var entry = mappings[key]
                showToast({title:entry+' is empty',icon:'error'})
                error = true
                break
            }
            else{
                if(key == 'name')
                {
                    const isValidName = nameRegex.test(element)
                    if (!isValidName) {
                        showToast({title:'Invalid first name',icon:'error'})
                        error = true
                        break
                    }
                }

                if(key == 'lname')
                {
                    const isValidName = nameRegex.test(element)
                    if (!isValidName) {
                        showToast({title:'Invalid last name',icon:'error'})
                        error = true
                        break
                    }
                }

                if(key == 'dob')
                {
                    const valid = isValidDOB(element)
                    if(!valid)
                    {
                        showToast({title:'Invalid date of birth',icon:'error'})
                        error = true
                        break
                    }
                }

                if(key == 'email')
                {
                    const isValid = emailRegex.test(element)
                    if (!isValid) {
                        showToast({title:'Invalid email',icon:'error'})
                        error = true
                        break
                    }
                }

                if(key == 'username')
                {
                    const isValid = usernameRegex.test(element)
                    if (!isValid) {
                        showToast({title:'Invalid username ',icon:'error'})
                        error = true
                        break
                    }
                }

                if(key == 'mobile')
                {
                    const isValid = mobileRegex.test(element)
                    if (!isValid) {
                        showToast({title:'Invalid mobile number ',icon:'error'})
                        error = true
                        break
                    }
                }

                if(key == 'password')
                {
                    const isValid = passwordRegex.test(element)
                    if (!isValid) {
                        showToast({title:'Invalid password (min 8 charecters) ',icon:'error'})
                        error = true
                        break
                    }
                }
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

</script>

<style scoped>
.font-sm{
    font-size: small;
}
</style>