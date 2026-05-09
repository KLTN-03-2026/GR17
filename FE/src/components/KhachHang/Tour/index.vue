<template>
  <div class="destination-page">
    <div class="destination-layout">
      <CustomerSidebar />

      <section class="destination-content">
        <header class="destination-hero">
          <div>
            <p class="destination-hero__eyebrow">Trải nghiệm du lịch</p>
            <h1>Danh sách tour du lịch</h1>
            <p class="destination-hero__subtitle">
              Khám phá các tour du lịch đa dạng, trọn gói với lịch trình hấp dẫn và chi phí tốt nhất cho bạn.
            </p>
          </div>
        </header>

        <section class="stats-row">
          <article class="stats-tile">
            <span>Tổng số tour</span>
            <strong>{{ totalTours }}</strong>
          </article>
          <article class="stats-tile">
            <span>Đang hoạt động</span>
            <strong>{{ tourHoatDong }}</strong>
          </article>
          <article class="stats-tile">
            <span>Đang hiển thị</span>
            <strong>{{ displayedCount }}</strong>
          </article>
        </section>

        <section class="toolbar">
          <form class="toolbar__search" @submit.prevent="timKiemTour">
            <i class="fas fa-magnifying-glass"></i>
            <input
              v-model.trim="boLoc.tim_kiem"
              type="text"
              placeholder="Bạn muốn tìm tour gì? (Ví dụ: Đà Nẵng, Hạ Long...)"
              :disabled="dang_tai"
            >
            <button class="toolbar__button" type="submit" :disabled="dang_tai">
              {{ dang_tai ? "Đang tải..." : "Tìm kiếm" }}
            </button>
          </form>
        </section>

        <div v-if="thong_bao_loi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thong_bao_loi }}</span>
        </div>

        <div v-else-if="dang_tai" class="notice notice--info">
          <i class="fas fa-spinner fa-spin"></i>
          <span>Đang tải danh sách tour...</span>
        </div>

        <div v-else-if="!danh_sach_hien_thi.length" class="empty-state">
          <i class="fas fa-map"></i>
          <h2>Chưa tìm thấy tour phù hợp</h2>
          <p>Hãy thử đổi từ khóa để xem thêm kết quả.</p>
        </div>

        <template v-else>
          <div class="destination-grid">
            <article
              v-for="tour in danh_sach_hien_thi"
              :key="tour.id"
              class="destination-card"
              :style="{ backgroundImage: `linear-gradient(180deg, rgba(9, 24, 41, 0.12), rgba(9, 24, 41, 0.8)), url(${tour.hinh_anh})` }"
              @click="moChiTietTour(tour)"
            >
              <div class="destination-card__badge-row">
                <span class="destination-card__badge">{{ tour.thoi_gian_hien_thi }}</span>
                <span class="destination-card__rating">
                  <i class="fas fa-user-group"></i>
                  {{ tour.so_nguoi }} người
                </span>
              </div>

              <div class="destination-card__content">
                <h3>{{ tour.ten }}</h3>
                <div class="destination-card__bottom-row">
                  <p>
                    <i class="fas fa-money-bill-wave"></i>
                    {{ tour.gia_hien_thi }}
                  </p>
                  <span class="destination-card__cta">
                    Xem chi tiết / Đặt tour
                    <i class="fas fa-arrow-right"></i>
                  </span>
                </div>
              </div>
            </article>
          </div>

          <nav v-if="tong_so_trang > 1" class="listing-pagination" aria-label="Phân trang tour">
            <button
              type="button"
              class="listing-pagination__button"
              :disabled="!pagination.hasPreviousPage"
              @click="diTrang(trang_hien_tai - 1)"
            >
              Trước
            </button>

            <button
              v-for="page in danh_sach_trang"
              :key="page"
              type="button"
              class="listing-pagination__page"
              :class="{ 'is-active': page === trang_hien_tai }"
              @click="diTrang(page)"
            >
              {{ page }}
            </button>

            <button
              type="button"
              class="listing-pagination__button"
              :disabled="!pagination.hasNextPage"
              @click="diTrang(trang_hien_tai + 1)"
            >
              Sau
            </button>
          </nav>
        </template>
      </section>
    </div>
  </div>
</template>

<script>
import CustomerSidebar from "../CustomerSidebar.vue";
import { goiApi } from "../../../services/httpClient.js";
import {
  buildPaginatedListing,
  buildPaginationWindow,
  resetListingPage,
} from "../listingPagination.js";

const TOUR_API = "/api/tour";
const HINH_MAC_DINH = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80";

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
  name: "KhachHangDanhSachTourPage",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      dang_tai: false,
      thong_bao_loi: "",
      trang_hien_tai: 1,
      moi_trang: 8,
      boLoc: {
        tim_kiem: this.$route.query.search || "",
      },
      danh_sach_tour: [],
    };
  },
  computed: {
    danh_sach_da_loc() {
      const keyword = this.boLoc.tim_kiem.trim().toLowerCase();

      return this.danh_sach_tour.filter((tour) => {
        const hopTuKhoa =
          !keyword ||
          `${tour.ten} ${tour.mo_ta}`.toLowerCase().includes(keyword);

        return hopTuKhoa;
      });
    },
    pagination() {
      return buildPaginatedListing(this.danh_sach_da_loc, {
        currentPage: this.trang_hien_tai,
        perPage: this.moi_trang,
      });
    },
    danh_sach_hien_thi() {
      return this.pagination.items;
    },
    danh_sach_trang() {
      return buildPaginationWindow(this.tong_so_trang, this.trang_hien_tai);
    },
    tong_so_trang() {
      return this.pagination.totalPages;
    },
    totalTours() {
      return this.danh_sach_tour.length;
    },
    tourHoatDong() {
      return this.danh_sach_tour.filter((tour) => tour.trang_thai).length;
    },
    displayedCount() {
      return this.danh_sach_hien_thi.length;
    },
  },
  mounted() {
    this.taiDanhSachTour();
  },
  methods: {
    xuLyDanhSach(duLieuPhanHoi) {
      if (Array.isArray(duLieuPhanHoi)) {
        return duLieuPhanHoi;
      }
      if (Array.isArray(duLieuPhanHoi?.data)) {
        return duLieuPhanHoi.data;
      }
      if (Array.isArray(duLieuPhanHoi?.data?.data)) {
        return duLieuPhanHoi.data.data;
      }
      if (Array.isArray(duLieuPhanHoi?.result)) {
        return duLieuPhanHoi.result;
      }
      if (Array.isArray(duLieuPhanHoi?.result?.data)) {
        return duLieuPhanHoi.result.data;
      }
      return [];
    },
    layGiaTriDauTien(obj, keys, macDinh = "") {
      for (const key of keys) {
        if (obj && obj[key] !== undefined && obj[key] !== null && obj[key] !== "") {
          return obj[key];
        }
      }
      return macDinh;
    },
    dinhDangTien(giaTri) {
      const so = Number(giaTri);
      if (!Number.isFinite(so) || so <= 0) return "Liên hệ";
      return `${so.toLocaleString("vi-VN")} đ`;
    },
    chuanHoaTour(item = {}, index = 0) {
      return {
        id: this.layGiaTriDauTien(item, ["ma_tour", "Ma_tour", "id", "Id"], `T${index + 1}`),
        ten: this.layGiaTriDauTien(item, ["ten_tour", "Ten_tour", "ten", "name"], "Tour nổi bật"),
        mo_ta: this.layGiaTriDauTien(item, ["mo_ta", "Mo_ta", "description"], "Khám phá trải nghiệm du lịch tuyệt vời."),
        hinh_anh: this.layGiaTriDauTien(item, ["hinh_anh", "Hinh_anh", "image", "thumbnail"], HINH_MAC_DINH),
        gia_hien_thi: this.dinhDangTien(this.layGiaTriDauTien(item, ["so_tien", "So_tien", "gia_tour", "gia"], 0)),
        thoi_gian_hien_thi: `${this.layGiaTriDauTien(item, ["so_ngay", "So_ngay"], 1)} ngày`,
        so_nguoi: this.layGiaTriDauTien(item, ["so_nguoi", "So_nguoi"], 10),
        trang_thai: true,
      };
    },
    async taiDanhSachTour() {
      this.dang_tai = true;
      // cleared thong_bao_loi

      try {
        const res = await goiDuLieu(TOUR_API);
        this.danh_sach_tour = this.xuLyDanhSach(res.data).map((item, index) => this.chuanHoaTour(item, index));
        this.trang_hien_tai = resetListingPage();
      } catch (error) {
        toaster.error(error?.phanHoi?.data?.message || "Không thể tải danh sách tour.");
      } finally {
        this.dang_tai = false;
      }
    },
    timKiemTour() {
      this.trang_hien_tai = resetListingPage();
      this.$router.replace({
        path: "/khach-hang/tour",
        query: this.boLoc.tim_kiem.trim() ? { search: this.boLoc.tim_kiem.trim() } : {},
      });
    },
    diTrang(page) {
      this.trang_hien_tai = page;
    },
    moChiTietTour(tour) {
      if (!tour) return;
      this.$router.push(`/khach-hang/tour/${tour.id}`);
    },
  },
  watch: {
    "$route.query.search"(value) {
      this.boLoc.tim_kiem = value || "";
      this.trang_hien_tai = resetListingPage();
    },
    "pagination.currentPage"(value) {
      if (value !== this.trang_hien_tai) {
        this.trang_hien_tai = value;
      }
    },
  },
};
</script>

<style scoped>
.destination-page {
  background: radial-gradient(circle at top right, rgba(12, 104, 150, 0.08), transparent 26%), linear-gradient(180deg, #eef5ff 0%, #f5f8ff 100%);
}

.destination-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 18px;
  min-height: calc(100vh - 68px);
}

.destination-content {
  padding: 28px 6px 0 0;
}

.destination-hero {
  margin-bottom: 20px;
}

.destination-hero__eyebrow {
  margin: 0 0 10px;
  color: #4f25f4;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.destination-hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.1rem, 3.3vw, 3rem);
  line-height: 1.06;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.destination-hero__subtitle {
  margin: 10px 0 0;
  color: #5f6f89;
  max-width: 720px;
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
.empty-state,
.destination-card,
.listing-pagination {
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
  display: grid;
  grid-template-columns: 20px minmax(0, 1fr) auto;
  align-items: center;
  gap: 12px;
  min-height: 56px;
  border-radius: 18px;
  border: 1px solid #dfe7f3;
  background: #f8fbff;
  padding: 0 16px;
}

.toolbar__search input {
  border: 0;
  background: transparent;
  outline: none;
  color: #24415d;
}

.toolbar__button {
  min-height: 40px;
  border: 0;
  border-radius: 12px;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  color: #fff;
  font-weight: 800;
  padding: 0 14px;
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

.notice--info {
  background: #eff6ff;
  color: #1d4ed8;
}

.destination-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.destination-card {
  position: relative;
  min-height: 255px;
  padding: 0;
  display: block;
  overflow: hidden;
  background-size: cover;
  background-position: center;
  cursor: pointer;
}

.destination-card__badge-row {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem;
}

.destination-card__badge {
  padding: 0.32rem 0.65rem;
  border-radius: 999px;
  background: rgba(168, 139, 250, 0.92);
  color: #ffffff;
  font-size: 0.72rem;
  font-weight: 800;
}

.destination-card__rating {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  color: #ffffff;
  font-size: 0.78rem;
  font-weight: 700;
}

.destination-card__rating i {
  color: #fbbf24;
}

.destination-card__content {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  padding: 1.05rem;
  color: #ffffff;
}

.destination-card__content h3 {
  margin: 0;
  color: #ffffff;
  font-size: 1.08rem;
  font-weight: 900;
}

.destination-card__content p {
  margin: 0;
  color: #8bedb4;
  font-size: 0.95rem;
  font-weight: 700;
  line-height: 1.6;
}

.destination-card__bottom-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-top: 0.45rem;
}

.destination-card__cta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 12px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.14);
  color: #ffffff;
  font-size: 0.82rem;
  font-weight: 800;
  backdrop-filter: blur(8px);
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

.listing-pagination {
  margin-top: 20px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.listing-pagination__button,
.listing-pagination__page {
  min-width: 44px;
  min-height: 44px;
  border-radius: 14px;
  border: 1px solid #d7e2f0;
  background: #ffffff;
  color: #24415d;
  font-weight: 800;
  transition: all 0.2s ease;
}

.listing-pagination__button:disabled,
.listing-pagination__page:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.listing-pagination__page.is-active {
  border-color: transparent;
  background: linear-gradient(135deg, #0b7198 0%, #0b63a0 100%);
  color: #ffffff;
  box-shadow: 0 12px 22px rgba(11, 99, 160, 0.2);
}

@media (max-width: 1200px) {
  .destination-layout {
    grid-template-columns: 1fr;
  }

  .destination-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {
  .destination-layout {
    width: calc(100% - 20px);
  }

  .stats-row,
  .destination-grid {
    grid-template-columns: 1fr;
  }

  .toolbar__search {
    grid-template-columns: 20px minmax(0, 1fr);
    padding-top: 10px;
    padding-bottom: 10px;
  }

  .toolbar__button {
    grid-column: 1 / -1;
  }

  .listing-pagination {
    flex-wrap: wrap;
  }
}
</style>
