<!-- Vue 3 Component: Login.vue -->
<template>
  <div class="auth-wrapper">
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-5">
          <div class="card shadow-lg border-0 organic-card">
            <div class="card-body p-5">
              
              <!-- Logo/Header -->
              <div class="text-center mb-4">
                <h2 class="organic-title">🌿 The Vegetable</h2>
                <p class="text-muted">สุขภาพดีเริ่มต้นที่นี่</p>
              </div>

              <!-- Tab Navigation -->
              <ul class="nav nav-pills nav-fill mb-4 organic-tabs">
                <li class="nav-item">
                  <a 
                    class="nav-link" 
                    :class="{ active: isLogin }"
                    @click="isLogin = true"
                    href="javascript:void(0)"
                  >
                    เข้าสู่ระบบ
                  </a>
                </li>
                <li class="nav-item">
                  <a 
                    class="nav-link" 
                    :class="{ active: !isLogin }"
                    @click="isLogin = false"
                    href="javascript:void(0)"
                  >
                    สมัครสมาชิก
                  </a>
                </li>
              </ul>

              <!-- Login Form -->
              <form v-if="isLogin" @submit.prevent="login">
                <div class="mb-3">
                  <label class="form-label organic-label">อีเมล</label>
                  <input 
                    v-model="loginData.email" 
                    type="email" 
                    class="form-control organic-input"
                    placeholder="your@email.com"
                    required
                  >
                </div>
                <div class="mb-3">
                  <label class="form-label organic-label">รหัสผ่าน</label>
                  <input 
                    v-model="loginData.password" 
                    type="password" 
                    class="form-control organic-input"
                    placeholder="••••••••"
                    required
                  >
                </div>
                
                <button 
                  type="submit" 
                  class="btn btn-organic w-100"
                  :disabled="loading"
                >
                  {{ loading ? 'กำลังเข้าสู่ระบบ...' : 'เข้าสู่ระบบ' }}
                </button>
              </form>

              <!-- Register Form -->
              <form v-else @submit.prevent="register">
                <div class="mb-3">
                  <label class="form-label organic-label">ชื่อ-นามสกุล</label>
                  <input 
                    v-model="registerData.name" 
                    type="text" 
                    class="form-control organic-input"
                    placeholder="ชื่อของคุณ"
                    required
                  >
                </div>
                <div class="mb-3">
                  <label class="form-label organic-label">อีเมล</label>
                  <input 
                    v-model="registerData.email" 
                    type="email" 
                    class="form-control organic-input"
                    placeholder="your@email.com"
                    required
                  >
                </div>
                <div class="mb-3">
                  <label class="form-label organic-label">รหัสผ่าน</label>
                  <input 
                    v-model="registerData.password" 
                    type="password" 
                    class="form-control organic-input"
                    placeholder="••••••••"
                    minlength="6"
                    required
                  >
                </div>
                <div class="mb-3">
                  <label class="form-label organic-label">ยืนยันรหัสผ่าน</label>
                  <input 
                    v-model="registerData.confirmPassword" 
                    type="password" 
                    class="form-control organic-input"
                    placeholder="••••••••"
                    required
                  >
                </div>
                
                <button 
                  type="submit" 
                  class="btn btn-organic w-100"
                  :disabled="loading"
                >
                  {{ loading ? 'กำลังสมัครสมาชิก...' : 'สมัครสมาชิก' }}
                </button>
              </form>

              <!-- Alert Messages -->
              <div v-if="message.text" 
                   :class="['alert', 'mt-3', message.type === 'success' ? 'alert-success' : 'alert-danger']"
                   role="alert">
                {{ message.text }}
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  name: 'LoginPage',
  
  data() {
    return {
      isLogin: true,
      loading: false,
      message: { text: '', type: '' },

      loginData: {
        email: '',
        password: ''
      },

      registerData: {
        name: '',
        email: '',
        password: '',
        confirmPassword: ''
      }
    };
  },

  methods: {
    async login() {
      this.loading = true;
      this.message = { text: '', type: '' };

      try {
        const response = await fetch('http://localhost:8081/group/api_php/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.loginData)
        });

        const data = await response.json();
        console.log('🔐 Login Response:', data);

        if (!data.success) {
          this.message = { text: data.message || 'เข้าสู่ระบบไม่สำเร็จ', type: 'error' };
          return;
        }

        // ✅ เก็บ localStorage
        localStorage.setItem('role', data.user.role);
        localStorage.setItem('username', data.user.name);
        localStorage.setItem('email', data.user.email || this.loginData.email);
        localStorage.setItem('user_id', data.user.id);
        localStorage.setItem('last_login', new Date().toISOString());

        // ✅ แจ้ง Navbar ให้อัปเดตตัวเอง
        window.dispatchEvent(new Event('auth-changed'));

        this.message = { text: 'เข้าสู่ระบบสำเร็จ!', type: 'success' };

        // ✅ redirect ตาม role
       setTimeout(() => {
  const role = data.user.role;

  if (role === 'admin' || role === 'staff') {
    this.$router.replace('/admin');   // ✅ Home แอดมิน (ไม่มีปุ่มซื้อ)
  } else {
    this.$router.replace('/');        // ✅ Home ลูกค้า (มีปุ่มซื้อ)
  }
}, 500);


      } catch (error) {
        console.error(error);
        this.message = { text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ', type: 'error' };
      } finally {
        this.loading = false;
      }
    },

    async register() {
      if (this.registerData.password !== this.registerData.confirmPassword) {
        this.message = { text: 'รหัสผ่านไม่ตรงกัน', type: 'error' };
        return;
      }

      this.loading = true;
      this.message = { text: '', type: '' };

      try {
        const response = await fetch('http://localhost:8081/group/api_php/signup.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            name: this.registerData.name,
            email: this.registerData.email,
            password: this.registerData.password
          })
        });

        const data = await response.json();

        if (data.success) {
          this.message = { text: 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ', type: 'success' };
          this.registerData = { name:'', email:'', password:'', confirmPassword:'' };

          setTimeout(() => {
            this.isLogin = true;
          }, 1200);
        } else {
          this.message = { text: data.message || 'สมัครสมาชิกไม่สำเร็จ', type: 'error' };
        }
      } catch (error) {
        console.error(error);
        this.message = { text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ', type: 'error' };
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.auth-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
  padding: 20px 0;
}

.organic-card {
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

.organic-title {
  color: #2e7d32;
  font-weight: 700;
  font-size: 1.8rem;
}

.organic-tabs .nav-link {
  color: #66bb6a;
  font-weight: 500;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.organic-tabs .nav-link.active {
  background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.organic-label {
  color: #2e7d32;
  font-weight: 600;
  font-size: 0.9rem;
}

.organic-input {
  border: 2px solid #a5d6a7;
  border-radius: 10px;
  padding: 12px 15px;
  transition: all 0.3s ease;
}

.organic-input:focus {
  border-color: #4caf50;
  box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
}

.btn-organic {
  background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
  border: none;
  color: white;
  padding: 12px;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.btn-organic:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
  background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);
}

.btn-organic:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.alert {
  border-radius: 10px;
  font-size: 0.9rem;
}
</style>