<template>
  <div>
    <header class="p-3" style="background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 50%, var(--secondary-brown-dark) 100%);">
 
      <div class="container"> 
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start"> 
          <router-link to="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
            <h3 class="logo-text mb-0">Vegetable</h3>
          </router-link> 
          
          <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0"> 
            <li><router-link to="/" class="nav-link px-2 text-white">Home</router-link></li>
            <li><router-link to="/menu" class="nav-link px-2 text-white">Menu</router-link></li>
            <li><router-link to="/table" class="nav-link px-2 text-white">Table</router-link></li>
            <li><router-link to="/reports" class="nav-link px-2 text-white">Reports</router-link></li>
            <li><router-link to="/user" class="nav-link px-2 text-white">User</router-link></li>
          </ul> 
          
          
          
          <div class="text-end">
            <button 
              type="button" 
              class="btn btn-outline-light me-2" 
              @click="goToLogin"
            >
              Login
            </button>
            <button 
              type="button" 
              class="btn btn-warning" 
              @click="goToSignup"
            >
              Sign-up
            </button>
          </div> 
        </div> 
      </div> 
    </header>
    
    <!-- เนื้อหาของคุณ -->
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App',
  data() {
    return {
      searchQuery: ''
    }
  },
  methods: {
    goToLogin() {
      console.log('✅ LOGIN CLICKED!')
      alert('Login button works!')
      this.$router.push('/login').catch(() => {
        console.log('Login route not setup yet')
      })
    },
    goToSignup() {
      console.log('✅ SIGNUP CLICKED!')
      alert('Signup button works!')
      this.$router.push('/signup').catch(() => {
        console.log('Signup route not setup yet')
      })
    },
    handleSearch() {
      console.log('🔍 Searching:', this.searchQuery)
      if (this.searchQuery.trim()) {
        alert(`Searching for: ${this.searchQuery}`)
      }
    }
  },
  mounted() {
    console.log('✅ App mounted successfully')
  }
}
</script>



<style>
/* ลบ @import ออก ใช้ที่ index.html แทน */

/* Apply font to entire app */
body, * {
  font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
}

/* Logo text styling */
.logo-text {
  font-weight: 700;
  font-size: 1.8rem;
  letter-spacing: 0.5px;
}

/* Navigation links */
.nav-link {
  font-weight: 600 !important;
  font-size: 1.05rem !important;
}

/* Buttons */
button, .btn {
  font-weight: 600 !important;
  pointer-events: auto !important;
  cursor: pointer !important;
  position: relative;
  z-index: 10;
}
</style>