<template>
<div>
    <div class="row">
        <div class="col-12">
            <nav class="navbar navbar-light bg-light">
                <span class="navbar-brand mb-0 h1 p-3 h-100">User Management</span>
                <div class="dropdown">
                    <button class="btn mx-5 dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ userStore.name }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="#" @click="logout">Logout</a></li>
                    </ul>
                </div>
            </nav>
        </div>
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