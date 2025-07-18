<template>
<div class="align-content-center container-fluid vh-100 m-0 bg">
    <div class="align-content-end align-content-center float-end row vh-100 w-30">
        <!-- <div class="col-4">
            &nbsp;
        </div> -->
        <div class="col-12">
            <div class="card p-5 w-75">
                <div class="card-container">
                    <h5 class="text-center">Login</h5>
                    <input type="text" name="" id="" class="form-control" placeholder="Enter Username" v-model="username">
                    <input type="password" name="" id="" class="form-control my-3" placeholder="Enter Password" v-model="password">
                    <button class="btn btn-primary my-3" @click="validateLogin">Login</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>

import { ref,watch } from 'vue'
import { showToast } from '../utils/toastr'
import { useUserStore } from '../stores/userStore'
import { useRouter,useRoute } from 'vue-router'

import api from '@/utils/axios'
const userStore = useUserStore()
const router = useRouter()


const baseURL = import.meta.env.VITE_API_BASE_URL;




const username = ref('')
const password = ref('')
const logged = ref(false)



const validateLogin = () =>{

    if(username.value === '' || password.value === '')
    {
        showToast({title:'Enter valid data !',icon:'error'})
        return;
    }

    const response = api.post(`${baseURL}/users/authenticate`,
    {
        username:username.value,
        password:password.value
    }
    ).then((response)=>{
        const data = response.data.data      
        console.log(data);
          
        if(data === '')
        {
            showToast({title:'Invalid Credentials',icon:'error'})
        }
        else{
            logged.value = true
            showToast({title:'Loggin in',icon:'info'})
            userStore.setLoggedIn(data)
            router.push('/home')
        }
    }).catch((error)=>{
        console.log(error);
        showToast({title:'Something went wrong while trying to login',icon:'error'})
    })

}
        
</script>

<style scoped>
.w-30
{
   width: 35%;
}

.bg
{
    background-image: url('../../../public/bg.jpg');
    background-size: contain;
    background-repeat: no-repeat;
}

</style>