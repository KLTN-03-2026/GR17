<template>
  <div class="tour-page">
    <section class="tour-page__hero">
      <div>
        <h1>Quản lý Danh sách Tour</h1>
        <p>Theo dõi danh mục tour, mức giá và trạng thái khai thác trong hệ thống quản trị.</p>
      </div>

      <router-link class="tour-page__create" to="/tour-management/create">
        <i class="fas fa-circle-plus"></i>
        <span>Thêm Tour Mới</span>
      </router-link>
    </section>

    <section class="stats-grid">
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--violet">
          <i class="fas fa-map"></i>
        </div>
        <div>
          <span>Tổng số tour</span>
          <strong>{{ tours.length.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--green">
          <i class="fas fa-circle-check"></i>
        </div>
        <div>
          <span>Tour đang hoạt động</span>
          <strong>{{ publishedCount.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--amber">
          <i class="fas fa-clock-rotate-left"></i>
        </div>
        <div>
          <span>Tour chờ rà soát</span>
          <strong>{{ draftCount.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--rose">
          <i class="fas fa-money-bill-wave"></i>
        </div>
        <div>
          <span>Doanh thu ước tính</span>
          <strong>{{ revenueLabel }} nghìn USD</strong>
        </div>
      </article>
    </section>

    <section class="filter-bar">
      <label class="filter-search">
        <i class="fas fa-search"></i>
        <input v-model.trim="search" type="text" placeholder="Tìm theo tên tour hoặc mã tour...">
      </label>

      <select v-model="categoryFilter" class="filter-select">
        <option value="all">Danh mục: Tất cả</option>
        <option v-for="category in categoryOptions" :key="category" :value="category">{{ category }}</option>
      </select>

      <select v-model="priceFilter" class="filter-select">
        <option value="all">Mức giá: Tất cả</option>
        <option value="low">Dưới 10 triệu VNĐ</option>
        <option value="mid">Từ 10 đến 30 triệu VNĐ</option>
        <option value="high">Trên 30 triệu VNĐ</option>
      </select>

      <select v-model="statusFilter" class="filter-select">
        <option value="all">Trạng thái: Tất cả</option>
        <option value="published">Đang hoạt động</option>
        <option value="draft">Bản nháp</option>
      </select>

      <button class="filter-apply" type="button" @click="applyFilters">
        Áp dụng bộ lọc
      </button>
    </section>

    <div v-if="errorMessage" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </div>

    <section class="table-card">
      <div class="tour-table">
        <div class="tour-table__row tour-table__row--head">
          <span>Tên tour</span>
          <span>Mã</span>
          <span>Danh mục</span>
          <span>Giá bán</span>
          <span>Ngày tạo</span>
          <span>Trạng thái</span>
          <span>Thao tác</span>
        </div>

        <div v-for="tour in paginatedTours" :key="tour.id" class="tour-table__row">
          <div class="tour-name">
            <div class="tour-name__image" :style="{ backgroundImage: `url(${tour.image})` }"></div>
            <div>
              <strong>{{ tour.name }}</strong>
              <small>{{ tour.meta }}</small>
            </div>
          </div>

          <div class="tour-id">
            <span>{{ tour.codePrefix }}</span>
            <strong>{{ tour.codeNumber }}</strong>
          </div>

          <div>
            <span :class="['tag', `tag--${tour.tagTone}`]">{{ tour.category }}</span>
          </div>

          <strong class="tour-price">{{ tour.priceLabel }}</strong>

          <span class="tour-date">{{ tour.createdAtLabel }}</span>

          <label class="switch">
            <input :checked="tour.published" type="checkbox" @change="toggleStatus(tour)">
            <span></span>
          </label>

          <div class="actions">
            <router-link class="icon-btn" :to="`/tour-management/${tour.id}/departures`" title="Lịch khởi hành">
              <i class="fas fa-eye"></i>
            </router-link>
            <router-link class="icon-btn" :to="`/tour-management/${tour.id}/edit`" title="Sửa">
              <i class="fas fa-pen"></i>
            </router-link>
            <button class="icon-btn" type="button" title="Xóa">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <footer class="table-footer">
        <span>Hiển thị {{ startIndex + 1 }}-{{ endIndex }} trên tổng {{ filteredTours.length.toLocaleString() }} tour</span>

        <div class="pagination">
          <button class="page-btn" type="button" :disabled="page === 1" @click="page = Math.max(1, page - 1)">
            <i class="fas fa-angle-left"></i>
          </button>
          <button
            v-for="pageNumber in visiblePages"
            :key="pageNumber"
            type="button"
            :class="['page-btn', { 'is-active': page === pageNumber }]"
            @click="page = pageNumber"
          >
            {{ pageNumber }}
          </button>
          <button class="page-btn" type="button" :disabled="page === totalPages" @click="page = Math.min(totalPages, page + 1)">
            <i class="fas fa-angle-right"></i>
          </button>
        </div>
      </footer>
    </section>
  </div>

</template>

<script>
import { goiApi } from '../../services/httpClient.js';
const API_BASE = "/api";
const FALLBACK_IMAGES = [
  "https://images.unsplash.com/photo-1573843981267-be1999ff37cd?auto=format&fit=crop&w=800&q=80",
  "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80",
  "https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=800&q=80",
];
export default {
  name: "DanhSachTour",
  data() {
    return {
      isLoading: false,
      errorMessage: "",
      search: "",
      categoryFilter: "all",
      priceFilter: "all",
      statusFilter: "all",
      page: 1,
      pageSize: 10,
      tours: [],
      appliedAt: 0,
    };
  },
  computed: {
    categoryOptions() {
      return [...new Set(this.tours.map((tour) => tour.category))];
    },
    filteredTours() {
      const _ = this.appliedAt;
      return this.tours.filter((tour) => {
        const haystack = [tour.name, tour.id, tour.codePrefix, tour.codeNumber, tour.category].join(" ").toLowerCase();
        const matchesSearch = haystack.includes(this.search.toLowerCase());
        const matchesCategory = this.categoryFilter === "all" || tour.category === this.categoryFilter;
        const matchesStatus =
          this.statusFilter === "all" ||
          (this.statusFilter === "published" && tour.published) ||
          (this.statusFilter === "draft" && !tour.published);

        const priceVnd = Number(tour.priceVnd);
        const matchesPrice =
          this.priceFilter === "all" ||
          (this.priceFilter === "low" && priceVnd < 10000000) ||
          (this.priceFilter === "mid" && priceVnd >= 10000000 && priceVnd <= 30000000) ||
          (this.priceFilter === "high" && priceVnd > 30000000);

        return matchesSearch && matchesCategory && matchesStatus && matchesPrice;
      });
    },
    totalPages() {
      return Math.max(1, Math.ceil(this.filteredTours.length / this.pageSize));
    },
    startIndex() {
      return Math.min((this.page - 1) * this.pageSize, Math.max(this.filteredTours.length - 1, 0));
    },
    endIndex() {
      return Math.min(this.page * this.pageSize, this.filteredTours.length);
    },
    paginatedTours() {
      return this.filteredTours.slice(this.startIndex, this.endIndex);
    },
    publishedCount() {
      return this.tours.filter((tour) => tour.published).length;
    },
    draftCount() {
      return this.tours.filter((tour) => !tour.published).length;
    },
    revenueLabel() {
      const total = this.tours.reduce((sum, tour) => sum + Number(tour.usdNumeric || 0), 0);
      return (total / 1000).toFixed(1);
    },
    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.page - 1);
      const end = Math.min(this.totalPages, start + 2);
      for (let i = start; i <= end; i += 1) pages.push(i);
      return pages;
    },
  },
  watch: {
    totalPages() {
      if (this.page > this.totalPages) {
        this.page = this.totalPages;
      }
    },
  },
  methods: {
    buildHeaders() {
      const token = localStorage.getItem("token");
      const headers = { Accept: "application/json" };
      if (token) headers.Authorization = `Bearer ${token}`;
      return headers;
    },
    normalizeCollection(payload) {
      if (Array.isArray(payload)) return payload;
      if (Array.isArray(payload?.data)) return payload.data;
      if (Array.isArray(payload?.data?.data)) return payload.data.data;
      if (Array.isArray(payload?.result)) return payload.result;
      return [];
    },
    normalizeArray(value) {
      if (Array.isArray(value)) return value;
      if (typeof value === "string") return value.split(/\r?\n|,/).map((item) => item.trim()).filter(Boolean);
      return [];
    },
    normalizeBoolean(value) {
      if (typeof value === "boolean") return value;
      if (typeof value === "number") return value === 1;
      if (typeof value === "string") return ["1", "true", "active", "published", "public"].includes(value.toLowerCase());
      return false;
    },
    formatUsd(vndValue) {
      return (Number(vndValue || 0) / 25000).toFixed(2);
    },
    formatVnd(vndValue) {
      return `${Number(vndValue || 0).toLocaleString("vi-VN")} VNĐ`;
    },
    formatDate(value, fallbackIndex) {
      if (value) {
        const date = new Date(value);
        if (!Number.isNaN(date.getTime())) {
          return date.toLocaleDateString("vi-VN", { day: "2-digit", month: "2-digit", year: "numeric" });
        }
      }
      const fallbackDates = ["12/10/2023", "02/11/2023", "15/12/2023"];
      return fallbackDates[fallbackIndex % fallbackDates.length];
    },
    categoryTone(category) {
      const normalized = String(category).toLowerCase();
      if (normalized.includes("biển") || normalized.includes("beach")) return "beach";
      if (normalized.includes("phiêu lưu") || normalized.includes("adventure")) return "adventure";
      if (normalized.includes("văn") || normalized.includes("culture")) return "culture";
      return "default";
    },
    normalizeCategory(category) {
      const normalized = String(category || "").toLowerCase();
      if (normalized.includes("beach") || normalized.includes("biển")) return "Biển";
      if (normalized.includes("adventure") || normalized.includes("phiêu lưu")) return "Phiêu lưu";
      if (normalized.includes("culture") || normalized.includes("văn hóa")) return "Văn hóa";
      return category || "Tổng hợp";
    },
    mapTour(item, index) {
      const priceVnd = Number(item.gia_khuyen_mai || item.Gia_khuyen_mai || item.gia_goc || item.Gia_goc || item.price || 0);
      const codeNumber = String(item.id || item.ID || item.ma_tour || item.Ma_tour || 7700 + index).padStart(4, "0");
      const images = this.normalizeArray(item.hinh_anh || item.images);
      const category = this.normalizeCategory(item.danh_muc || item.Danh_muc || item.category);
      return {
        id: item.id || item.ID || item.ma_tour || item.Ma_tour || index + 1,
        name: item.ten_tour || item.Ten_tour || item.name || item.title || `Tour số ${index + 1}`,
        category,
        difficulty: item.muc_do_kho || item.Muc_do_kho || item.difficulty || "Trung bình",
        priceVnd,
        usdPrice: this.formatUsd(priceVnd),
        usdNumeric: Number(this.formatUsd(priceVnd)),
        priceLabel: this.formatVnd(priceVnd),
        published: this.normalizeBoolean(item.trang_thai || item.Trang_thai || item.published),
        image: images[0] || FALLBACK_IMAGES[index % FALLBACK_IMAGES.length],
        meta: item.mo_ta_ngan || item.meta || "Việt Nam • 5 ngày",
        codePrefix: "TOUR",
        codeNumber,
        createdAtLabel: this.formatDate(item.ngay_tao || item.created_at || item.createdAt, index),
        tagTone: this.categoryTone(category),
      };
    },
    async loadTours() {
      this.isLoading = true;
      this.errorMessage = "";
      try {
        const response = await goiApi(`${API_BASE}/tour`, {
          headers: this.buildHeaders(),
        });
        const data = await response.json().catch(() => null);
        if (!response.ok) throw new Error(data?.message || "Không thể tải danh sách tour từ hệ thống.");
        const collection = this.normalizeCollection(data);
        this.tours = collection.map((item, index) => this.mapTour(item, index));
      } catch (error) {
        this.errorMessage = `${error.message} Hệ thống đang hiển thị dữ liệu mẫu trong khi API chưa sẵn sàng.`;
        this.tours = [
          this.mapTour({ id: 8821, ten_tour: "Nghỉ dưỡng Vịnh Ngọc Phú Quốc", danh_muc: "Biển", gia_khuyen_mai: 22475000, trang_thai: 1, mo_ta_ngan: "Việt Nam • 5 ngày" }, 0),
          this.mapTour({ id: 9104, ten_tour: "Hành trình Phiêu lưu Hà Giang", danh_muc: "Phiêu lưu", gia_khuyen_mai: 13725000, trang_thai: 1, mo_ta_ngan: "Việt Nam • 4 ngày" }, 1),
          this.mapTour({ id: 7729, ten_tour: "Khám phá Di sản Cố đô Huế", danh_muc: "Văn hóa", gia_khuyen_mai: 8000000, trang_thai: 0, mo_ta_ngan: "Việt Nam • 3 ngày" }, 2),
          this.mapTour({ id: 6650, ten_tour: "Kỳ nghỉ Ven biển Đà Nẵng", danh_muc: "Biển", gia_khuyen_mai: 17900000, trang_thai: 1, mo_ta_ngan: "Việt Nam • 4 ngày" }, 3),
        ];
      } finally {
        this.isLoading = false;
      }
    },
    applyFilters() {
      this.page = 1;
      this.appliedAt = Date.now();
    },
    toggleStatus(tour) {
      tour.published = !tour.published;
    },
  },
  mounted() {
    this.loadTours();
  },
};
</script>

<style scoped>
.tour-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background: linear-gradient(180deg, #f6f7fb 0%, #f3f4f8 100%);
}

.tour-page__hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.tour-page__hero h1 {
  margin: 0;
  color: #111b39;
  font-size: clamp(2.35rem, 4vw, 3.2rem);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.tour-page__hero p {
  margin: 0.8rem 0 0;
  color: #5f7191;
  font-size: 1.05rem;
}

.tour-page__create {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1.1rem 1.55rem;
  border-radius: 1rem;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
  text-decoration: none;
  font-size: 1.05rem;
  font-weight: 800;
  box-shadow: 0 16px 28px rgba(79, 37, 244, 0.28);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
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
.stats-card__icon--rose { background: #fff1f2; color: #e11d48; }

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
  grid-template-columns: minmax(0, 1.9fr) repeat(3, minmax(160px, 0.8fr)) auto;
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

.table-card {
  padding: 1.35rem 1.4rem 1rem;
  border-radius: 2rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
}

.tour-table {
  display: grid;
}

.tour-table__row {
  display: grid;
  grid-template-columns: minmax(280px, 2fr) 100px 150px 130px 130px 120px 130px;
  gap: 1rem;
  align-items: center;
  padding: 1.45rem 0;
  border-top: 1px solid #eef2f7;
}

.tour-table__row--head {
  border-top: 0;
  padding-top: 0.2rem;
  color: #637594;
  font-size: 0.82rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.tour-name {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.tour-name__image {
  width: 72px;
  height: 72px;
  border-radius: 1.1rem;
  background-size: cover;
  background-position: center;
  box-shadow: inset 0 0 0 1px rgba(17, 24, 39, 0.05);
}

.tour-name strong {
  display: block;
  color: #111b39;
  font-size: 1.05rem;
  line-height: 1.35;
}

.tour-name small,
.tour-date {
  color: #66768f;
  line-height: 1.45;
}

.tour-id {
  display: inline-flex;
  flex-direction: column;
  gap: 0.2rem;
  width: fit-content;
  padding: 0.3rem 0.55rem;
  border-radius: 0.75rem;
  background: #f3f5fa;
  color: #60708d;
  font-size: 0.9rem;
  font-weight: 700;
}

.tour-id strong {
  color: #5b6c8b;
}

.tag {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 1.8rem;
  width: fit-content;
  padding: 0 0.95rem;
  border-radius: 999px;
  font-weight: 800;
}

.tag--beach { background: #f1efff; color: #4f25f4; }
.tag--adventure { background: #fff4ea; color: #f97316; }
.tag--culture { background: #f4efff; color: #7c3aed; }
.tag--default { background: #eef2ff; color: #4338ca; }

.tour-price {
  color: #111b39;
  font-size: 1.05rem;
}

.switch {
  position: relative;
  width: 56px;
  height: 30px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.switch span {
  position: absolute;
  inset: 0;
  border-radius: 999px;
  background: #d8e0ed;
  transition: 0.2s ease;
}

.switch span::before {
  content: "";
  position: absolute;
  top: 3px;
  left: 3px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #ffffff;
  transition: 0.2s ease;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
}

.switch input:checked + span {
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
}

.switch input:checked + span::before {
  transform: translateX(26px);
}

.actions {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.icon-btn {
  width: 2.2rem;
  height: 2.2rem;
  border: 0;
  border-radius: 0.8rem;
  background: transparent;
  color: #8b97ae;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.icon-btn:hover {
  background: #f4f6fb;
  color: #344054;
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
  font-size: 1rem;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.page-btn {
  width: 3rem;
  height: 3rem;
  border: 1px solid #eceff6;
  border-radius: 1rem;
  background: #ffffff;
  color: #55657f;
  font-weight: 800;
}

.page-btn.is-active {
  border-color: transparent;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
  box-shadow: 0 12px 24px rgba(79, 37, 244, 0.22);
}

@media (max-width: 1280px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .filter-bar {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .tour-table__row {
    grid-template-columns: minmax(240px, 2fr) 100px 130px 120px 120px 120px 120px;
  }
}

@media (max-width: 1024px) {
  .tour-page {
    padding: 1rem;
  }

  .tour-page__hero {
    flex-direction: column;
    align-items: flex-start;
  }

  .tour-table__row,
  .tour-table__row--head {
    grid-template-columns: 1.6fr 100px 120px 110px;
  }

  .tour-table__row span:nth-child(5),
  .tour-table__row label,
  .tour-table__row .actions,
  .tour-table__row--head span:nth-child(5),
  .tour-table__row--head span:nth-child(6),
  .tour-table__row--head span:nth-child(7) {
    display: none;
  }
}

@media (max-width: 768px) {
  .stats-grid,
  .filter-bar {
    grid-template-columns: 1fr;
  }

  .tour-table__row--head {
    display: none;
  }

  .tour-table__row {
    grid-template-columns: 1fr;
    gap: 0.8rem;
  }

  .table-footer {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>






