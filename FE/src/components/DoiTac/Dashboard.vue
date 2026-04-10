<template>
  <PartnerShell
    title="Tổng quan đối tác"
    subtitle="Theo dõi trạng thái duyệt và điều hướng nhanh đến các chức năng quản lý."
    :partner="partner"
    :loading="loading"
  >
    <template #headerActions>
      <button class="refresh-btn" type="button" @click="loadDashboard">
        <i class="fas fa-rotate"></i>
        Làm mới
      </button>
    </template>

    <section v-if="errorMessage" class="alert-box">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ errorMessage }}</span>
    </section>

    <section class="stats-grid">
      <article class="stat-card">
        <p>Tour của bạn</p>
        <strong>{{ stats.totalTours }}</strong>
        <small>{{ stats.approvedTours }} đã duyệt · {{ stats.pendingTours }} chờ duyệt</small>
      </article>
      <article class="stat-card">
        <p>Địa điểm của bạn</p>
        <strong>{{ stats.totalLocations }}</strong>
        <small>{{ stats.approvedLocations }} đã duyệt · {{ stats.pendingLocations }} chờ duyệt</small>
      </article>
      <article class="stat-card stat-card--warning">
        <p>Mục bị từ chối</p>
        <strong>{{ stats.rejectedTours + stats.rejectedLocations }}</strong>
        <small>Cần cập nhật để gửi lại</small>
      </article>
      <article class="stat-card stat-card--accent">
        <p>Tỷ lệ duyệt</p>
        <strong>{{ approvalRate }}%</strong>
        <small>Dựa trên tour và địa điểm của đối tác</small>
      </article>
    </section>

    <section class="quick-grid">
      <article class="quick-card">
        <h3>Quản lý tour</h3>
        <p>Tạo, chỉnh sửa, xóa và gửi duyệt tour cho hệ thống.</p>
        <button type="button" @click="$router.push('/doi-tac/quan-ly-tour')">
          Mở quản lý tour
        </button>
      </article>
      <article class="quick-card">
        <h3>Quản lý địa điểm</h3>
        <p>Quản lý địa điểm riêng của đối tác và theo dõi trạng thái duyệt.</p>
        <button type="button" @click="$router.push('/doi-tac/quan-ly-dia-diem')">
          Mở quản lý địa điểm
        </button>
      </article>
    </section>
  </PartnerShell>
</template>

<script>
import PartnerShell from "./layout/PartnerShell.vue";
import {
  fetchPartnerLocations,
  fetchPartnerSession,
  fetchPartnerTours,
} from "./shared/partnerApi";

export default {
  name: "PartnerDashboard",
  components: {
    PartnerShell,
  },
  data() {
    return {
      loading: false,
      errorMessage: "",
      partner: null,
      tours: [],
      locations: [],
    };
  },
  computed: {
    stats() {
      const counters = {
        totalTours: this.tours.length,
        totalLocations: this.locations.length,
        approvedTours: 0,
        pendingTours: 0,
        rejectedTours: 0,
        approvedLocations: 0,
        pendingLocations: 0,
        rejectedLocations: 0,
      };

      this.tours.forEach((tour) => {
        const status = String(tour?.trang_thai_duyet || "").toLowerCase();
        if (status === "approved") counters.approvedTours += 1;
        if (status === "pending_approval") counters.pendingTours += 1;
        if (status === "rejected") counters.rejectedTours += 1;
      });

      this.locations.forEach((location) => {
        const status = String(location?.trang_thai_duyet || "").toLowerCase();
        if (status === "approved") counters.approvedLocations += 1;
        if (status === "pending_approval") counters.pendingLocations += 1;
        if (status === "rejected") counters.rejectedLocations += 1;
      });

      return counters;
    },
    approvalRate() {
      const approved = this.stats.approvedTours + this.stats.approvedLocations;
      const total = this.stats.totalTours + this.stats.totalLocations;
      if (!total) return 0;
      return Math.round((approved / total) * 100);
    },
  },
  methods: {
    async loadDashboard() {
      this.loading = true;
      this.errorMessage = "";

      try {
        const [partner, tours, locations] = await Promise.all([
          fetchPartnerSession(),
          fetchPartnerTours(),
          fetchPartnerLocations(),
        ]);

        this.partner = partner;
        this.tours = tours;
        this.locations = locations;
      } catch (error) {
        this.errorMessage = error?.message || "Không thể tải dữ liệu tổng quan đối tác.";
        if (
          this.errorMessage.toLowerCase().includes("đăng nhập")
          || this.errorMessage.toLowerCase().includes("không hợp lệ")
        ) {
          localStorage.removeItem("token");
          localStorage.removeItem("user");
          localStorage.removeItem("auth_type");
          window.dispatchEvent(new Event("storage"));
          this.$router.replace("/doi-tac/dang-nhap");
        }
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.loadDashboard();
  },
};
</script>

<style scoped>
.refresh-btn {
  min-height: 2.75rem;
  border-radius: 0.75rem;
  border: 1px solid #bfdbfe;
  background: #eff6ff;
  color: #1d4ed8;
  font-weight: 700;
  padding: 0 1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.refresh-btn:hover {
  background: #dbeafe;
  transform: translateY(-1px);
}

.alert-box {
  border-radius: 1rem;
  border: 1px solid #fecaca;
  background: #fef2f2;
  color: #b91c1c;
  padding: 1rem 1.5rem;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 1.5rem;
}

.stat-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
  transition: box-shadow 0.2s ease;
}

.stat-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.stat-card p {
  margin: 0;
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.stat-card strong {
  margin: 0.5rem 0;
  display: block;
  font-size: 2.25rem;
  color: #0f172a;
  font-weight: 900;
  line-height: 1.1;
}

.stat-card small {
  color: #64748b;
  font-size: 0.875rem;
}

.stat-card--warning {
  border-color: #fce7f3;
  background: linear-gradient(135deg, #fdf2f8, #fbcfe8);
}

.stat-card--accent {
  border-color: #dbeafe;
  background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
}

.quick-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.5rem;
}

.quick-card {
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  background: #ffffff;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
}

.quick-card h3 {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
  color: #0f172a;
  font-weight: 800;
}

.quick-card p {
  margin: 0 0 1.5rem;
  color: #64748b;
  font-size: 1rem;
  line-height: 1.5;
  flex: 1;
}

.quick-card button {
  min-height: 2.75rem;
  border: 0;
  border-radius: 0.75rem;
  background: #1d4ed8;
  color: #fff;
  font-weight: 700;
  padding: 0 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
  align-self: flex-start;
}

.quick-card button:hover {
  background: #1e40af;
  transform: translateY(-1px);
}

@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .stats-grid,
  .quick-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
</style>
