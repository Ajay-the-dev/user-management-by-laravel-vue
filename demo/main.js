// Demo entry: install the mock API first, then boot the real app unchanged.
import { resetDemoData } from './mock/api'
import '../resources/scss/app.scss'
import '../resources/js/app.js'

document.getElementById('demo-reset')?.addEventListener('click', () => {
  resetDemoData()
  try {
    // Pinia's persisted user store and the token the login page saves
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  } catch (e) { /* ignore */ }
  window.location.href = '/'
})

document.getElementById('demo-close')?.addEventListener('click', () => {
  document.getElementById('demo-banner')?.remove()
})
