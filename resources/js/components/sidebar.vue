<template>
    <div class="row">
            <div class="col-3 bg-body-secondary vh-100">
                <div class="row">
                    <div class="bg-dark-subtle col-12 px-5 py-3 mt menu-item" @click="router.push('/home')" :class="route.name ==='default' ? 'menu-item-active':''">
                        <i class="fa fa-home mx-2" aria-hidden="true"></i>Home
                    </div>
                    <div class="bg-dark-subtle col-12 px-5 py-3 menu-item" @click="router.push('/home/add')" :class="route.name ==='add' ? 'menu-item-active':''">
                        <i class="fa fa-user-plus mx-2" aria-hidden="true"></i>
                        Add User
                    </div>
                    <div class="bg-dark-subtle col-12 px-5 py-3 menu-item" @click="router.push('/home/update')" :class="route.name ==='update' ? 'menu-item-active':''">
                        <i class="fa fa-pencil-square mx-2" aria-hidden="true"></i>
                        Update User
                    </div>
                    <div class="bg-dark-subtle col-12 px-5 py-3 menu-item" @click="router.push('/home/delete')" :class="route.name ==='delete' ? 'menu-item-active':''">
                        <i class="fa fa-trash mx-2" aria-hidden="true"></i>
                        Delete User
                    </div>
                    <div class="bg-dark-subtle col-12 px-5 py-3 menu-item" @click="router.push('/home/list')" :class="route.name ==='list' ? 'menu-item-active':''">
                        <i class="fa fa-list mx-2" aria-hidden="true"></i>
                        All Users
                    </div>
                    <div class="bg-dark-subtle col-12 px-5 py-3 menu-item-danger mt" @click="logout">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        Logout
                    </div>
                </div>
            </div>
            <div class="col-8">
                <router-view/>
            </div>
        </div>
</template>

<script setup>

import { useRouter,RouterView,useRoute } from 'vue-router'
import Swal from 'sweetalert2'
import {useUserStore} from '../stores/userStore'


const router = useRouter()
const route = useRoute()
        
const userStore = useUserStore()

const logout = async() =>{
            const result = await Swal.fire({
            title: 'Confirmation',
            text: 'Do you want to logout',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Logout',
            cancelButtonText: 'No, I\'m Staying',
        })

        if(result.isConfirmed)
        {
            userStore.logout()
            router.push('/')
            
        }
}
</script>

<style scoped>
.mt
{
    margin-top: 25%;
}

.menu-item{
        box-shadow: 0 0px 10px rgba(0, 0, 0, 0.1); /* X Y blur color */
}

.menu-item-active{
    /* border: 1px solid black; */
    cursor: pointer;
    box-shadow: 0 0px 25px rgba(0, 0, 0, 0.1);
    background-color: #5353f3 !important; 
    color: white;

}
.menu-item:hover{
    /* border: 1px solid black; */
    cursor: pointer;
    box-shadow: 0 0px 25px rgba(0, 0, 0, 0.1);
    background-color: #5d5dfc !important; 
    color: white;

}

.menu-item-danger:hover{
    /* border: 1px solid black; */
    cursor: pointer;
    box-shadow: 0 0px 25px rgba(0, 0, 0, 0.1);
    background-color: #ec4a60 !important; 
    color: white;

}
</style>