<template>
  <div class="container mt-4">
    <h2 class="mb-3">📋 รายการจองโต๊ะ</h2>

    <!-- 🔹 ตัวเลือกจำนวนแถว -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-success" @click="fetchBookings">
          <i class="bi bi-arrow-clockwise"></i> รีเฟรช
        </button>
        <span class="badge bg-primary fs-6">ทั้งหมด: {{ bookings.length }} รายการ</span>
      </div>

      <div class="d-flex align-items-center">
        <label class="me-2">แสดงแถวต่อหน้า:</label>
        <select v-model.number="itemsPerPage" class="form-select w-auto">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
      </div>
    </div>

    <!-- 🏷️ ปุ่มกรองสถานะ -->
    <div class="mb-3">
      <label class="fw-bold mb-2">กรองตามสถานะ:</label>
      <div class="d-flex flex-wrap gap-2">
        <button 
          class="btn btn-sm"
          :class="statusFilter === '' ? 'btn-primary' : 'btn-outline-primary'"
          @click="statusFilter = ''"
        >
          ทั้งหมด
        </button>
        <button 
          class="btn btn-sm"
          :class="statusFilter === 'รอยืนยัน' ? 'btn-warning' : 'btn-outline-warning'"
          @click="statusFilter = 'รอยืนยัน'"
        >
          รอยืนยัน
        </button>
        <button 
          class="btn btn-sm"
          :class="statusFilter === 'ยืนยันแล้ว' ? 'btn-success' : 'btn-outline-success'"
          @click="statusFilter = 'ยืนยันแล้ว'"
        >
          ยืนยันแล้ว
        </button>
        <button 
          class="btn btn-sm"
          :class="statusFilter === 'ยกเลิก' ? 'btn-danger' : 'btn-outline-danger'"
          @click="statusFilter = 'ยกเลิก'"
        >
          ยกเลิก
        </button>
      </div>
    </div>

    <!-- 🔍 ช่องค้นหา -->
    <div class="mb-3">
      <input 
        type="text" 
        v-model="searchTerm" 
        class="form-control" 
        placeholder="🔍 ค้นหาด้วยชื่อลูกค้า, เบอร์โทร หรือโซน..."
      />
    </div>

    <!-- ✅ ตารางข้อมูลการจอง -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover">
        <thead class="table-success">
          <tr>
            <th class="text-center">ID</th>
            <th>ชื่อลูกค้า</th>
            <th>เบอร์โทร</th>
            <th class="text-center">โซน</th>
            <th class="text-center">จำนวน</th>
            <th class="text-center">เวลา</th>
            <th class="text-center">วันที่จอง</th>
            <th class="text-center">สถานะ</th>
            <th class="text-center">การจัดการ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="booking in paginatedBookings" :key="booking.booking_id">
            <td class="text-center">{{ booking.booking_id }}</td>
            <td>{{ booking.customer_name }}</td>
            <td>{{ booking.phone }}</td>
            <td class="text-center">
              <span class="badge bg-info">{{ booking.zone }}</span>
            </td>
            <td class="text-center">
              <span class="badge bg-secondary">{{ booking.guests }} คน</span>
            </td>
            <td class="text-center">{{ booking.time }} น.</td>
            <td class="text-center">{{ formatDate(booking.booking_date) }}</td>
            <td class="text-center">
              <select 
                class="form-select form-select-sm"
                :class="getStatusBadgeClass(booking.status)"
                v-model="booking.status"
                @change="quickUpdateStatus(booking)"
                style="width: auto; display: inline-block; cursor: pointer; font-weight: 500;"
              >
                <option value="รอยืนยัน">รอยืนยัน</option>
                <option value="ยืนยันแล้ว">ยืนยันแล้ว</option>
                <option value="ยกเลิก">ยกเลิก</option>
              </select>
            </td>
            <td class="text-center">
              <button 
                class="btn btn-sm btn-warning me-1" 
                @click="openEditModal(booking)"
                title="แก้ไข"
              >
                <i class="bi bi-pencil-square">แก้ไข</i>
              </button>
              <button 
                class="btn btn-sm btn-danger" 
                @click="deleteBooking(booking.booking_id)"
                title="ลบ"
              >
                <i class="bi bi-trash3">ลบ</i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">กำลังโหลด...</span>
      </div>
      <p class="mt-2">กำลังโหลดข้อมูล...</p>
    </div>
    
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- ✅ ระบบแบ่งหน้า -->
    <nav v-if="totalPages > 1" class="mt-3">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="prevPage">ก่อนหน้า</button>
        </li>

        <li
          class="page-item"
          v-for="page in totalPages"
          :key="page"
          :class="{ active: currentPage === page }"
        >
          <button class="page-link" @click="goToPage(page)">{{ page }}</button>
        </li>

        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <button class="page-link" @click="nextPage">ถัดไป</button>
        </li>
      </ul>
    </nav>

    <!-- ✅ Modal แก้ไขข้อมูล -->
    <div v-if="showModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">แก้ไขข้อมูลการจอง #{{ editForm.booking_id }}</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateBooking">
              <div class="mb-3">
                <label class="form-label">ชื่อลูกค้า *</label>
                <input v-model="editForm.customer_name" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">เบอร์โทร *</label>
                <input v-model="editForm.phone" type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">โซน *</label>
                <select v-model="editForm.zone" class="form-select" required>
                  <option value="หน้าต่าง">หน้าต่าง</option>
                  <option value="กลางร้าน">กลางร้าน</option>
                  <option value="มุมสบาย">มุมสบาย</option>
                  <option value="VIP">VIP</option>
                  <option value="ระเบียง">ระเบียง</option>
                </select>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">จำนวนคน *</label>
                  <input v-model.number="editForm.guests" type="number" min="1" class="form-control" required />
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">เวลา *</label>
                  <select v-model="editForm.time" class="form-select" required>
                    <option v-for="t in timeSlots" :key="t" :value="t">{{ t }} น.</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">วันที่จอง *</label>
                <input v-model="editForm.booking_date" type="date" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">สถานะ *</label>
                <select v-model="editForm.status" class="form-select" required>
                  <option value="รอยืนยัน">รอยืนยัน</option>
                  <option value="ยืนยันแล้ว">ยืนยันแล้ว</option>
                  <option value="ยกเลิก">ยกเลิก</option>
                </select>
              </div>

              <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-check-circle"></i> บันทึกการแก้ไข
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed, watch } from "vue";

export default {
  name: "BookingList",
  setup() {
    const bookings = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const showModal = ref(false);
    const statusFilter = ref("");
    const searchTerm = ref("");
    const editForm = ref({
      booking_id: null,
      customer_name: "",
      phone: "",
      zone: "",
      guests: 0,
      time: "",
      booking_date: "",
      status: ""
    });

    const timeSlots = [
      '11:00','11:30','12:00','12:30','13:00','13:30',
      '17:00','17:30','18:00','18:30','19:00','19:30','20:00'
    ];

    // Pagination
    const currentPage = ref(1);
    const itemsPerPage = ref(10);

    // กรองข้อมูล
    const filteredBookings = computed(() => {
      let filtered = bookings.value;

      // กรองตามสถานะ
      if (statusFilter.value !== "") {
        filtered = filtered.filter(b => b.status === statusFilter.value);
      }

      // ค้นหา
      if (searchTerm.value.trim() !== "") {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter(b => 
          b.customer_name.toLowerCase().includes(term) ||
          b.phone.includes(term) ||
          b.zone.toLowerCase().includes(term)
        );
      }

      return filtered;
    });

    const totalPages = computed(() =>
      Math.ceil(filteredBookings.value.length / itemsPerPage.value)
    );

    const paginatedBookings = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      return filteredBookings.value.slice(start, start + itemsPerPage.value);
    });

    const goToPage = (page) => {
      currentPage.value = page;
    };

    const nextPage = () => {
      if (currentPage.value < totalPages.value) currentPage.value++;
    };

    const prevPage = () => {
      if (currentPage.value > 1) currentPage.value--;
    };

    // รีเซ็ตหน้ากลับไปหน้า 1
    watch([itemsPerPage, statusFilter, searchTerm], () => {
      currentPage.value = 1;
    });

    // ฟอร์แมตวันที่
    const formatDate = (date) => {
      if (!date) return '-';
      const d = new Date(date);
      return d.toLocaleDateString('th-TH', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      });
    };

    // สีของ Badge สถานะ
    const getStatusBadgeClass = (status) => {
      switch(status) {
        case 'ยืนยันแล้ว': return 'bg-success';
        case 'รอยืนยัน': return 'bg-warning text-dark';
        case 'ยกเลิก': return 'bg-danger';
        default: return 'bg-secondary';
      }
    };

    // โหลดข้อมูล
    const fetchBookings = async () => {
      loading.value = true;
      error.value = null;
      try {
        const res = await fetch("http://localhost:8081/group/api_php/api_tablebooking.php");
        const data = await res.json();
        bookings.value = data.success ? data.data : [];
      } catch (err) {
        error.value = "ไม่สามารถโหลดข้อมูลได้: " + err.message;
      } finally {
        loading.value = false;
      }
    };

    const openEditModal = (booking) => {
      editForm.value = { ...booking };
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
    };

    // เปลี่ยนสถานะแบบเร็ว (Quick Update)
    const quickUpdateStatus = async (booking) => {
      try {
        const res = await fetch("http://localhost:8081/group/api_php/api_tablebooking.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "update_status",
            booking_id: booking.booking_id,
            status: booking.status
          })
        });
        const result = await res.json();
        
        if (result.success) {
          // แสดงการแจ้งเตือนแบบสั้น
          showNotification('✅ เปลี่ยนสถานะเป็น "' + booking.status + '" สำเร็จ');
          fetchBookings();
        } else {
          alert("❌ เกิดข้อผิดพลาด: " + (result.error || "ไม่สามารถเปลี่ยนสถานะได้"));
          fetchBookings(); // โหลดใหม่เพื่อ reset
        }
      } catch (err) {
        alert("❌ เกิดข้อผิดพลาด: " + err.message);
        fetchBookings();
      }
    };

    // แสดงการแจ้งเตือนแบบสั้น
    const showNotification = (message) => {
      // สร้าง toast notification
      const toast = document.createElement('div');
      toast.className = 'position-fixed top-0 end-0 m-3 alert alert-success alert-dismissible fade show';
      toast.setAttribute('role', 'alert');
      toast.style.zIndex = '9999';
      toast.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.remove();
      }, 3000);
    };

    const updateBooking = async () => {
      try {
        const res = await fetch("http://localhost:8081/group/api_php/api_tablebooking.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "update",
            ...editForm.value
          })
        });
        const result = await res.json();
        
        if (result.success) {
          alert("✅ อัพเดทข้อมูลสำเร็จ");
          fetchBookings();
          closeModal();
        } else {
          alert("❌ เกิดข้อผิดพลาด: " + (result.error || "ไม่สามารถอัพเดทได้"));
        }
      } catch (err) {
        alert("❌ เกิดข้อผิดพลาด: " + err.message);
      }
    };

    const deleteBooking = async (id) => {
      if (!confirm("คุณแน่ใจหรือไม่ที่จะลบการจองนี้?")) return;

      try {
        const res = await fetch("http://localhost:8081/group/api_php/api_tablebooking.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "delete",
            booking_id: id
          })
        });
        const result = await res.json();
        
        if (result.success) {
          alert("✅ ลบข้อมูลสำเร็จ");
          fetchBookings();
        } else {
          alert("❌ เกิดข้อผิดพลาด: " + (result.error || "ไม่สามารถลบได้"));
        }
      } catch (err) {
        alert("❌ เกิดข้อผิดพลาด: " + err.message);
      }
    };

    onMounted(fetchBookings);

    return {
      bookings,
      loading,
      error,
      showModal,
      statusFilter,
      searchTerm,
      editForm,
      timeSlots,
      currentPage,
      totalPages,
      paginatedBookings,
      itemsPerPage,
      goToPage,
      nextPage,
      prevPage,
      formatDate,
      getStatusBadgeClass,
      fetchBookings,
      quickUpdateStatus,
      openEditModal,
      closeModal,
      updateBooking,
      deleteBooking
    };
  }
};
</script>

<style scoped>
.badge { 
  font-size: 0.85rem; 
  padding: 0.4em 0.8em;
}

.table-hover tbody tr:hover {
  background-color: #f0f9f4;
  cursor: pointer;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}

/* สไตล์ของ Select สถานะ */
.form-select.bg-success {
  background-color: #28a745 !important;
  color: white !important;
  border-color: #28a745 !important;
}

.form-select.bg-warning {
  background-color: #ffc107 !important;
  color: #000 !important;
  border-color: #ffc107 !important;
}

.form-select.bg-danger {
  background-color: #dc3545 !important;
  color: white !important;
  border-color: #dc3545 !important;
}

.form-select:focus {
  box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>