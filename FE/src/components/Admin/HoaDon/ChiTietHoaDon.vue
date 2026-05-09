<template>
  <div class="invoice-detail-page">
    <section class="hero card">
      <button class="back-button" type="button" @click="quayLai">
        <i class="fas fa-arrow-left"></i>
      </button>

      <div class="hero-copy">
        <p class="eyebrow">Chi tiết hóa đơn quản trị</p>
        <h1>Chi tiết hóa đơn</h1>
        <p>Mã hóa đơn: <strong>{{ invoiceId }}</strong></p>
      </div>

      <button class="ghost-button" type="button" @click="taiHoaDon" :disabled="dangTai">
        <i class="fas fa-rotate-right"></i>
        <span>{{ dangTai ? "Đang tải..." : "Tải lại dữ liệu" }}</span>
      </button>
    </section>

    <div v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </div>

    <section v-if="dangTai" class="card loading-state">
      <i class="fas fa-spinner fa-spin"></i>
      <span>Đang tải chi tiết hóa đơn...</span>
    </section>

    <template v-else-if="invoice">
      <section class="banner card">
        <div>
          <p class="eyebrow">Tổng thanh toán</p>
          <h2>{{ formatCurrency(invoice.so_tien || invoice.tong_tien) }}</h2>
          <div class="tag-row">
            <span class="tag tag--slate">{{ invoice.loai_hoa_don === 0 ? "Đặt tour" : "Dịch vụ" }}</span>
            <span :class="['tag', `tag--${paymentStatus(invoice)}`]">{{ getStatusLabel(invoice.payment_status) }}</span>
            <span class="tag tag--sky">{{ paymentMethodLabel(invoice.payment_method) }}</span>
          </div>
        </div>

        <div class="banner-meta">
          <div>
            <span>Ngày tạo</span>
            <strong>{{ formatDate(invoice.ngay_tao || invoice.created_at) }}</strong>
          </div>
          <div>
            <span>Thanh toán lúc</span>
            <strong>{{ formatDate(invoice.paid_at) }}</strong>
          </div>
        </div>
      </section>

      <section class="detail-grid">
        <article class="card">
          <header class="card-head">
            <div class="icon icon--blue"><i class="fas fa-route"></i></div>
            <div>
              <h3>Thông tin tour</h3>
              <p>Thông tin đối tượng được gắn với hóa đơn.</p>
            </div>
          </header>

          <div class="info-grid">
            <div class="info-row">
              <span>Tên tour</span>
              <strong>{{ invoice.ten_tour || "Đang cập nhật" }}</strong>
            </div>
            <div class="info-row">
              <span>Mã tour</span>
              <strong>{{ invoice.ma_tour || invoice.ma_doi_tuong || "---" }}</strong>
            </div>
            <div class="info-row">
              <span>Mã lịch khởi hành</span>
              <strong>{{ invoice.ma_thoi_gian_tour || "---" }}</strong>
            </div>
            <div class="info-row">
              <span>Mã nhóm</span>
              <strong>{{ invoice.ma_nhom || "---" }}</strong>
            </div>
          </div>
        </article>

        <article class="card">
          <header class="card-head">
            <div class="icon icon--amber"><i class="fas fa-user"></i></div>
            <div>
              <h3>Thông tin khách đặt</h3>
              <p>Ảnh chụp thông tin khách hàng được lưu tại thời điểm tạo hóa đơn.</p>
            </div>
          </header>

          <div class="info-grid">
            <div class="info-row">
              <span>Họ tên</span>
              <strong>{{ invoice.customer_summary?.ho_ten || invoice.ten_nguoi_dat || "---" }}</strong>
            </div>
            <div class="info-row">
              <span>Email</span>
              <strong>{{ invoice.customer_summary?.email || invoice.email_nguoi_dat || "---" }}</strong>
            </div>
            <div class="info-row">
              <span>Số điện thoại</span>
              <strong>{{ invoice.customer_summary?.so_dien_thoai || invoice.so_dien_thoai_nguoi_dat || "---" }}</strong>
            </div>
            <div class="info-row">
              <span>Địa chỉ</span>
              <strong>{{ invoice.dia_chi_nguoi_dat || "---" }}</strong>
            </div>
          </div>
        </article>

        <article class="card">
          <header class="card-head">
            <div class="icon icon--violet"><i class="fas fa-qrcode"></i></div>
            <div>
              <h3>Thông tin thanh toán</h3>
              <p>Thông tin đối soát QR và giao dịch ngân hàng.</p>
            </div>
          </header>

          <div class="info-grid">
            <div class="info-row">
              <span>Mã tham chiếu</span>
              <strong>{{ paymentReference(invoice) }}</strong>
            </div>
            <div class="info-row">
              <span>Nội dung chuyển khoản</span>
              <strong>{{ invoice.noi_dung_chuyen_khoan || "---" }}</strong>
            </div>
            <div class="info-row">
              <span>Hạn QR</span>
              <strong>{{ formatDate(invoice.payment_expires_at) }}</strong>
            </div>
            <div class="info-row">
              <span>Trạng thái QR gần nhất</span>
              <strong>{{ invoice.latest_qr_payment_summary?.trang_thai || "---" }}</strong>
            </div>
          </div>
        </article>

        <article class="card">
          <header class="card-head">
            <div class="icon icon--green"><i class="fas fa-sliders"></i></div>
            <div>
              <h3>Quản lý trạng thái</h3>
              <p>Hóa đơn QR chỉ đọc, hóa đơn thủ công vẫn có thể cập nhật theo trạng thái cũ.</p>
            </div>
          </header>

          <div v-if="isQrInvoice" class="readonly-panel">
            <i class="fas fa-lock"></i>
            <div>
              <strong>Hóa đơn QR bị khóa thao tác tay</strong>
              <p>Trạng thái sẽ được đồng bộ từ webhook SePay hoặc luồng tạo lại QR của khách hàng.</p>
            </div>
          </div>

          <div v-else class="edit-panel">
            <label class="panel-label">Cập nhật trạng thái thủ công</label>
            <div class="edit-row">
              <select v-model="editStatus" class="field">
                <option :value="0">Chờ xử lý</option>
                <option :value="1">Đã thanh toán</option>
                <option :value="2">Thất bại / hủy</option>
              </select>
              <button class="primary-button" type="button" @click="luuTrangThaiHoaDon" :disabled="dangLuu || editStatus === invoice.trang_thai_thanh_toan">
                <i v-if="dangLuu" class="fas fa-spinner fa-spin"></i>
                <span v-else>Cập nhật</span>
              </button>
            </div>
          </div>
        </article>
      </section>
    </template>

    <section v-else class="card empty-state">
      <i class="fas fa-file-circle-xmark"></i>
      <h2>Không tìm thấy hóa đơn</h2>
      <p>Mã hóa đơn này hiện không tồn tại trong hệ thống.</p>
      <router-link class="detail-link" to="/admin/hoa-don">Quay về danh sách</router-link>
    </section>
  </div>
</template>

<script>
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
  position: "top-right",
  duration: 3000,
});
import { goiApi } from "../../../services/httpClient.js";
import { showConfirm } from "../../../services/appDialog";

export default {
  name: "ChiTietHoaDon",
  data() {
    return {
      dangTai: false,
      dangLuu: false,
      thongBaoLoi: "",
      invoice: null,
      editStatus: 0,
    };
  },
  computed: {
    invoiceId() {
      return this.$route.params.id;
    },
    isQrInvoice() {
      return this.invoice?.payment_method === "vietqr_bank_transfer";
    },
  },
  watch: {
    "$route.params.id": {
      immediate: true,
      handler() {
        this.taiHoaDon();
      },
    },
  },
  methods: {
    paymentStatus(invoiceOrStatus) {
      if (typeof invoiceOrStatus === "string") return invoiceOrStatus;
      return invoiceOrStatus?.payment_status || "pending";
    },
    paymentMethodLabel(method) {
      if (method === "vietqr_bank_transfer") return "QR ngân hàng";
      if (method === "legacy_manual") return "Thủ công / cũ";
      return method || "---";
    },
    paymentReference(invoice) {
      return invoice?.payment_reference || invoice?.ma_giao_dich || invoice?.latest_qr_payment_summary?.reference_code || "---";
    },
    async taiHoaDon() {
      if (!this.invoiceId) return;

      this.dangTai = true;
      this.thongBaoLoi = "";
      try {
        const response = await goiApi(`/api/admin/hoa-don/${this.invoiceId}`, {
          headers: { Accept: "application/json" },
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok || !payload?.success) {
          throw new Error(payload?.message || "Không thể tải chi tiết hóa đơn.");
        }
        this.invoice = payload.data;
        this.editStatus = Number(this.invoice?.trang_thai_thanh_toan ?? 0);
      } catch (error) {
        this.invoice = null;
        this.thongBaoLoi = error?.message || "Không thể tải chi tiết hóa đơn.";
      } finally {
        this.dangTai = false;
      }
    },
    async luuTrangThaiHoaDon() {
      if (!this.invoice || this.isQrInvoice) return;

      const confirmed = await showConfirm({
        title: "Xác nhận cập nhật",
        message: `Bạn có chắc muốn cập nhật hóa đơn ${this.invoice.ma_hoa_don}?`,
        tone: "warning",
        confirmText: "Đồng ý",
        cancelText: "Hủy",
      });
      if (!confirmed) return;

      this.dangLuu = true;
      try {
        const response = await goiApi(`/api/admin/hoa-don/${this.invoice.ma_hoa_don}/status`, {
          method: "PATCH",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            trang_thai_thanh_toan: Number(this.editStatus),
          }),
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok || !payload?.success) {
          throw new Error(payload?.message || "Hệ thống từ chối cập nhật.");
        }

        this.invoice = payload.data;
        this.editStatus = Number(this.invoice?.trang_thai_thanh_toan ?? 0);
        toaster.success(`Hóa đơn ${this.invoice.ma_hoa_don} đã được cập nhật.`);
      } catch (error) {
        toaster.error(error?.message || "Không thể cập nhật hóa đơn.");
      } finally {
        this.dangLuu = false;
      }
    },
    quayLai() {
      this.$router.push("/admin/hoa-don");
    },
    formatCurrency(value) {
      const amount = Number(value);
      if (!Number.isFinite(amount)) return "0 đ";
      return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(amount);
    },
    formatDate(value) {
      if (!value) return "---";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "---";
      return date.toLocaleString("vi-VN", {
        hour: "2-digit",
        minute: "2-digit",
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    },
    getStatusLabel(status) {
      switch (this.paymentStatus(status)) {
        case "paid":
          return "Đã thanh toán";
        case "expired":
          return "Đã hết hạn";
        case "failed":
          return "Thất bại / hủy";
        default:
          return "Đang chờ thanh toán";
      }
    },
  },
};
</script>

<style scoped>
.invoice-detail-page {
  min-height: calc(100vh - 120px);
  padding: 1.5rem;
  background:
    radial-gradient(circle at top left, rgba(37, 99, 235, 0.04), transparent 30%),
    radial-gradient(circle at top right, rgba(236, 72, 153, 0.04), transparent 25%),
    #f8fafc;
}

.card,
.notice {
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid #e2e8f0;
  box-shadow: 0 16px 36px rgba(15, 23, 42, 0.05);
}

.hero,
.banner {
  border-radius: 1.5rem;
  padding: 1.35rem 1.5rem;
}

.hero {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 1rem;
  align-items: center;
}

.back-button,
.ghost-button,
.primary-button,
.field {
  border-radius: 0.95rem;
  border: 1px solid #dbe4f0;
}

.back-button,
.ghost-button,
.primary-button {
  min-height: 2.9rem;
  cursor: pointer;
  font-weight: 800;
}

.back-button {
  width: 2.9rem;
  background: #f1f5f9;
  color: #475569;
}

.ghost-button,
.primary-button {
  padding: 0 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
}

.ghost-button {
  background: #fff;
  color: #334155;
}

.primary-button {
  background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
  color: #fff;
}

.eyebrow {
  margin: 0 0 0.35rem;
  color: #2563eb;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.hero-copy h1,
.banner h2,
.card-head h3,
.empty-state h2 {
  margin: 0;
  color: #0f172a;
  font-weight: 900;
}

.hero-copy p {
  margin: 0.35rem 0 0;
  color: #64748b;
}

.notice {
  margin-top: 1rem;
  border-radius: 1rem;
  padding: 0.95rem 1.1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.loading-state {
  margin-top: 1rem;
  border-radius: 1.5rem;
  min-height: 180px;
  display: grid;
  place-items: center;
  gap: 0.75rem;
  color: #475569;
}

.banner {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(240px, 0.9fr);
  gap: 1.5rem;
  align-items: center;
}

.banner h2 {
  font-size: clamp(2rem, 3vw, 2.5rem);
}

.tag-row {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.9rem;
}

.tag {
  display: inline-flex;
  align-items: center;
  min-height: 2rem;
  padding: 0 0.85rem;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 800;
}

.tag--slate { background: #f1f5f9; color: #475569; }
.tag--pending { background: #fef3c7; color: #b45309; }
.tag--paid { background: #dcfce7; color: #15803d; }
.tag--expired,
.tag--failed { background: #fee2e2; color: #b91c1c; }
.tag--sky { background: #e0f2fe; color: #0369a1; }

.banner-meta {
  display: grid;
  gap: 1rem;
  border-left: 1px dashed #cbd5e1;
  padding-left: 1.5rem;
}

.banner-meta span,
.info-row span {
  display: block;
  color: #64748b;
  font-size: 0.84rem;
}

.banner-meta strong,
.info-row strong {
  display: block;
  margin-top: 0.35rem;
  color: #0f172a;
}

.detail-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.card {
  border-radius: 1.5rem;
  padding: 1.4rem;
}

.card-head {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.card-head p {
  margin: 0.3rem 0 0;
  color: #64748b;
}

.icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.95rem;
  display: grid;
  place-items: center;
  font-size: 1.2rem;
}

.icon--blue { background: #dbeafe; color: #2563eb; }
.icon--amber { background: #fef3c7; color: #d97706; }
.icon--violet { background: #ede9fe; color: #7c3aed; }
.icon--green { background: #dcfce7; color: #15803d; }

.info-grid {
  display: grid;
  gap: 0.75rem;
}

.info-row {
  padding: 1rem;
  border-radius: 1rem;
  background: #f8fafc;
  border: 1px solid #eef2f7;
}

.readonly-panel,
.edit-panel {
  border-radius: 1rem;
  border: 1px solid #dbe4f0;
  background: #f8fafc;
  padding: 1rem;
}

.readonly-panel {
  display: flex;
  gap: 0.85rem;
  color: #1e3a8a;
}

.readonly-panel p {
  margin: 0.35rem 0 0;
  color: #475569;
  line-height: 1.6;
}

.panel-label {
  display: block;
  margin-bottom: 0.8rem;
  color: #475569;
  text-transform: uppercase;
  font-size: 0.78rem;
  letter-spacing: 0.12em;
  font-weight: 800;
}

.edit-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.field {
  min-height: 2.9rem;
  width: 100%;
  padding: 0 1rem;
  background: #fff;
  outline: none;
}

.empty-state {
  margin-top: 1rem;
  display: grid;
  place-items: center;
  text-align: center;
  gap: 0.8rem;
}

.empty-state i {
  font-size: 2rem;
  color: #2563eb;
}

.detail-link {
  min-height: 3rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0 1rem;
  border-radius: 1rem;
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  font-weight: 800;
  text-decoration: none;
}

@media (max-width: 960px) {
  .hero,
  .banner,
  .detail-grid {
    grid-template-columns: 1fr;
  }

  .banner-meta {
    border-left: none;
    border-top: 1px dashed #cbd5e1;
    padding-left: 0;
    padding-top: 1rem;
  }
}

@media (max-width: 768px) {
  .invoice-detail-page {
    padding: 1rem;
  }

  .hero {
    grid-template-columns: 1fr;
  }

  .edit-row {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
