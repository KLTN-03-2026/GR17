<template>
  <div class="landing-page">
    <!-- Atmospheric Background Elements -->
    <div class="bg-elements">
      <div class="bg-pulse-1"></div>
      <div class="bg-pulse-2"></div>
      <div class="bg-float"></div>
    </div>

    <!-- HERO SECTION -->
    <section class="hero-section">
      <div
        class="hero-rail-backdrop"
        :style="{ backgroundImage: `url('${anhNenDuongRay}')` }"
        aria-hidden="true"
      >
        <div class="hero-rail-overlay"></div>
      </div>
      
      <div class="hero-container">
        <!-- Floating Images for Desktop -->
        <div class="floating-image floating-image-left">
          <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&q=80&w=600" alt="Bãi biển nhiệt đới" />
          <div class="img-overlay"></div>
        </div>
        
        <div class="floating-image floating-image-right">
          <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&q=80&w=600" alt="Dãy núi hùng vĩ" />
          <div class="img-overlay"></div>
        </div>

        <div class="hero-content">
          <div class="ai-badge">
            <div class="ai-pulse"></div>
            <span>TRỢ LÝ LẬP KẾ HOẠCH DU LỊCH BẰNG AI</span>
          </div>
          <h1>
            Khám Phá <span class="text-blue italic-text">Thế Giới</span><br/>
            Theo Cách Của Bạn
          </h1>
          <p class="hero-subtitle">
            Lên kế hoạch cho chuyến đi mơ ước của bạn trong vài giây với công nghệ AI thông minh nhất hiện nay.
          </p>
        </div>

        <!-- AI Travel Planner Form -->
        <div class="search-wrapper">
          <TravelForm 
            :dang-tai="dangTaiKeHoachAI" 
            @gui-ke-hoach="xuLyGuiKeHoach" 
          />
        </div>
      </div>
    </section>

    <!-- AI RESULT SECTION -->
    <main class="max-w-5xl mx-auto px-4 pb-20" ref="resultRef" v-if="lichTrinh || dangTaiKeHoachAI || aiErrorMessage">
      <div v-if="dangTaiKeHoachAI" class="loading-state">
        <div class="circular-progress">
          <svg viewBox="0 0 100 100">
            <circle class="bg" cx="50" cy="50" r="45"></circle>
            <circle class="progress" cx="50" cy="50" r="45" :stroke-dashoffset="283 - (283 * aiProgress) / 100"></circle>
          </svg>
          <div class="percentage">{{ aiProgress }}%</div>
        </div>
        <h3>Đang thiết kế hành trình...</h3>
        <p>Vui lòng đợi trong giây lát</p>
      </div>

      <div v-else-if="aiErrorMessage" class="inline-ai-error">
        <i class="fas fa-triangle-exclamation"></i>
        <h3>Không thể tạo lịch trình AI</h3>
        <p>{{ aiErrorMessage }}</p>
        <p class="inline-ai-error__code" v-if="aiErrorCode">Mã lỗi: {{ aiErrorCode }}</p>
        <button type="button" class="inline-ai-error__retry" v-if="retryable" @click="thuLaiKeHoachAI">
          <i class="fas fa-rotate-right"></i>
          Thử lại
        </button>
      </div>

      <div v-else-if="lichTrinh" class="result-animate">
        <div class="result-header">
          <div class="rh-icon"><i class="fas fa-layer-group"></i></div>
          <div class="rh-copy">
            <h2>Lịch trình của bạn</h2>
            <p v-if="planSource === 'fallback'">Lịch trình dự phòng từ dữ liệu hệ thống</p>
            <p v-else>Được tạo bởi trí tuệ nhân tạo Gemini</p>
            <span class="result-fallback-badge" v-if="planSource === 'fallback'">Lịch trình dự phòng</span>
          </div>
        </div>
        <div class="inline-ai-notice" v-if="aiNotice">{{ aiNotice }}</div>
        <div class="inline-ai-save inline-ai-save--error" v-if="luuLoiMessage">{{ luuLoiMessage }}</div>
        <div class="inline-ai-save inline-ai-save--success" v-if="luuThanhCongMessage">{{ luuThanhCongMessage }}</div>
        <HomeItineraryResult 
          :ketQua="lichTrinh"
          :dang-luu="dangLuuHanhTrinh"
          @luu-hanh-trinh="luuHanhTrinhAI"
        />
      </div>
    </main>

    <!-- TOUR NỔI BẬT SECTION -->
    <section class="section tours-section">
      <div class="section-header">
        <div class="section-title-wrapper">
          <h2>Tour Nổi Bật</h2>
          <p>Những trải nghiệm được đánh giá cao nhất từ cộng đồng.</p>
        </div>
        <a href="/tour-management" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
      </div>

      <div class="tours-grid">
        <article class="tour-card" v-for="tour in tours" :key="tour.id">
          <div class="tour-img-wrapper">
            <img :src="tour.image" :alt="tour.title" class="tour-img" />
            <span class="tour-badge">{{ tour.price }}</span>
          </div>
          <div class="tour-info">
            <div class="tour-rating">
              <i class="fas fa-star text-yellow"></i> <span>{{ tour.rating.toFixed(1) }} ({{ tour.danhSachDanhGia }} đánh giá)</span>
            </div>
            <h3>{{ tour.title }}</h3>
            <p class="tour-desc">{{ tour.description }}</p>
            <div class="tour-footer">
              <span class="tour-duration"><i class="far fa-clock"></i> {{ tour.days }}</span>
              <button @click="tourDangChon = tour" class="tour-link" style="border:none; cursor:pointer; background:none;">Xem nhanh</button>
            </div>
          </div>
        </article>
      </div>
    </section>


    <!-- Quick View Modal cho Tour Nổi Bật - Inline styled -->
    <div v-if="tourDangChon" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
      <div style="position:absolute;inset:0;background:rgba(15,23,42,0.85);" @click="tourDangChon = null"></div>
      <div style="position:relative;z-index:1;width:100%;max-width:900px;max-height:90vh;background:#ffffff;border-radius:24px;overflow:hidden;display:flex;flex-direction:row;box-shadow:0 25px 60px rgba(0,0,0,0.5);">
        <!-- Close -->
        <button @click="tourDangChon = null" style="position:absolute;top:16px;right:16px;z-index:10;width:40px;height:40px;background:rgba(255,255,255,0.95);border:none;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:#0f172a;">
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
            <button @click="tourDangChon = null" style="padding:12px 24px;background:#2563eb;color:#fff;border:none;border-radius:12px;font-size:13px;font-weight:900;text-transform:uppercase;cursor:pointer;display:flex;align-items:center;gap:8px;">Đóng <i class="fas fa-times"></i></button>
          </div>
        </div>
      </div>
    </div>


    <!-- VỀ CHÚNG TÔI SECTION -->
    <AboutSection />

    <!-- ĐIỂM ĐẾN PHỔ BIẾN SECTION -->
    <section class="section popular-destinations">
      <div class="section-header-center">
        <h2>Điểm Đến Phổ Biến</h2>
        <p>Tìm cảm hứng cho chuyến hành trình tiếp theo của bạn với những địa danh<br>đang được yêu thích nhất.</p>
      </div>

      <div class="destinations-grid">
        <!-- ĐÀ NẴNG (Large Left) -->
        <div class="dest-card dest-large">
          <img :src="destinations.danang" alt="Đà Nẵng">
          <div class="dest-overlay"></div>
          <div class="dest-content">
             <!-- Using user dynamic list from API if available -->
            <h3>{{ danhSachDiemDen[0]?.name || 'Đà Nẵng' }}</h3>
            <p>{{ danhSachDiemDen[0]?.subtitle || 'Thành phố đáng sống nhất Việt Nam' }}</p>
          </div>
        </div>
        
        <!-- HUẾ (Wide Top Right) -->
        <div class="dest-card dest-wide">
          <img :src="destinations.hue" alt="Huế">
          <div class="dest-overlay"></div>
          <div class="dest-content">
            <h3>{{ danhSachDiemDen[1]?.name || 'Huế' }}</h3>
            <p>{{ danhSachDiemDen[1]?.subtitle || 'Kinh đô cổ kính' }}</p>
          </div>
        </div>

        <!-- CẦN THƠ (Bottom Small 1) -->
        <div class="dest-card dest-small">
          <img :src="destinations.cantho" alt="Cần Thơ">
          <div class="dest-overlay"></div>
          <div class="dest-content">
            <h3>{{ danhSachDiemDen[2]?.name || 'Cần Thơ' }}</h3>
          </div>
        </div>

        <!-- PHÚ QUỐC (Bottom Small 2) -->
        <div class="dest-card dest-small">
          <img :src="destinations.phuquoc" alt="Phú Quốc">
          <div class="dest-overlay"></div>
          <div class="dest-content">
            <h3>{{ danhSachDiemDen[3]?.name || 'Phú Quốc' }}</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- TIN TỨC SECTION -->
    <NewsSection />

    <!-- 3 BƯỚC ĐƠN GIẢN SECTION -->
    <section class="section steps-section">
      <div class="section-header-center mb-50">
        <h2>Chỉ Với 3 Bước Đơn Giản</h2>
        <div class="title-underline"></div>
      </div>

      <div class="steps-container">
        <div class="step-item">
          <div class="step-icon">
            <i class="fas fa-map"></i>
          </div>
          <h3>1. Chọn Điểm Đến</h3>
          <p>Nhập địa điểm bạn muốn đến và sở thích cá nhân của mình.</p>
        </div>
        
        <div class="step-connector"></div>

        <div class="step-item">
          <div class="step-icon">
            <i class="fas fa-robot text-blue"></i>
          </div>
          <h3>2. AI Lên Lịch Trình</h3>
          <p>Hệ thống thông minh sẽ tự động tối ưu hóa đường đi và các hoạt động cho bạn.</p>
        </div>

        <div class="step-connector"></div>

        <div class="step-item">
          <div class="step-icon">
            <i class="fas fa-suitcase-rolling"></i>
          </div>
          <h3>3. Xách Balo Và Đi</h3>
          <p>Nhận lịch trình chi tiết trên điện thoại và sẵn sàng cho chuyến đi!</p>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS SECTION -->
    <section class="section testimonials-section">
      <div class="section-header-center mb-40">
        <h2 class="text-white">Người Dùng Nói Gì Về Chúng Tôi</h2>
      </div>

      <div class="testimonials-grid">
        <div class="testimonial-card" v-for="review in danhSachDanhGia" :key="review.id">
          <div class="testimonial-author">
            <img :src="review.avatar" :alt="review.name" class="author-avatar" />
            <div class="author-info">
              <h4>{{ review.name }}</h4>
              <p>{{ review.role }}</p>
            </div>
          </div>
          <p class="testimonial-text">"{{ review.text }}"</p>
          <div class="testimonial-stars">
            <i class="fas fa-star" v-for="n in 5" :key="n"></i>
          </div>
        </div>
      </div>
    </section>

    <!-- LIÊN HỆ SECTION -->
    <ContactSection />

    <!-- ĐĂNG KÝ BẢN TIN SECTION -->
    <Newsletter />
  </div>

</template>

<script>
import { goiApi } from '../../services/httpClient.js';
import { AI_MAX_DAYS, AI_MIN_DAYS } from "../../constants/aiPreferences";
import { getStoredCustomerId } from "../KhachHang/KeHoach/planShared";
const API_BASE = "/api";

const TOUR_FALLBACK_IMAGES = [
  "https://images.unsplash.com/photo-1528127269322-539801943592?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
  "https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
  "https://images.unsplash.com/photo-1506462945848-ac8ea6f609cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
];

const DEST_FALLBACK_IMAGES = [
  "https://images.unsplash.com/photo-1583417657208-ca3cd10d8ab3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
  "https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80",
  "https://images.unsplash.com/photo-1596707328639-5095e26ba4cb?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
  "https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
];

const HERO_RAIL_IMAGE = "https://images.unsplash.com/photo-1541427468627-a89a96e5ca1d?auto=format&fit=crop&w=2000&q=80"; // Hai Van pass train in Vietnam

function dinhDangGia(value) {
  const num = Number(value);
  if (!num || isNaN(num)) return "Liên hệ";
  return num.toLocaleString("vi-VN") + "đ";
}

function chuanHoaDanhSach(duLieuPhanHoi) {
  if (Array.isArray(duLieuPhanHoi)) return duLieuPhanHoi;
  if (Array.isArray(duLieuPhanHoi?.data)) return duLieuPhanHoi.data;
  if (Array.isArray(duLieuPhanHoi?.data?.data)) return duLieuPhanHoi.data.data;
  if (Array.isArray(duLieuPhanHoi?.result)) return duLieuPhanHoi.result;
  return [];
}

import TravelForm from "./TravelForm.vue";
import HomeItineraryResult from "./HomeItineraryResult.vue";
import AboutSection from "./AboutSection.vue";
import NewsSection from "./NewsSection.vue";
import ContactSection from "./ContactSection.vue";
import Newsletter from "./Newsletter.vue";

export default {
  name: "LandingPage",
  components: {
    TravelForm,
    HomeItineraryResult,
    AboutSection,
    NewsSection,
    ContactSection,
    Newsletter
  },
  data() {
    return {
      tourDangChon: null,
      dangTaiKeHoachAI: false,
      lichTrinh: null,
      aiProgress: 0,
      progressInterval: null,
      aiErrorMessage: "",
      aiErrorCode: "",
      retryable: false,
      aiNotice: "",
      planSource: "",
      duLieuKeHoachGanNhat: null,
      dangLuuHanhTrinh: false,
      luuLoiMessage: "",
      luuThanhCongMessage: "",
      anhNenDuongRay: HERO_RAIL_IMAGE,
      dangTaiTour: true,
      dangTaiDiemDen: true,
      tours: [],
      danhSachDiemDen: [],
      destinations: {
        danang: DEST_FALLBACK_IMAGES[0],
        hue: DEST_FALLBACK_IMAGES[1],
        cantho: DEST_FALLBACK_IMAGES[2],
        phuquoc: DEST_FALLBACK_IMAGES[3],
      },
      danhSachDanhGia: [
        {
          id: 1,
          name: "Nguyễn Lan Anh",
          role: "Freelancer, Hà Nội",
          avatar: "https://randomuser.me/api/portraits/women/44.jpg",
          text: "Ứng dụng thực sự giúp mình tiết kiệm rất nhiều thời gian. Lịch trình AI gợi ý rất hợp lý và thú vị."
        },
        {
          id: 2,
          name: "Trần Minh Quân",
          role: "Kỹ sư, TP.HCM",
          avatar: "https://randomuser.me/api/portraits/men/32.jpg",
          text: "Lần đầu tiên mình thấy một công cụ lập kế hoạch du lịch chi tiết đến vậy. Các tour được chọn lọc rất chất lượng."
        },
        {
          id: 3,
          name: "Phạm Hải Yến",
          role: "Marketing Manager, Đà Nẵng",
          avatar: "https://randomuser.me/api/portraits/women/68.jpg",
          text: "Giao diện đẹp mắt, dễ sử dụng. Mình đã giới thiệu cho cả gia đình dùng thử và ai cũng thích."
        }
      ]
    };
  },
  methods: {
    xuLyGuiKeHoach(duLieuBieuMau) {
      const soNgay = Math.min(
        AI_MAX_DAYS,
        Math.max(AI_MIN_DAYS, Number(duLieuBieuMau?.duration || AI_MIN_DAYS))
      );
      this.dangTaiKeHoachAI = true;
      this.lichTrinh = null;
      this.aiProgress = 0;
      this.aiErrorMessage = "";
      this.aiErrorCode = "";
      this.retryable = false;
      this.aiNotice = "";
      this.planSource = "";
      this.luuLoiMessage = "";
      this.luuThanhCongMessage = "";
      this.duLieuKeHoachGanNhat = { ...duLieuBieuMau, duration: soNgay };
      
      this.progressInterval = setInterval(() => {
        if (this.aiProgress < 95) {
          const step = Math.floor(Math.random() * 5) + 1;
          this.aiProgress = Math.min(this.aiProgress + step, 95);
        }
      }, 500);
      
      goiApi('/khach-hang/ke-hoach-ai', {
        method: 'POST',
        timeout: 120000,
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: {
          diemDen: duLieuBieuMau.destination,
          soNgay,
          nganSach: duLieuBieuMau.budget,
          soThich: duLieuBieuMau.interests,
        },
      })
      .then(async (response) => {
        const payload = await response.json().catch(() => ({}));
        clearInterval(this.progressInterval);
        this.aiProgress = 100;
        
        setTimeout(() => {
          this.dangTaiKeHoachAI = false;
          if (response.ok && payload.success) {
            const aiData = payload.data;
            this.lichTrinh = aiData;
            const meta = aiData?.meta || {};
            this.planSource = meta.planSource || "ai";
            this.aiNotice = meta.notice || "";
            
            this.$nextTick(() => {
              if (this.$refs.resultRef) {
                this.$refs.resultRef.scrollIntoView({ behavior: 'smooth', block: 'start' });
              }
            });
          } else {
            this.aiErrorMessage =
              payload.message ||
              `Không thể tạo lịch trình AI (HTTP ${response.status}).`;
            this.aiErrorCode = payload.code || "AI_UNAVAILABLE";
            this.retryable =
              payload.retryable !== undefined
                ? Boolean(payload.retryable)
                : response.status >= 500 || response.status === 429 || response.status === 503;
          }
        }, 600); // short delay to see 100%
      })
      .catch(error => {
        clearInterval(this.progressInterval);
        this.dangTaiKeHoachAI = false;
        const chiTietLoi = error?.message ? ` (${error.message})` : '';
        if (error?.code === 'ECONNABORTED') {
          this.aiErrorMessage = 'Yêu cầu AI quá thời gian chờ. Vui lòng thử lại.';
          this.aiErrorCode = "AI_TIMEOUT";
        } else {
          this.aiErrorMessage = `Không thể kết nối máy chủ AI${chiTietLoi}. Vui lòng kiểm tra backend hoặc VITE_API_BASE_URL.`;
          this.aiErrorCode = "NETWORK_ERROR";
        }
        this.retryable = true;
        console.error(error);
      });
    },
    thuLaiKeHoachAI() {
      if (!this.duLieuKeHoachGanNhat) {
        return;
      }
      this.xuLyGuiKeHoach(this.duLieuKeHoachGanNhat);
    },
    async luuHanhTrinhAI() {
      this.luuLoiMessage = "";
      this.luuThanhCongMessage = "";

      if (!this.lichTrinh || !this.duLieuKeHoachGanNhat) {
        this.luuLoiMessage = "Chưa có lịch trình để lưu.";
        return;
      }

      const maKhachHang = getStoredCustomerId();
      if (!maKhachHang) {
        this.luuLoiMessage = "Bạn cần đăng nhập tài khoản khách hàng để lưu hành trình.";
        this.$router.push("/dang-nhap").catch(() => {});
        return;
      }

      this.dangLuuHanhTrinh = true;

      try {
        const payload = {
          ma_khach_hang: maKhachHang,
          ten_ke_hoach:
            this.lichTrinh?.tieuDe || `Hành trình ${this.duLieuKeHoachGanNhat.destination || ""}`.trim(),
          so_nguoi: 1,
          thong_tin_chuyen_di: {
            diemDen: this.duLieuKeHoachGanNhat.destination || "",
            soNgay: Number(this.duLieuKeHoachGanNhat.duration || AI_MIN_DAYS),
            nganSach: this.duLieuKeHoachGanNhat.budget || "",
            soThich: Array.isArray(this.duLieuKeHoachGanNhat.interests)
              ? this.duLieuKeHoachGanNhat.interests
              : [],
            tongChiPhi: this.lichTrinh?.tongChiPhi || "",
          },
          ket_qua_ai: this.lichTrinh,
        };

        const phanHoi = await goiApi("/khach-hang/ke-hoach-ai/save", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: payload,
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok || duLieuPhanHoi?.success === false) {
          throw new Error(duLieuPhanHoi?.message || "Không thể lưu hành trình AI.");
        }

        const maKeHoach = String(duLieuPhanHoi?.data?.ma_ke_hoach || "").trim();
        this.luuThanhCongMessage = "Đã lưu hành trình thành công. Đang chuyển sang danh sách đã lưu.";

        setTimeout(() => {
          this.$router.push({
            path: "/khach-hang/hanh-trinh-da-luu",
            query: maKeHoach ? { created: maKeHoach } : {},
          });
        }, 700);
      } catch (error) {
        this.luuLoiMessage = error?.message || "Không thể lưu hành trình AI.";
      } finally {
        this.dangLuuHanhTrinh = false;
      }
    },
    explore() {
      // Old fallback if needed
    },
    async taiTour() {
      this.dangTaiTour = true;
      try {
        const phanHoi = await goiApi(`${API_BASE}/tour`, {
          headers: { Accept: "application/json" },
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách tour.");
        }
        const raw = chuanHoaDanhSach(duLieuPhanHoi);

        if (raw.length) {
          this.tours = raw.slice(0, 3).map((item, index) => {
            const loc = item.mo_ta && item.mo_ta.includes('Đà Nẵng') ? 'Đà Nẵng' : (item.mo_ta && item.mo_ta.includes('Sapa') ? 'Sapa' : (item.ten_tour && item.ten_tour.includes('Phú Quốc') ? 'Phú Quốc' : 'Việt Nam'));
            return {
              id: item.ma_tour || item.Ma_tour || index + 1,
              title: item.ten_tour || item.Ten_tour || "Tour chưa có tên",
              name: item.ten_tour || item.Ten_tour || "Tour chưa có tên",
              location: loc,
              description: item.mo_ta || item.Mo_ta || "Khám phá hành trình thú vị cùng Smart Travel.",
              days: item.so_ngay ? `${item.so_ngay} Ngày` : (item.So_ngay ? `${item.So_ngay} Ngày` : "Liên hệ"),
              duration: item.so_ngay ? `${item.so_ngay} Ngày` : (item.So_ngay ? `${item.So_ngay} Ngày` : "Liên hệ"),
              price: `Giá ${dinhDangGia(item.gia_tour || item.Gia_tour || item.gia || 0)}`,
              rating: 4.5 + Math.random() * 0.5,
              danhSachDanhGia: Math.floor(50 + Math.random() * 200),
              image: item.hinh_anh || item.Hinh_anh || TOUR_FALLBACK_IMAGES[index % TOUR_FALLBACK_IMAGES.length],
              tags: ["Được yêu thích", "Nổi bật"]
            };
          });
        }
      } catch (error) {
        console.warn("Không thể tải tour từ API, sử dụng dữ liệu mẫu:", error.message);
        this.tours = [
          { id: 1, title: "Khám Phá Vịnh Hạ Long 2 Ngày 1 Đêm", description: "Trải nghiệm du thuyền sang trọng giữa kỳ quan thiên nhiên thế giới.", days: "2 Ngày", price: "Giá 1.500.000đ", rating: 4.8, danhSachDanhGia: 124, image: TOUR_FALLBACK_IMAGES[0] },
          { id: 2, title: "Hành Trình Di Sản Hội An & Đà Nẵng", description: "Thưởng thức ẩm thực địa phương và thả hoa đăng trên sông Hoài.", days: "3 Ngày", price: "Giá 2.800.000đ", rating: 4.9, danhSachDanhGia: 86, image: TOUR_FALLBACK_IMAGES[1] },
          { id: 3, title: "Chinh Phục Tây Bắc: Sapa - Fansipan", description: "Khám phá vẻ đẹp hùng vĩ của mây ngàn.", days: "4 Ngày", price: "Giá 3.200.000đ", rating: 4.7, danhSachDanhGia: 215, image: TOUR_FALLBACK_IMAGES[2] },
        ];
      } finally {
        this.dangTaiTour = false;
      }
    },
    async taiDiemDen() {
      this.dangTaiDiemDen = true;
      try {
        const phanHoi = await goiApi(`${API_BASE}/dia-diem`, {
          headers: { Accept: "application/json" },
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách điểm đến.");
        }
        const raw = chuanHoaDanhSach(duLieuPhanHoi);

        if (raw.length >= 4) {
          this.destinations = {
            danang: raw[0]?.hinh_anh || raw[0]?.Hinh_anh || DEST_FALLBACK_IMAGES[0],
            hue: raw[1]?.hinh_anh || raw[1]?.Hinh_anh || DEST_FALLBACK_IMAGES[1],
            cantho: raw[2]?.hinh_anh || raw[2]?.Hinh_anh || DEST_FALLBACK_IMAGES[2],
            phuquoc: raw[3]?.hinh_anh || raw[3]?.Hinh_anh || DEST_FALLBACK_IMAGES[3],
          };
          this.danhSachDiemDen = raw.slice(0, 4).map((item, index) => ({
            id: item.ma_dia_diem || item.Ma_dia_diem || index,
            name: item.ten_dia_diem || item.Ten_dia_diem || ["Đà Nẵng", "Huế", "Cần Thơ", "Phú Quốc"][index],
            subtitle: item.mo_ta || item.Mo_ta || "",
          }));
        }
      } catch (error) {
        console.warn("Không thể tải điểm đến từ API, sử dụng dữ liệu mẫu:", error.message);
      } finally {
        this.dangTaiDiemDen = false;
      }
    },
  },
  mounted() {
    this.taiTour();
    this.taiDiemDen();
  },
};
</script>

<style scoped>
/* GENERAL STYLES */
.landing-page {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  background-color: #ffffff;
  color: #0f172a;
  overflow-x: hidden;
  position: relative;
}

.text-blue { color: #2563eb; }
.text-yellow { color: #f59e0b; }
.mb-50 { margin-bottom: 50px; }
.mb-40 { margin-bottom: 40px; }

/* SECTIONS */
.section {
  width: min(1200px, calc(100% - 40px));
  margin: 0 auto;
  padding: 80px 0;
}

/* BACKGROUND ATMOSPHERE */
.bg-elements {
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 0;
  overflow: hidden;
}

.bg-pulse-1 {
  position: absolute;
  top: -10%; left: -10%;
  width: 40%; height: 40%;
  background-color: #eff6ff;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.7;
  animation: pulse-glow 8s infinite alternate;
}

.bg-pulse-2 {
  position: absolute;
  bottom: -10%; right: -10%;
  width: 40%; height: 40%;
  background-color: #eef2ff;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.7;
  animation: pulse-glow 10s infinite alternate;
}

.bg-float {
  position: absolute;
  top: 20%; right: 5%;
  width: 20%; height: 20%;
  background-color: rgba(239, 246, 255, 0.5);
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.5;
  animation: float-slow 12s ease-in-out infinite;
}

@keyframes pulse-glow {
  0% { transform: scale(1); opacity: 0.5; }
  100% { transform: scale(1.1); opacity: 0.8; }
}
@keyframes float-slow {
  0%, 100% { transform: translate(0, 0); }
  50% { transform: translate(-20px, 30px); }
}

/* HERO SECTION */
.hero-section {
  position: relative;
  min-height: 85vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 100px 20px 140px;
  overflow: hidden;
  z-index: 10;
}

.hero-container {
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  text-align: center;
  position: relative;
  z-index: 2;
}

.hero-rail-backdrop {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  background-position: center bottom;
  background-repeat: no-repeat;
  background-size: cover;
  z-index: 0;
  overflow: hidden;
}

.hero-rail-overlay {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse at 50% 30%, rgba(15, 23, 42, 0.4) 0%, rgba(15, 23, 42, 0) 50%),
    linear-gradient(180deg, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0) 35%, rgba(255, 255, 255, 0) 65%, rgba(255, 255, 255, 1) 100%);
}

/* Images */
.floating-image {
  position: absolute;
  border-radius: 3rem;
  overflow: hidden;
  box-shadow: 0 32px 64px -16px rgba(0,0,0,0.2);
  border: 8px solid white;
  animation: float 6s ease-in-out infinite;
  display: none;
}

@media (min-width: 1024px) {
  .floating-image { display: block; }
}

.floating-image img {
  width: 100%; height: 100%; object-fit: cover;
}
.img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);
}

.floating-image-left {
  left: -40px; top: 40px;
  width: 256px; height: 256px;
  animation-delay: 0s;
  transform: rotate(-5deg);
}

.floating-image-right {
  right: -40px; top: 100px;
  width: 288px; height: 224px;
  animation-delay: -3s;
  transform: rotate(5deg);
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(var(--rotation, 0deg)); }
  50% { transform: translateY(-20px) rotate(var(--rotation, 0deg)); }
}

.floating-image-left { --rotation: -5deg; }
.floating-image-right { --rotation: 5deg; }

/* Content */
.hero-content {
  max-width: 900px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.ai-badge {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 8px 20px;
  background-color: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 999px;
  margin-bottom: 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.ai-pulse {
  width: 8px; height: 8px;
  background-color: #38bdf8;
  border-radius: 50%;
  animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
  0% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.4); }
  70% { box-shadow: 0 0 0 10px rgba(56, 189, 248, 0); }
  100% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0); }
}

.ai-badge span {
  color: #ffffff;
  font-weight: 900;
  font-size: 0.75rem;
  letter-spacing: 0.1em;
}

.hero-content h1 {
  font-size: clamp(3rem, 6vw, 5rem);
  font-weight: 900;
  line-height: 1.1;
  color: #ffffff;
  text-shadow: 0 4px 16px rgba(0, 0, 0, 0.6);
  letter-spacing: -2px;
  margin-bottom: 24px;
}

.hero-content h1 .text-blue {
  color: #38bdf8;
  text-shadow: 0 4px 16px rgba(0, 0, 0, 0.6);
}

.italic-text {
  font-style: italic;
}

.hero-subtitle {
  font-size: clamp(1.1rem, 2vw, 1.4rem);
  color: #f8fafc;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
  max-width: 700px;
  margin: 0 auto 50px;
  line-height: 1.6;
  font-weight: 600;
}

.search-wrapper {
  position: relative;
  z-index: 2;
}

/* AI RESPONSE SECTION */
.max-w-5xl { max-width: 1024px; }
.mx-auto { margin-left: auto; margin-right: auto; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.pb-20 { padding-bottom: 5rem; }

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 0;
  text-align: center;
}

.loading-spinner {
  width: 80px;
  height: 80px;
  border: 4px solid #eff6ff;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 24px;
}

.loading-state h3 {
  font-size: 1.5rem;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 8px;
}
.loading-state p {
  color: #94a3b8;
  font-weight: 500;
}

.result-animate {
  animation: fade-in-up 0.6s ease-out forwards;
}

@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.result-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 32px;
}

.rh-icon {
  width: 48px;
  height: 48px;
  background: #eff6ff;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2563eb;
  font-size: 1.25rem;
}

.rh-copy h2 {
  font-size: 1.5rem;
  font-weight: 900;
  color: #0f172a;
  margin: 0 0 4px 0;
}
.rh-copy p {
  font-size: 0.85rem;
  color: #94a3b8;
  font-weight: 600;
  margin: 0;
}

/* SECTION HEADERS */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 40px;
  position: relative;
  z-index: 10;
}

.section-title-wrapper h2, .section-header-center h2 {
  font-size: 2.5rem;
  font-weight: 900;
  color: #0f172a;
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}

.section-title-wrapper p, .section-header-center p {
  color: #64748b;
  font-size: 1.1rem;
  font-weight: 500;
}

.view-all {
  color: #2563eb;
  text-decoration: none;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #eff6ff;
  border-radius: 999px;
  transition: all 0.3s ease;
}

.view-all:hover {
  background: #dbeafe;
  transform: translateX(5px);
}

.section-header-center {
  text-align: center;
  margin-bottom: 50px;
  position: relative;
  z-index: 10;
}

.section-header-center p {
  max-width: 650px;
  margin: 0 auto;
  line-height: 1.6;
}

/* TOURS GRID */
.tours-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  position: relative;
  z-index: 10;
}

.tour-card {
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid #f1f5f9;
  display: flex;
  flex-direction: column;
}

.tour-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.12);
  border-color: #cbd5e1;
}

.tour-img-wrapper {
  position: relative;
  height: 240px;
  overflow: hidden;
}

.tour-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.tour-card:hover .tour-img {
  transform: scale(1.08);
}

.tour-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  color: #0f172a;
  font-weight: 800;
  padding: 8px 16px;
  border-radius: 999px;
  font-size: 0.9rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.tour-info {
  padding: 24px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.tour-rating {
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 12px;
  font-weight: 600;
}

.tour-info h3 {
  font-size: 1.3rem;
  font-weight: 800;
  margin-bottom: 12px;
  color: #0f172a;
  line-height: 1.4;
}

.tour-desc {
  color: #475569;
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 24px;
  flex: 1;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.tour-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 20px;
  border-top: 1px solid #f1f5f9;
}

.tour-duration {
  color: #0f172a;
  font-weight: 700;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 6px;
}

.tour-link {
  color: #2563eb;
  font-weight: 700;
  text-decoration: none;
  background: #eff6ff;
  padding: 8px 16px;
  border-radius: 10px;
  transition: background 0.2s ease;
}

.tour-link:hover {
  background: #dbeafe;
}

/* DESTINATIONS GRID */
.popular-destinations {
  position: relative;
  z-index: 10;
}

.destinations-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-template-rows: repeat(2, 280px);
  gap: 24px;
}

.dest-card {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  cursor: pointer;
}

.dest-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.dest-card:hover img {
  transform: scale(1.05);
}

.dest-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0.2) 50%, transparent 100%);
  transition: opacity 0.3s ease;
}

.dest-card:hover .dest-overlay {
  background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 50%, transparent 100%);
}

.dest-content {
  position: absolute;
  bottom: 30px;
  left: 30px;
  color: white;
  z-index: 1;
}

.dest-content h3 {
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: 8px;
  letter-spacing: -0.5px;
}

.dest-content p {
  font-size: 1rem;
  opacity: 0.9;
  font-weight: 500;
}

.dest-large {
  grid-column: 1 / 3;
  grid-row: 1 / 3;
}

.dest-large .dest-content h3 {
  font-size: 2.8rem;
}

.dest-wide {
  grid-column: 3 / 5;
  grid-row: 1 / 2;
}

.dest-small {
  grid-row: 2 / 3;
}
.dest-small:nth-child(3) { grid-column: 3 / 4; }
.dest-small:nth-child(4) { grid-column: 4 / 5; }

/* 3 STEPS SECTION */
.steps-section {
  position: relative;
  z-index: 10;
  background: #f8fafc;
  border-radius: 40px;
  padding: 100px 40px;
  margin-bottom: 80px;
}

.title-underline {
  width: 80px;
  height: 6px;
  background-color: #2563eb;
  margin: 0 auto;
  border-radius: 4px;
}

.steps-container {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-top: 60px;
  position: relative;
}

.step-item {
  text-align: center;
  flex: 1;
  position: relative;
  z-index: 2;
  padding: 0 20px;
}

.step-icon {
  width: 100px;
  height: 100px;
  background: #ffffff;
  color: #2563eb;
  border-radius: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  margin: 0 auto 30px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
  transform: rotate(-5deg);
  transition: transform 0.3s ease;
}

.step-item:hover .step-icon {
  transform: rotate(0deg) scale(1.05);
}

.step-item h3 {
  font-size: 1.4rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 16px;
}

.step-item p {
  color: #64748b;
  line-height: 1.6;
  font-size: 1rem;
  font-weight: 500;
}

.step-connector {
  flex: 1;
  height: 4px;
  border-top: 4px dotted #cbd5e1;
  margin-top: 50px;
  z-index: 1;
}

/* TESTIMONIALS SECTION */
.testimonials-section {
  background-color: #0f172a;
  border-radius: 40px;
  padding: 100px 40px;
  color: white;
  margin-bottom: 40px;
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.testimonial-card {
  background: rgba(255, 255, 255, 0.05);
  padding: 40px 30px;
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.author-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.author-info h4 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 4px;
}

.author-info p {
  font-size: 0.85rem;
  color: #94a3b8;
}

.testimonial-text {
  color: #cbd5e1;
  font-style: italic;
  line-height: 1.7;
  margin-bottom: 20px;
  font-size: 1.05rem;
}

.testimonial-stars {
  color: #f59e0b;
  font-size: 1rem;
  display: flex;
  gap: 6px;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .hero-rail-backdrop {
    top: 80px;
    width: calc(100% - 16px);
    height: 470px;
    border-radius: 36px;
  }

  .tours-grid, .testimonials-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .destinations-grid {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto auto;
  }
  .dest-large { grid-column: 1 / 3; grid-row: 1 / 2; height: 400px; }
  .dest-wide { grid-column: 1 / 3; grid-row: 2 / 3; height: 300px; }
  .dest-small:nth-child(3) { grid-column: 1 / 2; grid-row: 3 / 4; height: 250px; }
  .dest-small:nth-child(4) { grid-column: 2 / 3; grid-row: 3 / 4; height: 250px; }
  
  .hero-content h1 { font-size: 3.5rem; }
  .search-box { flex-wrap: wrap; }
  .search-divider { display: none; }
  .search-item { border-bottom: 1px solid #e2e8f0; border-right: none; }
  .search-btn { width: 100%; margin-top: 10px; }
}

@media (max-width: 768px) {
  .hero-rail-backdrop {
    top: 74px;
    height: 420px;
    border-radius: 28px;
    background-position: 56% center;
  }

  .tours-grid, .testimonials-grid {
    grid-template-columns: 1fr;
  }
  .destinations-grid {
    display: flex;
    flex-direction: column;
  }
  .dest-card { height: 250px; }
  
  .hero-content h1 { font-size: 2.5rem; }
  .hero-section { padding: 80px 20px; }
  
  .steps-container {
    flex-direction: column;
    gap: 40px;
    align-items: center;
  }
  .step-connector { display: none; }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
  }
  .testimonials-section, .steps-section {
    padding: 60px 20px;
    border-radius: 24px;
  }
}
/* Contact Section Extra styles removed for brevity as they are in ContactSection.vue */

/* Quick View Modal Styles Added for Tour Nổi Bật */
.modal-overlay {
  position: fixed; inset: 0; z-index: 110; display: flex; align-items: center; justify-content: center; padding: 1rem;
}
.modal-backdrop {
  position: absolute; inset: 0; background-color: rgba(15,23,42,0.8); backdrop-filter: blur(12px);
}
.quick-view-modal {
  position: relative; width: 95%; max-width: 1000px; background-color: #ffffff; border-radius: 3rem; overflow: hidden;
  max-height: 95vh; display: grid; grid-template-columns: 1fr; box-sizing: border-box;
}
@media (min-width: 768px) { .quick-view-modal { grid-template-columns: 1fr 1fr; } }

.close-qv-btn {
  position: absolute; top: 1.5rem; right: 1.5rem; z-index: 10;
  width: 3rem; height: 3rem; background-color: rgba(255,255,255,0.9); backdrop-filter: blur(12px);
  border-radius: 1rem; border: none; cursor: pointer; color: #0f172a; display: flex; align-items: center; justify-content: center;
  transition: all 0.3s;
}
.close-qv-btn:hover { background-color: #fff; transform: scale(1.05); }

.qv-img-section { width: 100%; min-height: 250px; position: relative; }
@media (min-width: 768px) { .qv-img-section { height: 100%; min-height: 400px; } }
.qv-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.qv-img-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); }
.qv-img-content { position: absolute; bottom: 2rem; left: 2rem; right: 2rem; z-index: 2; }
.qv-tag { padding: 0.25rem 0.75rem; background-color: rgba(255,255,255,0.2); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); border-radius: 9999px; font-size: 0.625rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #ffffff; margin-right: 0.5rem; }
.qv-title { font-size: 2.25rem; font-weight: 900; color: #ffffff; letter-spacing: -0.05em; line-height: 1.1; margin: 0.5rem 0 0 0; }
.qv-content-section { width: 100%; padding: 2rem; overflow-y: auto; text-align: left; }
@media (min-width: 768px) { .qv-content-section { padding: 3rem; } }
.qv-header-info { display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; }
.qv-icon-box { width: 2.5rem; height: 2.5rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; }
.bg-blue-50 { background-color: #eff6ff; color: #2563eb; }
.bg-indigo-50 { background-color: #eef2ff; color: #4f46e5; }
.qv-label { display: block; font-size: 0.625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.125rem;}
.qv-val { font-size: 0.875rem; font-weight: 900; color: #1e293b; }
.qv-section-title { font-size: 0.75rem; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem; }
.qv-desc { color: #64748b; font-weight: 500; line-height: 1.625; margin: 0; }
.grid-2-cols-gap { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 1.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem; }
.qv-stat-box { padding: 1.5rem; background-color: #f8fafc; border-radius: 1.5rem; border: 1px solid #f1f5f9; }
.qv-stat-head { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; font-size: 0.625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; }
.qv-stat-val { font-size: 1.125rem; font-weight: 900; color: #0f172a; margin: 0; }
.qv-footer { padding-top: 2rem; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; }
@media (max-width: 639px) { .qv-footer { flex-direction: column; align-items: stretch; } }
.qv-price-label { display: block; font-size: 0.625rem; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.25rem; }
.qv-price-val { font-size: 1.875rem; font-weight: 900; color: #2563eb; }
.book-now-btn { padding: 1.25rem 2.5rem; background-color: #2563eb; color: #ffffff; border-radius: 1rem; font-size: 0.875rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem; transition: background-color 0.3s; }
.book-now-btn:hover { background-color: #1d4ed8; }
.animate-pop-modal { animation: popModal 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
@keyframes popModal {
  0% { transform: scale(0.95); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

.inline-ai-error {
  background: #fff7f7;
  border: 1px solid #fecaca;
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  color: #7f1d1d;
}
.inline-ai-error i {
  font-size: 2rem;
  margin-bottom: 10px;
}
.inline-ai-error h3 {
  margin: 0 0 8px;
  font-size: 1.2rem;
  font-weight: 800;
}
.inline-ai-error__code {
  margin-top: 8px;
  font-size: 0.9rem;
  font-weight: 700;
}
.inline-ai-error__retry {
  margin-top: 14px;
  border: none;
  border-radius: 10px;
  background: #b45309;
  color: #fff;
  font-weight: 700;
  padding: 10px 16px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.inline-ai-notice {
  margin: 0 0 14px;
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #fde68a;
  background: #fffbeb;
  color: #92400e;
  font-size: 0.92rem;
  font-weight: 600;
}
.inline-ai-save {
  margin: 0 0 14px;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: 0.92rem;
  font-weight: 700;
}
.inline-ai-save--error {
  border: 1px solid #fecaca;
  background: #fff1f2;
  color: #9f1239;
}
.inline-ai-save--success {
  border: 1px solid #86efac;
  background: #f0fdf4;
  color: #166534;
}
.result-fallback-badge {
  display: inline-block;
  margin-top: 8px;
  padding: 4px 10px;
  border-radius: 999px;
  background: #f59e0b;
  color: #fff;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.3px;
}
/* CIRCULAR PROGRESS */
.circular-progress {
  position: relative;
  width: 90px;
  height: 90px;
  margin: 0 auto 20px;
}

.circular-progress svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.circular-progress circle {
  fill: none;
  stroke-width: 8;
  stroke-linecap: round;
}

.circular-progress .bg {
  stroke: #e2e8f0;
}

.circular-progress .progress {
  stroke: #2563eb;
  stroke-dasharray: 283;
  transition: stroke-dashoffset 0.5s ease;
}

.circular-progress .percentage {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 1.1rem;
  font-weight: 800;
  color: #1e293b;
}

.loading-state {
  text-align: center;
  padding: 80px 0;
}
.loading-state h3 {
  font-size: 1.5rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 8px;
}
.loading-state p {
  color: #64748b;
  font-size: 1rem;
}
</style>
