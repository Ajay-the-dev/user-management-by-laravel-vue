import './bootstrap';

import router from './router'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'

import Swal from 'sweetalert2'; 
import 'sweetalert2/dist/sweetalert2.min.css';

import '@fortawesome/fontawesome-free/css/all.min.css'


import { createPinia } from 'pinia'
import piniaPersistedState from 'pinia-plugin-persistedstate'


import { createApp } from 'vue'
import App from './components/App.vue'

const app = createApp(App)

const pinia = createPinia()
pinia.use(piniaPersistedState)

app.use(pinia)
app.use(router)
app.mount('#app')

