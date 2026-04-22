<template>
<div class="mx-5 my-4">
    <h4 class="text-capitalize">Batch List</h4>
    <br>
    <list-view :filteredData="filteredValues" v-if="showListView" :routes="route"/>
</div>
</template>

<script setup>

import listView from './batchListView.vue';
import Userfilter from './filter.vue';
import { ref,watch,computed,onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/utils/axios'

const baseURL = import.meta.env.VITE_API_BASE_URL;



const route = useRoute()

const filteredValues = ref({})
const showListView = ref(true)
const filterRef = ref();


const filteredData = (payload) => {
    filteredValues.value = payload
}

const handleApi = (url) =>{
    
      filterRef.value?.getUsersByParams(url);
}


const getAllBatches = () =>{
    const url = `${baseURL}/batches/`
    const response = api.get(url).then((response)=>{
        const data = response.data        
        filteredValues.value = data.data
    }).catch((error)=>{
        console.error(error);
    })
}

watch(filteredValues,(newVal)=>{

    
    showListView.value = false
    setTimeout(() => {
        showListView.value = true
    }, 500);
})

onMounted(()=>{
   getAllBatches()
})


        
</script>

<style scoped>

</style>