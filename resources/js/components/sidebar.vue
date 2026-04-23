<template>
    <div class="row">
            <div class="col-3 bg-body-secondary vh-100 d-flex">
                <div class="row align-content-center">
                    <div v-for="side in currentMenuItems" class="bg-dark-subtle col-12 px-5 py-3 menu-item" @click="router.push(side.route)" :class="route.name === side.route || side.active ? 'menu-item-active':''">
                        <i :class="side.icon+' mx-2'" aria-hidden="true"></i>{{ side.name }}
                    </div>
                    <div class="bg-dark-subtle col-12 px-5 py-3 menu-item-danger mt" @click="logout">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        Logout {{ route.path }}
                    </div>
                </div>
            </div>
            <div class="col-8">
                <router-view/>
            </div>
        </div>
</template>

<script setup>
import { ref,computed } from 'vue'
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


const sideMenuItems = ref(
    { "/students":[
        {name:'Home',icon:'fa fa-home',route:'home'},
        {name:'Add Student',icon:'fa fa-user-plus',route:'add'},
        {name:'Update Student',icon:'fa fa-pencil-square',route:'update'},
        {name:'Delete Student',icon:'fa fa-trash',route:'delete'},
        {name:'All Students',icon:'fa fa-list',route:'list'},
    ],
    "/home":[
        {name:'Home',icon:'fa fa-home',route:'home'},
        {name:'Batch Management',icon:'fa fa-users',route:'/home/batch-home'},
        {name:'Staff Management',icon:'fa fa-building',route:'/home/staff-home'},
        {name:'Student Management',icon:'fa fa-graduation-cap',route:'/home/student-home'}
    ],
    "/home/batch-home":[
        {name:'Home',icon:'fa fa-home',route:'/home'},
        {name:'Batch Management',icon:'fa fa-users',route:'/home/batch-home'},
        {name:'Staff Management',icon:'fa fa-building',route:'/home/staff-home'},
        {name:'Student Management',icon:'fa fa-graduation-cap',route:'/home/student-home'}
    ],
    "/home/staff-home":[
        {name:'Home',icon:'fa fa-home',route:'/home'},
        {name:'Batch Management',icon:'fa fa-users',route:'/home/batch-home'},
        {name:'Staff Management',icon:'fa fa-building',route:'/home/staff-home'},
        {name:'Student Management',icon:'fa fa-graduation-cap',route:'/home/student-home'}
    ],
     "/home/staff-add":[
        {name:'Home',icon:'fa fa-home',route:'/home'},
        {name:'Batch Management',icon:'fa fa-users',route:'/home/batch-home'},
        {name:'Staff Management',icon:'fa fa-building',route:'/home/staff-home',active:true},
        {name:'Student Management',icon:'fa fa-graduation-cap',route:'/home/student-home'}
    ],
    "/home/student-home":[
        {name:'Home',icon:'fa fa-home',route:'/home'},
        {name:'Batch Management',icon:'fa fa-users',route:'/home/batch-home'},
        {name:'Staff Management',icon:'fa fa-building',route:'/home/staff-home'},
        {name:'Student Management',icon:'fa fa-graduation-cap',route:'/home/student-home'}
    ]
    }
);

const currentMenuItems = computed(() => {
    const currentPath = route.path;
    const menuItemsForPath = Object.keys(sideMenuItems.value).find(item => item === currentPath);
    return menuItemsForPath ? sideMenuItems.value[menuItemsForPath] : [];
});
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