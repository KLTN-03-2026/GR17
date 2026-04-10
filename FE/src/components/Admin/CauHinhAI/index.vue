<template>
  <div class="admin-page-content">
    <div class="page-header">
      <h1>Cấu Hình Mô Hình AI (Prompt)</h1>
      <p>Chỉnh sửa kỹ sư nhắc lệnh (prompt) lõi để quản lý cách AI lập kế hoạch. Khu vực bên dưới để bạn test cấu hình AI vừa lưu.</p>
    </div>

    <!-- PHẦN 1: CẤU HÌNH PROMPT -->
    <div class="api-config-card">
      <div class="card-header">
        <h3><i class="fas fa-key"></i> Cấu Hình API</h3>
        <div class="card-actions">
          <button class="btn btn-outline" @click="taiApiConfig" :disabled="loadingApiConfig || savingApiConfig">
            <i class="fas fa-sync-alt" :class="{ 'fa-spin': loadingApiConfig }"></i>
            Làm mới
          </button>
          <button class="btn btn-primary" @click="luuApiConfig" :disabled="savingApiConfig">
            <i class="fas fa-save" v-if="!savingApiConfig"></i>
            <i class="fas fa-spinner fa-spin" v-else></i>
            {{ savingApiConfig ? 'Đang lưu...' : 'Lưu API Config' }}
          </button>
        </div>
      </div>

      <div class="card-body">
        <div class="api-status-row">
          <span class="status-pill" :class="apiConfig.hasGeminiApiKey ? 'ok' : 'missing'">
            Gemini Key: {{ apiConfig.hasGeminiApiKey ? 'Đã cấu hình' : 'Chưa cấu hình' }}
          </span>
          <span class="status-pill" :class="apiConfig.hasPexelsApiKey ? 'ok' : 'missing'">
            Pexels Key: {{ apiConfig.hasPexelsApiKey ? 'Đã cấu hình' : 'Chưa cấu hình' }}
          </span>
        </div>

        <div class="api-form-grid">
          <div class="form-group">
            <label>Gemini API Key</label>
            <div class="api-secret-input">
              <input
                :type="showGeminiKey ? 'text' : 'password'"
                v-model.trim="apiConfig.geminiApiKey"
                placeholder="Nhập key mới nếu muốn thay đổi"
                autocomplete="off"
              />
              <button type="button" class="icon-btn" @click="showGeminiKey = !showGeminiKey">
                <i :class="showGeminiKey ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <p class="api-key-mask" v-if="apiConfig.geminiApiKeyMasked">
              Key đang dùng: {{ apiConfig.geminiApiKeyMasked }}
            </p>
          </div>

          <div class="form-group">
            <label>Pexels API Key</label>
            <div class="api-secret-input">
              <input
                :type="showPexelsKey ? 'text' : 'password'"
                v-model.trim="apiConfig.pexelsApiKey"
                placeholder="Nhập key mới nếu muốn thay đổi"
                autocomplete="off"
              />
              <button type="button" class="icon-btn" @click="showPexelsKey = !showPexelsKey">
                <i :class="showPexelsKey ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <p class="api-key-mask" v-if="apiConfig.pexelsApiKeyMasked">
              Key đang dùng: {{ apiConfig.pexelsApiKeyMasked }}
            </p>
          </div>
        </div>

        <div class="form-group">
          <label>Gemini Model Fallbacks (phân tách bằng dấu phẩy)</label>
          <input
            class="api-text-input"
            type="text"
            v-model.trim="apiConfig.geminiModelFallbacks"
            placeholder="gemini-2.0-flash,gemini-flash-latest,gemini-2.5-flash"
          />
        </div>
      </div>
    </div>

    <div class="prompt-editor-card">
      <div class="card-header">
        <h3><i class="fas fa-terminal"></i> System Prompt Hiện Tại</h3>
        <div class="card-actions">
          <button class="btn btn-outline" @click="layPrompt" v-if="!isEditing" :disabled="dangLuuPrompt">
            <i class="fas fa-sync-alt"></i> Làm mới
          </button>
          <button class="btn btn-outline" @click="batChinhSua" v-if="!isEditing" :disabled="dangLuuPrompt">
            <i class="fas fa-edit"></i> Chỉnh sửa
          </button>
          
          <button class="btn btn-outline" @click="huyChinhSua" v-if="isEditing" :disabled="dangLuuPrompt">
            <i class="fas fa-times"></i> Hủy bỏ
          </button>
          <button class="btn btn-primary" @click="luuPrompt" v-if="isEditing" :disabled="dangLuuPrompt">
            <i class="fas fa-save" v-if="!dangLuuPrompt"></i>
            <i class="fas fa-spinner fa-spin" v-else></i> 
            {{ dangLuuPrompt ? 'Đang lưu...' : 'Lưu Thay Đổi' }}
          </button>
        </div>
      </div>
      <div class="card-body">
        <textarea 
          ref="promptTextarea"
          v-model="aiPrompt" 
          class="prompt-textarea"
          :class="{ 'is-readonly': !isEditing }"
          placeholder="Đang tải prompt cấu hình từ máy chủ..."
          spellcheck="false"
          :readonly="!isEditing"
        ></textarea>
        <div class="prompt-hint">
          <strong>Lưu ý quan trọng:</strong> KHÔNG xóa các biến như {diemDen}, {soNgay}, {nganSach}, {soThich}, {dbJson}. Giữ nguyên cấu trúc JSON mảng mẫu ở cuối câu lệnh để AI trả dữ liệu chuẩn.
        </div>
      </div>
    </div>

    <hr class="section-divider" />

    <!-- PHẦN 2: KHU VỰC TEST (Giống UI Khách Hàng) -->
    <div class="page-header">
      <h2><i class="fas fa-flask"></i> Kiểm Thử Kết Quả Khai Thác Bằng Tham Số</h2>
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

          <div class="form-group preferences-section">
            <div class="preferences-header" v-if="form.soThich.length > 0">
              <i class="fas fa-check"></i> SỞ THÍCH:
            </div>
            <div class="preferences-header" v-else style="color: #64748b;">
              Sở thích của bạn:
            </div>
            <div class="preferences-grid">
              <label 
                class="pref-pill" 
                v-for="item in danhSachSoThich" 
                :key="item"
                :class="{ active: form.soThich.includes(item) }"
              >
                <input type="checkbox" :value="item" v-model="form.soThich" />
                <i class="fas fa-check" v-if="form.soThich.includes(item)"></i>
                <span>{{ item }}</span>
              </label>
            </div>
          </div>

          <button class="btn-generate" @click="taoHanhTrinh" :disabled="dangXuLy || !form.diemDen">
            <i class="fas fa-magic" v-if="!dangXuLy"></i>
            <i class="fas fa-spinner fa-spin" v-else></i>
            {{ dangXuLy ? 'Đang gọi API...' : 'Test Thực Thi Mẫu' }}
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
          <h3>Đang chờ AI phản hồi...</h3>
          <p>Hệ thống đang dùng hệ prompt bên trên cùng với các biến truyền vào để xử lý tác vụ.</p>
        </div>

        <!-- TRẠNG THÁI TRỐNG -->
        <div class="state-box state-box--error" v-else-if="aiErrorMessage">
          <i class="fas fa-triangle-exclamation text-icon text-icon--error"></i>
          <h3>Không thể tạo lịch trình test</h3>
          <p>{{ aiErrorMessage }}</p>
          <p class="error-code" v-if="aiErrorCode">Mã lỗi: {{ aiErrorCode }}</p>
          <button class="btn-retry" v-if="retryable" @click="taoHanhTrinh">
            <i class="fas fa-rotate-right"></i>
            Thử lại
          </button>
        </div>

        <div class="state-box state-box--empty" v-else-if="!ketQua">
          <i class="fas fa-code text-icon"></i>
          <h3>Khu vực Testing AI</h3>
          <p>Đổi giá trị form và ấn vào nút <strong>Test Thực Thi Mẫu</strong> để xem kết quả trả về của prompt hiện tại mà không làm ảnh hưởng luồng client.</p>
        </div>

        <!-- KẾT QUẢ ĐÃ TẠO -->
        <div class="result-content" v-else>
          <!-- Cover Header -->
          <header class="result-hero" :style="{ backgroundImage: `linear-gradient(180deg, rgba(14,26,45,0.1), rgba(14,26,45,0.85)), url('${ketQua.hinhAnh}')` }">
            <div class="result-hero__badge">KẾT QUẢ TEST AI</div>
            <div class="result-hero__badge result-hero__badge--fallback" v-if="planSource === 'fallback'">Lịch trình dự phòng</div>
            <h2>{{ ketQua.tieuDe }}</h2>
            <div class="result-hero__meta">
              <span><i class="far fa-clock"></i> {{ ketQua.thoiGian }}</span>
              <span><i class="fas fa-wallet"></i> {{ ketQua.nganSach }}</span>
            </div>
          </header>

          <div class="inline-notice" v-if="aiNotice">
            <i class="fas fa-circle-info"></i>
            <span>{{ aiNotice }}</span>
          </div>

          <!-- Timeline lộ trình Ngày -->
          <div class="timeline-day" v-for="(ngay, index) in displayedDays" :key="index">
            <div class="timeline-day__header">
              <h3>{{ ngay.tieuDe }}</h3>
              <span>{{ ngay.thoiGian }}</span>
            </div>
            <div class="timeline-list">
              
              <div class="timeline-item" v-for="(hd, idx) in ngay.danhSachHoatDong" :key="idx">
                <div class="timeline-item__icon" :class="hd.iconClass || getIconClass(hd.buoi || hd.thoiGian || hd.thoi_gian)"><i :class="hd.icon || getIcon(hd.buoi || hd.thoiGian || hd.thoi_gian)"></i></div>
                <div class="timeline-item__content">
                  <div class="timeline-item__label">{{ hd.buoi }}</div>
                  <div class="timeline-card">
                    <img :src="hd.hinhanh" :alt="hd.tieuDe || 'Hình ảnh hoạt động'" @error="fallbackImage" />
                    <div class="timeline-card__info">
                      <h4>{{ hd.tieuDe }}</h4>
                      <p>{{ hd.moTa }}</p>
                      <div class="timeline-card__meta">
                        <span class="tag-price" v-if="hd.gia">{{ hd.gia }}</span>
                        <span class="tag-time"><i class="far fa-clock"></i> {{ hd.thoiLuong }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { API_ORIGIN, goiApi } from "../../../services/httpClient.js";
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
  name: "AdminCauHinhAI",
  data() {
    return {
      AI_MIN_DAYS,
      AI_MAX_DAYS,
      aiPrompt: "",
      dangLuuPrompt: false,
      isEditing: false,
      loadingApiConfig: false,
      savingApiConfig: false,
      showGeminiKey: false,
      showPexelsKey: false,
      apiConfig: {
        geminiApiKey: "",
        pexelsApiKey: "",
        geminiApiKeyMasked: "",
        pexelsApiKeyMasked: "",
        geminiModelFallbacks: "gemini-2.0-flash,gemini-flash-latest,gemini-2.5-flash",
        hasGeminiApiKey: false,
        hasPexelsApiKey: false
      },
      
      // Test mode state
      dangXuLy: false,
      loadingPercent: 0,
      loadingTimer: null,
      form: {
        diemDen: "Đà Lạt",
        soNgay: DEFAULT_AI_DAYS,
        nganSach: DEFAULT_AI_BUDGET,
        soThich: [...DEFAULT_AI_INTERESTS]
      },
      danhSachSoThich: [...AI_INTEREST_OPTIONS],
      danhSachNganSach: [...AI_BUDGET_OPTIONS],
      ketQua: null,
      aiErrorMessage: "",
      aiErrorCode: "",
      retryable: false,
      aiNotice: "",
      planSource: ""
    };
  },
  mounted() {
    this.taiApiConfig();
    this.layPrompt();
  },
  beforeUnmount() {
    this.dungTienDo();
  },
  computed: {
    displayedDays() {
      const raw = this.ketQua?.lichTrinh;
      if (Array.isArray(raw)) return raw;
      if (raw && typeof raw === "object") return Object.values(raw);
      return [];
    },
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
    fallbackImage(event) {
      const target = event?.target;
      if (!target) return;
      const fallback =
        "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop";
      if (target.src !== fallback) {
        target.src = fallback;
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
    chuanHoaKetQuaHienThi(ketQuaAi = {}) {
      const fallbackCover =
        "https://images.unsplash.com/photo-1596347958988-cb942eb22eb7?w=1000";
      const fallbackActivity =
        "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop";

      const rawLichTrinh = ketQuaAi?.lichTrinh;
      const lichTrinh = (Array.isArray(rawLichTrinh)
        ? rawLichTrinh
        : rawLichTrinh && typeof rawLichTrinh === "object"
          ? Object.values(rawLichTrinh)
          : []
      ).map((ngay) => {
        const rawActivities = ngay?.danhSachHoatDong ?? ngay?.hoatDong ?? [];
        const danhSachHoatDong = (Array.isArray(rawActivities)
          ? rawActivities
          : rawActivities && typeof rawActivities === "object"
            ? Object.values(rawActivities)
            : []
        ).map((hd) => ({
          ...hd,
          hinhanh: this.resolveImageUrl(
            hd?.hinhanh || hd?.hinh_anh || hd?.hinhAnh || "",
            fallbackActivity
          ),
        }));

        return {
          ...ngay,
          danhSachHoatDong,
        };
      });

      return {
        ...ketQuaAi,
        hinhAnh: this.resolveImageUrl(
          ketQuaAi?.hinhAnh || lichTrinh?.[0]?.danhSachHoatDong?.[0]?.hinhanh || "",
          fallbackCover
        ),
        lichTrinh,
      };
    },
    chuanHoaSoNgay(value) {
      const soNgay = Number(value);
      if (!Number.isFinite(soNgay)) return AI_MIN_DAYS;
      return Math.min(AI_MAX_DAYS, Math.max(AI_MIN_DAYS, Math.trunc(soNgay)));
    },
    async taiApiConfig() {
      this.loadingApiConfig = true;
      try {
        const response = await goiApi("/admin/cau-hinh-ai/api-config", {
          headers: { Accept: "application/json" },
        });
        const payloadResponse = await response.json().catch(() => ({}));
        if (!response.ok || payloadResponse?.status !== "success") {
          throw new Error(payloadResponse?.message || "Không thể tải cấu hình API từ máy chủ.");
        }

        const payload = payloadResponse?.data || {};
        this.apiConfig.hasGeminiApiKey = Boolean(payload.hasGeminiApiKey);
        this.apiConfig.hasPexelsApiKey = Boolean(payload.hasPexelsApiKey);
        this.apiConfig.geminiApiKeyMasked = payload.geminiApiKeyMasked || "";
        this.apiConfig.pexelsApiKeyMasked = payload.pexelsApiKeyMasked || "";
        this.apiConfig.geminiModelFallbacks =
          payload.geminiModelFallbacks ||
          "gemini-2.0-flash,gemini-flash-latest,gemini-2.5-flash";
      } catch (err) {
        console.error("Lỗi tải API config:", err);
        alert(err?.message || "Không thể tải cấu hình API từ máy chủ.");
      } finally {
        this.loadingApiConfig = false;
      }
    },
    async luuApiConfig() {
      this.savingApiConfig = true;
      try {
        const response = await goiApi("/admin/cau-hinh-ai/api-config", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: {
            geminiApiKey: this.apiConfig.geminiApiKey,
            pexelsApiKey: this.apiConfig.pexelsApiKey,
            geminiModelFallbacks: this.apiConfig.geminiModelFallbacks,
          },
        });
        const payloadResponse = await response.json().catch(() => ({}));
        if (!response.ok || payloadResponse?.status !== "success") {
          throw new Error(payloadResponse?.message || "Lưu cấu hình API thất bại.");
        }

        const payload = payloadResponse?.data || {};
        this.apiConfig.hasGeminiApiKey = Boolean(payload.hasGeminiApiKey);
        this.apiConfig.hasPexelsApiKey = Boolean(payload.hasPexelsApiKey);
        this.apiConfig.geminiApiKeyMasked = payload.geminiApiKeyMasked || "";
        this.apiConfig.pexelsApiKeyMasked = payload.pexelsApiKeyMasked || "";
        this.apiConfig.geminiModelFallbacks =
          payload.geminiModelFallbacks ||
          this.apiConfig.geminiModelFallbacks;
        this.apiConfig.geminiApiKey = "";
        this.apiConfig.pexelsApiKey = "";
        alert("Đã lưu cấu hình API thành công.");
      } catch (err) {
        console.error("Lỗi lưu API config:", err);
        alert(err?.message || "Lưu cấu hình API thất bại.");
      } finally {
        this.savingApiConfig = false;
      }
    },
    batChinhSua() {
      this.isEditing = true;
      this.$nextTick(() => {
        if (this.$refs.promptTextarea) {
          this.$refs.promptTextarea.focus();
        }
      });
    },
    huyChinhSua() {
      // Xác nhận nếu có thể họ gõ nhiều thứ
      if (confirm("Những thay đổi chưa lưu sẽ bị mất. Bạn có chắc chắn muốn hủy?")) {
        this.isEditing = false;
        this.layPrompt(); // tải lại text ban đầu
      }
    },
    async layPrompt() {
      try {
        const response = await goiApi("/admin/cau-hinh-ai", {
          headers: { Accept: "application/json" },
        });
        const payloadResponse = await response.json().catch(() => ({}));
        if (!response.ok || payloadResponse?.status !== "success") {
          throw new Error(payloadResponse?.message || "Lỗi tải prompt từ máy chủ!");
        }

        this.aiPrompt = payloadResponse?.data?.prompt || "";
        this.isEditing = false;
      } catch (err) {
        console.error("Lỗi lấy prompt:", err);
        alert(err?.message || "Lỗi tải prompt từ máy chủ!");
      }
    },
    async luuPrompt() {
      if (!this.aiPrompt || this.aiPrompt.length < 50) {
        alert("Prompt quá ngắn, vui lòng thiết lập dài hơn hoặc chứa đủ JSON template.");
        return;
      }
      this.dangLuuPrompt = true;
      try {
        const response = await goiApi("/admin/cau-hinh-ai", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: { prompt: this.aiPrompt },
        });
        const payloadResponse = await response.json().catch(() => ({}));
        if (!response.ok || payloadResponse?.status !== "success") {
          throw new Error(payloadResponse?.message || "Lỗi khi lưu prompt.");
        }

        this.isEditing = false;
        alert("Đã lưu prompt thành công! API lập kế hoạch sẽ chạy dựa vào lời nhắc mới.");
      } catch (err) {
        alert(err?.message || "Lỗi khi lưu prompt.");
        console.error(err);
      } finally {
        this.dangLuuPrompt = false;
      }
    },
    taoHanhTrinh() {
      if (!this.form.diemDen) return;
      const soNgay = this.chuanHoaSoNgay(this.form.soNgay);
      this.form.soNgay = soNgay;
      
      this.dangXuLy = true;
      this.ketQua = null;
      this.aiErrorMessage = "";
      this.aiErrorCode = "";
      this.retryable = false;
      this.aiNotice = "";
      this.planSource = "";
      this.batDauTienDo();
      
      // Request without auth since it's testing the customer API
      goiApi('/khach-hang/ke-hoach-ai', {
        method: 'POST',
        timeout: 120000,
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: {
          diemDen: this.form.diemDen,
          soNgay,
          nganSach: this.form.nganSach,
          soThich: this.form.soThich,
        },
      })
      .then(async (response) => {
        const payload = await response.json().catch(() => ({}));
        this.ketThucTienDo();
        this.dangXuLy = false;
        if (response.ok && payload.success) {
          this.ketQua = this.chuanHoaKetQuaHienThi(payload.data || {});
          const meta = this.ketQua?.meta || {};
          this.planSource = meta.planSource || "ai";
          this.aiNotice = meta.notice || "";
        } else {
          this.aiErrorMessage =
            payload.message ||
            `Không thể tạo lịch trình test (HTTP ${response.status}).`;
          this.aiErrorCode = payload.code || "AI_UNAVAILABLE";
          this.retryable =
            payload.retryable !== undefined
              ? Boolean(payload.retryable)
              : response.status >= 500 || response.status === 429 || response.status === 503;
        }
      })
      .catch(error => {
        this.ketThucTienDo();
        this.dangXuLy = false;
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
    }
  }
};
</script>

<style scoped>
.admin-page-content {
  padding: 24px 30px;
}
.page-header {
  margin-bottom: 24px;
}
.page-header h1 {
  font-size: 1.8rem;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 8px;
}
.page-header h2 {
  font-size: 1.5rem;
  color: #0f172a;
  margin-top: 30px;
}
.page-header p {
  color: #64748b;
  font-size: 1.05rem;
}
.section-divider {
  border: 0;
  height: 1px;
  background: #e2e8f0;
  margin: 40px 0;
}

.api-config-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  margin-bottom: 24px;
  overflow: hidden;
}
.api-status-row {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 16px;
}
.status-pill {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 6px 12px;
  font-size: 0.82rem;
  font-weight: 700;
}
.status-pill.ok {
  background: #dcfce7;
  color: #166534;
}
.status-pill.missing {
  background: #fee2e2;
  color: #b91c1c;
}
.api-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.api-secret-input {
  display: flex;
  align-items: center;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 0 8px 0 10px;
  background: #ffffff;
}
.api-secret-input input {
  flex: 1;
  border: 0;
  outline: none;
  height: 40px;
  font-size: 0.92rem;
  background: transparent;
}
.icon-btn {
  border: 0;
  background: transparent;
  color: #64748b;
  cursor: pointer;
  width: 30px;
  height: 30px;
  border-radius: 6px;
}
.icon-btn:hover {
  background: #f1f5f9;
}
.api-text-input {
  width: 100%;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 0.92rem;
  outline: none;
}
.api-text-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}
.api-key-mask {
  margin: 6px 2px 0;
  font-size: 0.8rem;
  color: #475569;
  font-weight: 600;
}

/* KHUNG PROMPT EDITOR */
.prompt-editor-card {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}
.card-header h3 {
  margin: 0;
  font-size: 1.1rem;
  color: #334155;
  font-weight: 700;
}
.card-actions {
  display: flex;
  gap: 12px;
}
.btn {
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-outline { background: white; border: 1px solid #cbd5e1; color: #475569; }
.btn-outline:hover { background: #f1f5f9; }

.card-body {
  padding: 24px;
}
.prompt-textarea {
  width: 100%;
  height: 400px;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #cbd5e1;
  background: #1e293b;
  color: #38bdf8;
  font-family: 'Consolas', 'Monaco', monospace;
  font-size: 0.95rem;
  line-height: 1.6;
  resize: vertical;
  transition: all 0.3s;
}
.prompt-textarea.is-readonly {
  background: #f8fafc;
  color: #475569;
  border-color: #e2e8f0;
  cursor: not-allowed;
  opacity: 0.8;
}
.prompt-textarea:focus:not(.is-readonly) {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
.prompt-hint {
  padding: 12px;
  background: #fffbeb;
  color: #b45309;
  border-left: 4px solid #f59e0b;
  border-radius: 4px;
  margin-top: 16px;
  font-size: 0.9rem;
}


/* TÁI SỬ DỤNG CSS CỦA AI PLANNER TỪ CUSTOMER */
.ai-grid {
  display: grid;
  grid-template-columns: 360px 1fr;
  gap: 30px;
  align-items: start;
}
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
}
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group label { display: block; font-size: 0.9rem; font-weight: 700; color: #454f5b; margin-bottom: 8px; }
.input-with-icon { position: relative; background: #f4f6fb; border-radius: 12px; border: 1px solid #e1e8f2; }
.input-with-icon i { position: absolute; left: 14px; top: 14px; color: #8c98a4; }
.input-with-icon input, .input-with-icon select { width: 100%; border: none; background: transparent; padding: 12px 14px 12px 38px; color: #112a46; outline: none; }
.input-with-icon--group i { left: 10px; }
.input-with-icon--group input, .input-with-icon--group select { padding-left: 32px; }

.preferences-section { background: rgba(240, 244, 248, 0.4); padding: 16px; border-radius: 16px; margin-top: 10px; }
.preferences-header { text-align: center; margin-bottom: 12px; font-size: 0.8rem; font-weight: 800; color: #3b82f6; }
.preferences-grid { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
.pref-pill { display: inline-flex; align-items: center; gap: 6px; background: #ffffff; padding: 8px 16px; border-radius: 30px; cursor: pointer; transition: 0.2s; font-size: 0.85rem; font-weight: 700; color: #4b5563; border: 1px solid transparent; }
.pref-pill.active { background: #2563eb; color: #ffffff; }
.pref-pill input { display: none; }

.btn-generate { width: 100%; background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%); color: #ffffff; border: none; min-height: 52px; padding: 0 20px; border-radius: 12px; font-size: 1.05rem; font-weight: 700; cursor: pointer; margin-top: 24px; transition: 0.2s; }
.btn-generate:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(11, 99, 160, 0.25); }

.result-panel { display: flex; flex-direction: column; }
.state-box { background: #ffffff; border-radius: 20px; padding: 60px 40px; text-align: center; border: 1px solid #edf1f7; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 500px; }
.state-box h3 { font-size: 1.5rem; color: #1a2b4c; margin: 20px 0 14px; }
.state-box p { color: #637381; font-size: 1.05rem; max-width: 500px; }
.text-icon { font-size: 4rem; color: #b0c4de; }
.state-box--error { border-color: #fecaca; background: #fff7f7; }
.text-icon--error { color: #ef4444; }
.btn-retry { margin-top: 16px; border: none; border-radius: 10px; background: #b45309; color: #fff; font-weight: 700; padding: 10px 16px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
.inline-notice { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 12px; border: 1px solid #fde68a; background: #fffbeb; color: #92400e; font-size: 0.92rem; font-weight: 600; margin: 0 0 10px; }
.error-code { margin-top: 8px; font-size: 0.88rem; color: #b91c1c; font-weight: 700; }

.ai-loader { width: 60px; height: 60px; border: 5px solid #e1e8f2; border-top-color: #0055ff; border-radius: 50%; animation: spin 1s linear infinite; }
.loading-percent { margin-top: 12px; font-size: 1.7rem; font-weight: 800; color: #2563eb; }
.loading-track { width: 280px; max-width: 90%; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin: 8px auto 4px; }
.loading-track__bar { height: 100%; background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%); transition: width 0.35s ease; }
@keyframes spin { 100% { transform: rotate(360deg); } }

.result-content { display: flex; flex-direction: column; gap: 24px; }
.result-hero { height: 240px; border-radius: 24px; padding: 30px; display: flex; flex-direction: column; justify-content: flex-end; background-size: cover; background-position: center; color: #fff; position: relative;}
.result-hero__badge { position: absolute; top: 30px; left: 30px; background: #fbbf24; color: #000; font-size: 0.75rem; font-weight: 800; padding: 6px 14px; border-radius: 50px; }
.result-hero__badge--fallback { left: auto; right: 30px; background: #f59e0b; color: #fff; }
.result-hero h2 { font-size: 2.5rem; font-weight: 900; margin: 0 0 10px; color: #ffffff !important; text-shadow: 0 4px 15px rgba(0,0,0,0.8); -webkit-text-stroke: 1px #ffffff;}
.result-hero__meta { display: flex; gap: 16px; font-weight: 500; font-size: 0.95rem; color: #ffffff; }

.timeline-day { background: #ffffff; border-radius: 24px; border: 1px solid #cce0ff; border-left: 5px solid #0060aa; padding: 24px; }
.timeline-day__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.timeline-day__header h3 { font-size: 1.4rem; font-weight: 800; color: #00508b; margin: 0; }
.timeline-list { position: relative; padding-left: 20px; }
.timeline-list::before { content: ''; position: absolute; left: 35px; top: 20px; bottom: 40px; width: 2px; background: #e2eaf5; }
.timeline-item { position: relative; display: flex; align-items: flex-start; gap: 30px; margin-bottom: 30px; }
.timeline-item__icon { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 0.85rem; position: relative; z-index: 2; box-shadow: 0 0 0 6px #ffffff; }
.icon--morning { background: #38bdf8; } .icon--afternoon { background: #94a3b8; } .icon--evening { background: #1e1e2f; }
.timeline-item__content { flex: 1; }
.timeline-item__label { font-size: 0.8rem; font-weight: 800; color: #64748b; margin-bottom: 12px; }
.timeline-card { display: flex; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px; gap: 16px; }
.timeline-card img { width: 90px; height: 90px; border-radius: 12px; object-fit: cover; }
.timeline-card__info { flex: 1; display: flex; flex-direction: column; }
.timeline-card__info h4 { margin: 0 0 6px;  font-size: 1.05rem; font-weight: 800; }
.timeline-card__info p { margin: 0 0 10px; color: #475569; font-size: 0.9rem; line-height: 1.4; }
.timeline-card__meta { display: flex; gap: 12px; margin-top: auto; }
.tag-price, .tag-time { font-size: 0.8rem; font-weight: 700; color: #0369a1; background: #e0f2fe; padding: 4px 10px; border-radius: 6px; }
.tag-time { background: transparent; color: #64748b; padding: 0; }

@media (max-width: 1024px) {
  .api-form-grid,
  .ai-grid {
    grid-template-columns: 1fr;
  }
}
</style>
