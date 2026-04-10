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
          <span>Tổng Doanh Thu</span>
          <strong>{{ dinhDangTienTe(tongQuan.tong_doanh_thu) }}</strong>
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
          <strong>{{ tongQuan.tong_ke_hoach.toLocaleString('vi-VN') }}</strong>
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
            <h2>Biểu đồ doanh thu</h2>
            <p>Doanh thu đã thu được trong khoảng thời gian (nhóm theo ngày).</p>
          </div>
          <span class="table-chip">Xu hướng</span>
        </div>

        <div class="line-chart" v-if="chuoiDoanhThu.length > 0">
          <!-- Hiển thị chart bằng svg giống Dashboard -->
          <svg viewBox="0 0 760 320" preserveAspectRatio="none">
            <defs>
              <linearGradient id="lineFillBlue" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="rgba(37, 99, 235, 0.28)" />
                <stop offset="100%" stop-color="rgba(37, 99, 235, 0)" />
              </linearGradient>
            </defs>
            <path class="line-chart__area" :d="vungLine"></path>
            <path class="line-chart__path" :d="duongLine"></path>
          </svg>
          <div class="line-chart__labels">
            <!-- Hiển thị cách nhau để không bị tràn -->
            <span v-for="(point, index) in labelsHienThi" :key="index">{{ point.label }}</span>
          </div>
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
            <div class="donut-chart" :style="kieuBieuDoDonut">
              <div class="donut-chart__inner">
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
  </div>
</template>

<script>
import { goiApi } from '../../../services/httpClient.js';

export default {
  name: "QuanLyThongKe",
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
      
      filters: {
        fromDate: formatDate(from),
        toDate: formatDate(to)
      },

      tongQuan: {
        tong_doanh_thu: 0,
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
    };
  },
  computed: {
    kieuBieuDoDonut() {
      const total = this.tongQuan.so_luong_hoa_don.tong;
      if (!total) {
        return { background: 'conic-gradient(#e2e8f0 0 100%)' };
      }
      const pendingPct = (this.tongQuan.so_luong_hoa_don.cho_xu_ly / total) * 100;
      const paidPct = (this.tongQuan.so_luong_hoa_don.thanh_cong / total) * 100;
      const canceledPct = Math.max(0, 100 - pendingPct - paidPct);
      return {
        background: `conic-gradient(
          #f59e0b 0 ${pendingPct}%,
          #10b981 ${pendingPct}% ${pendingPct + paidPct}%,
          #ef4444 ${pendingPct + paidPct}% ${pendingPct + paidPct + canceledPct}%
        )`,
      };
    },
    // Tránh bị tràn nhãn biểu đồ nếu chuỗi ngày quá dài
    labelsHienThi() {
      if(this.chuoiDoanhThu.length <= 8) return this.chuoiDoanhThu;
      // Chia ra khoang 7-8 khoảng
      const step = Math.ceil(this.chuoiDoanhThu.length / 7);
      return this.chuoiDoanhThu.filter((_, index) => index % step === 0 || index === this.chuoiDoanhThu.length - 1);
    },
    duongLine() {
      return this.taoDuongBieuDo(this.chuoiDoanhThu);
    },
    vungLine() {
      if (!this.chuoiDoanhThu.length) return '';
      const line = this.taoDuongBieuDo(this.chuoiDoanhThu);
      return `${line} L 700 260 L 40 260 Z`;
    },
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
        const [tongQuanRes, bieuDoRes] = await Promise.all([
            goiApi(`/api/admin/thong-ke/tong-quan?${queryParams.toString()}`, { headers: { Accept: 'application/json' } }),
            goiApi(`/api/admin/thong-ke/bieu-do?${queryParams.toString()}`, { headers: { Accept: 'application/json' } })
        ]);

        const tongQuanPayload = await tongQuanRes.json().catch(() => ({}));
        const bieuDoPayload = await bieuDoRes.json().catch(() => ({}));

        if (tongQuanPayload?.success && tongQuanPayload.data) {
            this.tongQuan = tongQuanPayload.data;
        }

        if (bieuDoPayload?.success && bieuDoPayload.data) {
            this.chuoiDoanhThu = bieuDoPayload.data.map(item => ({
                label: item.label.split('-').slice(1).join('/'), // VD: 04/01
                value: parseFloat(item.total)
            }));
        } else {
             this.chuoiDoanhThu = [];
        }

      } catch (error) {
        console.error("Lỗi thống kê:", error);
        this.thongBaoLoi = "Không thể tải dữ liệu thống kê từ máy chủ.";
      } finally {
        this.dangTai = false;
      }
    },
    taoDuongBieuDo(series) {
      if (!series.length) return '';
      
      const max = Math.max(...series.map((item) => item.value), 1);
      const min = Math.min(...series.map((item) => item.value), 0);
      const width = 660; // Dành khoảng padding 2 bên tổng 100px (từ 40->700)
      const height = 210;
      const startX = 40;
      const startY = 30; // Từ 30 đến 240
      const step = series.length > 1 ? width / (series.length - 1) : width;

      const points = series.map((item, index) => {
        const ratio = max === min ? 0.5 : (item.value - min) / (max - min);
        const x = startX + step * index;
        const y = startY + (height - height * ratio);
        return { x, y };
      });

      return points.reduce((path, point, index) => {
        if (index === 0) return `M ${point.x} ${point.y}`;
        const prev = points[index - 1];
        // Curved line
        const controlX = (prev.x + point.x) / 2;
        return `${path} C ${controlX} ${prev.y}, ${controlX} ${point.y}, ${point.x} ${point.y}`;
      }, '');
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
  grid-template-columns: repeat(5, minmax(0, 1fr));
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

.surface-card__head h2 { margin: 0; color: #111827; font-size: 1.3rem; font-weight: 900; }
.surface-card__head p { margin: 0.35rem 0 0; color: #64748b; font-size: 0.9rem; }
.table-chip { padding: 0.65rem 0.95rem; border-radius: 0.95rem; background: #eef2ff; color: #4338ca; font-weight: 800; font-size: 0.8rem;}

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
.donut-chart { width: 175px; aspect-ratio: 1; border-radius: 50%; display: grid; place-items: center; }
.donut-chart__inner {
  width: 72%; aspect-ratio: 1; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; display: grid; place-items: center; align-content: center;
}
.donut-chart__inner strong { font-size: 1.6rem; font-weight: 900; color: #0f172a; line-height: 1; }
.donut-chart__inner span { font-size: 0.75rem; font-weight: 700; color: #64748b; margin-top: 5px;}

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

@media (max-width: 1400px) {
  .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 1024px) {
  .content-grid { grid-template-columns: 1fr; }
  .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
</style>
