import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'login',
    component: () => import('@/components/login.vue'), 
  },
  {
    path: '/home',
    name: 'main',
    component: () => import('@/components/home.vue'), 
    children:[
      {
        path:'',
        name:'home',
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
      },
      {
        path:'batch-home',
        name:'/home/batch-home',
        component: () => import('@/components/batchListView.vue'),
      },
      {
        path:'staff-home',
        name:'/home/staff-home',
        component: () => import('@/components/listView.vue'),
      },
      {
        path:'staff-add',
        name:'/home/staff-add',
        component: () => import('@/components/add.vue'),
      },
      {
        path:'student-home',
        name:'/home/student-home',
        component: () => import('@/components/listView.vue'),
      },
      {
        path:'student-add',
        name:'/home/student-add',
        component: () => import('@/components/add.vue'),
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
