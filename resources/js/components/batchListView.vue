<template>
<div class="row">
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="extraLargeModalLabel" aria-hidden="true" ref="myModalRef">
        <div class="modal-dialog modal-xl"> <!-- Extra-large size -->
            <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" v-if="showBatch" id="extraLargeModalLabel">Batch Details</h5>
                <h5 class="modal-title" v-else id="extraLargeModalLabel">{{isEditMode ? 'Edit Batch' : 'Create New Batch'}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            
            <div class="modal-body">
                <div class="row">
                    <div class="col-12" v-if="selectedBatch!==null">
                        <div class="row mx-5">
                            <div class="px-3 col-3">Batch Name <required/></div>
                            <div class="col-9 px-3">
                                <input type="text" v-model="name" class="form-control my-1" :disabled="showBatch">
                            </div>

                            <div class="px-3 col-3">Description</div>
                            <div class="col-9 px-3">
                                <textarea v-model="description" class="form-control my-1" rows="3" :disabled="showBatch"></textarea>
                            </div>

                            <div class="px-3 col-3" v-show="selectedBatch.is_passout===0">Current Semester <required/></div>
                            <div class="col-9 px-3" v-show="selectedBatch.is_passout===0">
                                <input type="number" v-model="semester" min="1" max="10" class="form-control my-1" :disabled="showBatch">
                            </div>

                            <div class="px-3 col-3">Start Date </div>
                            <div class="col-9 px-3">
                                <input type="date" v-model="startDate" class="form-control my-1" :disabled="showBatch">
                            </div>
                            
                            <div class="px-3 col-3">End Date </div>
                            <div class="col-9 px-3">
                                <input type="date" v-model="endDate" class="form-control my-1" :disabled="showBatch">
                            </div>
                            <div class="px-3 col-3">Status <required/></div>
                            <div class="col-9 px-3">
                                <select v-model="status" class="form-select my-1" :disabled="showBatch">
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button"  v-if="!showBatch" class="btn btn-success" @click="saveData" >Save</button>
                <button type="button"  v-if="!showBatch" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            
            </div>
        </div>
    </div>

    <div class="mx-4 col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">Batches</h5>
                    <div class="d-flex">
                        <input type="text" class="form-control w-auto mx-3" placeholder="Search batch here ..." v-model="searchQuery"
                        @change="getBatchesByName">
                        <button class="btn btn-primary btn-sm" @click="showAddModal">
                            <i class="fas fa-plus me-2"></i>New Batch
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="10%" class="fw-bold text-muted">#</th>
                                <th scope="col" width="40%" class="fw-bold text-muted">Batch Name</th>
                                <th scope="col" width="25%" class="fw-bold text-muted">Current Semester</th>
                                <th scope="col" width="25%" class="fw-bold text-muted text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(batche, index) in batches.data" :key="batche.id" class="align-middle border-bottom">
                                <td class="fw-bold">{{ batches.per_page * (batches.current_page - 1) + index + 1 }}</td>
                                <td class="fw-500">{{ batche.name }}</td>
                                <td>
                                    <span v-if="batche.is_passout === 1" class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>PASSED OUT
                                    </span>
                                    <span v-else class="badge bg-primary">
                                        <i class="fas fa-graduation-cap me-1"></i>SEMESTER {{ batche.semester }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary" @click="editBatch(batche, true)" title="View"
                                            data-bs-toggle="modal" data-bs-target="#myModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" @click="editBatch(batche)" title="Edit"
                                            data-bs-toggle="modal" data-bs-target="#myModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" @click="deletebatche(batche)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light p-3" v-if="batches.links">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item" v-for="link in batches.links" :key="link.label" :class="link.active ? 'active' : ''">
                            <a class="page-link" href="#" @click.prevent="link.url && getAllBatches(link.url)" :disabled="!link.url">
                                {{ decodeHtmlEntities(link.label) }}
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- <div class="card">
            <div class="card-title p-3 bg-body-secondary">
                <nav aria-label="Page navigation example class-end" class="text-end flex">
                    <input type="text" name="" id="" placeholder="Search batch here ..." class="form-control d-inline-block w-auto mx-2" @input="handleApi($event.target.value)">
                    <button class="btn btn-primary fw-light align-top" @click="showAddModal">
                       <i class="fas fa-plus"></i> New Batch
                    </button>
                </nav>
            </div>
            <div class="card-container py-3 px-3">
                
            </div>
        </div> -->
    </div>
</div>
</template>

<script setup>
    import { ref,onMounted,watch,computed} from 'vue'
    import api from '@/utils/axios'
    import { showToast } from '../utils/toastr'
    import Swal from 'sweetalert2'
    import { Modal } from 'bootstrap'
    import required from './required.vue'

    import debounce from 'lodash/debounce';

    const emit = defineEmits(['trigger']);


    const props = defineProps({        
        filteredData: {
            type: Object,
            default: {}
        },
        routes:{
            type:Object,
            default:{}
        }
        })

    const batches = ref([])

    const baseURL = import.meta.env.VITE_API_BASE_URL;

    const selectedBatch = ref(null)

    const lastInPage = ref('')

    let modalInstance = null;
    const myModalRef = ref(null);

    const name = ref('')
    const description = ref('')
    const startDate = ref('')
    const endDate = ref('')
    const status = ref('')
    const semester = ref('')
    const isEditMode = ref(false)
    const showBatch = ref(false)
    
    const searchQuery = ref('')

    const isFiltering = computed(()=>{
        return Object.keys(props.filteredData).length > 0 ? true : false
    })



    onMounted(()=>{

        console.log('mounted in list')


        if(Object.keys(props.filteredData).length > 0)
        {
            batches.value = props.filteredData
        }
        else
        {
            getAllBatches()
        }

        modalInstance = new Modal(myModalRef.value);
        myModalRef.value.addEventListener('hidden.bs.modal', () => {
            getAllBatches(lastInPage.value)
            selectedBatch.value = null
            showBatch.value = false
        })

    })



    const getAllBatches = (page = null) =>{
        const url = page ? page : `${baseURL}/batches` 
        lastInPage.value = url
        const response = api.get(url).then((response)=>{
            batches.value = response.data.data
        }).catch((error)=>{
            console.log(error);
            showToast({title:error,icon:'error'})
        })
    }

    const showAddModal = () => {
        isEditMode.value = false
        selectedBatch.value = { name: '', description: '', start_date: '', end_date: '', status: '' }
        semester.value = 1
        showBatch.value = false
        modalInstance.show()
    }

    const  decodeHtmlEntities = (html) =>  {
    const textarea = document.createElement('textarea');
    textarea.innerHTML = html;
    return textarea.value;
    }

    const editBatch = (batche,viewOnly=false) =>{
        isEditMode.value = true
        selectedBatch.value = null
        selectedBatch.value = batche
        showBatch.value = viewOnly
    }

 
    watch(selectedBatch,(newVal)=>{
        
        if(newVal)
        {
            name.value = newVal.name
            description.value = newVal.description || ''
            startDate.value = newVal.start_date || ''
            endDate.value = newVal.end_date || ''
            status.value = newVal.status || ''
            semester.value = newVal.semester || 1
        }
        else{
            name.value = ''
            description.value = ''
            startDate.value = ''
            endDate.value = ''
            status.value = ''
            semester.value = 1
        }
    })

    const saveData = () =>{

        var payload = {}
        payload.name = name.value
        payload.description = description.value
        payload.start_date = startDate.value
        payload.end_date = endDate.value
        payload.status = status.value
        payload.semester = semester.value

        var mappings = {}
        mappings.name = 'Batch Name'
        mappings.status = 'Status'
        mappings.start_date = 'Start Date'
        mappings.end_date = 'End Date'

        var error = false

        for (const key in payload) {
            if (Object.prototype.hasOwnProperty.call(payload, key)) {
                
                const element = payload[key];
                if(key !== 'description' && element?.toString().trim() === '')
                {
                    var entry = mappings[key]
                    showToast({title:entry+' is required',icon:'error'})
                    error = true
                    break
                }
            }
        }

        if(!error && startDate.value && endDate.value)
        {
            const start = new Date(startDate.value)
            const end = new Date(endDate.value)
            
            if(end <= start)
            {
                showToast({title:'End date must be after start date',icon:'error'})
                error = true
            }
        }

        if(!error)
        {
            showToast({title:'Verified successfully',icon:'success'})
            
            if(isEditMode.value && selectedBatch.value?.id)
            {
                payload.id = selectedBatch.value.id

                const response = api.put(`${baseURL}/batches/${payload.id}`,payload).then((response)=>{

                    if(response.data.status === 1)
                    {
                        showToast({title:response.data.message, icon:'success'})
                        setTimeout(() => {
                            modalInstance.hide()
                            semester.value = ''
                            selectedBatch.value = null
                            showBatch.value = false
                        }, 1500);
                    }
                    else{
                        showToast({title:response.data.message, icon:'error'})
                    }
                    
                }).catch((error)=>{
                    console.log(error);
                    showToast({title:error.response?.data?.message || error, icon:'error'})
                })
            }
            else
            {
                const response = api.post(`${baseURL}/batches/create`,payload).then((response)=>{
                    console.log(response)
                    if(response.data.status === 1)
                    {
                        showToast({title:response.data.message, icon:'success'})
                        setTimeout(() => {
                            modalInstance.hide()
                            semester.value = ''
                            selectedBatch.value = null
                            showBatch.value = false
                        }, 1500);
                    }
                    else{
                        showToast({title:response.data.message, icon:'error'})
                    }
                    
                }).catch((error)=>{
                    console.log(error);
                    showToast({title:error.response?.data?.message || error, icon:'error'})
                })
            }
        }
    }


    const deletebatche = async(batche) =>
    {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'You won’t be able to undo this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
        })

        if(result.isConfirmed)
        {
            const response = api.delete(`${baseURL}/batches/${batche.id}`).then((response)=>{
                if(response.data.status === 1)
                {
                    showToast({title:response.data.message, icon:'success'})
                    getAllBatches(lastInPage.value)
                }
                else{
                    showToast({title:response.data.message, icon:'error'})    
                }
            })
            
        }
    }

    const getBatchesByName = async ()=>{
        console.log(baseURL);
        
        const request = {}
        request.name = searchQuery.value
        const response = await api.post(`${baseURL}/batches/find`,request).then((response) =>{
            if(response.data.status === 1)
            {
                batches.value = response.data.data
            }
            else{
                    showToast({title:response.data.message, icon:'error'})    
            }
        })
    }

    

    

</script>

<style scoped>
.modal:not(.show) {
    display: none !important;
}

.font-sm{
    font-size: small;
}

.border{
    border: 1px solid #cccccc86;
}
</style>