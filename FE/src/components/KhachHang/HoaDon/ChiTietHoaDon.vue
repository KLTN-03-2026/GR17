<template>
  <div class="plan-page">
    <div class="plan-layout">
      <CustomerSidebar />

      <section class="plan-content">
        <div class="customer-invoice-page">
          <header class="invoice-hero">
            <div>
              <p class="invoice-hero__eyebrow">Hóa đơn khách hàng</p>
              <h1>Chi tiết hóa đơn</h1>
              <p>Tập trung xem lại thông tin tour, lịch khởi hành, giao dịch và đối chiếu thanh toán trên cùng một màn hình.</p>
            </div>

            <div class="invoice-hero__actions">
              <router-link class="invoice-hero__button invoice-hero__button--ghost" to="/khach-hang/lich-su-don-hang">
                Quay lại đơn hàng
              </router-link>

              <button
                v-if="canCancelPayment"
                type="button"
                class="invoice-hero__button invoice-hero__button--danger"
                :disabled="cancelling"
                @click="huyThanhToan"
              >
                <i class="fas" :class="cancelling ? 'fa-spinner fa-spin' : 'fa-ban'"></i>
                Hủy thanh toán
              </button>

              <router-link
                v-if="primaryActionRoute"
                class="invoice-hero__button invoice-hero__button--primary"
                :to="primaryActionRoute"
              >
                {{ primaryActionLabel }}
              </router-link>
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

          <div v-if="loading" class="notice notice--info">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Đang tải chi tiết hóa đơn...</span>
          </div>

          <template v-else-if="invoice">
            <section class="invoice-meta-grid">
              <article class="invoice-meta-card">
                <span>Mã hóa đơn</span>
                <strong>{{ invoice.ma_hoa_don }}</strong>
              </article>
              <article class="invoice-meta-card">
                <span>Trạng thái</span>
                <strong :class="`invoice-status invoice-status--${invoice.statusTone}`">{{ invoice.statusLabel }}</strong>
              </article>
              <article class="invoice-meta-card">
                <span>Số tiền</span>
                <strong>{{ dinhDangTien(invoice.so_tien) }}</strong>
              </article>
              <article class="invoice-meta-card">
                <span>Thời điểm</span>
                <strong>{{ dinhDangNgayGio(invoice.paid_at || invoice.created_at) }}</strong>
              </article>
            </section>

            <section class="invoice-grid">
              <article class="invoice-card">
                <header class="invoice-card__header">
                  <p class="invoice-card__eyebrow">Tour và lịch khởi hành</p>
                  <h2>{{ invoice.ten_tour || "Đơn tour đang cập nhật" }}</h2>
                </header>

                <dl class="invoice-detail-list">
                  <div>
                    <dt>Mã tour</dt>
                    <dd>{{ invoice.ma_tour || "--" }}</dd>
                  </div>
                  <div>
                    <dt>Mã lịch khởi hành</dt>
                    <dd>{{ invoice.ma_thoi_gian_tour || "--" }}</dd>
                  </div>
                  <div>
                    <dt>Mã nhóm</dt>
                    <dd>{{ invoice.ma_nhom || "--" }}</dd>
                  </div>
                  <div>
                    <dt>Ngày tạo hóa đơn</dt>
                    <dd>{{ dinhDangNgayGio(invoice.created_at) }}</dd>
                  </div>
                </dl>
              </article>

              <article class="invoice-card">
                <header class="invoice-card__header">
                  <p class="invoice-card__eyebrow">Người đặt</p>
                  <h2>{{ invoice.ten_nguoi_dat || invoice.customer_summary?.ho_ten || "Khách hàng" }}</h2>
                </header>

                <dl class="invoice-detail-list">
                  <div>
                    <dt>Email</dt>
                    <dd>{{ invoice.email_nguoi_dat || invoice.customer_summary?.email || "--" }}</dd>
                  </div>
                  <div>
                    <dt>Số điện thoại</dt>
                    <dd>{{ invoice.so_dien_thoai_nguoi_dat || invoice.customer_summary?.so_dien_thoai || "--" }}</dd>
                  </div>
                  <div>
                    <dt>Địa chỉ</dt>
                    <dd>{{ invoice.dia_chi_nguoi_dat || "--" }}</dd>
                  </div>
                  <div>
                    <dt>Mã khách hàng</dt>
                    <dd>{{ invoice.ma_khach_hang_dat || "--" }}</dd>
                  </div>
                </dl>
              </article>

              <article class="invoice-card invoice-card--highlight">
                <header class="invoice-card__header">
                  <p class="invoice-card__eyebrow">Thanh toán</p>
                  <h2>Thông tin đối chiếu</h2>
                </header>

                <dl class="invoice-detail-list">
                  <div>
                    <dt>Phương thức</dt>
                    <dd>{{ invoice.payment_method || "--" }}</dd>
                  </div>
                  <div>
                    <dt>Nội dung chuyển khoản</dt>
                    <dd>{{ invoice.noi_dung_chuyen_khoan || invoice.ma_hoa_don }}</dd>
                  </div>
                  <div>
                    <dt>Mã tham chiếu</dt>
                    <dd>{{ invoice.payment_reference || invoice.ma_giao_dich || "--" }}</dd>
                  </div>
                  <div>
                    <dt>Thanh toán lúc</dt>
                    <dd>{{ dinhDangNgayGio(invoice.paid_at) }}</dd>
                  </div>
                </dl>
              </article>
            </section>
          </template>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient";
import CustomerSidebar from "../CustomerSidebar.vue";
import { buildCustomerOrderActionRoute, enrichCustomerOrder } from "../customerOrdersShared.js";
import { showConfirm } from "../../../services/appDialog";

async function fetchJson(url, init = {}) {
  const response = await goiApi(url, init);
  const payload = await response.json().catch(() => ({}));

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
  name: "KhachHangChiTietHoaDon",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      loading: false,
      cancelling: false,
      errorMessage: "",
      statusMessage: "",
      invoice: null,
    };
  },
  computed: {
    maHoaDon() {
      return String(this.$route.params.ma_hoa_don || "").trim();
    },
    canCancelPayment() {
      return this.invoice?.payment_method === "vietqr_bank_transfer" && this.invoice?.payment_status === "pending";
    },
    primaryActionRoute() {
      if (!this.invoice) return null;
      if (this.invoice.payment_status === "paid") {
        return { path: "/khach-hang/tour" };
      }
      return buildCustomerOrderActionRoute(this.invoice);
    },
    primaryActionLabel() {
      if (!this.invoice) return "Mở hóa đơn";
      if (this.invoice.payment_status === "paid") return "Đặt thêm tour";
      return this.invoice.actionLabel;
    },
  },
  mounted() {
    this.taiHoaDon();
  },
  methods: {
    dinhDangTien(value) {
      const amount = Number(value);
      if (!Number.isFinite(amount) || amount <= 0) return "--";
      return `${amount.toLocaleString("vi-VN")} đ`;
    },
    dinhDangNgayGio(value) {
      if (!value) return "--";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "--";
      return date.toLocaleString("vi-VN", {
        hour: "2-digit",
        minute: "2-digit",
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    },
    async taiHoaDon() {
      if (!this.maHoaDon) {
        toaster.error("Không tìm thấy mã hóa đơn.");
        return;
      }

      this.loading = true;
      // cleared errorMessage

      try {
        const payload = await fetchJson(`/api/khach-hang/hoa-don/${this.maHoaDon}`);
        this.invoice = enrichCustomerOrder(payload?.data || {});
      } catch (error) {
        toaster.error(error?.response?.data?.message || error?.message || "Không thể tải chi tiết hóa đơn.");
      } finally {
        this.loading = false;
      }
    },
    async huyThanhToan() {
      if (!this.canCancelPayment) return;

      const confirmed = await showConfirm({
        title: "Xác nhận hủy",
        message: "Bạn có chắc muốn hủy thanh toán hóa đơn này không?",
        tone: "warning"
      });
      if (!confirmed) return;

      this.cancelling = true;
      // cleared errorMessage
      this.statusMessage = "";

      try {
        const payload = await fetchJson(`/api/khach-hang/hoa-don/${this.maHoaDon}/cancel-payment`, {
          method: "POST",
        });

        this.invoice = enrichCustomerOrder(payload?.data || {});
        this.statusMessage = "Đã hủy thanh toán hóa đơn.";
      } catch (error) {
        toaster.error(error?.response?.data?.message || error?.message || "Không thể hủy thanh toán lúc này.");
      } finally {
        this.cancelling = false;
      }
    },
  },
};
</script>

<style scoped>
.customer-invoice-page {
  display: grid;
  gap: 24px;
}

.notice {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 15px 18px;
  border-radius: 18px;
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

.invoice-hero,
.invoice-meta-card,
.invoice-card {
  border: 1px solid #dbe7f3;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.94);
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
}

.invoice-hero {
  padding: 28px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 20px;
}

.invoice-hero__eyebrow,
.invoice-card__eyebrow {
  margin: 0 0 10px;
  color: #4f46e5;
  font-size: 0.78rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.16em;
}

.invoice-hero h1,
.invoice-card h2 {
  margin: 0;
  color: #0f172a;
  font-weight: 900;
  letter-spacing: -0.04em;
}

.invoice-hero p:not(.invoice-hero__eyebrow),
.invoice-card__header p:not(.invoice-card__eyebrow) {
  margin: 10px 0 0;
  color: #64748b;
  line-height: 1.7;
}

.invoice-hero__actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.invoice-hero__button {
  min-height: 48px;
  padding: 0 18px;
  border-radius: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-decoration: none;
  font-weight: 800;
  border: none;
  cursor: pointer;
}

.invoice-hero__button--primary {
  background: linear-gradient(135deg, #0f766e 0%, #2563eb 100%);
  color: #fff;
}

.invoice-hero__button--ghost {
  border: 1px solid #dbeafe;
  background: #fff;
  color: #0f172a;
}

.invoice-hero__button--danger {
  background: rgba(244, 63, 94, 0.12);
  color: #be123c;
  border: 1px solid rgba(244, 63, 94, 0.24);
}

.invoice-hero__button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.invoice-meta-grid,
.invoice-grid {
  display: grid;
  gap: 18px;
}

.invoice-meta-grid {
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.invoice-meta-card,
.invoice-card {
  padding: 22px;
}

.invoice-meta-card span,
.invoice-detail-list dt {
  display: block;
  color: #64748b;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-weight: 800;
}

.invoice-meta-card strong,
.invoice-detail-list dd {
  margin: 8px 0 0;
  color: #0f172a;
  font-size: 1.1rem;
  font-weight: 800;
}

.invoice-status--paid {
  color: #047857 !important;
}

.invoice-status--pending {
  color: #b45309 !important;
}

.invoice-status--expired,
.invoice-status--failed {
  color: #be123c !important;
}

.invoice-grid {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.invoice-card--highlight {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(240, 249, 255, 0.96));
}

.invoice-card__header {
  margin-bottom: 18px;
}

.invoice-detail-list {
  display: grid;
  gap: 14px;
}

.invoice-detail-list div {
  padding-bottom: 14px;
  border-bottom: 1px solid #eef2f7;
}

.invoice-detail-list div:last-child {
  padding-bottom: 0;
  border-bottom: 0;
}

@media (max-width: 1180px) {
  .invoice-meta-grid,
  .invoice-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {
  .invoice-hero {
    flex-direction: column;
  }

  .invoice-hero__actions {
    width: 100%;
    justify-content: stretch;
  }

  .invoice-hero__button {
    flex: 1 1 100%;
  }

  .invoice-meta-grid,
  .invoice-grid {
    grid-template-columns: 1fr;
  }
}
</style>
