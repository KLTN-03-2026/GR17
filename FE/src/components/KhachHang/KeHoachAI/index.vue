<template>
  <div class="detail-page">
    <div class="detail-layout">
      <CustomerSidebar />

      <!-- NỘI DUNG CHÍNH AI PLANNER -->
      <section class="ai-content">
        <div class="page-header">
          <h1>Lên Kế Hoạch Bằng AI</h1>
          <p>Khám phá hành trình được thiết kế riêng cho bạn chỉ trong vài giây với công nghệ trí tuệ nhân tạo tiên
            tiến.</p>
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

              <div class="form-group">
                <label>Ngày bắt đầu</label>
                <div class="input-with-icon">
                  <i class="far fa-calendar-check"></i>
                  <input type="date" v-model="form.ngayBatDau" :min="today" />
                </div>
              </div>
              <div class="form-group">
                <label>Ngày kết thúc</label>
                <div class="input-with-icon">
                  <i class="fas fa-calendar-day"></i>
                  <input type="date" v-model="form.ngayKetThuc" :min="form.ngayBatDau || today" />
                </div>
              </div>

              <div class="form-group">
                <label>Ngân sách</label>
                <div class="input-with-icon">
                  <i class="fas fa-money-bill-wave"></i>
                  <input type="text" :value="formatBudgetDisplay" @input="updateBudget" placeholder="Nhập số tiền" />
                </div>
              </div>

              <div class="form-group">
                <label>Sở thích</label>
                <div class="preferences-grid">
                  <button v-for="interest in danhSachSoThich" :key="interest" type="button" class="pref-checkbox"
                    :class="{ active: form.soThich.includes(interest) }" @click="chuyenSoThich(interest)">
                    <span>{{ interest }}</span>
                  </button>
                </div>
              </div>
              <div class="form-group">
                <label>Số người</label>
                <div class="input-with-icon">
                  <i class="fas fa-user"></i>
                  <input type="number" v-model="form.soNguoi" :min="AI_MIN_PEOPLE" :max="AI_MAX_PEOPLE" />
                </div>
              </div>
              <div class="form-group">
                <label>Mô tả chuyến đi </label>
                <div class="input-with-icon">
                  <i class="fas fa-comment-dots"></i>
                  <input type="text" v-model="form.moTaChuyenDi" placeholder="VD: Muốn đi ăn nhiều đồ biển..." />
                </div>
              </div>
              <button v-if="step === 1" class="btn-generate" @click="layDeXuatDiaDiem"
                :disabled="dangXuLy || !form.diemDen">
                <i class="fas fa-magic" v-if="!dangXuLy"></i>
                <i class="fas fa-spinner fa-spin" v-else></i>
                {{ dangXuLy ? 'Đang tìm địa điểm...' : 'Đề Xuất Địa Điểm' }}
              </button>

              <div v-if="step === 2 || step === 3" class="selected-locations-summary">
                <p>Bạn đã chọn <strong>{{ selectedLocations.length }}</strong> địa điểm</p>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                  <button class="btn-back" @click="step === 2 ? step = 1 : step = 2"
                    :disabled="dangXuLy || dangTimTour">Quay lại</button>
                  <button v-if="step === 2" class="btn-generate btn-generate--step2" @click="xacNhanLuaChon"
                    :disabled="selectedLocations.length === 0 || dangTimTour">
                    <i class="fas fa-spinner fa-spin" v-if="dangTimTour"></i>
                    <i class="fas fa-check-circle" v-else></i>
                    {{ dangTimTour ? 'Đang lọc tour...' : 'Xác nhận lựa chọn' }}
                  </button>
                  <button v-if="step === 3" class="btn-generate btn-generate--step2" @click="taoHanhTrinh"
                    :disabled="dangXuLy">
                    <i class="fas fa-route" v-if="!dangXuLy"></i>
                    <i class="fas fa-spinner fa-spin" v-else></i>
                    {{ dangXuLy ? 'Đang tạo lịch trình...' : 'Tạo hành trình AI' }}
                  </button>
                </div>
              </div>
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
              <p>Hệ thống AI đang tổng hợp dữ liệu du lịch liên quan đến <strong>{{ form.diemDen }}</strong> để tạo ra
                lộ trình hoàn hảo nhất theo yêu cầu ngân sách {{ form.nganSach }}.</p>
            </div>

            <div class="state-box state-box--error" v-else-if="aiErrorMessage">
              <i class="fas fa-triangle-exclamation text-icon text-icon--error"></i>
              <h3>Không thể tạo lịch trình AI</h3>
              <p>{{ aiErrorMessage }}</p>
              <p class="error-code" v-if="aiErrorCode">Mã lỗi: {{ aiErrorCode }}</p>
              <button class="btn-retry" v-if="retryable" @click="retryAction">
                <i class="fas fa-rotate-right"></i>
                Thử lại
              </button>
            </div>

            <!-- TRẠNG THÁI CHỌN ĐỊA ĐIỂM -->
            <div class="state-box state-box--suggestions"
              v-else-if="step === 2 && !ketQua && (danhSachKhachSan.length > 0 || danhSachThamQuan.length > 0 || danhSachNhaHang.length > 0)">
              <h3>Gợi ý địa điểm cho bạn</h3>
              <p>Vui lòng chọn các địa điểm bạn muốn thêm vào lịch trình (Nhấn "Tạo Tự Động" để có thể bỏ qua):</p>

              <!-- 1. Khách sạn -->
              <div class="suggestion-section" v-if="danhSachKhachSan.length > 0">
                <h4 class="suggestion-section__title"><i class="fas fa-hotel"></i> Khách sạn / Chỗ nghỉ</h4>
                <div class="suggestions-grid">
                  <div v-for="(loc, idx) in danhSachKhachSan" :key="'ks' + idx" class="suggestion-card"
                    :class="{ selected: selectedLocations.includes(loc.ten_dia_diem) }"
                    @click="toggleLocation(loc.ten_dia_diem)">
                    <div class="suggestion-card__img">
                      <img :src="getCardImage(loc)" :alt="loc.ten_dia_diem"
                        @error="$event.target.src = fallbackActivityImage" />
                    </div>
                    <div class="suggestion-card__info">
                      <h4>{{ loc.ten_dia_diem }}</h4>
                      <p>{{ loc.mo_ta_ngan }}</p>
                      <span class="suggestion-card__addr"><i class="fas fa-location-dot"></i> {{ loc.dia_chi }}</span>
                    </div>
                    <div class="suggestion-card__check">
                      <i class="fas fa-check"></i>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 2. Tham quan -->
              <div class="suggestion-section" v-if="danhSachThamQuan.length > 0">
                <h4 class="suggestion-section__title"><i class="fas fa-map-signs"></i> Địa điểm tham quan</h4>
                <div class="suggestions-grid">
                  <div v-for="(loc, idx) in danhSachThamQuan" :key="'tq' + idx" class="suggestion-card"
                    :class="{ selected: selectedLocations.includes(loc.ten_dia_diem) }"
                    @click="toggleLocation(loc.ten_dia_diem)">
                    <div class="suggestion-card__img">
                      <img :src="getCardImage(loc)" :alt="loc.ten_dia_diem"
                        @error="$event.target.src = fallbackActivityImage" />
                    </div>
                    <div class="suggestion-card__info">
                      <h4>{{ loc.ten_dia_diem }}</h4>
                      <p>{{ loc.mo_ta_ngan }}</p>
                      <span class="suggestion-card__addr"><i class="fas fa-location-dot"></i> {{ loc.dia_chi }}</span>
                    </div>
                    <div class="suggestion-card__check">
                      <i class="fas fa-check"></i>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 3. Nhà hàng -->
              <div class="suggestion-section" v-if="danhSachNhaHang.length > 0">
                <h4 class="suggestion-section__title"><i class="fas fa-utensils"></i> Nhà hàng - Quán ăn</h4>
                <div class="suggestions-grid">
                  <div v-for="(loc, idx) in danhSachNhaHang" :key="'nh' + idx" class="suggestion-card"
                    :class="{ selected: selectedLocations.includes(loc.ten_dia_diem) }"
                    @click="toggleLocation(loc.ten_dia_diem)">
                    <div class="suggestion-card__img">
                      <img :src="getCardImage(loc)" :alt="loc.ten_dia_diem"
                        @error="$event.target.src = fallbackActivityImage" />
                    </div>
                    <div class="suggestion-card__info">
                      <h4>{{ loc.ten_dia_diem }}</h4>
                      <p>{{ loc.mo_ta_ngan }}</p>
                      <span class="suggestion-card__addr"><i class="fas fa-location-dot"></i> {{ loc.dia_chi }}</span>
                    </div>
                    <div class="suggestion-card__check">
                      <i class="fas fa-check"></i>
                    </div>
                  </div>
                </div>
              </div>

              <!-- 4. Tour gợi ý
              <div class="suggestion-section suggestion-section--tours" v-if="danhSachTour.length > 0">
                <h4 class="suggestion-section__title"><i class="fas fa-route"></i> Tour có sẵn hợp lý cho bạn</h4>
                <div class="suggestions-grid suggestions-grid--tours">
                  <div v-for="(tour, idx) in danhSachTour" :key="'tour'+idx" class="suggestion-card suggestion-card--tour"
                    :class="{ selected: selectedTour && selectedTour.ma_tour === tour.ma_tour }">
                    <div class="suggestion-card__img">
                      <img :src="tour.hinh_anh || fallbackActivityImage" :alt="tour.ten_tour"
                        @error="$event.target.src = fallbackActivityImage" />
                    </div>
                    <div class="suggestion-card__info">
                      <div class="tour-badge">GỢI Ý TOUR</div>
                      <h4>{{ tour.ten_tour }}</h4>
                      <p class="tour-price">{{ formatVND(tour.so_tien) }}</p>
                      <span class="suggestion-card__addr"><i class="fas fa-clock"></i> {{ tour.so_ngay ? tour.so_ngay + ' ngày' : 'Theo lịch trình' }}</span>
                      <div class="tour-actions">
                        <button class="btn-pick-tour" @click.stop="chonTourNay(tour)">
                          {{ (selectedTour && selectedTour.ma_tour === tour.ma_tour) ? 'Đã chọn tour này' : 'Chọn địa điểm từ tour' }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->
            </div>

            <!-- TRẠNG THÁI XÁC NHẬN LỰA CHỌN (STEP 3) -->
            <div class="state-box state-box--review" v-else-if="step === 3 && !ketQua">
              <div class="review-header">
                <h3><i class="fas fa-clipboard-check"></i> Xác nhận hành trình của bạn</h3>
                <p>Kiểm tra lại các địa điểm đã chọn và tham khảo thêm các tour phù hợp trước khi AI thiết lập lịch
                  trình chi tiết.</p>
              </div>

              <div class="review-layout">
                <!-- Bên trái: Danh sách địa điểm đã chọn -->
                <div class="review-side review-side--left">
                  <h4 class="review-side__title">Địa điểm bạn đã chọn ({{ selectedLocations.length }})</h4>
                  <div class="selected-points-list">
                    <div v-for="(name, idx) in selectedLocations" :key="'sel' + idx" class="selected-point-card">
                      <div class="point-number">{{ idx + 1 }}</div>
                      <div class="point-name">{{ name }}</div>
                      <button class="btn-remove-point" @click="toggleLocation(name)">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                    <div v-if="selectedLocations.length === 0" class="empty-points">
                      Chưa có địa điểm nào được chọn.
                    </div>
                  </div>
                </div>

                <!-- Bên phải: Tour gợi ý phù hợp -->
                <div class="review-side review-side--right">
                  <h4 class="review-side__title">Tour đề xuất phù hợp</h4>
                  <div class="review-tours-list">
                    <div v-for="(tour, idx) in danhSachTour.slice(0, 3)" :key="'rtour' + idx" class="mini-tour-card">
                      <div class="mini-tour-img">
                        <img :src="tour.hinh_anh || fallbackActivityImage" :alt="tour.ten_tour" />
                      </div>
                      <div class="mini-tour-info">
                        <h5>{{ tour.ten_tour }}</h5>
                        <div class="mini-tour-meta">
                          <span class="price">{{ formatVND(tour.so_tien) }}</span>
                          <span class="time"><i class="fas fa-clock"></i> {{ tour.so_ngay ? tour.so_ngay + ' ngày' : 'Theo lịch trình' }}</span>
                        </div>
                        <div style="display: flex; gap: 8px; margin-top: 10px;">
                          <button class="btn-view-tour" @click="xemChiTietTour(tour)">Chi tiết <i
                              class="fas fa-external-link-alt"></i></button>
                          <button class="btn-view-tour" style="background: #2563eb; color: #fff; border-color: #2563eb;" 
                            v-if="!selectedTour || selectedTour.ma_tour !== tour.ma_tour" @click="chonTourNay(tour)">Chọn tour</button>
                          <button class="btn-view-tour" style="background: #10b981; color: #fff; border-color: #10b981;" 
                            v-else @click="boChonTour()">Đã chọn <i class="fas fa-check"></i></button>
                        </div>
                      </div>
                    </div>
                    <div v-if="danhSachTour.length === 0" class="empty-tours">
                      Không có tour nào phù hợp được tìm thấy.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- TRẠNG THÁI TRỐNG -->
            <div class="state-box state-box--empty" v-else-if="!ketQua">
              <i class="fas fa-map-marked-alt text-icon"></i>
              <h3>Hành trình của riêng bạn</h3>
              <p>Mô hình AI chuyên gia du lịch của chúng tôi đã sẵn sàng. Hãy cho biết điểm đến mơ ước của bạn và nhấn
                <strong>Tạo hành trình</strong>.
              </p>
            </div>

            <!-- Ket qua da tao -->
            <div class="result-content" v-else>
              <!-- Cover Header -->
              <header class="result-hero"
                :style="{ backgroundImage: `linear-gradient(180deg, rgba(14,26,45,0.1), rgba(14,26,45,0.85)), url('${ketQua.hinhAnh}')` }">
                <div class="result-hero__badge">ĐỀ XUẤT HÀNG ĐẦU</div>
                <div class="result-hero__badge result-hero__badge--fallback" v-if="planSource === 'fallback'">
                  Lịch trình dự phòng
                </div>
                <h2>{{ ketQua.tieuDe }}</h2>
                <div class="result-hero__meta">
                  <span><i class="far fa-clock"></i> {{ ketQua.thoiGian }}</span>
                  <span style="display: flex; align-items: center; gap: 5px;"><i class="fas fa-money-bill-wave"></i>
                    Ngân sách <input type="number" v-model="ketQua.nganSach"
                      style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: inherit; padding: 2px 8px; border-radius: 4px; outline: none; width: 100px; font-family: inherit; font-size: inherit;" /></span>
                </div>
              </header>

              <!-- Cảnh báo ngân sách hoặc thông báo từ AI -->
              <div class="inline-notice" v-if="aiNotice || (ketQua && ketQua.canhBao)" :class="{ 'inline-notice--warning': ketQua && ketQua.canhBao }">
                <div class="inline-notice__content">
                    <i class="fas fa-triangle-exclamation" v-if="ketQua && ketQua.canhBao"></i>
                    <i class="fas fa-circle-info" v-else></i>
                    <span>{{ (ketQua && ketQua.canhBao) ? ketQua.canhBao : aiNotice }}</span>
                </div>
                <button v-if="ketQua && ketQua.canhBao" class="btn-adjust-locations" @click="step = 2; ketQua = null;">
                    <i class="fas fa-edit"></i> Điều chỉnh lại địa điểm
                </button>
              </div>

              <div class="result-main-grid">
                <!-- Timeline lộ trình theo từng ngày (Cột trái) -->
                <div class="itinerary-timeline">
                  <div class="timeline-day" v-for="(ngay, index) in displayedDays" :key="`day-${index}`">
                    <div class="timeline-day__header">
                      <h3>{{ ngay.tieuDe }}</h3>
                      <span>{{ ngay.thoiGian }}</span>
                    </div>
                    <div class="timeline-list">
                      <div class="timeline-item" v-for="(hd, idx) in ngay.danhSachHoatDong"
                        :key="`activity-${index}-${idx}`">
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
                                <span class="tag-tour" v-if="hd.ma_tour" style="background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; border: 1px solid #bfdbfe;"><i class="fas fa-flag"></i> Tour</span>
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
                </div>

                <!-- Sidebar Tổng hợp (Cột phải) -->
                <aside class="itinerary-sidebar">
                  <!-- Thẻ Ngân sách & Số người -->
                  <div class="sidebar-card sidebar-card--blue">
                    <div class="sidebar-card__row">
                      <span>Thời gian</span>
                      <strong>{{ formatDateRange(form.ngayBatDau, form.ngayKetThuc) }}</strong>
                    </div>
                    <div class="sidebar-card__row">
                      <span>Số người</span>
                      <strong>{{ form.soNguoi }} người</strong>
                    </div>
                    <div class="sidebar-card__row">
                      <span>Ngân sách dự kiến</span>
                      <strong>{{ formatVND(ketQua.nganSach || form.nganSach) }}</strong>
                    </div>
                    <button class="btn-sidebar-detail" @click="showBudget = true">Xem chi tiết ngân sách</button>
                  </div>

                  <!-- Thẻ Tour phù hợp (Các tour có sẵn từ hệ thống) -->
                  <div class="sidebar-card" v-if="danhSachTour && danhSachTour.length > 0">
                    <div class="sidebar-card__header">
                      <i class="fas fa-route"></i>
                      <h3>Tour phù hợp với lịch trình của bạn</h3>
                    </div>
                    <div class="sidebar-card__list">
                      <div v-for="(tour, lIdx) in danhSachTour" :key="lIdx" class="sidebar-item sidebar-item--tour"
                        @click="xemChiTietTour(tour)">
                        <div class="sidebar-item__dot"></div>
                        <div class="sidebar-item__info">
                          <strong>{{ tour.ten_tour }}</strong>
                          <span class="tour-price-mini">{{ formatVND(tour.so_tien) }}</span>
                        </div>
                        <i class="fas fa-chevron-right sidebar-item__arrow"></i>
                      </div>
                    </div>
                  </div>

                  <!-- Thẻ Bản đồ -->
                  <div class="sidebar-card sidebar-card--map">
                    <ItineraryMiniMap class="ai-mini-map" :activities="allMapActivities" />
                    <div class="map-footer">
                      <div class="map-info">
                        <span>Lộ trình ước tính</span>
                        <strong>{{ totalRouteDistance }} km</strong>
                      </div>
                      <button class="btn-map-open" @click="showMap = true" :disabled="!canOpenMap">
                        {{ canOpenMap ? "MỞ BẢN ĐỒ" : "CHƯA CÓ TỌA ĐỘ" }}
                      </button>
                    </div>
                  </div>

                  <!-- Nút hành động chính -->
                  <div class="sidebar-actions">
                    <button class="btn-sidebar-outline" @click="xuatPDF"><i class="fas fa-download"></i> Xuất
                      PDF</button>
                    <button class="btn-sidebar-solid" @click="luuHanhTrinh" :disabled="dangLuu || !coTheLuu">
                      <i class="fas fa-save" v-if="!dangLuu"></i>
                      <i class="fas fa-spinner fa-spin" v-else></i>
                      {{ dangLuu ? "Đang lưu" : "Lưu hành trình" }}
                    </button>
                  </div>
                </aside>
              </div>

            </div>
          </div>
        </div>

        <div class="notice notice--error" v-if="thongBaoLoi">{{ thongBaoLoi }}</div>
        <div class="notice notice--success" v-if="thongBaoThanhCong">{{ thongBaoThanhCong }}</div>
      </section>
    </div>

    <ItineraryMapModal :show="showMap" @update:show="showMap = $event" :activities="allMapActivities" />
    <BudgetDetailModal
      :show="showBudget"
      :plan="aiBudgetPlan"
      :timeline="aiBudgetTimeline"
      :budget-rows="aiBudgetDetails"
      @update:show="showBudget = $event"
    />
  </div>
</template>

<script>
import { API_ORIGIN, goiApi } from "../../../services/httpClient.js";
import CustomerSidebar from "../CustomerSidebar.vue";
import ItineraryMapModal from "../../Shared/ItineraryMapModal.vue";
import ItineraryMiniMap from "../../Shared/ItineraryMiniMap.vue";
import BudgetDetailModal from "../../Shared/BudgetDetailModal.vue";
import {
  getStoredCustomerId,
  buildBudgetDetails,
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
    ItineraryMapModal,
    ItineraryMiniMap,
    BudgetDetailModal,
  },
  data() {
    return {
      AI_MIN_DAYS,
      AI_MAX_DAYS,
      AI_MIN_PEOPLE: 1,
      AI_MAX_PEOPLE: 100,
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
      dangTimTour: false,
      showMap: false,
      showBudget: false,
      step: 1,
      danhSachKhachSan: [],
      danhSachThamQuan: [],
      danhSachNhaHang: [],
      danhSachTour: [],
      selectedLocations: [],
      selectedTour: null,
      form: {
        diemDen: "",
        ngayBatDau: new Date().toISOString().split('T')[0],
        ngayKetThuc: new Date(Date.now() + 86400000 * 3).toISOString().split('T')[0],
        nganSach: "",
        soThich: [...DEFAULT_AI_INTERESTS],
        soNguoi: 1,
        moTaChuyenDi: ""
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
    today() {
      return new Date().toISOString().split('T')[0];
    },
    soNgay() {
      if (!this.form.ngayBatDau || !this.form.ngayKetThuc) return 1;
      const start = new Date(this.form.ngayBatDau);
      const end = new Date(this.form.ngayKetThuc);
      if (end < start) return 1;
      const diffTime = Math.abs(end - start);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
      return Math.min(AI_MAX_DAYS, Math.max(AI_MIN_DAYS, diffDays));
    },
    formatBudgetDisplay() {
      if (!this.form.nganSach) return "";
      return new Intl.NumberFormat('vi-VN').format(this.form.nganSach);
    },
    soNgayConLai() {
      if (!Array.isArray(this.ketQua?.lichTrinh)) return 0;
      return Math.max(0, this.ketQua.lichTrinh.length - 1);
    },
    displayedDays() {
      if (!Array.isArray(this.ketQua?.lichTrinh)) return [];
      return this.xemTatCaNgay ? this.ketQua.lichTrinh : this.ketQua.lichTrinh.slice(0, 1);
    },
    uniqueLocations() {
      if (!this.ketQua?.lichTrinh) return [];
      const locations = [];
      this.ketQua.lichTrinh.forEach(day => {
        day.danhSachHoatDong?.forEach(act => {
          if (!locations.find(l => l.ten === act.tieuDe)) {
            locations.push({ ten: act.tieuDe, tg: act.buoi });
          }
        });
      });
      return locations.slice(0, 5); // Hiển thị 5 cái đầu
    },
    allMapActivities() {
      if (!this.ketQua?.lichTrinh) return [];
      const acts = [];
      this.ketQua.lichTrinh.forEach(day => {
        day.danhSachHoatDong?.forEach(act => {
          if (act.kinh_do && act.vi_do) {
            acts.push(act);
          }
        });
      });
      return acts;
    },
    canOpenMap() {
      return this.allMapActivities.length > 0;
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
    },
    aiBudgetPlan() {
      return {
        budget: Number(this.ketQua?.nganSach || this.form.nganSach || 0),
        budgetLabel: this.formatVND(this.ketQua?.nganSach || this.form.nganSach || 0),
        soNguoi: Number(this.form.soNguoi || 0),
        aiData: this.ketQuaAiRaw || this.ketQua || {},
      };
    },
    aiBudgetTimeline() {
      if (!Array.isArray(this.ketQua?.lichTrinh)) return [];
      return this.ketQua.lichTrinh.map((day, index) => ({
        title: day.tieuDe || `Ngày ${index + 1}`,
        dateRaw: day.ngay_cu_the || "",
        dateFormatted: day.thoiGian || "",
        activities: (day.danhSachHoatDong || []).map((activity) => ({
          ...activity,
          name: activity.tieuDe,
          locationName: activity.tieuDe,
          description: activity.moTa,
          time: activity.thoiGian || activity.buoi || "",
          locationCost: activity.gia || activity.gia_uoc_luong || activity.price || null,
          services: activity.dich_vu_dia_diems || activity.dichVuDiaDiems || activity.services || [],
        })),
      }));
    },
    aiBudgetDetails() {
      return buildBudgetDetails(this.aiBudgetPlan, this.aiBudgetTimeline);
    }
  },
  mounted() {
    this.maKhachHang = getStoredCustomerId();
  },
  beforeUnmount() {
    this.dungTienDo();
  },
  methods: {
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
      
      // Ưu tiên hình từ hệ thống, nếu không có dùng picsum seed theo tên địa điểm
      let rawHinh = hoatDong.hinhanh || hoatDong.hinh_anh || hoatDong.hinhAnh || "";
      if (!rawHinh || rawHinh === "null") {
        let seed = (hoatDong.tieuDe || hoatDong.tieu_de || hoatDong.ten || "smart-travel").replace(/[^a-zA-Z0-9]/g, '');
        if (!seed) seed = "travel-activity";
        rawHinh = `https://picsum.photos/seed/${seed}/400/300`;
      }

      const hinh = this.resolveImageUrl(rawHinh, this.fallbackActivityImage);
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
        kinh_do: hoatDong.kinh_do ?? hoatDong.lng ?? hoatDong.longitude ?? null,
        vi_do: hoatDong.vi_do ?? hoatDong.lat ?? hoatDong.latitude ?? null,
        services: hoatDong.dich_vu_dia_diems || hoatDong.dichVuDiaDiems || hoatDong.services || [],
        ma_tour: hoatDong.ma_tour || null,
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
    getCardImage(loc) {
      if (loc.hinhanh && loc.hinhanh.startsWith('http') && !loc.hinhanh.includes('upload.wikimedia.org')) {
        return loc.hinhanh;
      }
      let seed = loc.ten_dia_diem.replace(/[^a-zA-Z0-9]/g, '');
      if (!seed) seed = "smart-travel";
      return `https://picsum.photos/seed/${seed}/160/160`;
    },
    chonTourNay(tour) {
      this.selectedTour = tour;
      if (tour.chi_tiet_tours) {
        tour.chi_tiet_tours.forEach(ct => {
          if (ct.dia_diem && ct.dia_diem.ten_dia_diem) {
            if (!this.selectedLocations.includes(ct.dia_diem.ten_dia_diem)) {
              this.selectedLocations.push(ct.dia_diem.ten_dia_diem);
            }
          }
        });
      }
      this.thongBaoThanhCong = `Đã chọn tour "${tour.ten_tour}". Hành trình sẽ được tối ưu theo tour này!`;
      setTimeout(() => this.thongBaoThanhCong = "", 3000);
    },
    boChonTour() {
      this.selectedTour = null;
    },
    xemChiTietTour(tour) {
      if (tour && tour.ma_tour) {
        window.open(`/khach-hang/tour/${tour.ma_tour}`, '_blank');
      }
    },
    retryAction() {
      if (this.step === 1) this.layDeXuatDiaDiem();
      else this.taoHanhTrinh();
    },
    toggleLocation(locName) {
      const idx = this.selectedLocations.indexOf(locName);
      if (idx > -1) {
        this.selectedLocations.splice(idx, 1);
      } else {
        this.selectedLocations.push(locName);
      }
    },
    async layDeXuatDiaDiem() {
      if (!this.form.diemDen) return;
      const soNgay = this.soNgay;

      this.thongBaoLoi = "";
      this.aiErrorMessage = "";
      this.dangXuLy = true;
      this.ketQua = null;
      this.step = 1;
      this.batDauTienDo();

      try {
        const phanHoi = await goiApi("/khach-hang/ke-hoach-ai/de-xuat-dia-diem", {
          method: "POST",
          timeout: 120000,
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: {
            diem_den: this.form.diemDen,
            ngay_bat_dau: this.form.ngayBatDau,
            ngay_ket_thuc: this.form.ngayKetThuc,
            so_ngay: soNgay,
            ngan_sach: this.form.nganSach,
            so_thich: this.form.soThich,
            mo_ta_chuyen_di: this.form.moTaChuyenDi,
          },
        });

        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok || duLieuPhanHoi?.success === false) {
          throw new Error(duLieuPhanHoi?.message || "Không thể lấy đề xuất địa điểm.");
        }

        let responseData = duLieuPhanHoi?.data || {};
        let suggestions = responseData.ai_suggestions || [];
        this.danhSachTour = responseData.tours || [];

        this.danhSachKhachSan = [];
        this.danhSachThamQuan = [];
        this.danhSachNhaHang = [];

        if (suggestions && !Array.isArray(suggestions)) {
          if (Array.isArray(suggestions.khach_san)) this.danhSachKhachSan = suggestions.khach_san;
          if (Array.isArray(suggestions.dia_diem_tham_quan)) this.danhSachThamQuan = suggestions.dia_diem_tham_quan;
          if (Array.isArray(suggestions.nha_hang_quan_an)) this.danhSachNhaHang = suggestions.nha_hang_quan_an;
        } else if (Array.isArray(suggestions)) {
          this.danhSachThamQuan = suggestions;
        }

        this.selectedLocations = [];
        this.step = 2;
      } catch (error) {
        this.aiErrorMessage = error.message;
        this.retryable = true;
      } finally {
        this.ketThucTienDo();
        this.dangXuLy = false;
      }
    },
    async xacNhanLuaChon() {
      if (this.selectedLocations.length === 0) return;

      this.dangTimTour = true;
      try {
        const phanHoi = await goiApi("/khach-hang/ke-hoach-ai/goi-y-tour-phu-hop", {
          method: "POST",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: {
            diem_den: this.form.diemDen,
            selected_locations: this.selectedLocations,
            ngay_bat_dau: this.form.ngayBatDau,
            ngay_ket_thuc: this.form.ngayKetThuc,
          },
        });

        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (phanHoi.ok && duLieuPhanHoi?.success) {
          this.danhSachTour = duLieuPhanHoi.data || [];
        }

        this.step = 3;
      } catch (error) {
        console.error("Lỗi khi lọc tour phù hợp:", error);
        // Vẫn cho qua bước 3 nếu lỗi API lọc tour
        this.step = 3;
      } finally {
        this.dangTimTour = false;
      }
    },
    xuatPDF() {
      window.print();
    },
    updateBudget(event) {
      const val = event.target.value.replace(/[^0-9]/g, "");
      this.form.nganSach = val ? parseInt(val) : "";
    },
    formatVND(value) {
      if (!value) return "0 VNĐ";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    formatDateRange(start, end) {
      if (!start || !end) return "";
      const s = new Date(start).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
      const e = new Date(end).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
      return `${s} - ${e} (${this.soNgay} ngày)`;
    },
    async taoHanhTrinh() {
      if (!this.form.diemDen) return;
      const soNgay = this.soNgay;

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
            diem_den: this.form.diemDen,
            ngay_bat_dau: this.form.ngayBatDau,
            ngay_ket_thuc: this.form.ngayKetThuc,
            so_ngay: soNgay,
            ngan_sach: this.form.nganSach,
            so_thich: this.form.soThich,
            mo_ta_chuyen_di: this.form.moTaChuyenDi,
            selectedLocations: this.selectedLocations,
            selected_tour: this.selectedTour ? {
              ma_tour: this.selectedTour.ma_tour,
              ten_tour: this.selectedTour.ten_tour,
              ma_thoi_gian_tour: this.selectedTour.ma_thoi_gian_tour || null
            } : null,
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
          ten_ke_hoach: this.ketQua?.tieuDe || this.ketQuaAiRaw?.tieuDe || `Hành trình ${this.form.diemDen}`,
          so_nguoi: this.form.soNguoi,
          ngay_bat_dau: this.form.ngayBatDau,
          ngay_ket_thuc: this.form.ngayKetThuc,
          thong_tin_chuyen_di: {
            diemDen: this.form.diemDen,
            soNgay: this.soNgay,
            nganSach: this.ketQua?.nganSach || this.form.nganSach,
            soThich: this.form.soThich,
            tongChiPhi: this.ketQua?.nganSach || this.ketQuaAiRaw?.tongChiPhi || "",
            selected_tour: this.selectedTour ? {
              ma_tour: this.selectedTour.ma_tour,
              ten_tour: this.selectedTour.ten_tour,
              ma_thoi_gian_tour: this.selectedTour.ma_thoi_gian_tour || null
            } : null,
          },
          ket_qua_ai: {
            ...this.ketQuaAiRaw,
            tieuDe: this.ketQua?.tieuDe || this.ketQuaAiRaw?.tieuDe,
            nganSach: this.ketQua?.nganSach || this.ketQuaAiRaw?.nganSach,
          },
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
            path: "/khach-hang/ke-hoach",
            query: maKeHoach ? { created: maKeHoach } : {},
          });
        }, 700);
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể lưu hành trình AI.";
      } finally {
        this.dangLuu = false;
      }
    },
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
  font-size: 1.15rem;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 28px;
  display: flex;
  align-items: center;
  gap: 12px;
  position: relative;
}

.config-title i {
  color: #3b82f6;
  font-size: 1.1rem;
}

.form-group {
  margin-bottom: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
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
  background: #ffffff;
  border-radius: 14px;
  border: 1.5px solid #eef2f6;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01);
}

.input-with-icon:hover {
  border-color: #cbd5e1;
  background: #fcfdfe;
}

.input-with-icon:focus-within {
  border-color: #3b82f6;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
}

.input-with-icon i {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  font-size: 0.9rem;
  transition: color 0.2s;
}

.input-with-icon:focus-within i {
  color: #3b82f6;
}

.input-with-icon input,
.input-with-icon select {
  width: 100%;
  border: none;
  background: transparent;
  padding: 13px 14px 13px 42px;
  font-size: 0.95rem;
  color: #1e293b;
  outline: none;
  font-family: inherit;
  font-weight: 500;
}

.input-with-icon input[type="date"] {
  padding-right: 10px;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

.input-with-icon input[type="number"]::-webkit-inner-spin-button,
.input-with-icon input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
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
  border: 1.5px solid #eef2f6;
  outline: none;
  display: flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  padding: 8px 16px;
  border-radius: 100px;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 0.85rem;
  color: #64748b;
  font-family: inherit;
  font-weight: 600;
}

.pref-checkbox:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
  color: #1e293b;
}

.pref-checkbox.active {
  border-color: #3b82f6;
  color: #3b82f6;
  background: #eff6ff;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.btn-generate {
  width: 100%;
  background: linear-gradient(135deg, #0061ff 0%, #60efff 100%);
  color: #ffffff;
  border: none;
  padding: 16px;
  border-radius: 16px;
  font-size: 1rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  cursor: pointer;
  margin-top: 24px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 20px rgba(0, 97, 255, 0.2);
  letter-spacing: 0.5px;
}

.btn-generate:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(0, 97, 255, 0.3);
  filter: brightness(1.1);
}

.btn-generate:active {
  transform: translateY(0);
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

@keyframes s-spin {
  100% {
    transform: rotate(360deg);
  }
}

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
  background-image: linear-gradient(180deg, rgba(14, 26, 45, 0.1), rgba(14, 26, 45, 0.85)), url('https://images.unsplash.com/photo-1596347958988-cb942eb22eb7?w=1000');
  background-size: cover;
  background-position: center;
  color: #ffffff;
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.1);
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
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.result-hero__meta {
  display: flex;
  gap: 16px;
  font-size: 0.95rem;
  font-weight: 500;
}

.inline-notice {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 20px;
  border-radius: 16px;
  border: 1px solid #fde68a;
  background: #fffbeb;
  color: #92400e;
  font-size: 0.95rem;
  font-weight: 600;
  margin: 0 0 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.inline-notice__content {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.btn-adjust-locations {
  background: #b91c1c;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-adjust-locations:hover {
  background: #991b1b;
  transform: translateY(-1px);
}

.inline-notice--warning {
  background: #fff1f2;
  border-color: #fecaca;
  color: #b91c1c;
  animation: pulse-border 2s infinite;
}

@keyframes pulse-border {
  0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
  70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
  100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

/* TIMELINE BOX */
.timeline-day {
  background: #ffffff;
  border-radius: 24px;
  border: 1px solid #cce0ff;
  border-left: 5px solid #0060aa;
  padding: 24px;
  box-shadow: 0 12px 30px rgba(0, 85, 255, 0.03);
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

.icon--morning {
  background: #38bdf8;
}

.icon--afternoon {
  background: #94a3b8;
}

.icon--evening {
  background: #1e1e2f;
}

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

.tag-price,
.tag-time {
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

.tag-time i {
  margin-right: 4px;
}

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
}

/* 3. STEP 3 - REVIEW LAYOUT */
.state-box--review {
  padding: 40px;
  align-items: stretch;
  justify-content: flex-start;
  text-align: left;
}

.review-header {
  margin-bottom: 30px;
  border-bottom: 1px solid #edf2f7;
  padding-bottom: 20px;
}

.review-header h3 {
  margin: 0 0 10px;
  font-size: 1.6rem;
  font-weight: 800;
  color: #1a2b4c;
  display: flex;
  align-items: center;
  gap: 12px;
}

.review-header h3 i {
  color: #10b981;
}

.review-header p {
  margin: 0;
  font-size: 1rem;
  color: #64748b;
}

.review-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  flex: 1;
}

.review-side {
  display: flex;
  flex-direction: column;
}

.review-side__title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
}

.selected-points-list {
  display: grid;
  gap: 12px;
}

.selected-point-card {
  display: flex;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 12px 16px;
  border-radius: 12px;
  gap: 14px;
  transition: all 0.2s;
}

.selected-point-card:hover {
  border-color: #cbd5e1;
  transform: translateX(4px);
  background: #ffffff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.point-number {
  width: 28px;
  height: 28px;
  background: #3b82f6;
  color: #fff;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 0.85rem;
  font-weight: 700;
}

.point-name {
  flex: 1;
  font-weight: 600;
  color: #334155;
  font-size: 0.95rem;
}

.btn-remove-point {
  background: transparent;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
  transition: color 0.2s;
}

.btn-remove-point:hover {
  color: #ef4444;
}

.review-tours-list {
  display: grid;
  gap: 16px;
}

.mini-tour-card {
  display: flex;
  background: #ffffff;
  border: 1px solid #eef2f6;
  border-radius: 16px;
  overflow: hidden;
  gap: 16px;
  transition: all 0.3s;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.mini-tour-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
  border-color: #3b82f6;
}

.mini-tour-img {
  width: 100px;
  height: 100px;
}

.mini-tour-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.mini-tour-info {
  flex: 1;
  padding: 12px 12px 12px 0;
  display: flex;
  flex-direction: column;
}

.mini-tour-info h5 {
  margin: 0 0 8px;
  font-size: 1rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.4;
}

.mini-tour-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 10px;
}

.mini-tour-meta .price {
  font-weight: 800;
  color: #2563eb;
  font-size: 0.9rem;
}

.mini-tour-meta .time {
  font-size: 0.8rem;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-view-tour {
  align-self: flex-start;
  background: transparent;
  border: none;
  color: #3b82f6;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-view-tour:hover {
  text-decoration: underline;
}

.empty-points,
.empty-tours {
  padding: 30px;
  background: #f8fafc;
  border: 1.5px dashed #e2e8f0;
  border-radius: 12px;
  text-align: center;
  color: #94a3b8;
  font-style: italic;
}

@media (max-width: 1024px) {
  .review-layout {
    grid-template-columns: 1fr;
  }
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

.btn-outline:hover {
  background: #f1f5f9;
}

.btn-solid {
  background: #0076a0;
  color: #fff;
  border: none;
}

.btn-solid:hover {
  background: #008ebf;
}

.btn-solid:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* 2.3 LAYOUT MỚI CỦA KẾT QUẢ */
.result-main-grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 24px;
  align-items: start;
}

.itinerary-timeline {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.itinerary-sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
  position: sticky;
  top: 100px;
}

.sidebar-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 24px;
  border: 1px solid #edf1f7;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
}

.sidebar-card--blue {
  background: #114b71;
  color: #ffffff;
  border: none;
}

.sidebar-card__row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.sidebar-card__row span {
  font-size: 0.9rem;
  opacity: 0.85;
}

.sidebar-card__row strong {
  font-size: 1.1rem;
  font-weight: 800;
}

.btn-sidebar-detail {
  width: 100%;
  background: #ffffff;
  color: #114b71;
  border: none;
  padding: 12px;
  border-radius: 12px;
  font-weight: 700;
  margin-top: 12px;
  cursor: pointer;
  transition: opacity 0.2s;
}

.btn-sidebar-detail:hover {
  opacity: 0.9;
}

.sidebar-card__header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.sidebar-card__header i {
  color: #0055ff;
  font-size: 1.2rem;
}

.sidebar-card__header h3 {
  font-size: 1rem;
  font-weight: 800;
  color: #1a2b4c;
  margin: 0;
}

.sidebar-card__list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sidebar-item {
  display: flex;
  gap: 14px;
  align-items: center;
  padding: 12px;
  background: #f8fafc;
  border-radius: 16px;
}

.sidebar-item__dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #3b82f6;
  flex-shrink: 0;
}

.sidebar-item__info {
  display: flex;
  flex-direction: column;
}

.sidebar-item__info strong {
  font-size: 0.92rem;
  color: #1e293b;
}

.sidebar-item__info span {
  font-size: 0.8rem;
  color: #64748b;
}

.sidebar-item--tour {
  cursor: pointer;
  transition: all 0.2s ease;
  border-radius: 12px;
  margin: 0 -4px;
}

.sidebar-item--tour:hover {
  background: #f1f5f9;
  transform: translateX(4px);
}

.sidebar-item--tour:hover .sidebar-item__dot {
  background: #3b82f6;
  transform: scale(1.3);
}

.tour-price-mini {
  font-size: 0.75rem;
  color: #e11d48;
  font-weight: 700;
  margin-top: 2px;
}

.sidebar-item__arrow {
  font-size: 0.7rem;
  color: #cbd5e1;
  margin-left: auto;
  transition: all 0.2s;
}

.sidebar-item--tour:hover .sidebar-item__arrow {
  color: #3b82f6;
  transform: translateX(4px);
}

.sidebar-card--map {
  padding: 0;
  overflow: hidden;
}

.ai-mini-map {
  height: 180px;
  min-height: 180px;
}

.map-footer {
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
}

.map-info {
  display: flex;
  flex-direction: column;
}

.map-info span {
  font-size: 0.75rem;
  color: #64748b;
}

.map-info strong {
  font-size: 1rem;
  color: #0f172a;
}

.btn-map-open {
  background: transparent;
  border: none;
  color: #0055ff;
  font-weight: 800;
  font-size: 0.85rem;
  cursor: pointer;
}

.btn-map-open:disabled {
  color: #94a3b8;
  cursor: not-allowed;
}

.sidebar-actions {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
}

.btn-sidebar-outline,
.btn-sidebar-solid {
  width: 100%;
  padding: 14px;
  border-radius: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
}

.btn-sidebar-outline {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  color: #475569;
}

.btn-sidebar-solid {
  background: #0076a0;
  color: #ffffff;
  border: none;
}

.btn-sidebar-solid:disabled {
  opacity: 0.7;
}

@media (max-width: 1280px) {
  .result-main-grid {
    grid-template-columns: 1fr;
  }

  .itinerary-sidebar {
    position: static;
  }
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

/* KHUNG HIỂN THỊ ĐỀ XUẤT ĐỊA ĐIỂM */
.selected-locations-summary {
  display: flex;
  flex-direction: column;
  margin-top: 15px;
  background: #f8fafc;
  padding: 15px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.selected-locations-summary p {
  margin: 0;
  font-size: 0.95rem;
  color: #334155;
}

.btn-generate--step2 {
  flex: 1;
  margin-top: 0;
  padding: 12px 14px;
}

.btn-back {
  background: #f1f5f9;
  color: #475569;
  border-radius: 12px;
  padding: 12px 20px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
}

.btn-back:hover {
  background: #e2e8f0;
}

.suggestions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
  margin-top: 24px;
  margin-bottom: 10px;
  text-align: left;
}

.suggestion-card {
  position: relative;
  display: flex;
  align-items: center;
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e1e8f2;
  padding: 16px;
  gap: 16px;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

.suggestion-card:hover {
  transform: translateY(-2px);
  border-color: #bfdbfe;
  box-shadow: 0 10px 20px rgba(37, 99, 235, 0.08);
}

.suggestion-card.selected {
  background: #f0f5ff;
  border-color: #0055ff;
}

.suggestion-card__img {
  width: 64px;
  height: 64px;
  min-width: 64px;
  border-radius: 12px;
  overflow: hidden;
  background: #eef2ff;
}

.suggestion-card__img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.suggestion-card__icon {
  width: 64px;
  height: 64px;
  min-width: 64px;
  border-radius: 12px;
  background: #eef2ff;
  color: #4f46e5;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  transition: all 0.2s;
}

.suggestion-card.selected .suggestion-card__icon {
  background: #0055ff;
  color: #ffffff;
}

.suggestion-card__info {
  flex: 1;
}

.suggestion-card__info h4 {
  margin: 0 0 4px;
  font-size: 1.05rem;
  font-weight: 700;
  color: #1e293b;
}

.suggestion-card__info p {
  margin: 0 0 6px;
  font-size: 0.9rem;
  color: #64748b;
  line-height: 1.3;
}

.suggestion-card__addr {
  font-size: 0.8rem;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 6px;
}

.suggestion-card__check {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #ffffff;
  border: 2px solid #cbd5e1;
  color: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  transition: all 0.2s;
}

.suggestion-card.selected .suggestion-card__check {
  background: #0055ff;
  border-color: #0055ff;
  color: #ffffff;
}

@media (max-width: 1024px) {
  .ai-grid {
    grid-template-columns: 1fr;
  }
}

.suggestion-section {
  margin-top: 24px;
}

.suggestion-section__title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #112a46;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.suggestion-section__title i {
  color: #0055ff;
}

/* TOUR SUGGESTIONS IN STEP 2 */
.suggestion-section--tours {
  background: #f8fafc;
  padding: 24px;
  border-radius: 16px;
  border: 1.5px dashed #cbd5e1;
  margin-top: 32px;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
}

.suggestion-card--tour {
  border-left: 4px solid #3b82f6 !important;
  background: #ffffff;
  cursor: default !important;
}

.tour-badge {
  display: inline-block;
  background: #3b82f6;
  color: white;
  font-size: 0.65rem;
  font-weight: 800;
  padding: 2px 10px;
  border-radius: 6px;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.tour-price {
  color: #e11d48;
  font-weight: 800;
  font-size: 1.15rem;
  margin: 6px 0;
}

.tour-actions {
  display: flex;
  gap: 10px;
  margin-top: 16px;
}

.btn-pick-tour {
  flex: 1;
  background: #ffffff;
  border: 1.5px solid #3b82f6;
  color: #3b82f6;
  padding: 10px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-pick-tour:hover {
  background: #3b82f6;
  color: #ffffff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}
</style>
