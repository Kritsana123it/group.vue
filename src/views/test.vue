<template>
  <div class="container my-5">
    <!-- Search Bar และ Sort -->
    <div class="row mb-4 align-items-center">
      <div class="col-md-8">
        <div class="search-box">
          <input 
            type="text" 
            class="form-control form-control-lg" 
            placeholder="ค้นหาสินค้า..."
            v-model="searchQuery"
          />
        </div>
      </div>
      <div class="col-md-4">
        <select class="form-select form-select-lg" v-model="sortBy">
          <option value="">เรียงตาม: ทั้งหมด</option>
          <option value="price-low">ราคา: ต่ำ-สูง</option>
          <option value="price-high">ราคา: สูง-ต่ำ</option>
          <option value="name">ชื่อ: ก-ฮ</option>
        </select>
      </div>
    </div>

    <!-- ✨ ช่องเลือกประเภทอาหาร -->
    <div class="mb-5">
      <div class="category-pills">
        <button
          v-for="category in categories"
          :key="category.value"
          type="button"
          :class="['category-pill', selectedCategory === category.value ? 'active' : '']"
          @click="selectedCategory = category.value"
        >
          {{ category.icon }} {{ category.label }}
        </button>
      </div>
    </div>

    <!-- แสดงสินค้า -->
    <div class="row g-4">
      <div class="col-lg-3 col-md-4 col-sm-6" v-for="product in filteredProducts" :key="product.product_id">
        <div class="product-card">
          <div class="product-badge" v-if="product.stock && product.stock > 0">
            <span class="badge-available">เครื่องล้ม</span>
          </div>
          <div class="stock-badge">
            คลัง: {{ product.stock || 100 }}
          </div>
          <div class="product-image">
            <img
              :src="'http://localhost:8081/group/api_php/uploads/' + product.image"
              :alt="product.product_name"
              @error="handleImageError"
            />
          </div>
          <div class="product-body">
            <h5 class="product-title">{{ product.product_name }}</h5>
            <p class="product-description">{{ product.description || product.product_name }}</p>
            <div class="product-footer">
              <div class="price-tag">
                ฿{{ product.price }}
              </div>
              <button class="btn-add-cart" @click="addToCart(product)">
                เพิ่มลงตะกร้า
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- แสดงตะกร้าสินค้า -->
    <div class="cart-section mt-5" v-if="cart.length > 0">
      <div class="cart-header">
        <h4>🧺 ตะกร้าสินค้า</h4>
        <span class="cart-count">{{ cart.length }} รายการ</span>
      </div>

      <div class="cart-items">
        <div class="cart-item" v-for="(item, index) in cart" :key="index">
          <div class="item-info">
            <h6 class="item-name">{{ item.product_name }}</h6>
            <p class="item-price">฿{{ item.price }}</p>
          </div>
          <div class="item-controls">
            <div class="quantity-control">
              <button class="qty-btn" @click="decreaseQty(item)">-</button>
              <span class="qty-display">{{ item.quantity }}</span>
              <button class="qty-btn" @click="increaseQty(item)">+</button>
            </div>
            <div class="item-total">฿{{ (item.price * item.quantity).toFixed(2) }}</div>
            <button class="btn-remove" @click="removeFromCart(index)">
              🗑️
            </button>
          </div>
        </div>
      </div>

      <div class="cart-summary">
        <div class="summary-row">
          <span class="summary-label">ยอดรวมทั้งหมด</span>
          <span class="summary-value">฿{{ totalPrice.toFixed(2) }}</span>
        </div>
        <button class="btn-checkout" @click="showOrderTypeModal = true">
          เลือกประเภทการสั่ง
        </button>
      </div>
    </div>

    <div v-else class="empty-cart">
      <div class="empty-icon">🛒</div>
      <p>ยังไม่มีสินค้าในตะกร้า</p>
    </div>

    <!-- ✨ Modal เลือกประเภทการสั่ง -->
    <div v-if="showOrderTypeModal" class="modal-overlay" @click.self="showOrderTypeModal = false">
      <div class="modal-container">
        <div class="modal-header-custom">
          <h5>🍴 เลือกประเภทการสั่ง</h5>
          <button class="btn-close-custom" @click="showOrderTypeModal = false">✕</button>
        </div>
        <div class="modal-body-custom">
          <div class="order-type-grid">
            <!-- สั่งกลับบ้าน -->
            <div class="order-type-option" @click="selectOrderType('takeaway')">
              <div class="option-icon">🏠</div>
              <h6 class="option-title">สั่งกลับบ้าน</h6>
              <p class="option-description">รับสินค้าที่หน้าร้านหรือจัดส่ง</p>
            </div>

            <!-- ทานที่ร้าน -->
            <div class="order-type-option" @click="selectOrderType('dine-in')">
              <div class="option-icon">🍽️</div>
              <h6 class="option-title">ทานที่ร้าน</h6>
              <p class="option-description">จองโต๊ะและรับประทานที่ร้าน</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";

export default {
  name: "ProductList",
  setup() {
    const products = ref([]);
    const cart = ref([]);
    const selectedCategory = ref("all");
    const loading = ref(true);
    const error = ref(null);
    const searchQuery = ref("");
    const sortBy = ref("");
    const showOrderTypeModal = ref(false);

    // ตัวเลือกประเภทอาหาร
    const categories = [
      { value: "all", label: "ทั้งหมด", icon: "🏠" },
      { value: 1, label: "อาหาร", icon: "🍜" },
      { value: 2, label: "เครื่องดื่ม", icon: "🥤" },
      { value: 3, label: "ของหวาน", icon: "🍰" },
    ];

    // ✅ ดึงข้อมูลสินค้า
    const fetchProducts = async () => {
      try {
        const response = await fetch(
          "http://localhost:8081/group/api_php/api_product.php"
        );
        const result = await response.json();
        if (result.success) {
          products.value = result.data;
        } else {
          error.value = result.message;
        }
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // ✨ กรองสินค้าตามประเภท, ค้นหา, และเรียงลำดับ
    const filteredProducts = computed(() => {
      let result = products.value;

      // กรองตามประเภท
      if (selectedCategory.value !== "all") {
        result = result.filter(
          (product) => product.type_id === selectedCategory.value
        );
      }

      // ค้นหา
      if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter((product) =>
          product.product_name.toLowerCase().includes(query)
        );
      }

      // เรียงลำดับ
      if (sortBy.value === "price-low") {
        result = [...result].sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
      } else if (sortBy.value === "price-high") {
        result = [...result].sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
      } else if (sortBy.value === "name") {
        result = [...result].sort((a, b) =>
          a.product_name.localeCompare(b.product_name, "th")
        );
      }

      return result;
    });

    // ✅ เพิ่มสินค้าเข้าตะกร้า
    const addToCart = (product) => {
      const existing = cart.value.find(
        (item) => item.product_id === product.product_id
      );

      if (existing) {
        existing.quantity++;
      } else {
        cart.value.push({
          product_id: product.product_id,
          product_name: product.product_name,
          price: parseFloat(product.price),
          quantity: 1,
        });
      }

      alert(`✅ เพิ่ม "${product.product_name}" ลงในตะกร้าสำเร็จ!`);
    };

    // ✅ เพิ่มจำนวนสินค้า
    const increaseQty = (item) => {
      item.quantity++;
    };

    // ✅ ลดจำนวนสินค้า
    const decreaseQty = (item) => {
      if (item.quantity > 1) {
        item.quantity--;
      } else {
        if (confirm("ต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?")) {
          const index = cart.value.indexOf(item);
          if (index !== -1) cart.value.splice(index, 1);
        }
      }
    };

    // ✅ ลบสินค้าออกจากตะกร้า
    const removeFromCart = (index) => {
      if (confirm("ยืนยันการลบสินค้านี้หรือไม่?")) {
        cart.value.splice(index, 1);
      }
    };

    // ✅ คำนวณราคารวมทั้งหมด
    const totalPrice = computed(() =>
      cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
    );

    // ✨ เลือกประเภทการสั่ง
    const selectOrderType = (type) => {
      if (type === "takeaway") {
        showOrderTypeModal.value = false;
        proceedToPayment();
      } else if (type === "dine-in") {
        showOrderTypeModal.value = false;
        goToTableBooking();
      }
    };

    // ✅ ไปหน้าชำระเงิน (สำหรับสั่งกลับบ้าน)
    const proceedToPayment = () => {
      const orderData = {
        order_type: "takeaway",
        items: cart.value,
        total: totalPrice.value,
      };

      localStorage.setItem('cart', JSON.stringify(cart.value));
      localStorage.setItem('totalPrice', totalPrice.value.toString());
      localStorage.setItem('orderType', 'takeaway');
      
      console.log("Order Data (Takeaway):", orderData);
      
      alert(`✅ สั่งกลับบ้าน\nยอดรวม: ${totalPrice.value.toFixed(2)} บาท\n\nกำลังไปหน้าชำระเงิน...`);
      // window.location.href = '/payment';
    };

    // ✅ ไปหน้าจองโต๊ะ (สำหรับทานที่ร้าน)
    const goToTableBooking = () => {
      localStorage.setItem('cart', JSON.stringify(cart.value));
      localStorage.setItem('totalPrice', totalPrice.value.toString());
      localStorage.setItem('orderType', 'dine-in');
      
      window.location.href = '/table';
    };

    // ✅ จัดการรูปภาพที่โหลดไม่ได้
    const handleImageError = (e) => {
      e.target.src = 'https://via.placeholder.com/300x200/2d5016/ffffff?text=No+Image';
    };

    onMounted(fetchProducts);

    return {
      products,
      cart,
      selectedCategory,
      categories,
      filteredProducts,
      totalPrice,
      showOrderTypeModal,
      searchQuery,
      sortBy,
      addToCart,
      increaseQty,
      decreaseQty,
      removeFromCart,
      selectOrderType,
      handleImageError,
      loading,
      error,
    };
  },
};
</script>

<style scoped>
/* 🎨 Global Styles */
* {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* 🔍 Search Box */
.search-box input {
  border-radius: 50px;
  border: 2px solid #e8f5e9;
  padding: 12px 24px;
  font-size: 16px;
  background: white;
  box-shadow: 0 2px 8px rgba(45, 80, 22, 0.08);
}

.search-box input:focus {
  outline: none;
  border-color: #4caf50;
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
}

.form-select {
  border-radius: 50px;
  border: 2px solid #e8f5e9;
  padding: 12px 24px;
  background: white;
  box-shadow: 0 2px 8px rgba(45, 80, 22, 0.08);
}

.form-select:focus {
  outline: none;
  border-color: #4caf50;
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
}

/* 🏷️ Category Pills */
.category-pills {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
}

.category-pill {
  background: white;
  border: 2px solid #e8f5e9;
  border-radius: 50px;
  padding: 12px 28px;
  font-size: 16px;
  font-weight: 500;
  color: #2d5016;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(45, 80, 22, 0.08);
}

.category-pill:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
  border-color: #4caf50;
}

.category-pill.active {
  background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%);
  color: white;
  border-color: #2d5016;
  box-shadow: 0 4px 12px rgba(45, 80, 22, 0.3);
}

/* 🛍️ Product Card */
.product-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(45, 80, 22, 0.1);
  position: relative;
  cursor: pointer;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 25px rgba(45, 80, 22, 0.2);
}

.product-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 10;
}

.badge-available {
  background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%);
  color: white;
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 13px;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(45, 80, 22, 0.3);
}

.stock-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  color: #2d5016;
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 13px;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  z-index: 10;
}

.product-image {
  width: 100%;
  height: 220px;
  overflow: hidden;
  background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.1);
}

.product-body {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-title {
  font-size: 18px;
  font-weight: 700;
  color: #2d5016;
  margin-bottom: 8px;
  min-height: 50px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-description {
  font-size: 14px;
  color: #757575;
  margin-bottom: 16px;
  min-height: 40px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

.product-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-top: auto;
}

.price-tag {
  font-size: 24px;
  font-weight: 800;
  color: #2d5016;
}

.btn-add-cart {
  background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(45, 80, 22, 0.3);
  flex-shrink: 0;
}

.btn-add-cart:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(45, 80, 22, 0.4);
}

/* 🛒 Cart Section */
.cart-section {
  background: white;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(45, 80, 22, 0.12);
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid #e8f5e9;
}

.cart-header h4 {
  font-size: 24px;
  font-weight: 700;
  color: #2d5016;
  margin: 0;
}

.cart-count {
  background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%);
  color: white;
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
}

.cart-items {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}

.cart-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: #f9fdf9;
  border-radius: 16px;
  border: 2px solid #e8f5e9;
}

.item-info {
  flex: 1;
}

.item-name {
  font-size: 16px;
  font-weight: 600;
  color: #2d5016;
  margin-bottom: 4px;
}

.item-price {
  font-size: 14px;
  color: #757575;
  margin: 0;
}

.item-controls {
  display: flex;
  align-items: center;
  gap: 16px;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 12px;
  background: white;
  border-radius: 50px;
  padding: 6px 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.qty-btn {
  background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%);
  color: white;
  border: none;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  cursor: pointer;
  font-weight: 600;
}

.qty-btn:hover {
  transform: scale(1.1);
}

.qty-display {
  font-weight: 600;
  color: #2d5016;
  min-width: 30px;
  text-align: center;
}

.item-total {
  font-size: 18px;
  font-weight: 700;
  color: #2d5016;
  min-width: 80px;
  text-align: right;
}

.btn-remove {
  background: #ffebee;
  color: #d32f2f;
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  cursor: pointer;
}

.btn-remove:hover {
  background: #ffcdd2;
  transform: scale(1.1);
}

.cart-summary {
  padding-top: 24px;
  border-top: 2px solid #e8f5e9;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.summary-label {
  font-size: 18px;
  font-weight: 600;
  color: #2d5016;
}

.summary-value {
  font-size: 28px;
  font-weight: 800;
  color: #2d5016;
}

.btn-checkout {
  width: 100%;
  background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 16px;
  font-size: 18px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(45, 80, 22, 0.3);
}

.btn-checkout:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(45, 80, 22, 0.4);
}

.empty-cart {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 20px;
  margin-top: 40px;
  box-shadow: 0 4px 15px rgba(45, 80, 22, 0.1);
}

.empty-icon {
  font-size: 80px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.empty-cart p {
  font-size: 18px;
  color: #9e9e9e;
}

/* 🎭 Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}

.modal-container {
  background: white;
  border-radius: 24px;
  max-width: 600px;
  width: 100%;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  overflow: hidden;
  animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header-custom {
  background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%);
  color: white;
  padding: 24px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header-custom h5 {
  font-size: 22px;
  font-weight: 700;
  margin: 0;
}

.btn-close-custom {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  font-size: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.btn-close-custom:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.modal-body-custom {
  padding: 40px;
}

.order-type-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.order-type-option {
  background: #f9fdf9;
  border: 3px solid #e8f5e9;
  border-radius: 20px;
  padding: 32px 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.order-type-option:hover {
  transform: translateY(-8px);
  border-color: #4caf50;
  box-shadow: 0 8px 24px rgba(76, 175, 80, 0.25);
  background: white;
}

.option-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.option-title {
  font-size: 20px;
  font-weight: 700;
  color: #2d5016;
  margin-bottom: 8px;
}

.option-description {
  font-size: 14px;
  color: #757575;
  margin: 0;
}

/* 📱 Responsive */
@media (max-width: 768px) {
  .category-pills {
    justify-content: flex-start;
    overflow-x: auto;
    flex-wrap: nowrap;   
    -webkit-overflow-scrolling: touch;
    gap: 10px;
  }
}
</style>