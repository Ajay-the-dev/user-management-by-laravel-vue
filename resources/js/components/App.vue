<template>
  <router-view />
</template>
<script setup>
import { ref,onMounted,onBeforeUnmount, computed } from "vue";
import { showToast } from '../utils/toastr'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
const role = computed(()=>userStore.allData.role)

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

  switch (role.value) {
    case "STUDENT":
        listenForStudentNotices()
        listenForStudentBatchNotices()
        break;
    default:
        listenForStaffNotices()
        break;
  }

  }
  
  //student notices listener
  const listenForStudentNotices = () =>{
    window.Echo.private('student-notice')
    .listen('UserNotice', (e) => {
        showToast({title:e.message,icon:'info'})
    })
  }

//staff notices listener
  const listenForStaffNotices = () =>{
    window.Echo.private('staff-notice')
    .listen('UserNotice', (e) => {
        showToast({title:e.message,icon:'info'})
    })
  }

   //student notices listener
  const listenForStudentBatchNotices = () =>{    
    window.Echo.private('student-notice-'+userStore.allData.batchId)
    .listen('UserNotice', (e) => {
        showToast({title:e.message,icon:'info'})
    })
  }

onMounted(()=>{
  setTimeout(() => {
    listenForNotifications();

    window.Echo.connector.pusher.connection.bind('unavailable', () => {
        console.log('Connection unavailable. Stopping reconnection attempts.');
        window.Echo.disconnect();
    });

    window.Echo.connector.pusher.connection.bind('failed', () => {
        console.log('Connection failed.');
        window.Echo.disconnect();
    });
  }, 3000);
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