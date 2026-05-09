<template>
  <div class="dashboard-page">
    <section class="dashboard-page__hero">
      <div>
        <p class="dashboard-page__eyebrow">Bảng điều khiển quản trị</p>
        <h1>Tổng quan vận hành</h1>
        <p class="dashboard-page__subtitle">
          Theo dõi doanh thu, trạng thái thanh toán và sức khỏe API trong cùng một màn hình.
        </p>
      </div>

      <div class="dashboard-page__hero-actions">
        <div class="sync-chip" :class="{ 'sync-chip--loading': loading }">
          <span class="sync-chip__dot"></span>
          <span>{{ trangThaiDongBo }}</span>
        </div>

        <button type="button" class="ghost-button" @click="taiDuLieuDashboard" :disabled="loading">
          <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-rotate-right'"></i>
          <span>{{ loading ? 'Đang tải...' : 'Tải lại dữ liệu' }}</span>
        </button>
      </div>
    </section>

    <section class="update-bar">
      <span><i class="fas fa-clock"></i> Cập nhật lúc {{ nhanCapNhatGanNhat }}</span>
      <span><i class="fas fa-chart-line"></i> Tỷ lệ thành công: {{ chiSoTongQuan.paymentSuccessRate.toFixed(1) }}%</span>
    </section>

    <section class="stats-grid">
      <article
        v-for="(card, index) in theThongKe"
        :key="card.label"
        class="stats-card"
        :style="{ animationDelay: `${index * 0.05}s` }"
        @click="dieuHuongDen(card.route)"
      >
        <div class="stats-card__icon" :class="`stats-card__icon--theme-${index % 4}`">
          <i :class="card.icon"></i>
        </div>
        <div>
          <span>{{ card.label }}</span>
          <strong>{{ card.value }}</strong>
          <small>{{ card.caption }}</small>
        </div>
      </article>
    </section>

    <section class="content-grid">
      <article class="surface-card surface-card--chart">
        <div class="surface-card__head">
          <div>
            <h2>Xu hướng doanh thu 6 tháng</h2>
            <p>Doanh thu chỉ tính trên các hóa đơn đã thanh toán.</p>
          </div>
          <span class="table-chip">Doanh thu</span>
        </div>

        <div class="line-chart" v-if="coDuLieuDoanhThu">
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
            <span v-for="point in chuoiDoanhThu" :key="point.key">{{ point.label }}</span>
          </div>
        </div>

        <div v-else class="surface-card__empty">
          Chưa có dữ liệu doanh thu đã thanh toán để hiển thị biểu đồ.
        </div>
      </article>

      <aside class="surface-side">
        <article class="surface-card">
          <div class="surface-card__head">
            <div>
              <h2>Trạng thái thanh toán</h2>
              <p>Phân bố hóa đơn theo ba trạng thái chính.</p>
            </div>
          </div>

          <div class="donut-wrap">
            <div class="donut-chart" :style="kieuBieuDoDonut">
              <div class="donut-chart__inner">
                <strong>{{ chiSoTongQuan.paymentSuccessRate.toFixed(1) }}%</strong>
                <span>Thành công</span>
              </div>
            </div>
          </div>

          <div class="legend-list">
            <div class="legend-item">
              <span class="legend-item__label"><span class="legend-dot legend-dot--pending"></span>Chờ xử lý</span>
              <strong>{{ thongKeTrangThai.pending.toLocaleString('vi-VN') }}</strong>
            </div>
            <div class="legend-item">
              <span class="legend-item__label"><span class="legend-dot legend-dot--paid"></span>Đã thanh toán</span>
              <strong>{{ thongKeTrangThai.paid.toLocaleString('vi-VN') }}</strong>
            </div>
            <div class="legend-item">
              <span class="legend-item__label"><span class="legend-dot legend-dot--canceled"></span>Hủy / thất bại</span>
              <strong>{{ thongKeTrangThai.canceled.toLocaleString('vi-VN') }}</strong>
            </div>
          </div>
        </article>

        <article class="surface-card">
          <div class="surface-card__head">
            <div>
              <h2>Sức khỏe API</h2>
              <p>Giám sát các endpoint quan trọng theo thời gian thực.</p>
            </div>
          </div>

          <div class="health-list">
            <div class="health-item">
              <span>Trạng thái xác thực</span>
              <strong :class="loiXacThuc ? 'text-danger' : 'text-ok'">
                {{ loiXacThuc ? 'Cần đăng nhập lại' : 'Ổn định' }}
              </strong>
            </div>

            <div class="health-item">
              <span>Số API lỗi</span>
              <strong :class="danhSachLoiApi.length ? 'text-warn' : 'text-ok'">
                {{ danhSachLoiApi.length.toLocaleString('vi-VN') }}
              </strong>
            </div>

            <div class="health-item">
              <span>Tổng hóa đơn</span>
              <strong>{{ chiSoTongQuan.totalInvoices.toLocaleString('vi-VN') }}</strong>
            </div>
          </div>
        </article>
      </aside>
    </section>

    <section v-if="hienThiTrangThaiTrong" class="notice">
      Chưa có dữ liệu dashboard từ API. Vui lòng kiểm tra dữ liệu hệ thống và thử tải lại.
    </section>

    <section v-if="loiXacThuc" class="notice notice--danger">
      <i class="fas fa-triangle-exclamation"></i>
      <span>Không thể xác thực token (401/403) ở một số API dashboard. Vui lòng đăng nhập lại.</span>
    </section>

    <section v-if="danhSachLoiApi.length" class="notice notice--warn">
      <i class="fas fa-circle-info"></i>
      <span>Một số API đang lỗi: {{ danhSachLoiApi.join(', ') }}. Dashboard vẫn hiển thị phần dữ liệu còn lại.</span>
    </section>
  </div>
</template>

<script>
import { goiApi } from '../../services/httpClient.js';

const API_BASE = '/api';

const DANH_SACH_ENDPOINT = {
  invoices: { path: '/admin/hoa-don', name: 'admin/hoa-don' },
  tours: { path: '/tour', name: 'tour' },
  departures: { path: '/tour-khoi-hanh', name: 'tour-khoi-hanh' },
  reviews: { path: '/danh-gia-ke-hoach', name: 'danh-gia-ke-hoach' },
};

export default {
  name: 'DashboardPage',
  data() {
    return {
      loading: true,
      danhSachLoiApi: [],
      loiXacThuc: false,
      thoiDiemCapNhat: null,
      chiSoTongQuan: {
        paidRevenue: 0,
        totalInvoices: 0,
        paymentSuccessRate: 0,
        upcomingDepartures: 0,
        fullDepartures: 0,
        averageRating: 0,
      },
      thongKeTrangThai: {
        pending: 0,
        paid: 0,
        canceled: 0,
      },
      chuoiDoanhThu: [],
      tongHop: {
        tours: 0,
      },
    };
  },
  computed: {
    trangThaiDongBo() {
      if (this.loading) return 'Đang đồng bộ dữ liệu...';
      if (this.loiXacThuc) return 'Cần đăng nhập lại để đồng bộ dữ liệu';
      if (this.danhSachLoiApi.length) return 'Đồng bộ một phần dữ liệu';
      return 'Dữ liệu đã đồng bộ';
    },
    nhanCapNhatGanNhat() {
      if (!this.thoiDiemCapNhat) return '--';
      const d = this.thoiDiemCapNhat;
      const date = d.toLocaleDateString('vi-VN');
      const time = d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
      return `${time} ${date}`;
    },
    theThongKe() {
      return [
        {
          label: 'Tổng doanh thu',
          value: this.dinhDangTienTe(this.chiSoTongQuan.paidRevenue),
          caption: 'Chỉ tính hóa đơn đã thanh toán',
          icon: 'fas fa-money-bill-wave',
          route: '/admin/hoa-don',
        },
        {
          label: 'Tổng hóa đơn',
          value: this.chiSoTongQuan.totalInvoices.toLocaleString('vi-VN'),
          caption: 'Toàn bộ hóa đơn trong hệ thống',
          icon: 'fas fa-file-invoice',
          route: '/admin/hoa-don',
        },
        {
          label: 'Tỷ lệ giao dịch trực tuyến',
          value: `${this.chiSoTongQuan.paymentSuccessRate.toFixed(1)}%`,
          caption: 'Đã thanh toán / Tổng hóa đơn',
          icon: 'fas fa-chart-line',
          route: '/admin/hoa-don',
        },
        {
          label: 'Lịch khởi hành (7 ngày)',
          value: this.chiSoTongQuan.upcomingDepartures.toLocaleString('vi-VN'),
          caption: 'Các chuyến sắp diễn ra',
          icon: 'fas fa-calendar-day',
          route: '/admin/tour-khoi-hanh',
        },
        {
          label: 'Lịch hết chỗ',
          value: this.chiSoTongQuan.fullDepartures.toLocaleString('vi-VN'),
          caption: 'Đợt khởi hành không còn ghế trống',
          icon: 'fas fa-triangle-exclamation',
          route: '/admin/tour-khoi-hanh',
        },
        {
          label: 'Đánh giá trung bình',
          value: this.chiSoTongQuan.averageRating.toFixed(1),
          caption: 'Điểm sao trung bình từ người dùng',
          icon: 'fas fa-star',
          route: '/admin/reviews',
        },
      ];
    },
    coDuLieuDoanhThu() {
      return this.chuoiDoanhThu.some((item) => item.value > 0);
    },
    hienThiTrangThaiTrong() {
      return (
        !this.loading
        && this.chiSoTongQuan.totalInvoices === 0
        && this.chiSoTongQuan.upcomingDepartures === 0
        && this.chiSoTongQuan.averageRating === 0
      );
    },
    kieuBieuDoDonut() {
      const total = this.chiSoTongQuan.totalInvoices;
      if (!total) {
        return { background: 'conic-gradient(#e2e8f0 0 100%)' };
      }
      const pendingPct = (this.thongKeTrangThai.pending / total) * 100;
      const paidPct = (this.thongKeTrangThai.paid / total) * 100;
      const canceledPct = Math.max(0, 100 - pendingPct - paidPct);
      return {
        background: `conic-gradient(
          #f59e0b 0 ${pendingPct}%,
          #10b981 ${pendingPct}% ${pendingPct + paidPct}%,
          #ef4444 ${pendingPct + paidPct}% ${pendingPct + paidPct + canceledPct}%
        )`,
      };
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
  methods: {
    dieuHuongDen(path) {
      if (!path) return;
      this.$router.push(path);
    },
    taoHeaderXacThuc() {
      const headers = { Accept: 'application/json' };
      const token = localStorage.getItem('token');
      if (token) {
        headers.Authorization = `Bearer ${token}`;
      }
      return headers;
    },
    dinhDangTienTe(value) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        maximumFractionDigits: 0,
      }).format(Number(value) || 0);
    },
    chuanHoaDanhSach(payload) {
      if (Array.isArray(payload)) return payload;
      if (Array.isArray(payload?.data)) return payload.data;
      if (Array.isArray(payload?.data?.data)) return payload.data.data;
      if (Array.isArray(payload?.result)) return payload.result;
      return [];
    },
    chuanHoaSo(value) {
      const num = Number(value);
      return Number.isFinite(num) ? num : 0;
    },
    chuyenThanhNgay(value) {
      if (!value) return null;
      const d = new Date(value);
      return Number.isNaN(d.getTime()) ? null : d;
    },
    themLoiApi(name) {
      if (!this.danhSachLoiApi.includes(name)) this.danhSachLoiApi.push(name);
    },
    async taiDanhSachTuApi(endpoint) {
      try {
        const response = await goiApi(`${API_BASE}${endpoint.path}`, {
          method: 'GET',
          headers: this.taoHeaderXacThuc(),
        });

        if (response.status === 404) return [];

        if (response.status === 401 || response.status === 403) {
          this.loiXacThuc = true;
          this.themLoiApi(endpoint.name);
          return [];
        }

        if (!response.ok) {
          this.themLoiApi(endpoint.name);
          return [];
        }

        const payload = await response.json().catch(() => ({}));
        return this.chuanHoaDanhSach(payload);
      } catch (error) {
        this.themLoiApi(endpoint.name);
        return [];
      }
    },
    taoChuoiDoanhThu6ThangGanNhat(invoices) {
      const now = new Date();
      const bucketOrder = [];
      const bucketMap = {};

      for (let i = 5; i >= 0; i -= 1) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
        bucketOrder.push(key);
        bucketMap[key] = {
          key,
          label: `T${d.getMonth() + 1}`,
          value: 0,
        };
      }

      invoices.forEach((invoice) => {
        const status = this.chuanHoaSo(invoice.trang_thai_thanh_toan ?? invoice.Trang_thai_thanh_toan);
        if (status !== 1) return;
        const created = this.chuyenThanhNgay(invoice.ngay_tao ?? invoice.Ngay_tao ?? invoice.created_at);
        if (!created) return;

        const key = `${created.getFullYear()}-${String(created.getMonth() + 1).padStart(2, '0')}`;
        if (!bucketMap[key]) return;

        bucketMap[key].value += this.chuanHoaSo(invoice.tong_tien ?? invoice.Tong_tien ?? invoice.total);
      });

      return bucketOrder.map((key) => bucketMap[key]);
    },
    taoDuongBieuDo(series) {
      if (!series.length) return '';

      const max = Math.max(...series.map((item) => item.value), 1);
      const min = Math.min(...series.map((item) => item.value), 0);
      const width = 660;
      const height = 210;
      const startX = 40;
      const startY = 30;
      const step = series.length > 1 ? width / (series.length - 1) : width;

      const points = series.map((item, index) => {
        const ratio = (item.value - min) / (max - min || 1);
        const x = startX + step * index;
        const y = startY + (height - height * ratio);
        return { x, y };
      });

      return points.reduce((path, point, index) => {
        if (index === 0) return `M ${point.x} ${point.y}`;
        const prev = points[index - 1];
        const controlX = (prev.x + point.x) / 2;
        return `${path} C ${controlX} ${prev.y}, ${controlX} ${point.y}, ${point.x} ${point.y}`;
      }, '');
    },
    anhXaChiSo({ invoices, departures, reviews, tours }) {
      const invoiceStatuses = { pending: 0, paid: 0, canceled: 0 };
      let paidRevenue = 0;

      invoices.forEach((invoice) => {
        const status = this.chuanHoaSo(invoice.trang_thai_thanh_toan ?? invoice.Trang_thai_thanh_toan);
        if (status === 1) {
          invoiceStatuses.paid += 1;
          paidRevenue += this.chuanHoaSo(invoice.tong_tien ?? invoice.Tong_tien ?? invoice.total);
        } else if (status === 2) {
          invoiceStatuses.canceled += 1;
        } else {
          invoiceStatuses.pending += 1;
        }
      });

      const now = new Date();
      const in7Days = new Date();
      in7Days.setDate(now.getDate() + 7);
      const startToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      const endWindow = new Date(in7Days.getFullYear(), in7Days.getMonth(), in7Days.getDate(), 23, 59, 59, 999);

      const upcomingDepartures = departures.filter((item) => {
        const start = this.chuyenThanhNgay(item.ngay_bat_dau ?? item.Ngay_bat_dau);
        if (!start) return false;
        return start >= startToday && start <= endWindow;
      }).length;

      const fullDepartures = departures.filter((item) => {
        const seatsLeft = this.chuanHoaSo(item.so_cho ?? item.So_cho);
        return seatsLeft <= 0;
      }).length;

      const ratingValues = reviews
        .map((item) => this.chuanHoaSo(item.so_sao ?? item.So_sao ?? item.rating))
        .filter((val) => val > 0);

      const averageRating = ratingValues.length
        ? ratingValues.reduce((sum, value) => sum + value, 0) / ratingValues.length
        : 0;

      this.thongKeTrangThai = invoiceStatuses;
      this.chiSoTongQuan = {
        paidRevenue,
        totalInvoices: invoices.length,
        paymentSuccessRate: invoices.length ? (invoiceStatuses.paid / invoices.length) * 100 : 0,
        upcomingDepartures,
        fullDepartures,
        averageRating: Number(averageRating.toFixed(1)),
      };

      this.tongHop.tours = tours.length;
      this.chuoiDoanhThu = this.taoChuoiDoanhThu6ThangGanNhat(invoices);
    },
    async taiDuLieuDashboard() {
      this.loading = true;
      this.danhSachLoiApi = [];
      this.loiXacThuc = false;

      const [invoices, tours, departures, reviews] = await Promise.all([
        this.taiDanhSachTuApi(DANH_SACH_ENDPOINT.invoices),
        this.taiDanhSachTuApi(DANH_SACH_ENDPOINT.tours),
        this.taiDanhSachTuApi(DANH_SACH_ENDPOINT.departures),
        this.taiDanhSachTuApi(DANH_SACH_ENDPOINT.reviews),
      ]);

      this.anhXaChiSo({ invoices, departures, reviews, tours });
      this.thoiDiemCapNhat = new Date();
      this.loading = false;
    },
  },
  mounted() {
    this.taiDuLieuDashboard();
  },
};
</script>

<style scoped>
.dashboard-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background:
    radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24%),
    linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.dashboard-page__hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.dashboard-page__eyebrow {
  margin: 0 0 0.4rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.dashboard-page__hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.35rem, 4vw, 3.2rem);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.dashboard-page__subtitle {
  margin: 0.8rem 0 0;
  color: #5f7191;
  font-size: 1.05rem;
  max-width: 760px;
}

.dashboard-page__hero-actions {
  display: grid;
  gap: 0.75rem;
  min-width: 270px;
}

.sync-chip {
  min-height: 3.2rem;
  border-radius: 1rem;
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, 0.88);
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0 1rem;
  color: #334155;
  font-weight: 800;
}

.sync-chip__dot {
  width: 0.6rem;
  height: 0.6rem;
  border-radius: 999px;
  background: #16a34a;
}

.sync-chip--loading .sync-chip__dot {
  background: #2563eb;
  animation: nhapNhay 1.2s infinite;
}

.ghost-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  min-height: 3.2rem;
  padding: 0 1.25rem;
  border-radius: 1rem;
  font-weight: 800;
  cursor: pointer;
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, 0.88);
  color: #334155;
  transition: 0.2s;
}

.ghost-button:hover:not(:disabled) {
  background: #f8fafc;
  color: #0f172a;
}

.ghost-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.update-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
  min-height: 3rem;
  padding: 0 1rem;
  border-radius: 1rem;
  border: 1px solid #e1e7f3;
  background: #ffffff;
  color: #55657f;
  font-weight: 700;
  font-size: 0.9rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stats-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.15rem;
  border-radius: 1.35rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  animation: vaoLen 0.35s ease both;
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 18px 30px rgba(15, 23, 42, 0.08);
}

.stats-card__icon {
  width: 3.7rem;
  height: 3.7rem;
  border-radius: 1rem;
  display: grid;
  place-items: center;
  font-size: 1.2rem;
}

.stats-card__icon--theme-0 {
  background: #eaf4ff;
  color: #0369a1;
}

.stats-card__icon--theme-1 {
  background: #eaf9f1;
  color: #08986c;
}

.stats-card__icon--theme-2 {
  background: #fff7e8;
  color: #d97706;
}

.stats-card__icon--theme-3 {
  background: #efefff;
  color: #4f25f4;
}

.stats-card span {
  display: block;
  color: #60708d;
  font-weight: 700;
  font-size: 0.84rem;
}

.stats-card strong {
  display: block;
  margin-top: 0.25rem;
  color: #111b39;
  font-size: 1.45rem;
  line-height: 1;
  font-weight: 900;
}

.stats-card small {
  display: block;
  margin-top: 0.3rem;
  color: #64748b;
  font-size: 0.78rem;
}

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

.surface-card__head h2 {
  margin: 0;
  color: #111827;
  font-size: 1.3rem;
  font-weight: 900;
}

.surface-card__head p {
  margin: 0.35rem 0 0;
  color: #64748b;
  font-size: 0.9rem;
}

.table-chip {
  padding: 0.65rem 0.95rem;
  border-radius: 0.95rem;
  background: #eef2ff;
  color: #4338ca;
  font-weight: 800;
  font-size: 0.8rem;
}

.line-chart {
  min-height: 280px;
}

.line-chart svg {
  width: 100%;
  height: 230px;
  overflow: visible;
}

.line-chart__area {
  fill: url(#lineFillBlue);
}

.line-chart__path {
  fill: none;
  stroke: #2563eb;
  stroke-width: 4;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-dasharray: 1800;
  stroke-dashoffset: 1800;
  animation: veDuong 1s ease forwards;
}

.line-chart__labels {
  display: flex;
  justify-content: space-between;
  margin-top: 0.5rem;
  color: #64748b;
  font-size: 0.84rem;
  font-weight: 700;
}

.surface-card__empty {
  min-height: 180px;
  display: grid;
  place-items: center;
  text-align: center;
  color: #64748b;
  font-weight: 700;
}

.donut-wrap {
  display: flex;
  justify-content: center;
  padding: 0.2rem 0 1rem;
}

.donut-chart {
  width: 175px;
  aspect-ratio: 1;
  border-radius: 50%;
  display: grid;
  place-items: center;
}

.donut-chart__inner {
  width: 72%;
  aspect-ratio: 1;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  display: grid;
  place-items: center;
  text-align: center;
}

.donut-chart__inner strong {
  display: block;
  color: #1d4ed8;
  font-size: 1.45rem;
  font-weight: 900;
}

.donut-chart__inner span {
  display: block;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-size: 0.68rem;
  font-weight: 800;
}

.legend-list,
.health-list {
  display: grid;
  gap: 0.65rem;
}

.legend-item,
.health-item {
  min-height: 2.5rem;
  border-radius: 0.85rem;
  border: 1px solid #e7edf7;
  background: #f8faff;
  padding: 0 0.8rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.legend-item__label,
.health-item span {
  color: #475569;
  font-size: 0.87rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
}

.legend-item strong,
.health-item strong {
  color: #0f172a;
  font-size: 0.9rem;
  font-weight: 800;
}

.legend-dot {
  width: 0.6rem;
  height: 0.6rem;
  border-radius: 999px;
}

.legend-dot--pending {
  background: #f59e0b;
}

.legend-dot--paid {
  background: #10b981;
}

.legend-dot--canceled {
  background: #ef4444;
}

.text-danger {
  color: #b91c1c !important;
}

.text-warn {
  color: #b45309 !important;
}

.text-ok {
  color: #15803d !important;
}

.notice {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.1rem;
  border-radius: 1rem;
  margin-top: 1rem;
  border: 1px solid #dbe6f5;
  background: #ffffff;
  color: #475569;
}

.notice--danger {
  background: #fff1f2;
  color: #be123c;
  border-color: #fecdd3;
}

.notice--warn {
  background: #fff7ed;
  color: #9a3412;
  border-color: #fed7aa;
}

@keyframes veDuong {
  to {
    stroke-dashoffset: 0;
  }
}

@keyframes vaoLen {
  from {
    opacity: 0;
    transform: translateY(8px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes nhapNhay {
  0%,
  100% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.18);
  }
}

@media (max-width: 1360px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 1280px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .dashboard-page {
    padding: 1rem;
  }

  .dashboard-page__hero {
    flex-direction: column;
  }

  .dashboard-page__hero-actions {
    width: 100%;
    min-width: 0;
  }

  .update-bar {
    flex-direction: column;
    align-items: flex-start;
    padding: 0.8rem 1rem;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
