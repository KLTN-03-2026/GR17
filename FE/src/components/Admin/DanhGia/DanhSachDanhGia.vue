<template>
  <div class="review-page">
    <section class="review-page__hero">
      <div>
        <p class="review-page__eyebrow">Quản trị đánh giá</p>
        <h1>Danh sách đánh giá</h1>
        <p class="review-page__subtitle">
          Theo dõi phản hồi của khách hàng, rà soát nội dung đánh giá và điều hướng nhanh sang màn chi tiết hoặc chỉnh sửa.
        </p>
      </div>

      <div class="review-page__hero-actions">
        <button class="ghost-button" type="button" @click="taiDanhSachDanhGia" :disabled="dangTai">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dangTai ? "Đang tải..." : "Tải lại dữ liệu" }}</span>
        </button>
      </div>
    </section>

    <section class="stats-grid">
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--violet">
          <i class="fas fa-star"></i>
        </div>
        <div>
          <span>Tổng đánh giá</span>
          <strong>{{ reviews.length.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--green">
          <i class="fas fa-medal"></i>
        </div>
        <div>
          <span>Điểm trung bình</span>
          <strong>{{ nhanDiemTrungBinh }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--amber">
          <i class="fas fa-thumbs-up"></i>
        </div>
        <div>
          <span>Đánh giá 4-5 sao</span>
          <strong>{{ soDanhGiaTichCuc.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--rose">
          <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
          <span>Đánh giá 1-2 sao</span>
          <strong>{{ soDanhGiaThap.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--sky">
          <i class="fas fa-location-dot"></i>
        </div>
        <div>
          <span>Địa điểm được nhắc đến</span>
          <strong>{{ soDiaDiemDuocNhac.toLocaleString() }}</strong>
        </div>
      </article>
    </section>

    <section class="filter-bar">
      <label class="filter-search">
        <i class="fas fa-search"></i>
        <input
          v-model.trim="search"
          type="text"
          placeholder="Tìm theo mã đánh giá, khách hàng, địa điểm hoặc nội dung..."
        >
      </label>

      <select v-model="boLocSoSao" class="filter-select">
        <option value="all">Số sao: Tất cả</option>
        <option v-for="option in tuyChonSoSao" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>

      <button class="filter-apply" type="button" @click="apDungBoLoc">
        Áp dụng bộ lọc
      </button>
    </section>

    <div v-if="thongBaoLoi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thongBaoLoi }}</span>
    </div>

    <div v-if="thongBaoHanhDong.text" :class="['notice', thongBaoHanhDong.type === 'error' ? 'notice--error' : 'notice--success']">
      <i :class="thongBaoHanhDong.type === 'error' ? 'fas fa-circle-xmark' : 'fas fa-circle-check'"></i>
      <span>{{ thongBaoHanhDong.text }}</span>
    </div>

    <section v-if="dangTai" class="loading-shell">
      <div class="loading-card"></div>
      <div class="loading-card"></div>
    </section>

    <section v-else-if="reviews.length" class="content-grid">
      <article class="table-card">
        <div class="table-head">
          <div>
            <h2>Danh mục đánh giá</h2>
            <p>{{ danhSachDanhGiaDaLoc.length }} đánh giá phù hợp điều kiện hiện tại.</p>
          </div>
          <span class="table-chip">{{ dangTai ? "Đồng bộ..." : "Dữ liệu trực tiếp" }}</span>
        </div>

        <div class="review-table">
          <div class="review-table__row review-table__row--head">
            <span>Khách hàng</span>
            <span>Mã DG</span>
            <span>Địa điểm</span>
            <span>Số sao</span>
            <span>Nội dung</span>
          </div>

          <div
            v-for="review in danhSachDanhGiaPhanTrang"
            :key="review.id"
            class="review-table__row"
            :class="{ 'is-selected': danhGiaDangChon && danhGiaDangChon.id === review.id }"
            @click="danhGiaDangChon = review"
          >
            <div class="review-person">
              <div class="review-person__avatar" :style="{ background: review.avatarGradient }">
                {{ review.customerInitials }}
              </div>
              <div>
                <strong>{{ review.customerName }}</strong>
                <small>KH {{ review.customerId }}</small>
                <small>{{ review.customerEmail }}</small>
              </div>
            </div>

            <div class="review-id">
              <span>DG</span>
              <strong>{{ review.id }}</strong>
            </div>

            <div class="review-location">
              <strong>{{ review.locationName }}</strong>
              <small>Mã địa điểm: {{ review.locationId }}</small>
            </div>

            <span :class="['pill', xacDinhLopSoSao(review.rating)]">
              {{ review.ratingLabel }}
            </span>

            <div class="review-content">
              <strong>{{ review.stars }}</strong>
              <small>{{ review.contentPreview }}</small>
            </div>

          </div>
        </div>

        <footer class="table-footer">
          <span>
            Hiển thị {{ chiSoHienThiBatDau }}-{{ chiSoHienThiKetThuc }} trên tổng {{ danhSachDanhGiaDaLoc.length.toLocaleString() }} đánh giá
          </span>

          <div class="pagination">
            <button class="page-btn" type="button" :disabled="page === 1" @click="page = Math.max(1, page - 1)">
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
              @click="page = Math.min(tongSoTrang, page + 1)"
            >
              <i class="fas fa-angle-right"></i>
            </button>
          </div>
        </footer>
      </article>

      <aside class="profile-card">
        <div v-if="danhGiaDangChon">
          <div class="profile-card__header">
            <div class="profile-card__avatar" :style="{ background: danhGiaDangChon.avatarGradient }">
              {{ danhGiaDangChon.customerInitials }}
            </div>
            <div>
              <p class="profile-card__eyebrow">Xem nhanh đánh giá</p>
              <h2>{{ danhGiaDangChon.customerName }}</h2>
              <div class="profile-card__tags">
                <span class="tag tag--slate">{{ danhGiaDangChon.code }}</span>
                <span :class="['tag', xacDinhLopSoSao(danhGiaDangChon.rating, true)]">
                  {{ danhGiaDangChon.ratingLabel }}
                </span>
              </div>
            </div>
          </div>

          <div class="profile-card__body">
            <div class="profile-row">
              <span>Địa điểm</span>
              <strong>{{ danhGiaDangChon.locationName }}</strong>
            </div>
            <div class="profile-row">
              <span>Số sao</span>
              <strong>{{ danhGiaDangChon.stars }}</strong>
            </div>
            <div class="profile-row">
              <span>Liên hệ khách hàng</span>
              <strong>{{ danhGiaDangChon.customerPhone }}</strong>
            </div>
            <div class="profile-row profile-row--stacked">
              <span>Nội dung đánh giá</span>
              <strong>{{ danhGiaDangChon.content || "Khách hàng chưa để lại nội dung đánh giá." }}</strong>
            </div>
            <div class="profile-row">
              <span>Ngày tạo</span>
              <strong>{{ danhGiaDangChon.createdAtLabel }}</strong>
            </div>
            <div class="profile-row">
              <span>Cập nhật gần nhất</span>
              <strong>{{ danhGiaDangChon.updatedAtLabel }}</strong>
            </div>
          </div>

          <div class="profile-card__actions">
            <router-link class="profile-link profile-link--primary" :to="`/admin/reviews/${danhGiaDangChon.id}`">
              Xem chi tiết
            </router-link>
            <router-link class="profile-link profile-link--secondary" :to="`/admin/reviews/${danhGiaDangChon.id}/edit`">
              Chỉnh sửa
            </router-link>
            <button
              class="profile-link profile-link--danger"
              type="button"
              :disabled="maDangXoa === danhGiaDangChon.id"
              @click="xoaDanhGia(danhGiaDangChon)"
            >
              <i class="fas" :class="maDangXoa === danhGiaDangChon.id ? 'fa-spinner fa-spin' : 'fa-trash'" style="margin-right: 0.5rem;"></i> Xóa đánh giá
            </button>
          </div>
        </div>

        <div v-else class="profile-card__empty">
          <i class="fas fa-star-half-stroke"></i>
          <h2>Chưa chọn đánh giá</h2>
          <p>Hãy chọn một dòng trong bảng để xem tóm tắt đánh giá ở khung bên phải.</p>
        </div>
      </aside>
    </section>

    <section v-else class="empty-state">
      <i class="fas fa-comment-slash"></i>
      <h2>Chưa có đánh giá nào</h2>
      <p>Hệ thống hiện chưa ghi nhận đánh giá kế hoạch hoặc dữ liệu chưa được đồng bộ.</p>
    </section>
  </div>
</template>

<script>
import { API_BASE, mapReview, normalizeCollection } from "./reviewAdminShared";
import { goiApi } from "../../../services/httpClient.js";
import { showAlert, showConfirm } from "../../../services/appDialog";

export default {
  name: "DanhSachDanhGia",
  data() {
    return {
      dangTai: false,
      thongBaoLoi: "",
      thongBaoHanhDong: {
        type: "success",
        text: "",
      },
      maDangXoa: "",
      reviews: [],
      danhGiaDangChon: null,
      search: "",
      boLocSoSao: "all",
      page: 1,
      kichThuocTrang: 6,
      thoiDiemApDung: 0,
      tuyChonSoSao: [
        { value: "5", label: "5 sao" },
        { value: "4", label: "4 sao" },
        { value: "3", label: "3 sao" },
        { value: "2", label: "2 sao" },
        { value: "1", label: "1 sao" },
      ],
    };
  },
  computed: {
    danhSachDanhGiaDaLoc() {
      const _ = this.thoiDiemApDung;
      return this.reviews.filter((review) => {
        const haystack = [
          review.id,
          review.customerId,
          review.customerName,
          review.customerEmail,
          review.locationName,
          review.locationId,
          review.content,
        ]
          .join(" ")
          .toLowerCase();
        const matchesSearch = haystack.includes(this.search.toLowerCase());
        const matchesRating = this.boLocSoSao === "all" || review.rating === Number(this.boLocSoSao);
        return matchesSearch && matchesRating;
      });
    },
    tongSoTrang() {
      return Math.max(1, Math.ceil(this.danhSachDanhGiaDaLoc.length / this.kichThuocTrang));
    },
    chiSoBatDau() {
      return Math.min((this.page - 1) * this.kichThuocTrang, Math.max(this.danhSachDanhGiaDaLoc.length - 1, 0));
    },
    chiSoKetThuc() {
      return Math.min(this.page * this.kichThuocTrang, this.danhSachDanhGiaDaLoc.length);
    },
    danhSachDanhGiaPhanTrang() {
      return this.danhSachDanhGiaDaLoc.slice(this.chiSoBatDau, this.chiSoKetThuc);
    },
    danhSachTrangHienThi() {
      const pages = [];
      const start = Math.max(1, this.page - 1);
      const end = Math.min(this.tongSoTrang, start + 2);
      for (let i = start; i <= end; i += 1) {
        pages.push(i);
      }
      return pages;
    },
    chiSoHienThiBatDau() {
      return this.danhSachDanhGiaDaLoc.length ? this.chiSoBatDau + 1 : 0;
    },
    chiSoHienThiKetThuc() {
      return this.danhSachDanhGiaDaLoc.length ? this.chiSoKetThuc : 0;
    },
    nhanDiemTrungBinh() {
      if (!this.reviews.length) return "--";
      const total = this.reviews.reduce((sum, review) => sum + review.rating, 0);
      return `${(total / this.reviews.length).toFixed(1)}/5`;
    },
    soDanhGiaTichCuc() {
      return this.reviews.filter((review) => review.rating >= 4).length;
    },
    soDanhGiaThap() {
      return this.reviews.filter((review) => review.rating <= 2).length;
    },
    soDiaDiemDuocNhac() {
      return new Set(this.reviews.map((review) => review.locationId)).size;
    },
  },
  watch: {
    tongSoTrang() {
      if (this.page > this.tongSoTrang) {
        this.page = this.tongSoTrang;
      }
    },
    danhSachDanhGiaPhanTrang(reviews) {
      if (!reviews.length) {
        this.danhGiaDangChon = null;
        return;
      }

      const stillVisible = reviews.find((item) => item.id === this.danhGiaDangChon?.id);
      if (!stillVisible) {
        [this.danhGiaDangChon] = reviews;
      }
    },
  },
  methods: {
    xacDinhLopSoSao(rating, cheDoTag = false) {
      if (rating >= 5) return cheDoTag ? "tag--violet" : "pill--violet";
      if (rating >= 4) return cheDoTag ? "tag--green" : "pill--green";
      if (rating === 3) return cheDoTag ? "tag--amber" : "pill--amber";
      return cheDoTag ? "tag--rose" : "pill--rose";
    },
    async taiDanhSachDanhGia(options = {}) {
      const { giuThongBaoHanhDong = false } = options;
      this.dangTai = true;
      this.thongBaoLoi = "";
      if (!giuThongBaoHanhDong) {
        this.thongBaoHanhDong = { type: "success", text: "" };
      }

      try {
        const response = await goiApi(`${API_BASE}/danh-gia-ke-hoach`, {
          headers: {
            Accept: "application/json",
          },
        });
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          if (response.status === 404) {
            this.reviews = [];
            this.danhGiaDangChon = null;
            return;
          }
          throw new Error(payload?.message || "Không thể tải danh sách đánh giá.");
        }

        this.reviews = normalizeCollection(payload).map((item) => mapReview(item));
        this.page = 1;
        this.thoiDiemApDung = Date.now();
        this.danhGiaDangChon = this.reviews[0] || null;
      } catch (error) {
        this.reviews = [];
        this.danhGiaDangChon = null;
        this.thongBaoLoi = error.message || "Không thể tải danh sách đánh giá.";
      } finally {
        this.dangTai = false;
      }
    },
    apDungBoLoc() {
      this.page = 1;
      this.thoiDiemApDung = Date.now();
    },
    async xoaDanhGia(review) {
      const confirmed = await showConfirm({
        title: "Xác nhận xóa đánh giá",
        message: `Bạn có chắc muốn xóa đánh giá ${review.code} của khách hàng ${review.customerName}?`,
        tone: "danger",
        confirmText: "Xóa đánh giá",
        cancelText: "Hủy",
      });
      if (!confirmed) return;

      this.maDangXoa = review.id;
      this.thongBaoLoi = "";
      this.thongBaoHanhDong = { type: "success", text: "" };

      try {
        const response = await goiApi(`${API_BASE}/danh-gia-ke-hoach/${encodeURIComponent(review.id)}`, {
          method: "DELETE",
          headers: {
            Accept: "application/json",
          },
        });
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
          throw new Error(payload?.message || "Không thể xóa đánh giá.");
        }

        await this.taiDanhSachDanhGia({ giuThongBaoHanhDong: true });
        this.thongBaoHanhDong = {
          type: "success",
          text: "Đã xóa đánh giá thành công.",
        };
        await showAlert({
          title: "Xóa thành công",
          message: `Đã xóa đánh giá ${review.code}.`,
          tone: "success",
          confirmText: "Đã hiểu",
        });
      } catch (error) {
        this.thongBaoHanhDong = {
          type: "error",
          text: error.message || "Không thể xóa đánh giá.",
        };
        await showAlert({
          title: "Không thể xóa đánh giá",
          message: error.message || "Không thể xóa đánh giá.",
          tone: "danger",
        });
      } finally {
        this.maDangXoa = "";
      }
    },
  },
  mounted() {
    this.taiDanhSachDanhGia();
  },
};
</script>

<style scoped>
.review-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background:
    radial-gradient(circle at top right, rgba(79, 70, 229, 0.08), transparent 24%),
    linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.review-page__hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.review-page__eyebrow {
  margin: 0 0 0.4rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.review-page__hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.35rem, 4vw, 3.2rem);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.review-page__subtitle {
  margin: 0.8rem 0 0;
  color: #5f7191;
  font-size: 1.05rem;
  max-width: 780px;
}

.review-page__hero-actions {
  display: flex;
  align-items: center;
}

.ghost-button {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  min-height: 3.2rem;
  padding: 0 1.25rem;
  border-radius: 1rem;
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, 0.88);
  color: #334155;
  font-weight: 800;
}

.ghost-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

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

.stats-card__icon--violet { background: #efefff; color: #4f25f4; }
.stats-card__icon--green { background: #eaf9f1; color: #08986c; }
.stats-card__icon--amber { background: #fff7e8; color: #d97706; }
.stats-card__icon--rose { background: #fff1f2; color: #be123c; }
.stats-card__icon--sky { background: #eaf4ff; color: #0369a1; }

.stats-card span {
  display: block;
  color: #60708d;
  font-weight: 700;
}

.stats-card strong {
  display: block;
  margin-top: 0.35rem;
  color: #111b39;
  font-size: 1.75rem;
  line-height: 1;
  font-weight: 900;
}

.filter-bar {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(180px, 0.8fr) auto;
  gap: 1rem;
  padding: 1.15rem;
  border-radius: 1.6rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
  margin-bottom: 1.5rem;
}

.filter-search,
.filter-select {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  min-height: 3.45rem;
  padding: 0 1rem;
  border-radius: 1rem;
  background: #f7f8fc;
  border: 1px solid #eef1f7;
  color: #324053;
}

.filter-search i {
  color: #8d99af;
}

.filter-search input,
.filter-select {
  border: 0;
  outline: none;
  font-size: 1rem;
}

.filter-search input {
  width: 100%;
  background: transparent;
}

.filter-apply {
  min-height: 3.45rem;
  padding: 0 1.45rem;
  border: 0;
  border-radius: 1rem;
  background: #131b35;
  color: #ffffff;
  font-weight: 800;
}

.notice {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.1rem;
  border-radius: 1rem;
  margin-bottom: 1rem;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.notice--success {
  background: #ecfdf3;
  color: #15803d;
}

.loading-shell {
  display: grid;
  grid-template-columns: 1.6fr 1fr;
  gap: 1rem;
}

.loading-card,
.table-card,
.profile-card,
.empty-state {
  border-radius: 1.75rem;
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
}

.loading-card {
  min-height: 280px;
  position: relative;
  overflow: hidden;
}

.loading-card::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.85), transparent);
  transform: translateX(-100%);
  animation: shimmer 1.4s infinite;
}

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.9fr) minmax(320px, 0.9fr);
  gap: 1.5rem;
}

.table-card,
.profile-card {
  padding: 1.35rem 1.4rem 1rem;
}

.table-card {
  overflow: hidden;
}

.table-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.table-head h2 {
  margin: 0;
  color: #111827;
  font-size: 1.35rem;
  font-weight: 900;
}

.table-head p {
  margin: 0.4rem 0 0;
  color: #64748b;
}

.table-chip {
  padding: 0.7rem 1rem;
  border-radius: 0.95rem;
  background: #eef2ff;
  color: #4338ca;
  font-weight: 800;
}

.review-table {
  display: grid;
  width: 100%;
}

.review-table__row {
  display: grid;
  grid-template-columns: minmax(220px, 1.55fr) 84px minmax(170px, 1.1fr) 96px minmax(220px, 1.5fr);
  gap: 1.1rem;
  align-items: center;
  padding: 1.2rem 0.15rem;
  border-top: 1px solid #eef2f7;
  transition: background-color 0.2s ease;
}

.review-table__row:not(.review-table__row--head) {
  cursor: pointer;
}

.review-table__row.is-selected {
  background: #f8fbff;
}

.review-table__row--head {
  border-top: 0;
  padding-top: 0.1rem;
  color: #637594;
  font-size: 0.82rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.review-person {
  display: flex;
  align-items: center;
  gap: 1rem;
  min-width: 0;
}

.review-person__avatar,
.profile-card__avatar {
  display: grid;
  place-items: center;
  color: #ffffff;
  font-weight: 900;
}

.review-person__avatar {
  width: 3.4rem;
  height: 3.4rem;
  min-width: 3.4rem;
  flex-shrink: 0;
  border-radius: 1rem;
  font-size: 1.05rem;
  line-height: 1;
  letter-spacing: -0.03em;
  text-transform: uppercase;
}

.review-person strong,
.review-location strong,
.review-content strong {
  display: block;
  color: #111b39;
  line-height: 1.35;
}

.review-location,
.review-content {
  min-width: 0;
}

.review-person small,
.review-location small,
.review-content small {
  display: block;
  color: #66768f;
  line-height: 1.45;
}

.review-content small {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  overflow-wrap: anywhere;
  max-width: 32ch;
}

.review-id {
  display: inline-flex;
  flex-direction: row;
  gap: 0.35rem;
  min-width: 4.25rem;
  padding: 0.45rem 0.8rem;
  border-radius: 0.75rem;
  background: #f3f5fa;
  color: #60708d;
  font-size: 0.9rem;
  font-weight: 700;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
}

.pill,
.tag {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: fit-content;
  font-weight: 800;
}

.pill {
  min-height: 1.95rem;
  padding: 0 0.95rem;
  border-radius: 999px;
}

.pill--violet,
.tag--violet { background: #efefff; color: #4f25f4; }
.pill--green,
.tag--green { background: #eaf9f1; color: #08986c; }
.pill--amber,
.tag--amber { background: #fff7e8; color: #d97706; }
.pill--rose,
.tag--rose { background: #fff1f2; color: #be123c; }
.tag--slate { background: #f1f5f9; color: #475569; }

.muted {
  color: #66768f;
}

.actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.28rem;
  min-width: 0;
  width: 100%;
  margin: 0 auto;
}

.icon-btn {
  width: 1.9rem;
  height: 1.9rem;
  border: 0;
  border-radius: 0.8rem;
  background: transparent;
  color: #8b97ae;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  flex: 0 0 auto;
}

.icon-btn:hover {
  background: #f4f6fb;
  color: #344054;
}

.icon-btn--danger:hover {
  background: #fff1f2;
  color: #be123c;
}

.icon-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.table-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding-top: 1.2rem;
}

.table-footer span {
  color: #64748b;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.page-btn {
  width: 3rem;
  height: 3rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  border: 1px solid #eceff6;
  border-radius: 1rem;
  background: #ffffff;
  color: #55657f;
  font-weight: 800;
  font-size: 0.95rem;
  font-variant-numeric: tabular-nums;
}

.page-btn.is-active {
  border-color: transparent;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
  box-shadow: 0 12px 24px rgba(79, 37, 244, 0.22);
}

.page-btn i {
  font-size: 0.9rem;
  line-height: 1;
}

.profile-card {
  align-self: start;
  position: sticky;
  top: 1.5rem;
}

.profile-card__header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid #eef2f7;
}

.profile-card__avatar {
  width: 4.2rem;
  height: 4.2rem;
  border-radius: 1.3rem;
  font-size: 1.1rem;
}

.profile-card__eyebrow {
  margin: 0 0 0.4rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  font-size: 0.72rem;
  font-weight: 900;
}

.profile-card__header h2,
.profile-card__empty h2,
.empty-state h2 {
  margin: 0 0 0.55rem;
  color: #111827;
  font-size: 1.45rem;
  font-weight: 900;
}

.profile-card__tags {
  display: flex;
  gap: 0.55rem;
  flex-wrap: wrap;
}

.tag {
  min-height: 2rem;
  padding: 0 0.85rem;
  border-radius: 999px;
  font-size: 0.9rem;
}

.profile-card__body {
  display: grid;
  gap: 0.95rem;
  padding: 1.3rem 0;
}

.profile-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.profile-row--stacked {
  flex-direction: column;
}

.profile-row span {
  color: #64748b;
}

.profile-row strong {
  color: #0f172a;
  text-align: right;
  max-width: 60%;
}

.profile-row--stacked strong {
  max-width: 100%;
  text-align: left;
  white-space: pre-wrap;
}

.profile-card__actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.8rem;
}

.profile-link {
  min-height: 3rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
  text-decoration: none;
  font-weight: 800;
}

.profile-link--primary {
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
}

.profile-link--secondary {
  background: #f7f8fc;
  border: 1px solid #e6ebf4;
  color: #334155;
}

.profile-link--danger {
  grid-column: 1 / -1;
  background: #fff1f2;
  border: 1px solid #ffe4e6;
  color: #e11d48;
  font-family: inherit;
  font-size: inherit;
  cursor: pointer;
}

.profile-card__empty,
.empty-state {
  display: grid;
  place-items: center;
  text-align: center;
  color: #64748b;
}

.profile-card__empty {
  min-height: 420px;
}

.profile-card__empty i,
.empty-state i {
  width: 5rem;
  height: 5rem;
  display: grid;
  place-items: center;
  border-radius: 1.5rem;
  margin: 0 auto 1rem;
  background: #f4f7fb;
  color: #2453ff;
  font-size: 1.5rem;
}

.empty-state {
  min-height: 420px;
  padding: 2rem;
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

@media (max-width: 1360px) {
  .content-grid,
  .loading-shell {
    grid-template-columns: 1fr;
  }

  .profile-card {
    position: static;
  }
}

@media (max-width: 1280px) {
  .stats-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .filter-bar {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .review-table__row,
  .review-table__row--head {
    grid-template-columns: minmax(220px, 1.45fr) 84px minmax(170px, 1.05fr) 96px minmax(220px, 1.35fr) 112px;
  }

  .review-table__row > :nth-child(5),
  .review-table__row--head > :nth-child(5) {
    display: block;
  }
}

@media (max-width: 1024px) {
  .review-page {
    padding: 1rem;
  }

  .review-page__hero {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (max-width: 768px) {
  .stats-grid,
  .filter-bar,
  .profile-card__actions {
    grid-template-columns: 1fr;
  }

  .review-table__row--head {
    display: none;
  }

  .review-table__row {
    grid-template-columns: 1fr;
    gap: 0.8rem;
  }

  .table-footer {
    flex-direction: column;
    align-items: flex-start;
  }

  .profile-row {
    flex-direction: column;
    gap: 0.35rem;
  }

  .profile-row strong {
    max-width: 100%;
    text-align: left;
  }
}
</style>
