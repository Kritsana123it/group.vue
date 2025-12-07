<template>
  <div class="container mt-4">
    <h2 class="mb-3">จัดการผู้ใช้งาน</h2>

    <!-- ปุ่มเพิ่ม -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <button class="btn btn-primary" @click="openAddModal">เพิ่มผู้ใช้</button>
    </div>

    <!-- ตารางผู้ใช้ -->
    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th class="text-center">รูป</th>
          <th class="text-center">ID</th>
          <th>ชื่อ</th>
          <th>Email</th>
          <th class="text-center">Role</th>
          <th class="text-center">การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="u in paginatedUsers" :key="u.id">
          <td class="text-center">
            <img
              v-if="u.image"
              :src="'http://localhost:8081/group/api_php/uploads/' + u.image"
              width="70"
              height="70"
              class="rounded-circle"
            />
            <span v-else class="text-muted">ไม่มีรูป</span>
          </td>
          <td class="text-center">{{ u.id }}</td>
          <td>{{ u.name }}</td>
          <td>{{ u.email }}</td>
          <td class="text-center">
            <span class="badge bg-success" v-if="u.role === 'admin'">Admin</span>
            <span class="badge bg-secondary" v-else>Customer</span>
          </td>
          <td class="text-center">
            <button class="btn btn-warning btn-sm me-2" @click="openEditModal(u)">
              แก้ไข
            </button>
            <button class="btn btn-danger btn-sm" @click="deleteUser(u.id)">
              ลบ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <nav v-if="totalPages > 1">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{disabled: currentPage === 1}">
          <button class="page-link" @click="prevPage">ก่อนหน้า</button>
        </li>

        <li class="page-item"
            v-for="page in totalPages"
            :key="page"
            :class="{active: currentPage === page}">
          <button class="page-link" @click="goToPage(page)">{{ page }}</button>
        </li>

        <li class="page-item" :class="{disabled: currentPage === totalPages}">
          <button class="page-link" @click="nextPage">ถัดไป</button>
        </li>
      </ul>
    </nav>

    <!-- Modal เพิ่ม/แก้ไข -->
    <div class="modal fade" id="userModal" tabindex="-1">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEdit ? 'แก้ไขผู้ใช้' : 'เพิ่มผู้ใช้ใหม่' }}</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <input type="hidden" v-model="form.id">

            <div class="mb-3">
              <label>ชื่อ</label>
              <input type="text" class="form-control" v-model="form.name">
            </div>

            <div class="mb-3">
              <label>Email</label>
              <input type="email" class="form-control" v-model="form.email">
            </div>

            <!-- ถ้าเพิ่ม ต้องใส่ password -->
            <div class="mb-3" v-if="!isEdit">
              <label>Password</label>
              <input type="password" class="form-control" v-model="form.password">
            </div>

            <div class="mb-3">
              <label>Role</label>
              <select class="form-select" v-model="form.role">
                <option value="admin">Admin</option>
              </select>
            </div>

            <div class="mb-3">
              <label>รูปภาพ</label>
              <input type="file" class="form-control" @change="handleImage">
            </div>

            <!-- รูปเดิม -->
            <div v-if="isEdit && form.image" class="text-center">
              <p>รูปปัจจุบัน:</p>
              <img :src="'http://localhost:8081/group/api_php/uploads/' + form.image"
                  width="100" height="100" class="rounded-circle">
            </div>

            <button class="btn btn-success w-100 mt-3" @click="saveUser">
              {{ isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มผู้ใช้' }}
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";

export default {
  setup() {
    const users = ref([]);
    const isEdit = ref(false);
    const form = ref({
      id: null,
      name: "",
      email: "",
      password: "",
      role: "admin",
      image: ""
    });

    let newImage = null;
    let modal = null;

    // Pagination
    const currentPage = ref(1);
    const perPage = 5;

    const totalPages = computed(() => Math.ceil(users.value.length / perPage));
    const paginatedUsers = computed(() => {
      const start = (currentPage.value - 1) * perPage;
      return users.value.slice(start, start + perPage);
    });

    // ========== API ==========
    const fetchUsers = async () => {
  const res = await fetch("http://localhost:8081/group/api_php/api_users.php");
  const data = await res.json();

  // ✅ กรองเฉพาะ admin
  users.value = data.data.filter(u => u.role === 'admin');
};


    // เปิด modal เพิ่ม
    const openAddModal = () => {
      isEdit.value = false;
      form.value = { id: null, name: "", email: "", password: "", role: "customer", image: "" };
      newImage = null;

      modal = new window.bootstrap.Modal(document.getElementById("userModal"));
      modal.show();
    };

    // เปิด modal แก้ไข
    const openEditModal = (u) => {
      isEdit.value = true;
      form.value = JSON.parse(JSON.stringify(u));
      newImage = null;

      modal = new window.bootstrap.Modal(document.getElementById("userModal"));
      modal.show();
    };

    // จัดการรูป
    const handleImage = (e) => {
      newImage = e.target.files[0];
    };

    // บันทึกข้อมูล
    const saveUser = async () => {
      const fd = new FormData();
      fd.append("action", isEdit.value ? "update" : "add");

      if (isEdit.value) fd.append("id", form.value.id);

      fd.append("name", form.value.name);
      fd.append("email", form.value.email);
      fd.append("role", form.value.role);

      if (!isEdit.value) fd.append("password", form.value.password);
      if (newImage) fd.append("image", newImage);

      const res = await fetch("http://localhost:8081/group/api_php/api_users.php", {
        method: "POST",
        body: fd
      });

      const data = await res.json();
      alert(data.message);

      modal.hide();
      fetchUsers();
    };

    // ลบผู้ใช้
    const deleteUser = async (id) => {
      if (!confirm("ยืนยันการลบ?")) return;

      const fd = new FormData();
      fd.append("action", "delete");
      fd.append("id", id);

      const res = await fetch("http://localhost:8081/group/api_php/api_users.php", {
        method: "POST",
        body: fd
      });

      const data = await res.json();
      alert(data.message);
      fetchUsers();
    };

    onMounted(() => fetchUsers('admin'));


    return {
      users,
      form,
      isEdit,
      paginatedUsers,
      totalPages,
      currentPage,
      openAddModal,
      openEditModal,
      handleImage,
      saveUser,
      deleteUser,
      prevPage() {
        if (currentPage.value > 1) currentPage.value--;
      },
      nextPage() {
        if (currentPage.value < totalPages.value) currentPage.value++;
      },
      goToPage(page) {
        currentPage.value = page;
      }
    };
  }
};
</script>

<style scoped>
.rounded-circle {
  object-fit: cover;
}
</style>
