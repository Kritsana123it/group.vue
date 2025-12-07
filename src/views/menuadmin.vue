<template>
  <div class="container my-5">
    <div class="row g-4">
      <div
        class="col-lg-3 col-md-4 col-sm-6"
        v-for="product in products"
        :key="product.product_id"
      >
        <div class="product-card">
          <!-- รูป -->
          <div class="product-image">
            <img
              :src="'http://localhost:8081/group/api_php/uploads/' + product.image"
              :alt="product.product_name"
              @error="handleImageError"
            />
          </div>

          <!-- เนื้อหา -->
          <div class="product-body">
            <h5 class="product-title">{{ product.product_name }}</h5>

            <div class="product-footer">
              <div class="price-tag">
                ฿{{ product.price }}
              </div>

              <div class="quantity-box">
                จำนวน: {{ product.stock || 0 }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ref, onMounted } from "vue";

export default {
  name: "SimpleProductList",
  setup() {
    const products = ref([]);

    const fetchProducts = async () => {
      const res = await fetch(
        "http://localhost:8081/group/api_php/api_product.php"
      );
      const data = await res.json();
      if (data.success) {
        products.value = data.data;
      }
    };

    const handleImageError = (e) => {
      e.target.src =
        "https://via.placeholder.com/300x200/2d5016/ffffff?text=No+Image";
    };

    onMounted(fetchProducts);

    return {
      products,
      handleImageError,
    };
  },
};
</script>
<style scoped> /* */ * { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); } .search-box input { border-radius: 50px; border: 2px solid #e8f5e9; padding: 12px 24px; font-size: 16px; background: white; box-shadow: 0 2px 8px rgba(45, 80, 22, 0.08); } .search-box input:focus { outline: none; border-color: #4caf50; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2); } .form-select { border-radius: 50px; border: 2px solid #e8f5e9; padding: 12px 24px; background: white; box-shadow: 0 2px 8px rgba(45, 80, 22, 0.08); } .form-select:focus { outline: none; border-color: #4caf50; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2); } .category-pills { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; } .category-pill { background: white; border: 2px solid #e8f5e9; border-radius: 50px; padding: 12px 28px; font-size: 16px; font-weight: 500; color: #2d5016; cursor: pointer; box-shadow: 0 2px 8px rgba(45, 80, 22, 0.08); } .category-pill:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2); border-color: #4caf50; } .category-pill.active { background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%); color: white; border-color: #2d5016; box-shadow: 0 4px 12px rgba(45, 80, 22, 0.3); } .product-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(45, 80, 22, 0.1); position: relative; cursor: pointer; height: 100%; display: flex; flex-direction: column; } .product-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(45, 80, 22, 0.2); } .product-badge { position: absolute; top: 12px; left: 12px; z-index: 10; } .badge-available { background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%); color: white; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; box-shadow: 0 2px 8px rgba(45, 80, 22, 0.3); } .stock-badge { position: absolute; top: 12px; right: 12px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); color: #2d5016; padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); z-index: 10; } .product-image { width: 100%; height: 220px; overflow: hidden; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); } .product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; } .product-card:hover .product-image img { transform: scale(1.1); } .product-body { padding: 20px; flex: 1; display: flex; flex-direction: column; } .product-title { font-size: 18px; font-weight: 700; color: #2d5016; margin-bottom: 8px; min-height: 50px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; } .product-description { font-size: 14px; color: #757575; margin-bottom: 16px; min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex: 1; } .product-footer { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: auto; } .price-tag { font-size: 24px; font-weight: 800; color: #2d5016; } .btn-add-cart { background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%); color: white; border: none; border-radius: 50px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(45, 80, 22, 0.3); flex-shrink: 0; } .btn-add-cart:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(45, 80, 22, 0.4); } .cart-section { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 20px rgba(45, 80, 22, 0.12); } .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #e8f5e9; } .cart-header h4 { font-size: 24px; font-weight: 700; color: #2d5016; margin: 0; } .cart-count { background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%); color: white; padding: 6px 16px; border-radius: 50px; font-size: 14px; font-weight: 600; } .cart-items { display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px; } .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; background: #f9fdf9; border-radius: 16px; border: 2px solid #e8f5e9; } .item-info { flex: 1; } .item-name { font-size: 16px; font-weight: 600; color: #2d5016; margin-bottom: 4px; } .item-price { font-size: 14px; color: #757575; margin: 0; } .item-controls { display: flex; align-items: center; gap: 16px; } .quantity-control { display: flex; align-items: center; gap: 12px; background: white; border-radius: 50px; padding: 6px 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); } .qty-btn { background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%); color: white; border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; font-weight: 600; } .qty-btn:hover { transform: scale(1.1); } .qty-display { font-weight: 600; color: #2d5016; min-width: 30px; text-align: center; } .item-total { font-size: 18px; font-weight: 700; color: #2d5016; min-width: 80px; text-align: right; } .btn-remove { background: #ffebee; color: #d32f2f; border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; } .btn-remove:hover { background: #ffcdd2; transform: scale(1.1); } .cart-summary { padding-top: 24px; border-top: 2px solid #e8f5e9; } .summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; } .summary-label { font-size: 18px; font-weight: 600; color: #2d5016; } .summary-value { font-size: 28px; font-weight: 800; color: #2d5016; } .btn-checkout { width: 100%; background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%); color: white; border: none; border-radius: 50px; padding: 16px; font-size: 18px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 16px rgba(45, 80, 22, 0.3); } .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(45, 80, 22, 0.4); } .empty-cart { text-align: center; padding: 60px 20px; background: white; border-radius: 20px; margin-top: 40px; box-shadow: 0 4px 15px rgba(45, 80, 22, 0.1); } .empty-icon { font-size: 80px; margin-bottom: 16px; opacity: 0.5; } .empty-cart p { font-size: 18px; color: #9e9e9e; } .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; } .modal-container { background: white; border-radius: 24px; max-width: 600px; width: 100%; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; animation: modalSlideIn 0.3s ease; } @keyframes modalSlideIn { from { opacity: 0; transform: translateY(-30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } } .modal-header-custom { background: linear-gradient(135deg, #4caf50 0%, #2d5016 100%); color: white; padding: 24px 30px; display: flex; justify-content: space-between; align-items: center; } .modal-header-custom h5 { font-size: 22px; font-weight: 700; margin: 0; } .btn-close-custom { background: rgba(255, 255, 255, 0.2); border: none; color: white; width: 36px; height: 36px; border-radius: 50%; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; } .btn-close-custom:hover { background: rgba(255, 255, 255, 0.3); transform: rotate(90deg); } .modal-body-custom { padding: 40px; } .order-type-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; } .order-type-option { background: #f9fdf9; border: 3px solid #e8f5e9; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease; } .order-type-option:hover { transform: translateY(-8px); border-color: #4caf50; box-shadow: 0 8px 24px rgba(76, 175, 80, 0.25); background: white; } .option-icon { font-size: 64px; margin-bottom: 16px; } .option-title { font-size: 20px; font-weight: 700; color: #2d5016; margin-bottom: 8px; } .option-description { font-size: 14px; color: #757575; margin: 0; } @media (max-width: 768px) { .category-pills { justify-content: flex-start; overflow-x: auto; flex-wrap: nowrap; -webkit-overflow-scrolling: touch; gap: 10px; } .order-type-grid { grid-template-columns: 1fr; }  } </style>