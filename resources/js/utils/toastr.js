import Swal from 'sweetalert2'

export function showToast({ title = '', icon = 'success', position = 'top-end', timer = 3000 }) {
  Swal.fire({
    toast: true,
    position,
    icon,
    title,
    showConfirmButton: false,
    timer,  
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })
}
