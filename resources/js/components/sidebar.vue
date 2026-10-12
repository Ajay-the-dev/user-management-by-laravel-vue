<template>
  <div class="app-shell" :class="{ 'is-drawer-open': isDrawerOpen }">

    <!-- Mobile drawer backdrop -->
    <div class="app-backdrop" @click="isDrawerOpen = false"></div>

    <aside class="app-sidebar">

      <div class="app-sidebar__header px-4 py-4">
        <div class="app-sidebar__brand">
          <span class="app-sidebar__logo">
            <i class="fa fa-graduation-cap"></i>
          </span>
          <h5 class="app-sidebar__title fw-bold m-0 text-white">FeeMo</h5>
          <button type="button" class="btn-close btn-close-white ms-auto d-md-none" aria-label="Close menu" @click="isDrawerOpen = false"></button>
        </div>
        <small class="app-sidebar__tagline mt-2 text-white-50 text-capitalize">Welcome {{userStore.name}}!</small>
      </div>

      <nav class="app-sidebar__nav flex-grow-1 px-3 py-2 overflow-y-auto" v-if="!isLoading">

        <div
          v-for="item in menuForSelectedRole"
          :key="item.name"
          class="menu-wrapper"
          :class="{ 'is-open': item.children && (isActive(item) || expandedItem === item.name) }"
        >

          <div
            class="menu-item justify-content-between"
            :class="{ active: isActive(item) }"
            :title="item.name"
            @click="handleMenuClick(item)"
          >
            <div class="d-flex align-items-center">
              <i :class="item.icon + ' me-3'"></i>
              <span class="menu-label">{{ item.name }}</span>
            </div>
          </div>

          <div v-if="item.children" class="submenu">
            <router-link
              v-for="child in item.children"
              :key="child?.name"
              :to="child.route"
              class="menu-item submenu-item text-decoration-none"
              :class="{ 'child-active': route.path === child.route }"
              :title="child?.name"
            >
              <i :class="child.icon + ' me-3'"></i>
              <span class="menu-label">{{ child?.name }}</span>
            </router-link>
          </div>

        </div>

      </nav>
      <div class="flex-grow-1 px-3 py-4 text-center" v-else>
        <div class="spinner-border text-info" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <div class="px-3 pb-4">
        <div class="menu-item danger" title="Logout" @click="logout">
          <i class="fa fa-sign-out me-3"></i>
          <span class="menu-label">Logout</span>
        </div>
      </div>

    </aside>

    <main class="app-main">
      <!-- Mobile top bar -->
      <header class="app-topbar d-md-none">
        <button type="button" class="app-topbar__toggle btn" aria-label="Open menu" @click="isDrawerOpen = true">
          <i class="fa fa-bars"></i>
        </button>
        <span class="app-sidebar__logo">
          <i class="fa fa-graduation-cap"></i>
        </span>
        <span class="fw-bold">FeeMo</span>
        <span class="avatar avatar--brand ms-auto">{{ userStore.name?.charAt(0)?.toUpperCase() }}</span>
      </header>

      <router-view />
    </main>

  </div>
</template>

<script setup>
import {ref, onMounted, onBeforeUnmount, computed, watch} from 'vue';
import { useRouter, useRoute } from 'vue-router'
import Swal from 'sweetalert2'
import { useUserStore } from '@/stores/userStore'

const isLoading = ref(false)
const role = ref('')
const isDrawerOpen = ref(false)
const expandedItem = ref(null)

const router = useRouter()
const route = useRoute()
const userStore = useUserStore()
const menuForSelectedRole = computed(()=>{  
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
        name: 'Pay Fee',
        icon: 'fa fa-money-bill-alt',
        route:  '/home/fee/pay'
      },
      {
        name: 'Fee Header',
        icon: 'fa fa-money-check-dollar',
        route:  '/home/fee/manage'
      }
    ]
  },
  { 
    name: 'Notice Board', 
    icon: 'fa fa-comment-alt', 
    route: '/home/notice' ,
    children:[
      {
        name: 'Create',
        icon: 'fa fa-comment-medical',
        route:  '/home/notice-create'
      },
      {
        name: 'Old notices',
        icon: 'fa fa-list-dots',
        route:  '/home/notice-list'
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
    name: 'My profile', 
    icon: 'fa fa-address-card', 
    route: '/home/student/profile' 
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
    return
  }
  // Touch screens have no hover, so a tap toggles the submenu
  expandedItem.value = expandedItem.value === item.name ? null : item.name
}

// Close the mobile drawer after navigating
watch(() => route.fullPath, () => {
  isDrawerOpen.value = false
})

watch(isDrawerOpen, (open) => {
  document.body.classList.toggle('drawer-open', open)
})

onBeforeUnmount(() => {
  document.body.classList.remove('drawer-open')
})

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
