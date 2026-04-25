import { createRouter, createWebHistory } from 'vue-router'

// Layouts
const MainLayout = () => import('@/components/home.vue')

// Pages
const Login = () => import('@/components/login.vue')
const QuickAccess = () => import('@/components/quickAccess.vue')

// Batch
const BatchList = () => import('@/components/batchListView.vue')

// Staff
const StaffList = () => import('@/components/listView.vue')
const StaffAdd = () => import('@/components/add.vue')

// Student
const StudentList = () => import('@/components/listView.vue')
const StudentAdd = () => import('@/components/add.vue')
const StudentEdit = () => import('@/components/add.vue')

// Fee
const FeeHome = () => import('@/components/fee/feeHome.vue')
const FeePay = () => import('@/components/fee/feeEntry.vue')
const FeeRoot = () => import('@/components/fee/feeRouteView.vue')
const FeePayment = () => import('@/components/fee/paymentPage.vue')



// Student Fee Module
const StudentLayout = () => import('@/components/student/home.vue')
const StudentFee = () => import('@/components/student/fee.vue')

const routes = [
  {
    path: '/',
    name: 'login',
    component: Login
  },

  {
    path: '/home',
    component: MainLayout,
    meta: { requiresAuth: true },

    children: [
      {
        path: '',
        name: 'dashboard',
        component: QuickAccess
      },

      // ✅ Batch
      {
        path: 'batch',
        name: 'batch.list',
        component: BatchList
      },

      // ✅ Staff Module
      {
        path: 'staff',
        children: [
          {
            path: '',
            name: 'staff.list',
            component: StaffList
          },
          {
            path: 'add',
            name: 'staff.add',
            component: StaffAdd
          }
        ]
      },

      // ✅ Student Module
      {
        path: 'student',
        children: [
          {
            path: '',
            name: 'student.list',
            component: StudentList
          },
          {
            path: 'add',
            name: 'student.add',
            component: StudentAdd
          },
          {
            path: 'edit/:id',
            name: 'student.edit',
            component: StudentEdit,
            props: true
          },

          // Nested Student Fee
          {
            path: 'home',
            component: StudentLayout,
            children: [
              {
                path: '',
                name: 'student.home',
                component: StudentFee
              }
            ]
          },
           {
            path: 'fee',
            component: StudentFee,
            children: [
              {
                path: '',
                name: 'student.home',
                component: StudentFee
              }
            ]
          }
        ]
      },

      // ✅ Fee Module
      {
        path: 'fee',
        component: FeeRoot,
        children:[
          {
            path:'',
            name: 'fee.home',
            component:FeeHome
          },
          {
            path:'pay',
            name: 'fee.pay',
            component:FeePay
          },{
            path:'payment/:id',
            name:'fee.final',
            component:FeePayment
          }
        ]
      }
    ]
  },

  // ✅ Fallback (important)
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router;