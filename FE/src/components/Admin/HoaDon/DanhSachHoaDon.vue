<template>
  <div class="invoice-page">
    <section class="hero">
      <div>
        <p class="eyebrow">Hóa đơn quản trị</p>
        <h1>Quản lý hóa đơn</h1>
        <p class="subtitle">Theo dõi khách đặt, thanh toán QR và trạng thái thanh toán trên cùng một màn hình.</p>
      </div>

      <button class="ghost-button" type="button" @click="taiDanhSachHoaDon" :disabled="dangTai">
        <i class="fas fa-rotate-right"></i>
        <span>{{ dangTai ? "Đang tải..." : "Tải lại dữ liệu" }}</span>
      </button>
    </section>

    <section class="stats-grid">
      <article class="stat-card">
        <span>Tổng hóa đơn</span>
        <strong>{{ danhSachHoaDon.length.toLocaleString() }}</strong>
      </article>
      <article class="stat-card">
        <span>Đã thanh toán</span>
        <strong>{{ soHoaDonDaThanhToan.toLocaleString() }}</strong>
      </article>
      <article class="stat-card">
        <span>Đang chờ</span>
        <strong>{{ soHoaDonChoXuLy.toLocaleString() }}</strong>
      </article>
      <article class="stat-card">
        <span>Hết hạn / thất bại</span>
        <strong>{{ soHoaDonGapVanDe.toLocaleString() }}</strong>
      </article>
      <article class="stat-card">
        <span>Doanh thu đã thanh toán</span>
        <strong>{{ dinhDangTienTe(tongDoanhThu) }}</strong>
      </article>
    </section>

    <section class="filter-bar">
      <label class="search-box">
        <i class="fas fa-search"></i>
        <input v-model.trim="search" type="text" placeholder="Tìm mã hóa đơn, khách đặt, tour, mã tham chiếu..." />
      </label>

      <div class="date-range">
        <input type="date" v-model="boLocTuNgay" class="field" />
        <span>-</span>
        <input type="date" v-model="boLocDenNgay" class="field" />
      </div>

      <select v-model="boLocTrangThai" class="field">
        <option value="all">Trạng thái: Tất cả</option>
        <option value="pending">Đang chờ thanh toan</option>
        <option value="paid">Đã thanh toán</option>
        <option value="expired">Đã hết hạn</option>
        <option value="failed">Thất bại / hủy</option>
      </select>
    </section>

    <div v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </div>

    <section v-if="danhSachHoaDonDaLoc.length" class="content-grid">
      <article class="card">
        <div class="card-head">
          <div>
            <h2>Danh sách hóa đơn</h2>
            <p>{{ danhSachHoaDonDaLoc.length }} hóa đơn phù hợp bộ lọc hiện tại.</p>
          </div>
          <span class="chip">Đồng bộ thanh toán</span>
        </div>

        <div class="invoice-table">
          <div class="invoice-row invoice-row--head">
            <span>Mã HD</span>
            <span>Khách đặt</span>
            <span>Tour / lịch</span>
            <span>Thanh toán</span>
            <span style="text-align: right;">Tổng tiền</span>
            <span style="text-align: center;">Trạng thái</span>
          </div>

          <div
            v-for="invoice in danhSachHoaDonPhanTrang"
            :key="invoice.ma_hoa_don"
            class="invoice-row"
            :class="{ 'is-selected': hoaDonDangChon?.ma_hoa_don === invoice.ma_hoa_don }"
            @click="chonHoaDon(invoice)"
          >
            <div>
              <small class="muted">{{ invoice.loai_hoa_don === 0 ? "Tour" : "Dịch vụ" }}</small>
              <strong>{{ invoice.ma_hoa_don }}</strong>
            </div>

            <div>
              <strong>{{ tenKhach(invoice) }}</strong>
              <small class="muted">{{ invoice.customer_summary?.email || "Chưa có email" }}</small>
            </div>

            <div>
              <strong>{{ invoice.ten_tour || (invoice.ma_tour ? `Tour ${invoice.ma_tour}` : "Chưa xác định tour") }}</strong>
              <small class="muted">Lich: {{ invoice.ma_thoi_gian_tour || "---" }}</small>
            </div>

            <div>
              <strong>{{ nhanKenhThanhToan(invoice.payment_method) }}</strong>
              <small class="muted">{{ paymentReference(invoice) }}</small>
            </div>

            <strong class="text-right">{{ dinhDangTienTe(invoice.so_tien || invoice.tong_tien) }}</strong>

            <div class="centered">
              <span :class="['status-pill', `status-pill--${paymentStatus(invoice)}`]">
                {{ layNhanTrangThai(invoice.payment_status) }}
              </span>
            </div>
          </div>
        </div>

        <footer class="table-footer">
          <span>Hiển thị {{ chiSoBatDau }}-{{ chiSoKetThuc }} / {{ danhSachHoaDonDaLoc.length }} hoa don</span>

          <div class="pagination">
            <button class="page-btn" type="button" :disabled="page === 1" @click="page--">
              <i class="fas fa-angle-left"></i>
            </button>
            <button
              v-for="pageNumber in danhSachTrangHienThi"
              :key="pageNumber"
              type="button"
              :class="['page-btn', { 'is-active': page === pageNumber }]"
              @click="page = pageNumber"
            >
              {{ pageNumber }}
            </button>
            <button class="page-btn" type="button" :disabled="page === tongSoTrang" @click="page++">
              <i class="fas fa-angle-right"></i>
            </button>
          </div>
        </footer>
      </article>

      <aside class="card detail-card">
        <div v-if="hoaDonDangChon">
          <div class="detail-head">
            <div>
              <p class="eyebrow">Xem nhanh hóa đơn</p>
              <h2>{{ hoaDonDangChon.ma_hoa_don }}</h2>
            </div>
            <span :class="['status-pill', `status-pill--${paymentStatus(hoaDonDangChon)}`]">
              {{ layNhanTrangThai(hoaDonDangChon.payment_status) }}
            </span>
          </div>

          <div class="detail-grid">
            <div class="detail-row">
              <span>Khách đặt</span>
              <strong>{{ tenKhach(hoaDonDangChon) }}</strong>
            </div>
            <div class="detail-row">
              <span>Email</span>
              <strong>{{ hoaDonDangChon.customer_summary?.email || "---" }}</strong>
            </div>
            <div class="detail-row">
              <span>Số điện thoại</span>
              <strong>{{ hoaDonDangChon.customer_summary?.so_dien_thoai || "---" }}</strong>
            </div>
            <div class="detail-row">
              <span>Tour</span>
              <strong>{{ hoaDonDangChon.ten_tour || hoaDonDangChon.ma_tour || "---" }}</strong>
            </div>
            <div class="detail-row">
              <span>Lich khoi hanh</span>
              <strong>{{ hoaDonDangChon.ma_thoi_gian_tour || "---" }}</strong>
            </div>
            <div class="detail-row">
              <span>Kênh thanh toán</span>
              <strong>{{ nhanKenhThanhToan(hoaDonDangChon.payment_method) }}</strong>
            </div>
            <div class="detail-row">
              <span>Mã tham chiếu</span>
              <strong>{{ paymentReference(hoaDonDangChon) }}</strong>
            </div>
            <div class="detail-row">
              <span>Nội dung chuyển khoản</span>
              <strong>{{ hoaDonDangChon.noi_dung_chuyen_khoan || "---" }}</strong>
            </div>
            <div class="detail-row">
              <span>Ngày tạo</span>
              <strong>{{ dinhDangNgayGio(hoaDonDangChon.ngay_tao || hoaDonDangChon.created_at) }}</strong>
            </div>
            <div class="detail-row">
              <span>Thanh toán lúc</span>
              <strong>{{ dinhDangNgayGio(hoaDonDangChon.paid_at) }}</strong>
            </div>
            <div v-if="hoaDonDangChon.payment_expires_at" class="detail-row">
              <span>Hạn QR</span>
              <strong>{{ dinhDangNgayGio(hoaDonDangChon.payment_expires_at) }}</strong>
            </div>
            <div class="detail-row detail-row--total">
              <span>Tổng thanh toán</span>
              <strong>{{ dinhDangTienTe(hoaDonDangChon.so_tien || hoaDonDangChon.tong_tien) }}</strong>
            </div>
          </div>

          <div v-if="coTheSuaTrangThai(hoaDonDangChon)" class="edit-panel">
            <label class="panel-label">Cập nhật trạng thái thủ công</label>
            <div class="edit-row">
              <select v-model="trangThaiDangSua" class="field" style="flex: 1;">
                <option :value="0">Chờ xử lý</option>
                <option :value="1">Đã thanh toán</option>
                <option :value="2">Thất bại / hủy</option>
              </select>
              <button class="primary-button" type="button" @click="luuTrangThaiHoaDon" :disabled="dangLưu">
                <i v-if="dangLưu" class="fas fa-spinner fa-spin"></i>
                <span v-else>Lưu</span>
              </button>
            </div>
          </div>

          <div v-else class="readonly-panel">
            <i class="fas fa-lock"></i>
            <div>
              <strong>Hóa đơn QR chỉ đọc</strong>
              <p>Trạng thái hóa đơn này chỉ được đồng bộ từ webhook SePay hoặc luồng tạo lại QR.</p>
            </div>
          </div>

          <router-link :to="`/admin/hoa-don/${hoaDonDangChon.ma_hoa_don}`" class="detail-link">
            Xem chi tiết hóa đơn
          </router-link>
        </div>

        <div v-else class="empty-box">
          <i class="fas fa-hand-pointer"></i>
          <h2>Chưa chọn hóa đơn</h2>
          <p>Chọn một dòng trong bảng để xem thông tin khách đặt và thanh toán.</p>
        </div>
      </aside>
    </section>

    <div v-else class="empty-box card">
      <i class="fas fa-box-open"></i>
      <h2>Không có dữ liệu</h2>
      <p>Hệ thống hiện chưa có hóa đơn nào.</p>
    </div>
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
  name: "DanhSachHoaDon",
  data() {
    return {
      danhSachHoaDon: [],
      dangTai: false,
      thongBaoLoi: "",
      search: "",
      boLocTrangThai: "all",
      boLocTuNgay: "",
      boLocDenNgay: "",
      page: 1,
      soLuongMoiTrang: 10,
      hoaDonDangChon: null,
      trangThaiDangSua: 0,
      dangLưu: false,
    };
  },
  computed: {
    soHoaDonDaThanhToan() {
      return this.danhSachHoaDon.filter((invoice) => this.paymentStatus(invoice) === "paid").length;
    },
    soHoaDonChoXuLy() {
      return this.danhSachHoaDon.filter((invoice) => this.paymentStatus(invoice) === "pending").length;
    },
    soHoaDonGapVanDe() {
      return this.danhSachHoaDon.filter((invoice) => ["expired", "failed"].includes(this.paymentStatus(invoice))).length;
    },
    tongDoanhThu() {
      return this.danhSachHoaDon
        .filter((invoice) => this.paymentStatus(invoice) === "paid")
        .reduce((sum, invoice) => sum + Number(invoice.so_tien || invoice.tong_tien || 0), 0);
    },
    danhSachHoaDonDaLoc() {
      let result = [...this.danhSachHoaDon];

      if (this.search) {
        const query = this.search.toLowerCase();
        result = result.filter((invoice) => {
          const target = [
            invoice.ma_hoa_don,
            invoice.ten_tour,
            invoice.ma_tour,
            invoice.ma_thoi_gian_tour,
            this.tenKhach(invoice),
            invoice.customer_summary?.email,
            invoice.customer_summary?.so_dien_thoai,
            this.paymentReference(invoice),
          ]
            .filter(Boolean)
            .join(" ")
            .toLowerCase();
          return target.includes(query);
        });
      }

      if (this.boLocTrangThai !== "all") {
        result = result.filter((invoice) => this.paymentStatus(invoice) === this.boLocTrangThai);
      }

      if (this.boLocTuNgay) {
        const fromDate = new Date(this.boLocTuNgay);
        fromDate.setHours(0, 0, 0, 0);
        result = result.filter((invoice) => new Date(invoice.ngay_tao || invoice.created_at) >= fromDate);
      }

      if (this.boLocDenNgay) {
        const toDate = new Date(this.boLocDenNgay);
        toDate.setHours(23, 59, 59, 999);
        result = result.filter((invoice) => new Date(invoice.ngay_tao || invoice.created_at) <= toDate);
      }

      result.sort((a, b) => new Date(b.ngay_tao || b.created_at) - new Date(a.ngay_tao || a.created_at));
      return result;
    },
    tongSoTrang() {
      return Math.ceil(this.danhSachHoaDonDaLoc.length / this.soLuongMoiTrang) || 1;
    },
    danhSachHoaDonPhanTrang() {
      const start = (this.page - 1) * this.soLuongMoiTrang;
      return this.danhSachHoaDonDaLoc.slice(start, start + this.soLuongMoiTrang);
    },
    chiSoBatDau() {
      return this.danhSachHoaDonDaLoc.length === 0 ? 0 : (this.page - 1) * this.soLuongMoiTrang + 1;
    },
    chiSoKetThuc() {
      return Math.min(this.page * this.soLuongMoiTrang, this.danhSachHoaDonDaLoc.length);
    },
    danhSachTrangHienThi() {
      const pages = [];
      const total = this.tongSoTrang;
      const current = this.page;
      let start = Math.max(1, current - 2);
      let end = Math.min(total, start + 4);
      if (end - start < 4) start = Math.max(1, end - 4);
      for (let i = start; i <= end; i += 1) pages.push(i);
      return pages;
    },
  },
  watch: {
    search() {
      this.page = 1;
    },
    boLocTrangThai() {
      this.page = 1;
    },
    boLocTuNgay() {
      this.page = 1;
    },
    boLocDenNgay() {
      this.page = 1;
    },
    danhSachHoaDonPhanTrang(newList) {
      if (newList.length > 0) {
        const currentId = this.hoaDonDangChon?.ma_hoa_don;
        const selected = currentId ? newList.find((item) => item.ma_hoa_don === currentId) : null;
        this.chonHoaDon(selected || newList[0]);
      } else {
        this.hoaDonDangChon = null;
      }
    },
  },
  mounted() {
    this.taiDanhSachHoaDon();
  },
  methods: {
    mapLegacyStatus(status) {
      switch (Number(status)) {
        case 1:
          return "paid";
        case 2:
          return "failed";
        default:
          return "pending";
      }
    },
    paymentStatus(invoiceOrStatus) {
      if (typeof invoiceOrStatus === "string") return invoiceOrStatus;
      if (invoiceOrStatus && typeof invoiceOrStatus.payment_status === "string") return invoiceOrStatus.payment_status;
      return this.mapLegacyStatus(invoiceOrStatus?.trang_thai_thanh_toan);
    },
    tenKhach(invoice) {
      return invoice?.customer_summary?.ho_ten || invoice?.ten_nguoi_dat || "Khách lẻ";
    },
    paymentReference(invoice) {
      return invoice?.payment_reference || invoice?.ma_giao_dich || invoice?.latest_qr_payment_summary?.reference_code || "---";
    },
    nhanKenhThanhToan(method) {
      if (method === "vietqr_bank_transfer") return "QR ngân hàng";
      if (method === "legacy_manual") return "Thủ công / cũ";
      return method || "---";
    },
    coTheSuaTrangThai(invoice) {
      return invoice?.payment_method !== "vietqr_bank_transfer";
    },
    async taiDanhSachHoaDon() {
      this.dangTai = true;
      this.thongBaoLoi = "";
      try {
        const response = await goiApi("/api/admin/hoa-don", { headers: { Accept: "application/json" } });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(payload?.message || "Không thể tải danh sách hóa đơn.");

        this.danhSachHoaDon = Array.isArray(payload?.data) ? payload.data : [];
        if (this.danhSachHoaDon.length > 0) this.chonHoaDon(this.danhSachHoaDon[0]);
      } catch (error) {
        this.thongBaoLoi = error?.message || "Đã xảy ra lỗi khi lấy danh sách hóa đơn.";
        this.danhSachHoaDon = [];
        this.hoaDonDangChon = null;
      } finally {
        this.dangTai = false;
      }
    },
    chonHoaDon(invoice) {
      this.hoaDonDangChon = invoice;
      this.trangThaiDangSua = Number(invoice?.trang_thai_thanh_toan ?? 0);
    },
    async luuTrangThaiHoaDon() {
      if (!this.hoaDonDangChon || !this.coTheSuaTrangThai(this.hoaDonDangChon)) return;

      const confirmed = await showConfirm({
        title: "Xác nhận cập nhật",
        message: `Bạn có chắc muốn cập nhật hóa đơn ${this.hoaDonDangChon.ma_hoa_don}?`,
        tone: "warning",
        confirmText: "Đồng ý",
        cancelText: "Hủy",
      });

      if (!confirmed) return;

      this.dangLưu = true;
      try {
        const response = await goiApi(`/api/admin/hoa-don/${this.hoaDonDangChon.ma_hoa_don}/status`, {
          method: "PATCH",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            trang_thai_thanh_toan: Number(this.trangThaiDangSua),
          }),
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok || !payload?.success) {
          throw new Error(payload?.message || "Hệ thống từ chối cập nhật.");
        }

        const updatedInvoice = payload.data;
        const index = this.danhSachHoaDon.findIndex((item) => item.ma_hoa_don === updatedInvoice.ma_hoa_don);
        if (index !== -1) this.danhSachHoaDon.splice(index, 1, updatedInvoice);
        this.chonHoaDon(updatedInvoice);

        toaster.success(`Hóa đơn ${updatedInvoice.ma_hoa_don} đã được cập nhật.`);
      } catch (error) {
        toaster.error(error?.message || "Đã xảy ra lỗi trong quá trình cập nhật.");
      } finally {
        this.dangLưu = false;
      }
    },
    dinhDangTienTe(value) {
      const amount = Number(value);
      if (!Number.isFinite(amount)) return "0 đ";
      return new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(amount);
    },
    dinhDangNgayGio(value) {
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
    layNhanTrangThai(status) {
      switch (this.paymentStatus(status)) {
        case "paid":
          return "Đã thanh toán";
        case "expired":
          return "Đã hết hạn";
        case "failed":
          return "Thất bại / hủy";
        default:
          return "Đang chờ thanh toan";
      }
    },
  },
};
</script>

<style scoped>
.invoice-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background:
    radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24%),
    linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.hero,
.card,
.notice {
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid #e2e8f0;
  box-shadow: 0 16px 30px rgba(15, 23, 42, 0.05);
}

.hero {
  border-radius: 1.5rem;
  padding: 1.5rem;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.eyebrow {
  margin: 0 0 0.45rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.78rem;
  font-weight: 900;
}

.hero h1 {
  margin: 0;
  font-size: clamp(2.2rem, 3vw, 3rem);
  font-weight: 900;
  letter-spacing: -0.04em;
  color: #111827;
}

.subtitle {
  margin: 0.8rem 0 0;
  color: #64748b;
  max-width: 760px;
}

.ghost-button,
.primary-button,
.page-btn,
.field,
.search-box {
  border-radius: 1rem;
  border: 1px solid #dbe4f0;
}

.ghost-button,
.primary-button,
.page-btn {
  cursor: pointer;
  font-weight: 800;
}

.ghost-button {
  min-height: 3rem;
  padding: 0 1.1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  background: #fff;
  color: #334155;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.stat-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 1.3rem;
  padding: 1.1rem 1.2rem;
  display: grid;
  gap: 0.45rem;
}

.stat-card span {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 700;
}

.stat-card strong {
  color: #111827;
  font-size: 1.35rem;
  font-weight: 900;
}

.filter-bar {
  display: grid;
  grid-template-columns: minmax(0, 2fr) auto auto;
  gap: 1rem;
  margin-bottom: 1rem;
}

.search-box,
.field {
  min-height: 3rem;
  padding: 0 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: #fff;
  color: #334155;
}

.search-box input,
.field {
  width: 100%;
  outline: none;
  border: none;
  background: transparent;
  font-size: 0.95rem;
}

.date-range {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.notice {
  border-radius: 1rem;
  padding: 1rem 1.1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.8fr) minmax(320px, 0.9fr);
  gap: 1.25rem;
}

.card {
  border-radius: 1.5rem;
  padding: 1.25rem;
}

.card-head,
.detail-head,
.table-footer {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.card-head h2,
.detail-head h2 {
  margin: 0;
  color: #111827;
  font-size: 1.3rem;
  font-weight: 900;
}

.card-head p {
  margin: 0.35rem 0 0;
  color: #64748b;
}

.chip {
  padding: 0.65rem 0.9rem;
  border-radius: 999px;
  background: #eef2ff;
  color: #4338ca;
  font-size: 0.8rem;
  font-weight: 800;
}

.invoice-table {
  margin-top: 1rem;
}

.invoice-row {
  display: grid;
  grid-template-columns: 90px 1.5fr 1.4fr 1.15fr 1fr 140px;
  gap: 1rem;
  align-items: center;
  padding: 1rem 0;
  border-top: 1px solid #eef2f7;
  cursor: pointer;
}

.invoice-row--head {
  border-top: 0;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  font-size: 0.76rem;
  font-weight: 900;
  cursor: default;
  padding-top: 0;
}

.invoice-row.is-selected {
  background: #f8fbff;
  border-radius: 1rem;
  margin-inline: -0.75rem;
  padding-inline: 0.75rem;
}

.invoice-row strong,
.detail-row strong {
  display: block;
  color: #111827;
}

.muted {
  display: block;
  margin-top: 0.25rem;
  color: #64748b;
  font-size: 0.85rem;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2rem;
  padding: 0 0.9rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 800;
}

.status-pill--pending {
  background: #fef3c7;
  color: #b45309;
}

.status-pill--paid {
  background: #dcfce7;
  color: #15803d;
}

.status-pill--expired,
.status-pill--failed {
  background: #fee2e2;
  color: #b91c1c;
}

.text-right {
  text-align: right;
}

.centered {
  display: flex;
  justify-content: center;
}

.table-footer {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #eef2f7;
}

.table-footer span {
  color: #64748b;
  font-size: 0.9rem;
}

.pagination {
  display: flex;
  gap: 0.55rem;
}

.page-btn {
  width: 2.8rem;
  height: 2.8rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  color: #475569;
}

.page-btn.is-active {
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  border-color: transparent;
  color: #fff;
}

.detail-card {
  align-self: start;
  position: sticky;
  top: 1.25rem;
}

.detail-grid {
  display: grid;
  gap: 0.85rem;
  margin: 1rem 0 1.25rem;
}

.detail-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  font-size: 0.95rem;
}

.detail-row span {
  color: #64748b;
}

.detail-row strong {
  max-width: 62%;
  text-align: right;
  overflow-wrap: anywhere;
}

.detail-row--total {
  margin-top: 0.35rem;
  padding-top: 1rem;
  border-top: 1px dashed #dbe4f0;
}

.detail-row--total strong {
  color: #2563eb;
  font-size: 1.2rem;
}

.edit-panel,
.readonly-panel {
  border-radius: 1rem;
  padding: 1rem;
  border: 1px solid #dbe4f0;
  background: #f8fafc;
}

.panel-label {
  display: block;
  margin-bottom: 0.8rem;
  color: #475569;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.edit-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.primary-button {
  min-height: 3rem;
  padding: 0 1rem;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #fff;
}

.readonly-panel {
  display: flex;
  gap: 0.85rem;
  color: #1e3a8a;
}

.readonly-panel p {
  margin: 0.35rem 0 0;
  color: #475569;
  line-height: 1.55;
  font-size: 0.88rem;
}

.detail-link {
  margin-top: 0.85rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 3rem;
  border-radius: 1rem;
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  text-decoration: none;
  font-weight: 800;
}

.empty-box {
  min-height: 240px;
  display: grid;
  place-items: center;
  text-align: center;
  color: #64748b;
}

.empty-box i {
  font-size: 2rem;
  color: #2563eb;
  margin-bottom: 0.8rem;
}

.empty-box h2 {
  margin: 0;
  color: #111827;
  font-size: 1.35rem;
  font-weight: 900;
}

@media (max-width: 1320px) {
  .stats-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .content-grid {
    grid-template-columns: 1fr;
  }

  .detail-card {
    position: static;
  }
}

@media (max-width: 1080px) {
  .filter-bar {
    grid-template-columns: 1fr;
  }

  .invoice-row {
    grid-template-columns: 90px 1.5fr 1.3fr 1fr 1fr;
  }

  .invoice-row > :nth-child(6),
  .invoice-row--head > :nth-child(6) {
    display: none;
  }
}

@media (max-width: 768px) {
  .invoice-page {
    padding: 1rem;
  }

  .hero {
    flex-direction: column;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .invoice-row {
    grid-template-columns: 1fr;
  }

  .invoice-row--head {
    display: none;
  }

  .invoice-row {
    border: 1px solid #eef2f7;
    border-radius: 1rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
  }

  .text-right,
  .centered,
  .detail-row strong {
    text-align: left;
    justify-content: flex-start;
  }

  .detail-row {
    flex-direction: column;
    gap: 0.35rem;
  }

  .edit-row,
  .date-range {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
