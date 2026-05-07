<template>
  <div class="container">
    <!-- Trigger Button -->
    <!-- <button class="btn btn-primary" @click="showSidebar = true">
      Open Side Menu
    </button> -->

    <!-- Offcanvas Element -->
    <div 
      class="offcanvas offcanvas-end w-50" 
      :class="{ show: showSidebar }" 
      tabindex="-1"
      :style="{ visibility: showSidebar ? 'visible' : 'hidden' }"
    >
      <div class="offcanvas-header border-bottom">
        <!-- <h5 class="offcanvas-title">{{ heading }}</h5> -->
        <slot name="heading"></slot>
        <button type="button" class="btn-close" @click="emit('close')"></button>
      </div>
      <slot name="content"></slot>
    </div>

    <!-- Backdrop -->
    <div 
      v-if="showSidebar" 
      class="offcanvas-backdrop fade show" 
      @click="emit('close')"
    ></div>
  </div>
</template>

<script setup>
import { ref,onMounted } from 'vue'
// const showSidebar = ref(false)

const props = defineProps({
    showSidebar:{
        type:Boolean,
        default:''
    }
})

const emit = defineEmits(['close'])
</script>