import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

import "bootstrap/dist/css/bootstrap.css"
import "bootstrap/dist/js/bootstrap.bundle.js"

// 🎨 เพิ่มบรรทัดนี้ (ใส่ทีหลัง Bootstrap)
import "./assets/styles/global.css"

createApp(App).use(store).use(router).mount('#app')