<template>
  <div class="dashboard-container">
    <div class="container-fluid py-4">
      
      <!-- Header -->
      <div class="mb-4">
        <h1 class="display-4 fw-bold text-success mb-2">
          แดชบอร์ดร้านอาหาร
        </h1>
        <p class="text-success fs-5">ระบบจัดการการจองโต๊ะ</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-success" role="status">
          <span class="visually-hidden">กำลังโหลด...</span>
        </div>
        <p class="text-success mt-3 fs-5">กำลังโหลดข้อมูล...</p>
      </div>

      <!-- Dashboard Content -->
      <div v-else>
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          
          <!-- การจองวันนี้ -->
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100 border-start border-success border-4">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="icon-box bg-success bg-opacity-10 p-3 rounded">
                    <i class="bi bi-calendar-check fs-3 text-success"></i>
                  </div>
                  <span class="display-5 fw-bold text-success">
                    {{ dashboardData.todayBookings }}
                  </span>
                </div>
                <h5 class="card-title text-secondary mb-0">การจองวันนี้</h5>
              </div>
            </div>
          </div>

          <!-- รอยืนยัน -->
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100 border-start border-warning border-4">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="icon-box bg-warning bg-opacity-10 p-3 rounded">
                    <i class="bi bi-clock-history fs-3 text-warning"></i>
                  </div>
                  <span class="display-5 fw-bold text-warning">
                    {{ dashboardData.pendingBookings }}
                  </span>
                </div>
                <h5 class="card-title text-secondary mb-0">รอยืนยัน</h5>
              </div>
            </div>
          </div>

          <!-- การจองทั้งหมด -->
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100 border-start border-info border-4">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="icon-box bg-info bg-opacity-10 p-3 rounded">
                    <i class="bi bi-people fs-3 text-info"></i>
                  </div>
                  <span class="display-5 fw-bold text-info">
                    {{ dashboardData.recentBookings.length }}
                  </span>
                </div>
                <h5 class="card-title text-secondary mb-0">การจองล่าสุด</h5>
              </div>
            </div>
          </div>

          <!-- โซนยอดนิยม -->
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="card stat-card border-0 shadow-sm h-100 border-start border-primary border-4">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="icon-box bg-primary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-graph-up-arrow fs-3 text-primary"></i>
                  </div>
                  <span class="display-5 fw-bold text-primary">
                    {{ dashboardData.popularTables.length }}
                  </span>
                </div>
                <h5 class="card-title text-secondary mb-0">โซนที่มีการจอง</h5>
              </div>
            </div>
          </div>
        </div>

        <div class="row g-4">
          
          <!-- กราฟเส้นโซนยอดนิยม -->
          <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title fw-bold text-success mb-4">
                  <i class="bi bi-graph-up me-2"></i>
                  โซนยอดนิยม (7 วันย้อนหลัง)
                </h5>
                
                <div v-if="dashboardData.popularTables.length > 0" class="line-chart-container">
                  <!-- SVG Line Chart -->
                  <svg class="line-chart" viewBox="0 0 400 250" xmlns="http://www.w3.org/2000/svg">
                    <!-- Grid Lines -->
                    <line x1="40" y1="20" x2="40" y2="200" stroke="#e0e0e0" stroke-width="2"/>
                    <line x1="40" y1="200" x2="380" y2="200" stroke="#e0e0e0" stroke-width="2"/>
                    
                    <!-- Y-axis labels -->
                    <text x="25" y="25" class="chart-label">{{ maxBookings }}</text>
                    <text x="25" y="115" class="chart-label">{{ Math.floor(maxBookings / 2) }}</text>
                    <text x="35" y="205" class="chart-label">0</text>
                    
                    <!-- Grid horizontal lines -->
                    <line x1="40" y1="20" x2="380" y2="20" stroke="#f0f0f0" stroke-width="1" stroke-dasharray="5,5"/>
                    <line x1="40" y1="110" x2="380" y2="110" stroke="#f0f0f0" stroke-width="1" stroke-dasharray="5,5"/>
                    
                    <!-- Line Path -->
                    <polyline
                      :points="generateLinePoints()"
                      fill="none"
                      stroke="url(#lineGradient)"
                      stroke-width="3"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      class="line-path"
                    />
                    
                    <!-- Area under line -->
                    <polygon
                      :points="generateAreaPoints()"
                      fill="url(#areaGradient)"
                      opacity="0.3"
                      class="area-fill"
                    />
                    
                    <!-- Data Points -->
                    <circle
                      v-for="(zone, index) in dashboardData.popularTables"
                      :key="'point-' + index"
                      :cx="getXPosition(index)"
                      :cy="getYPosition(zone.booking_count)"
                      r="5"
                      fill="#28a745"
                      stroke="white"
                      stroke-width="2"
                      class="data-point"
                    >
                      <title>{{ zone.zone }}: {{ zone.booking_count }} ครั้ง</title>
                    </circle>
                    
                    <!-- X-axis labels -->
                    <text
                      v-for="(zone, index) in dashboardData.popularTables"
                      :key="'label-' + index"
                      :x="getXPosition(index)"
                      y="220"
                      class="chart-label-x"
                    >
                      {{ zone.zone }}
                    </text>
                    
                    <!-- Gradients -->
                    <defs>
                      <linearGradient id="lineGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#28a745;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#20c997;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#17a2b8;stop-opacity:1" />
                      </linearGradient>
                      <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#28a745;stop-opacity:0.5" />
                        <stop offset="100%" style="stop-color:#28a745;stop-opacity:0.1" />
                      </linearGradient>
                    </defs>
                  </svg>
                  
                  <!-- Legend -->
                  <div class="mt-3">
                    <div 
                      v-for="(zone, index) in dashboardData.popularTables" 
                      :key="'legend-' + index"
                      class="d-flex justify-content-between align-items-center mb-2"
                    >
                      <div class="d-flex align-items-center">
                        <div 
                          class="legend-dot me-2"
                          :style="{ backgroundColor: getLegendColor(index) }"
                        ></div>
                        <span class="fw-semibold text-dark">{{ zone.zone }}</span>
                      </div>
                      <span class="badge bg-success">{{ zone.booking_count }} ครั้ง</span>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-5 text-muted">
                  <i class="bi bi-inbox fs-1"></i>
                  <p class="mt-3">ยังไม่มีข้อมูลการจอง</p>
                </div>
              </div>
            </div>
          </div>

          <!-- รายการจองล่าสุด -->
          <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title fw-bold text-success mb-4">
                  <i class="bi bi-list-check me-2"></i>
                  รายการจองล่าสุด
                </h5>
                
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                  <table v-if="dashboardData.recentBookings.length > 0" class="table table-hover align-middle">
                    <thead class="table-success sticky-top">
                      <tr>
                        <th>ลูกค้า</th>
                        <th>โซน</th>
                        <th>เวลา</th>
                        <th>จำนวน</th>
                        <th>วันที่</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr 
                        v-for="booking in dashboardData.recentBookings" 
                        :key="booking.booking_id"
                      >
                        <td>
                          <div class="fw-semibold">{{ booking.customer_name }}</div>
                          <small class="text-muted">{{ booking.phone }}</small>
                        </td>
                        <td>{{ booking.zone }}</td>
                        <td>{{ booking.time }}</td>
                        <td>
                          <span class="badge bg-secondary">{{ booking.guests }} คน</span>
                        </td>
                        <td>
                          <small>{{ formatDate(booking.booking_date) }}</small>
                        </td>
                        <td>
                          <span 
                            class="badge"
                            :class="getStatusBadgeClass(booking.status)"
                          >
                            {{ booking.status }}
                          </span>
                        </td>
                        <td>
                          <button
                            v-if="booking.status === 'รอยืนยัน'"
                            @click="updateBookingStatus(booking.booking_id, 'ยืนยันแล้ว')"
                            class="btn btn-sm btn-success"
                          >
                            <i class="bi bi-check-circle me-1"></i>
                            ยืนยัน
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div v-else class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-3">ยังไม่มีรายการจอง</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-4">
          <small class="text-success">
            <i class="bi bi-arrow-clockwise me-1"></i>
            อัพเดทล่าสุด: {{ currentTime }}
          </small>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

// State
const dashboardData = ref({
  todayBookings: 0,
  pendingBookings: 0,
  recentBookings: [],
  popularTables: []
});
const loading = ref(true);
const currentTime = ref('');
let refreshInterval = null;

// API Base URL - เปลี่ยนตามเซิร์ฟเวอร์ของคุณ
const API_URL = 'http://localhost:8081/group/api_php/api_tablebooking.php';

// คำนวณค่าสูงสุดสำหรับกราฟ
const maxBookings = computed(() => {
  return Math.max(
    ...dashboardData.value.popularTables.map(z => parseInt(z.booking_count)), 
    1
  );
});

// ดึงข้อมูลจาก API
const fetchDashboardData = async () => {
  try {
    const response = await fetch(`${API_URL}?action=dashboard`);
    const result = await response.json();
    
    if (result.success) {
      dashboardData.value = {
        todayBookings: result.todayBookings || 0,
        pendingBookings: result.pendingBookings || 0,
        recentBookings: result.recentBookings || [],
        popularTables: result.popularTables || []
      };
    }
    loading.value = false;
    updateCurrentTime();
  } catch (error) {
    console.error('Error fetching dashboard:', error);
    loading.value = false;
  }
};

// อัพเดทสถานะการจอง
const updateBookingStatus = async (bookingId, newStatus) => {
  try {
    const response = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action: 'update_status',
        booking_id: bookingId,
        status: newStatus
      })
    });
    
    const result = await response.json();
    if (result.success) {
      await fetchDashboardData(); // รีเฟรชข้อมูล
    }
  } catch (error) {
    console.error('Error updating status:', error);
  }
};

// คำนวณเปอร์เซ็นต์สำหรับกราฟ
const calculatePercentage = (count) => {
  return (parseInt(count) / maxBookings.value) * 100;
};

// สีของ Progress Bar ตามลำดับ
const getProgressBarColor = (index) => {
  const colors = ['bg-success', 'bg-info', 'bg-primary', 'bg-warning', 'bg-secondary'];
  return colors[index % colors.length];
};

// คำนวณตำแหน่ง X สำหรับกราฟเส้น
const getXPosition = (index) => {
  const totalPoints = dashboardData.value.popularTables.length;
  const chartWidth = 340; // 380 - 40 (padding)
  const spacing = chartWidth / (totalPoints > 1 ? totalPoints - 1 : 1);
  return 40 + (index * spacing);
};

// คำนวณตำแหน่ง Y สำหรับกราฟเส้น
const getYPosition = (value) => {
  const chartHeight = 180; // 200 - 20 (padding)
  const max = maxBookings.value;
  return 200 - ((parseInt(value) / max) * chartHeight);
};

// สร้างจุดสำหรับเส้นกราฟ
const generateLinePoints = () => {
  return dashboardData.value.popularTables
    .map((zone, index) => {
      const x = getXPosition(index);
      const y = getYPosition(zone.booking_count);
      return `${x},${y}`;
    })
    .join(' ');
};

// สร้างจุดสำหรับพื้นที่ใต้กราฟ
const generateAreaPoints = () => {
  const points = dashboardData.value.popularTables
    .map((zone, index) => {
      const x = getXPosition(index);
      const y = getYPosition(zone.booking_count);
      return `${x},${y}`;
    });
  
  if (points.length > 0) {
    const lastX = getXPosition(dashboardData.value.popularTables.length - 1);
    const firstX = getXPosition(0);
    return `${firstX},200 ${points.join(' ')} ${lastX},200`;
  }
  return '';
};

// สีสำหรับ legend
const getLegendColor = (index) => {
  const colors = ['#28a745', '#20c997', '#17a2b8', '#ffc107', '#6c757d'];
  return colors[index % colors.length];
};

// Badge สีตามสถานะ
const getStatusBadgeClass = (status) => {
  const classes = {
    'รอยืนยัน': 'bg-warning text-dark',
    'ยืนยันแล้ว': 'bg-success',
    'เสร็จสิ้น': 'bg-secondary',
    'ยกเลิก': 'bg-danger'
  };
  return classes[status] || 'bg-secondary';
};

// ฟอร์แมตวันที่
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('th-TH', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};

// อัพเดทเวลาปัจจุบัน
const updateCurrentTime = () => {
  currentTime.value = new Date().toLocaleString('th-TH');
};

// Lifecycle Hooks
onMounted(() => {
  fetchDashboardData();
  // รีเฟรชทุก 30 วินาที
  refreshInterval = setInterval(fetchDashboardData, 30000);
});

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
});
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 50%, #a5d6a7 100%);
}

.stat-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.icon-box {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
}

.progress {
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  border-radius: 10px;
}

.table {
  font-size: 0.9rem;
}

.table thead {
  background-color: #d4edda;
}

.table tbody tr {
  transition: background-color 0.2s ease;
}

.table tbody tr:hover {
  background-color: #f1f8f4;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: #28a745;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: #218838;
}

/* Line Chart Styles */
.line-chart-container {
  position: relative;
  width: 100%;
}

.line-chart {
  width: 100%;
  height: auto;
}

.chart-label {
  font-size: 12px;
  fill: #666;
  text-anchor: end;
}

.chart-label-x {
  font-size: 10px;
  fill: #666;
  text-anchor: middle;
}

.line-path {
  animation: drawLine 1.5s ease-in-out;
}

.area-fill {
  animation: fadeIn 1.5s ease-in-out;
}

.data-point {
  cursor: pointer;
  transition: r 0.3s ease;
  animation: popIn 0.5s ease-in-out backwards;
}

.data-point:hover {
  r: 8;
}

.data-point:nth-child(1) {
  animation-delay: 0.2s;
}

.data-point:nth-child(2) {
  animation-delay: 0.4s;
}

.data-point:nth-child(3) {
  animation-delay: 0.6s;
}

.data-point:nth-child(4) {
  animation-delay: 0.8s;
}

.data-point:nth-child(5) {
  animation-delay: 1s;
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}

@keyframes drawLine {
  from {
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
  }
  to {
    stroke-dasharray: 1000;
    stroke-dashoffset: 0;
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 0.3;
  }
}

@keyframes popIn {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@media (max-width: 768px) {
  .display-4 {
    font-size: 2rem;
  }
  
  .display-5 {
    font-size: 1.5rem;
  }
  
  .chart-label-x {
    font-size: 8px;
  }
}
</style>