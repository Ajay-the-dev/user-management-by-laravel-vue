<template>
    <div class="row m-3 p-5 border my-2">
        <div class="col-2">
            <label for="">Choose Filter</label>
        </div>
        <div class="col-3">
            <select name="" id="" class="form-select" v-model="selectedFilter">
                <option value="id">Id</option>
                <option value="name">Name</option>
                <option value="gender">Gender</option>
            </select>
        </div>
        <div class="col-2 text-end">
            Value
        </div>
        <div class="col-3"  v-if="selectedFilter=='gender'" >
            <select name="" id="" class="form-select" v-model="enteredValue">
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        <div class="col-3"  v-else>
            <input type="text" name="" id="" class="form-control" v-model="enteredValue">
        </div>
        <div class="col-2">
            <button class="btn btn-outline-primary" @click="getUsersByParams()">Find</button>
            <button class="mx-2 btn btn-outline-primary" @click="clearAll()">Clear</button>
        </div>
    </div>
</template>

<script setup>
    import { ref,defineEmits,watch } from 'vue'
    import { showToast } from '../utils/toastr'
    import api from '@/utils/axios'

    const baseURL = import.meta.env.VITE_API_BASE_URL;


    const selectedFilter = ref('id')
    const enteredValue = ref('')
    const emits = defineEmits(['handleFilter'])


    const getUsersByParams = (path) =>{

        if(enteredValue.value.trim() === '' || selectedFilter.value.trim() === '')
        {
            showToast({title:'Entered value is empty',icon:'error'})
            return
        }

        const url = path ? path : `${baseURL}/users/byParams`

        const response = api.post(url,
            {
                param : selectedFilter.value,
                value : enteredValue.value
            }
        ).then((response) =>{
            const data = response.data
            emits('handleFilter', data.data)
                        
        }).catch((error)=>{
            console.error(error);
            
        })
        
    }

    const clearAll = () =>{
        selectedFilter.value = 'id',
        enteredValue.value = ''
        emits('handleFilter', {})
    }

    watch(selectedFilter,(newVal)=>{
        enteredValue.value = ''
    })

    defineExpose({ getUsersByParams });

        
</script>

<style scoped>

</style>