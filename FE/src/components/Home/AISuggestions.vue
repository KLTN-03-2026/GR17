<template>
  <section class="py-24 relative overflow-hidden">
    <!-- Background Glow -->
    <div class="bg-glow bg-top-right animate-pulse-glow"></div>
    <div class="bg-glow bg-bottom-left"></div>
    
    <div class="max-w-7xl mx-auto px-6">
      <div class="hero-flex mb-20">
        <div class="max-w-3xl">
          <div class="hero-badge animate-fade-in-left">
            <div class="badge-icon shadow-xl">
              <i class="fas fa-sparkles animate-pulse"></i>
            </div>
            <div>
              <span class="badge-text">Công cụ gợi ý bằng AI</span>
              <div class="badge-line"></div>
            </div>
          </div>
          
          <h2 class="section-title">
            Tour <span class="text-gradient italic">Cá Nhân Hóa</span><br />
            Dựa Trên Sở Thích
          </h2>
          <p class="section-desc">
            Hệ thống AI của chúng tôi phân tích hàng ngàn dữ liệu để tìm ra những hành trình phù hợp nhất với phong cách của bạn.
          </p>
        </div>

        <div class="interests-card shadow-2xl">
          <h4 class="card-title">
            <div class="dot-blue"></div>
            Bạn quan tâm đến điều gì?
          </h4>
          <div class="tags-wrap">
            <button
              v-for="interest in danhSachSoThich"
              :key="interest"
              @click="chuyenSoThich(interest)"
              class="tag-btn transition-all"
              :class="{ 'active scale-105 shadow-lg': soThichDaChon.includes(interest) }"
            >
              <i class="fas fa-check" v-if="soThichDaChon.includes(interest)"></i>
              {{ interest }}
            </button>
          </div>
          <button
            @click="apDungSoThich"
            :disabled="dangTai"
            class="update-btn group transition-all"
          >
            <div v-if="dangTai" class="spinner"></div>
            <template v-else>
              <span>Cập nhật gợi ý AI</span>
              <i class="fas fa-arrow-up-right group-hover-arrow transition-transform"></i>
            </template>
          </button>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="filters-bar animate-fade-in-up shadow-xl">
        <div class="filter-icon-wrap">
          <i class="fas fa-filter text-slate-400"></i>
          <span class="filter-label">Bộ lọc tour</span>
        </div>

        <!-- Search -->
        <div class="filter-item flex-1 min-w-[200px]">
          <label><i class="fas fa-search"></i> Tìm kiếm tour</label>
          <input 
            type="text"
            placeholder="Nhập tên tour hoặc địa điểm..."
            v-model="tuKhoaTimKiem"
            class="filter-input"
          />
        </div>

        <!-- Location -->
        <div class="filter-item min-w-[160px]">
          <label><i class="fas fa-map-marker-alt"></i> Địa điểm</label>
          <select v-model="boLocDiaDiem" class="filter-select">
            <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
          </select>
        </div>

        <!-- Price -->
        <div class="filter-item min-w-[160px]">
          <label><i class="fas fa-money-bill-wave"></i> Mức giá</label>
          <select v-model="boLocGia" class="filter-select">
            <option v-for="range in priceRanges" :key="range" :value="range">{{ range }}</option>
          </select>
        </div>

        <!-- Duration -->
        <div class="filter-item min-w-[160px]">
          <label><i class="far fa-clock"></i> Thời gian</label>
          <select v-model="boLocThoiGian" class="filter-select">
            <option v-for="dur in durations" :key="dur" :value="dur">{{ dur }}</option>
          </select>
        </div>

        <button @click="datLaiBoLoc" class="reset-filters">
          Đặt lại bộ lọc
        </button>
      </div>

      <!-- Tours Grid -->
      <div class="tours-grid">
        <template v-if="dangTai">
          <div v-for="i in 4" :key="`skeleton-${i}`" class="skeleton-card animate-pulse"></div>
        </template>
        <template v-else>
          <div 
            v-for="(item, idx) in goiYPhanTrang" 
            :key="item.id"
            class="tour-card group shadow-hover animate-pop"
            :style="`animation-delay: ${idx * 0.1}s`"
          >
            <div class="card-img-wrap">
              <img 
                :src="item.image" 
                :alt="item.name"
                class="card-img group-hover-scale transition-transform duration-1000"
                referrerpolicy="no-referrer"
              />
              
              <!-- Quick View Overlay -->
              <div class="img-overlay group-hover-opacity transition-opacity duration-300">
                <button @click="tourDangChon = item" class="overlay-btn overlay-btn-light group-hover-slide-up transition-transform duration-500 shadow-2xl hover-bg-blue">
                  <i class="fas fa-eye"></i> Xem nhanh
                </button>
                <button 
                  @click="chuyenSoSanh(item)" 
                  class="overlay-btn group-hover-slide-up transition-transform duration-500 shadow-2xl"
                  :class="daDuocSoSanh(item) ? 'bg-blue text-white' : 'overlay-btn-light hover-bg-blue'"
                >
                  <i class="fas fa-exchange-alt"></i>
                  {{ daDuocSoSanh(item) ? 'Đã chọn' : 'So sánh' }}
                </button>
              </div>

              <div class="top-badges">
                <div v-if="daDuocSoSanh(item)" class="compare-badge shadow-xl">
                  <i class="fas fa-check"></i>
                </div>
                <div class="rating-badge shadow-xl">
                  <i class="fas fa-star text-yellow"></i>
                  <span>{{ item.rating.toFixed(1) }}</span>
                </div>
              </div>
              
              <div class="bottom-badge">
                <div class="location-badge">
                  <div class="dot-green animate-pulse"></div>
                  <span>{{ item.location }}</span>
                </div>
              </div>
            </div>
            
            <div class="card-body">
              <div class="tags-row">
                <span v-for="tag in item.tags" :key="tag" class="small-tag">{{ tag }}</span>
              </div>
              
              <h3 class="card-title-text group-hover-text-blue transition-colors">
                {{ item.name }}
              </h3>
              
              <p class="card-desc line-clamp-3">
                {{ item.description }}
              </p>
              
              <div class="card-footer">
                <div class="footer-left">
                  <span class="footer-label">Thời gian</span>
                  <span class="footer-val">{{ item.duration }}</span>
                </div>
                <div class="footer-right text-right">
                  <span class="footer-label">Giá từ</span>
                  <span class="footer-price">{{ item.price }}</span>
                </div>
              </div>
              
              <button @click="tourDangChon = item" class="detail-btn group-hover-bg-blue transition-all duration-500">
                <span>Xem nhanh</span>
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
        </template>
      </div>

      <!-- Pagination Controls -->
      <div v-if="!dangTai && tongSoTrang > 1" class="pagination-controls animate-fade-in-up">
        <button 
          @click="trangHienTai > 1 && trangHienTai--"
          :disabled="trangHienTai === 1"
          class="page-nav-btn shadow-sm"
        >
          <i class="fas fa-chevron-left"></i>
        </button>

        <div class="pages-list">
          <button
            v-for="i in tongSoTrang"
            :key="i"
            @click="trangHienTai = i"
            class="page-btn transition-all"
            :class="trangHienTai === i ? 'active shadow-lg' : ''"
          >
            {{ i }}
          </button>
        </div>

        <button
          @click="trangHienTai < tongSoTrang && trangHienTai++"
          :disabled="trangHienTai === tongSoTrang"
          class="page-nav-btn shadow-sm"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>

      <!-- Compare Floating Bar -->
      <div v-if="danhSachTourSoSanh.length > 0" class="compare-bar-wrap animate-slide-up">
        <div class="compare-bar shadow-2xl">
          <div class="compare-info">
            <div class="cb-icon-wrap">
              <i class="fas fa-exchange-alt"></i>
            </div>
            <div>
              <h4 class="cb-title">So sánh tour</h4>
              <p class="cb-subtitle">Đã chọn {{ danhSachTourSoSanh.length }} / 4 tour</p>
            </div>
          </div>

          <div class="compare-actions">
            <button @click="danhSachTourSoSanh = []" class="clear-compare-btn">
              <i class="fas fa-trash-alt"></i>
            </button>
            <button 
              @click="moModalSoSanh"
              :disabled="danhSachTourSoSanh.length < 2"
              class="open-compare-btn shadow-xl"
            >
              So sánh ngay
            </button>
          </div>
        </div>
      </div>

      <!-- Comparison Modal -->
      <div v-if="hienModalSoSanh" class="modal-overlay">
        <div class="modal-backdrop" @click="dongModalSoSanh"></div>
        <div class="compare-modal shadow-2xl animate-pop-modal">
          <div class="modal-header">
            <div class="mh-left">
              <div class="mh-icon">
                <i class="fas fa-exchange-alt"></i>
              </div>
              <div>
                <h2 class="mh-title">So sánh chi tiết tour</h2>
                <p class="mh-subtitle">Phân tích sự khác biệt giữa các hành trình</p>
              </div>
            </div>
            <button @click="dongModalSoSanh" class="close-modal-btn shadow-sm">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <div class="modal-body overflow-auto">
            <div class="min-w-[800px]">
              <table class="compare-table border-collapse">
                <thead>
                  <tr>
                    <th class="feature-col bg-slate-50">
                      <span class="feature-label">Đặc điểm</span>
                    </th>
                    <th v-for="(tour, idx) in danhSachTourSoSanh" :key="tour.id" class="tour-header-col">
                      <div class="th-content">
                        <img :src="tour.image" :alt="tour.name" class="th-img shadow-lg" referrerpolicy="no-referrer" />
                        <h3 class="th-title">{{ tour.name }}</h3>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y">
                  <!-- Row 1: Location -->
                  <tr>
                    <td class="feature-name">Địa điểm</td>
                    <td v-for="tour in danhSachTourSoSanh" :key="tour.id" class="text-center font-bold">
                      <div class="flex-center-gap text-slate-700">
                        <i class="fas fa-map-marker-alt text-blue"></i> {{ tour.location }}
                      </div>
                    </td>
                  </tr>
                  <!-- Row 2: Duration -->
                  <tr>
                    <td class="feature-name">Thời gian</td>
                    <td v-for="tour in danhSachTourSoSanh" :key="tour.id" class="text-center font-bold">
                      <div class="flex-center-gap text-slate-700">
                        <i class="far fa-clock text-indigo"></i> {{ tour.duration }}
                      </div>
                    </td>
                  </tr>
                  <!-- Row 3: Price -->
                  <tr>
                    <td class="feature-name">Giá tour</td>
                    <td v-for="tour in danhSachTourSoSanh" :key="tour.id" class="text-center">
                      <span class="price-big">{{ tour.price }}</span>
                    </td>
                  </tr>
                  <!-- Row 4: Rating -->
                  <tr>
                    <td class="feature-name">Đánh giá</td>
                    <td v-for="tour in danhSachTourSoSanh" :key="tour.id" class="text-center">
                      <div class="flex-center-gap">
                        <i class="fas fa-star text-yellow"></i>
                        <span class="font-black">{{ tour.rating.toFixed(1) }}</span>
                      </div>
                    </td>
                  </tr>
                  <!-- Row 5: Tags -->
                  <tr>
                    <td class="feature-name">Phân loại</td>
                    <td v-for="tour in danhSachTourSoSanh" :key="tour.id" class="text-center">
                      <div class="flex-wrap-center gap-1">
                        <span v-for="tag in tour.tags" :key="tag" class="small-tag-badge">{{ tag }}</span>
                      </div>
                    </td>
                  </tr>
                  <!-- Row 6: Logic Description -->
                  <tr>
                    <td class="feature-name">Mô tả ngắn</td>
                    <td v-for="tour in danhSachTourSoSanh" :key="tour.id" class="text-center">
                      <p class="desc-clamp">{{ tour.description }}</p>
                    </td>
                  </tr>
                  <!-- Actions -->
                  <tr>
                    <td class="bg-slate-50 border-0"></td>
                    <td v-for="tour in danhSachTourSoSanh" :key="tour.id" class="text-center border-0 py-6">
                      <button 
                        @click="moXemNhanhTuSoSanh(tour)"
                        class="detail-btn-small transition-all"
                      >
                        Xem nhanh
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick View Modal -->
      <div v-if="tourDangChon" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
        <div style="position:absolute;inset:0;background:rgba(15,23,42,0.85);backdrop-filter:blur(12px);" @click="dongXemNhanh"></div>
        <div style="position:relative;z-index:1;width:95%;max-width:960px;max-height:90vh;background:#fff;border-radius:2rem;overflow:hidden;display:flex;flex-direction:row;box-shadow:0 25px 60px rgba(0,0,0,0.4);" class="animate-pop-modal">

          <!-- Close -->
          <button @click="dongXemNhanh" style="position:absolute;top:16px;right:16px;z-index:10;width:40px;height:40px;background:rgba(255,255,255,0.95);border:none;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:#0f172a;">
            <i class="fas fa-times"></i>
          </button>

          <!-- Left Image -->
          <div style="flex:0 0 45%;min-height:420px;position:relative;overflow:hidden;">
            <img :src="tourDangChon.image" :alt="tourDangChon.name" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" referrerpolicy="no-referrer" />
            <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.75),transparent);"></div>
            <div style="position:absolute;bottom:24px;left:24px;right:24px;z-index:2;">
              <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;">
                <span v-for="tag in tourDangChon.tags" :key="tag" style="padding:3px 12px;background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.4);border-radius:9999px;font-size:10px;font-weight:800;color:#fff;text-transform:uppercase;">{{ tag }}</span>
              </div>
              <h2 style="font-size:1.5rem;font-weight:900;color:#fff;line-height:1.2;margin:0;">{{ tourDangChon.name }}</h2>
            </div>
          </div>

          <!-- Right Content -->
          <div style="flex:1;padding:28px;overflow-y:auto;display:flex;flex-direction:column;gap:18px;min-width:0;">
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:36px;height:36px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#2563eb;"><i class="fas fa-star"></i></div>
                <div>
                  <div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;">Đánh giá</div>
                  <div style="font-size:14px;font-weight:900;color:#1e293b;">{{ tourDangChon.rating.toFixed(1) }} / 5.0</div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:36px;height:36px;background:#eef2ff;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#4f46e5;"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                  <div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;">Địa điểm</div>
                  <div style="font-size:14px;font-weight:900;color:#1e293b;">{{ tourDangChon.location }}</div>
                </div>
              </div>
            </div>
            <div>
              <div style="font-size:11px;font-weight:900;color:#0f172a;text-transform:uppercase;margin-bottom:8px;display:flex;align-items:center;gap:8px;"><i class="fas fa-info-circle" style="color:#2563eb;"></i> Chi tiết hành trình</div>
              <p style="color:#64748b;font-size:14px;line-height:1.7;margin:0;">{{ tourDangChon.description }}</p>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
              <div style="padding:14px;background:#f8fafc;border-radius:14px;border:1px solid #f1f5f9;">
                <div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;margin-bottom:6px;"><i class="far fa-calendar-alt" style="color:#2563eb;"></i> Thời gian</div>
                <p style="font-size:15px;font-weight:900;color:#0f172a;margin:0;">{{ tourDangChon.duration }}</p>
              </div>
              <div style="padding:14px;background:#f8fafc;border-radius:14px;border:1px solid #f1f5f9;">
                <div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;margin-bottom:6px;"><i class="fas fa-users" style="color:#4f46e5;"></i> Nhóm tối đa</div>
                <p style="font-size:15px;font-weight:900;color:#0f172a;margin:0;">12 người</p>
              </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid #f1f5f9;margin-top:auto;">
              <div>
                <div style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Giá trọn gói từ</div>
                <div style="font-size:1.5rem;font-weight:900;color:#2563eb;">{{ tourDangChon.price }}</div>
              </div>
              <button @click="dongXemNhanh" style="padding:12px 24px;background:#2563eb;color:#fff;border:none;border-radius:12px;font-size:13px;font-weight:900;text-transform:uppercase;cursor:pointer;display:flex;align-items:center;gap:8px;">Đóng <i class="fas fa-times"></i></button>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </section>
</template>

<script>
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
  position: "top-right",
  duration: 3000,
});
import { goiApi } from '../../services/httpClient.js';

import { AI_INTEREST_OPTIONS } from "../../constants/aiPreferences";

const API_BASE = "/api";

function chuanHoaDanhSach(duLieuPhanHoi) {
  if (Array.isArray(duLieuPhanHoi)) return duLieuPhanHoi;
  if (Array.isArray(duLieuPhanHoi?.data)) return duLieuPhanHoi.data;
  if (Array.isArray(duLieuPhanHoi?.data?.data)) return duLieuPhanHoi.data.data;
  if (Array.isArray(duLieuPhanHoi?.result)) return duLieuPhanHoi.result;
  return [];
}

function dinhDangGia(value) {
  const num = Number(value);
  if (!num || isNaN(num)) return "Liên hệ";
  return num.toLocaleString("vi-VN") + "đ";
}

const fallbackSuggestions = [
  {
    id: 1,
    name: "Tour Khám Phá Đảo Ngọc",
    location: "Phú Quốc",
    description: "Trải nghiệm lặn ngắm san hô, câu mực đêm và thưởng thức hải sản tươi sống. Hành trình đưa bạn đến với những bãi biển cát trắng mịn màng, làn nước trong xanh như ngọc và những rạn san hô rực rỡ sắc màu.",
    image: "https://images.unsplash.com/photo-1589394815804-964ed960eb3e?auto=format&fit=crop&q=80&w=800",
    tags: ["Biển Đảo", "Nghỉ Dưỡng"],
    duration: "3 ngày 2 đêm",
    price: "4.500.000đ",
    rating: 4.9
  }
];

export default {
  name: 'AISuggestions',
  data() {
    return {
      danhSachSoThich: [...AI_INTEREST_OPTIONS],
      danhSachGoiY: [],
      dangTai: true,
      soThichDaChon: [],
      tourDangChon: null,
      danhSachTourSoSanh: [],
      hienModalSoSanh: false,
      
      // Filters
      tuKhoaTimKiem: '',
      boLocDiaDiem: 'Tất cả',
      boLocGia: 'Tất cả',
      boLocThoiGian: 'Tất cả',
      
      // Pagination
      trangHienTai: 1,
      SO_MUC_MOI_TRANG: 4,
      
      henGioTimKiem: null,
      tuKhoaTimKiemDaDebounce: ''
    }
  },
  computed: {
    locations() {
      return ['Tất cả', ...new Set(this.danhSachGoiY.map(s => s.location))];
    },
    durations() {
      return ['Tất cả', ...new Set(this.danhSachGoiY.map(s => s.duration))];
    },
    priceRanges() {
      return ['Tất cả', 'Dưới 3tr', '3tr - 5tr', 'Trên 5tr'];
    },
    goiYDaLoc() {
      return this.danhSachGoiY.filter(item => {
        const matchSearch = item.name.toLowerCase().includes(this.tuKhoaTimKiemDaDebounce.toLowerCase()) || 
                            item.location.toLowerCase().includes(this.tuKhoaTimKiemDaDebounce.toLowerCase());
        
        const matchLocation = this.boLocDiaDiem === 'Tất cả' || item.location.includes(this.boLocDiaDiem);
        
        const priceNum = parseInt(item.price.replace(/\D/g, ''));
        let matchPrice = true;
        if (this.boLocGia === 'Dưới 3tr') matchPrice = priceNum < 3000000;
        else if (this.boLocGia === '3tr - 5tr') matchPrice = priceNum >= 3000000 && priceNum <= 5000000;
        else if (this.boLocGia === 'Trên 5tr') matchPrice = priceNum > 5000000;

        const matchDuration = this.boLocThoiGian === 'Tất cả' || item.duration === this.boLocThoiGian;

        return matchSearch && matchLocation && matchPrice && matchDuration;
      });
    },
    tongSoTrang() {
      return Math.ceil(this.goiYDaLoc.length / this.SO_MUC_MOI_TRANG);
    },
    goiYPhanTrang() {
      const start = (this.trangHienTai - 1) * this.SO_MUC_MOI_TRANG;
      return this.goiYDaLoc.slice(start, start + this.SO_MUC_MOI_TRANG);
    }
  },
  watch: {
    tuKhoaTimKiem(val) {
      clearTimeout(this.henGioTimKiem);
      this.henGioTimKiem = setTimeout(() => {
        this.tuKhoaTimKiemDaDebounce = val;
      }, 400);
    },
    tuKhoaTimKiemDaDebounce() { this.trangHienTai = 1; },
    boLocDiaDiem() { this.trangHienTai = 1; },
    boLocGia() { this.trangHienTai = 1; },
    boLocThoiGian() { this.trangHienTai = 1; },
    hienModalSoSanh() { this.dongBoKhoaCuonTrang(); },
    tourDangChon() { this.dongBoKhoaCuonTrang(); }
  },
  methods: {
    dongBoKhoaCuonTrang() {
      document.body.style.overflow = this.hienModalSoSanh || this.tourDangChon ? "hidden" : "";
    },
    async taiGoiY() {
      this.dangTai = true;
      try {
        const phanHoi = await goiApi(`${API_BASE}/tour`, {
          headers: { Accept: "application/json" }
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách tour gợi ý.");
        }
        const raw = chuanHoaDanhSach(duLieuPhanHoi);
        
        if (raw.length) {
          this.danhSachGoiY = raw.map((item, index) => {
            const loc = item.mo_ta && item.mo_ta.includes('Đà Nẵng') ? 'Đà Nẵng' 
                      : (item.mo_ta && item.mo_ta.includes('Sapa') ? 'Sapa'
                      : (item.ten_tour && item.ten_tour.includes('Phú Quốc') ? 'Phú Quốc' : 'Việt Nam'));
            return {
              id: item.ma_tour || item.Ma_tour || index + 1,
              name: item.ten_tour || item.Ten_tour || "Tour chưa có tên",
              location: loc,
              description: item.mo_ta || item.Mo_ta || "Khám phá hành trình thú vị.",
              image: item.hinh_anh || item.Hinh_anh || "https://images.unsplash.com/photo-1590050752117-238cb0fb12b1",
              tags: ["Khuyến Mãi", "Hot"],
              duration: item.so_ngay ? `${item.so_ngay} ngày` : (item.So_ngay ? `${item.So_ngay} ngày` : "Liên hệ"),
              price: dinhDangGia(item.so_tien || item.So_tien || item.gia_tour || item.Gia_tour || 0),
              rating: 4.5 + Math.random() * 0.5,
            };
          });
        } else {
          this.danhSachGoiY = [...fallbackSuggestions];
        }
      } catch (error) {
        console.warn("Không thể tải tour từ API, sử dụng dữ liệu mẫu:", error.message);
        this.danhSachGoiY = [...fallbackSuggestions];
      } finally {
        this.dangTai = false;
      }
    },
    chuyenSoThich(interest) {
      const idx = this.soThichDaChon.indexOf(interest);
      if (idx > -1) {
        this.soThichDaChon.splice(idx, 1);
      } else {
        this.soThichDaChon.push(interest);
      }
    },
    apDungSoThich() {
      this.taiGoiY();
    },
    moModalSoSanh() {
      if (this.danhSachTourSoSanh.length < 2) {
        return;
      }
      this.hienModalSoSanh = true;
    },
    dongModalSoSanh() {
      this.hienModalSoSanh = false;
    },
    moXemNhanh(tour) {
      this.tourDangChon = tour;
    },
    moXemNhanhTuSoSanh(tour) {
      this.dongModalSoSanh();
      this.moXemNhanh(tour);
    },
    dongXemNhanh() {
      this.tourDangChon = null;
    },
    chuyenSoSanh(tour) {
      const idx = this.danhSachTourSoSanh.findIndex(t => t.id === tour.id);
      if (idx > -1) {
        this.danhSachTourSoSanh.splice(idx, 1);
      } else {
        if (this.danhSachTourSoSanh.length >= 4) {
          toaster.warning("Bạn chỉ có thể so sánh tối đa 4 tour cùng lúc.");
          return;
        }
        this.danhSachTourSoSanh.push(tour);
      }
    },
    daDuocSoSanh(tour) {
      return this.danhSachTourSoSanh.some(t => t.id === tour.id);
    },
    datLaiBoLoc() {
      this.boLocDiaDiem = 'Tất cả';
      this.boLocGia = 'Tất cả';
      this.boLocThoiGian = 'Tất cả';
      this.tuKhoaTimKiem = '';
      this.tuKhoaTimKiemDaDebounce = '';
    }
  },
  mounted() {
    this.taiGoiY();
  },
  beforeUnmount() {
    document.body.style.overflow = "";
  }
}
</script>

<style scoped>
.py-24 { padding-top: 6rem; padding-bottom: 6rem; }
.relative { position: relative; }
.overflow-hidden { overflow: hidden; }
.max-w-7xl { max-width: 80rem; margin-left: auto; margin-right: auto; }
.px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
.mb-20 { margin-bottom: 5rem; }

/* Background Glows */
.bg-glow {
  position: absolute;
  z-index: -10;
  border-radius: 50%;
  filter: blur(80px); /* 3xl roughly */
}
.bg-top-right {
  top: 0; right: 0;
  width: 600px; height: 600px;
  background-color: rgba(219,234,254,0.4);
  transform: translate(33%, -33%);
}
.bg-bottom-left {
  bottom: 0; left: 0;
  width: 400px; height: 400px;
  background-color: rgba(224,231,255,0.3);
  transform: translate(-25%, 25%);
}
.animate-pulse-glow { animation: pulseGlow 8s infinite alternate; }
@keyframes pulseGlow {
  0% { transform: translate(33%, -33%) scale(1); opacity: 0.5; }
  100% { transform: translate(33%, -33%) scale(1.1); opacity: 0.8; }
}

/* Hero */
.hero-flex {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 3rem;
}
@media (min-width: 1024px) {
  .hero-flex { flex-direction: row; align-items: flex-end; }
}
.max-w-3xl { max-width: 48rem; }

.hero-badge {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.badge-icon {
  width: 3.5rem; height: 3.5rem;
  background: linear-gradient(to bottom right, #2563eb, #4338ca);
  border-radius: 1rem;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.5rem;
}
.animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
.badge-text {
  color: #2563eb; font-weight: 900; letter-spacing: 0.1em;
  font-size: 0.625rem; text-transform: uppercase; display: block; margin-bottom: 0.25rem;
}
.badge-line {
  height: 0.25rem; width: 3rem; background-color: #2563eb; border-radius: 9999px;
}

.section-title {
  font-size: 3rem; font-weight: 900; color: #0f172a;
  letter-spacing: -0.05em; line-height: 0.9; margin-bottom: 2rem;
}
@media (min-width: 768px) { .section-title { font-size: 3.75rem; } }
.text-gradient {
  background: linear-gradient(to right, #2563eb, #4f46e5);
  -webkit-background-clip: text;
  color: transparent;
}
.italic { font-style: italic; }

.section-desc {
  color: #64748b; font-size: 1.25rem; line-height: 1.625; max-width: 36rem; margin: 0;
}

/* Interests Card */
.interests-card {
  background-color: rgba(255,255,255,0.6);
  backdrop-filter: blur(24px);
  padding: 2rem;
  border-radius: 2.5rem;
  border: 1px solid #ffffff;
  max-width: 36rem; width: 100%;
}
.card-title {
  font-size: 0.875rem; font-weight: 900; color: #1e293b;
  text-transform: uppercase; letter-spacing: 0.05em;
  display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;
}
.dot-blue {
  width: 0.5rem; height: 0.5rem; background-color: #2563eb; border-radius: 50%;
}
.tags-wrap {
  display: flex; flex-wrap: wrap; gap: 0.625rem; margin-bottom: 2rem;
}
.tag-btn {
  padding: 0.625rem 1.25rem; border-radius: 1rem; font-size: 0.75rem;
  font-weight: 700; border: 1px solid #f1f5f9; background-color: #ffffff;
  color: #64748b; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;
}
.tag-btn:hover {
  border-color: #bfdbfe; color: #2563eb; background-color: rgba(239,246,255,0.3);
}
.tag-btn.active {
  background-color: #2563eb; color: #ffffff;
}
.active.scale-105 { transform: scale(1.05); }

.update-btn {
  width: 100%; padding: 1rem 0; background-color: #0f172a; color: #ffffff;
  border-radius: 1rem; font-size: 0.875rem; font-weight: 900; text-transform: uppercase;
  border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem;
}
.update-btn:hover:not(:disabled) {
  background-color: #2563eb; box-shadow: 0 20px 25px -5px rgba(191,219,254,0.5);
}
.update-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.spinner {
  width: 1.25rem; height: 1.25rem; border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #ffffff; border-radius: 50%;
  animation: spin 1s linear infinite;
}

/* Filters */
.filters-bar {
  margin-bottom: 3rem;
  display: flex; flex-wrap: wrap; align-items: center; gap: 1.5rem;
  background-color: rgba(255,255,255,0.4); backdrop-filter: blur(12px);
  padding: 1.5rem; border-radius: 2rem; border: 1px solid rgba(255,255,255,0.5);
}
.filter-icon-wrap {
  display: flex; align-items: center; gap: 0.75rem; margin-right: 1rem;
}
.filter-label {
  font-size: 0.625rem; font-weight: 900; text-transform: uppercase;
  letter-spacing: 0.2em; color: #94a3b8;
}
.filter-item {
  display: flex; flex-direction: column; gap: 0.375rem;
}
.filter-item label {
  font-size: 0.5625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase;
  letter-spacing: 0.1em; margin-left: 0.5rem; display: flex; align-items: center; gap: 0.375rem;
}
.filter-input, .filter-select {
  background-color: #ffffff; border: 1px solid #f1f5f9; border-radius: 0.75rem;
  padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 700; color: #334155;
  outline: none; transition: border-color 0.3s;
}
.filter-input:focus, .filter-select:focus {
  border-color: #2563eb; box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
}
.reset-filters {
  margin-left: auto; font-size: 0.625rem; font-weight: 900; color: #2563eb;
  text-transform: uppercase; letter-spacing: 0.1em; background: none; border: none;
  cursor: pointer;
}
.reset-filters:hover { text-decoration: underline; }

/* Grid */
.tours-grid {
  display: grid; grid-template-columns: 1fr; gap: 2rem;
}
@media (min-width: 768px) { .tours-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
@media (min-width: 1024px) { .tours-grid { grid-template-columns: repeat(4, minmax(0,1fr)); } }

.skeleton-card {
  background-color: #f8fafc; border-radius: 3rem; height: 520px;
}

.tour-card {
  background-color: #ffffff; border-radius: 3rem; overflow: hidden;
  border: 1px solid #f1f5f9; display: flex; flex-direction: column; height: 100%;
  transition: all 0.5s ease;
}
.tour-card:hover {
  transform: translateY(-12px) scale(1.02);
  box-shadow: 0 40px 80px -20px rgba(59,130,246,0.25);
}

.card-img-wrap {
  position: relative; height: 18rem; overflow: hidden;
}
.card-img {
  width: 100%; height: 100%; object-fit: cover;
}
.img-overlay {
  position: absolute; inset: 0; background-color: rgba(0,0,0,0.4);
  opacity: 0; display: flex; align-items: center; justify-content: center; gap: 0.75rem;
}
.tour-card:hover .img-overlay { opacity: 1; }

.overlay-btn {
  padding: 0.75rem 1.25rem; border-radius: 1rem; font-size: 0.625rem; font-weight: 900;
  text-transform: uppercase; letter-spacing: 0.1em; display: flex; align-items: center; gap: 0.5rem;
  transform: translateY(1rem); cursor: pointer; border: none;
}
.overlay-btn-light { background-color: #ffffff; color: #0f172a; }
.hover-bg-blue:hover { background-color: #2563eb; color: #ffffff; }
.tour-card:hover .overlay-btn { transform: translateY(0); }

.top-badges {
  position: absolute; top: 1.5rem; left: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;
}
.compare-badge {
  background-color: #2563eb; color: #ffffff; padding: 0.5rem; border-radius: 0.75rem;
  display: flex; align-items: center; justify-content: center;
}
.rating-badge {
  background-color: rgba(255,255,255,0.9); backdrop-filter: blur(12px);
  padding: 0.375rem 1rem; border-radius: 1rem; display: flex; align-items: center; gap: 0.375rem;
}
.rating-badge span { font-size: 0.875rem; font-weight: 900; color: #1e293b; }
.text-yellow { color: #facc15; }

.bottom-badge {
  position: absolute; bottom: 1.5rem; left: 1.5rem; right: 1.5rem;
}
.location-badge {
  background-color: rgba(0,0,0,0.4); backdrop-filter: blur(12px);
  padding: 0.5rem 1rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.2);
  display: inline-flex; align-items: center; gap: 0.5rem;
}
.dot-green { width: 0.5rem; height: 0.5rem; background-color: #4ade80; border-radius: 50%; }
.location-badge span { color: #ffffff; font-size: 0.625rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; }

.card-body {
  padding: 2rem; display: flex; flex-direction: column; flex-grow: 1;
}
.tags-row { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
.small-tag {
  font-size: 0.5625rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em;
  color: #2563eb; background-color: rgba(239,246,255,0.5); padding: 0.375rem 0.75rem;
  border-radius: 0.75rem; border: 1px solid rgba(219,234,254,0.5);
}

.card-title-text {
  font-size: 1.5rem; font-weight: 900; color: #0f172a; margin: 0 0 1rem 0; line-height: 1.25;
}
.card-desc {
  color: #64748b; font-size: 0.875rem; line-height: 1.625; margin: 0 0 2rem 0; flex-grow: 1;
}

.card-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding-top: 1.5rem; border-top: 1px solid #f8fafc;
}
.footer-label {
  display: block; font-size: 0.625rem; font-weight: 900; color: #94a3b8;
  text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.25rem;
}
.footer-val { font-size: 0.875rem; font-weight: 900; color: #1e293b; }
.footer-price { font-size: 1.125rem; font-weight: 900; color: #2563eb; }

.detail-btn {
  margin-top: 2rem; width: 100%; padding: 1rem 0; background-color: #f8fafc;
  border-radius: 1rem; font-size: 0.75rem; font-weight: 900; color: #1e293b;
  border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
}
.tour-card:hover .detail-btn { background-color: #2563eb; color: #ffffff; }

/* Pagination */
.pagination-controls {
  margin-top: 4rem; display: flex; align-items: center; justify-content: center; gap: 1.5rem;
}
.page-nav-btn {
  width: 3rem; height: 3rem; border-radius: 1rem; background-color: #ffffff;
  border: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: center;
  color: #94a3b8; cursor: pointer; transition: all 0.3s ease;
}
.page-nav-btn:hover:not(:disabled) { color: #2563eb; border-color: #bfdbfe; }
.page-nav-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.pages-list { display: flex; align-items: center; gap: 0.5rem; }
.page-btn {
  width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 900;
  background-color: #ffffff; color: #94a3b8; border: 1px solid #f1f5f9; cursor: pointer;
}
.page-btn:hover { border-color: #bfdbfe; color: #2563eb; }
.page-btn.active { background-color: #2563eb; color: #ffffff; }

/* Compare Bar */
.compare-bar-wrap {
  position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%);
  z-index: 50; width: 100%; max-width: 42rem; padding: 0 1rem;
}
.compare-bar {
  background-color: rgba(15,23,42,0.9); backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.1); padding: 1rem; border-radius: 2.5rem;
  display: flex; align-items: center; justify-content: space-between; gap: 1.5rem;
}
.compare-info { display: flex; align-items: center; gap: 1rem; margin-left: 1rem; }
.cb-icon-wrap {
  width: 3rem; height: 3rem; background-color: #2563eb; border-radius: 1rem;
  display: flex; align-items: center; justify-content: center; color: #ffffff;
}
.cb-title { color: #ffffff; font-weight: 900; font-size: 0.875rem; margin: 0; }
.cb-subtitle { color: #94a3b8; font-size: 0.625rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin: 0; }
.compare-actions { display: flex; align-items: center; gap: 0.75rem; }
.clear-compare-btn {
  padding: 1rem; color: #94a3b8; background: none; border: none; cursor: pointer; transition: color 0.3s;
}
.clear-compare-btn:hover { color: #ffffff; }
.open-compare-btn {
  padding: 1rem 2rem; background-color: #2563eb; color: #ffffff; border-radius: 1rem;
  font-size: 0.75rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; border: none; cursor: pointer;
}
.open-compare-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* Modals */
.modal-overlay {
  position: fixed; inset: 0; z-index: 110; display: flex; align-items: center; justify-content: center; padding: 1rem;
}
.modal-backdrop {
  position: absolute; inset: 0; background-color: rgba(15,23,42,0.8); backdrop-filter: blur(12px); z-index: 1;
}
.compare-modal {
  position: relative; z-index: 10; width: 95%; max-width: 1200px; background-color: #ffffff; border-radius: 3rem; overflow: hidden;
  max-height: 95vh; display: flex; flex-direction: column;
}
.quick-view-modal {
  position: relative; width: 95%; max-width: 1000px; background-color: #ffffff; border-radius: 3rem; overflow: hidden;
  max-height: 95vh; display: grid; grid-template-columns: 1fr; box-sizing: border-box;
}
@media (min-width: 768px) { .quick-view-modal { grid-template-columns: 1fr 1fr; } }

/* Compare Modal Specifics */
.modal-header {
  padding: 2rem; border-bottom: 1px solid #f1f5f9; background-color: rgba(248,250,252,0.5);
  display: flex; align-items: center; justify-content: space-between;
}
.mh-left { display: flex; align-items: center; gap: 1rem; }
.mh-icon {
  width: 3rem; height: 3rem; background-color: #2563eb; border-radius: 1rem;
  display: flex; align-items: center; justify-content: center; color: #ffffff;
}
.mh-title { font-size: 1.5rem; font-weight: 900; color: #0f172a; margin: 0; }
.mh-subtitle { font-size: 0.625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin: 0; }
.close-modal-btn {
  width: 3rem; height: 3rem; background-color: #ffffff; border-radius: 1rem;
  display: flex; align-items: center; justify-content: center; color: #0f172a; border: 1px solid #f1f5f9; cursor: pointer;
}
.modal-body { flex-grow: 1; padding: 2rem; overflow: auto; }
.compare-table { width: 100%; border-collapse: collapse; }
.feature-col { padding: 1.5rem; text-align: left; width: 20%; border-top-left-radius: 1.5rem; }
.feature-label { font-size: 0.625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; }
.tour-header-col { padding: 1.5rem; text-align: center; border-left: 1px solid #f8fafc; border-right: 1px solid #f8fafc; }
.th-content { display: flex; flex-direction: column; align-items: center; gap: 1rem; }
.th-img { width: 8rem; height: 5rem; object-fit: cover; border-radius: 1rem; }
.th-title { font-size: 0.875rem; font-weight: 900; color: #0f172a; margin: 0; line-height: 1.25; }
.divide-y tr { border-top: 1px solid #f8fafc; }
.feature-name { padding: 1.5rem; font-size: 0.75rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; background-color: rgba(248,250,252,0.3); }
.text-center { text-align: center; }
.flex-center-gap { display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.flex-wrap-center { display: flex; flex-wrap: wrap; justify-content: center; }
.font-bold { font-weight: 700; }
.font-black { font-weight: 900; }
.text-indigo { color: #4f46e5; }
.price-big { font-size: 1.125rem; font-weight: 900; color: #2563eb; }
.small-tag-badge { padding: 0.25rem 0.5rem; background-color: #eff6ff; font-size: 0.5625rem; font-weight: 900; color: #2563eb; border-radius: 0.5rem; text-transform: uppercase; }
.desc-clamp { font-size: 0.75rem; color: #64748b; font-weight: 500; line-height: 1.625; max-width: 200px; margin: 0 auto; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
.border-0 { border: none !important; }
.detail-btn-small { padding: 0.75rem 1.5rem; background-color: #0f172a; color: #ffffff; border-radius: 0.75rem; font-size: 0.625rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; border: none; cursor: pointer; }
.detail-btn-small:hover { background-color: #2563eb; }

/* Quick View Specifics */
.close-qv-btn {
  position: absolute; top: 1.5rem; right: 1.5rem; z-index: 10;
  width: 3rem; height: 3rem; background-color: rgba(255,255,255,0.9); backdrop-filter: blur(12px);
  border-radius: 1rem; border: none; cursor: pointer; color: #0f172a; display: flex; align-items: center; justify-content: center;
}
.qv-img-section { width: 100%; min-height: 250px; position: relative; }
@media (min-width: 768px) { .qv-img-section { height: 100%; min-height: 400px; } }
.qv-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.qv-img-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); }
.qv-img-content { position: absolute; bottom: 2rem; left: 2rem; right: 2rem; z-index: 2; }
.qv-tag { padding: 0.25rem 0.75rem; background-color: rgba(255,255,255,0.2); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); border-radius: 9999px; font-size: 0.625rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #ffffff; }
.qv-title { font-size: 2.25rem; font-weight: 900; color: #ffffff; letter-spacing: -0.05em; line-height: 1.1; margin: 0.5rem 0 0 0; }
.qv-content-section { width: 100%; padding: 2rem; overflow-y: auto; text-align: left; }
@media (min-width: 768px) { .qv-content-section { padding: 3rem; } }
.qv-header-info { display: flex; align-items: center; gap: 1.5rem; }
.qv-icon-box { width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; }
.bg-blue-50 { background-color: #eff6ff; }
.bg-indigo-50 { background-color: #eef2ff; }
.qv-label { display: block; font-size: 0.625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.125rem;}
.qv-val { font-size: 0.875rem; font-weight: 900; color: #1e293b; }
.qv-section-title { font-size: 0.75rem; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem; }
.qv-desc { color: #64748b; font-weight: 500; line-height: 1.625; margin: 0; }
.grid-2-cols-gap { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); }
.qv-stat-box { padding: 1.5rem; background-color: #f8fafc; border-radius: 1.5rem; border: 1px solid #f1f5f9; }
.qv-stat-head { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; font-size: 0.625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; }
.qv-stat-val { font-size: 1.125rem; font-weight: 900; color: #0f172a; margin: 0; }
.qv-footer { padding-top: 2rem; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; }
@media (max-width: 639px) { .qv-footer { flex-direction: column; align-items: stretch; } }
.qv-price-label { display: block; font-size: 0.625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.25rem; }
.qv-price-val { font-size: 1.875rem; font-weight: 900; color: #2563eb; }
.book-now-btn { padding: 1.25rem 2.5rem; background-color: #2563eb; color: #ffffff; border-radius: 1rem; font-size: 0.875rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem; }

/* Reused Utilities */
.shadow-lg { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
.shadow-xl { box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
.shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
.transition-transform { transition-property: transform; transition-duration: 300ms; }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

/* Shared Animations */
.animate-fade-in-left { animation: fadeInLeft 0.8s ease-out forwards; }
.animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
.animate-pop { animation: popIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
.animate-slide-up { animation: slideUp 0.4s ease-out forwards; }
.animate-pop-modal { animation: popModal 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }

@keyframes fadeInLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes popIn { from { opacity: 0; transform: translateY(30px) scale(0.9); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes slideUp { from { opacity: 0; transform: translateY(100px); } to { opacity: 1; transform: translateY(0); } }
@keyframes popModal { from { opacity: 0; transform: scale(0.9) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
