import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/menu',
    name: 'menu',
    component: () => import('../views/Menu.vue')
  },
  {
    path: '/table',
    name: 'table',
    component: () => import('../views/Table.vue')
  },
  {
    path: '/reports',
    name: 'reports',
    component: () => import('../views/Reports.vue')
  },
  {
    path: '/user',
    name: 'user',
    component: () => import('../views/User.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/signup',
    name: 'signup',
    component: () => import('../views/Signup.vue')
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router