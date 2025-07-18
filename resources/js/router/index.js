import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'login',
    component: () => import('@/components/login.vue'), 
  },
  {
    path: '/home',
    name: 'home',
    component: () => import('@/components/home.vue'), 
    children:[
      {
        path:'',
        name:'default',
        component: () => import('@/components/quickAccess.vue'), 
      },
      {
        path:'add',
        name:'add',
        component: () => import('@/components/add.vue'), 
      },
      {
        path:'update',
        name:'update',
        component: () => import('@/components/update.vue'), 
      },
      {
        path:'delete',
        name:'delete',
        component: () => import('@/components/delete.vue'), 
      },
      {
        path:'list',
        name:'list',
        component: () => import('@/components/list.vue'), 
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
