<template>
  <div class="table-booking">
    <!-- Container กลาง -->
    <div class="container-center">
      <!-- Header -->
      <div class="header-section">
        <h1 class="title">🌿The Vegetable</h1>
        <p class="subtitle">ร้านอาหารคลีน เพื่อสุขภาพที่ดี</p>
      </div>

      <!-- ✨ แสดงรายการอาหาร (ถ้ามี) -->
      <div v-if="cart.length > 0" class="cart-summary-card">
        <h3 class="cart-title">🧺 รายการอาหารที่สั่ง</h3>
        <div class="cart-items">
          <div v-for="(item, index) in cart" :key="index" class="cart-item">
            <div class="item-info">
              <span class="item-name">{{ item.product_name }}</span>
              <span class="item-detail">฿{{ item.price }} × {{ item.quantity }}</span>
            </div>
            <span class="item-total">฿{{ (item.price * item.quantity).toFixed(2) }}</span>
          </div>
        </div>
        <div class="cart-total">
          <span class="total-label">ยอดรวมทั้งหมด:</span>
          <span class="total-amount">฿{{ totalPrice.toFixed(2) }}</span>
        </div>
      </div>

      <!-- Card หลัก -->
      <div class="main-card">
        <!-- STEP 1 เลือกโซน + จำนวนคน + เวลา -->
        <div v-if="step === 1" class="step-section">
          <div class="section-header">
            <h2>📋 เลือกโซนและเวลา</h2>
          </div>

          <div class="filter-section">
            <label class="filter-label">🗂 โซน:</label>
            <div class="btn-group">
              <button
                v-for="zone in zones"
                :key="zone"
                :class="['btn-filter', { active: booking.zone === zone }]"
                @click="booking.zone = zone">
                {{ zone }}
              </button>
            </div>
          </div>

          <div class="filter-section">
            <label class="filter-label">👥 จำนวนผู้เข้าใช้บริการ:</label>
            <select class="form-control custom-input" v-model="booking.guests">
              <option value="">เลือกจำนวนคน</option>
              <option v-for="n in 10" :key="n" :value="n">{{ n }} คน</option>
            </select>
          </div>

          <div class="filter-section">
            <label class="filter-label">⏰ เวลา:</label>
            <select class="form-control custom-input" v-model="booking.time">
              <option value="">เลือกเวลา</option>
              <option v-for="t in timeSlots" :key="t" :value="t">{{ t }} น.</option>
            </select>
          </div>

          <button class="btn-next" @click="goToStep2" :disabled="!step1Valid">
            ถัดไป →
          </button>
        </div>

        <!-- STEP 2 ลงชื่อก่อนจอง -->
        <div v-if="step === 2" class="step-section">
          <div class="section-header">
            <h2>📝 กรอกข้อมูลลูกค้า</h2>
          </div>

          <div class="form-group">
            <label class="form-label">ชื่อ-นามสกุล *</label>
            <input 
              class="form-control custom-input" 
              v-model="booking.name" 
              placeholder="กรุณากรอกชื่อ-นามสกุล" 
            />
          </div>

          <div class="form-group">
            <label class="form-label">เบอร์โทรศัพท์ *</label>
            <input 
              class="form-control custom-input" 
              v-model="booking.phone" 
              placeholder="0XX-XXX-XXXX" 
            />
          </div>

          <div class="button-group">
            <button class="btn-back" @click="step = 1">← ย้อนกลับ</button>
            <button class="btn-confirm" @click="confirmBooking" :disabled="!step2Valid || isLoading">
              {{ isLoading ? '⏳ กำลังบันทึก...' : '✓ ยืนยันการจอง' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL SUCCESS -->
    <div v-if="showSuccessModal" class="modal-overlay" @click="closeSuccessModal">
      <div class="modal-content" @click.stop>
        <div class="success-icon">✓</div>
        <h2 class="success-title">จองสำเร็จ!</h2>
        <p class="success-message">ขอบคุณคุณ {{ booking.name }} ที่ใช้บริการ</p>

        <div class="summary-box">
          <p class="summary-text">🗂 โซน: {{ booking.zone }}</p>
          <p class="summary-text">👥 จำนวน: {{ booking.guests }} คน</p>
          <p class="summary-text">⏰ เวลา: {{ booking.time }} น.</p>
          <p class="summary-text">📞 เบอร์: {{ booking.phone }}</p>
          <p v-if="cart.length > 0" class="summary-text highlight">💰 ยอดรวมอาหาร: ฿{{ totalPrice.toFixed(2) }}</p>
        </div>
        
        <button class="btn-close-modal" @click="closeSuccessModal">ปิด</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      step: 1,
      showSuccessModal: false,
      isLoading: false,
      zones: ['หน้าต่าง', 'กลางร้าน', 'มุมสบาย', 'VIP', 'ระเบียง'],
      timeSlots: [
        '11:00','11:30','12:00','12:30','13:00','13:30',
        '17:00','17:30','18:00','18:30','19:00','19:30','20:00'
      ],
      booking: {
        zone: '',
        guests: '',
        time: '',
        name: '',
        phone: ''
      },
      cart: [],
      totalPrice: 0,
      orderType: ''
    }
  },
  computed: {
    step1Valid() {
      return this.booking.zone && this.booking.guests && this.booking.time;
    },
    step2Valid() {
      return this.booking.name && this.booking.phone;
    }
  },
  mounted() {
    const savedCart = localStorage.getItem('cart');
    const savedTotal = localStorage.getItem('totalPrice');
    const savedOrderType = localStorage.getItem('orderType');

    if (savedCart) {
      this.cart = JSON.parse(savedCart);
    }

    if (savedTotal) {
      this.totalPrice = parseFloat(savedTotal);
    }

    if (savedOrderType) {
      this.orderType = savedOrderType;
    }
  },
  methods: {
    goToStep2() {
      if (this.step1Valid) this.step = 2;
    },
    
    async confirmBooking() {
      if (!this.step2Valid || this.isLoading) return;

      this.isLoading = true;

      try {
        const user_id = localStorage.getItem('user_id');
        
        if (!user_id) {
          alert('⚠️ กรุณาเข้าสู่ระบบก่อนจองโต๊ะ');
          this.isLoading = false;
          return;
        }
        
        console.log('📤 กำลังบันทึกการจองโต๊ะ:', {
          user_id,
          zone: this.booking.zone,
          guests: this.booking.guests,
          time: this.booking.time,
          name: this.booking.name,
          phone: this.booking.phone
        });

        // 1️⃣ บันทึกการจองโต๊ะ
        const bookingResponse = await fetch('http://localhost:8081/group/api_php/api_tablebooking.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'add',
            user_id: user_id,
            zone: this.booking.zone,
            guests: this.booking.guests,
            time: this.booking.time,
            name: this.booking.name,
            phone: this.booking.phone,
            booking_date: new Date().toISOString().split('T')[0]
          })
        });

        const bookingResult = await bookingResponse.json();
        console.log('📥 ผลการจองโต๊ะ:', bookingResult);
        
        if (!bookingResult.success) {
          alert('❌ เกิดข้อผิดพลาดในการจองโต๊ะ: ' + (bookingResult.error || bookingResult.message));
          this.isLoading = false;
          return;
        }

        // 2️⃣ ถ้ามีอาหารในตะกร้า → บันทึกออเดอร์อาหารด้วย
        if (this.cart.length > 0) {
          console.log('🍽️ กำลังบันทึกออเดอร์อาหาร:', {
            user_id,
            table_no: this.booking.zone,
            items: this.cart,
            total: this.totalPrice
          });

          const orderResponse = await fetch('http://localhost:8081/group/api_php/order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              user_id: user_id,
              table_no: this.booking.zone,
              items: this.cart,
              total: this.totalPrice
            })
          });

          const orderResult = await orderResponse.json();
          console.log('📥 ผลการสั่งอาหาร:', orderResult);

          if (!orderResult.success) {
            alert('⚠️ จองโต๊ะสำเร็จ แต่บันทึกออเดอร์อาหารไม่สำเร็จ: ' + orderResult.message);
          }
        }

        // 3️⃣ แสดง Modal สำเร็จ
        this.showSuccessModal = true;
        
        // ล้าง localStorage
        localStorage.removeItem('cart');
        localStorage.removeItem('totalPrice');
        localStorage.removeItem('orderType');

      } catch (error) {
        alert('❌ เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message);
        console.error('Error:', error);
      } finally {
        this.isLoading = false;
      }
    },

    closeSuccessModal() {
      this.showSuccessModal = false;
      this.step = 1;
      this.booking = { zone: '', guests: '', time: '', name: '', phone: '' };
      this.cart = [];
      this.totalPrice = 0;
      
      // ✅ ไปหน้า Profile หลังจองสำเร็จ
      this.$router.push('/cus');
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap');

* {
  font-family: 'Prompt', sans-serif;
}

.table-booking {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f9f4 0%, #e8f5e9 100%);
  padding: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.container-center {
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
}

.header-section {
  text-align: center;
  margin-bottom: 30px;
}

.title {
  font-size: 48px;
  color: #2d5016;
  font-weight: 600;
  margin: 0;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.subtitle {
  margin-top: 5px;
  font-size: 18px;
  color: #5a7c3e;
}

.cart-summary-card {
  background: linear-gradient(135deg, #fff9e6 0%, #fff4d6 100%);
  border-radius: 20px;
  padding: 25px;
  margin-bottom: 25px;
  box-shadow: 0 8px 25px rgba(45, 80, 22, 0.12);
  border: 2px solid #ffe082;
}

.cart-title {
  color: #2d5016;
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 20px;
  text-align: center;
}

.cart-items {
  margin-bottom: 20px;
}

.cart-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: white;
  border-radius: 12px;
  margin-bottom: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.item-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.item-name {
  font-weight: 500;
  color: #2d5016;
  font-size: 16px;
}

.item-detail {
  font-size: 14px;
  color: #5a7c3e;
}

.item-total {
  font-weight: 600;
  color: #43a047;
  font-size: 18px;
}

.cart-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: white;
  border-radius: 14px;
  border: 2px dashed #ffb74d;
}

.total-label {
  font-weight: 600;
  color: #2d5016;
  font-size: 18px;
}

.total-amount {
  font-weight: 700;
  color: #43a047;
  font-size: 24px;
}

.main-card {
  background: white;
  border-radius: 24px;
  padding: 35px;
  box-shadow: 0 10px 40px rgba(45, 80, 22, 0.15);
  border: 2px solid #d4e7c5;
}

.section-header h2 {
  color: #2d5016;
  font-weight: 600;
  font-size: 24px;
  text-align: center;
  margin-bottom: 25px;
}

.filter-section {
  margin-bottom: 20px;
}

.filter-label {
  display: block;
  color: #2d5016;
  font-weight: 500;
  margin-bottom: 12px;
  font-size: 16px;
}

.btn-group {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
}

.btn-filter {
  background: #e8f5e9;
  color: #2d5016;
  border-radius: 25px;
  padding: 10px 20px;
  border: 2px solid #c8e6c9;
  transition: all 0.3s ease;
  cursor: pointer;
  font-weight: 500;
  font-size: 15px;
}

.btn-filter:hover {
  background: #c8e6c9;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(45, 80, 22, 0.2);
}

.btn-filter.active {
  background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);
  color: white;
  border-color: #43a047;
  box-shadow: 0 4px 15px rgba(67, 160, 71, 0.4);
  transform: scale(1.05);
}

.custom-input {
  width: 100%;
  border-radius: 14px;
  padding: 12px 18px;
  border: 2px solid #c8e6c9;
  background: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.custom-input:focus {
  outline: none;
  border-color: #66bb6a;
  box-shadow: 0 0 0 3px rgba(102, 187, 106, 0.1);
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  color: #2d5016;
  font-weight: 500;
  margin-bottom: 10px;
  font-size: 16px;
}

.btn-next, .btn-confirm {
  width: 100%;
  background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);
  color: white;
  border: none;
  padding: 14px 28px;
  border-radius: 16px;
  font-size: 18px;
  font-weight: 600;
  margin-top: 20px;
  transition: all 0.3s ease;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(67, 160, 71, 0.3);
}

.btn-next:hover, .btn-confirm:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(67, 160, 71, 0.4);
}

.btn-next:disabled, .btn-confirm:disabled {
  background: #a5d6a7;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.button-group {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}

.btn-back {
  flex: 1;
  background: #e0e0e0;
  color: #424242;
  border: none;
  padding: 14px 24px;
  border-radius: 16px;
  font-size: 16px;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
}

.btn-back:hover {
  background: #bdbdbd;
  transform: translateY(-2px);
}

.btn-confirm {
  flex: 2;
  margin-top: 0;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  backdrop-filter: blur(6px);
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.3s;
  z-index: 1000;
}

.modal-content {
  background: #ffffff;
  padding: 40px;
  width: 90%;
  max-width: 420px;
  border-radius: 24px;
  box-shadow: 0 15px 50px rgba(0,0,0,0.3);
  text-align: center;
  animation: scaleIn 0.3s ease;
  border: 2px solid #c8e6c9;
}

.success-icon {
  font-size: 72px;
  color: #66bb6a;
  margin-bottom: 15px;
  animation: bounce 0.6s;
}

.success-title {
  font-size: 28px;
  color: #2d5016;
  font-weight: 600;
  margin-bottom: 10px;
}

.success-message {
  color: #5a7c3e;
  font-size: 16px;
  margin-bottom: 25px;
}

.summary-box {
  background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 25px;
  text-align: left;
  border: 2px solid #c8e6c9;
}

.summary-text {
  color: #2d5016;
  margin: 10px 0;
  font-size: 16px;
  font-weight: 500;
}

.summary-text.highlight {
  color: #43a047;
  font-weight: 600;
  font-size: 18px;
}

.btn-close-modal {
  width: 100%;
  background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);
  color: white;
  padding: 12px 28px;
  border-radius: 14px;
  border: none;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(67, 160, 71, 0.3);
}

.btn-close-modal:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(67, 160, 71, 0.4);
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes scaleIn {
  from {
    transform: scale(0.9);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

@media (max-width: 768px) {
  .title {
    font-size: 36px;
  }

  .main-card {
    padding: 25px;
  }

  .button-group {
    flex-direction: column;
  }

  .btn-confirm {
    flex: 1;
  }
}
</style>