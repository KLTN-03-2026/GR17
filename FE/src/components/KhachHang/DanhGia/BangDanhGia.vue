<template>
  <div class="review-page">
    <div class="review-layout">
      <CustomerSidebar />

      <section class="review-content">
        <header class="review-hero">
          <div>
            <p class="review-hero__eyebrow">Đánh giá cá nhân</p>
            <h1>Bảng đánh giá của tôi</h1>
            <p class="review-hero__subtitle">
              Theo dõi các đánh giá đã gửi, cập nhật nhanh mức độ hài lòng và nội dung cho từng địa điểm.
            </p>
          </div>

          <div class="review-hero__actions">
            <button class="primary-link" type="button" @click="openCreateDialog">
              <i class="fas fa-plus"></i>
              <span>Tạo đánh giá</span>
            </button>
            <button class="ghost-button" type="button" :disabled="dangTai" @click="taiDuLieu">
              <i class="fas fa-rotate-right"></i>
              <span>{{ dangTai ? "Đang tải..." : "Tải lại" }}</span>
            </button>
          </div>
        </header>

        <section class="stats-row">
          <article class="stats-tile">
            <span>Tổng đánh giá</span>
            <strong>{{ totalReviews }}</strong>
          </article>
          <article class="stats-tile">
            <span>Điểm trung bình</span>
            <strong>{{ averageRating }}</strong>
          </article>
          <article class="stats-tile">
            <span>Địa điểm đã đánh giá</span>
            <strong>{{ reviewedLocations }}</strong>
          </article>
        </section>

        <section class="toolbar">
          <label class="toolbar__search">
            <i class="fas fa-search"></i>
            <input v-model.trim="search" type="text" placeholder="Tìm theo mã, địa điểm, nội dung...">
          </label>
          <select v-model="boLocDiaDiem" class="toolbar__select">
            <option value="all">Địa điểm: Tất cả</option>
            <option v-for="location in danhSachDiaDiem" :key="location.id" :value="location.id">
              {{ location.name }}
            </option>
          </select>
          <select v-model="ratingFilter" class="toolbar__select">
            <option value="all">Số sao: Tất cả</option>
            <option v-for="star in [5,4,3,2,1]" :key="star" :value="String(star)">
              {{ star }} sao
            </option>
          </select>
        </section>

        <div v-if="thongBaoLoi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thongBaoLoi }}</span>
        </div>

        <div v-if="thongBaoThanhCong" class="notice notice--success">
          <i class="fas fa-circle-check"></i>
          <span>{{ thongBaoThanhCong }}</span>
        </div>

        <section v-if="dangTai" class="loading-grid">
          <div class="loading-card"></div>
          <div class="loading-card"></div>
          <div class="loading-card"></div>
        </section>

        <section v-else-if="filteredReviews.length" class="review-table-wrap">
          <table class="review-table">
            <thead>
              <tr>
                <th>Mã DG</th>
                <th>Địa điểm</th>
                <th>Số sao</th>
                <th>Nội dung</th>
                <th>Cập nhật</th>
                <th>Thao tac</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="review in filteredReviews"
                :key="review.id"
                :class="{ 'is-highlighted': review.id === maDanhGiaNoiBat }"
              >
                <td>{{ review.id }}</td>
                <td>
                  <strong>{{ review.tenDiaDiem }}</strong>
                </td>
                <td>
                  <span class="stars">
                    <i
                      v-for="star in 5"
                      :key="`${review.id}-${star}`"
                      class="fas fa-star"
                      :class="{ 'is-off': star > review.rating }"
                    ></i>
                  </span>
                </td>
                <td class="review-content-cell">{{ review.noiDung || "--" }}</td>
                <td>{{ review.updatedAtLabel }}</td>
                <td>
                  <div class="row-actions">
                    <button class="row-action" type="button" title="Sửa đánh giá" @click="openEditDialog(review)">
                      <i class="fas fa-pen"></i>
                    </button>
                    <button class="row-action row-action--danger" type="button" title="Xóa đánh giá" @click="openDeleteDialog(review)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </section>

        <section v-else class="empty-state">
          <i class="fas fa-star"></i>
          <h2>Chưa có đánh giá phù hợp</h2>
          <p>{{ emptyMessage }}</p>
          <button class="primary-link" type="button" @click="openCreateDialog">Tạo đánh giá đầu tiên</button>
        </section>
      </section>
    </div>

    <div v-if="createDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card">
        <p class="dialog-card__eyebrow">Tạo đánh giá mới</p>
        <h2>Gửi đánh giá cho địa điểm</h2>
        <form class="dialog-form" @submit.prevent="createReview">
          <label class="field">
            <span>Địa điểm</span>
            <select v-model="form.ma_dia_diem">
              <option value="">Chọn địa điểm</option>
              <option v-for="location in danhSachDiaDiem" :key="`create-${location.id}`" :value="location.id">
                {{ location.name }}
              </option>
            </select>
            <small v-if="errors.ma_dia_diem">{{ errors.ma_dia_diem }}</small>
          </label>
          <label class="field">
            <span>Số sao</span>
            <select v-model.number="form.so_sao">
              <option v-for="star in [5,4,3,2,1]" :key="`create-star-${star}`" :value="star">
                {{ star }} sao
              </option>
            </select>
            <small v-if="errors.so_sao">{{ errors.so_sao }}</small>
          </label>
          <label class="field">
            <span>Nội dung</span>
            <textarea v-model.trim="form.noi_dung" rows="4" placeholder="Nhập nội dung đánh giá"></textarea>
          </label>
          <div class="dialog-card__actions">
            <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
            <button class="primary-link" type="submit" :disabled="isSubmitting">
              <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
              <span>{{ isSubmitting ? "Đang lưu..." : "Lưu đánh giá" }}</span>
            </button>
          </div>
        </form>
      </section>
    </div>

    <div v-if="editDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card">
        <p class="dialog-card__eyebrow">Cập nhật đánh giá</p>
        <h2>Chỉnh sửa mã đánh giá {{ danhGiaDangChon?.id }}</h2>
        <form class="dialog-form" @submit.prevent="updateReview">
          <label class="field">
            <span>Địa điểm</span>
            <input :value="danhGiaDangChon?.tenDiaDiem || '--'" type="text" disabled>
          </label>
          <label class="field">
            <span>Số sao</span>
            <select v-model.number="form.so_sao">
              <option v-for="star in [5,4,3,2,1]" :key="`edit-star-${star}`" :value="star">
                {{ star }} sao
              </option>
            </select>
            <small v-if="errors.so_sao">{{ errors.so_sao }}</small>
          </label>
          <label class="field">
            <span>Nội dung</span>
            <textarea v-model.trim="form.noi_dung" rows="4" placeholder="Nhập nội dung đánh giá"></textarea>
          </label>
          <div class="dialog-card__actions">
            <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
            <button class="primary-link" type="submit" :disabled="isSubmitting">
              <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
              <span>{{ isSubmitting ? "Đang cập nhật..." : "Lưu thay đổi" }}</span>
            </button>
          </div>
        </form>
      </section>
    </div>

    <div v-if="deleteDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card dialog-card--danger">
        <p class="dialog-card__eyebrow">Xác nhận xóa đánh giá</p>
        <h2>Xóa đánh giá {{ danhGiaDangChon?.id }}?</h2>
        <p class="dialog-card__text">Thao tác này không thể hoàn tác. Vui lòng xác nhận để tiếp tục.</p>
        <div class="dialog-card__actions">
          <button class="ghost-button" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="danger-btn" type="button" :disabled="isSubmitting" @click="deleteReview">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang xóa..." : "Xóa đánh giá" }}</span>
          </button>
        </div>
      </section>
    </div>
  </div>

</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import {
  API_BASE,
  buildHeaders,
  createInitials,
  formatDateDisplay,
  getStoredCustomerId,
  getStoredUser,
  chuanHoaDanhSach,
} from "../../Shared/customerSession";

function normalizeLocation(item) {
  return {
    id: String(item?.ma_dia_diem || item?.Ma_dia_diem || ""),
    name: item?.ten_dia_diem || item?.Ten_dia_diem || "Địa điểm chưa xác định",
  };
}

function normalizeReview(item, locationNameFallback = "") {
  const rating = Number(item?.so_sao || 0);
  return {
    id: String(item?.Ma_danh_gia || item?.ma_danh_gia || item?.id || ""),
    maKhachHang: String(item?.Ma_khach_hang || item?.ma_khach_hang || ""),
    maDiaDiem: String(item?.ma_dia_diem || item?.Ma_dia_diem || ""),
    tenDiaDiem: item?.dia_diem?.ten_dia_diem || locationNameFallback || "Địa điểm chưa xác định",
    rating: Number.isFinite(rating) ? rating : 0,
    noiDung: String(item?.noi_dung || "").trim(),
    createdAtLabel: formatDateDisplay(item?.created_at, true),
    updatedAtLabel: formatDateDisplay(item?.updated_at, true),
  };
}

import CustomerSidebar from "../CustomerSidebar.vue";
import { createToaster } from '@meforma/vue-toaster';
const toaster = createToaster({ position: 'top-right' });

export default {
  name: "BangDanhGia",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      dangTai: false,
      isSubmitting: false,
      thongBaoLoi: "",
      thongBaoThanhCong: "",
      maKhachHang: "",
      danhSachDanhGia: [],
      danhSachDiaDiem: [],
      search: "",
      boLocDiaDiem: "all",
      ratingFilter: "all",
      createDialogOpen: false,
      editDialogOpen: false,
      deleteDialogOpen: false,
      danhGiaDangChon: null,
      maDanhGiaNoiBat: "",
      form: {
        ma_dia_diem: "",
        so_sao: 5,
        noi_dung: "",
      },
      errors: {
        ma_dia_diem: "",
        so_sao: "",
      },
    };
  },
  computed: {
    filteredReviews() {
      const keyword = String(this.search || "").toLowerCase();
      return this.danhSachDanhGia.filter((review) => {
        const matchKeyword = !keyword || [review.id, review.tenDiaDiem, review.noiDung].join(" ").toLowerCase().includes(keyword);
        const matchLocation = this.boLocDiaDiem === "all" || review.maDiaDiem === this.boLocDiaDiem;
        const matchRating = this.ratingFilter === "all" || String(review.rating) === this.ratingFilter;
        return matchKeyword && matchLocation && matchRating;
      });
    },
    totalReviews() {
      return this.danhSachDanhGia.length;
    },
    averageRating() {
      if (!this.danhSachDanhGia.length) return "0.0";
      const total = this.danhSachDanhGia.reduce((sum, review) => sum + Number(review.rating || 0), 0);
      return (total / this.danhSachDanhGia.length).toFixed(1);
    },
    reviewedLocations() {
      return new Set(this.danhSachDanhGia.map((review) => review.maDiaDiem).filter(Boolean)).size;
    },
    emptyMessage() {
      if (!this.maKhachHang) {
        return "Bạn cần đăng nhập để tải danh sách đánh giá của mình.";
      }
      return "Bạn chưa tạo đánh giá nào. Hãy gửi đánh giá đầu tiên cho một địa điểm.";
    },
  },
  methods: {
    clearMessages() {
      this.thongBaoLoi = "";
      this.thongBaoThanhCong = "";
    },
    resetForm() {
      this.form = {
        ma_dia_diem: "",
        so_sao: 5,
        noi_dung: "",
      };
      this.errors = {
        ma_dia_diem: "",
        so_sao: "",
      };
    },
    validateReviewForm({ requireLocation = true } = {}) {
      this.errors = {
        ma_dia_diem: "",
        so_sao: "",
      };

      if (requireLocation && !String(this.form.ma_dia_diem || "").trim()) {
        this.errors.ma_dia_diem = "Địa điểm là bắt buộc.";
      }

      const star = Number(this.form.so_sao);
      if (!Number.isInteger(star) || star < 1 || star > 5) {
        this.errors.so_sao = "Số sao phải nằm trong khoảng từ 1 đến 5.";
      }

      return !this.errors.ma_dia_diem && !this.errors.so_sao;
    },
    async taiDiaDiem() {
      const phanHoi = await goiApi(`${API_BASE}/dia-diem`, {
        headers: buildHeaders(),
      });
      const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

      if (!phanHoi.ok) {
        throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách địa điểm.");
      }

      this.danhSachDiaDiem = chuanHoaDanhSach(duLieuPhanHoi)
        .map((item) => normalizeLocation(item))
        .filter((item) => item.id)
        .sort((a, b) => a.name.localeCompare(b.name, "vi"));
    },
    async taiDanhGia() {
      const phanHoi = await goiApi(`${API_BASE}/danh-gia-ke-hoach`, {
        headers: buildHeaders(),
      });
      const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

      if (!phanHoi.ok) {
        if (phanHoi.status === 404) {
          this.danhSachDanhGia = [];
          return;
        }
        throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách đánh giá.");
      }

      const diaDiemTheoMa = this.danhSachDiaDiem.reduce((acc, location) => {
        acc[location.id] = location.name;
        return acc;
      }, {});

      this.danhSachDanhGia = chuanHoaDanhSach(duLieuPhanHoi)
        .map((item) => normalizeReview(item, diaDiemTheoMa[String(item?.ma_dia_diem || item?.Ma_dia_diem || "")] || ""))
        .filter((item) => item.maKhachHang === this.maKhachHang)
        .sort((a, b) => String(b.id).localeCompare(String(a.id), undefined, { numeric: true }));
    },
    async taiDuLieu() {
      this.dangTai = true;
      this.clearMessages();
      this.maKhachHang = getStoredCustomerId();

      if (!this.maKhachHang) {
        this.danhSachDanhGia = [];
        this.danhSachDiaDiem = [];
        this.dangTai = false;
        this.thongBaoLoi = "Không tìm thấy mã khách hàng trong phiên đăng nhập hiện tại.";
        return;
      }

      try {
        await this.taiDiaDiem();
        await this.taiDanhGia();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể tải dữ liệu đánh giá.";
        toaster.error(this.thongBaoLoi);
      } finally {
        this.dangTai = false;
      }
    },
    openCreateDialog() {
      this.resetForm();
      this.danhGiaDangChon = null;
      this.clearMessages();
      this.createDialogOpen = true;
    },
    openEditDialog(review) {
      this.resetForm();
      this.danhGiaDangChon = review;
      this.form.ma_dia_diem = review.maDiaDiem;
      this.form.so_sao = review.rating;
      this.form.noi_dung = review.noiDung;
      this.clearMessages();
      this.editDialogOpen = true;
    },
    openDeleteDialog(review) {
      this.danhGiaDangChon = review;
      this.clearMessages();
      this.deleteDialogOpen = true;
    },
    closeDialogs(forceClose = false) {
      if (this.isSubmitting && !forceClose) return;
      this.createDialogOpen = false;
      this.editDialogOpen = false;
      this.deleteDialogOpen = false;
      this.danhGiaDangChon = null;
    },
    async createReview() {
      if (!this.validateReviewForm({ requireLocation: true })) return;

      this.isSubmitting = true;
      this.clearMessages();

      try {
        const phanHoi = await goiApi(`${API_BASE}/danh-gia-ke-hoach`, {
          method: "POST",
          headers: buildHeaders(true),
          body: JSON.stringify({
            Ma_khach_hang: this.maKhachHang,
            ma_dia_diem: this.form.ma_dia_diem,
            so_sao: Number(this.form.so_sao),
            noi_dung: String(this.form.noi_dung || "").trim(),
          }),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tạo đánh giá mới.");
        }

        const maMoiTao = String(duLieuPhanHoi?.data?.Ma_danh_gia || duLieuPhanHoi?.data?.ma_danh_gia || "");
        this.maDanhGiaNoiBat = maMoiTao;
        this.thongBaoThanhCong = "Đã tạo đánh giá thành công.";
        toaster.success(this.thongBaoThanhCong);
        this.closeDialogs(true);
        await this.taiDanhGia();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể tạo đánh giá mới.";
        toaster.error(this.thongBaoLoi);
      } finally {
        this.isSubmitting = false;
      }
    },
    async updateReview() {
      if (!this.danhGiaDangChon?.id) return;
      if (!this.validateReviewForm({ requireLocation: false })) return;

      this.isSubmitting = true;
      this.clearMessages();

      try {
        const phanHoi = await goiApi(`${API_BASE}/danh-gia-ke-hoach/${encodeURIComponent(this.danhGiaDangChon.id)}`, {
          method: "PUT",
          headers: buildHeaders(true),
          body: JSON.stringify({
            so_sao: Number(this.form.so_sao),
            noi_dung: String(this.form.noi_dung || "").trim(),
          }),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể cập nhật đánh giá.");
        }

        this.maDanhGiaNoiBat = this.danhGiaDangChon.id;
        this.thongBaoThanhCong = `Đã cập nhật đánh giá ${this.danhGiaDangChon.id} thành công.`;
        toaster.success(this.thongBaoThanhCong);
        this.closeDialogs(true);
        await this.taiDanhGia();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể cập nhật đánh giá.";
        toaster.error(this.thongBaoLoi);
      } finally {
        this.isSubmitting = false;
      }
    },
    async deleteReview() {
      if (!this.danhGiaDangChon?.id) return;

      this.isSubmitting = true;
      this.clearMessages();

      try {
        const phanHoi = await goiApi(`${API_BASE}/danh-gia-ke-hoach/${encodeURIComponent(this.danhGiaDangChon.id)}`, {
          method: "DELETE",
          headers: buildHeaders(),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể xóa đánh giá.");
        }

        this.thongBaoThanhCong = `Đã xóa đánh giá ${this.danhGiaDangChon.id} thành công.`;
        toaster.success(this.thongBaoThanhCong);
        this.closeDialogs(true);
        await this.taiDanhGia();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể xóa đánh giá.";
        toaster.error(this.thongBaoLoi);
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  mounted() {
    this.taiDuLieu();
  },
};
</script>

<style scoped>
.review-page {
  background:
    radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%),
    linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
}

.review-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 18px;
  min-height: calc(100vh - 68px);
}

.review-content {
  padding: 28px 6px 0 0;
}

.review-hero {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  align-items: flex-start;
  gap: 18px;
  margin-bottom: 24px;
}

.review-hero__eyebrow {
  margin: 0 0 10px;
  color: #4f25f4;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.review-hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.1rem, 3.3vw, 3rem);
  line-height: 1.06;
  font-weight: 900;
  letter-spacing: -0.05em;
  max-width: 720px;
}

.review-hero__subtitle {
  margin: 10px 0 0;
  color: #5f6f89;
  max-width: 640px;
  font-size: 1rem;
  line-height: 1.7;
}

.review-hero__actions {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-top: 8px;
}

.primary-link,
.ghost-button,
.danger-btn {
  min-height: 48px;
  padding: 0 22px;
  border-radius: 18px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-decoration: none;
  font-weight: 800;
}

.primary-link {
  border: 0;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  color: #ffffff;
  box-shadow: 0 16px 28px rgba(11, 99, 160, 0.22);
}

.ghost-button {
  border: 1px solid #d7e2f0;
  background: rgba(255, 255, 255, 0.82);
  color: #24415d;
}

.danger-btn {
  border: 0;
  background: linear-gradient(135deg, #dc2626 0%, #be123c 100%);
  color: #ffffff;
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
  margin-bottom: 20px;
}

.stats-tile,
.toolbar,
.review-table-wrap,
.empty-state,
.notice,
.loading-card,
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
  display: grid;
  grid-template-columns: minmax(0, 1.6fr) minmax(220px, 0.8fr) minmax(160px, 0.6fr);
  gap: 14px;
  padding: 18px;
  margin-bottom: 18px;
}

.toolbar__search,
.toolbar__select {
  min-height: 54px;
  padding: 0 18px;
  border-radius: 18px;
  border: 1px solid #dfe7f3;
  background: #f8fbff;
  display: flex;
  align-items: center;
  gap: 12px;
}

.toolbar__search input,
.toolbar__select {
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

.loading-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}

.loading-card {
  min-height: 220px;
}

.review-table-wrap {
  overflow: hidden;
}

.review-table {
  width: 100%;
  border-collapse: collapse;
}

.review-table th,
.review-table td {
  padding: 14px 16px;
  text-align: left;
  border-bottom: 1px solid #e6edf8;
  color: #2c405e;
  vertical-align: top;
}

.review-table th {
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #6c7e95;
}

.review-table tbody tr:last-child td {
  border-bottom: 0;
}

.review-table tbody tr.is-highlighted {
  background: #f0fdf4;
}

.review-content-cell {
  max-width: 340px;
  line-height: 1.6;
}

.stars {
  display: inline-flex;
  gap: 4px;
  color: #f59e0b;
}

.stars i.is-off {
  color: #d2d8e2;
}

.row-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

.row-action {
  width: 34px;
  height: 34px;
  border: 0;
  border-radius: 10px;
  background: #eff6ff;
  color: #2f5f9d;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.row-action--danger {
  background: #fff1f2;
  color: #be123c;
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

.dialog-card__text {
  margin: 10px 0 0;
  color: #64748b;
  line-height: 1.6;
}

.dialog-form {
  display: grid;
  gap: 10px;
  margin-top: 14px;
}

.field {
  display: grid;
  gap: 8px;
}

.field span {
  color: #1f2937;
  font-weight: 700;
}

.field input,
.field select,
.field textarea {
  min-height: 48px;
  border-radius: 14px;
  border: 1px solid #dbe4f0;
  padding: 0 14px;
  background: #f8faff;
  outline: none;
  color: #111827;
}

.field textarea {
  min-height: 120px;
  resize: vertical;
  padding: 12px 14px;
}

.field input:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.field small {
  color: #dc2626;
}

.dialog-card__actions {
  margin-top: 10px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

@media (max-width: 860px) {
  .review-layout {
    width: calc(100% - 20px);
  }

  .stats-row,
  .toolbar {
    grid-template-columns: 1fr;
  }

  .review-table-wrap {
    overflow-x: auto;
  }
}
</style>




