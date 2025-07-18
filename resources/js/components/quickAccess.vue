<template>
<div class="mx-4 my-4">
    <h4>Quick Access</h4>
    
    <div class="row my-5">
        <div class="col-4" v-for="menu in menus">
            <div class="card tile" @click="router.push(menu.path)">
                <div class="card-container p-5">
                    <i :class="menu.icon+' mx-2'"></i>
                        {{ menu.name }}
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>

import { ref } from 'vue';
import { useRouter } from 'vue-router'

const router = useRouter()

const menus = ref([{
    name: 'Create',
    path:'/home/add',
    icon:'fas fa-user-plus'
    },{
    name:'Update',
    path:'/home/update',
    icon:'fas fa-pen-to-square'
    },
    {
    name:'Delete',
    path:'/home/delete',
    icon:'fas fa-trash'
    }])
</script>

<style scoped>

.tile:hover{
    cursor: pointer;
    box-shadow: 0 0px 25px rgba(0, 0, 0, 0.1);
    background-color: #f5f5ff
}
</style>