<template>
  <div class="row g-0">
    
    <div class="col-3 sidebar d-flex flex-column">
      
      <div class="sidebar-header px-4 py-4">
        <h5 class="fw-bold m-0">FeeMo</h5>
        <small class="text-white-50">Welcome!</small>
      </div>

      <div class="flex-grow-1 px-3 overflow-y-auto" v-if="!isLoading">

        <div v-for="item in menuForSelectedRole" :key="item.name" class="menu-wrapper">

          <div
            class="menu-item d-flex align-items-center justify-content-between"
            :class="{ active: isActive(item) }"
            @click="handleMenuClick(item)"
          >
            <div class="d-flex align-items-center">
              <i :class="item.icon + ' me-3'"></i>
              <span>{{ item.name }}</span>
            </div>
          </div>

          <div v-if="item.children" class="submenu">
            <router-link
              v-for="child in item.children"
              :key="child?.name"
              :to="child.route"
              class="menu-item submenu-item d-flex align-items-center text-decoration-none"
              :class="{ 'child-active': route.path === child.route }"
            >
              <i :class="child.icon + ' me-3'"></i>
              <span>{{ child?.name }}</span>
            </router-link>
          </div>

        </div>

      </div>
      <div class="flex-grow-1 px-3 overflow-y-auto" v-else>
        <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <div class="px-3 pb-4">
        <div class="menu-item danger d-flex align-items-center" @click="logout">
          <i class="fa fa-sign-out me-3"></i>
          Logout
        </div>
      </div>

    </div>

    <div class="col-9 main-content p-4">
      <router-view />
    </div>

  </div>
</template>

<script setup>
import {ref, onMounted, computed, watch} from 'vue';
import { useRouter, useRoute } from 'vue-router'
import Swal from 'sweetalert2'
import { useUserStore } from '@/stores/userStore'

const isLoading = ref(false)
const role = ref('')

const router = useRouter()
const route = useRoute()
const userStore = useUserStore()
const menuForSelectedRole = computed(()=>{
  console.log(role);
  
  if(role.value==='STUDENT')
  {
    return studentMenuItems
  }
  else{
    return staffMenuItems
  }
})

const staffMenuItems = [
  { name: 'Home', icon: 'fa fa-home', route: '/home' },

  { 
    name: 'Batch Management', 
    icon: 'fa fa-users', 
    route: '/home/batch' 
  },

  {
    name: 'Staff Management',
    icon: 'fa fa-building',
    route: '/home/staff',
    children: [
      { 
        name: 'Staff List', 
        icon: 'fa fa-list', 
        route: '/home/staff' 
      },
      { 
        name: 'Add Staff', 
        icon: 'fa fa-plus', 
        route: '/home/staff/add' 
      }
    ]
  },

  {
    name: 'Student Management',
    icon: 'fa fa-graduation-cap',
    route: '/home/student',
    children: [
      { 
        name: 'Student List', 
        icon: 'fa fa-list', 
        route: '/home/student' 
      },
      { 
        name: 'Add Student', 
        icon: 'fa fa-plus', 
        route: '/home/student/add' 
      }
    ]
  },

  { 
    name: 'Fee Management', 
    icon: 'fa fa-money-bill-trend-up', 
    route: '/home/fee' ,
    children:[
      {
        name: 'Fee Home',
        icon: 'fa fa-home',
        route:  '/home/fee'
      },
      {
        name: 'Pay Fee',
        icon: 'fa fa-money-bill-alt',
        route:  '/home/fee/pay'
      }
    ]
  }
]

const studentMenuItems = [
  { 
    name: 'Home', 
    icon: 'fa fa-home', 
    route: '/home/student/home'   // dashboard (quickAccess)
  },

  { 
    name: 'My Fees', 
    icon: 'fa fa-money-bill-transfer', 
    route: '/home/student/fee' 
  },

  { 
    name: 'My Documents', 
    icon: 'fa fa-file-alt', 
    route: '/home/student/document'
  }
]

const handleMenuClick = (item) => {
  if (!item.children) {
    router.push(item.route)
  }
}

const isActive = (item) => {
  if (item.route === '/home') return route.path === '/home'
  if (item.children) {
    return item.children.some(child => route.path === child.route)
  }
  return route.path.startsWith(item.route)
}

const logout = async () => {
  const result = await Swal.fire({
    title: 'Confirmation',
    text: 'Do you want to logout?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#6366f1',
    confirmButtonText: 'Logout',
    cancelButtonText: "No, I'm Staying",
  })

  if (result.isConfirmed) {
    userStore.logout()
    router.push('/')
  }
}

onMounted(()=>{
  isLoading.value = true;
  setMenuRules();
})





const setMenuRules = ()=>{
  role.value = userStore.allData.role
  isLoading.value = false
}
</script>

<style scoped>
.sidebar {
  height: 100vh;
  background: #0f172a;
  color: #cbd5f5;
  position: sticky;
  top: 0;
}

.sidebar-header {
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.menu-item {
  padding: 12px 16px;
  margin: 4px 0;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;
  color: #cbd5f5;
}

.menu-item i {
  width: 20px;
  text-align: center;
}

.menu-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #fff;
}

/* Parent Active State */
.menu-item.active {
  background: rgba(99, 102, 241, 0.15);
  color: #818cf8;
}

/* Submenu Dropdown Logic */
.menu-wrapper {
  margin-bottom: 4px;
}

.submenu {
  display: none; /* Hidden by default */
  flex-direction: column;
  padding-left: 1.5rem; /* Indent sub-items */
  margin-bottom: 10px;
}

/* Show submenu when hovering the wrapper */
.menu-wrapper:hover .submenu {
  display: flex;
}

.submenu-item {
  font-size: 0.8rem;
  padding: 8px 16px;
  color: #94a3b8;
}

.submenu-item:hover {
  color: #fff;
}

/* Highlight specific child route */
.child-active {
  color: #fff !important;
  font-weight: 600;
}

.menu-item.danger:hover {
  background: rgba(239, 68, 68, 0.15);
  color: #ef4444;
}

.main-content {
  background: #f8fafc;
  min-height: 100vh;
}

/* For better scrolling on small screens */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
}
</style>