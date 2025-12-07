<template>
  <div class="profile-page">
    <div class="container py-5">
      <div class="row">
        
        <!-- Sidebar: Profile Info -->
        <div class="col-lg-4 mb-4">
          <div class="card profile-card shadow-sm">
            <div class="card-body text-center p-4">
              
              <!-- Profile Picture -->
              <div class="profile-avatar-wrapper mb-3">
                <img 
                  :src="profileData.avatar || defaultAvatar" 
                  alt="Profile" 
                  class="profile-avatar"
                >
                <label for="avatar-upload" class="avatar-edit-btn">
                  <i class="bi bi-camera-fill"></i>
                  <input 
                    type="file" 
                    id="avatar-upload" 
                    accept="image/*" 
                    @change="handleAvatarUpload"
                    style="display: none;"
                  >
                </label>
              </div>

              <!-- User Info -->
              <h3 class="profile-name">{{ profileData.name }}</h3>
              <p class="profile-email">{{ profileData.email }}</p>
              <span class="badge bg-success">ลูกค้า</span>

              <!-- Last Active -->
              <div class="last-active mt-3">
                <small class="text-muted">
                  <i class="bi bi-clock"></i> ใช้งานล่าสุด: {{ lastActive }}
                </small>
              </div>

              <!-- Edit Profile Button -->
              <button 
                class="btn btn-outline-success w-100 mt-3" 
                @click="showEditModal = true"
              >
                <i class="bi bi-pencil"></i> แก้ไขโปรไฟล์
              </button>
            </div>
          </div>
        </div>

        <!-- Main Content: History -->
        <div class="col-lg-8">
          
          <!-- Tab Navigation -->
          <ul class="nav nav-tabs history-tabs mb-4">
            <li class="nav-item">
              <a 
                class="nav-link" 
                :class="{ active: activeTab === 'orders' }"
                @click="activeTab = 'orders'"
                href="javascript:void(0)"
              >
                <i class="bi bi-basket"></i> ประวัติการสั่งอาหาร
              </a>
            </li>
            <li class="nav-item">
              <a 
                class="nav-link" 
                :class="{ active: activeTab === 'bookings' }"
                @click="activeTab = 'bookings'"
                href="javascript:void(0)"
              >
                <i class="bi bi-calendar-check"></i> ประวัติการจองโต๊ะ
              </a>
            </li>
          </ul>

          <!-- Orders History -->
          <div v-if="activeTab === 'orders'" class="history-content">
            <div v-if="loadingOrders" class="text-center py-5">
              <div class="spinner-border text-success"></div>
            </div>
            
            <div v-else-if="orders.length === 0" class="empty-state">
              <i class="bi bi-basket empty-icon"></i>
              <p>ยังไม่มีประวัติการสั่งอาหาร</p>
            </div>

            <div v-else>
              <div 
                v-for="order in orders" 
                :key="order.order_id" 
                class="history-card mb-3"
              >
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h5 class="history-title">คำสั่งซื้อ #{{ order.order_id }}</h5>
                    <p class="history-date">
                      <i class="bi bi-calendar"></i> {{ formatDate(order.order_date) }}
                    </p>
                    <p class="history-info">
                      <i class="bi bi-geo-alt"></i> {{ order.table_no || 'ไม่ระบุ' }}
                    </p>
                  </div>
                  <span 
                    class="badge" 
                    :class="getStatusClass(order.status)"
                  >
                    {{ order.status }}
                  </span>
                </div>
                
                <div class="order-items mt-2">
                  <div 
                    v-for="item in order.items" 
                    :key="item.id"
                    class="order-item"
                  >
                    <span>{{ item.product_name }} x{{ item.quantity }}</span>
                    <span class="text-success fw-bold">฿{{ item.price }}</span>
                  </div>
                </div>
                
                <div class="history-total">
                  ยอดรวม: <strong>฿{{ order.total }}</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Bookings History -->
          <div v-if="activeTab === 'bookings'" class="history-content">
            <div v-if="loadingBookings" class="text-center py-5">
              <div class="spinner-border text-success"></div>
            </div>
            
            <div v-else-if="bookings.length === 0" class="empty-state">
              <i class="bi bi-calendar-x empty-icon"></i>
              <p>ยังไม่มีประวัติการจองโต๊ะ</p>
            </div>

            <div v-else>
              <div 
                v-for="booking in bookings" 
                :key="booking.booking_id" 
                class="history-card mb-3"
              >
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h5 class="history-title">
                      <i class="bi bi-table"></i> โซน {{ booking.table_number }}
                    </h5>
                    <p class="history-date">
                      <i class="bi bi-calendar"></i> {{ formatDate(booking.booking_date) }}
                    </p>
                    <p class="history-time">
                      <i class="bi bi-clock"></i> {{ booking.booking_time }} น.
                    </p>
                    <p class="history-guests">
                      <i class="bi bi-people"></i> {{ booking.guests }} ท่าน
                    </p>
                    <p class="history-info">
                      <i class="bi bi-person"></i> {{ booking.customer_name }}
                    </p>
                    <p class="history-info">
                      <i class="bi bi-telephone"></i> {{ booking.phone }}
                    </p>
                  </div>
                  <span 
                    class="badge" 
                    :class="getBookingStatusClass(booking.status)"
                  >
                    {{ booking.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <div 
      v-if="showEditModal" 
      class="modal fade show d-block" 
      style="background: rgba(0,0,0,0.5);"
      @click.self="showEditModal = false"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">แก้ไขโปรไฟล์</h5>
            <button 
              type="button" 
              class="btn-close" 
              @click="showEditModal = false"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateProfile">
              <div class="mb-3">
                <label class="form-label">ชื่อ-นามสกุล</label>
                <input 
                  v-model="editForm.name" 
                  type="text" 
                  class="form-control"
                  required
                >
              </div>
              <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input 
                  v-model="editForm.email" 
                  type="email" 
                  class="form-control"
                  disabled
                >
                <small class="text-muted">ไม่สามารถเปลี่ยนอีเมลได้</small>
              </div>
              <button type="submit" class="btn btn-success w-100">
                บันทึกการเปลี่ยนแปลง
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';

export default {
  name: 'CustomerProfile',
  setup() {
    const profileData = ref({
      name: '',
      email: '',
      avatar: '',
      user_id: null
    });

    const orders = ref([]);
    const bookings = ref([]);
    const loadingOrders = ref(true);
    const loadingBookings = ref(true);
    const activeTab = ref('bookings');
    const showEditModal = ref(false);
    
    const editForm = ref({
      name: '',
      email: ''
    });

    const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=4a7c2c&color=fff&size=200';

    // โหลดข้อมูลโปรไฟล์
    const loadProfile = () => {
      const name = localStorage.getItem('username') || 'ผู้ใช้';
      const email = localStorage.getItem('email') || 'user@example.com';
      const user_id = localStorage.getItem('user_id');
      
      console.log('👤 Profile Data:', { name, email, user_id });
      
      profileData.value = {
        name,
        email,
        avatar: localStorage.getItem('avatar') || '',
        user_id
      };

      editForm.value = {
        name,
        email
      };
    };

    // โหลดประวัติการสั่งอาหาร
    const loadOrders = async () => {
      loadingOrders.value = true;
      try {
        const user_id = localStorage.getItem('user_id');
        console.log('🍔 Loading Orders for user_id:', user_id);
        
        const response = await fetch(`http://localhost:8081/group/api_php/order.php?user_id=${user_id}`);
        const data = await response.json();
        
        console.log('📦 Orders Response:', data);
        
        if (data.success) {
          orders.value = data.orders || [];
        } else {
          console.error('❌ Orders Error:', data.message);
        }
      } catch (error) {
        console.error('💥 Orders Fetch Error:', error);
      } finally {
        loadingOrders.value = false;
      }
    };

    // โหลดประวัติการจองโต๊ะ
    const loadBookings = async () => {
      loadingBookings.value = true;
      try {
        const user_id = localStorage.getItem('user_id');
        console.log('📅 Loading Bookings for user_id:', user_id);
        
        if (!user_id) {
          console.error('❌ ไม่พบ user_id ใน localStorage');
          alert('⚠️ กรุณาเข้าสู่ระบบใหม่อีกครั้ง');
          loadingBookings.value = false;
          return;
        }
        
        const url = `http://localhost:8081/group/api_php/api_tablebooking.php?action=customer_history&user_id=${user_id}`;
        console.log('🔗 API URL:', url);
        
        const response = await fetch(url);
        const data = await response.json();
        
        console.log('📦 Bookings Response:', data);
        
        if (data.success) {
          bookings.value = data.bookings || [];
          console.log('✅ จำนวนการจอง:', bookings.value.length);
        } else {
          console.error('❌ Bookings Error:', data.message);
          alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถโหลดข้อมูลได้'));
        }
      } catch (error) {
        console.error('💥 Bookings Fetch Error:', error);
        alert('เกิดข้อผิดพลาดในการเชื่อมต่อ API');
      } finally {
        loadingBookings.value = false;
      }
    };

    // อัปโหลดรูปโปรไฟล์
    const handleAvatarUpload = (event) => {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          profileData.value.avatar = e.target.result;
          localStorage.setItem('avatar', e.target.result);
        };
        reader.readAsDataURL(file);
      }
    };

    // อัปเดตโปรไฟล์
    const updateProfile = () => {
      profileData.value.name = editForm.value.name;
      localStorage.setItem('username', editForm.value.name);
      showEditModal.value = false;
      
      alert('✅ บันทึกการเปลี่ยนแปลงเรียบร้อย');
    };

    // Format วันที่
    const formatDate = (dateString) => {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    // สถานะคำสั่งซื้อ (รองรับทั้งภาษาไทยและอังกฤษ)
    const getStatusClass = (status) => {
      const classes = {
        'pending': 'bg-warning',
        'confirmed': 'bg-info',
        'completed': 'bg-success',
        'cancelled': 'bg-danger',
        'รอดำเนินการ': 'bg-warning',
        'ยืนยันแล้ว': 'bg-info',
        'สำเร็จ': 'bg-success',
        'ยกเลิก': 'bg-danger'
      };
      return classes[status] || 'bg-secondary';
    };

    // สถานะการจอง (รองรับภาษาไทย)
    const getBookingStatusClass = (status) => {
      const classes = {
        'รอยืนยัน': 'bg-warning text-dark',
        'ยืนยันแล้ว': 'bg-success',
        'ยกเลิก': 'bg-danger',
        'pending': 'bg-warning text-dark',
        'confirmed': 'bg-success',
        'cancelled': 'bg-danger'
      };
      return classes[status] || 'bg-secondary';
    };

    // วันที่ใช้งานล่าสุด
    const lastActive = computed(() => {
      const lastLogin = localStorage.getItem('last_login');
      if (lastLogin) {
        return formatDate(lastLogin);
      }
      return formatDate(new Date());
    });

    onMounted(() => {
      loadProfile();
      loadOrders();
      loadBookings();
      
      // ถ้ามี query parameter ?refresh=1 ให้เปลี่ยนไปแท็บ orders
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('refresh') === '1') {
        activeTab.value = 'orders';
      }
    });

    return {
      profileData,
      orders,
      bookings,
      loadingOrders,
      loadingBookings,
      activeTab,
      showEditModal,
      editForm,
      defaultAvatar,
      lastActive,
      handleAvatarUpload,
      updateProfile,
      formatDate,
      getStatusClass,
      getBookingStatusClass
    };
  }
};
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  background: #f8f9fa;
}

/* Profile Card */
.profile-card {
  border: none;
  border-radius: 15px;
}

.profile-avatar-wrapper {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto;
}

.profile-avatar {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid #4a7c2c;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.avatar-edit-btn {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background: #4a7c2c;
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.avatar-edit-btn:hover {
  background: #3d6624;
  transform: scale(1.1);
}

.profile-name {
  color: #2d5016;
  font-weight: 600;
  margin-top: 15px;
  margin-bottom: 5px;
}

.profile-email {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 10px;
}

.last-active {
  padding: 10px;
  background: #f8f9fa;
  border-radius: 8px;
}

/* History Tabs */
.history-tabs {
  border-bottom: 2px solid #e0e0e0;
}

.history-tabs .nav-link {
  color: #666;
  font-weight: 500;
  border: none;
  padding: 12px 24px;
  transition: all 0.3s;
}

.history-tabs .nav-link.active {
  color: #4a7c2c;
  border-bottom: 3px solid #4a7c2c;
  background: none;
}

.history-tabs .nav-link:hover {
  color: #4a7c2c;
}

/* History Cards */
.history-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  transition: all 0.3s;
}

.history-card:hover {
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.history-title {
  color: #2d5016;
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 8px;
}

.history-date,
.history-time,
.history-guests,
.history-info {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 5px;
}

.order-items {
  border-top: 1px solid #f0f0f0;
  padding-top: 12px;
}

.order-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  font-size: 0.95rem;
}

.history-total {
  text-align: right;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 2px solid #f0f0f0;
  font-size: 1.1rem;
  color: #2d5016;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}

.empty-icon {
  font-size: 4rem;
  color: #ddd;
  margin-bottom: 20px;
}

/* Modal */
.modal.show {
  display: block !important;
}

/* Bootstrap Icons */
@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css');
</style>