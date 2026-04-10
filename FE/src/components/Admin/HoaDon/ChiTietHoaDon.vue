<template>
  <div class="invoice-detail-page">
    <section class="detail-hero">
      <button class="back-button" type="button" @click="quayLai">
        <i class="fas fa-arrow-left"></i>
      </button>

      <div class="detail-hero__copy">
        <p class="detail-hero__eyebrow">Quản trị hóa đơn</p>
        <h1>Chi tiết Hóa đơn</h1>
        <p>
          Mã giao dịch: <strong>{{ invoiceId }}</strong>.
        </p>
      </div>

      <div class="detail-hero__actions">
        <button class="ghost-button" type="button" @click="taiHoaDon" :disabled="dangTai">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dangTai ? "Đang tải..." : "Tải lại dữ liệu" }}</span>
        </button>
      </div>
    </section>

    <div v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </div>

    <!-- Loading State -->
    <section v-if="dangTai" class="loading-shell">
      <div class="loading-grid">
        <div class="loading-card"></div>
        <div class="loading-card"></div>
      </div>
    </section>

    <!-- Main Content -->
    <template v-else-if="invoice">
      <section class="invoice-banner">
        <div class="invoice-banner__main">
          <div class="invoice-banner__avatar" :class="getAvatarColor(invoice.trang_thai_thanh_toan)">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>

          <div class="invoice-banner__copy">
            <p class="invoice-banner__eyebrow">Tổng giá trị thanh toán</p>
            <h2>{{ formatCurrency(invoice.tong_tien) }}</h2>
            <div class="invoice-banner__meta">
              <span class="tag tag--slate">{{ invoice.loai_hoa_don === 0 ? 'Đặt Tour' : 'Dịch vụ lẻ' }}</span>
              <span :class="['tag', getStatusColor(invoice.trang_thai_thanh_toan, true)]">
                {{ getStatusLabel(invoice.trang_thai_thanh_toan) }}
              </span>
              <span class="tag tag--sky" v-if="invoice.ma_giao_dich">GD: {{ invoice.ma_giao_dich }}</span>
            </div>
          </div>
        </div>

        <div class="invoice-banner__side">
          <div class="hero-stat">
            <span>Ngày tạo</span>
            <strong>{{ formatDate(invoice.ngay_tao) }}</strong>
          </div>
          <div class="hero-stat">
            <span>Cập nhật GD</span>
            <strong>{{ formatDate(invoice.updated_at) }}</strong>
          </div>
        </div>
      </section>

      <section class="detail-grid">
        <!-- Thông tin Đối tượng mua (Tour / Cập nhật dịch vụ) -->
        <article class="card">
          <header class="card__head">
            <div class="card__icon card__icon--blue">
              <i class="fas" :class="invoice.loai_hoa_don === 0 ? 'fa-route' : 'fa-bell-concierge'"></i>
            </div>
            <div>
              <h3>Thông tin sản phẩm</h3>
              <p>{{ invoice.loai_hoa_don === 0 ? 'Chi tiết tour khách hàng đã đặt.' : 'Dịch vụ tiện ích khách hàng sử dụng.' }}</p>
            </div>
          </header>

          <div class="info-grid" v-if="productData">
            <div class="info-row" style="background: #eef4ff; border-left: 3px solid #2563eb;">
              <span style="color: #1e3a8a;">Tên sản phẩm</span>
              <strong style="color: #1e3a8a; font-size: 1.1rem;">{{ productData.name }}</strong>
            </div>
            <div class="info-row">
              <span>Mã ID hệ thống</span>
              <strong>{{ productData.id }}</strong>
            </div>
            <template v-if="invoice.loai_hoa_don === 0">
               <!-- Specific info for Tour here if needed -->
               <div class="info-row" v-if="productData.price">
                 <span>Đơn giá cơ sở</span>
                 <strong>{{ formatCurrency(productData.price) }}</strong>
               </div>
            </template>
            <template v-else>
               <!-- Specific info for Service -->
               <div class="info-row" v-if="productData.price">
                 <span>Đơn giá dịch vụ</span>
                 <strong>{{ formatCurrency(productData.price) }}</strong>
               </div>
            </template>
          </div>
          <div class="info-grid" v-else-if="isLoadingProduct">
            <div class="info-row">
              <span class="muted"><i class="fas fa-spinner fa-spin"></i> Đang tải thông tin sản phẩm...</span>
            </div>
          </div>
          <div class="info-grid" v-else>
             <div class="info-row" style="background: #fff1f2;">
              <span style="color: #be123c;">Lỗi tải dữ liệu sản phẩm.</span>
              <strong>Mã hệ thống: {{ invoice.ma_doi_tuong }}</strong>
            </div>
          </div>
        </article>

        <!-- Thông tin Khách hàng -->
        <article class="card">
          <header class="card__head">
            <div class="card__icon card__icon--amber">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <h3>Đại diện đặt dịch vụ</h3>
              <p>Hệ thống ghi nhận hóa đơn trực tiếp cho nhóm hoặc cá nhân tạo.</p>
            </div>
          </header>

          <div class="info-grid">
            <div class="info-row" v-if="invoice.nhom">
              <span>Tên nhóm</span>
              <strong>{{ invoice.nhom.Ten_nhom || invoice.nhom.ten_nhom || 'Nhóm du lịch' }}</strong>
            </div>
            <div class="info-row">
              <span>Mã nhóm</span>
              <strong>{{ invoice.ma_nhom }}</strong>
            </div>
          </div>
        </article>

        <!-- Quản lý trạng thái -->
        <article class="card card--wide">
          <header class="card__head">
            <div class="card__icon card__icon--violet">
              <i class="fas fa-sliders"></i>
            </div>
            <div>
              <h3>Chuyển trạng thái giao dịch</h3>
              <p>Can thiệp thay đổi tiến độ thanh toán trong trường hợp lỗi mạng lưới hoặc khách thanh toán tiền mặt trực tiếp.</p>
            </div>
          </header>

          <div class="control-panel">
            <div class="status-selector">
              <label>Thiết lập trạng thái mới:</label>
              <select v-model="editStatus" class="filter-select">
                <option :value="0">Chờ xử lý (Pending)</option>
                <option :value="1">Đã thanh toán (Thành công)</option>
                <option :value="2">Hủy bỏ / Thất bại</option>
              </select>
            </div>
            <button class="primary-button" @click="luuTrangThaiHoaDon" :disabled="dangLuu || editStatus === invoice.trang_thai_thanh_toan">
              <i class="fas fa-check" v-if="!dangLuu"></i>
              <i class="fas fa-spinner fa-spin" v-else></i>
              <span>Cập nhật thay đổi</span>
            </button>
          </div>
        </article>
      </section>
    </template>

    <section v-else class="empty-state">
      <i class="fas fa-file-excel"></i>
      <h2>Không tìm thấy hóa đơn</h2>
      <p>Mã hóa đơn này hiện không có trong hệ thống hoặc đã bị xóa.</p>
      <router-link class="primary-link" to="/admin/hoa-don">Quay về danh sách</router-link>
    </section>
  </div>
</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import { showAlert, showConfirm } from "../../../services/appDialog";

export default {
  name: "ChiTietHoaDon",
  data() {
    return {
      dangTai: false,
      isLoadingProduct: false,
      dangLuu: false,
      thongBaoLoi: "",
      invoice: null,
      productData: null,
      editStatus: 0,
    };
  },
  computed: {
    invoiceId() {
      return this.$route.params.id;
    }
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
    async taiHoaDon() {
      if (!this.invoiceId) return;

      this.dangTai = true;
      this.thongBaoLoi = "";
      this.productData = null;

      try {
        const response = await goiApi(`/api/admin/hoa-don/${this.invoiceId}`, {
          headers: {
            Accept: 'application/json',
          },
        });
        const payload = await response.json().catch(() => ({}));
        if (response.ok && payload?.success) {
          this.invoice = payload.data;
          this.editStatus = parseInt(this.invoice.trang_thai_thanh_toan);
          
          // Sau khi có hóa đơn, tải thông tin Tour hoặc Dịch vụ tương ứng
          this.taiChiTietSanPham();
        } else {
          throw new Error("Dữ liệu trả về không hợp lệ.");
        }
      } catch (error) {
        console.error("Lỗi:", error);
        this.invoice = null;
        this.thongBaoLoi = "Không thể tải chi tiết hóa đơn.";
      } finally {
        this.dangTai = false;
      }
    },
    
    async taiChiTietSanPham() {
      if (!this.invoice || !this.invoice.ma_doi_tuong) return;
      
      this.isLoadingProduct = true;
      try {
        if (this.invoice.loai_hoa_don === 0) {
          // fetch tour list and find details manually or try public api
          const res = await goiApi(`/api/tour/${this.invoice.ma_doi_tuong}`, {
            headers: {
              Accept: 'application/json',
            },
          });
          const duLieuTour = await res.json().catch(() => ({}));
          if (res.ok && duLieuTour?.success) {
              const tour = duLieuTour.data;
              this.productData = {
                  id: tour.Ma_tour,
                  name: tour.Ten_tour,
                  price: tour.Gia_tour
              };
          } else {
             this.productData = null;
          }
        } else {
          // fetch dịch vụ list
          const res = await goiApi(`/api/dich-vu-dia-diem/${this.invoice.ma_doi_tuong}`, {
            headers: {
              Accept: 'application/json',
            },
          });
          const duLieuDichVu = await res.json().catch(() => ({}));
          if (res.ok && duLieuDichVu?.success) {
              const sv = duLieuDichVu.data;
              this.productData = {
                  id: sv.ma_dich_vu,
                  name: sv.ten_dich_vu,
                  price: sv.gia
              };
          } else {
              this.productData = null;
          }
        }
      } catch (err) {
         console.warn("Không lấy được thông tin chi tiết sp", err);
         this.productData = null;
      } finally {
         this.isLoadingProduct = false;
      }
    },

    async luuTrangThaiHoaDon() {
      if (!this.invoice) return;
      
      const newStatusLabel = this.getStatusLabel(this.editStatus);
      const confirmed = await showConfirm({
        title: "Xác thực thay đổi",
        message: `Bạn đang chuẩn bị chuyển hóa đơn ${this.invoice.ma_hoa_don} sang "${newStatusLabel}". Hành động thay đổi dòng tiền có thể ảnh hưởng hệ thống. Tiếp tục?`,
        tone: "warning",
        confirmText: "Thực hiện ngay",
        cancelText: "Hủy bỏ",
      });
      
      if (!confirmed) return;

      this.dangLuu = true;
      try {
        const response = await goiApi(`/api/admin/hoa-don/${this.invoice.ma_hoa_don}/status`, {
          method: 'PATCH',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            trang_thai_thanh_toan: parseInt(this.editStatus)
          }),
        });
        const payload = await response.json().catch(() => ({}));
        
        if (response.ok && payload?.success) {
          this.invoice.trang_thai_thanh_toan = parseInt(this.editStatus);
          await showAlert({
            title: "Cập nhật thành công",
            message: `Hóa đơn đã chuyển sang trạng thái "${newStatusLabel}".`,
            tone: "success"
          });
        } else {
           throw new Error(payload?.message || 'Hệ thống từ chối cập nhật');
        }
      } catch (error) {
         await showAlert({
            title: "Lỗi hệ thống",
            message: error.message || "Đã xảy ra lỗi trong quá trình kết nối đến máy chủ.",
            tone: "danger"
          });
      } finally {
        this.dangLuu = false;
      }
    },

    quayLai() {
      this.$router.push("/admin/hoa-don");
    },
    formatCurrency(value) {
      if (!value) return "0 ₫";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    formatDate(dateString) {
      if (!dateString) return "---";
      const d = new Date(dateString);
      return d.toLocaleDateString('vi-VN') + ' ' + d.toLocaleTimeString('vi-VN');
    },
    getStatusLabel(status) {
      switch (parseInt(status)) {
        case 0: return "Chờ xử lý";
        case 1: return "Đã thanh toán";
        case 2: return "Hủy bỏ / Lỗi";
        default: return "Không rõ";
      }
    },
    getStatusColor(status, isTag = false) {
      const prefix = isTag ? 'tag--' : 'pill--';
      switch (parseInt(status)) {
        case 0: return prefix + "amber"; 
        case 1: return prefix + "green"; 
        case 2: return prefix + "rose";  
        default: return prefix + "slate";
      }
    },
    getAvatarColor(status) {
        switch (parseInt(status)) {
            case 0: return "avatar--amber";
            case 1: return "avatar--green";
            case 2: return "avatar--rose";
            default: return "avatar--blue";
        }
    }
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

.detail-hero, .invoice-banner, .card, .empty-state, .loading-card {
  border: 1px solid #e2e8f0;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.04);
  backdrop-filter: blur(10px);
}

.detail-hero {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 1.25rem;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-radius: 1.5rem;
}

.back-button, .ghost-button, .primary-button {
  min-height: 2.8rem;
  border-radius: 0.85rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  font-weight: 700;
  transition: 0.2s;
  cursor: pointer;
  border: none;
}

.back-button {
  width: 2.8rem;
  background: #f1f5f9;
  color: #475569;
}
.back-button:hover { background: #e2e8f0; color: #0f172a; }

.ghost-button {
  padding: 0 1rem;
  border: 1px solid #cbd5e1;
  background: #ffffff;
  color: #334155;
}
.ghost-button:hover { border-color: #94a3b8; }

.primary-button {
  padding: 0 1.2rem;
  background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
  color: #ffffff;
}
.primary-button:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2); }
.primary-button:disabled { opacity: 0.5; cursor: not-allowed; }

.detail-hero__eyebrow, .invoice-banner__eyebrow {
  margin: 0 0 0.35rem;
  color: #2563eb;
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.detail-hero__copy h1 { margin: 0; color: #0f172a; font-size: 1.6rem; font-weight: 900;}
.detail-hero__copy p { margin: 0.35rem 0 0; color: #64748b; font-size: 0.95rem; }

.notice { padding: 0.9rem 1.1rem; border-radius: 1rem; margin-top: 1rem; display: flex; align-items: center; gap: 0.7rem; font-weight: 700; }
.notice--error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

.invoice-banner {
  margin-top: 1rem;
  padding: 1.35rem 1.5rem;
  border-radius: 1.5rem;
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(240px, 0.8fr);
  gap: 1.5rem;
}

.invoice-banner__main { display: flex; align-items: center; gap: 1.25rem; }
.invoice-banner__avatar {
  width: 5rem; height: 5rem;
  border-radius: 1.25rem;
  display: grid; place-items: center;
  font-size: 1.7rem;
}
.avatar--green { background: #dcfce7; color: #16a34a; }
.avatar--amber { background: #fef3c7; color: #d97706; }
.avatar--rose { background: #ffe4e6; color: #e11d48; }
.avatar--blue { background: #dbeafe; color: #2563eb; }

.invoice-banner__copy h2 { margin: 0; font-size: 2.2rem; color: #0f172a; font-weight: 900;}
.invoice-banner__meta { margin-top: 0.75rem; display: flex; gap: 0.5rem; flex-wrap: wrap; }

.invoice-banner__side { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; border-left: 1px dashed #cbd5e1; padding-left: 1.5rem; align-content: center;}

.hero-stat span, .info-row span { display: block; color: #64748b; font-size: 0.85rem;}
.hero-stat strong, .info-row strong { display: block; margin-top: 0.3rem; color: #0f172a; font-size: 1.05rem;}

.tag { min-height: 2rem; padding: 0 0.85rem; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 800; }
.tag--green { background: #dcfce7; color: #16a34a; }
.tag--amber { background: #fef3c7; color: #d97706; }
.tag--rose { background: #ffe4e6; color: #e11d48; }
.tag--slate { background: #f1f5f9; color: #475569; }
.tag--sky { background: #e0f2fe; color: #0284c7; }

.detail-grid { margin-top: 1rem; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
.card { padding: 1.5rem; border-radius: 1.5rem; display: flex; flex-direction: column; }
.card--wide { grid-column: 1 / -1; }

.card__head { display: flex; gap: 1rem; margin-bottom: 1.25rem; }
.card__icon { width: 3rem; height: 3rem; border-radius: 0.85rem; display: grid; place-items: center; font-size: 1.25rem; }
.card__icon--blue { background: #dbeafe; color: #2563eb; }
.card__icon--amber { background: #fef3c7; color: #d97706; }
.card__icon--violet { background: #ede9fe; color: #7c3aed; }

.card__head h3 { margin: 0; color: #0f172a; font-size: 1.15rem; font-weight: 800;}
.card__head p { margin: 0.25rem 0 0; color: #64748b; font-size: 0.9rem;}

.info-grid { display: grid; gap: 0.75rem; border: 1px solid #f1f5f9; border-radius: 1rem; overflow: hidden; background: #fafafb;}
.info-row { padding: 1rem; border-bottom: 1px solid #f1f5f9; }
.info-row:last-child { border-bottom: none; }

.control-panel { display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; padding: 1.25rem; background: #f8fafc; border-radius: 1rem; border: 1px dashed #cbd5e1;}
.status-selector { flex: 1; display: flex; align-items: center; gap: 1rem; }
.status-selector label { font-weight: 700; color: #334155; }
.filter-select { height: 2.8rem; padding: 0 1rem; border-radius: 0.75rem; border: 1px solid #cbd5e1; outline: none; background: #fff; font-size: 0.95rem; font-weight: 600; color: #0f172a;flex:1;}

.empty-state { margin-top: 1.5rem; padding: 4rem 1.5rem; text-align: center; border-radius: 1.5rem; }
.empty-state i { font-size: 3rem; color: #64748b; margin-bottom: 1rem; }

.loading-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;}
.loading-card { height: 250px; border-radius: 1.5rem; position: relative; overflow: hidden; }
.loading-card::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent); transform: translateX(-100%); animation: shimmer 1.2s infinite; }
@keyframes shimmer { 100% { transform: translateX(100%); } }

@media (max-width: 900px) {
  .invoice-banner { grid-template-columns: 1fr; }
  .invoice-banner__side { border-left: none; border-top: 1px dashed #cbd5e1; padding-left: 0; padding-top: 1.25rem; }
  .detail-grid { grid-template-columns: 1fr; }
  .control-panel { flex-direction: column; align-items: stretch; }
  .status-selector { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
}
</style>

