<template>
  <div class="checkout-page">
    <div class="checkout-workspace">
      <CustomerSidebar />

      <div class="checkout-shell">
        <header class="checkout-header">
          <router-link class="back-link" :to="`/khach-hang/tour/${maTour}`">
            <i class="fas fa-arrow-left"></i>
            Quay lại trang chi tiết tour
          </router-link>

          <div>
            <p class="eyebrow">Đặt tour > Thanh toán</p>
            <h1>Thanh toán tour bằng QR ngân hàng</h1>
            <p class="subheading">
              Tạo hóa đơn, quét QR MBBank, theo dõi đồng hồ đếm ngược 5 phút và đối chiếu thanh toán
              tự động trên cùng một màn hình.
            </p>
          </div>
        </header>

        <div v-if="errorMessage" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ errorMessage }}</span>
        </div>

        <div v-if="statusMessage" class="notice notice--success">
          <i class="fas fa-circle-check"></i>
          <span>{{ statusMessage }}</span>
        </div>

        <div v-if="loadingPage" class="notice notice--info">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang tải thông tin tour và lịch khởi hành...</span>
        </div>

        <div v-else class="checkout-grid">
          <section class="checkout-main">
            <PaymentHeroPanel
              v-if="paymentInfo"
              :payment-info="paymentInfo"
              :payment-status-label="paymentStatusLabelDisplay"
              :payment-status-tone="paymentStatusTone"
              :formatted-amount="dinhDangTien(paymentInfo.so_tien)"
              :schedule-label="schedule?.ngayBatDauHienThi || '--'"
              :countdown-label="countdownLabelDisplay"
              :formatted-expiry="dinhDangNgayGio(paymentInfo.payment_expires_at)"
              :checking-status="checkingStatus"
              :cancelling-payment="cancellingPayment"
              @copy-transfer-content="copyTransferContent"
              @check-now="refreshPaymentStatus(false)"
              @cancel-payment="huyThanhToan"
              @view-invoice="goToInvoiceDetail"
            />

            <article class="panel">
              <div class="panel__header">
                <div class="panel__icon"><i class="fas fa-user"></i></div>
                <div>
                  <h2>Thông tin người đặt</h2>
                  <p>Thông tin này được lưu vào hóa đơn thanh toán.</p>
                </div>
              </div>

              <div class="form-grid">
                <label class="form-group">
                  <span>Họ và tên</span>
                  <input v-model.trim="form.fullName" type="text" placeholder="Vũ Hà Thái Sơn" />
                </label>
                <label class="form-group">
                  <span>Số điện thoại</span>
                  <input v-model.trim="form.phone" type="tel" placeholder="09xxxxxxxx" />
                </label>
                <label class="form-group full-width">
                  <span>Email</span>
                  <input v-model.trim="form.email" type="email" placeholder="you@example.com" />
                </label>
                <label class="form-group full-width">
                  <span>Địa chỉ</span>
                  <input
                    v-model.trim="form.address"
                    type="text"
                    placeholder="Số nhà, đường, quận/huyện, tỉnh/thành phố"
                  />
                </label>
              </div>
            </article>

            <article class="panel">
              <div class="panel__header">
                <div class="panel__icon"><i class="fas fa-qrcode"></i></div>
                <div>
                  <h2>Phương thức thanh toán</h2>
                  <p>Hiện tại hệ thống hỗ trợ một luồng thanh toán duy nhất bằng QR ngân hàng.</p>
                </div>
              </div>

              <div class="payment-method payment-method--active">
                <div class="payment-method__icon"><i class="fas fa-building-columns"></i></div>
                <div class="payment-method__content">
                  <strong>Thanh toán QR ngân hàng</strong>
                  <span>Khách quét QR sẽ thấy sẵn số tiền và nội dung chuyển khoản là mã hóa đơn.</span>
                </div>
                <span class="payment-method__badge">VietQR</span>
              </div>
            </article>
          </section>

          <aside class="checkout-sidebar">
            <article class="summary-card">
              <div class="summary-hero">
                <img :src="tour?.hinhAnh || hinhMacDinh" alt="Tour cover" />
                <div class="summary-hero__overlay">
                  <span class="summary-hero__pill">Tour đang đặt</span>
                  <h3>{{ tour?.ten || "Đang cập nhật tour" }}</h3>
                </div>
              </div>

              <div class="summary-content">
                <div class="summary-item">
                  <span>Ngày khởi hành</span>
                  <strong>{{ schedule?.ngayBatDauHienThi || "--" }}</strong>
                </div>
                <div class="summary-item">
                  <span>Số chỗ còn lại</span>
                  <strong>{{ schedule?.soCho ?? "--" }} chỗ</strong>
                </div>
                <div class="summary-item align-items-center">
                  <span>Số lượng khách</span>
                  <div class="quantity-selector">
                    <button type="button" @click="giamSoLuong" :disabled="soLuongKhach <= 1 || hasActivePendingInvoice || paymentInfo?.payment_status === 'paid'">-</button>
                    <input type="number" v-model.number="soLuongKhach" min="1" :max="schedule?.soCho || 1" @change="kiemTraSoLuong" :disabled="hasActivePendingInvoice || paymentInfo?.payment_status === 'paid'" />
                    <button type="button" @click="tangSoLuong" :disabled="soLuongKhach >= (schedule?.soCho || 1) || hasActivePendingInvoice || paymentInfo?.payment_status === 'paid'">+</button>
                  </div>
                </div>
                <div class="summary-item">
                  <span>Giá tour (1 khách)</span>
                  <strong>{{ dinhDangTien(schedule?.soTien || tour?.soTien) }}</strong>
                </div>
                <div v-if="paymentInfo" class="summary-item">
                  <span>Mã hóa đơn hiện tại</span>
                  <strong>{{ paymentInfo.ma_hoa_don }}</strong>
                </div>

                <hr class="divider" />

                <!-- Voucher Section -->
                <div class="summary-item voucher-section">
                  <div class="voucher-header">
                    <span>Mã giảm giá</span>
                    <button type="button" class="btn-select-voucher" @click="openVoucherModal" :disabled="hasActivePendingInvoice || paymentInfo?.payment_status === 'paid'">
                      {{ selectedVoucher ? 'Đổi mã' : 'Chọn mã' }}
                    </button>
                  </div>
                  <div v-if="selectedVoucher" class="selected-voucher-info mt-2">
                    <div class="d-flex align-items-center justify-content-between">
                      <span class="badge badge-success">{{ selectedVoucher.ma_voucher }}</span>
                      <strong class="text-success">-{{ dinhDangTien(discountAmount) }}</strong>
                    </div>
                    <button type="button" class="btn-remove-voucher" @click="removeVoucher" :disabled="hasActivePendingInvoice || paymentInfo?.payment_status === 'paid'">
                      <i class="fas fa-times"></i> Bỏ chọn
                    </button>
                  </div>
                </div>

                <hr class="divider" />

                <div class="summary-total">
                  <span>Tổng thanh toán</span>
                  <strong>{{ dinhDangTien(finalTotalAmount) }}</strong>
                </div>

                <button
                  type="button"
                  class="primary-button"
                  :disabled="primaryButtonDisabled"
                  @click="xuLyNutThanhToan"
                >
                  <i class="fas" :class="primaryButtonIcon"></i>
                  {{ primaryButtonLabelDisplay }}
                </button>

                <p class="summary-note">
                  Sau khi thanh toán thành công, admin và đối tác sẽ nhìn thấy cùng một mã hóa đơn để đối chiếu.
                </p>
              </div>
            </article>
          </aside>
        </div>
      </div>
    </div>

    <!-- Voucher Selection Modal -->
    <div v-if="showVoucherModal" class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Chọn Mã Giảm Giá</h5>
            <button type="button" class="close" @click="showVoucherModal = false">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body p-0">
            <div v-if="loadingVouchers" class="text-center p-4">
              <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            </div>
            <div v-else class="voucher-list">
              <div v-if="availableVouchers.valid.length === 0 && availableVouchers.invalid.length === 0" class="text-center p-4 text-muted">
                Không có mã giảm giá nào.
              </div>
              
              <!-- Valid Vouchers -->
              <div v-for="v in availableVouchers.valid" :key="v.id" class="voucher-item valid-voucher">
                <div class="voucher-icon"><i class="fas fa-ticket-alt"></i></div>
                <div class="voucher-details">
                  <strong>{{ v.ma_voucher }}</strong>
                  <p>{{ v.ten_voucher }}</p>
                  <small class="text-muted">Đơn tối thiểu {{ dinhDangTien(v.don_toi_thieu) }}</small>
                </div>
                <button class="btn btn-sm btn-primary" @click="applyVoucher(v)">Dùng</button>
              </div>

              <!-- Invalid Vouchers -->
              <div v-if="availableVouchers.invalid.length > 0" class="invalid-vouchers-section p-3 bg-light">
                <h6 class="text-muted mb-3">Chưa đủ điều kiện</h6>
                <div v-for="v in availableVouchers.invalid" :key="v.id" class="voucher-item invalid-voucher">
                  <div class="voucher-icon text-muted"><i class="fas fa-ticket-alt"></i></div>
                  <div class="voucher-details text-muted">
                    <strong>{{ v.ma_voucher }}</strong>
                    <p>{{ v.ten_voucher }}</p>
                    <small>Cần mua thêm {{ dinhDangTien(v.don_toi_thieu - (tour?.soTien || 0)) }}</small>
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
import { API_ORIGIN, goiApi } from "../../../../services/httpClient.js";
import { getStoredCustomerId, getStoredUser } from "../../../Shared/customerSession";
import CustomerSidebar from "../../CustomerSidebar.vue";
import PaymentHeroPanel from "./PaymentHeroPanel.vue";
import { showConfirm } from "../../../../services/appDialog";

const TOUR_API = "/api/tour";
const TOUR_KHOI_HANH_API = "/api/tour-khoi-hanh";
const HINH_MAC_DINH = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80";
const CUSTOMER_CANCELLED_REFERENCE = "customer_cancelled";

async function fetchJson(url, init = {}) {
  const response = await goiApi(url, init);
  const payload = await response.json();

  if (!response.ok) {
    const error = new Error(payload?.message || "Không thể hoàn tất yêu cầu.");
    error.response = { data: payload, status: response.status };
    throw error;
  }

  return payload;
}

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "KhachHangThanhToanTourPage",
  components: {
    CustomerSidebar,
    PaymentHeroPanel,
  },
  data() {
    return {
      loadingPage: false,
      processing: false,
      checkingStatus: false,
      cancellingPayment: false,
      errorMessage: "",
      statusMessage: "",
      tour: null,
      schedule: null,
      paymentInfo: null,
      pollingTimer: null,
      countdownTimer: null,
      countdownNow: Date.now(),
      form: {
        fullName: "",
        phone: "",
        email: "",
        address: "",
      },
      hinhMacDinh: HINH_MAC_DINH,
      showVoucherModal: false,
      loadingVouchers: false,
      availableVouchers: { valid: [], invalid: [] },
      selectedVoucher: null,
      discountAmount: 0,
      soLuongKhach: 1,
    };
  },
  computed: {
    maTour() {
      return String(this.$route.params.id || "");
    },
    scheduleQuery() {
      return String(this.$route.query.schedule || "").trim();
    },
    invoiceQuery() {
      return String(this.$route.query.invoice || "").trim();
    },
    isCancelledPayment() {
      return this.paymentInfo?.payment_status === "failed" && this.paymentInfo?.payment_reference === CUSTOMER_CANCELLED_REFERENCE;
    },
    paymentStatusTone() {
      const status = this.paymentInfo?.payment_status;
      if (status === "paid") return "paid";
      if (status === "expired" || status === "failed") return "failed";
      return "pending";
    },
    paymentStatusLabelDisplay() {
      const status = this.paymentInfo?.payment_status;
      if (status === "paid") return "Đã thanh toán";
      if (status === "expired") return "Đã hết hạn";
      if (this.isCancelledPayment) return "Đã hủy";
      if (status === "failed") return "Thanh toán thất bại";
      return "Đang chờ thanh toán";
    },
    showRegenerateButton() {
      return ["expired", "failed"].includes(this.paymentInfo?.payment_status);
    },
    hasActivePendingInvoice() {
      return this.paymentInfo?.payment_status === "pending";
    },
    primaryButtonIcon() {
      if (this.showRegenerateButton) return "fa-rotate-right";
      if (this.hasActivePendingInvoice) return "fa-hourglass-half";
      if (this.paymentInfo?.payment_status === "paid") return "fa-circle-check";
      return "fa-qrcode";
    },
    primaryButtonDisabled() {
      if (this.processing || this.loadingPage || this.cancellingPayment || !this.schedule) return true;
      return this.hasActivePendingInvoice || this.paymentInfo?.payment_status === "paid";
    },
    primaryButtonLabelDisplay() {
      if (this.showRegenerateButton) return "Tạo mã QR mới";
      if (this.hasActivePendingInvoice) return "Đang chờ thanh toán";
      if (this.paymentInfo?.payment_status === "paid") return "Đã thanh toán";
      return "Tạo hóa đơn thanh toán";
    },
    countdownLabelDisplay() {
      const expiresAt = this.paymentInfo?.payment_expires_at;
      if (!expiresAt) return "--";

      const expiresTimestamp = new Date(expiresAt).getTime();
      if (!Number.isFinite(expiresTimestamp)) return "--";

      const diffMs = expiresTimestamp - this.countdownNow;
      if (diffMs <= 0) return "QR đã hết hạn";

      const totalSeconds = Math.max(0, Math.ceil(diffMs / 1000));
      const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, "0");
      const seconds = String(totalSeconds % 60).padStart(2, "0");
      return `Còn lại ${minutes}:${seconds}`;
    },
    finalTotalAmount() {
      const baseAmount = this.paymentInfo?.so_tien ?? (((this.schedule?.soTien || this.tour?.soTien) ?? 0) * this.soLuongKhach);
      return Math.max(0, baseAmount - this.discountAmount);
    }
  },
  mounted() {
    this.taiDuLieuTrang();
  },
  beforeUnmount() {
    this.stopPolling();
    this.stopCountdown();
  },
  methods: {
    layGiaTriDauTien(obj, keys, fallback = "") {
      for (const key of keys) {
        if (obj && obj[key] !== undefined && obj[key] !== null && obj[key] !== "") {
          return obj[key];
        }
      }
      return fallback;
    },
    dinhDangTien(value) {
      const amount = Number(value);
      if (!Number.isFinite(amount) || amount <= 0) return "--";
      return `${amount.toLocaleString("vi-VN")} đ`;
    },
    dinhDangNgayGio(value) {
      if (!value) return "--";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return value;
      const time = date.toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit" });
      const day = date.toLocaleDateString("vi-VN");
      return `${time} ${day}`;
    },
    resolveImageUrl(rawUrl) {
      if (!rawUrl) return HINH_MAC_DINH;
      if (rawUrl.startsWith("http") || rawUrl.startsWith("data:")) return rawUrl;
      return `${API_ORIGIN}/${rawUrl.replace(/^\/+/, "")}`;
    },
    normalizeTour(item = {}) {
      return {
        maTour: String(this.layGiaTriDauTien(item, ["ma_tour", "Ma_tour"], this.maTour)),
        ten: this.layGiaTriDauTien(item, ["ten_tour", "Ten_tour"], "Tour đang cập nhật"),
        moTa: this.layGiaTriDauTien(item, ["mo_ta", "Mo_ta"], ""),
        soTien: Number(this.layGiaTriDauTien(item, ["so_tien", "So_tien"], 0)),
        hinhAnh: this.resolveImageUrl(this.layGiaTriDauTien(item, ["hinh_anh", "Hinh_anh"], HINH_MAC_DINH)),
      };
    },
    normalizeSchedule(item = {}) {
      const ngayBatDau = this.layGiaTriDauTien(item, ["ngay_bat_dau", "Ngay_bat_dau"]);
      const date = ngayBatDau ? new Date(ngayBatDau) : null;

      return {
        maThoiGianTour: String(this.layGiaTriDauTien(item, ["ma_thoi_gian_tour", "Ma_thoi_gian_tour"], "")),
        maTour: String(this.layGiaTriDauTien(item, ["ma_tour", "Ma_tour"], this.maTour)),
        ngayBatDau,
        ngayBatDauHienThi: date && !Number.isNaN(date.getTime()) ? date.toLocaleDateString("vi-VN") : "--",
        soCho: Number(this.layGiaTriDauTien(item, ["so_cho", "So_cho"], 0)),
        soTien: Number(this.layGiaTriDauTien(item, ["so_tien", "So_tien"], 0)),
        tinhTrang: Boolean(this.layGiaTriDauTien(item, ["tinh_trang", "Tinh_trang"], false)),
      };
    },
    normalizePaymentInfo(item = {}) {
      return {
        ma_hoa_don: this.layGiaTriDauTien(item, ["ma_hoa_don"], ""),
        ma_tour: this.layGiaTriDauTien(item, ["ma_tour"], this.maTour),
        ten_tour: this.layGiaTriDauTien(item, ["ten_tour"], ""),
        ma_thoi_gian_tour: this.layGiaTriDauTien(item, ["ma_thoi_gian_tour"], ""),
        ma_nhom: this.layGiaTriDauTien(item, ["ma_nhom"], ""),
        so_tien: Number(this.layGiaTriDauTien(item, ["so_tien"], 0)),
        payment_method: this.layGiaTriDauTien(item, ["payment_method"], "vietqr_bank_transfer"),
        payment_status: this.layGiaTriDauTien(item, ["payment_status"], "pending"),
        payment_reference: this.layGiaTriDauTien(item, ["payment_reference"], ""),
        noi_dung_chuyen_khoan: this.layGiaTriDauTien(item, ["noi_dung_chuyen_khoan"], ""),
        qr_url: this.layGiaTriDauTien(item, ["qr_url"], ""),
        payment_expires_at: this.layGiaTriDauTien(item, ["payment_expires_at"], ""),
        ten_nguoi_dat: this.layGiaTriDauTien(item, ["ten_nguoi_dat"], ""),
        email_nguoi_dat: this.layGiaTriDauTien(item, ["email_nguoi_dat"], ""),
        so_dien_thoai_nguoi_dat: this.layGiaTriDauTien(item, ["so_dien_thoai_nguoi_dat"], ""),
        dia_chi_nguoi_dat: this.layGiaTriDauTien(item, ["dia_chi_nguoi_dat"], ""),
      };
    },
    prefillForm(invoiceInfo = null) {
      const user = getStoredUser() || {};
      this.form.fullName = invoiceInfo?.ten_nguoi_dat || this.layGiaTriDauTien(user, ["Ho_va_ten", "ho_va_ten", "full_name", "name"], "");
      this.form.phone = invoiceInfo?.so_dien_thoai_nguoi_dat || this.layGiaTriDauTien(user, ["so_dien_thoai", "phone", "So_dien_thoai"], "");
      this.form.email = invoiceInfo?.email_nguoi_dat || this.layGiaTriDauTien(user, ["Email", "email"], "");
      this.form.address = invoiceInfo?.dia_chi_nguoi_dat || this.form.address;
    },
    redirectMissingSchedule(message) {
      this.$router.replace({
        path: `/khach-hang/tour/${this.maTour}`,
        query: {
          notice: message,
          notice_type: "error",
        },
      });
    },
    async taiDuLieuTrang() {
      const maKhachHang = getStoredCustomerId();
      if (!maKhachHang) {
        this.$router.replace("/dang-nhap");
        return;
      }

      this.loadingPage = true;
      // cleared errorMessage
      this.statusMessage = "";

      try {
        let invoiceInfo = null;
        let maThoiGianTour = this.scheduleQuery;

        if (this.invoiceQuery) {
          const invoicePayload = await fetchJson(`/api/khach-hang/hoa-don/${this.invoiceQuery}/payment-status`);
          invoiceInfo = this.normalizePaymentInfo(invoicePayload?.data || {});
          this.paymentInfo = invoiceInfo;
          maThoiGianTour = maThoiGianTour || invoiceInfo.ma_thoi_gian_tour;
        }

        if (!maThoiGianTour) {
          this.redirectMissingSchedule("Vui lòng chọn lịch khởi hành trước khi thanh toán.");
          return;
        }

        const [tourPayload, schedulePayload] = await Promise.all([
          fetchJson(`${TOUR_API}/${this.maTour}`),
          fetchJson(`${TOUR_KHOI_HANH_API}/${maThoiGianTour}`),
        ]);

        this.tour = this.normalizeTour(tourPayload?.data || {});
        this.schedule = this.normalizeSchedule(schedulePayload?.data || {});

        if (this.schedule.maTour !== this.maTour) {
          this.redirectMissingSchedule("Lịch khởi hành không thuộc tour bạn đang đặt.");
          return;
        }

        this.prefillForm(invoiceInfo);

        if (this.paymentInfo?.payment_status === "pending") {
          this.startPolling();
          this.startCountdown();
        }
      } catch (error) {
        toaster.error(error?.response?.data?.message || error?.message || "Không thể tải dữ liệu thanh toán.");
      } finally {
        this.loadingPage = false;
      }
    },
    validateForm() {
      if (!this.form.fullName || !this.form.phone || !this.form.email) {
        toaster.error("Vui lòng điền đầy đủ họ tên, số điện thoại và email.");
        return false;
      }

      if (!this.schedule?.maThoiGianTour) {
        toaster.error("Không tìm thấy lịch khởi hành hợp lệ.");
        return false;
      }

      return true;
    },
    async xuLyNutThanhToan() {
      if (this.showRegenerateButton && this.paymentInfo?.ma_hoa_don) {
        await this.taoLaiThanhToanQr();
        return;
      }

      await this.taoThanhToanQr();
    },
    async taoThanhToanQr() {
      if (!this.validateForm()) return;

      this.processing = true;
      // cleared errorMessage
      this.statusMessage = "";

      try {
        const payload = await fetchJson(`/api/khach-hang/tour/${this.maTour}/thanh-toan/qr`, {
          method: "POST",
          body: {
            ma_thoi_gian_tour: this.schedule.maThoiGianTour,
            ma_voucher: this.selectedVoucher ? this.selectedVoucher.ma_voucher : null,
            so_luong_khach: this.soLuongKhach,
            thong_tin_nguoi_dat: {
              ho_ten: this.form.fullName,
              so_dien_thoai: this.form.phone,
              email: this.form.email,
              dia_chi: this.form.address,
            },
          },
        });

        this.paymentInfo = this.normalizePaymentInfo(payload?.data || {});
        this.statusMessage = "Đã tạo hóa đơn thanh toán. Bạn có thể quét QR ngay bây giờ.";

        await this.$router.replace({
          path: this.$route.path,
          query: {
            schedule: this.schedule.maThoiGianTour,
            invoice: this.paymentInfo.ma_hoa_don,
          },
        });

        this.startPolling();
        this.startCountdown();
      } catch (error) {
        toaster.error(error?.response?.data?.message || error?.message || "Không thể tạo hóa đơn QR.");
      } finally {
        this.processing = false;
      }
    },
    async taoLaiThanhToanQr() {
      if (!this.paymentInfo?.ma_hoa_don) return;

      this.processing = true;
      // cleared errorMessage
      this.statusMessage = "";

      try {
        const payload = await fetchJson(`/api/khach-hang/hoa-don/${this.paymentInfo.ma_hoa_don}/retry-payment`, {
          method: "POST",
        });

        this.paymentInfo = this.normalizePaymentInfo(payload?.data || {});
        this.statusMessage = "Đã tạo lại mã QR cho cùng một mã hóa đơn.";
        this.startPolling();
        this.startCountdown();
      } catch (error) {
        toaster.error(error?.response?.data?.message || error?.message || "Không thể tạo lại mã QR.");
      } finally {
        this.processing = false;
      }
    },
    async huyThanhToan() {
      if (!this.paymentInfo?.ma_hoa_don || this.paymentInfo?.payment_status !== "pending") return;

      const confirmed = await showConfirm({
        title: "Hủy thanh toán QR",
        message: "Bạn có chắc muốn hủy thanh toán hóa đơn này không?",
        tone: "warning"
      });
      if (!confirmed) return;

      this.cancellingPayment = true;
      // cleared errorMessage
      this.statusMessage = "";

      try {
        const payload = await fetchJson(`/api/khach-hang/hoa-don/${this.paymentInfo.ma_hoa_don}/cancel-payment`, {
          method: "POST",
        });

        this.paymentInfo = this.normalizePaymentInfo(payload?.data || {});
        this.statusMessage = "Đã hủy thanh toán. Bạn có thể tạo lại mã QR khi cần.";
        toaster.success("Đã hủy thanh toán thành công.");
        this.stopPolling();
        this.stopCountdown();
      } catch (error) {
        toaster.error(error?.response?.data?.message || error?.message || "Không thể hủy thanh toán lúc này.");
      } finally {
        this.cancellingPayment = false;
      }
    },
    async refreshPaymentStatus(silent = true) {
      if (!this.paymentInfo?.ma_hoa_don) return;

      if (!silent) {
        this.checkingStatus = true;
      }

      try {
        const payload = await fetchJson(`/api/khach-hang/hoa-don/${this.paymentInfo.ma_hoa_don}/payment-status`);
        this.paymentInfo = this.normalizePaymentInfo(payload?.data || {});

        if (this.paymentInfo.payment_status === "paid") {
          this.statusMessage = "Hóa đơn đã được xác nhận thanh toán thành công.";
          if (!silent) toaster.success("Thanh toán thành công!");
          this.stopPolling();
          this.stopCountdown();
        } else if (["expired", "failed"].includes(this.paymentInfo.payment_status)) {
          if (!silent) toaster.warning("Hóa đơn đã hết hạn hoặc bị hủy.");
          this.stopPolling();
          this.stopCountdown();
        } else if (this.paymentInfo.payment_status === "pending") {
          if (!silent) toaster.info("Hóa đơn vẫn đang chờ thanh toán.");
          this.startCountdown();
        }
      } catch (error) {
        if (!silent) {
          toaster.error(error?.response?.data?.message || error?.message || "Không thể kiểm tra trạng thái thanh toán.");
        }
      } finally {
        if (!silent) {
          this.checkingStatus = false;
        }
      }
    },
    startPolling() {
      this.stopPolling();

      if (this.paymentInfo?.payment_status !== "pending") return;

      this.pollingTimer = window.setInterval(() => {
        this.refreshPaymentStatus(true);
      }, 8000);
    },
    stopPolling() {
      if (!this.pollingTimer) return;
      window.clearInterval(this.pollingTimer);
      this.pollingTimer = null;
    },
    startCountdown() {
      this.stopCountdown();
      this.countdownNow = Date.now();

      if (this.paymentInfo?.payment_status !== "pending" || !this.paymentInfo?.payment_expires_at) return;

      this.countdownTimer = window.setInterval(() => {
        this.countdownNow = Date.now();
        const expiresTimestamp = new Date(this.paymentInfo?.payment_expires_at || "").getTime();
        if (Number.isFinite(expiresTimestamp) && expiresTimestamp <= this.countdownNow) {
          this.stopCountdown();
          this.refreshPaymentStatus(true);
        }
      }, 1000);
    },
    stopCountdown() {
      if (!this.countdownTimer) return;
      window.clearInterval(this.countdownTimer);
      this.countdownTimer = null;
    },
    async copyTransferContent() {
      if (!this.paymentInfo?.noi_dung_chuyen_khoan) return;

      try {
        await navigator.clipboard.writeText(this.paymentInfo.noi_dung_chuyen_khoan);
        this.statusMessage = "Đã sao chép nội dung chuyển khoản.";
      } catch {
        toaster.error("Không thể sao chép nội dung chuyển khoản trên trình duyệt này.");
      }
    },
    goToInvoiceDetail() {
      if (!this.paymentInfo?.ma_hoa_don) return;
      this.$router.push(`/khach-hang/hoa-don/${this.paymentInfo.ma_hoa_don}`);
    },
    async openVoucherModal() {
      this.showVoucherModal = true;
      this.loadingVouchers = true;
      try {
        const payload = await fetchJson(`/api/khach-hang/voucher/available?ma_tour=${this.maTour}&tong_tien=${((this.schedule?.soTien || this.tour?.soTien) || 0) * this.soLuongKhach}`);
        if (payload.success) {
          this.availableVouchers = payload.data;
        }
      } catch (error) {
        toaster.error("Không thể tải danh sách mã giảm giá.");
      } finally {
        this.loadingVouchers = false;
      }
    },
    async applyVoucher(voucher) {
      try {
        const payload = await fetchJson(`/api/khach-hang/voucher/apply`, {
          method: 'POST',
          body: {
            ma_voucher: voucher.ma_voucher,
            ma_tour: this.maTour,
            tong_tien: ((this.schedule?.soTien || this.tour?.soTien) || 0) * this.soLuongKhach
          }
        });

        if (payload.success) {
          this.selectedVoucher = payload.data.voucher;
          this.discountAmount = payload.data.tien_giam_gia;
          this.showVoucherModal = false;
          toaster.success("Áp dụng mã giảm giá thành công!");
        }
      } catch (error) {
        toaster.error(error?.response?.data?.message || "Không thể áp dụng mã giảm giá.");
      }
    },
    removeVoucher() {
      this.selectedVoucher = null;
      this.discountAmount = 0;
    },
    handleSoLuongChange() {
      if (this.selectedVoucher) {
        this.removeVoucher();
        toaster.info("Vui lòng chọn lại mã giảm giá do thay đổi số lượng khách.");
      }
    },
    giamSoLuong() {
      if (this.soLuongKhach > 1) {
        this.soLuongKhach--;
        this.handleSoLuongChange();
      }
    },
    tangSoLuong() {
      const max = this.schedule?.soCho || 1;
      if (this.soLuongKhach < max) {
        this.soLuongKhach++;
        this.handleSoLuongChange();
      }
    },
    kiemTraSoLuong() {
      const max = this.schedule?.soCho || 1;
      if (this.soLuongKhach < 1 || isNaN(this.soLuongKhach)) {
        this.soLuongKhach = 1;
      } else if (this.soLuongKhach > max) {
        this.soLuongKhach = max;
        toaster.warning(`Chỉ còn ${max} chỗ trống.`);
      }
      this.handleSoLuongChange();
    }
  },
};
</script>

<style scoped>
.quantity-selector {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
}
.quantity-selector button {
  background: #f8f9fa;
  border: none;
  width: 28px;
  height: 28px;
  font-weight: bold;
  cursor: pointer;
  color: #333;
}
.quantity-selector button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.quantity-selector input {
  width: 40px;
  border: none;
  text-align: center;
  font-weight: bold;
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  height: 28px;
  -moz-appearance: textfield;
}
.quantity-selector input::-webkit-outer-spin-button, 
.quantity-selector input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.checkout-page {
  min-height: 100vh;
  background:
    radial-gradient(circle at top right, rgba(3, 105, 161, 0.18), transparent 24%),
    linear-gradient(180deg, #edf6ff 0%, #f7fafc 100%);
  color: #0f172a;
}

.checkout-workspace {
  width: min(1840px, calc(100% - 64px));
  margin: 0 auto;
  padding: 28px 0 56px;
  display: grid;
  grid-template-columns: 352px minmax(0, 1fr);
  gap: 36px;
  align-items: start;
}

.checkout-shell {
  width: 100%;
  min-width: 0;
  padding: 16px 8px 56px 0;
}

.checkout-header {
  display: grid;
  gap: 14px;
  margin-bottom: 24px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #0369a1;
  text-decoration: none;
  font-weight: 700;
}

.eyebrow {
  margin: 0 0 8px;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: #0f766e;
}

.checkout-header h1 {
  margin: 0;
  font-size: clamp(2rem, 3vw, 3rem);
  font-weight: 900;
  letter-spacing: -0.03em;
}

.subheading {
  margin: 10px 0 0;
  max-width: 720px;
  color: #4b5563;
  line-height: 1.7;
}

.notice {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 15px 18px;
  border-radius: 18px;
  margin-bottom: 18px;
  border: 1px solid transparent;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
  border-color: #fecdd3;
}

.notice--success {
  background: #ecfdf5;
  color: #047857;
  border-color: #a7f3d0;
}

.notice--info {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #bfdbfe;
}

.checkout-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.55fr) 390px;
  gap: 28px;
  align-items: start;
}

.checkout-main {
  display: grid;
  gap: 20px;
}

.panel,
.summary-card {
  background: rgba(255, 255, 255, 0.94);
  border: 1px solid rgba(203, 213, 225, 0.8);
  border-radius: 28px;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
}

.panel {
  padding: 26px;
}

.panel__header {
  display: flex;
  gap: 16px;
  align-items: flex-start;
  margin-bottom: 22px;
}

.panel__icon {
  width: 52px;
  height: 52px;
  border-radius: 18px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #0f766e 0%, #0369a1 100%);
  color: #fff;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.panel__header h2 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 900;
}

.panel__header p {
  margin: 6px 0 0;
  color: #64748b;
  line-height: 1.6;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  display: grid;
  gap: 8px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group span {
  font-size: 0.82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
}

.form-group input {
  width: 100%;
  padding: 14px 16px;
  border-radius: 16px;
  border: 1px solid #dbe4f0;
  background: #f8fbff;
  color: #0f172a;
  font-size: 1rem;
  transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.form-group input:focus {
  outline: none;
  border-color: #0284c7;
  background: #fff;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
}

.payment-method {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 18px;
  border-radius: 22px;
  border: 1px solid #bae6fd;
  background: linear-gradient(135deg, #effcff 0%, #f8fbff 100%);
}

.payment-method__icon {
  width: 54px;
  height: 54px;
  border-radius: 18px;
  display: grid;
  place-items: center;
  background: #dbeafe;
  color: #1d4ed8;
  font-size: 1.3rem;
}

.payment-method__content {
  flex: 1;
  display: grid;
  gap: 6px;
}

.payment-method__content strong {
  font-size: 1.05rem;
}

.payment-method__content span {
  color: #64748b;
  line-height: 1.6;
}

.payment-method__badge {
  padding: 8px 12px;
  border-radius: 999px;
  background: #0f172a;
  color: #fff;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.summary-card {
  overflow: hidden;
  position: sticky;
  top: 24px;
}

.summary-hero {
  position: relative;
  height: 240px;
}

.summary-hero img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.summary-hero__overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 24px;
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.1), rgba(15, 23, 42, 0.88));
  color: #fff;
}

.summary-hero__pill {
  align-self: flex-start;
  margin-bottom: 10px;
  padding: 7px 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.18);
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-weight: 800;
}

.summary-hero__overlay h3 {
  margin: 0;
  font-size: 1.35rem;
  line-height: 1.45;
}

.summary-content {
  padding: 24px;
  display: grid;
  gap: 16px;
}

.summary-item,
.summary-total {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  align-items: flex-start;
}

.summary-item span,
.summary-total span {
  color: #64748b;
}

.summary-item strong,
.summary-total strong {
  text-align: right;
}

.summary-total strong {
  font-size: 1.8rem;
  color: #0369a1;
  letter-spacing: -0.03em;
}

.quantity-selector {
  display: flex;
  align-items: center;
  background: #f1f5f9;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  height: 38px;
}

.quantity-selector button {
  width: 36px;
  height: 100%;
  border: none;
  background: transparent;
  color: #475569;
  font-size: 1.2rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.quantity-selector button:hover:not(:disabled) {
  background: #e2e8f0;
  color: #0f172a;
}

.quantity-selector button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.quantity-selector input {
  width: 48px;
  height: 100%;
  border: none;
  border-left: 1px solid #e2e8f0;
  border-right: 1px solid #e2e8f0;
  background: transparent;
  text-align: center;
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  padding: 0;
}

.quantity-selector input:focus {
  outline: none;
  background: #fff;
}

/* Ẩn nút tăng giảm mặc định của input type number */
.quantity-selector input::-webkit-outer-spin-button,
.quantity-selector input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.quantity-selector input[type=number] {
  -moz-appearance: textfield;
}

.divider {
  width: 100%;
  border: 0;
  border-top: 1px dashed #cbd5e1;
}

.primary-button {
  width: 100%;
  padding: 15px 18px;
  border-radius: 18px;
  font-size: 1rem;
  border: none;
  cursor: pointer;
  font-weight: 800;
  transition: transform 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease;
  background: linear-gradient(135deg, #0f766e 0%, #0369a1 100%);
  color: #fff;
  box-shadow: 0 18px 24px rgba(3, 105, 161, 0.16);
}

.primary-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.primary-button:not(:disabled):hover {
  transform: translateY(-1px);
}

.summary-note {
  margin: 0;
  color: #64748b;
  line-height: 1.7;
  font-size: 0.92rem;
}

@media (max-width: 1080px) {
  .checkout-workspace {
    grid-template-columns: 1fr;
  }

  .checkout-grid {
    grid-template-columns: 1fr;
  }

  .summary-card {
    position: static;
  }
}

@media (max-width: 640px) {
  .checkout-shell {
    width: min(100%, calc(100% - 20px));
    padding-top: 24px;
  }

  .checkout-workspace {
    width: min(100%, calc(100% - 18px));
    gap: 18px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .panel,
  .summary-content {
    padding: 20px;
  }
}

/* Voucher styles */
.voucher-section {
  flex-direction: column;
}
.voucher-header {
  display: flex;
  justify-content: space-between;
  width: 100%;
}
.btn-select-voucher {
  background: none;
  border: none;
  color: #0369a1;
  font-weight: 700;
  cursor: pointer;
  padding: 0;
}
.btn-select-voucher:disabled {
  color: #94a3b8;
  cursor: not-allowed;
}
.btn-remove-voucher {
  background: none;
  border: none;
  color: #ef4444;
  font-size: 0.8rem;
  padding: 0;
  cursor: pointer;
  margin-top: 4px;
}
.btn-remove-voucher:disabled {
  display: none;
}
.voucher-list {
  max-height: 400px;
  overflow-y: auto;
}
.voucher-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  border-bottom: 1px solid #e2e8f0;
}
.voucher-icon {
  width: 40px;
  height: 40px;
  background: #f1f5f9;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #0369a1;
}
.voucher-details {
  flex: 1;
}
.voucher-details strong {
  display: block;
  font-size: 1.1rem;
}
.voucher-details p {
  margin: 0;
  font-size: 0.9rem;
}
.invalid-voucher {
  opacity: 0.6;
}
</style>
