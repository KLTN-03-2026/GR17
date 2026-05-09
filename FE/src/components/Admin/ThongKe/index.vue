<template>
  <div class="thongke-page">
    <section class="thongke-page__hero">
      <div>
        <p class="thongke-page__eyebrow">Bảng Điều Khiển Quản Trị</p>
        <h1>Quản lý Thống Kê</h1>
        <p class="thongke-page__subtitle">
          Theo dõi tổng quan doanh thu, tình trạng đơn hàng và khách hàng theo tuỳ chỉnh thời gian.
        </p>
      </div>

      <div class="thongke-page__hero-actions">
        <!-- Date filters -->
        <div class="filter-group">
          <label class="filter-label">Từ ngày</label>
          <input type="date" v-model="filters.fromDate" class="filter-input" />
        </div>
        <div class="filter-group">
          <label class="filter-label">Đến ngày</label>
          <input type="date" v-model="filters.toDate" class="filter-input" />
        </div>
        <button class="primary-button" type="button" @click="taiDuLieuThongKe" :disabled="dangTai">
          <i class="fas fa-search"></i>
          <span>{{ dangTai ? "Đang xử lý..." : "Lọc dữ liệu" }}</span>
        </button>
      </div>
    </section>

    <!-- Thống kê nhanh -->
    <section class="stats-grid">
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--blue">
          <i class="fas fa-money-bill-wave"></i>
        </div>
        <div>
          <span>Tổng Doanh Thu Hóa Đơn</span>
          <strong>{{ dinhDangTienTe(tongQuan.tong_doanh_thu) }}</strong>
        </div>
      </article>

      <article class="stats-card" style="border-left: 4px solid #db2777;">
        <div class="stats-card__icon" style="background: #fdf2f8; color: #db2777;">
          <i class="fas fa-hand-holding-dollar"></i>
        </div>
        <div>
          <span>Lợi Nhuận Nền Tảng (Hoa Hồng)</span>
          <strong style="color: #be185d;">{{ dinhDangTienTe(tongQuan.tong_hoa_hong) }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--green">
          <i class="fas fa-file-invoice"></i>
        </div>
        <div>
          <span>Hóa đơn thành công</span>
          <strong>{{ tongQuan.so_luong_hoa_don.thanh_cong.toLocaleString('vi-VN') }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--amber">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <span>Lượng Khách hàng</span>
          <strong>{{ tongQuan.tong_khach_hang.toLocaleString('vi-VN') }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--violet">
          <i class="fas fa-map-marked-alt"></i>
        </div>
        <div>
          <span>Tổng Tour</span>
          <strong>{{ tongQuan.tong_tour.toLocaleString('vi-VN') }}</strong>
        </div>
      </article>
      
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--sky">
          <i class="fas fa-calendar-check"></i>
        </div>
        <div>
          <span>Kế hoạch đã lập (AI)</span>
          <strong>{{ (tongQuan.tong_ke_hoach || 0).toLocaleString('vi-VN') }}</strong>
        </div>
      </article>
    </section>

    <div v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </div>

    <!-- Bảng Dữ Liệu & Biểu Đồ -->
    <section class="content-grid">
      <!-- Cột Trái: Chart -->
      <article class="surface-card surface-card--chart">
        <div class="surface-card__head">
          <div>
            <h2>Biểu đồ biến động</h2>
            <p>Chọn loại biểu đồ để xem Doanh thu hóa đơn hoặc Tiền hoa hồng.</p>
          </div>
          <div class="chart-tabs">
            <button :class="['chart-tab', chartType === 'revenue' ? 'active' : '']" @click="chartType = 'revenue'">
              Doanh Thu Hóa Đơn
            </button>
            <button :class="['chart-tab', chartType === 'commission' ? 'active' : '']" @click="chartType = 'commission'">
              Hoa Hồng Nền Tảng
            </button>
          </div>
        </div>

        <div class="line-chart" v-if="(chartType === 'revenue' ? chuoiDoanhThu : chuoiHoaHong).length > 0">
          <LineChart :data="chartData" :options="chartOptions" style="height: 250px" />
        </div>

        <div v-else class="surface-card__empty">
          <i class="fas fa-chart-line" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
          Không có dữ liệu doanh thu trong khoảng thời gian này.
        </div>
      </article>

      <!-- Cột Phải: Phân bổ -->
      <aside class="surface-side">
        <article class="surface-card">
          <div class="surface-card__head">
            <div>
              <h2>Trạng thái hóa đơn</h2>
              <p>Phân bố các khoản thanh toán.</p>
            </div>
          </div>

          <div class="donut-wrap">
            <div style="position: relative; width: 175px; height: 175px;">
              <DoughnutChart :data="donutData" :options="donutOptions" />
              <div class="donut-center">
                <strong v-if="tongQuan.so_luong_hoa_don.tong > 0">{{ ((tongQuan.so_luong_hoa_don.thanh_cong / tongQuan.so_luong_hoa_don.tong) * 100).toFixed(0) }}%</strong>
                <strong v-else>0%</strong>
                <span>Thành công</span>
              </div>
            </div>
          </div>

          <div class="legend-list">
            <div class="legend-item">
              <span class="legend-item__label"><span class="legend-dot legend-dot--pending"></span>Chờ xử lý</span>
              <strong>{{ tongQuan.so_luong_hoa_don.cho_xu_ly.toLocaleString('vi-VN') }}</strong>
            </div>
            <div class="legend-item">
              <span class="legend-item__label"><span class="legend-dot legend-dot--paid"></span>Đã thanh toán</span>
              <strong>{{ tongQuan.so_luong_hoa_don.thanh_cong.toLocaleString('vi-VN') }}</strong>
            </div>
            <div class="legend-item">
              <span class="legend-item__label"><span class="legend-dot legend-dot--canceled"></span>Hủy / Thất bại</span>
              <strong>{{ tongQuan.so_luong_hoa_don.huy.toLocaleString('vi-VN') }}</strong>
            </div>
          </div>
        </article>
      </aside>
    </section>

    <!-- Top Tours Section -->
    <section class="top-tours-section" v-if="topTours.length > 0">
      <div class="surface-card">
        <div class="surface-card__head">
          <div>
            <h2>🔥 Top Tour Bán Chạy Nhất</h2>
            <p>5 tour có số lượng đơn mua/thanh toán cao nhất trong khoảng thời gian.</p>
          </div>
        </div>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Hạng</th>
                <th>Thông tin Tour</th>
                <th>Đối tác cung cấp</th>
                <th class="text-right">Số lượt mua (Vé)</th>
                <th class="text-right">Tổng Doanh Thu</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in topTours" :key="item.ma_doi_tuong">
                <td><span class="rank-badge" :class="'rank-' + (idx + 1)">#{{ idx + 1 }}</span></td>
                <td>
                  <div class="tour-info">
                    <img :src="item.tour?.hinh_anh ? ('http://localhost:8000/storage/' + item.tour.hinh_anh) : 'https://placehold.co/100x100?text=No+Image'" class="tour-thumb" />
                    <strong>{{ item.tour?.ten_tour || 'Tour Không Xác Định' }}</strong>
                  </div>
                </td>
                <td>{{ item.tour?.doi_tac?.ten_doi_tac || 'N/A' }}</td>
                <td class="text-right"><strong>{{ item.total_orders }}</strong></td>
                <td class="text-right"><strong style="color: #10b981;">{{ dinhDangTienTe(item.revenue) }}</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, ArcElement, Filler } from 'chart.js';
import { Line as LineChart, Doughnut as DoughnutChart } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, ArcElement, Filler);

export default {
  name: "QuanLyThongKe",
  components: { LineChart, DoughnutChart },
  data() {
    // Mac dinh 30 ngày qua
    const to = new Date();
    const from = new Date();
    from.setDate(to.getDate() - 30);
    
    // Format YYYY-MM-DD cho input type="date"
    const formatDate = (date) => {
        const d = new Date(date);
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        const year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    };

    return {
      dangTai: false,
      thongBaoLoi: "",
      chartType: 'revenue',
      
      filters: {
        fromDate: formatDate(from),
        toDate: formatDate(to)
      },

      tongQuan: {
        tong_doanh_thu: 0,
        tong_hoa_hong: 0,
        so_luong_hoa_don: {
          thanh_cong: 0,
          cho_xu_ly: 0,
          huy: 0,
          tong: 0
        },
        tong_khach_hang: 0,
        tong_tour: 0,
        tong_ke_hoach: 0
      },

      chuoiDoanhThu: [],
      chuoiHoaHong: [],
      topTours: [],
    };
  },
  computed: {
    chartData() {
      const isRevenue = this.chartType === 'revenue';
      const rawData = isRevenue ? this.chuoiDoanhThu : this.chuoiHoaHong;
      return {
        labels: rawData.map(d => d.label),
        datasets: [
          {
            label: isRevenue ? 'Doanh Thu Hóa Đơn' : 'Lợi Nhuận Hoa Hồng',
            backgroundColor: isRevenue ? 'rgba(37, 99, 235, 0.2)' : 'rgba(219, 39, 119, 0.2)',
            borderColor: isRevenue ? '#2563eb' : '#db2777',
            borderWidth: 2,
            pointBackgroundColor: isRevenue ? '#2563eb' : '#db2777',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: isRevenue ? '#2563eb' : '#db2777',
            fill: true,
            data: rawData.map(d => d.value)
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
      return {
        labels: ['Chờ xử lý', 'Đã thanh toán', 'Hủy / Thất bại'],
        datasets: [
          {
            data: [
              this.tongQuan.so_luong_hoa_don.cho_xu_ly || 0,
              this.tongQuan.so_luong_hoa_don.thanh_cong || 0,
              this.tongQuan.so_luong_hoa_don.huy || 0
            ],
            backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
            borderWidth: 0
          }
        ]
      };
    },
    donutOptions() {
      return {
        responsive: true,
        cutout: '72%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => ` ${context.label}: ${context.raw} đơn`
            }
          }
        }
      };
    }
  },
  mounted() {
    this.taiDuLieuThongKe();
  },
  methods: {
    async taiDuLieuThongKe() {
      this.dangTai = true;
      this.thongBaoLoi = "";
      
      const queryParams = new URLSearchParams();
      if(this.filters.fromDate) queryParams.append('from_date', this.filters.fromDate);
      if(this.filters.toDate) queryParams.append('to_date', this.filters.toDate);

      try {
        const res = await goiApi(`/api/admin/statistics/revenue?${queryParams.toString()}`, { headers: { Accept: 'application/json' } });
        const payload = await res.json().catch(() => ({}));

        if (payload?.success) {
            if (payload.tong_quan) {
                this.tongQuan = payload.tong_quan;
            }
            if (payload.bieu_do) {
                this.chuoiDoanhThu = payload.bieu_do.map(item => ({
                    label: item.label.split('-').slice(1).join('/'),
                    value: parseFloat(item.total)
                }));
            } else {
                this.chuoiDoanhThu = [];
            }
            
            if (payload.bieu_do_hoa_hong) {
                this.chuoiHoaHong = payload.bieu_do_hoa_hong.map(item => ({
                    label: item.label.split('-').slice(1).join('/'),
                    value: parseFloat(item.total)
                }));
            } else {
                this.chuoiHoaHong = [];
            }
            
            if (payload.top_tours) {
                this.topTours = payload.top_tours;
            }
        } else {
            this.thongBaoLoi = "Lỗi tải dữ liệu thống kê.";
        }


      } catch (error) {
        console.error("Lỗi thống kê:", error);
        this.thongBaoLoi = "Không thể tải dữ liệu thống kê từ máy chủ.";
      } finally {
        this.dangTai = false;
      }
    },
    dinhDangTienTe(value) {
      if (!value) return "0 ₫";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    }
  }
};
</script>

<style scoped>
.thongke-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background:
    radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24%),
    linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.thongke-page__hero {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.thongke-page__eyebrow {
  margin: 0 0 0.4rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.thongke-page__hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.35rem, 4vw, 3.2rem);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.thongke-page__subtitle {
  margin: 0.8rem 0 0;
  color: #5f7191;
  font-size: 1.05rem;
  max-width: 760px;
}

.thongke-page__hero-actions {
  display: flex;
  align-items: flex-end;
  gap: 0.75rem;
  flex-wrap: wrap;
  background: white;
  padding: 1rem;
  border-radius: 1.25rem;
  border: 1px solid #ebeef5;
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.03);
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.filter-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}

.filter-input {
    min-height: 2.75rem;
    padding: 0 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    font-family: inherit;
    outline: none;
    transition: 0.2s;
    font-size: 0.95rem;
    color: #1e293b;
}

.filter-input:focus {
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.primary-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  min-height: 2.75rem;
  padding: 0 1.25rem;
  border-radius: 0.5rem;
  font-weight: 800;
  cursor: pointer;
  border: none;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
  transition: 0.2s;
  box-shadow: 0 4px 12px rgba(79, 37, 244, 0.22);
}
.primary-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(79, 37, 244, 0.3);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

.stats-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.45rem;
  border-radius: 1.6rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
}

.stats-card__icon {
  width: 4.25rem;
  height: 4.25rem;
  border-radius: 1.25rem;
  display: grid;
  place-items: center;
  font-size: 1.45rem;
  flex-shrink: 0;
}
.stats-card__icon--blue { background: #eaf4ff; color: #0369a1; }
.stats-card__icon--green { background: #eaf9f1; color: #08986c; }
.stats-card__icon--amber { background: #fff7e8; color: #d97706; }
.stats-card__icon--violet { background: #efefff; color: #4f25f4; }
.stats-card__icon--sky { background: #eef2ff; color: #4338ca; }

.stats-card span { display: block; color: #60708d; font-weight: 700; font-size: 0.85rem;}
.stats-card strong { display: block; margin-top: 0.35rem; color: #111b39; font-size: 1.6rem; line-height: 1; font-weight: 900;}


/* CHHART AND DONUT */
.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.8fr) minmax(330px, 1fr);
  gap: 1.2rem;
}

.surface-side {
  display: grid;
  gap: 1.2rem;
}

.surface-card {
  border-radius: 1.5rem;
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
  padding: 1.2rem;
}

.surface-card__head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.surface-card__head h2 { margin: 0; color: #111827; font-size: 1.2rem; font-weight: 900; }
.surface-card__head p { margin: 0.35rem 0 0; color: #64748b; font-size: 0.85rem; }

.chart-tabs { display: flex; gap: 0.5rem; background: #f1f5f9; padding: 0.25rem; border-radius: 0.5rem; }
.chart-tab { background: transparent; border: none; padding: 0.4rem 0.8rem; font-size: 0.85rem; font-weight: 700; color: #64748b; cursor: pointer; border-radius: 0.35rem; transition: 0.2s;}
.chart-tab:hover { color: #0f172a; }
.chart-tab.active { background: white; color: #0f172a; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

.line-chart { min-height: 280px; position: relative;}
.line-chart svg { width: 100%; height: 230px; overflow: visible; }
.line-chart__area { fill: url(#lineFillBlue); }
.line-chart__path {
  fill: none; stroke: #2563eb; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round;
}
.line-chart__labels {
  display: flex; justify-content: space-between; margin-top: 0.5rem; color: #64748b; font-size: 0.84rem; font-weight: 700;
}
.surface-card__empty { min-height: 280px; display: grid; place-items: center; align-content: center; text-align: center; color: #64748b; font-weight: 700; }

.donut-wrap { display: flex; justify-content: center; padding: 0.2rem 0 1rem; }
.donut-center { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;}
.donut-center strong { font-size: 1.6rem; font-weight: 900; color: #0f172a; line-height: 1; }
.donut-center span { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 5px;}

.legend-list { display: grid; gap: 0.8rem; margin-top: 1rem; }
.legend-item { display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem;}
.legend-item__label { display: flex; align-items: center; gap: 0.6rem; color: #475569; font-weight: 600; }
.legend-dot { width: 10px; height: 10px; border-radius: 50%; }
.legend-dot--pending { background: #f59e0b; }
.legend-dot--paid { background: #10b981; }
.legend-dot--canceled { background: #ef4444; }
.legend-item strong { color: #0f172a; font-weight: 800; font-size: 1.05rem; }

.notice { display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.1rem; border-radius: 1rem; margin-bottom: 1rem; }
.notice--error { background: #fff1f2; color: #be123c; }

.top-tours-section { margin-top: 1.5rem; }
.table-responsive { overflow-x: auto; margin-top: 1rem; }
.data-table { width: 100%; border-collapse: collapse; text-align: left; }
.data-table th { padding: 1rem; font-size: 0.85rem; color: #64748b; text-transform: uppercase; font-weight: 800; border-bottom: 2px solid #e2e8f0; }
.data-table td { padding: 1rem; border-bottom: 1px solid #e2e8f0; font-size: 0.95rem; color: #1e293b; vertical-align: middle;}
.tour-info { display: flex; align-items: center; gap: 1rem; }
.tour-thumb { width: 44px; height: 44px; border-radius: 0.5rem; object-fit: cover; }
.text-right { text-align: right; }
.rank-badge { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; font-weight: 900; font-size: 0.8rem; background: #f1f5f9; color: #64748b; }
.rank-1 { background: #fef08a; color: #854d0e; }
.rank-2 { background: #e2e8f0; color: #475569; }
.rank-3 { background: #fed7aa; color: #9a3412; }

@media (max-width: 1400px) {
  .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 1024px) {
  .content-grid { grid-template-columns: 1fr; }
  .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
</style>
