<template>
<div class="mx-5 my-4">
    <h4 class="text-capitalize">{{ route.name }} user</h4>
    <br>
    <Userfilter  @handleFilter="filteredData" ref="filterRef"/>
    <list-view :filteredData="filteredValues" v-if="showListView" @trigger="handleApi" :routes="route"/>
</div>
</template>

<script setup>

import listView from './listView.vue';
import Userfilter from './filter.vue';
import { ref,watch,computed,onMounted } from 'vue';
import { useRoute } from 'vue-router';

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

watch(filteredValues,(newVal)=>{

    
    showListView.value = false
    setTimeout(() => {
        showListView.value = true
    }, 500);
})

onMounted(()=>{
    console.log('update changed');
    
})


        
</script>

<style scoped>

</style>