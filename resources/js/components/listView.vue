<template>
<div class="row">
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="extraLargeModalLabel" aria-hidden="true" ref="myModalRef">
        <div class="modal-dialog modal-xl"> <!-- Extra-large size -->
            <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="extraLargeModalLabel">{{routes.name==='list' ? 'User view' : 'Edit / Update'}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-12" v-if="selectedUser!==null">
                        <div class="row mx-5">
                            <div class="px-3 col-3">Name <required/></div>
                            <div class="col-9 px-3">
                                <input type="text" name="" id="" v-model="name"  class="form-control my-1 w-50" :disabled="routes.name==='list'">
                            </div>

                            <div class="px-3 col-3">username <required/></div>
                            <div class="col-9 px-3">
                                <input type="text" name="" id=""  v-model="username"   class="form-control my-1 w-50" :disabled="routes.name==='list'">
                            </div>

                            <div class="px-3 col-3">Email <required/></div>
                            <div class="col-9 px-3">
                                <input type="text" name="" id=""  v-model="email"  class="form-control my-1 w-50" :disabled="routes.name==='list'">
                            </div>
                            
                            <div class="px-3 col-3">Mobile <required/></div>
                            <div class="col-9 px-3">
                                <input type="text" name="" id="" v-model="mobile"  class="form-control my-1 w-50" :disabled="routes.name==='list'">
                            </div>

                            <div class="px-3 col-3">DOB <required/></div>
                            <div class="col-9 px-3">
                                <input type="date" name="" id="" v-model="dob"  class="form-control my-1 w-50" :disabled="routes.name==='list'">
                            </div>
                            
                            <div class="px-3 col-3">Gender <required/></div>
                            <div class="col-9 px-3">
                                <select name="" id="" class="form-select my-1 w-50" v-model="gender" :disabled="routes.name==='list'">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="row my-3 p-3 font-sm" >
                            <div class="col-6 border p-4" v-for="addr,index in selectedAddress">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="" class="form-label">Type <required/></label>
                                        <select name="" id="" class="form-control" v-model="addr.officeType" :disabled="routes.name==='list'">
                                            <option value="home">Home</option>
                                            <option value="office">Office</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="" class="form-label">Street <required/></label>
                                        <input type="text" class="form-control" v-model="addr.street" :disabled="routes.name==='list'">
                                    </div>
                                    <div class="col-6">
                                        <label for="" class="form-label">Landmark</label>
                                        <input type="text" class="form-control" v-model="addr.landmark" :disabled="routes.name==='list'">
                                    </div>
                                    <div class="col-6">
                                        <label for="" class="form-label">City <required/></label>
                                        <input type="text" class="form-control" v-model="addr.city" :disabled="routes.name==='list'">
                                    </div>
                                    <div class="col-6">
                                        <label for="" class="form-label">State <required/></label>
                                        <input type="text" class="form-control" v-model="addr.state" :disabled="routes.name==='list'">
                                    </div>
                                    <div class="col-6">
                                        <label for="" class="form-label">Country <required/></label>
                                        <input type="text" class="form-control" v-model="addr.country" :disabled="routes.name==='list'">
                                    </div>
                                     <div class="col-6 align-content-end my-3">
                                        <label for="" class="form-label">&nbsp;</label>
                                        <button class="btn btn-primary btn-sm" @click="addAddress" :disabled="routes.name==='list' || selectedAddress.length >= 2">
                                            Add address
                                        </button>
                                        <button class="btn btn-primary btn-sm mx-2 " @click="removeAddress(index)" :disabled="routes.name==='list' || selectedAddress.length <= 1">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-success" @click="saveData" v-if="routes.name !=='list'">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-title p-3 bg-body-secondary">
                <nav aria-label="Page navigation example">
                    <ul class="pagination float-end">
                        <li class="page-item" v-for="link in users.links" v-if="!isFiltering">
                            <a class="page-link"  v-show="link.url !== null" :class="link.active ? link.url === null ? 'active disabled' : 'active':''" href="#" @click="getAllUsers(link.url)">{{ decodeHtmlEntities(link.label) }}</a>
                        </li>
                        <li class="page-item" v-for="link in users.links" v-else>
                            <a class="page-link"  v-show="link.url !== null" :class="link.active ? link.url === null ? 'active disabled' : 'active':''" href="#" @click="emitPagination(link.url)">{{ decodeHtmlEntities(link.label) }}</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="card-container py-3 px-3">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col px-3 p-3">#</th>
                        <th scope="col p-3">First Name</th>
                        <th scope="col p-3">Gender</th>
                        <th scope="col p-3">DOB</th>
                        <th scope="col p-3">Phone</th>
                        <th scope="col p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user,index in users.data">
                        <th scope="row px-3">{{ users.per_page * (users.current_page-1)+index+1 }}</th>
                        <td>{{ user.name }}</td>
                        <td>{{ user.gender }}</td>
                        <td>{{ user.dob }}</td>
                        <td>{{ user.mobile }}</td>
                        <td>
                            <button v-if="routes.name ==='update'" type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#myModal" @click="editUser(user)">Edit</button>
                            <button v-if="routes.name ==='delete'" type="button" class="btn btn-outline-info"  @click="deleteUser(user)">Delete</button>
                            <button v-if="routes.name ==='list'" type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#myModal" @click="editUser(user)">View</button>
                            <button v-if="routes.name ==='list'" type="button" class="btn btn-outline-dark mx-2" title="Click to reset password"  @click="resetPassword(user)">
                                <i class="fa fa-key" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                </table>
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

    import { Modal } from 'bootstrap'
    import required from './required.vue'

    const emit = defineEmits(['trigger']);


    const props = defineProps({        
        filteredData: {
            type: Object,
            default: {}
        },
        routes:{
            type:Object,
            default:{}
        }
        })

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
    
    

    const isFiltering = computed(()=>{
        return Object.keys(props.filteredData).length > 0 ? true : false
    })



    onMounted(()=>{

        console.log('mounted in list')


        if(Object.keys(props.filteredData).length > 0)
        {
            users.value = props.filteredData
        }
        else
        {
            getAllUsers()
        }

        modalInstance = new Modal(myModalRef.value);
        myModalRef.value.addEventListener('hidden.bs.modal', () => {
            getAllUsers(lastInPage.value)
            selectedUser.value = null
            selectedAddress.value = []
        })

    })



    const getAllUsers = (page = null) =>{
        const url = page ? page : `${baseURL}/users` 
        lastInPage.value = url
        const response = api.get(url).then((response)=>{
            users.value = response.data.data
        }).catch((error)=>{
            console.log(error);
            showToast({title:error,icon:'error'})
        })
    }

    const  decodeHtmlEntities = (html) =>  {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = html;
    return textarea.value;
    }

    const editUser = (user) =>{
        selectedUser.value = null
        selectedUser.value = user
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
            selectedAddress.value = JSON.parse(newVal.address)
            name.value = newVal.name
            username.value = newVal.username
            email.value = newVal.email
            dob.value = newVal.dob
            mobile.value = newVal.mobile
            gender.value = newVal.gender
        }
        else{
            selectedAddress.value = ''
            name.value = ''
            username.value = ''
            email.value = ''
            dob.value = ''
            mobile.value = ''
            gender.value = ''
        }
    })

    const saveData = () =>{

        var payload = {}
        payload.name = name.value
        payload.username = username.value
        payload.email = email.value
        payload.mobile = mobile.value
        payload.dob = dob.value
        payload.gender = gender.value

        var mappings = {}
        mappings.name = 'Name'
        mappings.gender = 'Gender'
        mappings.dob = 'Date of Birth'
        mappings.email = 'Email'
        mappings.username = 'Username'
        mappings.mobile = 'Mobile Number'

        var error = false

        for (const key in payload) {
            if (Object.prototype.hasOwnProperty.call(payload, key)) {
                
                const element = payload[key];
                if(element?.trim() === '')
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
                }
            }
        }

        if(selectedAddress.value.length == 0)
        {
            showToast({title:'Address cannot be empty ',icon:'error'})
            error = true   
        }
        else{

            var mappings = {}
            mappings.city = 'City'
            mappings.country = 'Country'
            mappings.landmark = 'Landmark'
            mappings.officeType = 'Office type'
            mappings.state = 'State'
            mappings.street = 'Street'


            for (let index = 0; index < selectedAddress.value.length; index++) {
                const element = selectedAddress.value[index];                
                for (const key in element) {
                if (Object.prototype.hasOwnProperty.call(element, key)) {
                    if(key != 'landmark')
                    {
                        if(element[key].trim() === '')
                        {
                            showToast({title:'ADDRESS 2:  '+(index+1)+' '+mappings[key]+' cannot be empty',icon:'error'})
                            error = true
                        }
                    }
                }}
                
            }
            
        }

        if(!error)
        {
            showToast({title:'Verified successfully',icon:'success'})
            payload.id = selectedUser.value.id

            const response = api.put(`${baseURL}/users/${payload.id}`,{
                data : payload,
                address : selectedAddress.value
            }).then((response)=>{

                if(response.data.status === 1)
                {
                    showToast({title:response.data.message, icon:'success'})
                    setTimeout(() => {
                        modalInstance.hide()
                        selectedUser.value = null
                    }, 1500);
                }
                else{
                    showToast({title:response.data.message, icon:'error'})
                }
                
            }).catch((error)=>{
                console.log(error);
                showToast({title:error, icon:'error'})
            })

        }
    }


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
</style>