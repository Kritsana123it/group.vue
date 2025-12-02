<template>
  <div class="container mt-5" style="max-width:400px;">
    <h3 class="text-center mb-4">🔐 เข้าสู่ระบบลูกค้า</h3>

    <div class="card p-4 shadow">
      <div class="mb-3">
        <label class="form-label">ชื่อผู้ใช้</label>
        <input v-model="username" type="text" class="form-control" />
      </div>

      <div class="mb-3">
        <label class="form-label">รหัสผ่าน</label>
        <input v-model="password" type="password" class="form-control" />
      </div>

      <button @click="login" class="btn btn-primary w-100">เข้าสู่ระบบ</button>

      <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      username: "",
      password: "",
      error: "",
    };
  },
  methods: {
    async login() {
      try {
        const res = await axios.post("http://localhost:8081/group/api_php/login.php", {
          username: this.username,
          password: this.password,
        });

        if (res.data.success) {
          // ✅ บันทึกสถานะ login สำหรับลูกค้า
          localStorage.setItem("customer", "true");
          localStorage.setItem("username", this.username);
          this.$router.push("/Menu");
        } else {
          this.error = res.data.message;
        }
      } catch (err) {
        this.error = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
      }
    },
  },
};
</script>
