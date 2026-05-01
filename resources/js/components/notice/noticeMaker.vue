<template>
  <div class="container-fluid py-4 content-scroll vh-100">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="mb-0">Notice Board</h4>
        <small class="text-muted">{{isEditMode?'Update published announcements':'Create and publish announcements'}}</small>
      </div>

      <div class="d-flex align-items-center gap-2">

        <!-- Preview Toggle -->
        <button
          class="btn btn-outline-primary btn-sm"
          @click="isPreview = !isPreview"
        >
          {{ isPreview ? 'Back to Edit' : 'Preview' }}
        </button>

        <span class="badge"
          :class="isDirty ? 'bg-warning text-dark' : 'bg-success'">
          {{ isDirty ? 'Draft (unsaved)' : 'Saved' }}
        </span>

      </div>
    </div>

    <!-- ================= EDIT MODE ================= -->
    <div v-if="!isPreview">

      <div class="card shadow-sm border-0" v-if="editor">
        <div class="card-body">

          <!-- TOOLBAR -->

          <!-- TITLE -->
          <input
            v-model="title"
            class="form-control mb-3"
            placeholder="Enter notice title..."
          />

          <!-- TYPE -->
          <div class="mb-3">
            <label class="form-label fw-semibold">Notice Type</label>

            <div class="btn-group w-100 flex-wrap">

              <button
                v-for="t in noticeTypes"
                :key="t.value"
                class="btn btn-outline-secondary btn-sm"
                :class="{ active: noticeType === t.value }"
                @click="noticeType = t.value"
              >
                {{ t.label }}
              </button>

            </div>
          </div>

          <!-- AUDIENCE -->
          <div class="mb-3">
            <label class="form-label fw-semibold">Audience</label>

            <div class="d-flex flex-wrap gap-2">

              <button
                v-for="a in audienceOptions"
                :key="a.value"
                class="btn btn-sm"
                :class="selectedAudience.includes(a.value) ? 'btn-dark' : 'btn-outline-secondary'"
                @click="toggleAudience(a.value)"
              >
                {{ a.name }}
              </button>
              <select name="selectedBatch" id="" v-if="selectedAudience==='STUDENT'" class="form-control w-auto border-secondary" v-model="selectedBatch">
                <option value="">All Batch</option>
                <option v-for="value in batches" :value="value.id">{{ value.name }}</option>
              </select>

            </div>
          </div>

          <!-- EDITOR -->
          <div class="card px-3 py-2">
             <div class="btn-group flex-wrap w-100 mb-3 border-bottom pb-2">

            <button class="btn btn-light btn-sm"
              :class="{ active: editor.isActive('bold') }"
              @mousedown.prevent="editor.chain().focus().toggleBold().run()">
              B
            </button>

            <button class="btn btn-light btn-sm"
              :class="{ active: editor.isActive('italic') }"
              @mousedown.prevent="editor.chain().focus().toggleItalic().run()">
              I
            </button>

            <button class="btn btn-light btn-sm"
              :class="{ active: editor.isActive('underline') }"
              @mousedown.prevent="editor.chain().focus().toggleUnderline().run()">
              U
            </button>

            <span class="vr mx-2"></span>

            <button class="btn btn-light btn-sm"
              @mousedown.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()">
              H2
            </button>

            <button class="btn btn-light btn-sm"
              @mousedown.prevent="editor.chain().focus().toggleBulletList().run()">
              • List
            </button>

            <button class="btn btn-light btn-sm"
              @mousedown.prevent="editor.chain().focus().toggleOrderedList().run()">
              1.
            </button>

            <span class="vr mx-2"></span>

            <button class="btn btn-light btn-sm" @mousedown.prevent="setLink">
              Link
            </button>

            <button class="btn btn-light btn-sm"
              @mousedown.prevent="editor.chain().focus().toggleCode().run()">
              Code
            </button>

            <span class="vr mx-2"></span>

            <button class="btn btn-light btn-sm"
              @mousedown.prevent="editor.chain().focus().undo().run()">
              Undo
            </button>

            <button class="btn btn-light btn-sm"
              @mousedown.prevent="editor.chain().focus().redo().run()">
              Redo
            </button>

          </div>
            <div class="border rounded p-3 bg-light">
              <editor-content :editor="editor" />
            </div>
          </div>

          <!-- FOOTER -->
          <div class="d-flex justify-content-between align-items-center mt-3">

            <div class="text-muted small">
              {{ wordCount }} words · {{ charCount }} characters
              <span v-if="lastEdited"> · updated {{ lastEdited }}</span>
            </div>

            <div class="d-flex gap-2">

              <!-- <button class="btn btn-outline-secondary btn-sm"
                @click="$emit('schedule', payload)">
                Schedule
              </button> -->

              <button class="btn btn-primary btn-sm"  @click="initPublish">
                {{isEditMode ? 'Publish as new':'Publish'}}
              </button>
              <button class="btn btn-secondary btn-sm" v-if="isEditMode">
                Cancel
              </button>
            </div>

          </div>

        </div>
      </div>

    </div>

    <!-- ================= PREVIEW MODE ================= -->
    <div v-else>

      <div class="card shadow-sm border-0">
        <div class="card-body">

          <h3 class="mb-2">{{ title || 'Untitled Notice' }}</h3>

          <div class="mb-3">
            <span class="badge bg-primary me-2">{{ noticeType }}</span>
            <span
              class="badge bg-secondary me-1"
            >
              {{ selectedAudience }}
            </span>
          </div>

          <hr />

          <div v-html="editor?.getHTML()"></div>

          <hr />

          <small class="text-muted">
            {{ wordCount }} words · {{ charCount }} characters
          </small>

        </div>
      </div>

    </div>

    <!-- TOAST -->
    <div v-if="toast" class="position-fixed bottom-0 end-0 m-3">
      <div class="alert alert-dark py-2 px-3 mb-0">
        {{ toast }}
      </div>
    </div>

  </div>
</template>

<script setup>
import api from '@/utils/axios'
import { ref, computed,onMounted } from 'vue'
import { useRouter,useRoute } from "vue-router";
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'
import { showToast } from '@/utils/toastr'
import Swal from 'sweetalert2'


const emit = defineEmits(['save', 'schedule'])

const router = useRouter()
const route = useRoute()
const selectedNotice = ref(null)

// ---------------- EDITOR ----------------
const editor = useEditor({
  extensions: [StarterKit, Underline, Link.configure({ openOnClick: false })],
  content: '',
  onUpdate() {
    isDirty.value = true
    updateLastEdited()
  }
})

// ---------------- STATE ----------------
const title = ref('')
const noticeType = ref('general')
const selectedAudience = ref('ALL')
const isDirty = ref(false)
const toast = ref('')
const lastEdited = ref('')
const isPreview = ref(false)
const batches = ref([])
const selectedBatch = ref('')
const isEditMode = ref(false)




// ---------------- DATA ----------------
const noticeTypes = [
  { value: 'general', label: 'General' },
  { value: 'urgent', label: 'Urgent' },
  { value: 'event', label: 'Event' },
  { value: 'exam', label: 'Exam / Result' },
]

const audienceOptions = [
  { name:"All",value:"ALL"},
  { name:"Students",value:"STUDENT" },
  { name:"faculties",value:"STAFF" },
   ]

// ---------------- COMPUTED ----------------
const bodyText = computed(() => editor.value?.getText() ?? '')

const combined = computed(() =>
  `${title.value} ${bodyText.value}`.trim()
)

const wordCount = computed(() =>
  combined.value ? combined.value.split(/\s+/).filter(Boolean).length : 0
)

const charCount = computed(() => combined.value.length)

const payload = computed(() => ({
  title: title.value,
  type: noticeType.value,
  audience: selectedAudience.value,
  customBatch: selectedBatch.value,
  html: editor.value?.getHTML() ?? ''
}))

// ---------------- METHODS ----------------
function toggleAudience(a) {
  selectedAudience.value = a
}

function updateLastEdited() {
  lastEdited.value = new Date().toLocaleTimeString([], {
    hour: '2-digit',
    minute: '2-digit'
  })
}

function setLink() {
  const url = window.prompt('Enter URL')
  if (!url) return
  editor.value?.chain().focus().setLink({ href: url }).run()
}


const initPublish = async()=>{
  
  let msgString = isEditMode.value ? 'Do you want to public this updated notice? It will be shown on designated users':'Do you want to public this notice? It will be shown on designated users';
  const result = await Swal.fire({
  title: 'Confirmation',
  text: msgString,
  icon: 'info',
  showCancelButton: true,
  confirmButtonColor: '#6366f1',
  confirmButtonText: 'Publish',
  cancelButtonText: "Cancel",
})

  if (result.isConfirmed) {
    publish()
  }
}



async function publish() {

  if (!title.value.trim()) {
    showToast({title:'Title is required',icon:'error'})
    return
  }

  
  if(!selectedAudience.value.trim())
  {
    showToast({title:'Please choose an audience',icon:'error'})
    return
  }

  if (!bodyText.value.trim()) {
    showToast({title:'Notice content is empty',icon:'error'})
    return
  }
  
  isDirty.value = false
  const response = await api.post('notice/create',payload.value)
  if(response.data.status === 1){
    showToast({title:response.data.message,icon:'success'})
    setTimeout(() => {
      router.go(0)
    }, 2000);
  }
  else{
    showToast({title:'Notice creation is failed',icon:'error'})
  }
}


const getNoticeDetails = async()=>
{
  const response = await api.get('/notice/getDetails/'+selectedNotice.value)
  let notice = response.data.data
  title.value = notice.title
  noticeType.value = notice.type
  selectedAudience.value = notice.audience
  selectedBatch.value = notice.customBatch
  editor.value?.commands.setContent(notice.html)
  isEditMode.value = true
}

onMounted(()=>{
  loadBatches()
  if(route.name === 'notice.edit')
  {
    selectedNotice.value = route.params.id
    setTimeout(() => {
      getNoticeDetails();
    }, 300);
  }
});

const loadBatches = async() => {
  const res = await api.get('/batches/all');   
  batches.value = res.data.data
}
</script>