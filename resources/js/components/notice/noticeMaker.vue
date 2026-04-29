<template>
  <div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="mb-0">Notice Board</h4>
        <small class="text-muted">Create and publish announcements</small>
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

          <!-- TITLE -->
          <input
            v-model="title"
            class="form-control form-control-lg mb-3"
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
                :key="a"
                class="btn btn-sm"
                :class="selectedAudience.includes(a) ? 'btn-dark' : 'btn-outline-secondary'"
                @click="toggleAudience(a)"
              >
                {{ a }}
              </button>

            </div>
          </div>

          <!-- EDITOR -->
          <div class="border rounded p-3 bg-light">
            <editor-content :editor="editor" />
          </div>

          <!-- FOOTER -->
          <div class="d-flex justify-content-between align-items-center mt-3">

            <div class="text-muted small">
              {{ wordCount }} words · {{ charCount }} characters
              <span v-if="lastEdited"> · updated {{ lastEdited }}</span>
            </div>

            <div class="d-flex gap-2">

              <button class="btn btn-outline-secondary btn-sm"
                @click="$emit('schedule', payload)">
                Schedule
              </button>

              <button class="btn btn-primary btn-sm"
                @click="publish">
                Publish Notice →
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
              v-for="a in selectedAudience"
              :key="a"
              class="badge bg-secondary me-1"
            >
              {{ a }}
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
import { ref, computed } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'

const emit = defineEmits(['save', 'schedule'])

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
const selectedAudience = ref(['All Students'])
const isDirty = ref(false)
const toast = ref('')
const lastEdited = ref('')
const isPreview = ref(false)

// ---------------- DATA ----------------
const noticeTypes = [
  { value: 'general', label: 'General' },
  { value: 'urgent', label: 'Urgent' },
  { value: 'event', label: 'Event' },
  { value: 'exam', label: 'Exam / Result' },
]

const audienceOptions = [
  'All Students',
  'Year 1',
  'Year 2',
  'Year 3',
  'Postgrad',
  'Faculty'
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
  html: editor.value?.getHTML() ?? ''
}))

// ---------------- METHODS ----------------
function toggleAudience(a) {
  const i = selectedAudience.value.indexOf(a)
  i === -1
    ? selectedAudience.value.push(a)
    : selectedAudience.value.splice(i, 1)
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

let toastTimer
function showToast(msg) {
  toast.value = msg
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => (toast.value = ''), 2500)
}

function publish() {
  if (!title.value.trim()) {
    showToast('Title is required')
    return
  }

  if (!bodyText.value.trim()) {
    showToast('Notice content is empty')
    return
  }

  emit('save', payload.value)
  isDirty.value = false
  showToast('Notice published successfully')
}
</script>