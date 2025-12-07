<template>
  <div>
    <header class="p-3"
      style="background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 50%, var(--secondary-brown-dark) 100%);">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">

         <router-link
  :to="isStaffOrAdmin ? '/h' : '/'"
  class="d-flex align-items-center text-white text-decoration-none"
>
  <h3 class="logo-text mb-0">🌿 The Vegetable</h3>
</router-link>

<!-- เมนูลูกค้า -->
<ul
  v-if="isLoggedIn && isCustomer"
  class="nav mb-2 justify-content-center mb-md-0 d-none d-lg-flex gap-3">

  

  <li>
    <router-link to="/t" class="nav-link px-2 text-white">
      Menu
    </router-link>
  </li>

  <li>
    <router-link to="/table" class="nav-link px-2 text-white">
      Table
    </router-link>
  </li>
    <li><router-link to="/cus" class="nav-link px-2 text-white">USER</router-link></li>

</ul>

         

          <!-- เมนู Admin / Staff -->
<ul
  v-if="isLoggedIn && isStaffOrAdmin"
  class="nav mb-2 justify-content-center mb-md-0 d-none d-lg-flex gap-3">

  <li>
    <router-link to="/dash" class="nav-link px-2 text-white">
      Dashboard
    </router-link>
  </li>

  <li>
    <router-link to="/menu" class="nav-link px-2 text-white">
      Menu
    </router-link>
  </li>

  <li>
    <router-link to="/ed" class="nav-link px-2 text-white">
      Menu Edit
    </router-link>
  </li>

  <li>
    <router-link to="/em" class="nav-link px-2 text-white">
      Employee
    </router-link>
  </li>

  <li>
    <router-link to="/listbooking" class="nav-link px-2 text-white">
      List Booking
    </router-link>
  </li>

</ul>


          <!-- แสดงชื่อผู้ใช้ + ปุ่ม Logout -->
          <div v-if="isLoggedIn" class="d-flex align-items-center gap-3">
            <span class="text-white">
              👤 สวัสดี, <strong>{{ username }}</strong>
              <span class="badge bg-warning text-dark ms-2">{{ roleText }}</span>
            </span>
            <button @click="logout" class="btn btn-danger btn-sm">
              Logout
            </button>
          </div>

          <!-- ปุ่ม Login (เมื่อยังไม่ Login) -->
          <div v-if="!isLoggedIn" class="text-end">
            <button type="button" class="btn btn-outline-light me-2" @click="goToLogin">
              Login
            </button>
          </div>

        </div>
      </div>
    </header>

    <router-view :key="$route.fullPath" />
  </div>
</template>

<script>
export default {
  name: 'App',

 data() {
  return {
    role: null
  }
},
mounted() {
  this.role = localStorage.getItem('role');

  window.addEventListener('auth-changed', () => {
    this.role = localStorage.getItem('role');
  });
},


  computed: {
    // ตรวจว่า Login หรือยัง
    isLoggedIn() {
      return localStorage.getItem("role") !== null;
    },

    // ตรวจว่าเป็น Customer
    isCustomer() {
      const role = localStorage.getItem("role");
      return role === "customer";
    },

    // ตรวจว่า Staff หรือ Admin
    isStaffOrAdmin() {
      const role = localStorage.getItem("role");
      return role === "staff" || role === "admin";
    },

    // แปลง Role เป็นภาษาไทย
    roleText() {
      const role = localStorage.getItem("role");
      if (role === "customer") return "ลูกค้า";
      if (role === "staff") return "พนักงาน";
      if (role === "admin") return "ผู้ดูแลระบบ";
      return "";
    }
  },

  mounted() {
    // โหลดข้อมูลผู้ใช้
    this.loadUserData();
    
    // ฟังการเปลี่ยนแปลงของ localStorage
    window.addEventListener('storage', this.loadUserData);
  },

  beforeUnmount() {
    window.removeEventListener('storage', this.loadUserData);
  },

  methods: {
    // โหลดข้อมูลผู้ใช้จาก localStorage
    loadUserData() {
      this.username = localStorage.getItem("username") || "ผู้ใช้";
      this.role = localStorage.getItem("role") || "";
    },

    goToLogin() {
      this.$router.push('/login');
    },

    logout() {
      // ลบข้อมูลผู้ใช้ทั้งหมด
      localStorage.removeItem("role");
      localStorage.removeItem("username");
      localStorage.removeItem("customer_id");

      // รีเซ็ตค่า
      this.username = "";
      this.role = "";

      // บังคับให้ Vue อัปเดต computed properties
      this.$forceUpdate();

      // กลับหน้า Home และ reload เพื่อให้แน่ใจว่า state เคลียร์
      this.$router.push('/').then(() => {
        window.location.reload();
      });
    }
  },

  // อัปเดต username ทุกครั้งที่เปลี่ยนหน้า
  watch: {
    '$route'() {
      this.loadUserData();
    }
  }
};
</script>

<style scoped>
.logo-text {
  font-weight: 700;
  letter-spacing: 0.5px;
}

.nav-link {
  font-weight: 500;
  transition: all 0.3s ease;
}

.nav-link:hover {
  transform: translateY(-2px);
  text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}
</style>