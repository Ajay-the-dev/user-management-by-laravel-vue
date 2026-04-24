<template>
  <div class="row g-0">
    
    <!-- Sidebar -->
    <div class="col-3 sidebar d-flex flex-column">
      
      <!-- Logo / Header -->
      <div class="sidebar-header px-4 py-4">
        <h5 class="fw-bold m-0">FeeMo</h5>
        <small class="text-white">Welcome !</small>
      </div>

      <!-- Menu -->
      <div class="flex-grow-1 px-3">
        <div 
          v-for="side in currentMenuItems" 
          :key="side.name"
          class="menu-item d-flex align-items-center"
          @click="router.push(side.route)"
          :class="route.name === side.route || side.active ? 'active' : ''"
        >
          <i :class="side.icon + ' me-3'"></i>
          <span>{{ side.name }}</span>
        </div>
      </div>

      <!-- Logout -->
      <div class="px-3 pb-4">
        <div class="menu-item danger d-flex align-items-center" @click="logout">
          <i class="fa fa-sign-out me-3"></i>
          Logout
        </div>
      </div>

    </div>

    <!-- Main Content -->
    <div class="col-9 main-content p-4">
      <router-view />
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
    ],
    "/home/student-add":[
        {name:'Home',icon:'fa fa-home',route:'/home'},
        {name:'Batch Management',icon:'fa fa-users',route:'/home/batch-home'},
        {name:'Staff Management',icon:'fa fa-building',route:'/home/staff-home'},
        {name:'Student Management',icon:'fa fa-graduation-cap',route:'/home/student-home',active:true}
    ],
    "/home/student-edit":[
        {name:'Home',icon:'fa fa-home',route:'/home'},
        {name:'Batch Management',icon:'fa fa-users',route:'/home/batch-home'},
        {name:'Staff Management',icon:'fa fa-building',route:'/home/staff-home'},
        {name:'Student Management',icon:'fa fa-graduation-cap',route:'/home/student-home',active:true}
    ]
    }
);

const currentMenuItems = computed(() => {
    const currentPath = route.name;    
    const menuItemsForPath = Object.keys(sideMenuItems.value).find(item => item === currentPath);
    return menuItemsForPath ? sideMenuItems.value[menuItemsForPath] : [];
});
</script>

<style scoped>
.sidebar {
  height: 100vh;
  background: #0f172a; /* dark navy */
  color: #cbd5f5;
}

.sidebar-header {
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.menu-item {
  padding: 12px 16px;
  margin: 6px 0;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.25s ease;
  font-weight: 500;
}

.menu-item i {
  width: 20px;
  text-align: center;
}

/* Hover */
.menu-item:hover {
  background: rgba(99, 102, 241, 0.15);
  color: #fff;
}

/* Active */
.menu-item.active {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
}

/* Logout */
.menu-item.danger:hover {
  background: rgba(239, 68, 68, 0.2);
  color: #ef4444;
}

/* Main content */
.main-content {
  background: #f8fafc;
  min-height: 100vh;
}
</style>