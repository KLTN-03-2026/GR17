<template>
  <div class="plan-page">
    <div class="plan-layout">
      <CustomerSidebar />

      <section class="plan-content">
        <!-- New Hero Banner -->
        <header class="plan-hero-card">
          <div class="plan-hero-text">
            <h1>{{ pageTitle }}</h1>
            <p>{{ pageSubtitle }}</p>
            <router-link class="btn-create-pill" to="/khach-hang/len-ke-hoach-ai">
              <i class="fas fa-plus"></i> Tạo lịch trình mới
            </router-link>
          </div>
          <div class="plan-hero-bg-icon">
            <i class="fas fa-search-location"></i>
          </div>
        </header>

        <!-- New Toolbar -->
        <section class="plan-toolbar">
          <label class="search-pill">
            <i class="fas fa-search"></i>
            <input
              v-model.trim="search"
              type="text"
              placeholder="Tìm kiếm lịch trình..."
            >
          </label>

          <div class="filter-tabs">
            <button :class="['tab-btn', statusFilter === 'all' ? 'active' : '']" @click="statusFilter = 'all'; applyFilters()">Tất cả</button>
            <button :class="['tab-btn', statusFilter === '1' ? 'active' : '']" @click="statusFilter = '1'; applyFilters()">Sắp tới</button>
            <button :class="['tab-btn', statusFilter === '2' ? 'active' : '']" @click="statusFilter = '2'; applyFilters()">Hoàn thành</button>
            <button :class="['tab-btn', statusFilter === '0' ? 'active' : '']" @click="statusFilter = '0'; applyFilters()">Bản nháp</button>
          </div>
        </section>

        <!-- Messages -->
        <div v-if="thongBaoLoi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i><span>{{ thongBaoLoi }}</span>
        </div>
        <div v-if="actionMessage" class="notice notice--success">
          <i class="fas fa-circle-check"></i><span>{{ actionMessage }}</span>
        </div>

        <section v-if="dangTai" class="plan-grid plan-grid--loading">
          <div class="plan-card plan-card--skeleton"></div>
          <div class="plan-card plan-card--skeleton"></div>
          <div class="plan-card plan-card--skeleton"></div>
        </section>

        <!-- Grid Cards -->
        <section v-else-if="keHoachDaLoc.length" class="plan-grid">
          <article
            v-for="plan in keHoachPhanTrang"
            :key="plan.id"
            class="plan-card"
          >
            <div class="plan-card__media" :style="{ backgroundImage: plan.coverImage }">
              <span :class="['plan-badge', getBadgeStyle(plan.statusValue)]">
                {{ getBadgeText(plan.statusValue) }}
              </span>
            </div>

            <div class="plan-card__body">
              <div class="plan-card__header">
                <h2>{{ plan.name }}</h2>
                <div class="plan-actions">
                  <router-link :to="`/khach-hang/ke-hoach/${plan.id}/edit`" title="Chỉnh sửa"><i class="fas fa-pen"></i></router-link>
                  <button type="button" @click="xoaKeHoach(plan.id)" title="Xoá"><i class="fas fa-trash"></i></button>
                </div>
              </div>

              <div class="plan-card__meta">
                <div class="meta-item">
                  <i class="far fa-calendar-alt"></i>
                  <span>{{ getShortDateRange(plan) }}</span>
                </div>
                <div class="meta-item text-green">
                  <i class="fas fa-money-bill-wave"></i>
                  <span>{{ plan.budgetLabel }}</span>
                </div>
              </div>

              <router-link :to="`/khach-hang/ke-hoach/${plan.id}`" :class="['btn-action-full', getActionBtnStyle(plan.statusValue)]">
                {{ getActionBtnText(plan.statusValue) }}
                <i :class="getActionBtnIcon(plan.statusValue)"></i>
              </router-link>
            </div>
          </article>

          <!-- Create New Card -->
          <article class="plan-card plan-card--dashed">
            <div class="dashed-icon">
              <i class="fas fa-plus"></i>
            </div>
            <h3>Bắt đầu hành trình mới</h3>
            <p>Hãy để AI của chúng tôi thiết kế một lịch trình hoàn hảo cho điểm đến tiếp theo của bạn.</p>
            <router-link to="/khach-hang/len-ke-hoach-ai" class="btn-text-blue">
              Tạo ngay
            </router-link>
          </article>
        </section>

        <section v-if="!dangTai && !keHoachDaLoc.length" class="empty-state">
           <article class="plan-card plan-card--dashed">
            <div class="dashed-icon">
              <i class="fas fa-plus"></i>
            </div>
            <h3>Bắt đầu hành trình mới</h3>
            <p>{{ emptyMessage }}</p>
            <router-link to="/khach-hang/len-ke-hoach-ai" class="btn-text-blue">
              Tạo ngay
            </router-link>
          </article>
        </section>

        <!-- Pagination -->
        <footer v-if="keHoachDaLoc.length" class="plan-footer">
          <span>Hiển thị {{ footerStart }}-{{ footerEnd }} / {{ keHoachDaLoc.length.toLocaleString() }}</span>
          <div class="pagination">
            <button class="page-btn" type="button" :disabled="page === 1" @click="page = Math.max(1, page - 1)"><i class="fas fa-angle-left"></i></button>
            <button v-for="pageNumber in visiblePages" :key="pageNumber" type="button" :class="['page-btn', { 'is-active': page === pageNumber }]" @click="page = pageNumber">{{ pageNumber }}</button>
            <button class="page-btn" type="button" :disabled="page === tongSoTrang" @click="page = Math.min(tongSoTrang, page + 1)"><i class="fas fa-angle-right"></i></button>
          </div>
        </footer>
      </section>
    </div>
  </div>

</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import CustomerSidebar from '../CustomerSidebar.vue';
import {
  API_BASE,
  buildHeaders,
  formatCurrencyDisplay,
  getStoredCustomerId,
  getStoredUser,
  mapPlan,
  chuanHoaDanhSach,
} from "./planShared";

const PLAN_COVERS = [
  "linear-gradient(180deg, rgba(7, 26, 54, 0.08), rgba(7, 26, 54, 0.2)), url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80')",
  "linear-gradient(180deg, rgba(7, 26, 54, 0.08), rgba(7, 26, 54, 0.2)), url('https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1200&q=80')",
  "linear-gradient(180deg, rgba(7, 26, 54, 0.08), rgba(7, 26, 54, 0.2)), url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80')",
  "linear-gradient(180deg, rgba(7, 26, 54, 0.08), rgba(7, 26, 54, 0.2)), url('https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=1200&q=80')",
];
export default {
  name: "DanhSachKeHoach",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      dangTai: false,
      thongBaoLoi: "",
      actionMessage: "",
      maKhachHang: "",
      plans: [],
      keHoachDangChon: null,
      search: "",
      statusFilter: "all",
      sourceFilter: "all",
      page: 1,
      pageSize: 6,
      appliedAt: 0,
    };
  },
  computed: {
    keHoachDaLoc() {
      const _ = this.appliedAt;
      return this.plans.filter((plan) => {
        const haystack = [plan.id, plan.name, plan.tenNhom, plan.maNhom].join(" ").toLowerCase();
        const matchesSearch = haystack.includes(this.search.toLowerCase());
        const matchesStatus = this.statusFilter === "all" || String(plan.statusValue) === this.statusFilter;
        const matchesSource = this.sourceFilter === "all" || (this.sourceFilter === "ai" && plan.isAiPlan);
        return matchesSearch && matchesStatus && matchesSource;
      });
    },
    isSavedAiPage() {
      return this.$route.path === "/khach-hang/hanh-trinh-da-luu";
    },
    pageTitle() {
      return this.isSavedAiPage ? "Hành trình đã lưu" : "Lịch trình của bạn";
    },
    pageSubtitle() {
      if (this.isSavedAiPage) {
        return "Tất cả hành trình được bạn lưu từ AI sẽ xuất hiện tại đây để theo dõi và chỉnh sửa nhanh.";
      }

      return "Khám phá và quản lý các chuyến đi mơ ước được tạo bởi AI. Biến những ý tưởng thành hiện thực chỉ với một vài cú chạm.";
    },
    tongSoTrang() {
      return Math.max(1, Math.ceil(this.keHoachDaLoc.length / this.pageSize));
    },
    keHoachPhanTrang() {
      const start = (this.page - 1) * this.pageSize;
      return this.keHoachDaLoc.slice(start, start + this.pageSize);
    },
    footerStart() {
      if (!this.keHoachDaLoc.length) return 0;
      return (this.page - 1) * this.pageSize + 1;
    },
    footerEnd() {
      return Math.min(this.page * this.pageSize, this.keHoachDaLoc.length);
    },
    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.page - 1);
      const end = Math.min(this.tongSoTrang, start + 2);
      for (let i = start; i <= end; i += 1) {
        pages.push(i);
      }
      return pages;
    },
    activePlans() {
      return this.plans.filter((plan) => plan.isActive).length;
    },
    totalBudgetLabel() {
      const total = this.plans.reduce((sum, plan) => sum + plan.budget, 0);
      return formatCurrencyDisplay(total);
    },
    soLuongNhom() {
      return new Set(this.plans.map((plan) => plan.maNhom).filter(Boolean)).size;
    },
    emptyMessage() {
      if (!this.maKhachHang) {
        return "Bạn cần đăng nhập tài khoản khách hàng để tải đúng danh sách kế hoạch theo nhóm hành trình.";
      }

      if (this.search || this.statusFilter !== "all") {
        return "Không có kế hoạch nào khớp với bộ lọc hiện tại.";
      }

      if (this.sourceFilter === "ai") {
        return "Bạn chưa lưu hành trình AI nào. Hãy vào mục Lên kế hoạch AI và bấm Lưu hành trình.";
      }

      return "Hệ thống chưa ghi nhận kế hoạch nào gắn với nhóm hành trình của bạn.";
    },
  },
  watch: {
    "$route.path"() {
      this.applyRouteContext();
      this.applyFilters();
    },
    tongSoTrang() {
      if (this.page > this.tongSoTrang) {
        this.page = this.tongSoTrang;
      }
    },
    keHoachPhanTrang(plans) {
      if (!plans.length) {
        this.keHoachDangChon = null;
        return;
      }

      const enriched = plans.map((plan, index) => ({
        ...plan,
        coverImage: PLAN_COVERS[index % PLAN_COVERS.length],
      }));

      this.plans = this.plans.map((plan) => {
        const match = enriched.find((item) => item.id === plan.id);
        return match || plan;
      });

      const stillVisible = enriched.find((plan) => plan.id === this.keHoachDangChon?.id);
      if (!stillVisible) {
        [this.keHoachDangChon] = enriched;
      }
    },
  },
  methods: {
    applyRouteContext() {
      this.sourceFilter = this.isSavedAiPage ? "ai" : "all";
    },
    applyRouteFeedback() {
      const maMoiTao = String(this.$route.query.created || "").trim();
      if (!maMoiTao) return;

      const createdIndex = this.plans.findIndex((plan) => plan.id === maMoiTao);
      if (createdIndex >= 0) {
        this.page = Math.floor(createdIndex / this.pageSize) + 1;
        this.keHoachDangChon = this.plans[createdIndex];
        this.actionMessage = `Đã tạo kế hoạch ${maMoiTao} thành công.`;
      } else {
        this.actionMessage = "Đã tạo kế hoạch thành công.";
      }

      const query = { ...this.$route.query };
      delete query.created;
      this.$router.replace({ path: this.$route.path, query }).catch(() => {});
    },
    applyFilters() {
      this.page = 1;
      this.appliedAt = Date.now();
    },
    async taiKeHoach() {
      this.dangTai = true;
      this.thongBaoLoi = "";
      this.actionMessage = "";
      this.maKhachHang = getStoredCustomerId();

      if (!this.maKhachHang) {
        this.plans = [];
        this.keHoachDangChon = null;
        this.dangTai = false;
        this.thongBaoLoi = "Không tìm thấy mã khách hàng trong phiên đăng nhập hiện tại.";
        return;
      }

      try {
        const url = `${API_BASE}/ke-hoach?ma_khach_hang=${encodeURIComponent(this.maKhachHang)}`;
        const phanHoi = await goiApi(url, {
          headers: buildHeaders(),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));


        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách kế hoạch.");
        }
        this.plans = chuanHoaDanhSach(duLieuPhanHoi).map((item, index) => ({
          ...mapPlan(item),
          coverImage: PLAN_COVERS[index % PLAN_COVERS.length],
        }));
        this.keHoachDangChon = this.plans[0] || null;
        this.page = 1;
        this.appliedAt = Date.now();
        this.applyRouteFeedback();
      } catch (error) {
        this.plans = [];
        this.keHoachDangChon = null;
        this.thongBaoLoi = error.message || "Không thể tải danh sách kế hoạch.";
      } finally {
        this.dangTai = false;
      }
    },
    getBadgeStyle(status) {
      if(status === 1) return 'badge-upcoming';
      if(status === 2) return 'badge-completed';
      return 'badge-draft';
    },
    getBadgeText(status) {
      if(status === 1) return 'Upcoming';
      if(status === 2) return 'Completed';
      return 'Drafts';
    },
    getActionBtnStyle(status) {
      if(status === 1) return 'btn-upcoming';
      if(status === 2) return 'btn-completed';
      return 'btn-draft';
    },
    getActionBtnText(status) {
      if(status === 1) return 'Xem chi tiết';
      if(status === 2) return 'Xem chi tiết';
      return 'Hoàn thiện bản nháp';
    },
    getActionBtnIcon(status) {
      if(status === 1) return 'fas fa-arrow-right';
      if(status === 2) return 'fas fa-history';
      return 'fas fa-bars-staggered';
    },
    getShortDateRange(plan) {
      if(!plan.dateRangeLabel) return '-- / --';
      // Format from "01/10/2026 - 03/10/2026" to "01/10 - 03/10"
      return plan.dateRangeLabel.replace(/\/\d{4}/g, '');
    },
    async xoaKeHoach(id) {
      if (!confirm("Bạn có chắc chắn muốn xóa lịch trình này?")) return;
      try {
        const phanHoi = await goiApi(`${API_BASE}/ke-hoach/${id}`, {
          method: "DELETE",
          headers: buildHeaders()
        });
        if (!phanHoi.ok) throw new Error("Lỗi khi xóa kế hoạch");
        this.actionMessage = "Đã xóa lịch trình thành công";
        setTimeout(() => this.actionMessage = "", 3000);
        this.taiKeHoach();
      } catch(error) {
        alert(error.message);
      }
    },
  },
  mounted() {
    this.applyRouteContext();
    this.taiKeHoach();
  },
};
</script>

<style scoped>
.plan-page {
  background: #f5f7fa;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
}

.plan-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding: 0 0 40px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 24px;
}

.plan-content {
  padding: 24px 0 0;
  max-width: 100%;
}

/* HERO BANNER */
.plan-hero-card {
  position: relative;
  border-radius: 24px;
  background: linear-gradient(135deg, #0f6292 0%, #1a4d6f 100%);
  color: white;
  padding: 32px 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  overflow: hidden;
  margin-bottom: 24px;
  box-shadow: 0 12px 24px rgba(15, 98, 146, 0.15);
}

.plan-hero-text {
  position: relative;
  z-index: 2;
  max-width: 600px;
}

.plan-hero-text h1 {
  margin: 0 0 12px;
  font-size: 2.2rem;
  font-weight: 800;
  letter-spacing: -0.02em;
}

.plan-hero-text p {
  margin: 0 0 24px;
  font-size: 1.05rem;
  line-height: 1.6;
  opacity: 0.9;
}

.btn-create-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: white;
  color: #0f6292;
  padding: 12px 24px;
  border-radius: 999px;
  font-weight: 700;
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.btn-create-pill:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.plan-hero-bg-icon {
  position: absolute;
  right: -20px;
  bottom: -40px;
  font-size: 200px;
  opacity: 0.1;
  transform: rotate(-15deg);
  z-index: 1;
}

/* TOOLBAR */
.plan-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  gap: 16px;
  flex-wrap: wrap;
}

.search-pill {
  display: flex;
  align-items: center;
  gap: 12px;
  background: white;
  padding: 0 20px;
  height: 48px;
  border-radius: 999px;
  flex: 1;
  max-width: 360px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
  border: 1px solid #eef0f4;
}

.search-pill i {
  color: #8fa0b5;
}

.search-pill input {
  border: none;
  background: transparent;
  height: 100%;
  width: 100%;
  outline: none;
  font-size: 0.95rem;
  color: #334155;
}

.filter-tabs {
  display: flex;
  background: #e7f0fp; /* slight tint */
  background: white;
  padding: 6px;
  border-radius: 999px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
  border: 1px solid #eef0f4;
}

.tab-btn {
  background: transparent;
  border: none;
  padding: 8px 18px;
  border-radius: 999px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
}

.tab-btn:hover {
  color: #0f6292;
}

.tab-btn.active {
  background: #eef7ff;
  color: #0f6292;
}

/* GRID */
.plan-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 24px;
}

.plan-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,0.04);
  border: 1px solid #f1f3f7;
  transition: all 0.25s cubic-bezier(0.02, 0.01, 0.47, 1);
  display: flex;
  flex-direction: column;
}

.plan-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(15, 98, 146, 0.12);
}

.plan-card__media {
  height: 160px;
  background-size: cover;
  background-position: center;
  padding: 16px;
  display: flex;
  justify-content: flex-end;
  align-items: flex-start;
}

.plan-badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  backdrop-filter: blur(4px);
}

.badge-upcoming {
  background: rgba(14, 165, 233, 0.9);
  color: white;
}

.badge-completed {
  background: rgba(100, 116, 139, 0.9);
  color: white;
}

.badge-draft {
  background: rgba(51, 65, 85, 0.9);
  color: white;
}

.plan-card__body {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.plan-card__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
}

.plan-card__header h2 {
  margin: 0;
  font-size: 1.15rem;
  color: #0d2f57;
  font-weight: 800;
  line-height: 1.4;
}

.plan-actions {
  display: flex;
  gap: 8px;
}

.plan-actions a, .plan-actions button {
  background: #f8fafc;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.plan-actions a:hover {
  background: #e0f2fe;
  color: #0369a1;
}

.plan-actions button:hover {
  background: #fee2e2;
  color: #e11d48;
}

.plan-card__meta {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.9rem;
  font-weight: 600;
  color: #475569;
}

.meta-item.text-green {
  color: #059669;
}

.meta-item i {
  color: #94a3b8;
}

.meta-item.text-green i {
  color: #10b981;
}

.btn-action-full {
  margin-top: auto;
  border-radius: 12px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 700;
  text-decoration: none;
  transition: all 0.2s;
  font-size: 0.95rem;
}

.btn-upcoming {
  background: #e0f2fe;
  color: #0284c7;
}

.btn-upcoming:hover {
  background: #bae6fd;
}

.btn-completed {
  background: #f1f5f9;
  color: #475569;
}

.btn-completed:hover {
  background: #e2e8f0;
}

.btn-draft {
  background: #1e293b;
  color: white;
}

.btn-draft:hover {
  background: #0f172a;
}

/* CREATE NEW CARD */
.plan-card--dashed {
  border: 2px dashed #cbd5e1;
  background: rgba(248, 250, 252, 0.5);
  box-shadow: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 32px 24px;
  min-height: 380px;
}

.plan-card--dashed:hover {
  border-color: #0ea5e9;
  background: #f0f9ff;
  box-shadow: none;
}

.dashed-icon {
  width: 64px;
  height: 64px;
  background: #e0f2fe;
  color: #0284c7;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin-bottom: 20px;
}

.plan-card--dashed h3 {
  margin: 0 0 12px;
  color: #0f172a;
  font-size: 1.15rem;
}

.plan-card--dashed p {
  margin: 0 0 24px;
  color: #64748b;
  font-size: 0.95rem;
  line-height: 1.6;
}

.btn-text-blue {
  color: #0284c7;
  font-weight: 700;
  text-decoration: none;
  font-size: 1rem;
}

.btn-text-blue:hover {
  text-decoration: underline;
}

/* NOTICE */
.notice {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  border-radius: 12px;
  margin-bottom: 20px;
  font-weight: 600;
}

.notice--error {
  background: #fee2e2;
  color: #b91c1c;
}

.notice--success {
  background: #dcfce7;
  color: #15803d;
}

/* PAGINATION */
.plan-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 32px;
  color: #64748b;
  font-size: 0.9rem;
}

.pagination {
  display: flex;
  gap: 8px;
}

.page-btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #475569;
  font-weight: 600;
  transition: all 0.2s;
}

.page-btn:not(:disabled):hover {
  background: #f1f5f9;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-btn.is-active {
  background: #0f6292;
  color: white;
  border-color: #0f6292;
}

/* SKELETON */
.plan-grid--loading .plan-card--skeleton {
  min-height: 380px;
  background: #f1f5f9;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { opacity: 0.6; }
  50% { opacity: 1; }
  100% { opacity: 0.6; }
}

@media (max-width: 1200px) {
  .plan-layout {
    grid-template-columns: 1fr;
  }
  .plan-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 860px) {
  .plan-grid {
    grid-template-columns: 1fr;
  }
  .plan-hero-card {
    padding: 24px;
    flex-direction: column;
    align-items: flex-start;
  }
  .plan-hero-bg-icon {
    display: none;
  }
  .plan-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  .filter-tabs {
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>





