<template>
  <div class="detail-page">
    <div class="detail-layout">
      <CustomerSidebar />

      <!-- NỘI DUNG CHÍNH AI PLANNER -->
      <section class="ai-content">
        <div class="page-header">
          <h1>Lên Kế Hoạch Bằng AI</h1>
          <p>Khám phá hành trình được thiết kế riêng cho bạn chỉ trong vài giây với công nghệ trí tuệ nhân tạo tiên tiến.</p>
        </div>

        <div class="ai-grid">
          <!-- CỘT FORM THÔNG TIN -->
          <div class="config-panel">
            <div class="config-card">
              <h2 class="config-title"><i class="fas fa-sparkles"></i> Thông Tin Chuyến Đi</h2>
              
              <div class="form-group">
                <label>Điểm đến</label>
                <div class="input-with-icon">
                  <i class="fas fa-map-marker-alt"></i>
                  <input type="text" v-model="form.diemDen" placeholder="Ví dụ: Đà Lạt, Việt Nam" />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label>Số ngày</label>
                  <div class="input-with-icon input-with-icon--group">
                    <i class="far fa-calendar-alt"></i>
                    <input type="number" v-model="form.soNgay" :min="AI_MIN_DAYS" :max="AI_MAX_DAYS" />
                  </div>
                </div>
                <div class="form-group">
                  <label>Ngân sách</label>
                  <div class="input-with-icon input-with-icon--group">
                    <i class="fas fa-money-bill-wave"></i>
                    <select v-model="form.nganSach">
                      <option
                        v-for="budgetOption in danhSachNganSach"
                        :key="budgetOption"
                        :value="budgetOption"
                      >
                        {{ budgetOption }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Sở thích</label>
                <div class="preferences-grid">
                  <button
                    v-for="interest in danhSachSoThich"
                    :key="interest"
                    type="button"
                    class="pref-checkbox"
                    :class="{ active: form.soThich.includes(interest) }"
                    @click="chuyenSoThich(interest)"
                  >
                    <span>{{ interest }}</span>
                  </button>
                </div>
              </div>

              <button class="btn-generate" @click="taoHanhTrinh" :disabled="dangXuLy || !form.diemDen">
                <i class="fas fa-magic" v-if="!dangXuLy"></i>
                <i class="fas fa-spinner fa-spin" v-else></i>
                {{ dangXuLy ? 'Đang phân tích dữ liệu...' : 'Tạo Hành Trình Ngay' }}
              </button>
            </div>
          </div>

          <!-- CỘT KẾT QUẢ HIỂN THỊ -->
          <div class="result-panel">
            
            <!-- TRẠNG THÁI LOADING -->
            <div class="state-box state-box--loading" v-if="dangXuLy">
              <div class="ai-loader"></div>
              <div class="loading-percent">{{ loadingPercent }}%</div>
              <div class="loading-track">
                <div class="loading-track__bar" :style="{ width: `${loadingPercent}%` }"></div>
              </div>
              <h3>AI đang suy nghĩ...</h3>
              <p>Hệ thống AI đang tổng hợp dữ liệu du lịch liên quan đến <strong>{{ form.diemDen }}</strong> để tạo ra lộ trình hoàn hảo nhất theo yêu cầu ngân sách {{ form.nganSach }}.</p>
            </div>

            <div class="state-box state-box--error" v-else-if="aiErrorMessage">
              <i class="fas fa-triangle-exclamation text-icon text-icon--error"></i>
              <h3>Không thể tạo lịch trình AI</h3>
              <p>{{ aiErrorMessage }}</p>
              <p class="error-code" v-if="aiErrorCode">Mã lỗi: {{ aiErrorCode }}</p>
              <button class="btn-retry" v-if="retryable" @click="taoHanhTrinh">
                <i class="fas fa-rotate-right"></i>
                Thử lại
              </button>
            </div>

            <!-- TRẠNG THÁI TRỐNG -->
            <div class="state-box state-box--empty" v-else-if="!ketQua">
              <i class="fas fa-map-marked-alt text-icon"></i>
              <h3>Hành trình của riêng bạn</h3>
              <p>Mô hình AI chuyên gia du lịch của chúng tôi đã sẵn sàng. Hãy cho biết điểm đến mơ ước của bạn và nhấn <strong>Tạo hành trình</strong>.</p>
            </div>

            <!-- KẾT QUẢ ĐÃ TẠO -->
            <div class="result-content" v-else>
              <!-- Cover Header -->
              <header
                class="result-hero"
                :style="{ backgroundImage: `linear-gradient(180deg, rgba(14,26,45,0.1), rgba(14,26,45,0.85)), url('${ketQua.hinhAnh}')` }"
              >
                <div class="result-hero__badge">ĐỀ XUẤT HÀNG ĐẦU</div>
                <div class="result-hero__badge result-hero__badge--fallback" v-if="planSource === 'fallback'">
                  Lịch trình dự phòng
                </div>
                <h2>{{ ketQua.tieuDe }}</h2>
                <div class="result-hero__meta">
                  <span><i class="far fa-clock"></i> {{ ketQua.thoiGian }}</span>
                  <span><i class="fas fa-wallet"></i> Ngân sách {{ ketQua.nganSach }}</span>
                </div>
                <div class="result-hero__actions">
                  <button class="hero-btn"><i class="fas fa-share-alt"></i></button>
                  <button class="hero-btn"><i class="far fa-bookmark"></i></button>
                </div>
              </header>

              <div class="inline-notice" v-if="aiNotice">
                <i class="fas fa-circle-info"></i>
                <span>{{ aiNotice }}</span>
              </div>

              <!-- Timeline lộ trình theo từng ngày -->
              <div class="timeline-day" v-for="(ngay, index) in displayedDays" :key="`day-${index}`">
                <div class="timeline-day__header">
                  <h3>{{ ngay.tieuDe }}</h3>
                  <span>{{ ngay.thoiGian }}</span>
                </div>
                <div class="timeline-list">
                  
                  <div class="timeline-item" v-for="(hd, idx) in ngay.danhSachHoatDong" :key="`activity-${index}-${idx}`">
                    <div class="timeline-item__icon" :class="hd.iconClass"><i :class="hd.icon"></i></div>
                    <div class="timeline-item__content">
                      <div class="timeline-item__label">{{ hd.buoi }}</div>
                      <div class="timeline-card">
                        <img :src="hd.hinhanh" :alt="hd.tieuDe" @error="fallbackImage($event, 'activity')" />
                        <div class="timeline-card__info">
                          <h4>{{ hd.tieuDe }}</h4>
                          <p>{{ hd.moTa }}</p>
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

              <div class="expand-action" v-if="coNhieuNgay && !xemTatCaNgay">
                <button class="btn-expand" @click="xemTatCaNgay = true">
                  Xem thêm {{ soNgayConLai }} ngày khác <i class="fas fa-chevron-down"></i>
                </button>
              </div>

              <div class="expand-action" v-if="coNhieuNgay && xemTatCaNgay">
                <button class="btn-expand" @click="xemTatCaNgay = false">
                  Thu gọn lịch trình <i class="fas fa-chevron-up"></i>
                </button>
              </div>

              <!-- Tổng chi phí Footer -->
              <div class="result-footer">
                <div class="total-cost">
                  <div class="total-cost__icon"><i class="fas fa-wallet"></i></div>
                  <div>
                    <span class="label">Tổng chi phí ước tính</span>
                    <div class="amount">{{ ketQua.tongChiPhi }} <span>/người</span></div>
                  </div>
                </div>
                <div class="footer-actions">
                  <button class="btn-outline"><i class="fas fa-download"></i> Xuất PDF</button>
                  <button class="btn-solid" @click="luuHanhTrinh" :disabled="dangLuu || !coTheLuu">
                    <i class="fas fa-save" v-if="!dangLuu"></i>
                    <i class="fas fa-spinner fa-spin" v-else></i>
                    {{ dangLuu ? "Đang lưu..." : "Lưu Hành Trình" }}
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="notice notice--error" v-if="thongBaoLoi">{{ thongBaoLoi }}</div>
        <div class="notice notice--success" v-if="thongBaoThanhCong">{{ thongBaoThanhCong }}</div>
      </section>
    </div>
  </div>
</template>

<script>
import { API_ORIGIN, goiApi } from "../../../services/httpClient.js";
import CustomerSidebar from "../CustomerSidebar.vue";
import {
  getStoredCustomerId,
} from "../KeHoach/planShared";
import {
  AI_BUDGET_OPTIONS,
  AI_INTEREST_OPTIONS,
  AI_MAX_DAYS,
  AI_MIN_DAYS,
  DEFAULT_AI_BUDGET,
  DEFAULT_AI_DAYS,
  DEFAULT_AI_INTERESTS,
} from "../../../constants/aiPreferences";

export default {
  name: "KhachHangLuuKeHoachAIPage",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      AI_MIN_DAYS,
      AI_MAX_DAYS,
      maKhachHang: "",
      dangXuLy: false,
      loadingPercent: 0,
      loadingTimer: null,
      dangLuu: false,
      thongBaoLoi: "",
      thongBaoThanhCong: "",
      aiErrorMessage: "",
      aiErrorCode: "",
      retryable: false,
      aiNotice: "",
      planSource: "",
      xemTatCaNgay: false,
      form: {
        diemDen: "",
        soNgay: DEFAULT_AI_DAYS,
        nganSach: DEFAULT_AI_BUDGET,
        soThich: [...DEFAULT_AI_INTERESTS]
      },
      danhSachSoThich: [...AI_INTEREST_OPTIONS],
      danhSachNganSach: [...AI_BUDGET_OPTIONS],
      ketQua: null,
      ketQuaAiRaw: null,
      fallbackCoverImage: "https://images.unsplash.com/photo-1596347958988-cb942eb22eb7?w=1000",
      fallbackActivityImage: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop",
    };
  },
  computed: {
    coTheLuu() {
      return Boolean(this.ketQuaAiRaw && this.maKhachHang);
    },
    coNhieuNgay() {
      return Array.isArray(this.ketQua?.lichTrinh) && this.ketQua.lichTrinh.length > 1;
    },
    soNgayConLai() {
      if (!Array.isArray(this.ketQua?.lichTrinh)) return 0;
      return Math.max(0, this.ketQua.lichTrinh.length - 1);
    },
    displayedDays() {
      if (!Array.isArray(this.ketQua?.lichTrinh)) return [];
      return this.xemTatCaNgay ? this.ketQua.lichTrinh : this.ketQua.lichTrinh.slice(0, 1);
    },
  },
  mounted() {
    this.maKhachHang = getStoredCustomerId();
  },
  beforeUnmount() {
    this.dungTienDo();
  },
  methods: {
    batDauTienDo() {
      this.dungTienDo();
      this.loadingPercent = 1;
      this.loadingTimer = setInterval(() => {
        if (!this.dangXuLy) return;
        if (this.loadingPercent < 70) {
          this.loadingPercent = Math.min(70, this.loadingPercent + 4);
          return;
        }
        if (this.loadingPercent < 90) {
          this.loadingPercent = Math.min(90, this.loadingPercent + 2);
          return;
        }
        if (this.loadingPercent < 97) {
          this.loadingPercent = Math.min(97, this.loadingPercent + 1);
        }
      }, 600);
    },
    ketThucTienDo() {
      this.loadingPercent = 100;
      this.dungTienDo();
    },
    dungTienDo() {
      if (this.loadingTimer) {
        clearInterval(this.loadingTimer);
        this.loadingTimer = null;
      }
    },
    getIconClass(thoiGian) {
      const tg = String(thoiGian || "").toLowerCase();
      if (tg.includes("chiều")) return "icon--afternoon";
      if (tg.includes("tối") || tg.includes("đêm")) return "icon--evening";
      return "icon--morning";
    },
    getIcon(thoiGian) {
      const tg = String(thoiGian || "").toLowerCase();
      if (tg.includes("chiều")) return "fas fa-cloud-sun";
      if (tg.includes("tối") || tg.includes("đêm")) return "fas fa-moon";
      return "fas fa-sun";
    },
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
    fallbackImage(event, type = "activity") {
      const target = event?.target;
      if (!target) return;
      const fallback =
        type === "cover" ? this.fallbackCoverImage : this.fallbackActivityImage;
      if (target.src !== fallback) {
        target.src = fallback;
      }
    },
    chuanHoaHoatDong(hoatDong = {}) {
      const buoi = String(
        hoatDong.buoi || hoatDong.thoiGian || hoatDong.thoi_gian || "Hoạt động"
      );
      const hinh = this.resolveImageUrl(
        hoatDong.hinhanh || hoatDong.hinh_anh || hoatDong.hinhAnh || "",
        this.fallbackActivityImage
      );
      return {
        buoi,
        iconClass: hoatDong.iconClass || this.getIconClass(buoi),
        icon: hoatDong.icon || this.getIcon(buoi),
        tieuDe: String(
          hoatDong.tieuDe || hoatDong.tieu_de || hoatDong.ten || "Hoạt động du lịch"
        ),
        moTa: String(
          hoatDong.moTa || hoatDong.mo_ta || hoatDong.hoat_dong || hoatDong.chiTiet || ""
        ),
        hinhanh: hinh,
        gia: String(hoatDong.gia || ""),
        thoiLuong: String(hoatDong.thoiLuong || hoatDong.thoi_luong || ""),
      };
    },
    chuanHoaNgay(ngay = {}, index = 0) {
      const tieuDe = String(
        ngay.tieuDe || ngay.ngay || `Ngày ${index + 1}`
      );
      const thoiGian = String(
        ngay.thoiGian || ngay.chuDe || ngay.ngayThang || ""
      );
      const rawActivities = ngay.danhSachHoatDong ?? ngay.hoatDong ?? [];
      const ds = Array.isArray(rawActivities)
        ? rawActivities
        : rawActivities && typeof rawActivities === "object"
          ? Object.values(rawActivities)
          : [];
      return {
        tieuDe,
        thoiGian,
        danhSachHoatDong: ds.map((item) => this.chuanHoaHoatDong(item)),
      };
    },
    chuanHoaKetQuaHienThi(ketQuaAi = {}) {
      const rawLichTrinh = ketQuaAi?.lichTrinh;
      const lichTrinhRaw = Array.isArray(rawLichTrinh)
        ? rawLichTrinh
        : rawLichTrinh && typeof rawLichTrinh === "object"
          ? Object.values(rawLichTrinh)
          : [];
      const lichTrinh = lichTrinhRaw.map((ngay, index) => this.chuanHoaNgay(ngay, index));
      const hinhAnh = this.resolveImageUrl(
        ketQuaAi.hinhAnh || lichTrinh?.[0]?.danhSachHoatDong?.[0]?.hinhanh || "",
        this.fallbackCoverImage
      );
      return {
        ...ketQuaAi,
        hinhAnh,
        lichTrinh,
      };
    },
    chuanHoaSoNgay(value) {
      const soNgay = Number(value);
      if (!Number.isFinite(soNgay)) return AI_MIN_DAYS;
      return Math.min(AI_MAX_DAYS, Math.max(AI_MIN_DAYS, Math.trunc(soNgay)));
    },
    chuyenSoThich(interest) {
      if (this.form.soThich.includes(interest)) {
        this.form.soThich = this.form.soThich.filter((item) => item !== interest);
      } else {
        this.form.soThich = [...this.form.soThich, interest];
      }
    },
    async taoHanhTrinh() {
      if (!this.form.diemDen) return;
      const soNgay = this.chuanHoaSoNgay(this.form.soNgay);
      this.form.soNgay = soNgay;

      this.thongBaoLoi = "";
      this.thongBaoThanhCong = "";
      this.aiErrorMessage = "";
      this.aiErrorCode = "";
      this.retryable = false;
      this.aiNotice = "";
      this.planSource = "";
      this.xemTatCaNgay = false;
      this.dangXuLy = true;
      this.ketQua = null;
      this.ketQuaAiRaw = null;
      this.batDauTienDo();

      try {
        const phanHoi = await goiApi("/khach-hang/ke-hoach-ai", {
          method: "POST",
          timeout: 120000,
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: {
            diemDen: this.form.diemDen,
            soNgay,
            nganSach: this.form.nganSach,
            soThich: this.form.soThich,
          },
        });

        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok || duLieuPhanHoi?.success === false) {
          this.aiErrorMessage =
            duLieuPhanHoi?.message ||
            `Không thể tạo lịch trình AI (HTTP ${phanHoi.status}).`;
          this.aiErrorCode = duLieuPhanHoi?.code || "AI_UNAVAILABLE";
          this.retryable =
            duLieuPhanHoi?.retryable !== undefined
              ? Boolean(duLieuPhanHoi.retryable)
              : phanHoi.status >= 500 || phanHoi.status === 429 || phanHoi.status === 503;
          return;
        }

        const ketQuaAi = duLieuPhanHoi?.data || {};
        const meta = ketQuaAi?.meta || {};

        this.ketQuaAiRaw = ketQuaAi;
        this.ketQua = this.chuanHoaKetQuaHienThi(ketQuaAi);
        this.planSource = meta.planSource || "ai";
        this.aiNotice = meta.notice || "";
      } catch (error) {
        const chiTietLoi = error?.message ? ` (${error.message})` : "";
        if (error?.code === "ECONNABORTED") {
          this.aiErrorMessage = "Yêu cầu AI quá thời gian chờ. Vui lòng thử lại.";
          this.aiErrorCode = "AI_TIMEOUT";
        } else {
          this.aiErrorMessage = `Không thể kết nối máy chủ AI${chiTietLoi}. Vui lòng kiểm tra backend hoặc VITE_API_BASE_URL.`;
          this.aiErrorCode = "NETWORK_ERROR";
        }
        this.retryable = true;
      } finally {
        this.ketThucTienDo();
        this.dangXuLy = false;
      }
    },
    async luuHanhTrinh() {
      this.thongBaoLoi = "";
      this.thongBaoThanhCong = "";

      if (!this.coTheLuu) {
        this.thongBaoLoi = "Bạn cần đăng nhập và tạo hành trình AI trước khi lưu.";
        return;
      }

      this.dangLuu = true;

      try {
        const payload = {
          ma_khach_hang: this.maKhachHang,
          ten_ke_hoach: this.ketQuaAiRaw?.tieuDe || `Hành trình ${this.form.diemDen}`,
          so_nguoi: 1,
          thong_tin_chuyen_di: {
            diemDen: this.form.diemDen,
            soNgay: this.chuanHoaSoNgay(this.form.soNgay),
            nganSach: this.form.nganSach,
            soThich: this.form.soThich,
            tongChiPhi: this.ketQuaAiRaw?.tongChiPhi || "",
          },
          ket_qua_ai: this.ketQuaAiRaw,
        };

        const phanHoi = await goiApi("/khach-hang/ke-hoach-ai/save", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: JSON.stringify(payload),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok || duLieuPhanHoi?.success === false) {
          throw new Error(duLieuPhanHoi?.message || "Không thể lưu hành trình AI.");
        }

        const maKeHoach = duLieuPhanHoi?.data?.ma_ke_hoach || "";
        this.thongBaoThanhCong = "Đã lưu hành trình AI thành công. Đang chuyển sang danh sách đã lưu.";

        setTimeout(() => {
          this.$router.push({
            path: "/khach-hang/hanh-trinh-da-luu",
            query: maKeHoach ? { created: maKeHoach } : {},
          });
        }, 700);
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể lưu hành trình AI.";
      } finally {
        this.dangLuu = false;
      }
    }
  }
};
</script>

<style scoped>
/* 1. LAYOUT & SIDEBAR CHUẨN KỊCH BẢN KHÁCH HÀNG */
.detail-page {
  background: #f7f9fc;
}

.detail-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 20px;
  min-height: calc(100vh - 68px);
}

.profile-panel {
  padding: 20px 0 0;
  background: #ffffff;
  border-right: 1px solid #edf1f7;
}

.profile-panel__card {
  display: grid;
  gap: 18px;
  padding: 18px 18px 24px 12px;
  border-bottom: 1px solid #edf1f7;
}

.profile-panel__identity {
  display: flex;
  align-items: center;
  gap: 14px;
}

.profile-panel__avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #ffffff;
  font-weight: 800;
  background: linear-gradient(135deg, #0b6b93 0%, #69b8ff 100%);
}

.profile-panel__identity strong {
  display: block;
  color: #1a2b4c;
  font-size: 1rem;
}

.profile-panel__identity span {
  display: block;
  margin-top: 4px;
  color: #637381;
  font-size: 0.92rem;
}

.profile-panel__menu {
  display: grid;
  gap: 8px;
  padding: 18px 10px 0 8px;
}

.profile-panel__link {
  min-height: 52px;
  padding: 0 16px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 14px;
  color: #454f5b;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s;
}

.profile-panel__link i {
  width: 18px;
  text-align: center;
}

.profile-panel__link.is-active,
.profile-panel__link:hover {
  background: #f0f5ff;
  color: #0055ff;
}

/* 2. CHÍNH - LÊN KẾ HOẠCH AI */
.ai-content {
  padding: 24px 0 0;
}

.page-header {
  margin-bottom: 30px;
}
.page-header h1 {
  margin: 0 0 10px;
  font-size: 2.2rem;
  font-weight: 900;
  color: #112a46;
}
.page-header p {
  color: #556b82;
  font-size: 1.05rem;
  margin: 0;
  max-width: 700px;
  line-height: 1.6;
}

.ai-grid {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 30px;
  align-items: start;
}

/* 2.1 CỘT FORM TRÁI */
.config-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
  border: 1px solid #edf1f7;
}
.config-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #1a2b4c;
  margin: 0 0 24px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.config-title i {
  color: #0055ff;
}

.form-group {
  margin-bottom: 20px;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.form-group label {
  display: block;
  font-size: 0.9rem;
  font-weight: 700;
  color: #454f5b;
  margin-bottom: 8px;
}

.input-with-icon {
  position: relative;
  background: #f4f6fb;
  border-radius: 12px;
  border: 1px solid #e1e8f2;
}
.input-with-icon i {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #8c98a4;
}
.input-with-icon input,
.input-with-icon select {
  width: 100%;
  border: none;
  background: transparent;
  padding: 12px 14px 12px 38px;
  font-size: 0.95rem;
  color: #112a46;
  outline: none;
  font-family: inherit;
}
.input-with-icon--group i {
  left: 10px;
}
.input-with-icon--group input,
.input-with-icon--group select {
  padding-left: 32px;
  padding-right: 10px;
}

/* Grid Checkbox Button Sở Thích */
.preferences-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.pref-checkbox {
  appearance: none;
  border: 1px solid #e1e8f2;
  outline: none;
  display: flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  padding: 9px 14px;
  border-radius: 100px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
  color: #454f5b;
  font-family: inherit;
}
.pref-checkbox::before {
  content: '\f00c';
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  display: inline-flex;
  width: 18px;
  height: 18px;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: #e1e8f2;
  color: transparent;
  font-size: 10px;
}
.pref-checkbox.active {
  border-color: #0055ff;
  color: #0055ff;
  background: #f0f5ff;
  font-weight: 600;
}
.pref-checkbox.active::before {
  background: #0055ff;
  color: #fff;
}

.btn-generate {
  width: 100%;
  background: #006b8f;
  color: #ffffff;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-size: 1.05rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  margin-top: 24px;
  transition: opacity 0.2s;
}
.btn-generate:hover {
  opacity: 0.9;
}
.btn-generate:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* 2.2 CỘT KẾT QUẢ PHẢI */
.result-panel {
  display: flex;
  flex-direction: column;
}

.state-box {
  background: #ffffff;
  border-radius: 20px;
  padding: 60px 40px;
  text-align: center;
  border: 1px solid #edf1f7;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 500px;
}
.state-box h3 {
  font-size: 1.5rem;
  color: #1a2b4c;
  margin: 20px 0 14px;
}
.state-box p {
  color: #637381;
  font-size: 1.05rem;
  line-height: 1.6;
  max-width: 500px;
}
.state-box--error {
  border-color: #fecaca;
  background: #fff7f7;
}
.text-icon {
  font-size: 4rem;
  color: #b0c4de;
}
.text-icon--error {
  color: #ef4444;
}
.error-code {
  margin-top: 8px;
  font-size: 0.88rem;
  color: #b91c1c;
  font-weight: 700;
}

.ai-loader {
  width: 60px;
  height: 60px;
  border: 5px solid #e1e8f2;
  border-top-color: #0055ff;
  border-radius: 50%;
  animation: s-spin 1s linear infinite;
}
@keyframes s-spin { 100% { transform: rotate(360deg); } }
.loading-percent {
  margin-top: 12px;
  font-size: 1.7rem;
  font-weight: 800;
  color: #2563eb;
}
.loading-track {
  width: 280px;
  max-width: 90%;
  height: 8px;
  background: #e2e8f0;
  border-radius: 999px;
  overflow: hidden;
  margin: 8px auto 4px;
}
.loading-track__bar {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
  transition: width 0.35s ease;
}
.btn-retry {
  margin-top: 16px;
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

/* RESULT RENDERED CONTENT */
.result-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* HERO */
.result-hero {
  position: relative;
  height: 240px;
  border-radius: 24px;
  padding: 30px;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  background-image: linear-gradient(180deg, rgba(14,26,45,0.1), rgba(14,26,45,0.85)), url('https://images.unsplash.com/photo-1596347958988-cb942eb22eb7?w=1000');
  background-size: cover;
  background-position: center;
  color: #ffffff;
  box-shadow: 0 10px 24px rgba(0,0,0,0.1);
}
.result-hero__badge {
  position: absolute;
  top: 30px;
  left: 30px;
  background: #baa2ff;
  color: #ffffff;
  font-size: 0.75rem;
  font-weight: 800;
  padding: 6px 14px;
  border-radius: 999px;
  letter-spacing: 0.5px;
}
.result-hero__badge--fallback {
  left: auto;
  right: 30px;
  background: #f59e0b;
}
.result-hero h2 {
  font-size: 2.2rem;
  font-weight: 900;
  margin: 0 0 10px;
  text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
.result-hero__meta {
  display: flex;
  gap: 16px;
  font-size: 0.95rem;
  font-weight: 500;
}
.result-hero__actions {
  position: absolute;
  right: 30px;
  bottom: 30px;
  display: flex;
  gap: 10px;
}
.hero-btn {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: rgba(0,0,0,0.4);
  backdrop-filter: blur(8px);
  color: #fff;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.hero-btn:hover { background: rgba(0,0,0,0.6); }
.inline-notice {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border-radius: 12px;
  border: 1px solid #fde68a;
  background: #fffbeb;
  color: #92400e;
  font-size: 0.92rem;
  font-weight: 600;
  margin: 0 0 8px;
}

/* TIMELINE BOX */
.timeline-day {
  background: #ffffff;
  border-radius: 24px;
  border: 1px solid #cce0ff;
  border-left: 5px solid #0060aa;
  padding: 24px;
  box-shadow: 0 12px 30px rgba(0,85,255,0.03);
}
.timeline-day__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}
.timeline-day__header h3 {
  font-size: 1.4rem;
  font-weight: 800;
  color: #00508b;
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
.icon--afternoon { background: #94a3b8; }
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
  width: 90px;
  height: 90px;
  border-radius: 12px;
  object-fit: cover;
}
.timeline-card__info {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.timeline-card__info h4 {
  margin: 0 0 6px;
  color: #0f172a;
  font-size: 1.05rem;
  font-weight: 800;
}
.timeline-card__info p {
  margin: 0 0 10px;
  color: #475569;
  font-size: 0.9rem;
  line-height: 1.4;
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
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 700;
}
.tag-time {
  background: transparent;
  color: #64748b;
  padding: 0;
}
.tag-time i { margin-right: 4px; }

.expand-action {
  display: flex;
  justify-content: center;
  margin-top: -6px;
  position: relative;
  z-index: 1;
}
.btn-expand {
  background: #ffffff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  padding: 10px 20px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 0.92rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
  box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
}
.btn-expand:hover {
  background: #eff6ff;
  border-color: #93c5fd;
}

/* FOOTER */
.result-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  border-radius: 20px;
  padding: 24px;
  border: 1px solid #e2e8f0;
}

.total-cost {
  display: flex;
  align-items: center;
  gap: 16px;
}
.total-cost__icon {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  background: #ebdfff;
  color: #834cff;
  display: grid;
  place-items: center;
  font-size: 1.4rem;
}
.total-cost .label {
  display: block;
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 4px;
}
.total-cost .amount {
  font-size: 1.6rem;
  font-weight: 900;
  color: #0f172a;
}
.total-cost .amount span {
  font-size: 0.9rem;
  color: #94a3b8;
  font-weight: 500;
}

.footer-actions {
  display: flex;
  gap: 14px;
}
.footer-actions button {
  padding: 12px 20px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}
.btn-outline {
  background: transparent;
  border: 1px solid #cbd5e1;
  color: #475569;
}
.btn-outline:hover { background: #f1f5f9; }
.btn-solid {
  background: #0076a0;
  color: #fff;
  border: none;
}
.btn-solid:hover { background: #008ebf; }
.btn-solid:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.notice {
  margin-top: 16px;
  padding: 12px 16px;
  border-radius: 10px;
  font-weight: 600;
}

.notice--error {
  background: #fff1f2;
  border: 1px solid #fecdd3;
  color: #be123c;
}

.notice--success {
  background: #ecfdf5;
  border: 1px solid #a7f3d0;
  color: #047857;
}

@media (max-width: 1024px) {
  .ai-grid {
    grid-template-columns: 1fr;
  }
}
</style>
