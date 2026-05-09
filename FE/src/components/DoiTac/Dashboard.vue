<template>
  <PartnerShell
    title="Tổng quan đối tác"
    subtitle="Theo dõi trạng thái duyệt và điều hướng nhanh đến các chức năng quản lý."
    :partner="partner"
    :loading="loading"
  >
    <template #headerActions>
      <button class="refresh-btn" type="button" @click="loadDashboard">
        <i class="fas fa-rotate"></i>
        Làm mới
      </button>
    </template>

    <section v-if="errorMessage" class="alert-box">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </section>

    <section class="stats-grid">
      <article class="stat-card">
        <p>Tour của bạn</p>
        <strong>{{ stats.totalTours }}</strong>
        <small>{{ stats.approvedTours }} da duyet · {{ stats.pendingTours }} chờ duyệt</small>
      </article>
      <article class="stat-card">
        <p>Địa điểm của bạn</p>
        <strong>{{ stats.totalLocations }}</strong>
        <small>{{ stats.approvedLocations }} da duyet · {{ stats.pendingLocations }} chờ duyệt</small>
      </article>
      <article class="stat-card stat-card--warning">
        <p>Mục bị từ chối</p>
        <strong>{{ stats.rejectedTours + stats.rejectedLocations }}</strong>
        <small>Cần cập nhật để gửi lại</small>
      </article>
      <article class="stat-card stat-card--accent">
        <p>Tỷ lệ duyệt</p>
        <strong>{{ approvalRate }}%</strong>
        <small>Dựa trên tour và địa điểm của đối tác</small>
      </article>
    </section>

    <!-- Thẻ Thống kê Tài chính (Mới) -->
    <section class="finance-grid">
      <article class="stat-card" style="border-left: 4px solid #10b981;">
        <p>Thu Nhập Thực Nhận</p>
        <strong style="color: #059669;">{{ formatCurrency(netRevenue) }}</strong>
        <small>Doanh thu của bạn sau khi trừ phí</small>
      </article>
      <article class="stat-card" style="border-left: 4px solid #f59e0b;">
        <p>Tiền Đang Chờ Đối Soát</p>
        <strong style="color: #d97706;">{{ formatCurrency(pendingPayout) }}</strong>
        <small>Admin chưa thanh toán (tạm giữ)</small>
      </article>
      <article class="stat-card" style="border-left: 4px solid #ef4444;">
        <p>Chi Phí Hoa Hồng</p>
        <strong style="color: #b91c1c;">{{ formatCurrency(paidCommission) }}</strong>
        <small>Đã trích % đóng cho Nền tảng</small>
      </article>
    </section>

    <section class="chart-grid">
      <!-- Cột Trái: Chart -->
      <article class="surface-card">
        <div class="surface-card__head">
          <div>
            <h3>Doanh thu biến động</h3>
            <p>Tổng doanh thu các Tour đã thanh toán thành công.</p>
          </div>
        </div>
        <div class="chart-container" v-if="chuoiDoanhThu.length > 0">
          <LineChart :data="chartData" :options="chartOptions" style="height: 250px" />
        </div>
        <div v-else class="surface-card__empty">
          <p>Chưa có dữ liệu giao dịch.</p>
        </div>
      </article>

      <!-- Cột Phải: Phân bổ Đánh giá -->
      <article class="surface-card">
        <div class="surface-card__head">
          <div>
            <h3>Phân bố đánh giá</h3>
            <p>TB: {{ avgRating }} <i class="fas fa-star" style="color: #f59e0b"></i> / {{ totalRatings }} lượt</p>
          </div>
        </div>
        <div class="donut-wrap" v-if="totalRatings > 0">
          <div style="position: relative; width: 175px; height: 175px;">
            <DoughnutChart :data="donutData" :options="donutOptions" />
            <div class="donut-center">
              <strong>{{ avgRating }}</strong>
              <span>Sao</span>
            </div>
          </div>
        </div>
        <div v-else class="surface-card__empty">
          <p>Chưa có đánh giá nào.</p>
        </div>
      </article>
    </section>

    <section class="quick-grid">
      <article class="quick-card">
        <h3>Quản lý tour</h3>
        <p>Tạo, chỉnh sửa, xóa và gửi duyệt tour cho hệ thống.</p>
        <button type="button" @click="$router.push('/doi-tac/quan-ly-tour')">
          Mở quản lý tour
        </button>
      </article>
      <article class="quick-card">
        <h3>Quản lý địa điểm</h3>
        <p>Quản lý địa điểm rieng cua doi tac va theo doi trang thai duyet.</p>
        <button type="button" @click="$router.push('/doi-tac/quan-ly-dia-diem')">
          Mở quản lý địa điểm
        </button>
      </article>
      <article class="quick-card">
        <h3>Quản lý đơn hàng</h3>
        <p>Theo dõi người đặt tour, hóa đơn và trạng thái thanh toán mới nhất của từng chuyến.</p>
        <button type="button" @click="$router.push('/doi-tac/don-hang')">
          Mở quản lý đơn hàng
        </button>
      </article>
      <article class="quick-card">
        <h3>Đối soát doanh thu</h3>
        <p>Kiểm tra doanh thu đã thanh toán, các khoản chờ đối soát và dữ liệu tổng hợp hiện tại.</p>
        <button type="button" @click="$router.push('/doi-tac/doanh-thu')">
          Xem đối soát doanh thu
        </button>
      </article>
      <article class="quick-card">
        <h3>Thông tin tài khoản</h3>
        <p>Cập nhật hồ sơ doanh nghiệp, người đại diện và thông tin liên hệ dùng cho vận hành.</p>
        <button type="button" @click="$router.push('/doi-tac/thong-tin-tai-khoan')">
          Cập nhật tài khoản
        </button>
      </article>
    </section>
  </PartnerShell>
</template>

<script>
import PartnerShell from "./layout/PartnerShell.vue";
import {
  fetchPartnerLocations,
  fetchPartnerSession,
  fetchPartnerTours,
} from "./shared/partnerApi";
import { goiApi } from "../../services/httpClient";
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, ArcElement, Filler } from 'chart.js';
import { Line as LineChart, Doughnut as DoughnutChart } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, ArcElement, Filler);

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "PartnerDashboard",
  components: {
    PartnerShell,
    LineChart, 
    DoughnutChart
  },
  data() {
    return {
      loading: false,
      errorMessage: "",
      partner: null,
      tours: [],
      locations: [],
      chuoiDoanhThu: [],
      ratingDistribution: [],
      avgRating: 0.0,
      totalRatings: 0,
      netRevenue: 0,
      pendingPayout: 0,
      paidCommission: 0,
    };
  },
  computed: {
    stats() {
      const counters = {
        totalTours: this.tours.length,
        totalLocations: this.locations.length,
        approvedTours: 0,
        pendingTours: 0,
        rejectedTours: 0,
        approvedLocations: 0,
        pendingLocations: 0,
        rejectedLocations: 0,
      };

      this.tours.forEach((tour) => {
        const status = String(tour?.trang_thai_duyet || "").toLowerCase();
        if (status === "approved") counters.approvedTours += 1;
        if (status === "pending_approval") counters.pendingTours += 1;
        if (status === "rejected") counters.rejectedTours += 1;
      });

      this.locations.forEach((location) => {
        const status = String(location?.trang_thai_duyet || "").toLowerCase();
        if (status === "approved") counters.approvedLocations += 1;
        if (status === "pending_approval") counters.pendingLocations += 1;
        if (status === "rejected") counters.rejectedLocations += 1;
      });

      return counters;
    },
    approvalRate() {
      const approved = this.stats.approvedTours + this.stats.approvedLocations;
      const total = this.stats.totalTours + this.stats.totalLocations;
      if (!total) return 0;
      return Math.round((approved / total) * 100);
    },
    chartData() {
      return {
        labels: this.chuoiDoanhThu.map(d => d.label),
        datasets: [
          {
            label: 'Doanh thu',
            backgroundColor: 'rgba(56, 189, 248, 0.2)', // Light blue for partner
            borderColor: '#0284c7', // Sky blue
            borderWidth: 2,
            pointBackgroundColor: '#0284c7',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#0284c7',
            fill: true,
            data: this.chuoiDoanhThu.map(d => d.value)
          }
        ]
      };
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => {
                if (value >= 1000000) return (value / 1000000) + ' Tr';
                if (value >= 1000) return (value / 1000) + ' K';
                return value;
              }
            }
          }
        }
      };
    },
    donutData() {
      // Map từ 1 đến 5 sao
      const ratings = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };
      this.ratingDistribution.forEach(item => {
        ratings[item.so_sao] = item.count;
      });

      return {
        labels: ['1 Sao', '2 Sao', '3 Sao', '4 Sao', '5 Sao'],
        datasets: [
          {
            data: [ratings[1], ratings[2], ratings[3], ratings[4], ratings[5]],
            backgroundColor: ['#ef4444', '#f97316', '#f59e0b', '#84cc16', '#10b981'],
            borderWidth: 0
          }
        ]
      };
    },
    donutOptions() {
      return {
        responsive: true,
        cutout: '76%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => ` ${context.label}: ${context.raw} đánh giá`
            }
          }
        }
      };
    }
  },
  methods: {
    formatCurrency(value) {
      if (!value) return "0 ₫";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    async loadDashboard() {
      this.loading = true;
      // cleared errorMessage

      try {
        const [partner, tours, locations] = await Promise.all([
          fetchPartnerSession(),
          fetchPartnerTours(),
          fetchPartnerLocations(),
        ]);

        this.partner = partner;
        this.tours = tours;
        this.locations = locations;

        // Fetch custom stats inside wrapper non-blocking
        Promise.all([
          goiApi(`/api/doi-tac/statistics/revenue?period=month`, { headers: { Accept: 'application/json'} }),
          goiApi(`/api/doi-tac/statistics/ratings`, { headers: { Accept: 'application/json'} })
        ]).then(async ([revenueRes, ratingsRes]) => {
          const revenuePayload = await revenueRes.json().catch(()=>({}));
          const ratingsPayload = await ratingsRes.json().catch(()=>({}));

          if (revenuePayload?.success && revenuePayload.data) {
             this.chuoiDoanhThu = (revenuePayload.data.chart_data || []).map(item => ({
                label: item.date.split('-').slice(1).join('/'),
                value: parseFloat(item.total_revenue)
            }));
            this.netRevenue = parseFloat(revenuePayload.data.net_revenue) || 0;
            this.pendingPayout = parseFloat(revenuePayload.data.pending_payout) || 0;
            this.paidCommission = parseFloat(revenuePayload.data.paid_to_admin_commission) || 0;
          }
          if (ratingsPayload?.success && ratingsPayload.data) {
            this.ratingDistribution = ratingsPayload.data.rating_distribution || [];
            this.avgRating = ratingsPayload.data.average_rating || 0;
            this.totalRatings = ratingsPayload.data.total_ratings || 0;
          }
        }).catch(err => console.error("Error fetching stats:", err));

      } catch (error) {
        toaster.error(error?.message || "Không thể tải dữ liệu tổng quan đối tác.");
        if (
          this.errorMessage.toLowerCase().includes("dang nhap")
          || this.errorMessage.toLowerCase().includes("khong hop le")
        ) {
          localStorage.removeItem("token");
          localStorage.removeItem("user");
          localStorage.removeItem("auth_type");
          window.dispatchEvent(new Event("storage"));
          this.$router.replace("/doi-tac/dang-nhap");
        }
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.loadDashboard();
  },
};
</script>

<style scoped>
.refresh-btn {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  border: 1px solid #bfdbfe;
  background: #eff6ff;
  color: #1d4ed8;
  font-weight: 700;
  padding: 0 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.refresh-btn:hover {
  background: #dbeafe;
  transform: translateY(-1px);
}

.alert-box {
  border-radius: 1rem;
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #b91c1c;
  padding: 1rem 1.5rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.finance-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
  transition: box-shadow 0.2s ease;
}

.stat-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.stat-card p {
  margin: 0;
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.stat-card strong {
  margin: 0.5rem 0;
  display: block;
  font-size: 2.25rem;
  color: #0f172a;
  font-weight: 900;
  line-height: 1.1;
}

.stat-card small {
  color: #64748b;
  font-size: 0.875rem;
}

.stat-card--warning {
  border-color: #fce7f3;
  background: linear-gradient(135deg, #fdf2f8, #fbcfe8);
}

.stat-card--accent {
  border-color: #dbeafe;
  background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
}

.quick-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.5rem;
}

.quick-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
}

.quick-card h3 {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
  color: #0f172a;
  font-weight: 800;
}

.quick-card p {
  margin: 0 0 1.5rem;
  color: #64748b;
  font-size: 1rem;
  line-height: 1.5;
  flex: 1;
}

.quick-card button {
  min-height: 2.75rem;
  border: 0;
  border-radius: 0.75rem;
  background: #1d4ed8;
  color: #fff;
  font-weight: 700;
  padding: 0 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
  align-self: flex-start;
}

  .quick-card button:hover {
  background: #1e40af;
  transform: translateY(-1px);
}

.chart-grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(320px, 1fr);
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.surface-card {
  border-radius: 1rem;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  padding: 1.25rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
}

.surface-card__head h3 {
  margin: 0;
  font-size: 1.15rem;
  font-weight: 800;
  color: #0f172a;
}

.surface-card__head p {
  margin: 0.25rem 0 1rem;
  font-size: 0.85rem;
  color: #64748b;
}

.chart-container {
  position: relative;
  width: 100%;
}

.donut-wrap {
  display: flex;
  justify-content: center;
  padding: 1rem 0;
}

.donut-center {
  position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;
}
.donut-center strong { font-size: 1.8rem; font-weight: 900; color: #0f172a; line-height: 1; }
.donut-center span { font-size: 0.8rem; font-weight: 700; color: #64748b; margin-top: 2px;}

.surface-card__empty {
  min-height: 200px;
  display: grid;
  place-items: center;
  color: #94a3b8;
  font-weight: 600;
  font-size: 0.95rem;
  font-style: italic;
  background: #f8fafc;
  border-radius: 0.75rem;
  border: 1px dashed #cbd5e1;
}

@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .finance-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .chart-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .stats-grid,
  .finance-grid,
  .quick-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
</style>
