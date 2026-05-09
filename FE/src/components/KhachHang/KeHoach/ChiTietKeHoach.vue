<template>
  <div class="plan-detail-page">
    <div class="detail-shell">
      <!-- Header Area (Blue Background) -->
      <header class="detail-header">
        <div class="header-top">
          <div class="header-meta">
            <span class="tag tag--primary">HÀNH TRÌNH CÁ NHÂN</span>
            <span class="date-range" v-if="plan">
              <i class="far fa-calendar-alt"></i> {{ plan.dateRangeLabel }}
            </span>
          </div>
          <div class="header-actions">
            <router-link class="btn btn--back" to="/khach-hang/ke-hoach">
              Danh sách
            </router-link>
          </div>
        </div>

        <div class="header-content" v-if="plan">
          <h1>Chi tiết hành trình: {{ plan.name }}</h1>
          <p class="description">
            {{ plan.description || "Kế hoạch này chưa có mô tả chi tiết." }}
          </p>
          
          <div class="action-buttons">
            <router-link class="btn btn--white" :to="`/khach-hang/ke-hoach/${maKeHoach}/edit`">
              <i class="fas fa-pen"></i> Chỉnh sửa
            </router-link>
            <button class="btn btn--danger-light" @click="xacNhanXoa" :disabled="dangXoa">
              <i class="fas fa-spinner fa-spin" v-if="dangXoa"></i>
              <i class="fas fa-trash" v-else></i> Xóa
            </button>
          </div>
        </div>
      </header>

      <div v-if="thongBaoLoi" class="notice notice--error">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ thongBaoLoi }}</span>
      </div>
      <div v-if="generationNotice" class="notice notice--info">
        <i class="fas fa-circle-info"></i>
        <span>{{ generationNotice }}</span>
      </div>

      <section v-if="dangTai" class="loading-state">
        <div class="spinner"></div> Đang tải dữ liệu...
      </section>

      <!-- Main Content Grid -->
      <div v-else-if="plan" class="content-grid">
        <!-- Left Column: Timeline -->
        <main class="timeline-section">
          <div v-if="isPlanTimelineEmpty" class="timeline-empty-state">
            <h2>Chưa có lịch trình cụ thể</h2>
            <p>Hãy thêm địa điểm thủ công hoặc tạo lịch trình AI để timeline và bản đồ dùng dữ liệu thật.</p>
            <div class="empty-actions">
              <router-link class="btn btn--white" :to="`/khach-hang/ke-hoach/${maKeHoach}/edit`">
                <i class="fas fa-plus"></i> Thêm địa điểm thủ công
              </router-link>
              <button class="btn btn--primary" @click="taoLichTrinhAi" :disabled="dangTaoLichTrinh">
                <i class="fas" :class="dangTaoLichTrinh ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
                <span v-if="dangTaoLichTrinh">Đang tạo... {{ aiProgressPercent }}%</span>
                <span v-else>Tạo lịch trình AI</span>
              </button>
            </div>
            <div v-if="dangTaoLichTrinh" class="ai-progress">
              <div class="ai-progress__meta">
                <span>{{ aiProgressStage }}</span>
                <strong>{{ aiProgressPercent }}%</strong>
              </div>
              <div class="ai-progress__track">
                <div class="ai-progress__bar" :style="{ width: `${aiProgressPercent}%` }"></div>
              </div>
            </div>
          </div>

          <div class="timeline-day" v-for="(day, index) in displayTimeline" :key="index">
            <!-- Day Header -->
            <div class="day-header">
              <div class="day-badge">{{ String(index + 1).padStart(2, '0') }}</div>
              <h2>{{ day.title }}</h2>
              <span class="day-date-sub" v-if="day.dateRaw">{{ formatDateSimple(day.dateRaw) }}</span>
            </div>
            
            <!-- Timeline Line Container -->
            <div class="timeline-content">
              <div class="timeline-line"></div>
              
              <!-- Activity Cards -->
              <div class="activity-item" v-for="(activity, actIdx) in day.activities" :key="actIdx">
                <div class="activity-dot" :class="{'activity-dot--active': index === 0 && actIdx === 0}"></div>
                <div class="activity-card">
                  <div class="activity-image-wrapper" v-if="activity.image">
                    <img :src="resolveImageUrl(activity.image)" :alt="activity.name" class="activity-image" />
                  </div>
                  <div class="activity-details">
                    <div class="activity-meta">
                      <span class="time">{{ activity.timeLabel }} • {{ activity.time }}</span>
                      <button class="menu-btn"><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                    <h3>{{ activity.name }}</h3>
                    <p>{{ activity.description }}</p>
                  </div>
                </div>
              </div>

              <div v-if="day.activities.length === 0" class="empty-day-placeholder">
                Chưa có hoạt động nào cho ngày này.
              </div>
            </div>
          </div>
        </main>

        <!-- Right Column: Sidebar sidebar -->
        <aside class="sidebar-section">
          <!-- Cost Breakdown Card -->
          <div class="card card--dark-blue premium-card">
            <p class="card-subtitle">Tổng chi phí dự kiến</p>
            <h2 class="card-price">{{ plan.budgetLabel }}</h2>
            
            <ul class="cost-list">
              <li v-for="(item, idx) in displayCostBreakdown" :key="idx">
                <span>{{ item.label }}</span>
                <span>{{ item.value }}</span>
              </li>
            </ul>
            
            <button class="btn btn--white-full" @click="showBudget = true">Xem chi tiết ngân sách</button>
            <button v-if="hasTourActivity" class="btn btn--white-full" @click="thanhToanTour">
              Thanh toán tour trong chuyến đi của bạn
            </button>
          </div>

          <!-- Places to visit Card -->
          <div class="card card--white places-card">
            <div class="card-header-icon">
              <!-- Location pin SVG or FontAwesome -->
              <i class="fas fa-map-marker-alt"></i>
              <h3>Các địa điểm ghé thăm</h3>
            </div>
            
            <ul class="places-list">
              <li class="place-item" v-for="(place, idx) in displayPlaces" :key="idx">
                <img :src="resolveImageUrl(place.image, true)" :alt="place.name" class="place-thumb" />
                <div class="place-info">
                  <h4>{{ place.name }}</h4>
                  <p>{{ place.area }}</p>
                </div>
              </li>
            </ul>
            <p v-if="displayPlaces.length === 0" class="places-empty">Chưa có địa điểm trong lịch trình.</p>
          </div>

          <!-- Map Thumbnail Card -->
          <div class="card card--map">
            <div class="map-wrapper">
              <ItineraryMiniMap class="map-image" :activities="allMapActivities" />
              <div class="map-overlay">
                <span class="route-length">Lộ trình {{ totalRouteDistance }} km</span>
                <button class="btn btn--text-blue" @click="moBanDo" :disabled="!canOpenMap">
                  {{ canOpenMap ? "MỞ BẢN ĐỒ" : "CHƯA CÓ TỌA ĐỘ" }}
                </button>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <!-- Modal Bản Đồ -->
    <ItineraryMapModal :show="showMap" @update:show="showMap = $event" :activities="allMapActivities" />
    <BudgetDetailModal
      :show="showBudget"
      :plan="plan || {}"
      :timeline="displayTimeline"
      :budget-rows="budgetDetails"
      @update:show="showBudget = $event"
    />
  </div>
</template>

<script>
import { goiApi, API_ORIGIN } from '../../../services/httpClient.js';
import { API_BASE, buildHeaders, mapPlan, normalizeRecord, buildPlanTimeline, buildPlanMapActivities, buildBudgetDetails } from "./planShared";
import { showConfirm } from "../../../services/appDialog";
import { createToaster } from "@meforma/vue-toaster";
import ItineraryMapModal from "../../Shared/ItineraryMapModal.vue";
import ItineraryMiniMap from "../../Shared/ItineraryMiniMap.vue";
import BudgetDetailModal from "../../Shared/BudgetDetailModal.vue";

const toaster = createToaster({ position: "top-right" });

export default {
  name: "ChiTietKeHoach",
  components: {
    ItineraryMapModal,
    ItineraryMiniMap,
    BudgetDetailModal,
  },
  data() {
    return {
      dangTai: false,
      dangXoa: false,
      dangTaoLichTrinh: false,
      aiProgressPercent: 0,
      aiProgressStage: "",
      aiProgressTimer: null,
      showMap: false,
      showBudget: false,
      thongBaoLoi: "",
      generationNotice: "",
      plan: null,
    };
  },
  computed: {
    maKeHoach() {
      return String(this.$route.params.id || "");
    },
    displayCostBreakdown() {
      return this.budgetDetails.rows.slice(0, 3);
    },
    budgetDetails() {
      return buildBudgetDetails(this.plan || {}, this.displayTimeline);
    },
    displayTimeline() {
      return buildPlanTimeline(this.plan);
    },
    displayPlaces() {
      const places = [];
      this.displayTimeline.forEach(day => {
        day.activities.forEach(act => {
          if (!act.name) return;
          places.push({
            name: act.name,
            area: act.timeLabel,
            image: act.image
          });
        });
      });

      const unique = [];
      const seen = new Set();
      for (const place of places) {
        if (!seen.has(place.name)) {
          unique.push(place);
          seen.add(place.name);
        }
        if (unique.length >= 3) break;
      }
      return unique;
    },
    allMapActivities() {
      return buildPlanMapActivities(this.displayTimeline);
    },
    canOpenMap() {
      return this.allMapActivities.length > 0;
    },
     isPlanTimelineEmpty() {
       const timeline = this.displayTimeline;
       return timeline.length > 0 && timeline.every(day => day.activities.length === 0);
     },
     hasTourActivity() {
       return (this.plan?.activities || []).some(activity => activity.tourId && activity.scheduleId);
     },
    totalRouteDistance() {
      const acts = this.allMapActivities;
      if (acts.length < 2) return "0";
      let dist = 0;
      for (let i = 0; i < acts.length - 1; i++) {
        const p1 = acts[i];
        const p2 = acts[i + 1];
        dist += this.tinhKhoangCach(parseFloat(p1.vi_do), parseFloat(p1.kinh_do), parseFloat(p2.vi_do), parseFloat(p2.kinh_do));
      }
      return dist.toFixed(1);
    }
  },
  beforeUnmount() {
    this.cleanupAiProgress();
  },
  methods: {
    thanhToanTour() {
      const tourActivity = (this.plan?.activities || []).find(activity => activity.tourId && activity.scheduleId);
      if (!tourActivity) {
        toaster.error("Không tìm thấy thông tin tour và lịch khởi hành trong kế hoạch này.");
        return;
      }

      this.$router.push({
        path: `/khach-hang/tour/${tourActivity.tourId}/thanh-toan`,
        query: {
          schedule: tourActivity.scheduleId,
        },
      });
    },
    startAiProgress() {
      this.cleanupAiProgress();
      this.aiProgressPercent = 5;
      this.aiProgressStage = "Đang phân tích kế hoạch";
      this.aiProgressTimer = window.setInterval(() => {
        if (this.aiProgressPercent >= 92) return;
        const step = this.aiProgressPercent < 45 ? 7 : this.aiProgressPercent < 75 ? 4 : 2;
        this.aiProgressPercent = Math.min(92, this.aiProgressPercent + step);
        if (this.aiProgressPercent >= 75) {
          this.aiProgressStage = "Đang lưu bản nháp";
        } else if (this.aiProgressPercent >= 45) {
          this.aiProgressStage = "Đang tạo lịch trình";
        }
      }, 700);
    },
    completeAiProgress() {
      if (this.aiProgressTimer) {
        window.clearInterval(this.aiProgressTimer);
        this.aiProgressTimer = null;
      }
      this.aiProgressPercent = 100;
      this.aiProgressStage = "Đang lưu bản nháp";
    },
    cleanupAiProgress() {
      if (this.aiProgressTimer) {
        window.clearInterval(this.aiProgressTimer);
        this.aiProgressTimer = null;
      }
      this.aiProgressPercent = 0;
      this.aiProgressStage = "";
    },
    moBanDo() {
      if (!this.canOpenMap) {
        toaster.info("Chưa có tọa độ để vẽ bản đồ.");
        return;
      }
      this.showMap = true;
    },
    async taoLichTrinhAi() {
      if (this.dangTaoLichTrinh) return;
      this.dangTaoLichTrinh = true;
      this.generationNotice = "";
      this.startAiProgress();
      try {
        const phanHoi = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}/lich-trinh-ai`, {
          method: "POST",
          headers: buildHeaders(true),
        });
        const duLieu = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok) {
          throw new Error(duLieu.message || "Không thể tạo lịch trình AI.");
        }
        const generationMode = duLieu?.data?.generation_mode;
        const notice = duLieu?.data?.notice || "";
        if (generationMode === "fallback") {
          this.generationNotice = notice || "AI đang bận, hệ thống đã tạo bản nháp để bạn chỉnh tiếp.";
          toaster.info(this.generationNotice);
        } else {
          toaster.success(duLieu.message || "Đã tạo lịch trình AI cho kế hoạch.");
        }
        this.completeAiProgress();
        await this.taiKeHoachChiTiet();
        window.setTimeout(() => this.cleanupAiProgress(), 500);
      } catch (error) {
        this.cleanupAiProgress();
        toaster.error(error.message || "Không thể tạo lịch trình AI.");
      } finally {
        this.dangTaoLichTrinh = false;
      }
    },
    tinhKhoangCach(lat1, lon1, lat2, lon2) {
      if (isNaN(lat1) || isNaN(lon1) || isNaN(lat2) || isNaN(lon2)) return 0;
      const R = 6371; // km
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    },
    formatDateSimple(dateStr) {
      if (!dateStr || dateStr === "Khác") return "";
      try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString("vi-VN", { day: "2-digit", month: "2-digit" });
      } catch {
        return dateStr;
      }
    },
    resolveImageUrl(value, isThumb = false) {
      const fallbackActivityImage = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80";
      const fallbackThumbImage = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=150";
      const fallback = isThumb ? fallbackThumbImage : fallbackActivityImage;
      
      const raw = String(value || "").trim();
      if (!raw || raw === "null" || raw === "undefined") return fallback;
      
      if (/^https?:\/\//i.test(raw) || raw.startsWith("data:image/")) {
        return raw;
      }
      if (raw.startsWith("//")) {
        return `${window.location.protocol}${raw}`;
      }
      const path = raw.replace(/^\/+/, "");
      return `${API_ORIGIN}/${encodeURI(path)}`;
    },
    async taiKeHoachChiTiet() {
      this.dangTai = true;
      this.thongBaoLoi = "";

      try {
        const phanHoi = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}`, {
          headers: buildHeaders(),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tải chi tiết kế hoạch.");
        }

        this.plan = mapPlan(normalizeRecord(duLieuPhanHoi));
      } catch (error) {
        this.plan = null;
        this.thongBaoLoi = error.message || "Không thể tải chi tiết kế hoạch.";
      } finally {
        this.dangTai = false;
      }
    },
    async xacNhanXoa() {
      const isConfirmed = await showConfirm({
        title: "Xác nhận xóa",
        message: `Bạn có chắc chắn muốn xóa kế hoạch "${this.plan?.name || this.maKeHoach}" không? Hành động này không thể hoàn tác.`,
        tone: "warning"
      });
      if (!isConfirmed) return;
      
      this.dangXoa = true;
      try {
        const phanHoi = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}`, {
          method: 'DELETE',
          headers: buildHeaders(true),
        });
        
        if (!phanHoi.ok) {
          const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
          throw new Error(duLieuPhanHoi.message || "Lỗi khi xóa kế hoạch");
        }
        
        toaster.success("Xóa kế hoạch thành công!");
        this.$router.push('/khach-hang/ke-hoach');
      } catch (error) {
        toaster.error(error.message || "Có lỗi xảy ra khi xóa kế hoạch.");
      } finally {
        this.dangXoa = false;
      }
    },
  },
  mounted() {
    this.taiKeHoachChiTiet();
  },
};
</script>

<style scoped>
/* Base Variables & Layout */
.plan-detail-page {
  width: min(1280px, calc(100% - 40px));
  margin: 0 auto;
  padding: 32px 0 64px;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  color: #111827;
}

.detail-shell {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

/* --- Header Section --- */
.detail-header {
  background: #EAF4FD;
  border-radius: 24px;
  padding: 36px 48px;
  position: relative;
  overflow: hidden;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.header-meta {
  display: flex;
  align-items: center;
  gap: 16px;
}

.tag--primary {
  background: #DCECFB;
  color: #5850EC;
  font-weight: 800;
  font-size: 0.75rem;
  padding: 6px 14px;
  border-radius: 99px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

.date-range {
  color: #4B5563;
  font-size: 0.95rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

.header-content h1 {
  font-size: 2.5rem;
  font-weight: 900;
  color: #1E3A8A;
  margin: 0 0 16px;
  line-height: 1.25;
}

.header-content .description {
  color: #4B5563;
  font-size: 1.1rem;
  line-height: 1.6;
  max-width: 800px;
  margin: 0 0 32px;
}

.action-buttons {
  display: flex;
  gap: 16px;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 24px;
  border-radius: 99px;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  border: none;
  transition: all 0.2s ease;
  text-decoration: none;
}

.btn--white {
  background: #FFFFFF;
  color: #1E3A8A;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.btn--white:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}

.btn--danger-light {
  background: #FEE2E2;
  color: #DC2626;
}
.btn--danger-light:hover {
  background: #FCA5A5;
}

.btn--back {
  background: transparent;
  color: #4B5563;
  border: 1px solid #D1D5DB;
}
.btn--back:hover {
  background: white;
  color: #111827;
}

.btn--primary {
  background: #00476B;
  color: #FFFFFF;
}

.btn:disabled {
  cursor: not-allowed;
  opacity: 0.58;
  transform: none;
}

/* --- Main Grid --- */
.content-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 32px;
  align-items: start;
}

/* --- Left Column: Timeline --- */
.timeline-section {
  display: flex;
  flex-direction: column;
  gap: 48px;
}

.timeline-empty-state {
  padding: 24px;
  background: #FFFFFF;
  border: 1px solid #DBEAFE;
  border-radius: 20px;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
}

.timeline-empty-state h2 {
  margin: 0 0 8px;
  color: #0F172A;
  font-size: 1.25rem;
}

.timeline-empty-state p {
  margin: 0 0 18px;
  color: #64748B;
  line-height: 1.6;
}

.empty-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.ai-progress {
  margin-top: 18px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.ai-progress__meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  color: #475569;
  font-size: 0.9rem;
  font-weight: 700;
}

.ai-progress__track {
  height: 8px;
  overflow: hidden;
  border-radius: 999px;
  background: #E2E8F0;
}

.ai-progress__bar {
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #0F766E, #0284C7);
  transition: width 0.35s ease;
}

.timeline-day {
  display: flex;
  flex-direction: column;
}

.day-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.day-badge {
  width: 44px;
  height: 44px;
  background: #105378; /* Derived from the mockup's dark blue tone for the bubble */
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 1.1rem;
}

.day-header h2 {
  font-size: 1.5rem;
  font-weight: 800;
  color: #111827; /* Darker than the pill */
  margin: 0;
}

.day-badge {
  background: #00476B;
}

.timeline-content {
  position: relative;
  padding-left: 21px; /* Center of the 44px badge */
  margin-left: 1px;
}

.timeline-line {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 21px; /* Align with center of the day badge above */
  width: 2px;
  background: #E5E7EB;
  z-index: 0;
}

.activity-item {
  position: relative;
  margin-bottom: 32px;
  padding-left: 36px; /* spacing from the line */
  z-index: 1;
}
.activity-item:last-child {
  margin-bottom: 0;
}

.activity-dot {
  position: absolute;
  left: -6px; /* 21px (line) - 6px = 15px. Wait, the dot is relative to activity-item. */
  top: 24px; 
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #9CA3AF;
  border: 3px solid white;
}

.activity-dot--active {
  background: #00476B;
}

.activity-card {
  background: #FFFFFF;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  display: flex;
  overflow: hidden;
  border: 1px solid #F3F4F6;
  transition: transform 0.2s, box-shadow 0.2s;
}
.activity-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
}

.empty-day-placeholder {
  margin-left: 36px;
  padding: 18px 20px;
  background: #F8FAFC;
  border: 1px dashed #CBD5E1;
  border-radius: 16px;
  color: #64748B;
  font-weight: 600;
}

.activity-image-wrapper {
  width: 220px;
  flex-shrink: 0;
  position: relative;
  min-height: 160px;
}

.activity-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.activity-details {
  padding: 24px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.activity-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.time {
  font-weight: 800;
  color: #374151;
  font-size: 0.85rem;
  letter-spacing: 0.05em;
}

.menu-btn {
  background: none;
  border: none;
  color: #9CA3AF;
  cursor: pointer;
  font-size: 1.2rem;
}

.activity-details h3 {
  font-size: 1.2rem;
  font-weight: 800;
  margin: 0 0 8px;
  color: #111827;
}

.activity-details p {
  color: #4B5563;
  margin: 0;
  line-height: 1.6;
  font-size: 0.95rem;
}

/* --- Right Column: Sidebar --- */
.sidebar-section {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.card {
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.04);
}

/* Premium Dark Blue Card */
.card--dark-blue {
  background: #114E73;
  color: white;
  padding: 32px;
}

.card-subtitle {
  color: #D3E7F8;
  font-weight: 500;
  margin: 0 0 12px;
  font-size: 0.95rem;
}

.card-price {
  font-size: 2.2rem;
  font-weight: 800;
  margin: 0 0 28px;
  color: white;
}

.cost-list {
  list-style: none;
  padding: 0;
  margin: 0 0 32px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.cost-list li {
  display: flex;
  justify-content: space-between;
  color: white;
  font-size: 0.95rem;
  padding-bottom: 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}
.cost-list li:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.btn--white-full {
  width: 100%;
  background: white;
  color: #114E73;
  padding: 16px;
  border-radius: 12px;
  font-weight: 800;
}

/* Places Card */
.places-card {
  background: #F8FAFC;
  padding: 24px;
  border: 1px solid #F1F5F9;
}

.card-header-icon {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  color: #114E73;
}

.card-header-icon i {
  font-size: 1.1rem;
}

.card-header-icon h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 800;
  color: #111827;
}

.places-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.place-item {
  display: flex;
  align-items: center;
  gap: 16px;
  background: white;
  padding: 12px;
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.02);
}

.place-thumb {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  object-fit: cover;
}

.place-info h4 {
  margin: 0 0 4px;
  font-size: 0.95rem;
  font-weight: 700;
  color: #111827;
}

.place-info p {
  margin: 0;
  font-size: 0.8rem;
  color: #6B7280;
}

.places-empty {
  margin: 12px 0 0;
  color: #64748B;
  font-weight: 600;
}

/* Map Thumbnail Card */
.card--map {
  border-radius: 24px;
}

.map-wrapper {
  position: relative;
  height: 300px;
}

.map-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.map-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 16px 20px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.route-length {
  font-weight: 700;
  color: #111827;
  font-size: 0.95rem;
}

.btn--text-blue {
  background: none;
  border: none;
  color: #114E73;
  font-weight: 800;
  font-size: 0.85rem;
  letter-spacing: 0.05em;
  padding: 0;
}

.btn--text-blue:disabled {
  color: #64748B;
}

/* Utilities */
.loading-state {
  text-align: center;
  padding: 64px;
  color: #6B7280;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #E5E7EB;
  border-top-color: #3B82F6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.notice--error {
  padding: 16px;
  background: #FEF2F2;
  color: #DC2626;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.notice--info {
  padding: 16px;
  background: #EFF6FF;
  color: #1D4ED8;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}

@media (max-width: 1080px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .detail-header {
    padding: 24px;
  }
  .header-content h1 {
    font-size: 2rem;
  }
  .activity-card {
    flex-direction: column;
  }
  .activity-image-wrapper {
    width: 100%;
    height: 180px;
  }
}

.day-date-sub {
  color: #6B7280;
  font-weight: 600;
  font-size: 0.95rem;
  margin-left: 8px;
}
</style>
