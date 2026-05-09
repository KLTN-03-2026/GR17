<template>
  <div class="plan-edit-container">
    <!-- Header/Navigation -->
    <header class="edit-nav-header">
      <div class="nav-left">
        <button class="icon-btn-back" @click="$router.push(`/khach-hang/ke-hoach/${maKeHoach}`)">
          <i class="fas fa-chevron-left"></i>
        </button>
        <h1>Chỉnh sửa kế hoạch</h1>
      </div>
      <div class="nav-right">
        <button class="save-master-btn" @click="guiBieuMau" :disabled="isSubmitting">
          <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
          {{ isSubmitting ? 'Đăng lưu...' : 'Lưu thay đổi' }}
        </button>
      </div>
    </header>

    <main class="edit-content-shell">
      <div v-if="thongBaoLoi" class="notice notice--error">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ thongBaoLoi }}</span>
      </div>
      <div v-if="thongBaoThanhCong" class="notice notice--success">
        <i class="fas fa-circle-check"></i>
        <span>{{ thongBaoThanhCong }}</span>
      </div>
      <div v-if="generationNotice" class="notice notice--info">
        <i class="fas fa-circle-info"></i>
        <span>{{ generationNotice }}</span>
      </div>

      <div v-if="dangTai" class="loading-full-screen">
        <div class="spinner-ui"></div>
        <p>Đang chuẩn bị dữ liệu...</p>
      </div>

      <!-- Main Layout Grid -->
      <div v-else class="content-grid-wrapper">
        <!-- Left Column: Primary Editors -->
        <div class="editor-main-col">
          <!-- SECTION 1: Base Information Form -->
          <section class="edit-section form-card">
            <div class="section-title-row">
              <i class="fas fa-info-circle"></i>
              <h2>Thông tin cơ bản</h2>
            </div>

            <div class="form-grid">
              <label class="field field--full">
                <span>Tên kế hoạch</span>
                <input v-model.trim="form.ten_ke_hoach" type="text" placeholder="Nhập tên kế hoạch du lịch...">
              </label>

              <label class="field">
                <span>Nhóm hành trình</span>
                <select v-model="form.ma_nhom">
                  <option value="">Chọn nhóm hành trình</option>
                  <option v-for="group in groups" :key="group.id" :value="group.id">
                    {{ group.name }}
                  </option>
                </select>
              </label>

              <label class="field">
                <span>Số người</span>
                <input v-model.number="form.so_nguoi" type="number" min="1">
              </label>

              <label class="field">
                <span>Ngày bắt đầu</span>
                <input v-model="form.ngay_bat_dau" type="date">
              </label>

              <label class="field">
                <span>Ngày kết thúc</span>
                <input v-model="form.ngay_ket_thuc" type="date">
              </label>

              <label class="field field--full">
                <span>Ngân sách dự kiến</span>
                <div class="currency-input-wrapper">
                  <input 
                    v-model="displayBudget" 
                    type="text" 
                    placeholder="Ví dụ: 25.000.000"
                    @input="onBudgetInput"
                  >
                  <span class="currency-suffix">VNĐ</span>
                </div>
              </label>
            </div>
          </section>

          <!-- SECTION 2: Itinerary Timeline Editor -->
          <section class="edit-section timeline-editor">
            <div class="section-title-row">
              <i class="fas fa-route"></i>
              <h2>Lịch trình chi tiết</h2>
            </div>

            <div v-if="isTimelineEmpty" class="timeline-empty-actions">
              <div>
                <strong>Chưa có lịch trình cụ thể</strong>
                <p>Tạo lịch trình AI hoặc chọn địa điểm gợi ý để thêm thủ công cho từng ngày.</p>
              </div>
              <button class="generate-ai-btn" @click="taoLichTrinhAi" :disabled="isGeneratingTimeline">
                <i class="fas" :class="isGeneratingTimeline ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
                <span v-if="isGeneratingTimeline">Đang tạo... {{ aiProgressPercent }}%</span>
                <span v-else>Tạo lịch trình AI</span>
              </button>
            </div>
            <div v-if="isGeneratingTimeline" class="ai-progress">
              <div class="ai-progress__meta">
                <span>{{ aiProgressStage }}</span>
                <strong>{{ aiProgressPercent }}%</strong>
              </div>
              <div class="ai-progress__track">
                <div class="ai-progress__bar" :style="{ width: `${aiProgressPercent}%` }"></div>
              </div>
            </div>

            <div class="timeline-itinerary">
              <div class="day-bucket" v-for="(day, dIdx) in displayTimeline" :key="dIdx">
                <div class="day-header-interactive">
                  <div class="day-title-group">
                    <h3>{{ day.title }}</h3>
                    <span class="day-date-text">{{ day.dateFormatted }}</span>
                  </div>
                </div>

                <div class="timeline-track-container">
                  <div class="track-line"></div>

                  <div class="activity-card-ui" v-for="(act, aIdx) in day.activities" :key="aIdx">
                    <div class="marker-dot"></div>
                    <div class="card-body">
                      <div class="drag-handle-ui">
                        <i class="fas fa-grip-vertical"></i>
                      </div>

                      <div class="card-thumb">
                        <img :src="resolveImageUrl(act.locationImage)" alt="Destination">
                      </div>

                      <div class="card-main-info">
                        <h4>{{ act.locationName }}</h4>
                        <p>{{ act.time }} • {{ act.locationAddress }}</p>
                      </div>

                      <div class="card-action-btns" v-if="act.id">
                        <button class="icon-action edit" @click="moModalSua(act)">
                          <i class="fas fa-pen"></i>
                        </button>
                        <button class="icon-action delete" @click="xacNhanXoaActivity(act)">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </div>
                      <span v-else class="ai-suggestion-chip">AI đề xuất</span>
                    </div>
                  </div>

                  <div v-if="day.activities.length === 0" class="empty-day-placeholder">
                    Hãy chọn địa điểm từ Gợi ý ở bên phải để lên lịch cho ngày này.
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Action Strip -->
          <div class="danger-zone-strip">
            <button class="btn-outline-status" @click="toggleStatus" :disabled="isToggling">
              <i class="fas" :class="isToggling ? 'fa-spinner fa-spin' : 'fa-toggle-on'"></i>
              Đổi trạng thái
            </button>
            <button class="btn-outline-danger" @click="deletePlan" :disabled="isDeleting">
              <i class="fas" :class="isDeleting ? 'fa-spinner fa-spin' : 'fa-trash'"></i>
              Xóa kế hoạch
            </button>
          </div>
        </div>

        <!-- Right Column: Sidebar -->
        <aside class="sidebar-section">
          <!-- Cost Card -->
          <div class="card-sidebar card-sidebar--premium shadow-sm">
            <p class="card-sidebar__eyebrow">Tổng chi phí dự kiến</p>
            <h2 class="card-sidebar__price">{{ formattedBudget }}</h2>

            <ul class="cost-summary-list">
              <li v-for="(item, idx) in displayCostBreakdown" :key="idx">
                <span>{{ item.label }}</span>
                <span>{{ item.value }}</span>
              </li>
            </ul>

            <button class="sidebar-action-btn" @click="showBudget = true">Xem chi tiết ngân sách</button>
          </div>

          <!-- REDESIGNED: Suggested Places (MATCHING MOCKUP) -->
          <div class="card-sidebar card-sidebar--white shadow-sm suggestions-container">
            <div class="card-sidebar__header">
              <i class="fas fa-sparkles text-primary"></i>
              <h3 class="suggestion-title">Gợi ý địa điểm</h3>
            </div>

            <div class="suggestion-search-box">
              <i class="fas fa-search search-icon"></i>
              <input
                v-model.trim="suggestionSearch"
                type="text"
                placeholder="Tìm kiếm địa điểm..."
                class="suggestion-search-input"
              />
            </div>
            <label v-if="availablePlanDays.length > 0" class="suggestion-day-picker">
              <span>Thêm vào ngày</span>
              <select v-model="selectedSuggestionDate">
                <option v-for="day in availablePlanDays" :key="day.dateRaw" :value="day.dateRaw">
                  {{ day.title }} - {{ day.dateFormatted }}
                </option>
              </select>
            </label>

            <div class="suggestions-list">
              <div class="suggestion-card" v-for="(place, idx) in filteredSuggestedPlaces" :key="idx">
                <div class="suggestion-thumb">
                  <img :src="resolveImageUrl(place.image)" :alt="place.name" />
                </div>
                <div class="suggestion-body">
                  <h4 class="suggestion-name">{{ place.name }}</h4>
                  <div class="suggestion-rating">
                    <i class="fas fa-star active-star"></i>
                    <span>{{ place.ratingLabel || "Chưa có đánh giá" }}</span>
                  </div>
                  <div class="suggestion-actions">
                    <button class="btn-add-plan" @click="themDiaDiemVaoKeHoach(place)">
                      Thêm vào lịch trình
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="suggestedPlaces.length === 0" class="empty-mini-note">
              Đang tìm kiếm gợi ý tốt nhất...
            </div>
            <div v-else-if="filteredSuggestedPlaces.length === 0" class="empty-mini-note">
              Không tìm thấy địa điểm phù hợp.
            </div>
          </div>

          <!-- Map View -->
          <div class="card-sidebar-map shadow-sm">
            <div class="map-mini-wrapper">
              <ItineraryMiniMap class="map-mini-image" :activities="mapActivities" />
              <div class="map-mini-overlay">
                <span class="route-length-mini">Lộ trình ~{{ totalDistance }} km</span>
                <button class="map-mini-btn" @click="moBanDo" :disabled="!canOpenMap">
                  {{ canOpenMap ? "MỞ BẢN ĐỒ" : "CHƯA CÓ TỌA ĐỘ" }}
                </button>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>

    <!-- Activity Modal -->
    <transition name="fade-scale">
      <div v-if="showModal" class="modal-overlay-premium" @click.self="showModal = false">
        <div class="modal-box-premium">
          <header class="modal-header-premium">
            <div class="header-title">
              <div class="icon-circle"><i class="fas fa-clock"></i></div>
              <h3>{{ isEditingActivity ? 'Chỉnh sửa lịch tham quan' : 'Thêm vào lịch trình' }}</h3>
            </div>
            <button class="btn-close-premium" @click="showModal = false" :disabled="isProcessingActivity"><i
                class="fas fa-times"></i></button>
          </header>

          <div class="modal-body-premium">
            <!-- Selected Destination Card -->
            <div class="selected-dest-card shadow-sm">
              <img :src="resolveImageUrl(activityForm.locationImage)" alt="thumb" />
              <div class="dest-info">
                <h4>{{ activityForm.locationName }}</h4>
                <p><i class="fas fa-map-marker-alt"></i> {{ activityForm.locationAddress }}</p>
              </div>
            </div>

            <!-- Time Inputs Grid -->
            <div class="time-editor-grid">
              <label class="premium-field full-width">
                <span>Ngày tham quan</span>
                <div class="input-with-icon">
                  <i class="far fa-calendar-alt"></i>
                  <input v-model="activityForm.date" type="date" :disabled="isProcessingActivity">
                </div>
              </label>

              <label class="premium-field">
                <span>Giờ bắt đầu</span>
                <div class="input-with-icon">
                  <i class="far fa-clock"></i>
                  <input v-model="activityForm.startTime" type="time" :disabled="isProcessingActivity">
                </div>
              </label>

              <label class="premium-field">
                <span>Giờ kết thúc</span>
                <div class="input-with-icon">
                  <i class="fas fa-stopwatch"></i>
                  <input v-model="activityForm.endTime" type="time" :disabled="isProcessingActivity">
                </div>
              </label>

              <label class="premium-field full-width">
                <span>Ghi chú</span>
                <textarea v-model.trim="activityForm.note" rows="3" placeholder="Ghi chú thêm cho hoạt động này..." :disabled="isProcessingActivity"></textarea>
              </label>
            </div>
          </div>

          <footer class="modal-footer-premium">
            <button class="btn-cancel-premium" @click="showModal = false" :disabled="isProcessingActivity">Hủy
              bỏ</button>
            <button class="btn-save-premium" @click="luuActivity" :disabled="isProcessingActivity">
              <i v-if="isProcessingActivity" class="fas fa-spinner fa-spin"></i> {{ isEditingActivity ? 'Cập nhật thời gian' : 'Thêm điểm đến' }}
            </button>
          </footer>
        </div>
      </div>
    </transition>

    <ItineraryMapModal :show="showMap" @update:show="showMap = $event" :activities="mapActivities" />
    <BudgetDetailModal
      :show="showBudget"
      :plan="budgetPlan"
      :timeline="displayTimeline"
      :budget-rows="budgetDetails"
      @update:show="showBudget = $event"
    />

    <!-- </div> Removed extra div -->
  </div>
</template>

<script>
import { goiApi, API_ORIGIN } from '../../../services/httpClient.js';
import { showConfirm } from "../../../services/appDialog";
import { createToaster } from "@meforma/vue-toaster";
import ItineraryMapModal from "../../Shared/ItineraryMapModal.vue";
import ItineraryMiniMap from "../../Shared/ItineraryMiniMap.vue";
import BudgetDetailModal from "../../Shared/BudgetDetailModal.vue";

const toaster = createToaster({ position: "top-right" });
import {
  API_BASE,
  buildHeaders,
  getStoredCustomerId,
  mapGroupsFromMembership,
  mapPlan,
  mapPlanToForm,
  normalizeRecord,
  validatePlanForm,
  buildPlanTimeline,
  buildPlanMapActivities,
  buildBudgetDetails
} from "./planShared";

export default {
  name: "ChinhSuaKeHoach",
  components: {
    ItineraryMapModal,
    ItineraryMiniMap,
    BudgetDetailModal,
  },
  data() {
    return {
      dangTai: false,
      isSubmitting: false,
      isDeleting: false,
      isToggling: false,
      isGeneratingTimeline: false,
      aiProgressPercent: 0,
      aiProgressStage: "",
      aiProgressTimer: null,
      showMap: false,
      showBudget: false,
      thongBaoLoi: "",
      thongBaoThanhCong: "",
      generationNotice: "",
      maKhachHang: "",
      plan: null,
      form: {
        ma_ke_hoach: "",
        ma_nhom: "",
        ten_ke_hoach: "",
        so_nguoi: 1,
        ngay_bat_dau: "",
        ngay_ket_thuc: "",
        ngan_sach_du_kien: 0,
        trang_thai: 1
      },
      errors: {},
      groups: [],

      // Timeline Section
      activities: [], // Managed locally
      showModal: false,
      isProcessingActivity: false,
      isEditingActivity: false,
      activityForm: {
        id: null,
        locationId: null,
        date: "",
        locationName: "",
        locationAddress: "",
        startTime: "08:00",
        endTime: "10:00",
        locationImage: "",
        note: ""
      },

      // Sidebar Suggestions
      suggestedPlaces: [],
      suggestionSearch: "",
      selectedSuggestionDate: "",
      displayBudget: ""
    };
  },
  watch: {
    "form.ngan_sach_du_kien"(newVal) {
      this.syncDisplayBudget(newVal);
    },
    "form.ngay_bat_dau"() {
      this.syncSelectedSuggestionDate();
    },
    "form.ngay_ket_thuc"() {
      this.syncSelectedSuggestionDate();
    }
  },
  computed: {
    maKeHoach() {
      return String(this.$route.params.id || "");
    },
    formattedBudget() {
      const val = Number(this.form.ngan_sach_du_kien || 0);
      return val.toLocaleString('vi-VN') + " VNĐ";
    },
    budgetPlan() {
      return {
        ...(this.plan || {}),
        budget: Number(this.form.ngan_sach_du_kien || 0),
        budgetLabel: this.formattedBudget,
        soNguoi: Number(this.form.so_nguoi || 0),
        aiData: this.plan?.aiData || {},
      };
    },
    displayTimeline() {
      const basePlan = {
        ...(this.plan || {}),
        startDate: this.form.ngay_bat_dau,
        endDate: this.form.ngay_ket_thuc,
        activities: this.activities,
      };
      return buildPlanTimeline(basePlan);
    },
    isTimelineEmpty() {
      const timeline = this.displayTimeline;
      return timeline.length > 0 && timeline.every(day => day.activities.length === 0);
    },
    mapActivities() {
      return buildPlanMapActivities(this.displayTimeline);
    },
    canOpenMap() {
      return this.mapActivities.length > 0;
    },
    totalDistance() {
      const mapActivities = this.mapActivities;
      if (mapActivities.length < 2) return "0.0";

      let total = 0;
      for (let index = 0; index < mapActivities.length - 1; index += 1) {
        const current = mapActivities[index].coordinates;
        const next = mapActivities[index + 1].coordinates;
        total += this.tinhKhoangCach(current.lat, current.lng, next.lat, next.lng);
      }

      return total.toFixed(1);
    },
    availablePlanDays() {
      return this.displayTimeline
        .filter((day) => day.dateRaw)
        .map((day) => ({
          dateRaw: day.dateRaw,
          title: day.title,
          dateFormatted: day.dateFormatted || this.formatDateSimple(day.dateRaw),
        }));
    },
    filteredSuggestedPlaces() {
      const query = this.suggestionSearch.toLowerCase();
      if (!query) return this.suggestedPlaces;

      return this.suggestedPlaces.filter((place) => {
        const haystack = `${place.name || ""} ${place.address || ""} ${place.reason || ""}`.toLowerCase();
        return haystack.includes(query);
      });
    },
    budgetDetails() {
      return buildBudgetDetails(this.budgetPlan, this.displayTimeline);
    },
    displayCostBreakdown() {
      return this.budgetDetails.rows.slice(0, 3);
    }
  },
  beforeUnmount() {
    this.cleanupAiProgress();
  },
  methods: {
    syncSelectedSuggestionDate() {
      const days = this.availablePlanDays;
      if (days.some((day) => day.dateRaw === this.selectedSuggestionDate)) return;
      this.selectedSuggestionDate = days[0]?.dateRaw || "";
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
    tinhKhoangCach(lat1, lon1, lat2, lon2) {
      if ([lat1, lon1, lat2, lon2].some(value => !Number.isFinite(Number(value)))) return 0;
      const R = 6371;
      const dLat = (Number(lat2) - Number(lat1)) * Math.PI / 180;
      const dLon = (Number(lon2) - Number(lon1)) * Math.PI / 180;
      const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(Number(lat1) * Math.PI / 180) * Math.cos(Number(lat2) * Math.PI / 180) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    },
    async taoLichTrinhAi() {
      if (this.isGeneratingTimeline) return;
      const replaceExisting = this.activities.length > 0;
      if (replaceExisting) {
        const confirmed = await showConfirm({
          title: "Tạo lại lịch trình",
          message: "Kế hoạch đã có hoạt động. Tạo lại bằng AI sẽ thay thế lịch trình hiện tại.",
          tone: "warning"
        });
        if (!confirmed) return;
      }

      this.isGeneratingTimeline = true;
      this.generationNotice = "";
      this.startAiProgress();
      try {
        const res = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}/lich-trinh-ai`, {
          method: "POST",
          headers: buildHeaders(true),
          body: JSON.stringify({ replace_existing: replaceExisting }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
          throw new Error(data.message || "Không thể tạo lịch trình AI.");
        }
        const generationMode = data?.data?.generation_mode;
        const notice = data?.data?.notice || "";
        if (generationMode === "fallback") {
          this.generationNotice = notice || "AI đang bận, hệ thống đã tạo bản nháp để bạn chỉnh tiếp.";
          toaster.info(this.generationNotice);
        } else {
          toaster.success(data.message || "Đã tạo lịch trình AI cho kế hoạch.");
        }
        this.completeAiProgress();
        await this.taiThongTinKeHoach();
        await this.taiGoiYDiaDiem();
        window.setTimeout(() => this.cleanupAiProgress(), 500);
      } catch (error) {
        this.cleanupAiProgress();
        toaster.error(error.message || "Không thể tạo lịch trình AI.");
      } finally {
        this.isGeneratingTimeline = false;
      }
    },
    syncDisplayBudget(val) {
      if (val === "" || val === null || val === undefined) {
        this.displayBudget = "";
        return;
      }
      const num = parseInt(String(val).replace(/\D/g, ""), 10);
      if (isNaN(num)) {
        this.displayBudget = "";
      } else {
        this.displayBudget = num.toLocaleString("vi-VN");
      }
    },
    onBudgetInput(e) {
      const rawValue = e.target.value;
      const numericValue = rawValue.replace(/\D/g, "");
      this.form.ngan_sach_du_kien = numericValue;
      this.syncDisplayBudget(numericValue);
    },
    formatDateSimple(dateStr) {
      if (!dateStr || dateStr === "Khác") return "";
      const d = new Date(dateStr);
      return d.toLocaleDateString("vi-VN", { day: "2-digit", month: "2-digit", year: "numeric" });
    },
    resolveImageUrl(value) {
      const fallback = "https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=400";
      const raw = String(value || "").trim();
      if (!raw || raw === "null") return fallback;
      if (/^https?:\/\//i.test(raw)) return raw;
      return `${API_ORIGIN}/${raw.replace(/^\/+/, "")}`;
    },

    // --- Data Fetching ---
    async bootstrap() {
      this.dangTai = true;
      this.thongBaoLoi = "";
      try {
        this.maKhachHang = getStoredCustomerId();

        // 1. Groups
        const nhomRes = await goiApi(`${API_BASE}/thanh-vien-nhom/search?Ma_khach_hang=${encodeURIComponent(this.maKhachHang)}`, { headers: buildHeaders() });
        if (nhomRes.ok) {
          const nhomData = await nhomRes.json();
          this.groups = mapGroupsFromMembership(nhomData);
        }

        // 2. Plan
        await this.taiThongTinKeHoach();

        // 3. Suggestions
        this.taiGoiYDiaDiem();
      } catch (error) { this.thongBaoLoi = error.message; }
      finally { this.dangTai = false; }
    },

    async taiThongTinKeHoach() {
      const phanHoi = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}`, { headers: buildHeaders() });
      const duLieu = await phanHoi.json().catch(() => ({}));
      if (!phanHoi.ok) throw new Error(duLieu.message || "Không thể tải kế hoạch.");
      const plan = mapPlan(normalizeRecord(duLieu));
      this.plan = plan;
      this.form = mapPlanToForm(plan);
      this.activities = plan.activities || [];
      if (!this.selectedSuggestionDate) {
        this.selectedSuggestionDate = this.form.ngay_bat_dau || this.availablePlanDays[0]?.dateRaw || "";
      }
    },

    async taiGoiYDiaDiem() {
      try {
        const res = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}/goi-y-lan-can?radius=15`, { headers: buildHeaders() });
        if (res.ok) {
          const data = await res.json();
          const list = Array.isArray(data) ? data : (data.data || []);
          this.suggestedPlaces = list.slice(0, 8).map(item => {
            const rating = item.rating ?? item.danh_gia_trung_binh ?? item.avg_rating ?? null;
            const reviewCount = item.review_count ?? item.so_luot_danh_gia ?? null;
            const ratingLabel = Number.isFinite(Number(rating))
              ? `${Number(rating).toFixed(1)}${reviewCount ? ` (${reviewCount})` : ""}`
              : "";

            return {
              id: item.ma_dia_diem || item.id,
              name: item.ten_dia_diem || "Địa danh du lịch",
              image: item.hinh_anh || item.image || "",
              address: item.dia_chi || item.location || "",
              reason: item.reason || item.ly_do || item.mo_ta_ngan || "",
              ratingLabel,
            };
          });
        }
      } catch (e) { console.error("Lỗi tải gợi ý:", e); }
    },

    // --- Interaction ---
    themDiaDiemVaoKeHoach(place) {
      if (this.isProcessingActivity) return;
      this.isEditingActivity = false;
      this.activityForm = {
        id: null,
        locationId: place.id,
        date: this.selectedSuggestionDate || this.form.ngay_bat_dau,
        locationName: place.name,
        locationAddress: place.address,
        locationImage: place.image,
        startTime: "08:00",
        endTime: "10:00",
        note: ""
      };
      this.showModal = true;
    },

    moModalSua(act) {
      this.isEditingActivity = true;
      this.activityForm = {
        id: act.id,
        locationId: act.locationId,
        date: act.date,
        locationName: act.locationName || act.name,
        locationAddress: act.locationAddress || "",
        locationImage: act.locationImage || act.image || "",
        startTime: act.startTime || "08:00",
        endTime: act.endTime || "10:00",
        note: act.note || act.description || ""
      };
      this.showModal = true;
    },
    async luuActivity() {
      this.isProcessingActivity = true;
      try {
        if (this.isEditingActivity) {
          const payload = {
            gio_bat_dau: String(this.activityForm.startTime).substring(0, 5),
            gio_ket_thuc: String(this.activityForm.endTime).substring(0, 5),
            ngay_cu_the: this.activityForm.date || this.form.ngay_bat_dau,
            ghi_chu: this.activityForm.note || ""
          };
          const res = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}/hoat-dong/${encodeURIComponent(this.activityForm.id)}`, {
            method: 'PUT',
            headers: buildHeaders(true),
            body: JSON.stringify(payload)
          });
          if (!res.ok) {
            const result = await res.json().catch(() => ({}));
            throw new Error(result.message || "Không thể cập nhật thời gian");
          }
          this.thongBaoThanhCong = "Đã cập nhật thời gian.";
        } else {
          const payload = {
            ma_dia_diem: this.activityForm.locationId,
            gio_bat_dau: String(this.activityForm.startTime).substring(0, 5),
            gio_ket_thuc: String(this.activityForm.endTime).substring(0, 5),
            ngay_cu_the: this.activityForm.date || this.form.ngay_bat_dau,
            ghi_chu: this.activityForm.note || ""
          };
          const res = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}/hoat-dong`, {
            method: 'POST',
            headers: buildHeaders(true),
            body: JSON.stringify(payload)
          });
          if (!res.ok) {
            const result = await res.json().catch(() => ({}));
            throw new Error(result.message || "Không thể thêm địa điểm");
          }
          toaster.success(`Đã thêm ${this.activityForm.locationName} vào lịch trình.`);
        }
        
        this.showModal = false;
        await this.taiThongTinKeHoach();
      } catch (e) {
        toaster.error("Lỗi: " + e.message);
      } finally {
        this.isProcessingActivity = false;
      }
    },
    async xacNhanXoaActivity(act) {
      const isConfirmed = await showConfirm({
        title: "Xác nhận xóa",
        message: `Xóa "${act.locationName}" khỏi lịch trình?`,
        tone: "warning"
      });
      if (isConfirmed) {
        try {
          const res = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(this.maKeHoach)}/hoat-dong/${encodeURIComponent(act.id)}`, {
            method: 'DELETE',
            headers: buildHeaders()
          });
          if (!res.ok) throw new Error("Không thể xóa");
          await this.taiThongTinKeHoach();
        } catch (e) {
          toaster.error('Lỗi khi xóa: ' + e.message);
        }
      }
    },

    // --- Master Save ---
    async guiBieuMau() {
      this.isSubmitting = true;
      try {
        const payload = {
          ma_khach_hang: this.maKhachHang,
          ma_nhom: this.form.ma_nhom,
          ten_ke_hoach: this.form.ten_ke_hoach,
          so_nguoi: Number(this.form.so_nguoi),
          ngay_bat_dau: this.form.ngay_bat_dau,
          ngay_ket_thuc: this.form.ngay_ket_thuc,
          ngan_sach_du_kien: Number(this.form.ngan_sach_du_kien),
          trang_thai: Number(this.form.trang_thai)
        };
        const res = await goiApi(`${API_BASE}/ke-hoach/${this.maKeHoach}`, { method: "PUT", headers: buildHeaders(true), body: JSON.stringify(payload) });
        if (res.ok) {
          toaster.success("Cập nhật thông tin kế hoạch thành công!");
        } else {
          throw new Error("Không thể lưu thông tin.");
        }
      } catch (err) { this.thongBaoLoi = err.message; }
      finally { this.isSubmitting = false; }
    },
    async toggleStatus() {
      this.isToggling = true;
      try {
        const res = await goiApi(`${API_BASE}/ke-hoach/${this.maKeHoach}/status`, { method: "PATCH", headers: buildHeaders() });
        if (res.ok) { this.form.trang_thai = this.form.trang_thai === 1 ? 0 : 1; toaster.success("Đã đổi trạng thái."); }
      } catch (e) { this.thongBaoLoi = e.message; }
      finally { this.isToggling = false; }
    },
    async deletePlan() {
      const isConfirmed = await showConfirm({
        title: "Xác nhận xóa kế hoạch",
        message: `Hành động này sẽ xóa vĩnh viễn kế hoạch "${this.form.ten_ke_hoach}". Bạn có chắc chắn không?`,
        tone: "warning"
      });
      if (!isConfirmed) return;

      this.isDeleting = true;
      try {
        const res = await goiApi(`${API_BASE}/ke-hoach/${this.maKeHoach}`, { method: "DELETE", headers: buildHeaders() });
        if (res.ok) {
          toaster.success("Đã xóa kế hoạch thành công!");
          this.$router.push("/khach-hang/ke-hoach");
        }
      } catch (e) { this.thongBaoLoi = e.message; }
      finally { this.isDeleting = false; }
    }
  },
  mounted() { this.bootstrap(); }
};
</script>

<style scoped>
.plan-edit-container {
  background-color: #F8FAFD;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
  color: #111827;
  padding-bottom: 64px;
}


.edit-nav-header {
  position: sticky;
  top: 0;
  background: #ffffff;
  padding: 16px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 100;
  border-bottom: 1px solid #E5E7EB;
}

.nav-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.nav-left h1 {
  font-size: 1.15rem;
  font-weight: 800;
  margin: 0;
  color: #111827;
}

.icon-btn-back {
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #4B5563;
  cursor: pointer;
}

.save-master-btn {
  background: #00476B;
  color: white;
  padding: 10px 24px;
  border-radius: 99px;
  font-weight: 800;
  border: none;
  cursor: pointer;
  transition: transform 0.2s;
}

.save-master-btn:hover {
  transform: scale(1.05);
}

.edit-content-shell {
  width: min(1200px, calc(100% - 40px));
  margin: 32px auto;
}

.content-grid-wrapper {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 32px;
  align-items: start;
}

.edit-section {
  background: #ffffff;
  border-radius: 28px;
  padding: 32px;
  margin-bottom: 32px;
  box-shadow: 0 10px 40px rgba(15, 23, 42, 0.04);
  border: 1px solid #E5EBF4;
}

.section-title-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
  color: #00476B;
}

.section-title-row i {
  font-size: 1.4rem;
}

.section-title-row h2 {
  font-size: 1.25rem;
  font-weight: 900;
  margin: 0;
  color: #111827;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.field--full {
  grid-column: 1 / -1;
}

.field span {
  display: block;
  font-size: 0.85rem;
  font-weight: 700;
  color: #64748B;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.field input,
.field select {
  width: 100%;
  min-height: 52px;
  padding: 0 16px;
  background: #F8FAFF;
  border: 1px solid #DBE4F0;
  border-radius: 14px;
  outline: none;
}

.field input:focus,
.field select:focus {
  border-color: #00476B;
  background: #fff;
  box-shadow: 0 0 0 4px rgba(0, 71, 107, 0.08);
}

.currency-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.currency-input-wrapper input {
  flex: 1;
  padding-right: 60px !important;
}

.currency-suffix {
  position: absolute;
  right: 16px;
  color: #64748b;
  font-weight: 700;
  pointer-events: none;
}

/* Timeline Editor */
.timeline-empty-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  margin-bottom: 28px;
  padding: 18px 20px;
  background: #F8FAFC;
  border: 1px dashed #CBD5E1;
  border-radius: 18px;
}

.timeline-empty-actions strong {
  display: block;
  margin-bottom: 4px;
  color: #0F172A;
}

.timeline-empty-actions p {
  margin: 0;
  color: #64748B;
  font-weight: 600;
}

.generate-ai-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  min-width: 170px;
  padding: 10px 16px;
  background: #00476B;
  color: #FFFFFF;
  border: none;
  border-radius: 999px;
  font-weight: 800;
  cursor: pointer;
}

.generate-ai-btn:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.ai-progress {
  margin: -12px 0 28px;
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

.timeline-itinerary {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.day-header-interactive {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 24px;
}

.day-title-group h3 {
  font-size: 1.8rem;
  font-weight: 900;
  color: #00476B;
  margin: 0;
}

.day-date-text {
  color: #94A3B8;
  font-weight: 600;
  font-size: 0.95rem;
}

.text-btn-add {
  background: none;
  border: none;
  color: #00476B;
  font-weight: 800;
  font-size: 0.9rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
}

.timeline-track-container {
  position: relative;
  padding-left: 48px;
}

.track-line {
  position: absolute;
  left: 10px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #EAF4FD;
}

.activity-card-ui {
  position: relative;
  margin-bottom: 20px;
}

.marker-dot {
  position: absolute;
  left: -43px;
  top: 22px;
  width: 12px;
  height: 12px;
  background: #00476B;
  border-radius: 50%;
  border: 3px solid #fff;
  z-index: 2;
}

.card-body {
  background: #ffffff;
  border: 1px solid #F1F5F9;
  border-radius: 20px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}

.card-thumb img {
  width: 64px;
  height: 64px;
  border-radius: 14px;
  object-fit: cover;
}

.card-main-info {
  flex: 1;
}

.card-main-info h4 {
  font-size: 1.05rem;
  font-weight: 800;
  margin: 0 0 4px;
  color: #1E293B;
}

.card-main-info p {
  margin: 0;
  font-size: 0.85rem;
  color: #64748B;
  font-weight: 600;
}

.card-action-btns {
  display: flex;
  gap: 8px;
}

.icon-action {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #F8FAFC;
  border: none;
  color: #64748B;
  cursor: pointer;
}

.icon-action:hover {
  background: #F1F5F9;
  color: #1E293B;
}

.ai-suggestion-chip {
  padding: 6px 10px;
  background: #EEF2FF;
  color: #3730A3;
  border-radius: 999px;
  font-size: 0.78rem;
  font-weight: 800;
  white-space: nowrap;
}

/* SIDEBAR & REDESIGNED SUGGESTIONS */
.sidebar-section {
  display: flex;
  flex-direction: column;
  gap: 24px;
  position: sticky;
  top: 100px;
}

.card-sidebar {
  border-radius: 28px;
  overflow: hidden;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.04);
}

.card-sidebar--premium {
  background: #114E73;
  color: white;
  padding: 32px;
}

.card-sidebar__price {
  font-size: 2rem;
  font-weight: 900;
  margin-bottom: 28px;
  color: white;
}

.cost-summary-list {
  list-style: none;
  padding: 0;
  margin-bottom: 32px;
}

.cost-summary-list li {
  display: flex;
  justify-content: space-between;
  padding-bottom: 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  margin-bottom: 16px;
}

.sidebar-action-btn {
  width: 100%;
  background: white;
  color: #114E73;
  padding: 14px;
  border-radius: 12px;
  font-weight: 800;
  border: none;
  cursor: pointer;
}

/* Mockup Matching Suggestions */
.suggestions-container {
  background: #ffffff;
  padding: 24px;
  border: 1px solid #F1F5F9;
}

.card-sidebar__header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.suggestion-title {
  margin: 0;
  font-size: 1.15rem;
  font-weight: 900;
  color: #1E293B;
}

.suggestion-search-box {
  position: relative;
  margin-bottom: 12px;
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94A3B8;
  font-size: 0.9rem;
}

.suggestion-search-input {
  width: 100%;
  padding: 10px 14px 10px 40px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 99px;
  font-size: 0.9rem;
  outline: none;
  transition: all 0.2s;
  box-sizing: border-box;
}

.suggestion-search-input:focus {
  background: #ffffff;
  border-color: #00476B;
  box-shadow: 0 0 0 3px rgba(0, 71, 107, 0.08);
}

.suggestion-day-picker {
  display: grid;
  gap: 7px;
  margin-bottom: 18px;
  color: #64748b;
  font-size: 0.78rem;
  font-weight: 900;
  text-transform: uppercase;
}

.suggestion-day-picker select {
  width: 100%;
  min-height: 42px;
  padding: 0 13px;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  background: #ffffff;
  color: #0f172a;
  font-size: 0.9rem;
  font-weight: 700;
}

.suggestions-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.suggestion-card {
  background: #ffffff;
  border: 1px solid #F1F5F9;
  border-radius: 24px;
  padding: 12px;
  display: flex;
  gap: 16px;
  align-items: center;
  transition: all 0.2s;
}

.suggestion-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
}

.suggestion-thumb {
  width: 80px;
  height: 80px;
  flex-shrink: 0;
}

.suggestion-thumb img {
  width: 100%;
  height: 100%;
  border-radius: 18px;
  object-fit: cover;
}

.suggestion-body {
  flex: 1;
}

.suggestion-name {
  margin: 0 0 6px;
  font-size: 1.05rem;
  font-weight: 800;
  color: #1E293B;
}

.suggestion-rating {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 12px;
  font-size: 0.85rem;
  font-weight: 700;
  color: #64748B;
}

.active-star {
  color: #8F6A24;
  font-size: 0.75rem;
}

.suggestion-actions {
  display: flex;
  justify-content: flex-end;
}

.btn-add-plan {
  background: #E8F2FB;
  color: #114E73;
  border: none;
  padding: 8px 18px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-add-plan:hover {
  background: #114E73;
  color: white;
}

/* Actions */
.danger-zone-strip {
  display: flex;
  gap: 16px;
  margin-top: 16px;
}

.btn-outline-status,
.btn-outline-danger {
  flex: 1;
  min-height: 52px;
  border-radius: 14px;
  font-weight: 800;
  border: 1px solid #DBE4F0;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
}

.btn-outline-danger {
  border-color: #FFE4E6;
  color: #E11D48;
}

.card-sidebar-map {
  overflow: hidden;
  border-radius: 24px;
  background: #ffffff;
}

.map-mini-wrapper {
  position: relative;
  height: 300px;
  overflow: hidden;
}

.map-mini-image {
  display: block;
  width: 100%;
  height: 100%;
}

.map-mini-overlay {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(10px);
}

.route-length-mini {
  color: #111827;
  font-weight: 800;
  font-size: 0.9rem;
}

.map-mini-btn {
  border: 0;
  background: #f8fafc;
  color: #114E73;
  padding: 10px 14px;
  border-radius: 12px;
  font-size: 0.78rem;
  font-weight: 900;
  cursor: pointer;
  white-space: nowrap;
}

.map-mini-btn:disabled {
  color: #64748b;
  cursor: not-allowed;
}

/* Premium Modal Styles */
.modal-overlay-premium {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(12px);
  z-index: 9999;
  display: grid;
  place-items: center;
  padding: 20px;
}

.modal-box-premium {
  background: #ffffff;
  width: min(460px, 100%);
  border-radius: 32px;
  padding: 32px;
  box-shadow: 0 40px 80px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.modal-header-premium {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 14px;
}

.header-title .icon-circle {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #E8F2FB;
  color: #00476B;
  display: grid;
  place-items: center;
  font-size: 1.1rem;
}

.header-title h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 900;
  color: #1E293B;
}

.btn-close-premium {
  background: #F1F5F9;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  color: #64748B;
  cursor: pointer;
  transition: all 0.2s;
  display: grid;
  place-items: center;
}

.btn-close-premium:hover {
  background: #E2E8F0;
  color: #1E293B;
}

.selected-dest-card {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  padding: 12px;
  display: flex;
  gap: 16px;
  align-items: center;
}

.selected-dest-card img {
  width: 64px;
  height: 64px;
  border-radius: 14px;
  object-fit: cover;
}

.dest-info h4 {
  margin: 0 0 6px;
  font-size: 1.05rem;
  font-weight: 800;
  color: #1E293B;
}

.dest-info p {
  margin: 0;
  font-size: 0.85rem;
  color: #64748B;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
}

.time-editor-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-top: 10px;
}

.premium-field.full-width {
  grid-column: 1 / -1;
}

.premium-field span {
  display: block;
  font-size: 0.8rem;
  font-weight: 700;
  color: #64748B;
  text-transform: uppercase;
  margin-bottom: 8px;
  letter-spacing: 0.05em;
}

.input-with-icon {
  position: relative;
}

.input-with-icon i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #00476B;
  font-size: 1.1rem;
}

.input-with-icon input {
  width: 100%;
  padding: 14px 16px 14px 46px;
  background: #ffffff;
  border: 2px solid #E2E8F0;
  border-radius: 16px;
  font-size: 1rem;
  font-weight: 600;
  color: #1E293B;
  outline: none;
  transition: all 0.2s;
  box-sizing: border-box;
}

.input-with-icon input:focus {
  border-color: #00476B;
  box-shadow: 0 4px 12px rgba(0, 71, 107, 0.1);
}

.premium-field textarea {
  width: 100%;
  resize: vertical;
  min-height: 86px;
  padding: 14px 16px;
  background: #ffffff;
  border: 2px solid #E2E8F0;
  border-radius: 16px;
  font-size: 1rem;
  font-weight: 600;
  color: #1E293B;
  outline: none;
  box-sizing: border-box;
}

.premium-field textarea:focus {
  border-color: #00476B;
  box-shadow: 0 4px 12px rgba(0, 71, 107, 0.1);
}

.modal-footer-premium {
  display: flex;
  gap: 12px;
}

.btn-cancel-premium {
  flex: 1;
  padding: 16px;
  border-radius: 16px;
  font-weight: 800;
  font-size: 1rem;
  background: #F1F5F9;
  color: #475569;
  border: none;
  cursor: pointer;
  transition: 0.2s;
}

.btn-cancel-premium:hover {
  background: #E2E8F0;
}

.btn-save-premium {
  flex: 2;
  padding: 16px;
  border-radius: 16px;
  font-weight: 800;
  font-size: 1rem;
  background: #00476B;
  color: #ffffff;
  border: none;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  transition: 0.2s;
}

.btn-save-premium:hover {
  background: #003755;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 71, 107, 0.2);
}

/* Animation */
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.3s ease;
}

.fade-scale-enter,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Notices */
.notice {
  padding: 16px;
  border-radius: 16px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
}

.notice--error {
  background: #FEF2F2;
  color: #DC2626;
}

.notice--success {
  background: #F0FDF4;
  color: #16A34A;
}

.notice--info {
  background: #EFF6FF;
  color: #1D4ED8;
}

@media (max-width: 1120px) {
  .content-grid-wrapper {
    grid-template-columns: 1fr;
  }

  .sidebar-section {
    position: static;
  }
}
</style>
