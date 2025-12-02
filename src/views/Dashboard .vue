<template>
  <div class="dashboard">
    <!-- Header -->
    <div class="dashboard-header">
      <h1>🍽️ แดชบอร์ดจัดการการจอง</h1>
      <button @click="refreshData" class="refresh-btn">
        🔄 รีเฟรช
      </button>
    </div>

    <!-- สถิติภาพรวม -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
          <h3>{{ dashboardData.todayBookings || 0 }}</h3>
          <p>การจองวันนี้</p>
        </div>
      </div>

      <div class="stat-card pending">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
          <h3>{{ dashboardData.pendingBookings || 0 }}</h3>
          <p>รอยืนยัน</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
          <h3>{{ confirmedCount }}</h3>
          <p>ยืนยันแล้ว</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
          <h3>{{ totalGuests }}</h3>
          <p>จำนวนลูกค้าทั้งหมด</p>
        </div>
      </div>
    </div>

    <!-- กราฟและสถิติ -->
    <div class="charts-section">
      <!-- กราฟโซนยอดนิยม -->
      <div class="chart-card">
        <h2>📊 โซนยอดนิยม (7 วันที่ผ่านมา)</h2>
        <canvas ref="zoneChart" id="zoneChart"></canvas>
      </div>

      <!-- กราฟสถานะการจอง -->
      <div class="chart-card">
        <h2>🥧 สถานะการจอง</h2>
        <canvas ref="statusChart" id="statusChart"></canvas>
      </div>
    </div>

    <!-- กราฟการจองรายวัน (7 วัน) -->
    <div class="chart-card-full">
      <h2>📈 แนวโน้มการจอง 7 วันที่ผ่านมา</h2>
      <canvas ref="trendChart" id="trendChart"></canvas>
    </div>

    <!-- รายการจองล่าสุด -->
    <div class="recent-bookings">
      <div class="section-header">
        <h2>📋 รายการจองล่าสุด</h2>
        <span class="auto-refresh">อัพเดทอัตโนมัติทุก 30 วินาที</span>
      </div>

      <div v-if="recentBookings.length === 0" class="no-data">
        ยังไม่มีข้อมูลการจอง
      </div>

      <div v-else class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>ชื่อลูกค้า</th>
              <th>โทรศัพท์</th>
              <th>โซน</th>
              <th>จำนวนคน</th>
              <th>วันที่</th>
              <th>เวลา</th>
              <th>สถานะ</th>
              <th>จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in recentBookings" :key="booking.booking_id" :class="getRowClass(booking.status)">
              <td>#{{ booking.booking_id }}</td>
              <td>{{ booking.customer_name }}</td>
              <td>{{ booking.phone }}</td>
              <td><span class="table-badge">{{ booking.zone }}</span></td>
              <td>{{ booking.guests }} คน</td>
              <td>{{ formatDate(booking.booking_date) }}</td>
              <td>{{ booking.time }}</td>
              <td>
                <span :class="'status-badge ' + getStatusClass(booking.status)">
                  {{ booking.status }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button 
                    v-if="booking.status === 'รอยืนยัน'" 
                    @click="updateStatus(booking.booking_id, 'ยืนยันแล้ว')"
                    class="btn-confirm"
                    title="ยืนยัน"
                  >
                    ✓
                  </button>
                  <button 
                    v-if="booking.status !== 'ยกเลิก'" 
                    @click="updateStatus(booking.booking_id, 'ยกเลิก')"
                    class="btn-cancel"
                    title="ยกเลิก"
                  >
                    ✕
                  </button>
                  <button 
                    @click="viewDetails(booking)"
                    class="btn-view"
                    title="ดูรายละเอียด"
                  >
                    👁️
                  </button>
                  <button 
                    @click="deleteBooking(booking.booking_id)"
                    class="btn-delete"
                    title="ลบ"
                  >
                    🗑️
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal รายละเอียด -->
    <div v-if="selectedBooking" class="modal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <button @click="closeModal" class="close-btn">✕</button>
        <h2>รายละเอียดการจอง #{{ selectedBooking.booking_id }}</h2>
        <div class="detail-grid">
          <div class="detail-item">
            <strong>ชื่อลูกค้า:</strong> {{ selectedBooking.customer_name }}
          </div>
          <div class="detail-item">
            <strong>โทรศัพท์:</strong> {{ selectedBooking.phone }}
          </div>
          <div class="detail-item">
            <strong>โซน:</strong> {{ selectedBooking.zone }}
          </div>
          <div class="detail-item">
            <strong>จำนวนคน:</strong> {{ selectedBooking.guests }} คน
          </div>
          <div class="detail-item">
            <strong>วันที่:</strong> {{ formatDate(selectedBooking.booking_date) }}
          </div>
          <div class="detail-item">
            <strong>เวลา:</strong> {{ selectedBooking.time }}
          </div>
          <div class="detail-item">
            <strong>สถานะ:</strong> 
            <span :class="'status-badge ' + getStatusClass(selectedBooking.status)">
              {{ selectedBooking.status }}
            </span>
          </div>
          <div class="detail-item">
            <strong>วันที่สร้าง:</strong> {{ formatDateTime(selectedBooking.created_at) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
  name: 'Dashboard',
  data() {
    return {
      dashboardData: {
        todayBookings: 0,
        pendingBookings: 0,
        recentBookings: [],
        popularTables: []
      },
      recentBookings: [],
      selectedBooking: null,
      autoRefreshInterval: null,
      apiUrl: 'http://localhost:8081/group/api_php/api_tablebooking.php',
      charts: {
        zone: null,
        status: null,
        trend: null
      }
    }
  },
  computed: {
    confirmedCount() {
      return this.recentBookings.filter(b => b.status === 'ยืนยันแล้ว').length;
    },
    totalGuests() {
      return this.recentBookings.reduce((sum, b) => sum + parseInt(b.guests || 0), 0);
    },
    statusCounts() {
      const counts = {
        'รอยืนยัน': 0,
        'ยืนยันแล้ว': 0,
        'ยกเลิก': 0,
        'เสร็จสิ้น': 0
      };
      this.recentBookings.forEach(b => {
        if (counts.hasOwnProperty(b.status)) {
          counts[b.status]++;
        }
      });
      return counts;
    }
  },
  mounted() {
    this.loadDashboard();
    this.autoRefreshInterval = setInterval(() => {
      this.loadDashboard();
    }, 30000);
  },
  beforeUnmount() {
    if (this.autoRefreshInterval) {
      clearInterval(this.autoRefreshInterval);
    }
    // ทำลาย charts
    Object.values(this.charts).forEach(chart => {
      if (chart) chart.destroy();
    });
  },
  methods: {
    async loadDashboard() {
      try {
        const response = await fetch(`${this.apiUrl}?action=dashboard`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (!result.success) {
          throw new Error(result.error || 'Failed to load dashboard');
        }
        
        this.dashboardData = result;
        this.recentBookings = result.recentBookings || [];
        
        // รอให้ DOM อัพเดทแล้วค่อยสร้างกราฟ
        this.$nextTick(() => {
          this.createCharts();
        });
        
      } catch (error) {
        console.error('Error loading dashboard:', error);
        alert('ไม่สามารถโหลดข้อมูลได้: ' + error.message);
      }
    },
    createCharts() {
      this.createZoneChart();
      this.createStatusChart();
      this.createTrendChart();
    },
    createZoneChart() {
      const canvas = this.$refs.zoneChart;
      if (!canvas) return;

      if (this.charts.zone) {
        this.charts.zone.destroy();
      }

      const zones = this.dashboardData.popularTables || [];
      const labels = zones.map(z => `โซน ${z.zone}`);
      const data = zones.map(z => parseInt(z.booking_count));

      this.charts.zone = new Chart(canvas, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'จำนวนการจอง',
            data: data,
            backgroundColor: [
              'rgba(54, 162, 235, 0.8)',
              'rgba(75, 192, 192, 0.8)',
              'rgba(255, 206, 86, 0.8)',
              'rgba(153, 102, 255, 0.8)',
              'rgba(255, 159, 64, 0.8)'
            ],
            borderColor: [
              'rgba(54, 162, 235, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      });
    },
    createStatusChart() {
      const canvas = this.$refs.statusChart;
      if (!canvas) return;

      if (this.charts.status) {
        this.charts.status.destroy();
      }

      const counts = this.statusCounts;

      this.charts.status = new Chart(canvas, {
        type: 'doughnut',
        data: {
          labels: ['รอยืนยัน', 'ยืนยันแล้ว', 'ยกเลิก', 'เสร็จสิ้น'],
          datasets: [{
            data: [
              counts['รอยืนยัน'],
              counts['ยืนยันแล้ว'],
              counts['ยกเลิก'],
              counts['เสร็จสิ้น']
            ],
            backgroundColor: [
              'rgba(255, 152, 0, 0.8)',
              'rgba(76, 175, 80, 0.8)',
              'rgba(244, 67, 54, 0.8)',
              'rgba(33, 150, 243, 0.8)'
            ],
            borderColor: [
              'rgba(255, 152, 0, 1)',
              'rgba(76, 175, 80, 1)',
              'rgba(244, 67, 54, 1)',
              'rgba(33, 150, 243, 1)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    },
    createTrendChart() {
      const canvas = this.$refs.trendChart;
      if (!canvas) return;

      if (this.charts.trend) {
        this.charts.trend.destroy();
      }

      // สร้างข้อมูล 7 วันย้อนหลัง
      const days = [];
      const bookingCounts = [];
      
      for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        
        // นับจำนวนการจองในวันนั้น
        const count = this.recentBookings.filter(b => b.booking_date === dateStr).length;
        
        days.push(this.formatDateShort(dateStr));
        bookingCounts.push(count);
      }

      this.charts.trend = new Chart(canvas, {
        type: 'line',
        data: {
          labels: days,
          datasets: [{
            label: 'จำนวนการจอง',
            data: bookingCounts,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top'
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      });
    },
    async refreshData() {
      await this.loadDashboard();
      alert('รีเฟรชข้อมูลเรียบร้อย!');
    },
    async updateStatus(booking_id, status) {
      const confirmMsg = status === 'ยืนยันแล้ว' ? 'ยืนยันการจองนี้?' : 'ยกเลิกการจองนี้?';
      if (!confirm(confirmMsg)) return;

      try {
        const response = await fetch(this.apiUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            action: 'update_status',
            booking_id: booking_id,
            status: status
          })
        });
        
        const result = await response.json();
        if (result.success) {
          await this.loadDashboard();
          alert('อัพเดทสถานะเรียบร้อย!');
        } else {
          alert('เกิดข้อผิดพลาด: ' + result.error);
        }
      } catch (error) {
        console.error('Error updating status:', error);
        alert('ไม่สามารถอัพเดทสถานะได้');
      }
    },
    async deleteBooking(booking_id) {
      if (!confirm('ต้องการลบการจองนี้หรือไม่?')) return;

      try {
        const response = await fetch(this.apiUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            action: 'delete',
            booking_id: booking_id
          })
        });
        
        const result = await response.json();
        if (result.success) {
          await this.loadDashboard();
          alert('ลบข้อมูลเรียบร้อย!');
        } else {
          alert('เกิดข้อผิดพลาด: ' + result.error);
        }
      } catch (error) {
        console.error('Error deleting booking:', error);
        alert('ไม่สามารถลบข้อมูลได้');
      }
    },
    viewDetails(booking) {
      this.selectedBooking = booking;
    },
    closeModal() {
      this.selectedBooking = null;
    },
    getStatusClass(status) {
      const statusMap = {
        'รอยืนยัน': 'pending',
        'ยืนยันแล้ว': 'confirmed',
        'ยกเลิก': 'cancelled',
        'เสร็จสิ้น': 'completed'
      };
      return statusMap[status] || 'pending';
    },
    getRowClass(status) {
      return `row-${this.getStatusClass(status)}`;
    },
    formatDate(date) {
      if (!date) return '-';
      const d = new Date(date);
      const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                      'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
      return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear() + 543}`;
    },
    formatDateShort(date) {
      if (!date) return '-';
      const d = new Date(date);
      return `${d.getDate()}/${d.getMonth() + 1}`;
    },
    formatDateTime(datetime) {
      if (!datetime) return '-';
      const d = new Date(datetime);
      return d.toLocaleString('th-TH');
    }
  }
}
</script>

<style scoped>
.dashboard {
  padding: 20px;
  background: #f5f5f5;
  min-height: 100vh;
  font-family: 'Prompt', sans-serif;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dashboard-header h1 {
  margin: 0;
  color: #333;
  font-size: 28px;
}

.refresh-btn {
  padding: 10px 20px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.3s;
}

.refresh-btn:hover {
  background: #45a049;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: transform 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-card.pending {
  border-left: 4px solid #ff9800;
}

.stat-icon {
  font-size: 40px;
  opacity: 0.8;
}

.stat-info h3 {
  margin: 0;
  font-size: 32px;
  color: #333;
}

.stat-info p {
  margin: 5px 0 0 0;
  color: #666;
  font-size: 14px;
}

.charts-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.chart-card {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  height: 400px;
}

.chart-card h2 {
  margin-top: 0;
  margin-bottom: 20px;
  color: #333;
  font-size: 18px;
}

.chart-card canvas {
  max-height: 320px;
}

.chart-card-full {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 30px;
  height: 350px;
}

.chart-card-full h2 {
  margin-top: 0;
  margin-bottom: 20px;
  color: #333;
  font-size: 18px;
}

.chart-card-full canvas {
  max-height: 280px;
}

.recent-bookings {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 30px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h2 {
  margin: 0;
  color: #333;
}

.auto-refresh {
  font-size: 12px;
  color: #999;
}

.no-data {
  text-align: center;
  padding: 40px;
  color: #999;
  font-size: 16px;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #f8f9fa;
  padding: 12px;
  text-align: left;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #dee2e6;
}

td {
  padding: 12px;
  border-bottom: 1px solid #dee2e6;
}

tr:hover {
  background: #f8f9fa;
}

.row-pending {
  background: #fff3e0;
}

.row-cancelled {
  opacity: 0.6;
  text-decoration: line-through;
}

.table-badge {
  background: #2196F3;
  color: white;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.status-badge.pending {
  background: #fff3e0;
  color: #f57c00;
}

.status-badge.confirmed {
  background: #e8f5e9;
  color: #2e7d32;
}

.status-badge.cancelled {
  background: #ffebee;
  color: #c62828;
}

.status-badge.completed {
  background: #e3f2fd;
  color: #1565c0;
}

.action-buttons {
  display: flex;
  gap: 5px;
}

.action-buttons button {
  padding: 6px 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s;
}

.btn-confirm {
  background: #4CAF50;
  color: white;
}

.btn-confirm:hover {
  background: #45a049;
}

.btn-cancel {
  background: #f44336;
  color: white;
}

.btn-cancel:hover {
  background: #da190b;
}

.btn-view {
  background: #2196F3;
  color: white;
}

.btn-view:hover {
  background: #0b7dda;
}

.btn-delete {
  background: #FF5722;
  color: white;
}

.btn-delete:hover {
  background: #E64A19;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 10px;
  max-width: 600px;
  width: 90%;
  position: relative;
  max-height: 80vh;
  overflow-y: auto;
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: #f44336;
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 18px;
}

.modal-content h2 {
  margin-top: 0;
  color: #333;
  margin-bottom: 20px;
}

.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.detail-item {
  padding: 10px;
  background: #f8f9fa;
  border-radius: 5px;
}

.detail-item strong {
  color: #555;
  display: block;
  margin-bottom: 5px;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .charts-section {
    grid-template-columns: 1fr;
  }
  
  .detail-grid {
    grid-template-columns: 1fr;
  }
  
  table {
    font-size: 12px;
  }
  
  th, td {
    padding: 8px;
  }
}
</style>