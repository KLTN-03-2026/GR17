<template>
  <div class="plan-page">
    <div class="plan-layout">
      <CustomerSidebar />

      <section class="plan-content">
        <header class="plan-header">
          <div>
            <p class="plan-header__eyebrow">{{ isSavedAiPage ? "Hành trình đã lưu" : "Không gian kế hoạch" }}</p>
            <h1>{{ pageTitle }}</h1>
            <p>{{ pageSubtitle }}</p>
          </div>
          <div class="plan-header__actions">
            <router-link class="btn btn--secondary" to="/khach-hang/ke-hoach/create">
              <i class="fas fa-pen-to-square"></i>
              Tạo thủ công
            </router-link>
            <router-link class="btn btn--primary" to="/khach-hang/len-ke-hoach-ai">
              <i class="fas fa-wand-magic-sparkles"></i>
              Tạo bằng AI
            </router-link>
          </div>
        </header>

        <section class="stats-strip" aria-label="Tổng quan kế hoạch">
          <article>
            <span>Tổng kế hoạch</span>
            <strong>{{ plans.length }}</strong>
          </article>
          <article>
            <span>Sắp tới</span>
            <strong>{{ upcomingPlans }}</strong>
          </article>
          <article>
            <span>Bản nháp</span>
            <strong>{{ draftPlans }}</strong>
          </article>
          <article>
            <span>Tổng ngân sách</span>
            <strong>{{ totalBudgetLabel }}</strong>
          </article>
        </section>

        <section class="plan-toolbar">
          <label class="search-pill">
            <i class="fas fa-search"></i>
            <input v-model.trim="search" type="text" placeholder="Tìm kế hoạch, nhóm hoặc địa điểm...">
          </label>

          <div class="filter-tabs" role="tablist" aria-label="Lọc kế hoạch">
            <button
              v-for="tab in filterTabs"
              :key="tab.key"
              type="button"
              :class="['tab-btn', { active: activeTab === tab.key }]"
              @click="chonTab(tab.key)"
            >
              {{ tab.label }}
            </button>
          </div>
        </section>

        <div v-if="thongBaoLoi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thongBaoLoi }}</span>
        </div>
        <div v-if="actionMessage" class="notice notice--success">
          <i class="fas fa-circle-check"></i>
          <span>{{ actionMessage }}</span>
        </div>

        <section v-if="dangTai" class="plan-grid">
          <div v-for="index in 6" :key="index" class="plan-card plan-card--skeleton"></div>
        </section>

        <section v-else-if="keHoachDaLoc.length" class="plan-grid">
          <article v-for="plan in keHoachPhanTrang" :key="plan.id" class="plan-card">
            <div class="plan-card__media" :style="{ backgroundImage: plan.coverImage }">
              <span :class="['plan-badge', getBadgeStyle(plan.statusValue)]">
                {{ getBadgeText(plan.statusValue) }}
              </span>
              <span class="source-badge">{{ getSourceLabel(plan) }}</span>
            </div>

            <div class="plan-card__body">
              <div class="plan-card__header">
                <div>
                  <p class="plan-card__group">{{ plan.tenNhom || "Nhóm chưa xác định" }}</p>
                  <h2>{{ plan.name }}</h2>
                </div>
                <div class="plan-actions">
                  <router-link :to="`/khach-hang/ke-hoach/${plan.id}/edit`" title="Chỉnh sửa">
                    <i class="fas fa-pen"></i>
                  </router-link>
                  <button type="button" @click="xoaKeHoach(plan.id)" title="Xóa">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>

              <div class="plan-card__meta">
                <span><i class="far fa-calendar-alt"></i>{{ getShortDateRange(plan) }}</span>
                <span><i class="fas fa-users"></i>{{ plan.soNguoi || 0 }} người</span>
                <span><i class="fas fa-wallet"></i>{{ plan.budgetLabel }}</span>
              </div>

              <div class="activity-summary">
                <strong>{{ plan.activities.length }} hoạt động</strong>
                <p>{{ getPlacesPreview(plan) }}</p>
              </div>

              <router-link :to="`/khach-hang/ke-hoach/${plan.id}`" :class="['btn-action-full', getActionBtnStyle(plan.statusValue)]">
                {{ getActionBtnText(plan) }}
                <i :class="getActionBtnIcon(plan.statusValue)"></i>
              </router-link>
            </div>
          </article>

          <article class="plan-card plan-card--create">
            <div class="create-icon">
              <i class="fas fa-plus"></i>
            </div>
            <h3>Bắt đầu kế hoạch mới</h3>
            <p>Chọn tạo bằng AI để có lịch trình gợi ý, hoặc tạo thủ công nếu bạn đã biết điểm đến.</p>
            <div class="create-actions">
              <router-link to="/khach-hang/len-ke-hoach-ai">Tạo bằng AI</router-link>
              <router-link to="/khach-hang/ke-hoach/create">Tạo thủ công</router-link>
            </div>
          </article>
        </section>

        <section v-if="!dangTai && !keHoachDaLoc.length" class="empty-state">
          <div class="empty-state__icon"><i class="fas fa-route"></i></div>
          <h2>Chưa có kế hoạch phù hợp</h2>
          <p>{{ emptyMessage }}</p>
          <div class="empty-state__actions">
            <router-link class="btn btn--primary" to="/khach-hang/len-ke-hoach-ai">Tạo bằng AI</router-link>
            <router-link class="btn btn--secondary" to="/khach-hang/ke-hoach/create">Tạo thủ công</router-link>
          </div>
        </section>

        <footer v-if="keHoachDaLoc.length" class="plan-footer">
          <span>Hiển thị {{ footerStart }}-{{ footerEnd }} / {{ keHoachDaLoc.length.toLocaleString("vi-VN") }}</span>
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
            <button class="page-btn" type="button" :disabled="page === tongSoTrang" @click="page = Math.min(tongSoTrang, page + 1)">
              <i class="fas fa-angle-right"></i>
            </button>
          </div>
        </footer>
      </section>
    </div>
  </div>
</template>

<script>
import { createToaster } from "@meforma/vue-toaster";
import { goiApi } from "../../../services/httpClient.js";
import CustomerSidebar from "../CustomerSidebar.vue";
import { showConfirm } from "../../../services/appDialog";
import {
  API_BASE,
  buildHeaders,
  formatCurrencyDisplay,
  getStoredCustomerId,
  mapPlan,
  chuanHoaDanhSach,
} from "./planShared";

const toaster = createToaster({
  position: "top-right",
  duration: 3000,
});

const PLAN_COVERS = [
  "linear-gradient(180deg, rgba(7, 26, 54, 0.08), rgba(7, 26, 54, 0.26)), url('https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1200&q=80')",
  "linear-gradient(180deg, rgba(7, 26, 54, 0.08), rgba(7, 26, 54, 0.26)), url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80')",
  "linear-gradient(180deg, rgba(7, 26, 54, 0.08), rgba(7, 26, 54, 0.26)), url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80')",
  "linear-gradient(180deg, rgba(7, 26, 54, 0.08), rgba(7, 26, 54, 0.26)), url('https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=1200&q=80')",
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
      search: "",
      activeTab: "all",
      page: 1,
      pageSize: 6,
    };
  },
  computed: {
    filterTabs() {
      return [
        { key: "all", label: "Tất cả" },
        { key: "upcoming", label: "Sắp tới" },
        { key: "draft", label: "Bản nháp" },
        { key: "completed", label: "Đã hoàn thành" },
        { key: "ai", label: "Tạo bằng AI" },
      ];
    },
    isSavedAiPage() {
      return this.$route.path === "/khach-hang/hanh-trinh-da-luu";
    },
    pageTitle() {
      return this.isSavedAiPage ? "Hành trình AI đã lưu" : "Kế hoạch của bạn";
    },
    pageSubtitle() {
      if (this.isSavedAiPage) {
        return "Theo dõi các hành trình đã tạo bằng AI, tiếp tục chỉnh sửa timeline và mở bản đồ khi có tọa độ thật.";
      }
      return "Quản lý kế hoạch theo nhóm, ngân sách, trạng thái và lịch trình thật trong một màn hình.";
    },
    keHoachDaLoc() {
      const keyword = this.search.toLowerCase();
      return this.plans.filter((plan) => {
        const locations = plan.activities.map((activity) => activity.locationName).join(" ");
        const haystack = [plan.id, plan.name, plan.tenNhom, plan.maNhom, locations].join(" ").toLowerCase();
        const matchesSearch = haystack.includes(keyword);
        const matchesTab =
          this.activeTab === "all" ||
          (this.activeTab === "upcoming" && plan.statusValue === 1) ||
          (this.activeTab === "draft" && plan.statusValue === 0) ||
          (this.activeTab === "completed" && plan.statusValue === 2) ||
          (this.activeTab === "ai" && plan.isAiPlan);

        return matchesSearch && matchesTab;
      });
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
      for (let i = start; i <= end; i += 1) pages.push(i);
      return pages;
    },
    upcomingPlans() {
      return this.plans.filter((plan) => plan.statusValue === 1).length;
    },
    draftPlans() {
      return this.plans.filter((plan) => plan.statusValue === 0).length;
    },
    totalBudgetLabel() {
      return formatCurrencyDisplay(this.plans.reduce((sum, plan) => sum + plan.budget, 0));
    },
    emptyMessage() {
      if (!this.maKhachHang) {
        return "Bạn cần đăng nhập tài khoản khách hàng để tải danh sách kế hoạch theo nhóm.";
      }
      if (this.search || this.activeTab !== "all") {
        return "Không có kế hoạch nào khớp với bộ lọc hiện tại.";
      }
      return "Bạn chưa có kế hoạch nào. Hãy tạo một kế hoạch mới để bắt đầu lên lịch trình.";
    },
  },
  watch: {
    "$route.path"() {
      this.activeTab = this.isSavedAiPage ? "ai" : "all";
      this.page = 1;
    },
    search() {
      this.page = 1;
    },
    tongSoTrang() {
      if (this.page > this.tongSoTrang) this.page = this.tongSoTrang;
    },
  },
  methods: {
    chonTab(tab) {
      this.activeTab = tab;
      this.page = 1;
    },
    applyRouteFeedback() {
      const maMoiTao = String(this.$route.query.created || "").trim();
      if (!maMoiTao) return;

      this.actionMessage = `Đã tạo kế hoạch ${maMoiTao} thành công.`;
      const query = { ...this.$route.query };
      delete query.created;
      this.$router.replace({ path: this.$route.path, query }).catch(() => {});
    },
    async taiKeHoach() {
      this.dangTai = true;
      this.thongBaoLoi = "";
      this.actionMessage = "";
      this.maKhachHang = getStoredCustomerId();

      if (!this.maKhachHang) {
        this.plans = [];
        this.thongBaoLoi = "Không tìm thấy mã khách hàng trong phiên đăng nhập hiện tại.";
        this.dangTai = false;
        return;
      }

      try {
        const url = `${API_BASE}/ke-hoach?ma_khach_hang=${encodeURIComponent(this.maKhachHang)}`;
        const phanHoi = await goiApi(url, { headers: buildHeaders() });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));

        if (!phanHoi.ok) {
          throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách kế hoạch.");
        }

        this.plans = chuanHoaDanhSach(duLieuPhanHoi).map((item, index) => ({
          ...mapPlan(item),
          coverImage: PLAN_COVERS[index % PLAN_COVERS.length],
        }));
        this.page = 1;
        this.applyRouteFeedback();
      } catch (error) {
        this.plans = [];
        this.thongBaoLoi = error.message || "Không thể tải danh sách kế hoạch.";
      } finally {
        this.dangTai = false;
      }
    },
    getBadgeStyle(status) {
      if (status === 1) return "badge-upcoming";
      if (status === 2) return "badge-completed";
      return "badge-draft";
    },
    getBadgeText(status) {
      if (status === 1) return "Sắp tới";
      if (status === 2) return "Đã hoàn thành";
      return "Bản nháp";
    },
    getSourceLabel(plan) {
      const mode = plan.aiData?.generation_mode || plan.aiData?._metadata?.generation_mode || "";
      if (mode === "fallback") return "Gợi ý hệ thống";
      return plan.isAiPlan ? "AI" : "Thủ công";
    },
    getActionBtnStyle(status) {
      if (status === 1) return "btn-upcoming";
      if (status === 2) return "btn-completed";
      return "btn-draft";
    },
    getActionBtnText(plan) {
      if (!plan.activities.length) return "Hoàn thiện lịch trình";
      return "Xem chi tiết";
    },
    getActionBtnIcon(status) {
      if (status === 2) return "fas fa-history";
      if (status === 0) return "fas fa-bars-staggered";
      return "fas fa-arrow-right";
    },
    getShortDateRange(plan) {
      if (!plan.dateRangeLabel) return "-- / --";
      return plan.dateRangeLabel.replace(/\/\d{4}/g, "");
    },
    getPlacesPreview(plan) {
      const allNames = plan.activities
        .map((activity) => activity.locationName)
        .filter(name => name && !name.toLowerCase().includes("tham gia tour"));
      
      const uniqueNames = [...new Set(allNames)];

      if (!uniqueNames.length) return "Chưa có địa điểm trong lịch trình.";
      const preview = uniqueNames.slice(0, 3).join(", ");
      return uniqueNames.length > 3 ? `${preview}...` : preview;
    },
    async xoaKeHoach(id) {
      const isConfirmed = await showConfirm({
        title: "Xác nhận xóa",
        message: "Bạn có chắc chắn muốn xóa kế hoạch này không? Hành động này không thể hoàn tác.",
        tone: "warning",
      });
      if (!isConfirmed) return;

      try {
        const phanHoi = await goiApi(`${API_BASE}/ke-hoach/${encodeURIComponent(id)}`, {
          method: "DELETE",
          headers: buildHeaders(),
        });
        const duLieu = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok) throw new Error(duLieu.message || "Không thể xóa kế hoạch.");
        this.actionMessage = "Đã xóa kế hoạch thành công.";
        await this.taiKeHoach();
      } catch (error) {
        toaster.error(error.message || "Không thể xóa kế hoạch.");
      }
    },
  },
  mounted() {
    this.activeTab = this.isSavedAiPage ? "ai" : "all";
    this.taiKeHoach();
  },
};
</script>

<style scoped>
.plan-page {
  min-height: 100vh;
  background: #f5f7fb;
  color: #111827;
  font-family: "Inter", "Segoe UI", sans-serif;
}

.plan-layout {
  width: min(1440px, calc(100% - 24px));
  margin: 0 auto;
  padding-bottom: 44px;
  display: grid;
  grid-template-columns: 264px minmax(0, 1fr);
  gap: 24px;
}

.plan-content {
  padding-top: 24px;
}

.plan-header {
  display: flex;
  justify-content: space-between;
  gap: 24px;
  align-items: flex-end;
  margin-bottom: 22px;
  padding: 30px 34px;
  border-radius: 22px;
  background: #eaf4fd;
  border: 1px solid #dbeafe;
}

.plan-header__eyebrow {
  margin: 0 0 8px;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-size: 0.76rem;
  font-weight: 900;
}

.plan-header h1 {
  margin: 0;
  color: #1e3a8a;
  font-size: clamp(2rem, 4vw, 3.1rem);
  line-height: 1.05;
  font-weight: 900;
  letter-spacing: 0;
}

.plan-header p {
  margin: 12px 0 0;
  color: #475569;
  max-width: 700px;
  line-height: 1.65;
}

.plan-header__actions,
.empty-state__actions,
.create-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.btn,
.create-actions a {
  min-height: 44px;
  padding: 0 18px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border: 0;
  text-decoration: none;
  font-weight: 800;
  cursor: pointer;
}

.btn--primary {
  background: #00476b;
  color: #ffffff;
}

.btn--secondary,
.create-actions a {
  background: #ffffff;
  color: #0f4f73;
  border: 1px solid #dbe4f0;
}

.stats-strip {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
  margin-bottom: 18px;
}

.stats-strip article {
  background: #ffffff;
  border: 1px solid #e5ebf4;
  border-radius: 18px;
  padding: 18px;
}

.stats-strip span {
  display: block;
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 800;
  margin-bottom: 8px;
}

.stats-strip strong {
  color: #0f4f73;
  font-size: 1.45rem;
  font-weight: 900;
}

.plan-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.search-pill {
  height: 48px;
  min-width: min(100%, 360px);
  flex: 1;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 18px;
  background: #ffffff;
  border: 1px solid #e5ebf4;
  border-radius: 999px;
}

.search-pill i {
  color: #94a3b8;
}

.search-pill input {
  width: 100%;
  border: 0;
  outline: 0;
  background: transparent;
  color: #111827;
}

.filter-tabs {
  display: flex;
  gap: 6px;
  padding: 6px;
  border-radius: 999px;
  background: #ffffff;
  border: 1px solid #e5ebf4;
}

.tab-btn {
  min-height: 36px;
  border: 0;
  border-radius: 999px;
  background: transparent;
  color: #64748b;
  padding: 0 14px;
  font-weight: 800;
  cursor: pointer;
}

.tab-btn.active {
  background: #e0f2fe;
  color: #075985;
}

.notice {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 18px;
  padding: 14px 18px;
  border-radius: 14px;
  font-weight: 700;
}

.notice--error {
  background: #fee2e2;
  color: #b91c1c;
}

.notice--success {
  background: #dcfce7;
  color: #15803d;
}

.plan-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 20px;
}

.plan-card {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  min-height: 380px;
  background: #ffffff;
  border: 1px solid #e8edf5;
  border-radius: 18px;
  box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
}

.plan-card__media {
  min-height: 142px;
  background-size: cover;
  background-position: center;
  padding: 14px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 10px;
}

.plan-badge,
.source-badge {
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 0.74rem;
  font-weight: 900;
  background: rgba(255, 255, 255, 0.92);
  color: #0f172a;
}

.badge-upcoming {
  background: #0ea5e9;
  color: #ffffff;
}

.badge-completed {
  background: #64748b;
  color: #ffffff;
}

.badge-draft {
  background: #f59e0b;
  color: #ffffff;
}

.plan-card__body {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 18px;
}

.plan-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 14px;
}

.plan-card__group {
  margin: 0 0 6px;
  color: #64748b;
  font-size: 0.82rem;
  font-weight: 800;
}

.plan-card__header h2 {
  margin: 0;
  color: #0f172a;
  font-size: 1.12rem;
  line-height: 1.35;
  font-weight: 900;
}

.plan-actions {
  display: flex;
  gap: 8px;
}

.plan-actions a,
.plan-actions button {
  width: 34px;
  height: 34px;
  border: 0;
  border-radius: 10px;
  background: #f8fafc;
  color: #64748b;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  cursor: pointer;
}

.plan-actions a:hover {
  color: #0369a1;
  background: #e0f2fe;
}

.plan-actions button:hover {
  color: #e11d48;
  background: #ffe4e6;
}

.plan-card__meta {
  display: grid;
  gap: 8px;
  margin-bottom: 14px;
  color: #475569;
  font-weight: 700;
  font-size: 0.9rem;
}

.plan-card__meta span {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.plan-card__meta i {
  color: #0f79a8;
  width: 16px;
}

.activity-summary {
  padding: 12px 14px;
  border-radius: 14px;
  background: #f8fafc;
  border: 1px solid #edf2f7;
  margin-bottom: 16px;
}

.activity-summary strong {
  color: #0f4f73;
  display: block;
  margin-bottom: 5px;
}

.activity-summary p {
  margin: 0;
  color: #64748b;
  line-height: 1.45;
  font-size: 0.9rem;
}

.btn-action-full {
  min-height: 44px;
  margin-top: auto;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-decoration: none;
  font-weight: 900;
}

.btn-upcoming {
  background: #e0f2fe;
  color: #0284c7;
}

.btn-completed {
  background: #f1f5f9;
  color: #475569;
}

.btn-draft {
  background: #00476b;
  color: #ffffff;
}

.plan-card--create,
.empty-state {
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 28px;
  border-style: dashed;
  box-shadow: none;
}

.create-icon,
.empty-state__icon {
  width: 58px;
  height: 58px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #0f79a8;
  background: #e0f2fe;
  font-size: 1.35rem;
  margin-bottom: 18px;
}

.plan-card--create h3,
.empty-state h2 {
  margin: 0 0 10px;
  color: #0f172a;
  font-weight: 900;
}

.plan-card--create p,
.empty-state p {
  margin: 0 0 18px;
  color: #64748b;
  line-height: 1.65;
}

.empty-state {
  border: 1px dashed #cbd5e1;
  border-radius: 20px;
  background: #ffffff;
  min-height: 320px;
}

.plan-card--skeleton {
  background: linear-gradient(90deg, #f1f5f9, #e2e8f0, #f1f5f9);
  background-size: 200% 100%;
  animation: pulse 1.3s linear infinite;
}

.plan-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  padding-top: 28px;
  color: #64748b;
  font-weight: 700;
}

.pagination {
  display: flex;
  gap: 8px;
}

.page-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #475569;
  font-weight: 800;
  cursor: pointer;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-btn.is-active {
  background: #00476b;
  border-color: #00476b;
  color: #ffffff;
}

@keyframes pulse {
  from { background-position: 0 0; }
  to { background-position: -200% 0; }
}

@media (max-width: 1200px) {
  .plan-layout {
    grid-template-columns: 1fr;
  }

  .plan-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {
  .plan-layout {
    width: calc(100% - 20px);
  }

  .plan-header,
  .plan-toolbar,
  .plan-footer {
    flex-direction: column;
    align-items: stretch;
  }

  .stats-strip,
  .plan-grid {
    grid-template-columns: 1fr;
  }

  .filter-tabs {
    border-radius: 16px;
    flex-wrap: wrap;
  }
}
</style>
