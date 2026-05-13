import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import axios from 'axios'

export const initEcho = async () => {

    const { useUserStore } = await import('@/stores/userStore')
    const userStore = useUserStore()

    window.axios = axios

    // ✅ API URL from env
    window.axios.defaults.baseURL = import.meta.env.VITE_API_URL

    window.axios.defaults.withCredentials = true
    window.Pusher = Pusher

    window.Echo = new Echo({
        broadcaster: 'reverb',

        key: import.meta.env.VITE_REVERB_APP_KEY,

        wsHost: import.meta.env.VITE_REVERB_HOST,

        wsPort: import.meta.env.VITE_REVERB_PORT,

        wssPort: import.meta.env.VITE_REVERB_PORT,

        forceTLS: true,

        enabledTransports: ['ws', 'wss'],

        authEndpoint: `${import.meta.env.VITE_API_URL}/api/broadcasting/auth`,

        auth: {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`,
                Accept: 'application/json',
            }
        }
    })

    return window.Echo
}