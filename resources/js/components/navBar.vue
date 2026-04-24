<template>
  <div class="topbar d-flex align-items-center justify-content-between px-4">
    
    <!-- Left: Welcome -->
    <div>
      <h6 class="mb-0 fw-semibold">
        Welcome back,
        <span class="username">
          {{ formattedName }}
        </span>
      </h6>
      <small class="text-muted">Here’s what’s happening today</small>
    </div>

    <!-- Right: Profile -->
    <div class="dropdown">
      <div 
        class="profile d-flex align-items-center"
        data-bs-toggle="dropdown"
      >
        <div class="avatar">
          {{ userStore.name.charAt(0).toUpperCase() }}
        </div>
        <span class="ms-2 d-none d-md-block">{{ userStore.name }}</span>
        <i class="fa fa-chevron-down ms-2 small"></i>
      </div>

      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li class="dropdown-header text-muted">
          Signed in as<br>
          <strong>{{ userStore.name }}</strong>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item text-danger" href="#" @click="logout">
            <i class="fa fa-sign-out me-2"></i> Logout
          </a>
        </li>
      </ul>
    </div>

  </div>
</template>

<script setup>
import {useUserStore} from '../stores/userStore'
import { useRouter } from 'vue-router'

const userStore = useUserStore()
const router = useRouter()

const logout = () => {
    userStore.logout()
    router.push('/')
}
</script>

<style scoped>

</style>