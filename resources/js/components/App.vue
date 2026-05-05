<template>
  <router-view />
</template>
<script setup>
import { ref,onMounted,onBeforeUnmount } from "vue";
import { showToast } from '../utils/toastr'

onBeforeUnmount(() => {
  if (window.Echo) {
    window.Echo.leave('notifications')
  }
});

const listenForNotifications = async() => {


  //public notifications
  if (!window.Echo) {
    console.error("Echo not initialized")
    return
  }

  window.Echo.channel('notifications')
    .listen('NewNotification', (e) => {
        showToast({title:e.message,icon:'info'})
    })

    //student notifications

    Echo.private('student-notice')
    .listen('UserNotice', (e) => {
        showToast({title:e.message,icon:'info'})
    })

    //faculty notifications

      Echo.private('staff-notice')
    .listen('UserNotice', (e) => {
        showToast({title:e.message,icon:'info'})
    })
}

onMounted(()=>{
  listenForNotifications();
})
</script>
<style>
body {
  overflow: hidden;
}
.content-scroll {
  height: 100%;
  overflow-y: auto;
}
</style>