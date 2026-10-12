<template>
  <div class="login-root vh-100 container-fluid m-0 p-0 d-flex">

    <div class="login-brand col-7 d-none d-lg-flex flex-column justify-content-end p-5">
      <div class="brand-content">
        <div class="brand-logo mb-4">
          <i class="fa fa-graduation-cap fa-2x text-white"></i>
        </div>
        <h1 class="text-white fw-bold display-5 mb-2">Feemo</h1>
        <p class="text-white-50 fs-5 mb-0">Manage students, fees, and batches — all in one place.</p>
      </div>
    </div>

    <div class="col-12 col-lg-5 d-flex align-items-center justify-content-center bg-white px-4 px-md-5">
      <div class="login-form-wrap w-100">

        <div class="mb-5">
          <h2 class="fw-bold text-dark mb-1">Welcome back</h2>
          <p class="text-muted mb-0">Sign in to your account to continue</p>
        </div>

        <div class="mb-3">
          <label for="username" class="form-label fw-semibold text-dark small">Username</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-light border-end-0 text-muted">
              <i class="fa fa-user fa-sm"></i>
            </span>
            <input
              id="username"
              type="text"
              class="form-control bg-light border-start-0 ps-0"
              placeholder="Enter your username"
              v-model="username"
            />
          </div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-semibold text-dark small mb-0">Password</label>
            <!-- <a href="#" class="text-primary small text-decoration-none">Forgot password?</a> -->
          </div>
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-light border-end-0 text-muted">
              <i class="fa fa-lock fa-sm"></i>
            </span>
            <input
              id="password"
              type="password"
              class="form-control bg-light border-start-0 ps-0"
              placeholder="Enter your password"
              v-model="password"
            />
          </div>
        </div>

        <button class="btn btn-primary btn-lg w-100 fw-semibold" @click="validateLogin">
          <i class="fa fa-sign-in-alt me-2"></i>Sign In
        </button>

        <p class="text-center text-muted mt-4 small mb-0">
          Protected by enterprise-grade security
          <i class="fa fa-shield-alt ms-1 text-success"></i>
        </p>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { showToast } from '../utils/toastr'
import { useUserStore } from '../stores/userStore'
import { useRouter } from 'vue-router'
import api from '@/utils/axios'

const userStore = useUserStore()
const router = useRouter()
const baseURL = import.meta.env.VITE_API_BASE_URL

const username = ref('')
const password = ref('')

const validateLogin = () => {
  if (username.value === '') {
    showToast({ title: 'Username is required !', icon: 'error' })
    return
  }
  if (password.value === '') {
    showToast({ title: 'Password is required !', icon: 'error' })
    return
  }
  
  api.post(`${baseURL}/users/authenticate`, {
    username: username.value,
    password: password.value
  }).then((response) => {
    
    const data = response.data.data
    const token = response.data.token
    if (data === '') {
      showToast({ title: 'Invalid Credentials', icon: 'error' })
    } else {
      showToast({ title: 'Logging in', icon: 'info' })

      userStore.setLoggedIn(data)
      userStore.setToken(token)
      localStorage.setItem('token', token)


      if (userStore.allData.role === 'STUDENT') {
        router.push('/home/student/home')
      } else {
        router.push('/home')
      }
    }
  }).catch((error) => {
    console.log(error)
    showToast({ title: 'Something went wrong while trying to login', icon: 'error' })
  })
}
</script>

<style scoped lang="scss">
@use "sass:color";
@use "abstracts" as *;

.login-brand {
  position: relative;
  background-image: url('/image.webp'), $gradient-hero;
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;

  &::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, color.change($sky-950, $alpha: 0.9) 0%, color.change($sky-800, $alpha: 0.35) 100%);
  }
}

.brand-content {
  position: relative;
  z-index: 1;
}

.brand-logo {
  @include square(3.5rem);
  @include flex-center;
  border-radius: $radius;
  background: $gradient-brand;
  box-shadow: 0 8px 24px color.change($sky-400, $alpha: 0.4);
}

.login-form-wrap {
  max-width: 400px;
}

.input-group {
  border-radius: $radius-sm;
  transition: box-shadow $transition-base;

  &:focus-within {
    @include focus-ring;

    .input-group-text,
    .form-control {
      border-color: $sky-400;
    }
  }

  .form-control:focus {
    box-shadow: none;
  }
}

.btn-primary {
  padding-block: 0.75rem;

  @include below(md) {
    padding-block: 0.625rem;
  }

  letter-spacing: 0.3px;
  background-image: $gradient-brand;
  border: 0;
  box-shadow: 0 6px 18px color.change($sky-500, $alpha: 0.35);
}
</style>