<template>
  <div class="home-page">
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <h1 class="hero-title">The Vegetable</h1>
        <div class="hero-line"></div>
        <p class="hero-subtitle">Premium Organic Restaurant</p>
        <p class="hero-description">สดใหม่ทุกวัน · ออร์แกนิค · รสชาติเข้มข้น</p>
      </div>
    </div>

    <div class="container my-5 px-4">
      <!-- About Section -->
      <div class="about-section">
        <h2 class="section-title">เกี่ยวกับเรา</h2>
        <div class="title-underline"></div>
        <p class="about-text">
          <strong>The Vegetable</strong> คือร้านอาหารเพื่อสุขภาพที่เน้นความสดใหม่ของวัตถุดิบ
          เรานำผักออร์แกนิคคุณภาพสูงมาปรุงเป็นเมนูอาหารแสนอร่อย
          ด้วยสูตรพิเศษที่ผสมผสานระหว่างรสชาติไทยแท้และความทันสมัย
          เพื่อสุขภาพที่ดีของคุณและคนที่คุณรัก
        </p>
      </div>

      <!-- Popular Menu Section -->
      <div class="popular-menu-section mt-5">
        <h2 class="section-title text-center">เมนูยอดนิยม</h2>
        <div class="title-underline mx-auto"></div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">กำลังโหลด...</span>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <!-- Menu Grid -->
        <div v-else class="row g-4 mt-4">
          <div
            v-for="menu in popularMenus"
            :key="menu.product_id"
            class="col-lg-4 col-md-6"
          >
            <div class="menu-card" @click="handleMenuClick">
              <div class="menu-image-wrapper">
                <img
                  v-if="menu.image"
                  :src="getImageUrl(menu.image)"
                  :alt="menu.product_name"
                  class="menu-image"
                />
                <div v-else class="menu-image-placeholder">
                  <span class="placeholder-icon">🥗</span>
                </div>
              </div>
              <div class="menu-details">
                <h3 class="menu-name">{{ menu.product_name }}</h3>
                <div class="menu-footer">
                  <span class="menu-price">{{ menu.price }} ฿</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

     
       
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
      <div class="container py-4">
        <div class="footer-content">
          <p class="footer-text">📍 เปิดทุกวัน 10:00 - 22:00 น.</p>
          <p class="footer-text">☎️ โทร: 02-233-5647</p>
          <p class="footer-copyright">© 2025 The Vegetable - All rights reserved</p>
        </div>
      </div>
    </footer>
</template>

<script>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

export default {
  name: "RestaurantHome",
  setup() {
    const router = useRouter();
    const popularMenus = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const isLoggedIn = ref(false);

    // ⭐ ตรวจสอบสถานะ Login
    const checkLoginStatus = () => {
      isLoggedIn.value = localStorage.getItem("role") !== null;
    };

    // โหลดข้อมูลเมนู
    const fetchPopularMenus = async () => {
      try {
        const response = await fetch(
          "http://localhost:8081/group/api_php/show_product.php"
        );
        const result = await response.json();

        if (result.success) {
          popularMenus.value = result.data.slice(0, 6);
        } else {
          error.value = result.message || "ไม่สามารถโหลดข้อมูลได้";
        }
      } catch (err) {
        error.value = "เกิดข้อผิดพลาด: " + err.message;
      } finally {
        loading.value = false;
      }
    };

    // สร้าง URL รูปภาพ
    const getImageUrl = (imageName) => {
      return `http://localhost:8081/group/api_php/uploads/${imageName}`;
    };

    // ⭐ ฟังก์ชันเมื่อคลิกเมนู
    const handleMenuClick = () => {
      if (!isLoggedIn.value) {
        // ❌ ยังไม่ล็อกอิน → ไปหน้า Login
        router.push('/login');
      } else {
        // ✅ ล็อกอินแล้ว → ไปหน้าเมนูเลย
        router.push('/t');
      }
    };

    // ไปหน้า Login
    const goToLogin = () => {
      router.push('/login');
    };

    // โหลดข้อมูลตอนเริ่มต้น
    onMounted(() => {
      checkLoginStatus();
      fetchPopularMenus();
    });

    return {
      popularMenus,
      loading,
      error,
      isLoggedIn,
      getImageUrl,
      handleMenuClick,
      goToLogin,
    };
  },
};
</script>

<style scoped>
/* ===== Global Styles ===== */
.home-page {
  background: #ffffff;
  min-height: 100vh;
}

/* ===== Hero Section ===== */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #bdc2ba 0%, #749561 50%, #5a9138 100%);
  min-height: 500px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 30% 50%, rgba(106, 169, 127, 0.1) 0%, transparent 50%);
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: #310b0b;
  padding: 40px 20px;
}

.hero-title {
  font-size: 4.5rem;
  font-weight: 300;
  letter-spacing: 3px;
  margin-bottom: 20px;
  text-shadow: 0 2px 20px rgba(105, 0, 0, 0.2);
}

.hero-line {
  width: 100px;
  height: 3px;
  background: #e2e2e2;
  margin: 0 auto 25px;
  box-shadow: 0 0 15px rgba(100, 75, 75, 0.5);
}

.hero-subtitle {
  font-size: 1.3rem;
  font-weight: 400;
  letter-spacing: 2px;
  margin-bottom: 10px;
  text-transform: uppercase;
  opacity: 0.95;
}

.hero-description {
  font-size: 1rem;
  font-weight: 300;
  opacity: 0.85;
  letter-spacing: 1px;
}

/* ===== About Section ===== */
.about-section {
  background: #e8e8e8;
  border-radius: 12px;
  padding: 50px;
  box-shadow: 0 4px 20px rgba(255, 255, 255, 0.08);
  margin-top: 50px;
  border: 2px solid #000000;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 300;
  color: #2d5016;
  margin-bottom: 20px;
  letter-spacing: 1px;
  text-align: center;
}

.about-text {
  font-size: 1.1rem;
  line-height: 1.9;
  color: #555;
  text-align: center;
  max-width: 900px;
  margin: 0 auto;
}

.about-text strong {
  color: #2d5016;
  font-weight: 500;
}

/* ===== Popular Menu Section ===== */
.popular-menu-section {
  margin-top: 80px;
}

.menu-card {
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  height: 100%;
}

.menu-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 35px rgba(45, 80, 22, 0.15);
}

.menu-image-wrapper {
  width: 100%;
  height: 280px;
  overflow: hidden;
  background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
  position: relative;
}

.menu-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.menu-card:hover .menu-image {
  transform: scale(1.08);
}

.menu-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
}

.placeholder-icon {
  font-size: 5rem;
  opacity: 0.6;
}

.menu-details {
  padding: 25px;
}

.menu-name {
  font-size: 1.3rem;
  font-weight: 400;
  color: #2d5016;
  margin-bottom: 20px;
  letter-spacing: 0.5px;
}

.menu-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.menu-price {
  font-size: 1.8rem;
  font-weight: 500;
  color: #4a7c2c;
  letter-spacing: 0.5px;
}

.btn-order {
  background: linear-gradient(135deg, #4a7c2c 0%, #5a9138 100%);
  color: #ffffff;
  border: none;
  padding: 10px 28px;
  border-radius: 25px;
  font-size: 0.95rem;
  font-weight: 400;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(74, 124, 44, 0.2);
}

.btn-order:hover {
  background: linear-gradient(135deg, #3d6624 0%, #4a7c2c 100%);
  box-shadow: 0 6px 20px rgba(74, 124, 44, 0.3);
  transform: translateY(-2px);
}

/* ===== Login Notice ===== */
.login-notice {
  background: #ffffff;
  border: 2px solid #e8e8e8;
  border-radius: 12px;
  padding: 35px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.notice-content {
  display: flex;
  align-items: center;
  gap: 25px;
}

.notice-icon {
  font-size: 3rem;
  flex-shrink: 0;
}

.notice-title {
  font-size: 1.4rem;
  font-weight: 500;
  color: #2d5016;
  margin-bottom: 8px;
  letter-spacing: 0.5px;
}

.notice-description {
  color: #666;
  margin-bottom: 15px;
  line-height: 1.6;
}

.btn-login {
  background: linear-gradient(135deg, #4a7c2c 0%, #5a9138 100%);
  color: #ffffff;
  border: none;
  padding: 12px 35px;
  border-radius: 25px;
  font-weight: 400;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(74, 124, 44, 0.25);
}

.btn-login:hover {
  background: linear-gradient(135deg, #3d6624 0%, #4a7c2c 100%);
  box-shadow: 0 6px 20px rgba(74, 124, 44, 0.35);
  transform: translateY(-2px);
}

/* ===== Footer ===== */
.footer {
  background: #ffffff;
  color: #2d5016;
  border-top: 2px solid #f0f0f0;
  box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
}

.footer-content {
  text-align: center;
}

.footer-text {
  font-size: 1rem;
  margin-bottom: 8px;
  opacity: 0.85;
  letter-spacing: 0.5px;
  color: #555;
}

.footer-copyright {
  font-size: 0.9rem;
  margin-top: 15px;
  opacity: 0.6;
  letter-spacing: 0.5px;
  color: #777;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .hero-title {
    font-size: 3rem;
  }
  
  .about-section {
    padding: 30px 20px;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .notice-content {
    flex-direction: column;
    text-align: center;
  }
}
</style>