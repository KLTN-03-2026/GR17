<template>
  <div class="premium-tour-editor">
    <div class="breadcrumb">
      QUẢN TRỊ &nbsp;❯&nbsp; CHUYẾN ĐI &nbsp;❯&nbsp; <span>CHI TIẾT TOUR</span>
    </div>

    <!-- Error/Success notices -->
    <div v-if="errorMessage" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </div>
    
    <!-- Hero Card -->
    <div class="hero-card">
      <div class="hero-image" :style="{ backgroundImage: `url(${form.images[0] || fallbackImg})` }">
        <span class="status-badge" :class="{ 'inactive': !form.published }">
          {{ form.published ? 'HIỂN THỊ' : 'BẢN NHÁP' }}
        </span>
        <label class="image-upload-overlay">
           <input type="file" accept="image/*" @change="handleImageUpload" multiple />
           <i class="fas fa-camera"></i> Đổi ảnh
        </label>
      </div>

      <div class="hero-content">
        <div class="hero-header">
          <div class="hero-title-group">
            <input v-model="form.name" class="title-input" placeholder="Nhập tên tour..." />
            <div class="location-group">
              <i class="fas fa-map-marker-alt"></i>
              <input v-model="form.location" class="location-input" placeholder="Địa điểm..." />
            </div>
          </div>
          <div class="price-badge">
            <small>GIÁ KHỞI ĐIỂM</small>
            <div class="price-input-wrapper">
               <input v-model.number="form.basePrice" class="price-input" type="number" />
               <span>đ</span>
            </div>
          </div>
        </div>

        <div class="stats-grid">
          <div class="stat-card">
             <div class="stat-icon stat-icon--blue"><i class="fas fa-clock"></i></div>
             <div class="stat-info">
               <small>THỜI GIAN</small>
               <input v-model="form.durationInfo" class="stat-input" placeholder="VD: 3 Ngày 2 Đêm" />
             </div>
          </div>
          <div class="stat-card">
             <div class="stat-icon stat-icon--blue"><i class="far fa-calendar-alt"></i></div>
             <div class="stat-info">
               <small>KHỞI HÀNH</small>
               <input v-model="form.departureInfo" class="stat-input" placeholder="VD: Hàng ngày" />
             </div>
          </div>
          <div class="stat-card">
             <div class="stat-icon stat-icon--blue"><i class="fas fa-bus"></i></div>
             <div class="stat-info">
               <small>PHƯƠNG TIỆN</small>
               <input v-model="form.transportInfo" class="stat-input" placeholder="VD: Xe Limousine" />
             </div>
          </div>
          <div class="stat-card">
             <div class="stat-icon stat-icon--blue"><i class="fas fa-users"></i></div>
             <div class="stat-info">
               <small>SỐ CHỖ</small>
               <div class="guest-input-group">
                 <input v-model.number="form.maxGuests" class="stat-input stat-input--number" type="number" />
                 <span>khách</span>
               </div>
             </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Timeline Itinerary Section -->
    <div class="itinerary-header">
      <div class="itinerary-title">
        <h2>Lịch trình chi tiết</h2>
        <p>Quản lý và cập nhật hoạt động từng ngày cho chuyến đi.</p>
      </div>
      <button class="btn-add-day-top" type="button" @click="addDay">
        <i class="fas fa-plus-circle"></i> Thêm lịch trình
      </button>
    </div>

    <div class="timeline-container">
      <div class="timeline-line"></div>

      <div class="timeline-day" v-for="(day, dIndex) in form.itinerary" :key="day.id">
        <div class="day-badge">
          <small>NGÀY</small>
          <strong>{{ String(dIndex + 1).padStart(2, '0') }}</strong>
        </div>

        <div class="day-card">
          <div class="day-card-header">
             <input v-model="day.title" class="day-title-input" placeholder="Tiêu đề ngày (VD: Khởi hành & Thăm Vịnh Hạ Long)" />
             <button class="btn-remove-day" @click="removeDay(dIndex)" title="Xóa ngày này"><i class="fas fa-trash-alt"></i></button>
          </div>

          <!-- Buổi Sáng -->
          <div class="activity-row">
            <div class="activity-indicator">
              <small>SÁNG</small>
              <div class="icon-circle morning"><i class="fas fa-sun"></i></div>
            </div>
            <div class="activity-content">
              <input v-model="day.morning.title" class="activity-title" placeholder="Tiêu đề hoạt động sáng..." />
              <textarea v-model="day.morning.desc" class="activity-desc" placeholder="Mô tả chi tiết..." rows="2"></textarea>
            </div>
          </div>

          <!-- Buổi Chiều -->
          <div class="activity-row">
            <div class="activity-indicator">
              <small>CHIỀU</small>
              <div class="icon-circle afternoon"><i class="fas fa-ship"></i></div>
            </div>
            <div class="activity-content">
              <input v-model="day.afternoon.title" class="activity-title" placeholder="Tiêu đề hoạt động chiều..." />
              <textarea v-model="day.afternoon.desc" class="activity-desc" placeholder="Mô tả chi tiết..." rows="2"></textarea>
            </div>
          </div>

          <!-- Buổi Tối -->
          <div class="activity-row">
            <div class="activity-indicator">
              <small>TỐI</small>
              <div class="icon-circle evening"><i class="fas fa-moon"></i></div>
            </div>
            <div class="activity-content">
              <input v-model="day.evening.title" class="activity-title" placeholder="Tiêu đề hoạt động tối..." />
              <textarea v-model="day.evening.desc" class="activity-desc" placeholder="Mô tả chi tiết..." rows="2"></textarea>
            </div>
          </div>

        </div>
      </div>

      <!-- Add Day Dashed Button -->
      <button class="add-day-dashed" type="button" @click="addDay">
        <div class="add-day-icon"><i class="fas fa-plus"></i></div>
        <span>Thêm Ngày mới cho hành trình</span>
      </button>
    </div>

    <!-- Action Bar -->
    <div class="premium-action-bar">
      <!-- Mới thêm các nút khác cho Backend tương thích -->
      <div class="switches-group">
         <label class="switch-item">
            <input type="checkbox" v-model="form.published" /> Công khai
         </label>
      </div>
      <div>
        <button class="btn-ghost" @click="goBack">Hủy thay đổi</button>
        <button class="btn-primary" @click="saveTour" :disabled="isSaving">
          <i v-if="isSaving" class="fas fa-spinner fa-spin"></i>
          <span>{{ isSaving ? "Đang lưu..." : "Lưu toàn bộ thay đổi" }}</span>
        </button>
      </div>
    </div>
  </div>

</template>

<script>
import { goiApi } from '../../services/httpClient.js';

import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({ position: "top-right" });
const API_BASE = "/api";
const FALLBACK_IMAGE = "https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=900&q=80";
export default {
  name: "ChiTietTour",
  data() {
    return {
      isLoading: false,
      isSaving: false,
      errorMessage: "",
      saveMessage: { type: "info", text: "" },
      fallbackImg: FALLBACK_IMAGE,
      form: this.createDefaultForm(),
    };
  },
  methods: {
    createDefaultForm() {
      return {
        id: "",
        name: "Vịnh Hạ Long – Kỳ quan thiên nhiên",
        category: "Nghỉ dưỡng & Biển",
        difficulty: "Trung bình",
        location: "Quảng Ninh, Việt Nam",
        description: "",
        highlights: [""],
        durationInfo: "3 Ngày 2 Đêm",
        departureInfo: "Hàng ngày",
        transportInfo: "Xe Limousine",
        images: [FALLBACK_IMAGE],
        basePrice: 4500000,
        salePrice: 4500000,
        maxGuests: 15,
        published: true,
        featured: false,
        seoKeywords: [],
        itinerary: [
          this.createEmptyDay(1)
        ],
      };
    },
    createEmptyDay(dayNum) {
      return {
        id: Date.now() + Math.random(),
        title: "Khởi hành & Thăm quan",
        morning: { title: "Đón khách & Di chuyển", desc: "" },
        afternoon: { title: "Lên du thuyền & Ăn trưa", desc: "" },
        evening: { title: "Hoạt động đêm", desc: "" }
      };
    },
    normalizeDayActivity(rawDesc) {
      try {
        const parsed = JSON.parse(rawDesc);
        if (parsed && typeof parsed === 'object' && parsed.morning) {
          return parsed;
        }
      } catch (e) {
        // Fallback if not JSON
      }
      return {
        morning: { title: "Hoạt động sáng", desc: rawDesc || "" },
        afternoon: { title: "Hoạt động chiều", desc: "" },
        evening: { title: "Hoạt động tối", desc: "" }
      };
    },
    buildHeaders() {
      const token = localStorage.getItem("token");
      const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
      };
      if (token) headers.Authorization = `Bearer ${token}`;
      return headers;
    },
    async requestJson(path, options = {}) {
      const response = await goiApi(`${API_BASE}${path}`, options);
      const data = await response.json().catch(() => null);
      if (!response.ok) {
        throw new Error(data?.message || `Yêu cầu thất bại: ${path}`);
      }
      return data;
    },
    normalizeBoolean(value, fallback = false) {
      if (typeof value === "boolean") return value;
      if (typeof value === "number") return value === 1;
      if (typeof value === "string") {
        return ["1", "true", "active", "published"].includes(value.toLowerCase());
      }
      return fallback;
    },
    normalizeTour(rawTour) {
      if (!rawTour) return this.createDefaultForm();
      const format = this.createDefaultForm();
      
      const itineraryRaw = Array.isArray(rawTour.lich_trinh || rawTour.itinerary) 
        ? (rawTour.lich_trinh || rawTour.itinerary) 
        : [];
      
      let itinerary = itineraryRaw.map((item, index) => {
        const act = this.normalizeDayActivity(item.mo_ta || item.description);
        return {
          id: item.id || item.ID || `${Date.now()}-${index}`,
          title: item.tieu_de || item.title || item.ten || `Ngày ${index + 1}`,
          morning: act.morning,
          afternoon: act.afternoon,
          evening: act.evening
        };
      });

      if (itinerary.length === 0) {
        itinerary = [this.createEmptyDay(1)];
      }

      // Restore specific highlights logic for dummy fields (location, transport, duration) mapping if needed
      // Or just use highlights map
      const highlightsArray = Array.isArray(rawTour.diem_noi_bat || rawTour.highlights) ? (rawTour.diem_noi_bat || rawTour.highlights) : [];
      const location = highlightsArray[0] || "Quảng Ninh, Việt Nam";
      const durationInfo = highlightsArray[1] || "3 Ngày 2 Đêm";
      const departureInfo = highlightsArray[2] || "Hàng ngày";
      const transportInfo = highlightsArray[3] || "Xe máy/Limousine";

      return {
        id: rawTour.id || rawTour.ID || rawTour.ma_tour || format.id,
        name: rawTour.ten_tour || rawTour.name || format.name,
        category: rawTour.danh_muc || rawTour.category || format.category,
        difficulty: rawTour.muc_do_kho || rawTour.difficulty || format.difficulty,
        description: rawTour.mo_ta || rawTour.description || format.description,
        location,
        durationInfo,
        departureInfo,
        transportInfo,
        highlights: highlightsArray,
        itinerary,
        images: Array.isArray(rawTour.hinh_anh || rawTour.images) && (rawTour.hinh_anh || rawTour.images).length > 0 
           ? (rawTour.hinh_anh || rawTour.images) 
           : format.images,
        basePrice: Number(rawTour.gia_goc || rawTour.price || format.basePrice),
        salePrice: Number(rawTour.gia_khuyen_mai || rawTour.sale_price || format.salePrice),
        maxGuests: Number(rawTour.so_khach_toi_da || rawTour.max_guests || format.maxGuests),
        published: this.normalizeBoolean(rawTour.trang_thai || rawTour.published, format.published),
        featured: this.normalizeBoolean(rawTour.tour_noi_bat || rawTour.featured, format.featured),
        seoKeywords: Array.isArray(rawTour.the_seo || rawTour.seo_keywords) ? (rawTour.the_seo || rawTour.seo_keywords) : format.seoKeywords,
      };
    },
    async loadTour() {
      this.isLoading = true;
      try {
        let rawTour;
        try {
          const payload = await this.requestJson(`/tour/${this.$route.params.id}`, {
            headers: this.buildHeaders(),
          });
          rawTour = payload?.data?.data || payload?.data || payload;
        } catch {
          const payload = await this.requestJson("/tour", {
            headers: this.buildHeaders(),
          });
          const collection = Array.isArray(payload) ? payload : payload?.data?.data || payload?.data || payload?.result || [];
          rawTour = collection.find((item) => String(item.id || item.ma_tour || item.Ma_tour) === String(this.$route.params.id));
        }
        this.form = this.normalizeTour(rawTour);
      } catch (error) {
        toaster.error(`Lỗi tải Tour: ${error.message}`);
      } finally {
        this.isLoading = false;
      }
    },
    addDay() {
      this.form.itinerary.push(this.createEmptyDay(this.form.itinerary.length + 1));
    },
    removeDay(index) {
      if (this.form.itinerary.length > 1) {
        this.form.itinerary.splice(index, 1);
      }
    },
    handleImageUpload(event) {
      Array.from(event.target.files || []).forEach((file) => {
        if (!file.type.startsWith("image/")) return;
        const reader = new FileReader();
        reader.onload = () => {
          if (this.form.images[0] === FALLBACK_IMAGE) this.form.images = [];
          this.form.images.push(reader.result);
        };
        reader.readAsDataURL(file);
      });
      event.target.value = "";
    },
    buildPayload() {
      // Store dummy string fields back to highlights
      const highlights = [
        this.form.location,
        this.form.durationInfo,
        this.form.departureInfo,
        this.form.transportInfo
      ];

      return {
        ten_tour: this.form.name,
        danh_muc: this.form.category,
        muc_do_kho: this.form.difficulty,
        mo_ta: this.form.description,
        diem_noi_bat: highlights,
        lich_trinh: this.form.itinerary.map((day, index) => ({
          ngay: index + 1,
          tieu_de: day.title,
          mo_ta: JSON.stringify({
            morning: day.morning,
            afternoon: day.afternoon,
            evening: day.evening
          }),
        })),
        hinh_anh: this.form.images,
        gia_goc: Number(this.form.basePrice) || 0,
        gia_khuyen_mai: Number(this.form.salePrice) || 0,
        so_khach_toi_da: Number(this.form.maxGuests) || 0,
        trang_thai: this.form.published ? 1 : 0,
        tour_noi_bat: this.form.featured ? 1 : 0,
        the_seo: this.form.seoKeywords,
      };
    },
    async saveTour() {
      this.isSaving = true;
      try {
        await this.requestJson(`/tour/${this.$route.params.id}`, {
          method: "PUT",
          headers: this.buildHeaders(),
          body: JSON.stringify(this.buildPayload()),
        });
        toaster.success("Đã lưu thay đổi thành công!"); // Temporary success notice
      } catch (error) {
        toaster.error(error.message || "Không thể lưu tour lúc này.");
      } finally {
        this.isSaving = false;
      }
    },
    goBack() {
      this.$router.push("/tour-management");
    },
  },
  mounted() {
    this.loadTour();
  },
};
</script>

<style scoped>
.premium-tour-editor {
  min-height: 100%;
  padding: 2rem;
  background-color: #f4f7fa; /* Light fresh bg */
  font-family: inherit;
  color: #1e293b;
}

/* Breadcrumb */
.breadcrumb {
  font-size: 0.75rem;
  font-weight: 800;
  color: #64748b;
  margin-bottom: 1.5rem;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.breadcrumb span {
  color: #0f172a;
}

/* Hero Section */
.hero-card {
  display: flex;
  gap: 24px;
  background: #ffffff;
  padding: 24px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
  margin-bottom: 40px;
}

.hero-image {
  width: 320px;
  min-height: 220px;
  border-radius: 16px;
  background-size: cover;
  background-position: center;
  position: relative;
  flex-shrink: 0;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.status-badge {
  display: inline-flex;
  margin: 16px;
  padding: 4px 12px;
  background: #0284c7;
  color: #fff;
  font-size: 0.75rem;
  font-weight: 800;
  border-radius: 8px;
  width: fit-content;
  box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
}
.status-badge.inactive {
  background: #64748b;
  box-shadow: none;
}

.image-upload-overlay {
  background: rgba(15, 23, 42, 0.6);
  color: #fff;
  padding: 12px;
  text-align: center;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.3s ease;
  backdrop-filter: blur(4px);
}
.hero-image:hover .image-upload-overlay {
  opacity: 1;
}
.image-upload-overlay input {
  display: none;
}

.hero-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.hero-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
}

.hero-title-group {
  flex: 1;
}

.title-input {
  width: 100%;
  font-size: 1.7rem;
  font-weight: 800;
  color: #0f172a;
  border: none;
  background: transparent;
  outline: none;
  letter-spacing: -0.02em;
  margin-bottom: 8px;
}
.title-input:focus {
  border-bottom: 2px solid #e2e8f0;
}

.location-group {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #475569;
}
.location-group i {
  font-size: 1rem;
}
.location-input {
  border: none;
  background: transparent;
  outline: none;
  color: #475569;
  font-size: 0.95rem;
  font-weight: 500;
  width: 100%;
}

.price-badge {
  background: #eff6ff;
  border-radius: 16px;
  padding: 10px 16px;
  text-align: right;
  flex-shrink: 0;
}
.price-badge small {
  display: block;
  font-size: 0.65rem;
  font-weight: 800;
  color: #64748b;
  margin-bottom: 4px;
}
.price-input-wrapper {
  display: flex;
  align-items: center;
  gap: 4px;
  color: #1d4ed8;
  font-size: 1.35rem;
  font-weight: 900;
}
.price-input {
  width: 100px;
  border: none;
  background: transparent;
  outline: none;
  color: inherit;
  font-weight: inherit;
  font-size: inherit;
  text-align: right;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-top: 24px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f8fafc;
  padding: 14px 16px;
  border-radius: 12px;
  border: 1px solid #f1f5f9;
}

.stat-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}
.stat-icon--blue {
  background: #e0f2fe;
  color: #0284c7;
}

.stat-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.stat-info small {
  color: #64748b;
  font-size: 0.65rem;
  font-weight: 800;
  text-transform: uppercase;
}
.stat-input {
  border: none;
  background: transparent;
  outline: none;
  color: #0f172a;
  font-weight: 700;
  font-size: 0.95rem;
  width: 100%;
}
.guest-input-group {
  display: flex;
  align-items: center;
  gap: 4px;
  color: #0f172a;
  font-weight: 700;
  font-size: 0.95rem;
}
.stat-input--number {
  width: 36px;
  text-align: center;
}

/* Itinerary Timeline */
.itinerary-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 32px;
}
.itinerary-title h2 {
  margin: 0;
  font-size: 1.4rem;
  color: #0f172a;
  font-weight: 800;
}
.itinerary-title p {
  margin: 4px 0 0;
  color: #64748b;
  font-size: 0.95rem;
}

.btn-add-day-top {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #0284c7;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-add-day-top:hover {
  background: #0369a1;
}

.timeline-container {
  position: relative;
  padding-left: 80px;
  margin-bottom: 40px;
}

.timeline-line {
  position: absolute;
  left: 40px;
  top: 40px;
  bottom: 80px;
  width: 2px;
  background: #cbd5e1;
  z-index: 1;
}

.timeline-day {
  position: relative;
  margin-bottom: 40px;
}

.day-badge {
  position: absolute;
  left: -40px;
  top: 10px;
  transform: translateX(-50%);
  width: 50px;
  height: 50px;
  background: #0c4a6e;
  border-radius: 50%;
  border: 4px solid #f4f7fa;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #fff;
  z-index: 2;
}
.day-badge small {
  font-size: 0.5rem;
  font-weight: 800;
  line-height: 1;
  margin-bottom: 2px;
}
.day-badge strong {
  font-size: 1rem;
  font-weight: 900;
  line-height: 1;
}

.day-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 32px;
  box-shadow: 0 10px 40px rgba(15, 23, 42, 0.04);
}

.day-card-header {
  display: flex;
  gap: 16px;
  align-items: center;
  margin-bottom: 24px;
}
.day-title-input {
  flex: 1;
  font-size: 1.15rem;
  font-weight: 800;
  color: #0f172a;
  border: none;
  background: transparent;
  outline: none;
}
.day-title-input:focus {
  border-bottom: 2px solid #e2e8f0;
}
.btn-remove-day {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  font-size: 1.1rem;
  transition: color 0.2s;
}
.btn-remove-day:hover {
  color: #e11d48;
}

/* Activities */
.activity-row {
  display: flex;
  gap: 24px;
  margin-top: 16px;
}
.activity-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 60px;
  flex-shrink: 0;
}
.activity-indicator small {
  font-size: 0.65rem;
  font-weight: 800;
  color: #64748b;
}

.icon-circle {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  margin-top: 8px;
}
.icon-circle.morning {
  background: #e0f2fe; color: #0284c7;
}
.icon-circle.afternoon {
  background: #dbeafe; color: #1d4ed8;
}
.icon-circle.evening {
  background: #e0e7ff; color: #3730a3;
}

.activity-content {
  flex: 1;
  background: #f8fafc;
  border-radius: 16px;
  padding: 20px;
  border: 1px solid #f1f5f9;
}
.activity-title {
  width: 100%;
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  border: none;
  background: transparent;
  outline: none;
  margin-bottom: 8px;
}
.activity-desc {
  width: 100%;
  font-size: 0.95rem;
  color: #475569;
  border: none;
  background: transparent;
  outline: none;
  resize: vertical;
  line-height: 1.6;
}

/* Dashed Button */
.add-day-dashed {
  width: 100%;
  background: #ffffff;
  border: 2px dashed #cbd5e1;
  border-radius: 20px;
  padding: 32px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  cursor: pointer;
  color: #475569;
  font-weight: 700;
  font-size: 1rem;
  transition: all 0.3s;
}
.add-day-dashed:hover {
  border-color: #0284c7;
  color: #0284c7;
  background: #f0f9ff;
}
.add-day-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #334155;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

/* Action Bar */
.premium-action-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #ffffff;
  padding: 16px 24px;
  border-radius: 20px;
  margin-top: 24px;
  box-shadow: 0 10px 40px rgba(15, 23, 42, 0.05);
}

.switches-group {
  display: flex;
  gap: 20px;
}
.switch-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  color: #0f172a;
  cursor: pointer;
}

.btn-ghost {
  background: transparent;
  border: none;
  color: #475569;
  font-weight: 700;
  padding: 12px 24px;
  cursor: pointer;
}
.btn-primary {
  background: #0284c7;
  color: #fff;
  border: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
  margin-left: 12px;
}

@media (max-width: 1024px) {
  .hero-card {
    flex-direction: column;
  }
  .hero-image {
    width: 100%;
    min-height: 260px;
  }
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>






