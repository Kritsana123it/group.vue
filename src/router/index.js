import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
   {
    path: '/h',
    name: 'h',
    component: () => import('../views/homeadmin.vue')
  },
  {
    path: '/t',
    name: 'test',
    component: () => import('../views/test.vue')
  },
   {
    path: '/menu',
    name: 'menu',
    component: () => import('../views/menuadmin.vue')
  },
  {
    path: '/cus',
    name: 'cus',
    component: () => import('../views/customer.vue')
  },
  {
    path: '/em',
    name: 'em',
    component: () => import('../views/employee.vue')
  },
  {
    path: '/table',
    name: 'table',
    component: () => import('../views/Table.vue')
  },
  {
    path: '/user',
    name: 'user',
    component: () => import('../views/User.vue')
  },
  {
  path: '/login',
  name: 'Login',
  component: () => import('../views/Login.vue')
},
{
  path: '/signup',
  redirect: '/login'  // เมื่อกด Sign-up จะไปหน้า Login แทน
},
  
  {
    path: '/ed',
    name: 'editmenu',
    component: () => import('../views/editmenu.vue')
  },
  {
    path: '/listbooking',
    name: 'listbooking',
    component: () => import('../views/tablebooking.vue')
  },
   {
    path: '/dash',
    name: 'dashboard',
    component: () => import('../views/Dashboard .vue')
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})
// 🧠 Navigation Guard — ตรวจสอบการเข้าสู่ระบบ
router.beforeEach((to, from, next) => {
  const isLoggedIn = localStorage.getItem("customer_login") === "true";

  // ถ้าหน้านั้นต้องล็อกอินก่อน แต่ยังไม่ได้ล็อกอิน
  if (to.meta.requiresAuth && !isLoggedIn) {
    alert("⚠ กรุณาเข้าสู่ระบบก่อนใช้งานหน้านี้");
    next("/login");
  }
  // ถ้าเข้าสู่ระบบแล้วแต่พยายามกลับไปหน้า login อีก → ส่งกลับหน้าแรก
  else if (to.path === "/login" && isLoggedIn) {
    next("/");
  } 
  // อื่น ๆ ไปต่อได้ตามปกติ
  else {
    next();
  }
});
export default router