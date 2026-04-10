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
            {{ plan.description || "Trải nghiệm thành phố ngàn hoa với lịch trình tối ưu, từ những quán cafe săn mây đến những địa danh lịch sử nổi tiếng." }}
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

      <section v-if="dangTai" class="loading-state">
        <div class="spinner"></div> Đang tải dữ liệu...
      </section>

      <!-- Main Content Grid -->
      <div v-else-if="plan" class="content-grid">
        <!-- Left Column: Timeline -->
        <main class="timeline-section">
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
            
            <button class="btn btn--white-full">Xem chi tiết ngân sách</button>
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
          </div>

          <!-- Map Thumbnail Card -->
          <div class="card card--map">
            <div class="map-wrapper">
              <!-- Placeholder Map Image -->
              <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&q=80&w=600" alt="Map Route" class="map-image" />
              <div class="map-overlay">
                <span class="route-length">Lộ trình 32.5 km</span>
                <button class="btn btn--text-blue">MỞ BẢN ĐỒ</button>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script>
import { goiApi, API_ORIGIN } from '../../../services/httpClient.js';
import { API_BASE, buildHeaders, mapPlan, normalizeRecord } from "./planShared";

export default {
  name: "ChiTietKeHoach",
  data() {
    return {
      dangTai: false,
      dangXoa: false,
      thongBaoLoi: "",
      plan: null,
      
      // MOCK DATA for new UI
      mockTimeline: [
        {
          title: "Ngày 1: Chạm ngõ Cao Nguyên",
          activities: [
            {
              timeLabel: "SÁNG",
              time: "08:30",
              name: "Săn mây tại Đồi Chè Cầu Đất",
              description: "Khởi đầu ngày mới với không khí se lạnh, ngắm nhìn thảm mây bồng bềnh và thưởng thức ly trà nóng giữa đồi chè xanh mướt.",
              image: "https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?auto=format&fit=crop&q=80&w=400"
            },
            {
              timeLabel: "TRƯA",
              time: "12:00",
              name: "Ăn trưa Lẩu Gà Lá É Tao Ngộ",
              description: "Thưởng thức món đặc sản trứ danh của Đà Lạt với nước lẩu đậm đà, thơm nồng mùi lá é.",
              image: "https://images.unsplash.com/photo-1628294895950-9805252327bc?auto=format&fit=crop&q=80&w=400"
            },
            {
              timeLabel: "TỐI",
              time: "19:00",
              name: "Dạo Chợ Đêm Đà Lạt",
              description: "Khám phá thiên đường ẩm thực đường phố và mua sắm đồ len thủ công tại trung tâm thành phố.",
              image: "https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=400"
            }
          ]
        },
        {
          title: "Ngày 2: Văn hóa & Kiến trúc",
          activities: [
            {
              timeLabel: "SÁNG",
              time: "09:00",
              name: "Biệt thự Hằng Nga (Crazy House)",
              description: "Tham quan công trình kiến trúc kỳ lạ top thế giới với những đường nét uốn lượn như trong cổ tích.",
              image: "https://images.unsplash.com/photo-1575883584852-6a68364dbd42?auto=format&fit=crop&q=80&w=400"
            }
          ]
        }
      ],
      mockCostBreakdown: [
        { label: "Di chuyển", value: "1.200.000 VNĐ" },
        { label: "Lưu trú", value: "1.800.000 VNĐ" },
        { label: "Ăn uống & Vé", value: "1.500.000 VNĐ" }
      ],
      mockPlaces: [
        { name: "Hồ Xuân Hương", area: "Trung tâm thành phố", image: "https://images.unsplash.com/photo-1582239451403-53d9f2e46bc9?auto=format&fit=crop&q=80&w=150" },
        { name: "Tiệm Cà Phê Túi Mơ To", area: "Phường 11, Đà Lạt", image: "https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=150" },
        { name: "Dinh Bảo Đại", area: "Phường 4, Đà Lạt", image: "https://images.unsplash.com/photo-1586520165565-df457aa3ff1c?auto=format&fit=crop&q=80&w=150" }
      ]
    };
  },
  computed: {
    maKeHoach() {
      return String(this.$route.params.id || "");
    },
    displayTimeline() {
      if (!this.plan) return this.mockTimeline;
      
      const ai = this.plan.aiData || {};
      const lichTrinh = ai.lichTrinh || ai.lich_trinh || ai.itinerary;
      
      // 1. Process AI Data
      if (this.plan.source === "ai" && lichTrinh) {
        let daysArray = Array.isArray(lichTrinh) ? lichTrinh : Object.values(lichTrinh);
        return daysArray.map((ngay, idx) => {
          let hoatDongs = ngay.danhSachHoatDong || ngay.danh_sach_hoat_dong || ngay.hoat_dong || ngay.activities || [];
          if (!Array.isArray(hoatDongs)) hoatDongs = typeof hoatDongs === "object" ? Object.values(hoatDongs) : [];
          
          return {
            dateRaw: ngay.ngay_cu_the || null,
            title: ngay.tieuDe || ngay.tieu_de || ngay.title || `Ngày ${idx + 1}`,
            activities: hoatDongs.map(hd => ({
              timeLabel: hd.buoi || hd.thoiGian || hd.thoi_gian || "TRONG NGÀY",
              time: hd.thoiGian || hd.thoi_gian || hd.time || "",
              name: hd.tieuDe || hd.tieu_de || hd.ten_dia_diem || hd.name || "",
              description: hd.moTa || hd.mo_ta || hd.hoat_dong || hd.activity || "",
              image: hd.hinhanh || hd.hinh_anh || hd.hinhAnh || hd.image || ""
            }))
          };
        });
      }

      // 2. Process Manual Data
      if (this.plan.activities?.length > 0) {
        const groups = {};
        this.plan.activities.forEach(act => {
          const d = act.date || "Khác";
          if (!groups[d]) groups[d] = [];
          groups[d].push(act);
        });

        const sortedDates = Object.keys(groups).sort();
        return sortedDates.map((date, idx) => ({
          dateRaw: date,
          title: `Ngày ${idx + 1}`,
          activities: groups[date].sort((a,b) => String(a.startTime).localeCompare(String(b.startTime))).map(a => ({
            timeLabel: String(a.startTime).slice(0, 5),
            time: `${String(a.startTime).slice(0, 5)} - ${String(a.endTime).slice(0, 5)}`,
            name: a.locationName,
            description: a.locationDesc || a.locationAddress,
            image: a.locationByAiId ? a.locationByAiId : a.locationImage
          }))
        }));
      }

      return this.mockTimeline;
    },
    displayCostBreakdown() {
       if (!this.plan) return this.mockCostBreakdown;
       const ai = this.plan.aiData || {};
       const nganSach = ai.nganSach || ai.ngan_sach || ai.budget;
       
       if (nganSach) {
         return [{ label: "Ngân sách AI ước tính", value: typeof nganSach === 'number' ? `${nganSach.toLocaleString()}đ` : nganSach }];
       }
       
       if (this.plan.budgetLabel) {
         return [
           { label: "Ngân sách dự kiến", value: this.plan.budgetLabel },
           { label: "Số người", value: `${this.plan.soNguoi} người` }
         ];
       }
       return this.mockCostBreakdown;
    },
    displayPlaces() {
       const timeline = this.displayTimeline;
       if (timeline && timeline !== this.mockTimeline) {
          const places = [];
          timeline.forEach(day => {
            day.activities.forEach(act => {
              places.push({
                name: act.name,
                area: act.timeLabel,
                image: act.image
              });
            });
          });

          // Distinct by name
          const unique = [];
          const seen = new Set();
          for(const p of places) {
            if(!seen.has(p.name)) {
              unique.push(p);
              seen.add(p.name);
            }
            if(unique.length >= 3) break;
          }
          return unique.length > 0 ? unique : this.mockPlaces;
       }
       return this.mockPlaces;
    }
  },
  methods: {
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
      if (!confirm(`Bạn có chắc chắn muốn xóa kế hoạch "${this.plan?.name || this.maKeHoach}" không? Hành động này không thể hoàn tác.`)) {
        return;
      }
      
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
        
        alert("Xóa kế hoạch thành công!");
        this.$router.push('/khach-hang/ke-hoach');
      } catch (error) {
        alert(error.message || "Có lỗi xảy ra khi xóa kế hoạch.");
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

/* Map Thumbnail Card */
.card--map {
  border-radius: 24px;
}

.map-wrapper {
  position: relative;
  height: 240px;
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
