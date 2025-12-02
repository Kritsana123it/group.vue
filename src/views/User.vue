<template>
  <div>
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="container">
        <h1 class="display-4 fw-bold text-white">🥗 ยินดีต้อนรับสู่ The Vegetable </h1>
        <p class="lead text-white">เลือกซื้อผักและผลไม้สดใหม่คุณภาพดี ส่งตรงถึงบ้านคุณ</p>
      </div>
    </div>

    <div class="container py-4">
      <!-- Search & Filter Section -->
      <div class="row mb-4">
        <div class="col-md-8">
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search"></i>
            </span>
            <input 
              v-model="searchQuery" 
              type="text" 
              class="form-control border-start-0 ps-0" 
              placeholder="ค้นหาสินค้า..."
            />
          </div>
        </div>
        <div class="col-md-4">
          <select v-model="sortBy" class="form-select form-select-lg">
            <option value="">เรียงตาม: ทั้งหมด</option>
            <option value="price-asc">ราคา: ต่ำ → สูง</option>
            <option value="price-desc">ราคา: สูง → ต่ำ</option>
            <option value="name">ชื่อสินค้า A-Z</option>
          </select>
        </div>
      </div>

      <!-- Category Filter -->
      <div class="mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
          <button 
            class="btn btn-filter"
            :class="categoryFilter === '' ? 'active' : ''"
            @click="categoryFilter = ''"
          >
            <i class="bi bi-grid-3x3-gap"></i> ทั้งหมด
          </button>
          <button 
            v-for="type in productTypes" 
            :key="type.id"
            class="btn btn-filter"
            :class="categoryFilter === type.id ? 'active' : ''"
            @click="categoryFilter = type.id"
          >
            {{ type.icon }} {{ type.name }}
          </button>
        </div>
      </div>

      <!-- Products Grid -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-success" role="status">
          <span class="visually-hidden">กำลังโหลด...</span>
        </div>
      </div>

      <div v-else-if="filteredProducts.length === 0" class="text-center py-5">
        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
        <p class="text-muted mt-3">ไม่พบสินค้าที่ค้นหา</p>
      </div>

      <div v-else class="row g-4">
        <div 
          v-for="product in filteredProducts" 
          :key="product.product_id"
          class="col-lg-3 col-md-4 col-sm-6"
        >
          <div class="product-card">
            <div class="product-image">
              <img
                v-if="product.image"
                :src="'http://localhost:8081/group/api_php/uploads/' + product.image"
                :alt="product.product_name"
              />
              <img
                v-else
                src="https://via.placeholder.com/300x300?text=No+Image"
                alt="No image"
              />
              <div class="product-badge">
                <span class="badge bg-success">{{ getTypeName(product.type_id) }}</span>
              </div>
            </div>
            <div class="product-body">
              <h5 class="product-title">{{ product.product_name }}</h5>
              <p class="product-description">{{ product.description }}</p>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="product-price">
                  <span class="price">฿{{ product.price }}</span>
                </div>
                <div class="product-stock">
                  <span v-if="product.stock > 0" class="badge bg-success">
                    คลัง: {{ product.stock }}
                  </span>
                  <span v-else class="badge bg-danger">สินค้าหมด</span>
                </div>
              </div>
              <button 
                class="btn btn-add-cart w-100 mt-3"
                :disabled="product.stock === 0"
                @click="addToCart(product)"
              >
                <i class="bi bi-cart-plus"></i> เพิ่มลงตะกร้า
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Shopping Cart Button (Fixed) -->
    <button class="btn-cart-floating" @click="showCart = true">
      <i class="bi bi-cart3"></i>
      <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
    </button>

    <!-- Cart Modal -->
    <div v-if="showCart" class="cart-modal" @click="showCart = false">
      <div class="cart-content" @click.stop>
        <div class="cart-header">
          <h4><i class="bi bi-cart3"></i> ตะกร้าสินค้า</h4>
          <button class="btn-close-cart" @click="showCart = false">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="cart-body">
          <div v-if="cart.length === 0" class="text-center py-5">
            <i class="bi bi-cart-x" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">ตะกร้าสินค้าว่างเปล่า</p>
          </div>
          <div v-else>
            <div v-for="item in cart" :key="item.product_id" class="cart-item">
              <img 
                :src="item.image ? 'http://localhost:8081/group/api_php/uploads/' + item.image : 'https://via.placeholder.com/80'"
                :alt="item.product_name"
              />
              <div class="cart-item-info">
                <h6>{{ item.product_name }}</h6>
                <p class="text-muted mb-0">฿{{ item.price }} x {{ item.quantity }}</p>
              </div>
              <div class="cart-item-controls">
                <button class="btn btn-sm btn-outline-secondary" @click="decreaseQuantity(item)">
                  <i class="bi bi-dash"></i>
                </button>
                <span class="mx-2">{{ item.quantity }}</span>
                <button class="btn btn-sm btn-outline-secondary" @click="increaseQuantity(item)">
                  <i class="bi bi-plus"></i>
                </button>
                <button class="btn btn-sm btn-danger ms-2" @click="removeFromCart(item)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div v-if="cart.length > 0" class="cart-footer">
          <div class="cart-total">
            <h5>ยอดรวม</h5>
            <h4 class="text-success">฿{{ cartTotal.toFixed(2) }}</h4>
          </div>
          <button class="btn btn-checkout w-100" @click="checkout">
            <i class="bi bi-credit-card"></i> ชำระเงิน
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';

export default {
  name: 'UserProductPage',
  setup() {
    const products = ref([]);
    const productTypes = ref([
      { id: 1, name: 'อาหาร', icon: '🍽️' },
      { id: 2, name: 'เครื่องดื่ม', icon: '🥤' },
      { id: 3, name: 'ของหวาน', icon: '🍰' },
      { id: 4, name: 'ของทานเล่น', icon: '🍿' }
    ]);
    const loading = ref(true);
    const searchQuery = ref('');
    const categoryFilter = ref('');
    const sortBy = ref('');
    const cart = ref([]);
    const showCart = ref(false);

    const getTypeName = (typeId) => {
      const type = productTypes.value.find(t => t.id === typeId);
      return type ? type.name : 'ไม่ระบุ';
    };

    const fetchProducts = async () => {
      try {
        const res = await fetch('http://localhost:8081/group/api_php/api_product.php');
        const data = await res.json();
        products.value = data.success ? data.data : [];
      } catch (err) {
        console.error('Error:', err);
      } finally {
        loading.value = false;
      }
    };

    const filteredProducts = computed(() => {
      let result = products.value;

      // Filter by category
      if (categoryFilter.value !== '') {
        result = result.filter(p => p.type_id === categoryFilter.value);
      }

      // Filter by search
      if (searchQuery.value) {
        result = result.filter(p => 
          p.product_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          p.description.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
      }

      // Sort
      if (sortBy.value === 'price-asc') {
        result = [...result].sort((a, b) => a.price - b.price);
      } else if (sortBy.value === 'price-desc') {
        result = [...result].sort((a, b) => b.price - a.price);
      } else if (sortBy.value === 'name') {
        result = [...result].sort((a, b) => a.product_name.localeCompare(b.product_name));
      }

      return result;
    });

    const cartCount = computed(() => {
      return cart.value.reduce((sum, item) => sum + item.quantity, 0);
    });

    const cartTotal = computed(() => {
      return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    });

    const addToCart = (product) => {
      const existingItem = cart.value.find(item => item.product_id === product.product_id);
      if (existingItem) {
        if (existingItem.quantity < product.stock) {
          existingItem.quantity++;
        } else {
          alert('จำนวนสินค้าในตะกร้าถึงขีดจำกัดแล้ว');
        }
      } else {
        cart.value.push({ ...product, quantity: 1 });
      }
      showCart.value = true;
    };

    const increaseQuantity = (item) => {
      const product = products.value.find(p => p.product_id === item.product_id);
      if (item.quantity < product.stock) {
        item.quantity++;
      } else {
        alert('จำนวนสินค้าในตะกร้าถึงขีดจำกัดแล้ว');
      }
    };

    const decreaseQuantity = (item) => {
      if (item.quantity > 1) {
        item.quantity--;
      }
    };

    const removeFromCart = (item) => {
      const index = cart.value.findIndex(i => i.product_id === item.product_id);
      if (index > -1) {
        cart.value.splice(index, 1);
      }
    };

    const checkout = () => {
      alert(`ชำระเงินทั้งหมด ฿${cartTotal.value.toFixed(2)}\n\nขอบคุณที่ใช้บริการครับ!`);
      cart.value = [];
      showCart.value = false;
    };

    onMounted(fetchProducts);

    return {
      products,
      productTypes,
      loading,
      searchQuery,
      categoryFilter,
      sortBy,
      cart,
      showCart,
      filteredProducts,
      cartCount,
      cartTotal,
      getTypeName,
      addToCart,
      increaseQuantity,
      decreaseQuantity,
      removeFromCart,
      checkout
    };
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap');

* {
  font-family: 'Sarabun', sans-serif;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 50%, #52b788 100%);
  padding: 80px 0;
  margin-bottom: 40px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(27, 67, 50, 0.3);
}

.hero-section h1 {
  font-weight: 700;
  letter-spacing: -0.5px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.hero-section p {
  font-weight: 400;
  opacity: 0.95;
}

/* Search & Filter */
.input-group-text {
  border-radius: 12px 0 0 12px !important;
  border: 2px solid #95d5b2;
  border-right: none !important;
  color: #1b4332;
}

.form-control {
  border-radius: 0 12px 12px 0 !important;
  border: 2px solid #95d5b2;
  border-left: none !important;
  font-size: 1rem;
  padding: 12px 16px;
}

.form-control:focus {
  box-shadow: 0 0 0 0.2rem rgba(82, 183, 136, 0.25);
  border-color: #52b788;
}

.form-select {
  border: 2px solid #95d5b2;
  border-radius: 12px;
  font-size: 1rem;
  padding: 12px 16px;
  font-weight: 500;
  color: #1b4332;
}

.form-select:focus {
  box-shadow: 0 0 0 0.2rem rgba(82, 183, 136, 0.25);
  border-color: #52b788;
}

/* Filter Buttons */
.btn-filter {
  padding: 12px 28px;
  border: 2px solid #d8f3dc;
  background: white;
  border-radius: 25px;
  transition: all 0.3s ease;
  font-weight: 600;
  font-size: 1rem;
  color: #1b4332;
}

.btn-filter:hover {
  border-color: #52b788;
  background: #f1faee;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(82, 183, 136, 0.2);
}

.btn-filter.active {
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
  color: white;
  border-color: transparent;
  box-shadow: 0 4px 15px rgba(27, 67, 50, 0.3);
}

/* Product Card */
.product-card {
  background: white;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
  border: 1px solid #d8f3dc;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 28px rgba(27, 67, 50, 0.15);
  border-color: #95d5b2;
}

.product-image {
  position: relative;
  width: 100%;
  height: 250px;
  overflow: hidden;
  background: linear-gradient(135deg, #f1faee 0%, #d8f3dc 100%);
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

.product-badge {
  position: absolute;
  top: 12px;
  right: 12px;
}

.product-badge .badge {
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%) !important;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(27, 67, 50, 0.3);
}

.product-body {
  padding: 20px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.product-title {
  font-size: 1.15rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: #1b4332;
  line-height: 1.4;
}

.product-description {
  font-size: 0.95rem;
  color: #52796f;
  margin-bottom: 15px;
  flex-grow: 1;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.6;
}

.product-price .price {
  font-size: 1.75rem;
  font-weight: 800;
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.product-stock .badge {
  padding: 6px 14px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
}

.product-stock .bg-success {
  background: linear-gradient(135deg, #52b788 0%, #74c69d 100%) !important;
}

.btn-add-cart {
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
  color: white;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(27, 67, 50, 0.25);
}

.btn-add-cart:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(27, 67, 50, 0.35);
  background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
}

.btn-add-cart:disabled {
  background: #ccc;
  cursor: not-allowed;
  box-shadow: none;
}

/* Floating Cart Button */
.btn-cart-floating {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 68px;
  height: 68px;
  border-radius: 50%;
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
  color: white;
  border: none;
  font-size: 1.8rem;
  box-shadow: 0 6px 24px rgba(27, 67, 50, 0.4);
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-cart-floating:hover {
  transform: scale(1.12) rotate(5deg);
  box-shadow: 0 8px 30px rgba(27, 67, 50, 0.5);
}

.cart-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #dc3545;
  color: white;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  font-weight: 800;
  border: 3px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

/* Cart Modal */
.cart-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: flex-end;
  z-index: 2000;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.cart-content {
  background: white;
  width: 450px;
  max-width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from { transform: translateX(100%); }
  to { transform: translateX(0); }
}

.cart-header {
  padding: 24px;
  border-bottom: 2px solid #d8f3dc;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
  color: white;
}

.cart-header h4 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
}

.btn-close-cart {
  background: none;
  border: none;
  color: white;
  font-size: 1.8rem;
  cursor: pointer;
  transition: all 0.2s ease;
  padding: 4px 10px;
  border-radius: 8px;
}

.btn-close-cart:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: rotate(90deg);
}

.cart-body {
  flex-grow: 1;
  overflow-y: auto;
  padding: 20px;
  background: #f8f9fa;
}

.cart-item {
  display: flex;
  gap: 15px;
  padding: 16px;
  background: white;
  border-radius: 14px;
  margin-bottom: 16px;
  align-items: center;
  border: 1px solid #d8f3dc;
  transition: all 0.2s ease;
}

.cart-item:hover {
  box-shadow: 0 4px 12px rgba(27, 67, 50, 0.1);
  border-color: #95d5b2;
}

.cart-item img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 10px;
  border: 2px solid #d8f3dc;
}

.cart-item-info {
  flex-grow: 1;
}

.cart-item-info h6 {
  margin: 0 0 6px 0;
  font-weight: 700;
  font-size: 1rem;
  color: #1b4332;
}

.cart-item-info p {
  font-weight: 500;
  color: #52796f;
}

.cart-item-controls {
  display: flex;
  align-items: center;
  gap: 6px;
}

.cart-item-controls .btn {
  min-width: 34px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.cart-item-controls .btn-outline-secondary {
  border: 2px solid #52b788;
  color: #1b4332;
  background: white;
}

.cart-item-controls .btn-outline-secondary:hover {
  background: #d8f3dc;
  border-color: #2d6a4f;
}

.cart-item-controls .btn-danger {
  border: 2px solid #dc3545;
  background: white;
  color: #dc3545;
}

.cart-item-controls .btn-danger:hover {
  background: #dc3545;
  color: white;
}

.cart-item-controls span {
  font-weight: 700;
  color: #1b4332;
  font-size: 1.1rem;
  min-width: 30px;
  text-align: center;
}

.cart-footer {
  padding: 24px;
  border-top: 2px solid #d8f3dc;
  background: linear-gradient(to bottom, #f8f9fa, #ffffff);
}

.cart-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 16px;
  background: #d8f3dc;
  border-radius: 12px;
}

.cart-total h5 {
  margin: 0;
  font-weight: 700;
  color: #1b4332;
  font-size: 1.3rem;
}

.cart-total h4 {
  margin: 0;
  font-weight: 800;
  font-size: 2rem;
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.btn-checkout {
  background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
  color: white;
  border: none;
  padding: 16px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1.15rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(27, 67, 50, 0.3);
}

.btn-checkout:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(27, 67, 50, 0.4);
  background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
}

/* Loading Spinner */
.spinner-border {
  width: 3rem;
  height: 3rem;
  border-width: 0.3em;
}

.text-success {
  color: #2d6a4f !important;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-section {
    padding: 50px 20px;
  }

  .hero-section h1 {
    font-size: 2rem;
  }

  .cart-content {
    width: 100%;
  }

  .btn-filter {
    padding: 10px 20px;
    font-size: 0.95rem;
  }
}
</style>