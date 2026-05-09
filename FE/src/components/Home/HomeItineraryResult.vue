<template>
  <div class="result-content" v-if="ketQua">
    <!-- Cover Header -->
    <header class="result-hero" :style="{ backgroundImage: `linear-gradient(180deg, rgba(14,26,45,0.1), rgba(14,26,45,0.85)), url('${coverImage}')` }">
      <div class="result-hero__badge">ĐỀ XUẤT TỪ AI</div>
      <h2>{{ ketQua.tieuDe }}</h2>
      <div class="result-hero__meta">
        <span><i class="far fa-clock"></i> {{ ketQua.thoiGian }}</span>
        <span><i class="fas fa-wallet"></i> Ngân sách: {{ ketQua.nganSach }}</span>
      </div>
    </header>

    <!-- Timeline lộ trình Ngày -->
    <div class="timeline-day" v-for="(ngay, index) in displayedDays" :key="index">
      <div class="timeline-day__header">
        <h3>{{ 'Ngày ' + (ngay.ngay || ngay.ngayThang || (index + 1)) }}</h3>
        <span v-if="ngay.thoiGian">{{ ngay.thoiGian }}</span>
      </div>
      <div class="timeline-list">
        
        <div class="timeline-item" v-for="(hd, idx) in layDanhSachHoatDong(ngay)" :key="idx">
          <div class="timeline-item__icon" :class="getIconClass(hd.buoi || hd.thoiGian || hd.thoi_gian)"><i :class="getIcon(hd.buoi || hd.thoiGian || hd.thoi_gian)"></i></div>
          <div class="timeline-item__content">
            <div class="timeline-item__label" v-if="hd.buoi">{{ hd.buoi }}</div>
            <div class="timeline-card">
              <img :src="layHinhAnhHoatDong(hd)" :alt="hd.tieuDe || hd.tieu_de || 'Hình ảnh hoạt động'" @error="fallbackImage" />
              <div class="timeline-card__info">
                <h4><span v-if="hd.thoiGian || hd.thoi_gian">[{{ hd.thoiGian || hd.thoi_gian }}] </span>{{ hd.tieuDe || hd.tieu_de }}</h4>
                <p>{{ hd.moTa || hd.hoat_dong || hd.chiTiet }}</p>
                <div class="timeline-card__meta">
                  <span class="tag-price" v-if="hd.gia">{{ hd.gia }}</span>
                  <span class="tag-time" v-if="hd.thoiLuong"><i class="far fa-clock"></i> {{ hd.thoiLuong }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Nút xem thêm nếu có nhiều hơn 1 ngày -->
    <div class="expand-action" v-if="hasMoreDays && !isExpanded">
      <button class="btn-expand" @click="isExpanded = true">
        Xem chi tiết toàn bộ hành trình ({{ normalizedDays.length }} ngày) <i class="fas fa-chevron-down"></i>
      </button>
    </div>

    <!-- Nút thu gọn nếu đang mở rộng -->
    <div class="expand-action" v-if="hasMoreDays && isExpanded">
      <button class="btn-expand" @click="isExpanded = false">
        Thu gọn hành trình <i class="fas fa-chevron-up"></i>
      </button>
    </div>

    <!-- Tổng chi phí Footer -->
    <div class="result-footer">
      <div class="total-cost">
        <div class="total-cost__icon"><i class="fas fa-wallet"></i></div>
        <div>
          <span class="label">Tổng chi phí dự kiến</span>
          <div class="amount">{{ ketQua.tongChiPhi }}</div>
        </div>
      </div>
      <div class="footer-actions">
        <button class="btn-solid" @click="luuHanhTrinh" :disabled="dangLuu">
          <i class="fas fa-spinner fa-spin" v-if="dangLuu"></i>
          <i class="fas fa-save" v-else></i>
          {{ dangLuu ? "Đang lưu..." : "Lưu Hành Trình Vào Tài Khoản" }}
        </button>
      </div>
    </div>

  </div>
</template>

<script>
import { API_ORIGIN } from "../../services/httpClient.js";

export default {
  name: "HomeItineraryResult",
  props: {
    ketQua: {
      type: Object,
      required: true
    },
    dangLuu: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isExpanded: false,
      fallbackCoverImage: "https://images.unsplash.com/photo-1596347958988-cb942eb22eb7?w=1000",
      fallbackActivityImage: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop",
    }
  },
  computed: {
    coverImage() {
      return this.resolveImageUrl(this.ketQua?.hinhAnh || "", this.fallbackCoverImage);
    },
    normalizedDays() {
      const raw = this.ketQua?.lichTrinh;
      if (Array.isArray(raw)) return raw;
      if (raw && typeof raw === "object") return Object.values(raw);
      return [];
    },
    displayedDays() {
      if (this.isExpanded) return this.normalizedDays;
      return this.normalizedDays.slice(0, 1);
    },
    hasMoreDays() {
      return this.normalizedDays.length > 1;
    }
  },
  methods: {
    resolveImageUrl(value, fallback = "") {
      const raw = String(value || "").trim();
      if (!raw) return fallback;
      if (/^https?:\/\//i.test(raw) || raw.startsWith("data:image/")) {
        return raw;
      }
      if (raw.startsWith("//")) {
        return `${window.location.protocol}${raw}`;
      }
      const path = raw.replace(/^\/+/, "");
      return `${API_ORIGIN}/${encodeURI(path)}`;
    },
    layDanhSachHoatDong(ngay) {
      const ds = ngay?.danhSachHoatDong ?? ngay?.hoatDong ?? [];
      if (Array.isArray(ds)) return ds;
      if (ds && typeof ds === "object") return Object.values(ds);
      return [];
    },
    layHinhAnhHoatDong(hoatDong = {}) {
      return this.resolveImageUrl(
        hoatDong.hinhanh || hoatDong.hinh_anh || hoatDong.hinhAnh || "",
        this.fallbackActivityImage
      );
    },
    fallbackImage(event) {
      const target = event?.target;
      if (!target) return;
      if (target.src !== this.fallbackActivityImage) {
        target.src = this.fallbackActivityImage;
      }
    },
    getIconClass(thoiGian) {
      if (!thoiGian) return 'icon--morning';
      const tg = thoiGian.toLowerCase();
      if (tg.includes('chiều')) return 'icon--afternoon';
      if (tg.includes('tối') || tg.includes('đêm')) return 'icon--evening';
      return 'icon--morning';
    },
    getIcon(thoiGian) {
      if (!thoiGian) return 'fas fa-sun';
      const tg = thoiGian.toLowerCase();
      if (tg.includes('sáng')) return 'fas fa-sun';
      if (tg.includes('chiều')) return 'fas fa-cloud-sun';
      if (tg.includes('tối') || tg.includes('đêm')) return 'fas fa-moon';
      return 'fas fa-sun';
    },
    luuHanhTrinh() {
      if (this.dangLuu) return;
      this.$emit("luu-hanh-trinh");
    }
  }
}
</script>

<style scoped>
.result-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
  animation: slide-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

/* HERO */
.result-hero {
  position: relative;
  height: 300px;
  border-radius: 24px;
  padding: 30px;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  background-size: cover;
  background-position: center;
  color: #ffffff;
  box-shadow: 0 10px 24px rgba(0,0,0,0.1);
  overflow: hidden;
}
.result-hero__badge {
  position: absolute;
  top: 30px;
  left: 30px;
  background: #3b82f6;
  color: #ffffff;
  font-size: 0.75rem;
  font-weight: 800;
  padding: 8px 16px;
  border-radius: 999px;
  letter-spacing: 0.5px;
}
.result-hero h2 {
  font-size: 2.8rem;
  font-weight: 900;
  margin: 0 0 10px;
  color: #ffffff !important;
  text-shadow: 0 4px 15px rgba(0,0,0,0.8);
  -webkit-text-stroke: 1px #ffffff;
}
.result-hero__meta {
  display: flex;
  gap: 16px;
  font-size: 1.05rem;
  font-weight: 600;
  color: #ffffff;
  text-shadow: 0 2px 5px rgba(0,0,0,0.8);
}
/* TIMELINE BOX */
.timeline-day {
  background: #ffffff;
  border-radius: 24px;
  border: 1px solid #cce0ff;
  border-left: 5px solid #0060aa;
  padding: 30px;
  box-shadow: 0 12px 30px rgba(0,85,255,0.03);
}
.timeline-day__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}
.timeline-day__header h3 {
  font-size: 1.5rem;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
}
.timeline-day__header span {
  font-size: 0.95rem;
  color: #6a7c92;
  font-weight: 600;
}

.timeline-list {
  position: relative;
  padding-left: 20px;
}
.timeline-list::before {
  content: '';
  position: absolute;
  left: 35px;
  top: 20px;
  bottom: 40px;
  width: 2px;
  background: #e2eaf5;
}

.timeline-item {
  position: relative;
  display: flex;
  align-items: flex-start;
  gap: 30px;
  margin-bottom: 30px;
}
.timeline-item:last-child {
  margin-bottom: 0;
}

.timeline-item__icon {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 0.85rem;
  position: relative;
  z-index: 2;
  box-shadow: 0 0 0 6px #ffffff;
}
.icon--morning { background: #38bdf8; }
.icon--afternoon { background: #f59e0b; }
.icon--evening { background: #1e1e2f; }

.timeline-item__content {
  flex: 1;
}
.timeline-item__label {
  font-size: 0.8rem;
  font-weight: 800;
  color: #64748b;
  letter-spacing: 0.8px;
  margin-bottom: 12px;
}

.timeline-card {
  display: flex;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 16px;
  gap: 16px;
}
.timeline-card img {
  width: 120px;
  height: 120px;
  border-radius: 12px;
  object-fit: cover;
}
.timeline-card__info {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.timeline-card__info h4 {
  margin: 0 0 8px;
  color: #0f172a;
  font-size: 1.15rem;
  font-weight: 800;
}
.timeline-card__info p {
  margin: 0 0 12px;
  color: #475569;
  font-size: 0.95rem;
  line-height: 1.5;
}
.timeline-card__meta {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: auto;
}
.tag-price, .tag-time {
  background: #e0f2fe;
  color: #0369a1;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
}
.tag-time {
  background: transparent;
  color: #64748b;
  padding: 0;
}
.tag-time i { margin-right: 4px; }

/* EXPAND ACTION */
.expand-action {
  display: flex;
  justify-content: center;
  margin-top: -10px;
  position: relative;
  z-index: 10;
}
.btn-expand {
  background: #ffffff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  padding: 12px 24px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
  box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
}
.btn-expand:hover {
  background: #eff6ff;
  border-color: #93c5fd;
  transform: translateY(-2px);
}

/* FOOTER */
.result-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  border-radius: 20px;
  padding: 30px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 30px rgba(0,0,0,0.03);
}

.total-cost {
  display: flex;
  align-items: center;
  gap: 20px;
}
.total-cost__icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: #eff6ff;
  color: #2563eb;
  display: grid;
  place-items: center;
  font-size: 1.6rem;
}
.total-cost .label {
  display: block;
  font-size: 0.95rem;
  color: #64748b;
  margin-bottom: 6px;
}
.total-cost .amount {
  font-size: 1.8rem;
  font-weight: 900;
  color: #0f172a;
}
.total-cost .amount span {
  font-size: 1rem;
  color: #94a3b8;
  font-weight: 500;
}

.footer-actions {
  display: flex;
  gap: 14px;
}
.btn-solid {
  background: #2563eb;
  color: #fff;
  border: none;
  padding: 14px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.2s;
}
.btn-solid:hover { background: #1d4ed8; transform: translateY(-2px); }
.btn-solid:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

@media (max-width: 768px) {
  .result-footer {
    flex-direction: column;
    align-items: stretch;
    gap: 20px;
  }
  .timeline-card {
    flex-direction: column;
  }
  .timeline-card img {
    width: 100%;
    height: 180px;
  }
}
</style>
