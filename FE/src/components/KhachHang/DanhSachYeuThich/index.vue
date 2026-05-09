<template>
  <div class="favorite-page">
    <div class="favorite-layout">
      <CustomerSidebar />

      <section class="favorite-content">
        <header class="favorite-hero">
          <div>
            <p class="favorite-hero__eyebrow">Yêu thích của tôi</p>
            <h1>Danh sách yêu thích</h1>
            <p class="favorite-hero__subtitle">
              Lưu các điểm đến bạn quan tâm và thao tác nhanh để thêm, sửa hoặc xóa khỏi danh sách.
            </p>
          </div>
          <div class="favorite-hero__actions">
            <button class="primary-link" type="button" @click="openCreateDialog">
              <i class="fas fa-plus"></i>
              <span>Thêm yêu thích</span>
            </button>
            <button class="ghost-button" type="button" :disabled="dang_tai_danh_sach" @click="taiDanhSachYeuThich">
              <i class="fas fa-rotate-right"></i>
              <span>{{ dang_tai_danh_sach ? "Đang tải..." : "Tải lại" }}</span>
            </button>
          </div>
        </header>

        <section class="stats-row">
          <article class="stats-tile">
            <span>Tổng mục yêu thích</span>
            <strong>{{ danhSachYeuThich.length }}</strong>
          </article>
          <article class="stats-tile">
            <span>Đang hiển thị</span>
            <strong>{{ danhSachYeuThichHienThi.length }}</strong>
          </article>
          <article class="stats-tile">
            <span>Điểm trung bình</span>
            <strong>{{ diemDanhGiaTrungBinh }}</strong>
          </article>
        </section>

        <section class="toolbar">
          <label class="toolbar__search">
            <i class="fas fa-search"></i>
            <input v-model.trim="search" type="text" placeholder="Tìm theo tên địa điểm, địa chỉ...">
          </label>
        </section>

        <section v-if="thong_bao.text" class="notice" :class="thong_bao.type === 'success' ? 'notice--success' : 'notice--info'">
          <i :class="thong_bao.type === 'success' ? 'fas fa-circle-check' : 'fas fa-circle-info'"></i>
          <span>{{ thong_bao.text }}</span>
        </section>

        <section v-if="danh_sach_loi.length" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ danh_sach_loi.join(' ') }}</span>
        </section>

        <section v-else-if="dang_tai_danh_sach" class="notice notice--info">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang tải danh sách yêu thích...</span>
        </section>

        <section v-else-if="!danhSachYeuThichHienThi.length" class="empty-state">
          <i class="fas fa-heart-crack"></i>
          <h2>Chưa có địa điểm yêu thích</h2>
          <p>{{ search ? 'Không có kết quả phù hợp từ khóa tìm kiếm.' : 'Hãy thêm địa điểm vào danh sách yêu thích của bạn.' }}</p>
          <button class="primary-link" type="button" @click="openCreateDialog">Thêm yêu thích</button>
        </section>

        <section v-else class="favorite-grid">
          <article v-for="item in danhSachYeuThichHienThi" :key="item.id" class="favorite-card">
            <div class="favorite-card__media">
              <img :src="item.hinhAnh" :alt="item.ten" @error="loiHinhAnh">
              <span class="favorite-card__rating">
                <i class="fas fa-star"></i>
                {{ item.danhGia }}
              </span>
            </div>

            <div class="favorite-card__content">
              <div>
                <h2>{{ item.ten }}</h2>
                <p class="favorite-card__location">
                  <i class="fas fa-location-dot"></i>
                  {{ item.diaChi }}
                </p>
              </div>

              <div class="favorite-card__actions">
                <button type="button" class="plan-button" @click="themVaoKeHoach(item)">
                  <i class="fas fa-calendar-plus"></i>
                  Thêm vào kế hoạch
                </button>
                <button type="button" class="ghost-button" @click="openEditDialog(item)">
                  <i class="fas fa-pen"></i>
                  Sửa
                </button>
                <button type="button" class="danger-button" @click="openDeleteDialog(item)">
                  <i class="fas fa-trash"></i>
                  Xóa
                </button>
              </div>
            </div>
          </article>
        </section>
      </section>
    </div>

    <div v-if="createDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card">
        <p class="dialog-card__eyebrow">Thêm mục yêu thích</p>
        <h2>Chọn địa điểm muốn lưu</h2>
        <label class="field">
          <span>Địa điểm</span>
          <select v-model="form.ma_dia_diem">
            <option value="">-- Chọn địa điểm --</option>
            <option v-for="item in availableCreateDestinations" :key="`create-${item.id}`" :value="item.id">
              {{ item.name }}
            </option>
          </select>
          <small v-if="errors.ma_dia_diem">{{ errors.ma_dia_diem }}</small>
        </label>
        <div class="dialog-card__actions">
          <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="primary-link" type="button" :disabled="isSubmitting" @click="createFavorite">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang thêm..." : "Xác nhận thêm" }}</span>
          </button>
        </div>
      </section>
    </div>

    <div v-if="editDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card">
        <p class="dialog-card__eyebrow">Cập nhật mục yêu thích</p>
        <h2>Sửa {{ yeuThichDangChon?.ten }}</h2>
        <label class="field">
          <span>Địa điểm mới</span>
          <select v-model="form.ma_dia_diem">
            <option value="">-- Chọn địa điểm --</option>
            <option v-for="item in availableEditDestinations" :key="`edit-${item.id}`" :value="item.id">
              {{ item.name }}
            </option>
          </select>
          <small v-if="errors.ma_dia_diem">{{ errors.ma_dia_diem }}</small>
        </label>
        <div class="dialog-card__actions">
          <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="primary-link" type="button" :disabled="isSubmitting" @click="updateFavorite">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang cập nhật..." : "Lưu thay đổi" }}</span>
          </button>
        </div>
      </section>
    </div>

    <div v-if="deleteDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card dialog-card--danger">
        <p class="dialog-card__eyebrow">Xác nhận xóa</p>
        <h2>Xóa {{ yeuThichDangChon?.ten }} khỏi yêu thích?</h2>
        <p class="dialog-text">Thao tác này sẽ xóa mục khỏi danh sách yêu thích của bạn.</p>
        <div class="dialog-card__actions">
          <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="danger-button" type="button" :disabled="isSubmitting" @click="deleteFavorite">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang xóa..." : "Xóa" }}</span>
          </button>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import CustomerSidebar from '../CustomerSidebar.vue';
import { goiApi } from "../../../services/httpClient.js";
import { API_BASE, buildHeaders, createInitials, getStoredCustomerId, getStoredUser } from "../../Shared/customerSession";

const FALLBACK_IMAGE = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80";

async function goiDuLieu(url, { method = "GET", body, params, headers } = {}) {
  const phanHoi = await goiApi(url, { method, body, params, headers });
  const duLieu = await phanHoi.json();

  if (!phanHoi.ok) {
    const loi = new Error(duLieu?.message || "Không thể hoàn tất yêu cầu.");
    loi.phanHoi = { data: duLieu, status: phanHoi.status };
    throw loi;
  }

  return { data: duLieu, status: phanHoi.status };
}

import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "DanhSachYeuThichKhachHangPage",
  components: {
    CustomerSidebar,
  },
  computed: {
    danhSachYeuThichHienThi() {
      return this.filteredFavorites.slice(0, 10);
    },
    filteredFavorites() {
      const keyword = String(this.search || "").trim().toLowerCase();
      if (!keyword) return this.danhSachYeuThich;
      return this.danhSachYeuThich.filter((item) => {
        const haystack = [item.ten, item.diaChi].join(" ").toLowerCase();
        return haystack.includes(keyword);
      });
    },
    hoSoKhachHang() {
      return getStoredUser() || {};
    },
    tenKhachHang() {
      return this.hoSoKhachHang.Ho_va_ten || this.hoSoKhachHang.ho_va_ten || "Tài khoản của tôi";
    },
    chuVietTatKhachHang() {
      return createInitials(this.tenKhachHang, "KH");
    },
    nhanThanhVien() {
      return this.maKhachHang ? `Mã khách hàng ${this.maKhachHang}` : "Vui lòng đăng nhập để đồng bộ";
    },
    diemDanhGiaTrungBinh() {
      if (!this.danhSachYeuThich.length) return "0.0";
      const sum = this.danhSachYeuThich.reduce((total, item) => total + Number(item.danhGia || 0), 0);
      return (sum / this.danhSachYeuThich.length).toFixed(1);
    },
    availableCreateDestinations() {
      const currentIds = new Set(this.danhSachYeuThich.map((item) => String(item.maDiaDiem)));
      return this.destinations.filter((item) => !currentIds.has(String(item.id)));
    },
    availableEditDestinations() {
      if (!this.yeuThichDangChon) return this.destinations;
      const currentIds = new Set(this.danhSachYeuThich
        .filter((item) => item.id !== this.yeuThichDangChon.id)
        .map((item) => String(item.maDiaDiem)));
      return this.destinations.filter((item) => !currentIds.has(String(item.id)));
    },
  },
  data() {
    return {
      maKhachHang: "",
      dang_tai_danh_sach: false,
      isSubmitting: false,
      thong_bao: {
        type: "success",
        text: "",
      },
      danh_sach_loi: [],
      danhSachYeuThich: [],
      destinations: [],
      search: "",
      createDialogOpen: false,
      editDialogOpen: false,
      deleteDialogOpen: false,
      yeuThichDangChon: null,
      form: {
        ma_dia_diem: "",
      },
      errors: {
        ma_dia_diem: "",
      },
    };
  },
  async mounted() {
    this.maKhachHang = getStoredCustomerId();
    await Promise.all([this.taiDanhSachYeuThich(), this.taiDiemDen()]);
  },
  methods: {
    thongBao(type, text) {
      toaster.success({ type, text });
      if (!text) return;
      window.setTimeout(() => {
        if (this.thong_bao.text === text) {
          toaster.success({ type: "success", text: "" });
        }
      }, 3500);
    },
    clearMessages() {
      this.danh_sach_loi = [];
      this.thongBao("success", "");
    },
    buildCustomerParams() {
      return this.maKhachHang ? { ma_khach_hang: this.maKhachHang } : {};
    },
    extractList(duLieuPhanHoi) {
      if (!duLieuPhanHoi) return [];
      if (Array.isArray(duLieuPhanHoi)) return duLieuPhanHoi;
      if (Array.isArray(duLieuPhanHoi.data)) return duLieuPhanHoi.data;
      if (Array.isArray(duLieuPhanHoi.result)) return duLieuPhanHoi.result;
      if (Array.isArray(duLieuPhanHoi.data?.data)) return duLieuPhanHoi.data.data;
      if (Array.isArray(duLieuPhanHoi.result?.data)) return duLieuPhanHoi.result.data;
      return [];
    },
    layThongBaoLoi(error, macDinh) {
      const data = error?.phanHoi?.data;
      if (typeof data === "string") return data;
      if (data?.message) return data.message;
      if (data?.errors) {
        const list = Object.values(data.errors).flat();
        if (list.length) return list.join(" ");
      }
      return error?.message || macDinh;
    },
    normalizeDestination(item = {}) {
      return {
        id: String(item?.ma_dia_diem || item?.Ma_dia_diem || item?.id || item?.Id || ""),
        name: item?.ten_dia_diem || item?.Ten_dia_diem || item?.ten || item?.name || "Địa điểm",
      };
    },
    chuanHoaYeuThich(item = {}) {
      const diaDiem = item?.dia_diem || item?.diaDiem || item?.Dia_diem || item?.location || {};
      const id = String(item?.ma_danh_sach_ua_thich || item?.Ma_danh_sach_ua_thich || item?.id || item?.Id || "");
      const maDiaDiem = String(item?.ma_dia_diem || item?.Ma_dia_diem || diaDiem?.ma_dia_diem || diaDiem?.Ma_dia_diem || "");
      const ten = diaDiem?.ten_dia_diem || diaDiem?.Ten_dia_diem || diaDiem?.name || "Địa điểm yêu thích";
      const diaChi = diaDiem?.dia_chi || diaDiem?.Dia_chi || diaDiem?.diaChi || "Chưa có địa chỉ";
      const hinhAnh = diaDiem?.hinh_anh || diaDiem?.Hinh_anh || diaDiem?.image || FALLBACK_IMAGE;
      const danhGia = Number(diaDiem?.danh_gia ?? diaDiem?.Danh_gia ?? diaDiem?.rating ?? 4.8).toFixed(1);

      return {
        raw: item,
        id,
        maDiaDiem,
        ten,
        diaChi,
        hinhAnh,
        danhGia,
      };
    },
    async taiDiemDen() {
      try {
        const res = await goiDuLieu(`${API_BASE}/dia-diem`, {
          headers: buildHeaders(),
        });
        this.destinations = this.extractList(res.data)
          .map((item) => this.normalizeDestination(item))
          .filter((item) => item.id);
      } catch {
        this.destinations = [];
      }
    },
    async taiDanhSachYeuThich() {
      this.dang_tai_danh_sach = true;
      this.danh_sach_loi = [];

      if (!this.maKhachHang) {
        this.dang_tai_danh_sach = false;
        this.danhSachYeuThich = [];
        this.danh_sach_loi = ["Không tìm thấy mã khách hàng trong phiên đăng nhập hiện tại."];
        return;
      }

      try {
        const res = await goiDuLieu(`${API_BASE}/khach-hang/danh-sach-yeu-thich`, {
          params: this.buildCustomerParams(),
          headers: buildHeaders(),
        });

        this.danhSachYeuThich = this.extractList(res.data).map((item) => this.chuanHoaYeuThich(item));
      } catch (error) {
        this.danhSachYeuThich = [];
        this.danh_sach_loi = [this.layThongBaoLoi(error, "Không thể tải danh sách yêu thích.")];
      } finally {
        this.dang_tai_danh_sach = false;
      }
    },
    resetForm() {
      this.form = { ma_dia_diem: "" };
      this.errors = { ma_dia_diem: "" };
    },
    validateForm() {
      this.errors = { ma_dia_diem: "" };
      if (!String(this.form.ma_dia_diem || "").trim()) {
        this.errors.ma_dia_diem = "Vui lòng chọn địa điểm.";
      }
      return !this.errors.ma_dia_diem;
    },
    openCreateDialog() {
      this.clearMessages();
      this.yeuThichDangChon = null;
      this.resetForm();
      this.createDialogOpen = true;
    },
    openEditDialog(item) {
      this.clearMessages();
      this.yeuThichDangChon = item;
      this.resetForm();
      this.form.ma_dia_diem = String(item.maDiaDiem || "");
      this.editDialogOpen = true;
    },
    openDeleteDialog(item) {
      this.clearMessages();
      this.yeuThichDangChon = item;
      this.deleteDialogOpen = true;
    },
    closeDialogs(force = false) {
      if (this.isSubmitting && !force) return;
      this.createDialogOpen = false;
      this.editDialogOpen = false;
      this.deleteDialogOpen = false;
      this.yeuThichDangChon = null;
      this.resetForm();
    },
    async createFavorite() {
      if (!this.validateForm()) return;
      this.isSubmitting = true;
      this.clearMessages();

      try {
        await goiDuLieu(`${API_BASE}/khach-hang/danh-sach-yeu-thich`, {
          method: "POST",
          body: {
            ...this.buildCustomerParams(),
            ma_dia_diem: this.form.ma_dia_diem,
          },
          headers: buildHeaders(true),
        });

        this.closeDialogs(true);
        await this.taiDanhSachYeuThich();
        this.thongBao("success", "Thêm địa điểm yêu thích thành công.");
      } catch (error) {
        this.danh_sach_loi = [this.layThongBaoLoi(error, "Không thể thêm vào danh sách yêu thích.")];
      } finally {
        this.isSubmitting = false;
      }
    },
    async updateFavorite() {
      if (!this.yeuThichDangChon?.id) return;
      if (!this.validateForm()) return;
      this.isSubmitting = true;
      this.clearMessages();

      try {
        await goiDuLieu(`${API_BASE}/khach-hang/danh-sach-yeu-thich/${this.yeuThichDangChon.id}`, {
          method: "PUT",
          body: {
            ...this.buildCustomerParams(),
            ma_dia_diem: this.form.ma_dia_diem,
          },
          headers: buildHeaders(true),
        });

        this.closeDialogs(true);
        await this.taiDanhSachYeuThich();
        this.thongBao("success", "Cập nhật danh sách yêu thích thành công.");
      } catch (error) {
        this.danh_sach_loi = [this.layThongBaoLoi(error, "Không thể cập nhật danh sách yêu thích.")];
      } finally {
        this.isSubmitting = false;
      }
    },
    async deleteFavorite() {
      if (!this.yeuThichDangChon?.id) return;
      this.isSubmitting = true;
      this.clearMessages();

      try {
        await goiDuLieu(`${API_BASE}/khach-hang/danh-sach-yeu-thich/${this.yeuThichDangChon.id}`, {
          method: "DELETE",
          params: this.buildCustomerParams(),
          headers: buildHeaders(),
        });

        this.closeDialogs(true);
        await this.taiDanhSachYeuThich();
        this.thongBao("success", "Đã xóa khỏi danh sách yêu thích.");
      } catch (error) {
        this.danh_sach_loi = [this.layThongBaoLoi(error, "Không thể xóa khỏi danh sách yêu thích.")];
      } finally {
        this.isSubmitting = false;
      }
    },
    themVaoKeHoach(item) {
      this.thongBao("info", `Đã chọn "${item.ten}" để thêm vào kế hoạch.`);
    },
    loiHinhAnh(event) {
      event.target.src = FALLBACK_IMAGE;
    },
  },
};
</script>

<style scoped>
.favorite-page {
  background: radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%), linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
}

.favorite-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 18px;
  min-height: calc(100vh - 68px);
}

.favorite-content {
  padding: 28px 6px 0 0;
}

.favorite-hero {
  margin-bottom: 24px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}

.favorite-hero__actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.favorite-hero__eyebrow {
  margin: 0 0 10px;
  color: #4f25f4;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.favorite-hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.1rem, 3.3vw, 3rem);
  line-height: 1.06;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.favorite-hero__subtitle {
  margin: 10px 0 0;
  color: #5f6f89;
  max-width: 640px;
  font-size: 1rem;
  line-height: 1.7;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
  margin-bottom: 20px;
}

.stats-tile,
.toolbar,
.notice,
.favorite-card,
.empty-state,
.dialog-card {
  border-radius: 24px;
  border: 1px solid #e2eaf5;
  background: rgba(255, 255, 255, 0.9);
  box-shadow: 0 16px 34px rgba(31, 65, 114, 0.06);
}

.stats-tile {
  padding: 22px;
}

.stats-tile span {
  color: #617891;
  font-weight: 700;
}

.stats-tile strong {
  display: block;
  margin-top: 10px;
  color: #0d2f57;
  font-size: 1.95rem;
  line-height: 1;
  font-weight: 900;
}

.toolbar {
  padding: 18px;
  margin-bottom: 18px;
}

.toolbar__search {
  min-height: 54px;
  padding: 0 18px;
  border-radius: 18px;
  border: 1px solid #dfe7f3;
  background: #f8fbff;
  display: flex;
  align-items: center;
  gap: 12px;
}

.toolbar__search input {
  border: 0;
  outline: none;
  background: transparent;
  color: #24415d;
  width: 100%;
}

.notice {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  margin-bottom: 18px;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.notice--success {
  background: #ecfdf5;
  color: #047857;
}

.notice--info {
  background: #eff6ff;
  color: #1d4ed8;
}

.favorite-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}

.favorite-card {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.favorite-card__media {
  position: relative;
  height: 180px;
  background: #dbeafe;
  flex-shrink: 0;
  overflow: hidden;
}

.favorite-card__media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.favorite-card__rating {
  position: absolute;
  left: 0.7rem;
  bottom: 0.7rem;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  min-height: 1.7rem;
  padding: 0 0.6rem;
  border-radius: 999px;
  background: rgba(147, 197, 253, 0.92);
  color: #0f4573;
  font-size: 0.78rem;
  font-weight: 800;
}

.favorite-card__content {
  display: flex;
  flex: 1;
  flex-direction: column;
  justify-content: space-between;
  gap: 0.8rem;
  padding: 1rem;
}

.favorite-card__content h2 {
  margin: 0 0 0.35rem;
  color: #102f56;
  font-size: 1.06rem;
  font-weight: 900;
}

.favorite-card__location {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  margin: 0;
  color: #3f648b;
}

.favorite-card__actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.favorite-card__actions .plan-button {
  grid-column: 1 / -1;
}

.plan-button,
.ghost-button,
.primary-link,
.danger-button {
  min-height: 44px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 800;
  text-decoration: none;
}

.plan-button,
.primary-link {
  border: 0;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  color: #fff;
}

.ghost-button {
  border: 1px solid #d7e2f0;
  background: #fff;
  color: #24415d;
}

.danger-button {
  border: 0;
  background: linear-gradient(135deg, #dc2626 0%, #be123c 100%);
  color: #fff;
}

.empty-state {
  min-height: 260px;
  display: grid;
  place-items: center;
  text-align: center;
  gap: 10px;
  padding: 28px;
}

.empty-state i {
  width: 72px;
  height: 72px;
  border-radius: 22px;
  display: grid;
  place-items: center;
  background: #e7f2ff;
  color: #0b63a0;
  font-size: 1.5rem;
}

.empty-state p {
  max-width: 560px;
  margin: 0;
  color: #617891;
  line-height: 1.7;
}

.dialog-backdrop {
  position: fixed;
  inset: 0;
  z-index: 50;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(8px);
  display: grid;
  place-items: center;
  padding: 20px;
}

.dialog-card {
  width: min(560px, 100%);
  padding: 24px;
}

.dialog-card__eyebrow {
  margin: 0;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  font-size: 0.76rem;
  font-weight: 900;
}

.dialog-card h2 {
  margin: 10px 0 0;
  color: #111827;
  font-weight: 900;
}

.dialog-text {
  margin-top: 10px;
  color: #64748b;
}

.field {
  display: grid;
  gap: 8px;
  margin-top: 18px;
}

.field span {
  color: #1f2937;
  font-weight: 700;
}

.field select {
  min-height: 48px;
  border-radius: 14px;
  border: 1px solid #dbe4f0;
  padding: 0 14px;
  background: #f8faff;
  outline: none;
}

.field small {
  color: #dc2626;
}

.dialog-card__actions {
  margin-top: 20px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

@media (max-width: 1200px) {
  .favorite-layout {
    grid-template-columns: 1fr;
  }

  .profile-panel {
    border-right: 0;
    border-bottom: 1px solid #e4ecf8;
  }

  .profile-panel__menu {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .favorite-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {
  .favorite-layout {
    width: calc(100% - 20px);
  }

  .favorite-hero {
    flex-direction: column;
  }

  .stats-row,
  .favorite-grid,
  .profile-panel__menu,
  .favorite-card__actions {
    grid-template-columns: 1fr;
  }

  .dialog-card__actions {
    flex-direction: column;
  }
}
</style>
