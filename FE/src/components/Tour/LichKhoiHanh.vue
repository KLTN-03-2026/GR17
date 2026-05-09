<template>
  <div class="departure-page">
    <section class="departure-page__hero">
      <div class="departure-page__copy">
        <p class="departure-page__eyebrow">Quản lý tour <span>&rsaquo;</span> Lịch khởi hành</p>
        <h1>Quản lý Lịch Khởi Hành</h1>
      </div>

      <div class="departure-page__actions">
        <button class="ghost-action" type="button">
          <i class="fas fa-tags"></i>
          <span>Cập nhật giá hàng loạt</span>
        </button>
        <button class="primary-action" type="button" @click="addSchedule">
          <i class="fas fa-plus"></i>
          <span>Tạo Lịch Khởi Hành Mới</span>
        </button>
      </div>
    </section>

    <section class="overview-grid">
      <article class="tour-summary-card">
        <div class="tour-summary-card__image" :style="{ backgroundImage: `url(${tour.image})` }"></div>

        <div class="tour-summary-card__body">
          <div class="tour-summary-card__meta">
            <span class="tour-summary-card__code">{{ tour.code }}</span>
            <span class="tour-summary-card__rating">
              <i class="fas fa-star"></i>
              {{ tour.rating }} ({{ tour.reviews }} đánh giá)
            </span>
          </div>

          <h2>{{ tour.name }}</h2>

          <div class="tour-summary-card__facts">
            <span><i class="fas fa-location-dot"></i>{{ tour.location }}</span>
            <span><i class="fas fa-clock"></i>{{ tour.duration }}</span>
          </div>
        </div>
      </article>

      <article class="metric-card">
        <div class="metric-card__eyebrow">Tổng lượt đặt chỗ</div>
        <div class="metric-card__value">{{ totalBookings.toLocaleString() }}</div>
        <div class="metric-card__delta">+12% trong tháng này</div>
        <div class="metric-card__footer">
          <span>Doanh thu: {{ revenueLabel }}</span>
          <span>Tỷ lệ lấp đầy TB: {{ averageOccupancy }}%</span>
        </div>
        <div class="metric-card__bars">
          <span></span>
          <span></span>
          <span></span>
          <span></span>
        </div>
      </article>
    </section>

    <section class="schedule-card">
      <header class="schedule-card__toolbar">
        <div class="schedule-tabs">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            type="button"
            :class="['schedule-tab', { 'is-active': activeTab === tab.key }]"
            @click="activeTab = tab.key"
          >
            {{ tab.label }}
          </button>
        </div>

        <div class="view-switches">
          <button type="button" class="view-switch">
            <i class="fas fa-calendar-days"></i>
          </button>
          <button type="button" class="view-switch is-active">
            <i class="fas fa-list"></i>
          </button>
        </div>
      </header>

      <div v-if="errorMessage" class="notice notice--error">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ errorMessage }}</span>
      </div>

      <div class="schedule-table">
        <div class="schedule-table__row schedule-table__row--head">
          <span>Ngày khởi hành</span>
          <span>Ngày về</span>
          <span>Số lượng (đặt chỗ)</span>
          <span>Điều chỉnh giá</span>
          <span>Trạng thái</span>
          <span>Thao tác</span>
        </div>

        <div
          v-for="item in paginatedSchedules"
          :key="item.id"
          :class="['schedule-table__row', { 'is-cancelled': item.statusKey === 'cancelled' }]"
        >
          <div class="date-stack">
            <strong>{{ item.departDay }}</strong>
            <span>{{ item.departMonth }}</span>
            <small>{{ item.departWeekday }}</small>
          </div>

          <div class="date-stack date-stack--return">
            <strong>{{ item.returnDay }}</strong>
            <span>{{ item.returnMonth }}</span>
            <small>{{ item.returnWeekday }}</small>
          </div>

          <div class="progress-cell">
            <div class="progress-cell__top">
              <strong>{{ item.booked }}/{{ item.capacity }}</strong>
              <span>{{ item.occupancy }}%</span>
            </div>
            <div class="progress-track">
              <span :class="['progress-track__fill', `progress-track__fill--${item.progressTone}`]" :style="{ width: `${item.occupancy}%` }"></span>
            </div>
          </div>

          <div class="price-cell">
            <strong>{{ item.priceAdjustmentLabel }}</strong>
            <span v-if="item.note" class="price-note">{{ item.note }}</span>
          </div>

          <div>
            <span :class="['status-pill', `status-pill--${item.statusKey}`]">{{ item.statusLabel }}</span>
          </div>

          <div class="action-list">
            <button class="icon-btn" type="button" title="Xem">
              <i class="fas fa-eye"></i>
            </button>
            <button class="icon-btn" type="button" title="Sửa">
              <i class="fas fa-pen"></i>
            </button>
            <button class="icon-btn" type="button" title="Xóa">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <footer class="schedule-card__footer">
        <span>Hiển thị {{ paginatedSchedules.length }} trên tổng {{ filteredSchedules.length }} ngày khởi hành</span>

        <div class="pagination">
          <button class="page-btn" type="button" :disabled="page === 1" @click="page = Math.max(1, page - 1)">
            <i class="fas fa-angle-left"></i>
          </button>
          <button
            v-for="pageNumber in totalPages"
            :key="pageNumber"
            type="button"
            :class="['page-btn', { 'is-active': page === pageNumber }]"
            @click="page = pageNumber"
          >
            {{ pageNumber }}
          </button>
          <button class="page-btn" type="button" :disabled="page === totalPages" @click="page = Math.min(totalPages, page + 1)">
            <i class="fas fa-angle-right"></i>
          </button>
        </div>
      </footer>
    </section>

    <section class="insight-grid">
      <article class="alert-card">
        <h3>Thông báo quan trọng</h3>
        <p>
          Có {{ nearFullSchedules }} lịch khởi hành trong tháng đang đạt từ 90% công suất.
          Hệ thống gợi ý tăng giá 15% hoặc mở thêm booking.
        </p>
        <button type="button">Xem chi tiết</button>
        <div class="alert-card__icon">
          <i class="fas fa-bullhorn"></i>
        </div>
      </article>

      <article class="trend-card">
        <div class="trend-card__head">
          <div>
            <h3>Xu hướng lấp đầy theo mùa</h3>
            <p>Báo cáo dự đoán 3 tháng tới</p>
          </div>
          <i class="fas fa-arrow-trend-up"></i>
        </div>

        <div class="trend-bars">
          <div v-for="item in trendSeries" :key="item.label" class="trend-bars__item">
            <span :style="{ height: `${item.value}%` }"></span>
            <strong>{{ item.label }}</strong>
          </div>
        </div>
      </article>
    </section>
  </div>

</template>

<script>
import { goiApi } from '../../services/httpClient.js';
const API_BASE = "/api";
const FALLBACK_TOUR_IMAGE = "https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=900&q=80";
import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "LichKhoiHanh",
  data() {
    return {
      errorMessage: "",
      activeTab: "all",
      page: 1,
      pageSize: 4,
      tour: this.createFallbackTour(),
      schedules: [],
      tabs: [
        { key: "all", label: "Tất cả lịch trình" },
        { key: "confirmed", label: "Đã xác nhận" },
        { key: "full", label: "Sắp hết chỗ" },
        { key: "history", label: "Lịch sử" },
      ],
      trendSeries: [
        { label: "T3", value: 40 },
        { label: "T4", value: 58 },
        { label: "T5", value: 82 },
        { label: "T6", value: 76 },
        { label: "T7", value: 48 },
      ],
    };
  },
  computed: {
    filteredSchedules() {
      if (this.activeTab === "all") return this.schedules;
      if (this.activeTab === "history") {
        return this.schedules.filter((item) => item.statusKey === "cancelled");
      }
      return this.schedules.filter((item) => item.statusKey === this.activeTab);
    },
    totalPages() {
      return Math.max(1, Math.ceil(this.filteredSchedules.length / this.pageSize));
    },
    paginatedSchedules() {
      const start = (this.page - 1) * this.pageSize;
      return this.filteredSchedules.slice(start, start + this.pageSize);
    },
    totalBookings() {
      return this.schedules.reduce((sum, item) => sum + item.booked, 0);
    },
    averageOccupancy() {
      if (!this.schedules.length) return 0;
      const total = this.schedules.reduce((sum, item) => sum + item.occupancy, 0);
      return Math.round(total / this.schedules.length);
    },
    revenueLabel() {
      const total = this.schedules.reduce((sum, item) => sum + item.projectedRevenue, 0);
      return `${new Intl.NumberFormat("vi-VN", {
        maximumFractionDigits: 0,
      }).format(total)} VNĐ`;
    },
    nearFullSchedules() {
      return this.schedules.filter((item) => item.occupancy >= 90 && item.statusKey !== "cancelled").length;
    },
  },
  watch: {
    activeTab() {
      this.page = 1;
    },
    totalPages() {
      if (this.page > this.totalPages) this.page = this.totalPages;
    },
  },
  methods: {
    buildHeaders() {
      const token = localStorage.getItem("token");
      const headers = { Accept: "application/json" };
      if (token) headers.Authorization = `Bearer ${token}`;
      return headers;
    },
    normalizeCollection(payload) {
      if (Array.isArray(payload)) return payload;
      if (Array.isArray(payload?.data)) return payload.data;
      if (Array.isArray(payload?.data?.data)) return payload.data.data;
      if (Array.isArray(payload?.result)) return payload.result;
      return [];
    },
    async fetchJson(path) {
      const response = await goiApi(`${API_BASE}${path}`, { headers: this.buildHeaders() });
      const data = await response.json().catch(() => null);
      if (!response.ok) {
        throw new Error(data?.message || `Yêu cầu thất bại: ${path}`);
      }
      return data;
    },
    createFallbackTour() {
      return {
        id: this.$route.params.id || 1,
        name: "Khám Phá Kỳ Quan Vịnh Hạ Long - 3 Ngày 2 Đêm",
        image: FALLBACK_TOUR_IMAGE,
        code: "ID:VN-DAD-2024",
        rating: "4.9",
        reviews: 124,
        location: "Quảng Ninh, Việt Nam",
        duration: "3 Ngày 2 Đêm",
      };
    },
    createFallbackSchedules() {
      return [
        this.mapSchedule({ id: 1, departure_date: "2024-05-25", return_date: "2024-05-27", booked: 15, capacity: 20, price_adjustment: 500000, status: "confirmed", note: "Mùa cao điểm" }),
        this.mapSchedule({ id: 2, departure_date: "2024-06-01", return_date: "2024-06-03", booked: 20, capacity: 20, price_adjustment: 1200000, status: "full", note: "Lễ hội" }),
        this.mapSchedule({ id: 3, departure_date: "2024-06-12", return_date: "2024-06-14", booked: 5, capacity: 20, price_adjustment: 0, status: "open", note: "" }),
        this.mapSchedule({ id: 4, departure_date: "2024-06-18", return_date: "2024-06-20", booked: 2, capacity: 20, price_adjustment: 0, status: "cancelled", note: "" }),
      ];
    },
    formatDateParts(value) {
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return { day: "--", month: "---", weekday: "" };
      }
      const month = date.toLocaleDateString("vi-VN", { month: "2-digit" });
      return {
        day: date.toLocaleDateString("vi-VN", { day: "2-digit" }),
        month: `Th${month}, ${date.getFullYear()}`,
        weekday: date.toLocaleDateString("vi-VN", { weekday: "long" }),
      };
    },
    resolveStatus(rawStatus, occupancy) {
      const normalized = String(rawStatus || "").toLowerCase();
      if (normalized.includes("cancel")) return { key: "cancelled", label: "Đã hủy" };
      if (normalized.includes("confirm")) return { key: "confirmed", label: "Đã xác nhận" };
      if (normalized.includes("full") || occupancy >= 100) return { key: "full", label: "Đã đầy" };
      return { key: "open", label: "Mở bán" };
    },
    formatAdjustment(value) {
      const amount = Number(value || 0);
      if (!amount) return "Giá mặc định";
      const formatted = new Intl.NumberFormat("vi-VN").format(Math.abs(amount));
      return `${amount > 0 ? "+" : "-"}${formatted}đ`;
    },
    mapTour(rawTour) {
      const fallback = this.createFallbackTour();
      return {
        id: rawTour?.id || rawTour?.ID || rawTour?.ma_tour || fallback.id,
        name: rawTour?.ten_tour || rawTour?.Ten_tour || rawTour?.name || fallback.name,
        image: Array.isArray(rawTour?.hinh_anh) && rawTour.hinh_anh[0] ? rawTour.hinh_anh[0] : rawTour?.image || fallback.image,
        code: rawTour?.ma_tour ? `ID:${rawTour.ma_tour}` : fallback.code,
        rating: rawTour?.rating || fallback.rating,
        reviews: rawTour?.reviews || fallback.reviews,
        location: rawTour?.dia_diem || rawTour?.location || fallback.location,
        duration: rawTour?.thoi_luong || rawTour?.duration || fallback.duration,
      };
    },
    mapSchedule(rawItem, index = 0) {
      const departParts = this.formatDateParts(rawItem.departure_date || rawItem.ngay_khoi_hanh || rawItem.ngay_di || rawItem.start_date);
      const returnParts = this.formatDateParts(rawItem.return_date || rawItem.ngay_ve || rawItem.end_date);
      const capacity = Number(rawItem.capacity || rawItem.so_luong || rawItem.suc_chua || rawItem.max_guests || 20);
      const booked = Number(rawItem.booked || rawItem.so_cho_da_dat || rawItem.sl_dat || 0);
      const occupancy = Math.min(100, Math.round((booked / Math.max(capacity, 1)) * 100));
      const status = this.resolveStatus(rawItem.status || rawItem.trang_thai, occupancy);
      const adjustment = Number(rawItem.price_adjustment || rawItem.dieu_chinh_gia || rawItem.phu_thu || 0);
      const tone = occupancy >= 100 ? "violet" : occupancy >= 70 ? "green" : occupancy >= 25 ? "amber" : "rose";

      return {
        id: rawItem.id || rawItem.ID || `${index}-${rawItem.departure_date || rawItem.ngay_khoi_hanh || Date.now()}`,
        departDay: departParts.day,
        departMonth: departParts.month,
        departWeekday: departParts.weekday,
        returnDay: returnParts.day,
        returnMonth: returnParts.month,
        returnWeekday: returnParts.weekday,
        booked,
        capacity,
        occupancy,
        priceAdjustmentLabel: this.formatAdjustment(adjustment),
        projectedRevenue: booked * Number(rawItem.gia || rawItem.gia_ve || rawItem.base_price || 5000000),
        note: rawItem.note || rawItem.ghi_chu || "",
        statusKey: status.key,
        statusLabel: status.label,
        progressTone: tone,
      };
    },
    matchesTour(rawItem) {
      const routeId = String(this.$route.params.id || "");
      const itemTourId = String(rawItem.tour_id || rawItem.id_tour || rawItem.ma_tour || rawItem.tourId || "");
      return !routeId || !itemTourId || itemTourId === routeId;
    },
    async loadDepartureData() {
      // cleared errorMessage

      try {
        const [tourResult, scheduleResult] = await Promise.allSettled([
          this.fetchJson(`/tour/${this.$route.params.id}`),
          this.fetchJson("/ke-hoach"),
        ]);

        if (tourResult.status === "fulfilled") {
          const rawTour = tourResult.value?.data?.data || tourResult.value?.data || tourResult.value;
          this.tour = this.mapTour(rawTour);
        } else {
          this.tour = this.createFallbackTour();
        }

        if (scheduleResult.status === "fulfilled") {
          const collection = this.normalizeCollection(scheduleResult.value);
          const matched = collection.filter((item) => this.matchesTour(item));
          this.schedules = (matched.length ? matched : collection).slice(0, 12).map((item, index) => this.mapSchedule(item, index));
        } else {
          this.schedules = this.createFallbackSchedules();
        }

        if (!this.schedules.length) {
          this.schedules = this.createFallbackSchedules();
        }

        if (tourResult.status === "rejected" || scheduleResult.status === "rejected") {
          toaster.error("API lịch khởi hành chưa đầy đủ, trang đang hiển thị dữ liệu mẫu để bạn tiếp tục chỉnh giao diện.");
        }
      } catch (error) {
        this.tour = this.createFallbackTour();
        this.schedules = this.createFallbackSchedules();
        toaster.error(error.message || "Không thể tải dữ liệu lịch khởi hành.");
      }
    },
    addSchedule() {
      const today = new Date();
      const depart = new Date(today);
      depart.setDate(today.getDate() + this.schedules.length * 7 + 3);
      const back = new Date(depart);
      back.setDate(depart.getDate() + 2);

      this.schedules.unshift(
        this.mapSchedule({
          id: `new-${Date.now()}`,
          departure_date: depart.toISOString(),
          return_date: back.toISOString(),
          booked: 0,
          capacity: 20,
          price_adjustment: 0,
          status: "open",
        })
      );
      this.page = 1;
    },
  },
  mounted() {
    this.loadDepartureData();
  },
};
</script>

<style scoped>
.departure-page {
  min-height: 100%;
  padding: 2rem;
  background: linear-gradient(180deg, #f7f8fd 0%, #f3f4f9 100%);
}

.departure-page__hero,
.overview-grid,
.insight-grid {
  display: grid;
  gap: 1.5rem;
}

.departure-page__hero {
  grid-template-columns: minmax(0, 1fr) auto;
  align-items: center;
  margin-bottom: 1.5rem;
}

.departure-page__eyebrow {
  margin: 0 0 0.55rem;
  color: #4f25f4;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.92rem;
  font-weight: 900;
}

.departure-page__eyebrow span {
  color: #94a3b8;
  margin: 0 0.4rem;
}

.departure-page__copy h1 {
  margin: 0;
  color: #111b39;
  font-size: clamp(2.4rem, 4vw, 3.45rem);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.departure-page__actions {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.ghost-action,
.primary-action {
  min-height: 3.4rem;
  padding: 0 1.4rem;
  border-radius: 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1rem;
  font-weight: 800;
}

.ghost-action {
  background: #ffffff;
  border: 1px solid #dfe4f2;
  color: #25324b;
}

.primary-action {
  border: 0;
  color: #ffffff;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  box-shadow: 0 18px 30px rgba(79, 37, 244, 0.28);
}

.overview-grid {
  grid-template-columns: minmax(0, 1.9fr) minmax(280px, 0.9fr);
  margin-bottom: 1.5rem;
}

.tour-summary-card,
.metric-card,
.schedule-card,
.alert-card,
.trend-card {
  background: #ffffff;
  border: 1px solid #e8ebf4;
  border-radius: 1.75rem;
  box-shadow: 0 18px 36px rgba(15, 23, 42, 0.04);
}

.tour-summary-card {
  display: grid;
  grid-template-columns: 136px minmax(0, 1fr);
  gap: 1.5rem;
  align-items: center;
  padding: 1.6rem;
}

.tour-summary-card__image {
  width: 136px;
  height: 136px;
  border-radius: 1.25rem;
  background-size: cover;
  background-position: center;
}

.tour-summary-card__meta,
.tour-summary-card__facts {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.tour-summary-card__code {
  padding: 0.35rem 0.65rem;
  border-radius: 0.6rem;
  background: #eef0ff;
  color: #4338f2;
  font-size: 0.8rem;
  font-weight: 800;
}

.tour-summary-card__rating {
  color: #f59e0b;
  font-weight: 800;
}

.tour-summary-card__body h2 {
  margin: 0.95rem 0 1rem;
  color: #121c39;
  font-size: clamp(1.75rem, 2.5vw, 2.4rem);
  line-height: 1.1;
  font-weight: 900;
}

.tour-summary-card__facts span {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  color: #5f7191;
  font-size: 1.02rem;
}

.metric-card {
  position: relative;
  overflow: hidden;
  padding: 1.9rem 1.7rem;
  color: #ffffff;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
}

.metric-card__eyebrow {
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-weight: 900;
  font-size: 0.9rem;
  opacity: 0.92;
}

.metric-card__value {
  margin-top: 0.7rem;
  font-size: 4rem;
  line-height: 1;
  font-weight: 900;
}

.metric-card__delta {
  margin-top: 0.4rem;
  font-size: 1.05rem;
  color: rgba(255, 255, 255, 0.9);
}

.metric-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 2rem;
  font-weight: 700;
}

.metric-card__bars {
  position: absolute;
  right: 1rem;
  bottom: 0.5rem;
  display: flex;
  align-items: flex-end;
  gap: 0.55rem;
  opacity: 0.14;
}

.metric-card__bars span {
  width: 14px;
  border-radius: 0.45rem 0.45rem 0 0;
  background: #ffffff;
}

.metric-card__bars span:nth-child(1) { height: 44px; }
.metric-card__bars span:nth-child(2) { height: 66px; }
.metric-card__bars span:nth-child(3) { height: 36px; }
.metric-card__bars span:nth-child(4) { height: 88px; }

.schedule-card {
  padding: 1.5rem;
}

.schedule-card__toolbar,
.schedule-card__footer,
.view-switches,
.schedule-tabs {
  display: flex;
  align-items: center;
}

.schedule-card__toolbar,
.schedule-card__footer {
  justify-content: space-between;
  gap: 1rem;
}

.schedule-card__toolbar {
  margin-bottom: 1.2rem;
  padding-bottom: 1.2rem;
  border-bottom: 1px solid #eef2f7;
}

.schedule-tabs {
  gap: 1.35rem;
  flex-wrap: wrap;
}

.schedule-tab {
  border: 0;
  padding: 0 0 0.6rem;
  background: transparent;
  color: #9aa5bb;
  font-size: 1rem;
  font-weight: 800;
}

.schedule-tab.is-active {
  color: #4338f2;
  box-shadow: inset 0 -2px 0 #4338f2;
}

.view-switches {
  gap: 0.7rem;
}

.view-switch {
  width: 3rem;
  height: 3rem;
  border: 1px solid #e6ebf5;
  border-radius: 0.95rem;
  background: #f8faff;
  color: #71819f;
}

.view-switch.is-active {
  border-color: transparent;
  color: #ffffff;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  box-shadow: 0 14px 24px rgba(79, 37, 244, 0.24);
}

.notice {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.95rem 1rem;
  border-radius: 1rem;
  margin-bottom: 1rem;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.schedule-table {
  display: grid;
}

.schedule-table__row {
  display: grid;
  grid-template-columns: 1.1fr 1.1fr 1.6fr 1.3fr 1fr 1fr;
  gap: 1rem;
  align-items: center;
  padding: 1.45rem 0;
  border-top: 1px solid #eef2f7;
}

.schedule-table__row--head {
  padding-top: 0;
  border-top: 0;
  color: #60708d;
  font-size: 0.82rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.schedule-table__row.is-cancelled {
  margin: 0 -1.5rem;
  padding-left: 1.5rem;
  padding-right: 1.5rem;
  background: rgba(255, 241, 242, 0.58);
}

.date-stack {
  display: grid;
  gap: 0.15rem;
}

.date-stack strong {
  color: #111b39;
  font-size: 1.12rem;
  line-height: 1.25;
}

.date-stack span {
  color: #23355f;
  font-size: 1rem;
}

.date-stack small {
  color: #94a3b8;
  font-size: 0.98rem;
}

.progress-cell {
  display: grid;
  gap: 0.55rem;
}

.progress-cell__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  color: #50617c;
  font-weight: 800;
}

.progress-track {
  width: 100%;
  height: 7px;
  border-radius: 999px;
  background: #e8edf5;
  overflow: hidden;
}

.progress-track__fill {
  display: block;
  height: 100%;
  border-radius: inherit;
}

.progress-track__fill--green { background: #10b981; }
.progress-track__fill--violet { background: #4f25f4; }
.progress-track__fill--amber { background: #f59e0b; }
.progress-track__fill--rose { background: #fb7185; }

.price-cell {
  display: grid;
  gap: 0.45rem;
}

.price-cell strong {
  color: #111b39;
  font-size: 1.05rem;
}

.price-note {
  width: fit-content;
  padding: 0.35rem 0.6rem;
  border-radius: 0.65rem;
  background: #fff7dd;
  color: #d97706;
  font-size: 0.84rem;
  font-weight: 800;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2rem;
  padding: 0 0.95rem;
  border-radius: 999px;
  font-size: 0.85rem;
  font-weight: 900;
}

.status-pill--confirmed { background: #e8fbf2; color: #059669; }
.status-pill--full { background: #f1efff; color: #4338f2; }
.status-pill--open { background: #eef2f7; color: #64748b; }
.status-pill--cancelled { background: #ffe4e6; color: #ef4444; }

.action-list {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.icon-btn {
  width: 2.2rem;
  height: 2.2rem;
  border: 0;
  border-radius: 0.8rem;
  background: transparent;
  color: #8b97ae;
}

.icon-btn:hover {
  background: #f4f6fb;
  color: #324053;
}

.schedule-card__footer {
  margin-top: 1.2rem;
  padding-top: 1.1rem;
}

.schedule-card__footer span {
  color: #8b97ae;
  font-weight: 700;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.page-btn {
  width: 2.7rem;
  height: 2.7rem;
  border: 1px solid #e8ecf5;
  border-radius: 0.85rem;
  background: #ffffff;
  color: #55657f;
  font-weight: 800;
}

.page-btn.is-active {
  border-color: transparent;
  color: #ffffff;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  box-shadow: 0 12px 22px rgba(79, 37, 244, 0.22);
}

.insight-grid {
  grid-template-columns: minmax(0, 1fr) minmax(360px, 0.95fr);
  margin-top: 1.7rem;
}

.alert-card {
  position: relative;
  overflow: hidden;
  padding: 2rem;
  color: #ffffff;
  background: linear-gradient(135deg, #3c2f97 0%, #2d2677 100%);
}

.alert-card h3,
.trend-card h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 900;
}

.alert-card p {
  max-width: 440px;
  margin: 0.95rem 0 1.5rem;
  color: rgba(255, 255, 255, 0.86);
  line-height: 1.6;
}

.alert-card button {
  min-height: 3.1rem;
  padding: 0 1.6rem;
  border: 0;
  border-radius: 0.95rem;
  background: #ffffff;
  color: #1f2562;
  text-transform: uppercase;
  font-weight: 900;
  letter-spacing: 0.05em;
}

.alert-card__icon {
  position: absolute;
  right: 1.8rem;
  bottom: 1.2rem;
  color: rgba(255, 255, 255, 0.13);
  font-size: 5rem;
}

.trend-card {
  padding: 1.8rem;
}

.trend-card__head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.trend-card__head p {
  margin: 0.4rem 0 0;
  color: #7f8ca7;
}

.trend-card__head i {
  color: #b0bad0;
}

.trend-bars {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 1rem;
  align-items: end;
  min-height: 230px;
  margin-top: 1.5rem;
}

.trend-bars__item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.85rem;
}

.trend-bars__item span {
  width: 100%;
  border-radius: 1rem 1rem 0.4rem 0.4rem;
  background: linear-gradient(180deg, rgba(125, 99, 255, 0.6), rgba(125, 99, 255, 0.28));
}

.trend-bars__item strong {
  color: #8a98b1;
  font-size: 0.9rem;
  letter-spacing: 0.08em;
}

@media (max-width: 1280px) {
  .overview-grid,
  .insight-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 1024px) {
  .departure-page {
    padding: 1rem;
  }

  .departure-page__hero {
    grid-template-columns: 1fr;
  }

  .schedule-table__row,
  .schedule-table__row--head {
    grid-template-columns: 1fr 1fr 1.2fr 1fr;
  }

  .schedule-table__row > :nth-child(5),
  .schedule-table__row > :nth-child(6),
  .schedule-table__row--head > :nth-child(5),
  .schedule-table__row--head > :nth-child(6) {
    display: none;
  }
}

@media (max-width: 768px) {
  .tour-summary-card {
    grid-template-columns: 1fr;
  }

  .departure-page__actions,
  .schedule-card__toolbar,
  .schedule-card__footer {
    flex-direction: column;
    align-items: stretch;
  }

  .schedule-table__row--head {
    display: none;
  }

  .schedule-table__row {
    grid-template-columns: 1fr;
    gap: 0.9rem;
  }

  .schedule-table__row.is-cancelled {
    margin: 0;
    padding-left: 0;
    padding-right: 0;
    background: transparent;
  }

  .pagination {
    flex-wrap: wrap;
  }
}
</style>






