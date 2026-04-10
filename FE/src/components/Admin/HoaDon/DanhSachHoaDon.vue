<template>
  <div class="invoice-page">
    <section class="invoice-page__hero">
      <div>
        <p class="invoice-page__eyebrow">Bảng Điều Khiển Quản Trị</p>
        <h1>Quản lý Hóa Đơn</h1>
        <p class="invoice-page__subtitle">
          Theo dõi tất cả đơn đặt hàng, quản lý doanh thu và trạng thái thanh toán của hệ thống.
        </p>
      </div>

      <div class="invoice-page__hero-actions">
        <button class="ghost-button" type="button" @click="taiDanhSachHoaDon" :disabled="dangTai">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dangTai ? "Đang tải..." : "Tải lại dữ liệu" }}</span>
        </button>
      </div>
    </section>

    <!-- Thống kê nhanh -->
    <section class="stats-grid">
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--blue">
          <i class="fas fa-file-invoice"></i>
        </div>
        <div>
          <span>Tổng hóa đơn</span>
          <strong>{{ danhSachHoaDon.length.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--green">
          <i class="fas fa-sack-dollar"></i>
        </div>
        <div>
          <span>Đã thanh toán</span>
          <strong>{{ soHoaDonDaThanhToan.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--amber">
          <i class="fas fa-clock-rotate-left"></i>
        </div>
        <div>
          <span>Chờ xử lý</span>
          <strong>{{ soHoaDonChoXuLy.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--violet">
          <i class="fas fa-money-bill-trend-up"></i>
        </div>
        <div>
          <span>Tổng doanh thu</span>
          <strong>{{ dinhDangTienTe(tongDoanhThu) }}</strong>
        </div>
      </article>
      
      <article class="stats-card">
         <div class="stats-card__icon stats-card__icon--sky">
          <i class="fas fa-chart-line"></i>
        </div>
        <div>
          <span>Hủy / Thất bại</span>
           <strong>{{ soHoaDonDaHuy.toLocaleString() }}</strong>
        </div>
      </article>
    </section>

    <!-- Bộ lọc -->
    <section class="filter-bar">
      <label class="filter-search">
        <i class="fas fa-search"></i>
        <input
          v-model.trim="search"
          type="text"
          placeholder="Tìm theo mã hóa đơn, mã nhóm, giao dịch..."
          autocomplete="new-password"
        >
      </label>

      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <input type="date" v-model="boLocTuNgay" class="filter-select" style="min-width: 140px;" />
        <span style="color: #64748b; font-weight: 600;">-</span>
        <input type="date" v-model="boLocDenNgay" class="filter-select" style="min-width: 140px;" />
      </div>

      <select v-model="boLocTrangThai" class="filter-select" style="min-width: 200px;">
        <option value="all">Trạng thái: Tất cả</option>
        <option value="0">Chờ xử lý</option>
        <option value="1">Đã thanh toán</option>
        <option value="2">Hủy bỏ</option>
      </select>
    </section>

    <div v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </div>

    <!-- Bảng Dữ Liệu & Khung Xem Nhanh -->
    <section class="content-grid" v-if="danhSachHoaDonDaLoc.length > 0 || dangTai">
      <!-- Cột Trái: Bảng -->
      <article class="table-card">
        <div class="table-head">
          <div>
            <h2>Danh sách hóa đơn</h2>
            <p>{{ danhSachHoaDonDaLoc.length }} hóa đơn phù hợp.</p>
          </div>
          <span class="table-chip">{{ dangTai ? "Đồng bộ..." : "Dữ liệu trực tiếp" }}</span>
        </div>

        <div class="customer-table" v-if="danhSachHoaDonDaLoc.length > 0">
          <div class="customer-table__row customer-table__row--head">
            <span>Mã HĐ</span>
            <span>Khách / Nhóm đặt</span>
            <span>Mã GD</span>
            <span>Ngày tạo</span>
            <span style="text-align: right;">Tổng tiền</span>
            <span style="text-align: center;">Trạng thái</span>
          </div>

          <div
            v-for="(invoice, index) in danhSachHoaDonPhanTrang"
            :key="invoice.ma_hoa_don"
            class="customer-table__row"
            :class="{ 'is-selected': hoaDonDangChon && hoaDonDangChon.ma_hoa_don === invoice.ma_hoa_don }"
            @click="chonHoaDon(invoice)"
          >
            <div class="customer-id">
               <span class="muted" style="font-size: 0.75rem;">{{ invoice.loai_hoa_don === 0 ? 'Tour' : 'Dịch vụ' }}</span><br>
              <strong>{{ invoice.ma_hoa_don }}</strong>
            </div>

            <div class="customer-person">
              <div>
                <strong>{{ invoice.nhom?.Ten_nhom || invoice.nhom?.ten_nhom || (invoice.nhom ? 'Nhóm chưa đặt tên' : 'Khách lẻ') }}</strong>
                <small v-if="invoice.nhom" style="display:block; margin-top:2px; color: #64748b;">Mã nhóm: {{ invoice.ma_nhom }}</small>
              </div>
            </div>

            <div>
               <span class="muted" style="font-family: monospace; font-size: 0.85rem;">{{ invoice.ma_giao_dich || '---' }}</span>
            </div>

            <span class="muted" style="font-size: 0.85rem;">{{ dinhDangNgayGio(invoice.ngay_tao) }}</span>
            
            <strong style="color: #0f172a; text-align: right;">{{ dinhDangTienTe(invoice.tong_tien) }}</strong>

            <div style="display: flex; justify-content: center;">
              <span :class="['pill', layLopMauTrangThai(invoice.trang_thai_thanh_toan)]">
                {{ layNhanTrangThai(invoice.trang_thai_thanh_toan) }}
              </span>
            </div>
          </div>
        </div>
        
        <div v-else class="empty-state" style="border: none; min-height: 200px;">
          <p class="muted">Không có dữ liệu.</p>
        </div>

        <footer class="table-footer" v-if="danhSachHoaDonDaLoc.length > 0">
          <span>
            Hiển thị {{ chiSoBatDau }}-{{ chiSoKetThuc }} / {{ danhSachHoaDonDaLoc.length }} hóa đơn
          </span>

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
            <button
              class="page-btn"
              type="button"
              :disabled="page === tongSoTrang"
              @click="page++"
            >
              <i class="fas fa-angle-right"></i>
            </button>
          </div>
        </footer>
      </article>

      <!-- Cột Phải: Xem nhanh (Profile Card Đồng bộ UX) -->
      <aside class="profile-card">
        <div v-if="hoaDonDangChon">
          <div class="profile-card__header">
            <div class="profile-card__avatar" :class="layLopMauAvatar(hoaDonDangChon.trang_thai_thanh_toan)">
              <i class="fas fa-file-invoice"></i>
            </div>
            <div>
              <p class="profile-card__eyebrow">Xem nhanh hóa đơn</p>
              <h2>{{ hoaDonDangChon.ma_hoa_don }}</h2>
              <div class="profile-card__tags">
                <span class="tag tag--slate">{{ hoaDonDangChon.loai_hoa_don === 0 ? 'Tour' : 'Dịch vụ' }}</span>
                <span :class="['tag', layLopMauTrangThai(hoaDonDangChon.trang_thai_thanh_toan, true)]">
                  {{ layNhanTrangThai(hoaDonDangChon.trang_thai_thanh_toan) }}
                </span>
              </div>
            </div>
          </div>

          <div class="profile-card__body">
             <div class="profile-row">
              <span>Đại diện / Nhóm</span>
              <strong>{{ hoaDonDangChon.nhom?.Ten_nhom || hoaDonDangChon.nhom?.ten_nhom || (hoaDonDangChon.nhom ? 'Nhóm chưa đặt tên' : 'Khách lẻ') }}</strong>
            </div>
             <div class="profile-row">
              <span>Mã ĐT (Tour/DV)</span>
              <strong>{{ hoaDonDangChon.ma_doi_tuong }}</strong>
            </div>
            <div class="profile-row">
              <span>Kênh Giao Dịch</span>
              <strong>{{ hoaDonDangChon.ma_giao_dich || 'Chưa thanh toán' }}</strong>
            </div>
            <div class="profile-row">
              <span>Ngày tạo lập</span>
              <strong>{{ dinhDangNgayGio(hoaDonDangChon.ngay_tao) }}</strong>
            </div>
            <div class="profile-row" style="margin-top: 0.5rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0;">
              <span>Tổng thanh toán</span>
              <strong style="color: #2563eb; font-size: 1.25rem;">{{ dinhDangTienTe(hoaDonDangChon.tong_tien) }}</strong>
            </div>
          </div>

          <div class="profile-card__actions" style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
             <label style="font-size: 0.85rem; font-weight: 700; color: #475569;">CHUYỂN TRẠNG THÁI</label>
             <div style="display: flex; gap: 0.5rem; align-items: center;">
                 <select v-model="trangThaiDangSua" class="filter-select" style="flex: 1;">
                    <option :value="0">Chờ xử lý</option>
                    <option :value="1">Đã thanh toán (Thành công)</option>
                    <option :value="2">Hủy bỏ / Thất bại</option>
                 </select>
                 <button class="profile-link profile-link--primary" @click="luuTrangThaiHoaDon" :disabled="dangLuu" style="min-width: 100px; padding: 0;">
                    <i class="fas fa-spinner fa-spin" v-if="dangLuu"></i>
                    <span v-else>Cập nhật</span>
                 </button>
             </div>
             <router-link :to="`/admin/hoa-don/${hoaDonDangChon.ma_hoa_don}`" class="profile-link" style="margin-top: 0.5rem; background: #f8fafc; color: #3b82f6; border: 1px solid #bfdbfe;">
                 Xem chi tiết hóa đơn
             </router-link>
          </div>
        </div>

        <!-- Trống khi chưa chọn -->
        <div v-else class="profile-card__empty">
          <i class="fas fa-hand-pointer"></i>
          <h2>Chưa chọn hóa đơn</h2>
          <p>Hãy chọn một dòng trong bảng bên trái để xem chi tiết thông tin và thực hiện cập nhật trạng thái hóa đơn.</p>
        </div>
      </aside>
    </section>

    <div v-else class="empty-state">
      <i class="fas fa-box-open"></i>
      <h2>Không có dữ liệu</h2>
      <p>Hệ thống hiện chưa có hóa đơn nào.</p>
    </div>
  </div>
</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import { showAlert, showConfirm } from "../../../services/appDialog";

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
      dangLuu: false,
    };
  },
  computed: {
    soHoaDonDaThanhToan() { return this.danhSachHoaDon.filter(inv => parseInt(inv.trang_thai_thanh_toan) === 1).length; },
    soHoaDonChoXuLy() { return this.danhSachHoaDon.filter(inv => parseInt(inv.trang_thai_thanh_toan) === 0).length; },
    soHoaDonDaHuy() { return this.danhSachHoaDon.filter(inv => parseInt(inv.trang_thai_thanh_toan) === 2).length; },
    tongDoanhThu() {
      return this.danhSachHoaDon
        .filter(inv => parseInt(inv.trang_thai_thanh_toan) === 1)
        .reduce((sum, inv) => sum + parseFloat(inv.tong_tien || 0), 0);
    },
    danhSachHoaDonDaLoc() {
      let result = [...this.danhSachHoaDon];
      if (this.search) {
        const query = this.search.toLowerCase();
        result = result.filter(inv => {
          const maHD = (inv.ma_hoa_don || '').toLowerCase();
          const maGiaoDich = (inv.ma_giao_dich || '').toLowerCase();
          const tenNhom = (inv.nhom?.Ten_nhom || inv.nhom?.ten_nhom || '').toLowerCase();
          return maHD.includes(query) || maGiaoDich.includes(query) || tenNhom.includes(query);
        });
      }
      if (this.boLocTrangThai !== "all") {
        result = result.filter(inv => parseInt(inv.trang_thai_thanh_toan) === parseInt(this.boLocTrangThai));
      }
      if (this.boLocTuNgay) {
        const fromD = new Date(this.boLocTuNgay);
        fromD.setHours(0, 0, 0, 0);
        result = result.filter(inv => new Date(inv.ngay_tao) >= fromD);
      }
      if (this.boLocDenNgay) {
        const toD = new Date(this.boLocDenNgay);
        toD.setHours(23, 59, 59, 999);
        result = result.filter(inv => new Date(inv.ngay_tao) <= toD);
      }
      result.sort((a, b) => new Date(b.ngay_tao) - new Date(a.ngay_tao));
      return result;
    },
    tongSoTrang() { return Math.ceil(this.danhSachHoaDonDaLoc.length / this.soLuongMoiTrang) || 1; },
    danhSachHoaDonPhanTrang() {
      const start = (this.page - 1) * this.soLuongMoiTrang;
      return this.danhSachHoaDonDaLoc.slice(start, start + this.soLuongMoiTrang);
    },
    chiSoBatDau() { return this.danhSachHoaDonDaLoc.length === 0 ? 0 : (this.page - 1) * this.soLuongMoiTrang + 1; },
    chiSoKetThuc() { return Math.min(this.page * this.soLuongMoiTrang, this.danhSachHoaDonDaLoc.length); },
    danhSachTrangHienThi() {
      const pages = [];
      const total = this.tongSoTrang;
      const current = this.page;
      let start = Math.max(1, current - 2);
      let end = Math.min(total, start + 4);
      if (end - start < 4) { start = Math.max(1, end - 4); }
      for (let i = start; i <= end; i++) pages.push(i);
      return pages;
    }
  },
  watch: {
    search() { this.page = 1; },
    boLocTrangThai() { this.page = 1; },
    boLocTuNgay() { this.page = 1; },
    boLocDenNgay() { this.page = 1; },
    danhSachHoaDonPhanTrang(newList) {
        // Auto select first item if list changes and current selection is gone
        if (newList.length > 0 && (!this.hoaDonDangChon || !newList.find(i => i.ma_hoa_don === this.hoaDonDangChon.ma_hoa_don))) {
            this.chonHoaDon(newList[0]);
        } else if (newList.length === 0) {
            this.hoaDonDangChon = null;
        }
    }
  },
  mounted() {
    this.taiDanhSachHoaDon();
  },
  methods: {
    async taiDanhSachHoaDon() {
      this.dangTai = true;
      this.thongBaoLoi = "";
      try {
        const response = await goiApi('/api/admin/hoa-don', {
          headers: {
            Accept: 'application/json',
          },
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
          throw new Error(payload?.message || 'Không thể tải danh sách hóa đơn.');
        }
        if (payload?.success) {
          this.danhSachHoaDon = payload.data || [];
          if(this.danhSachHoaDon.length > 0) this.chonHoaDon(this.danhSachHoaDon[0]);
        } else {
          this.danhSachHoaDon = [];
        }
      } catch (error) {
        console.error("Lỗi:", error);
        this.thongBaoLoi = "Đã xảy ra lỗi khi lấy danh sách hóa đơn.";
        this.danhSachHoaDon = [];
      } finally {
        this.dangTai = false;
      }
    },
    chonHoaDon(invoice) {
        this.hoaDonDangChon = invoice;
        this.trangThaiDangSua = parseInt(invoice.trang_thai_thanh_toan);
    },
    async luuTrangThaiHoaDon() {
      if(!this.hoaDonDangChon) return;
      
      const newStatusLabel = this.layNhanTrangThai(this.trangThaiDangSua);
      const confirmed = await showConfirm({
        title: "Xác nhận chuyển trạng thái",
        message: `Bạn có chắc muốn chuyển hóa đơn ${this.hoaDonDangChon.ma_hoa_don} sang trạng thái "${newStatusLabel}"?`,
        tone: "warning",
        confirmText: "Đồng ý",
        cancelText: "Hủy",
      });
      
      if (!confirmed) return;

      this.dangLuu = true;
      try {
        const response = await goiApi(`/api/admin/hoa-don/${this.hoaDonDangChon.ma_hoa_don}/status`, {
          method: 'PATCH',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            trang_thai_thanh_toan: parseInt(this.trangThaiDangSua)
          }),
        });
        const payload = await response.json().catch(() => ({}));
        
        if (response.ok && payload?.success) {
          const index = this.danhSachHoaDon.findIndex(inv => inv.ma_hoa_don === this.hoaDonDangChon.ma_hoa_don);
          if (index !== -1) {
            this.danhSachHoaDon[index].trang_thai_thanh_toan = this.trangThaiDangSua;
          }
          await showAlert({
            title: "Cập nhật thành công",
            message: `Hóa đơn ${this.hoaDonDangChon.ma_hoa_don} đã được chuyển sang "${newStatusLabel}".`,
            tone: "success"
          });
        } else {
           await showAlert({
            title: "Cập nhật thất bại",
            message: "Lỗi: " + (payload?.message || 'Hệ thống từ chối cập nhật'),
            tone: "danger"
          });
        }
      } catch (error) {
         await showAlert({
            title: "Lỗi hệ thống",
            message: "Đã xảy ra lỗi trong quá trình kết nối đến máy chủ.",
            tone: "danger"
          });
      } finally {
        this.dangLuu = false;
      }
    },
    dinhDangTienTe(value) {
      if (!value) return "0 ₫";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    dinhDangNgayGio(dateString) {
      if (!dateString) return "---";
      const d = new Date(dateString);
      return d.toLocaleDateString('vi-VN') + ' ' + d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },
    layNhanTrangThai(status) {
      switch (parseInt(status)) {
        case 0: return "Chờ xử lý";
        case 1: return "Đã thanh toán";
        case 2: return "Hủy bỏ";
        default: return "Không rõ";
      }
    },
    layLopMauTrangThai(status, isTag = false) {
      const prefix = isTag ? 'tag--' : 'pill--';
      switch (parseInt(status)) {
        case 0: return prefix + "amber"; // Cam chờ
        case 1: return prefix + "green"; // Xanh lá thành công
        case 2: return prefix + "rose";  // Đỏ hủy
        default: return prefix + "slate";
      }
    },
    layLopMauAvatar(status) {
        switch (parseInt(status)) {
            case 0: return "profile-card__avatar--amber";
            case 1: return "profile-card__avatar--green";
            case 2: return "profile-card__avatar--rose";
            default: return "profile-card__avatar--blue";
        }
    }
  }
};
</script>

<style scoped>
/* Đồng bộ 100% với DayConfig Admin UX Pattern */
.invoice-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background:
    radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24%),
    linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.invoice-page__hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.invoice-page__eyebrow {
  margin: 0 0 0.4rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.invoice-page__hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.35rem, 4vw, 3.2rem);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.invoice-page__subtitle {
  margin: 0.8rem 0 0;
  color: #5f7191;
  font-size: 1.05rem;
  max-width: 760px;
}

.invoice-page__hero-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.ghost-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  min-height: 3.2rem;
  padding: 0 1.25rem;
  border-radius: 1rem;
  font-weight: 800;
  cursor: pointer;
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, 0.88);
  color: #334155;
  transition: 0.2s;
}
.ghost-button:hover { background: #f8fafc; color: #0f172a; }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

.stats-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.45rem;
  border-radius: 1.6rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
}

.stats-card__icon {
  width: 4.25rem;
  height: 4.25rem;
  border-radius: 1.25rem;
  display: grid;
  place-items: center;
  font-size: 1.45rem;
}
.stats-card__icon--blue { background: #eaf4ff; color: #0369a1; }
.stats-card__icon--green { background: #eaf9f1; color: #08986c; }
.stats-card__icon--amber { background: #fff7e8; color: #d97706; }
.stats-card__icon--violet { background: #efefff; color: #4f25f4; }
.stats-card__icon--sky { background: #eef2ff; color: #4338ca; }

.stats-card span { display: block; color: #60708d; font-weight: 700; font-size: 0.85rem;}
.stats-card strong { display: block; margin-top: 0.35rem; color: #111b39; font-size: 1.65rem; line-height: 1; font-weight: 900;}

.filter-bar {
  display: grid;
  grid-template-columns: minmax(0, 2fr) auto auto; /* Fix 3 elements */
  gap: 1rem;
  padding: 1.15rem;
  border-radius: 1.6rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
  margin-bottom: 1.5rem;
}

.filter-search, .filter-select {
  display: flex; align-items: center; gap: 0.8rem;
  min-height: 3.45rem; padding: 0 1rem;
  border-radius: 1rem; background: #f7f8fc; border: 1px solid #eef1f7; color: #324053;
}
.filter-search input, .filter-select { border: 0; outline: none; font-size: 1rem;}
.filter-search input { width: 100%; background: transparent; }

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.9fr) minmax(340px, 0.9fr); /* SPLIT VIEW */
  gap: 1.5rem;
}

.table-card, .profile-card, .empty-state {
  border-radius: 1.75rem;
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
  padding: 1.35rem 1.4rem 1rem;
}

.table-card { overflow: hidden; display: flex; flex-direction: column; }

.table-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1rem;}
.table-head h2 { margin: 0; color: #111827; font-size: 1.35rem; font-weight: 900; }
.table-head p { margin: 0.4rem 0 0; color: #64748b; font-size: 0.9rem;}
.table-chip { padding: 0.7rem 1rem; border-radius: 0.95rem; background: #eef2ff; color: #4338ca; font-weight: 800; font-size: 0.85rem;}

.customer-table { display: grid; width: 100%; }

.customer-table__row {
  display: grid;
  grid-template-columns: 80px 2.5fr 1fr 1.2fr 1.5fr 120px;
  gap: 1rem; align-items: center;
  padding: 1.2rem 0;
  border-top: 1px solid #eef2f7;
  cursor: pointer;
}
.customer-table__row--head { border-top: 0; padding-top: 0.1rem; color: #637594; font-size: 0.82rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.12em; cursor: default;}
.customer-table__row.is-selected { background: #f8fbff; border-radius: 8px; padding-left: 1rem; padding-right: 1rem; margin-left:-1rem; margin-right:-1rem; width: calc(100% + 2rem); box-sizing: border-box; }

.customer-id strong { color: #0f172a; }
.customer-person strong { display: block; color: #111b39; }
.muted { color: #66768f; }

.pill, .tag {
  display: inline-flex; align-items: center; justify-content: center; width: fit-content; font-weight: 800;
}
.pill { min-height: 1.95rem; padding: 0 0.95rem; border-radius: 999px; font-size: 0.8rem;}
.pill--green { background: #dcfce7; color: #16a34a; }
.pill--rose { background: #ffe4e6; color: #e11d48; }
.pill--amber { background: #fef3c7; color: #d97706; }
.pill--slate { background: #f1f5f9; color: #475569; }

.tag { min-height: 2.2rem; padding: 0 1rem; border-radius: 999px; font-size: 0.85rem;}
.tag--slate { background: #f1f5f9; color: #475569; }
.tag--green { background: #dcfce7; color: #16a34a; }
.tag--rose { background: #ffe4e6; color: #e11d48; }
.tag--amber { background: #fef3c7; color: #d97706; }

.table-footer { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding-top: 1.2rem; border-top: 1px solid #eef2f7; margin-top: auto;}
.table-footer span { color: #64748b; font-size: 0.9rem; }

.pagination { display: flex; align-items: center; gap: 0.65rem; }
.page-btn {
  width: 3rem; height: 3rem; padding: 0; display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid #eceff6; border-radius: 1rem; background: #ffffff; color: #55657f; font-weight: 800; font-size: 0.95rem; cursor: pointer; transition: 0.2s;
}
.page-btn:hover:not(:disabled) { border-color: #cbd5e1; }
.page-btn.is-active { border-color: transparent; background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%); color: #ffffff; box-shadow: 0 12px 24px rgba(79, 37, 244, 0.22); }
.page-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* PROFILE CARD */
.profile-card { align-self: start; position: sticky; top: 1.5rem; }
.profile-card__header { display: flex; align-items: center; gap: 1rem; padding-bottom: 1.25rem; border-bottom: 1px solid #eef2f7; }
.profile-card__avatar { width: 4.2rem; height: 4.2rem; border-radius: 1.3rem; display: grid; place-items: center; font-size: 1.5rem; }
.profile-card__avatar--green { background: #eaf9f1; color: #08986c; }
.profile-card__avatar--amber { background: #fff7e8; color: #d97706; }
.profile-card__avatar--rose { background: #ffe4e6; color: #e11d48; }
.profile-card__avatar--blue { background: #eaf4ff; color: #0369a1; }

.profile-card__eyebrow { margin: 0 0 0.4rem; color: #2453ff; text-transform: uppercase; letter-spacing: 0.12em; font-size: 0.72rem; font-weight: 900; }
.profile-card__header h2 { margin: 0 0 0.55rem; color: #111827; font-size: 1.45rem; font-weight: 900; }
.profile-card__tags { display: flex; gap: 0.55rem; flex-wrap: wrap; }

.profile-card__body { display: grid; gap: 0.95rem; padding: 1.3rem 0; }
.profile-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; font-size: 0.95rem; }
.profile-row span { color: #64748b; }
.profile-row strong { color: #0f172a; text-align: right; max-width: 65%; overflow-wrap: anywhere; }

.profile-link { min-height: 3rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 1rem; text-decoration: none; font-weight: 800; cursor: pointer; border: none;}
.profile-link--primary { background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%); color: #ffffff; }

.profile-card__empty { min-height: 420px; display: grid; place-items: center; text-align: center; color: #64748b; }
.profile-card__empty i { width: 5rem; height: 5rem; display: grid; place-items: center; border-radius: 1.5rem; margin: 0 auto 1rem; background: #f4f7fb; color: #2453ff; font-size: 1.5rem; }
.profile-card__empty h2 { margin: 0 0 0.55rem; color: #111827; font-size: 1.45rem; font-weight: 900; }

.notice { display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.1rem; border-radius: 1rem; margin-bottom: 1rem; }
.notice--error { background: #fff1f2; color: #be123c; }

@media (max-width: 1360px) { .content-grid { grid-template-columns: 1fr; } .profile-card { position: static; } }
@media (max-width: 1280px) { .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } .filter-bar { grid-template-columns: 1fr; } }
@media (max-width: 1024px) { .invoice-page { padding: 1rem; } .invoice-page__hero { flex-direction: column; } .customer-table__row { grid-template-columns: 80px 2.5fr 1fr 1.2fr 1.5fr; } .customer-table__row > :nth-child(6), .customer-table__row--head > :nth-child(6) { display: none; } }
</style>

