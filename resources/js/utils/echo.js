import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import axios from 'axios'

export const initEcho = async () => {

    const { useUserStore } = await import('@/stores/userStore') // ✅ dynamic import
    const userStore = useUserStore()

    window.axios = axios
    window.axios.defaults.baseURL = 'http://127.0.0.1:8000'
    window.axios.defaults.withCredentials = true
    window.Pusher = Pusher

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: 'http://127.0.0.1:8000/api/broadcasting/auth',
        auth: {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        }
    })

    return window.Echo
}