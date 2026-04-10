<template>
  <aside class="admin-sidebar" id="accordionSidebar">
    <button class="admin-sidebar__brand" type="button" @click="goToHome">
      <div class="admin-sidebar__brand-icon">
        <i class="fas fa-plane"></i>
      </div>
      <div class="admin-sidebar__brand-copy">
        <strong>Smart Travel</strong>
        <span>{{ brandSubtitle }}</span>
      </div>
    </button>

    <div class="admin-sidebar__divider"></div>

    <nav class="admin-sidebar__nav">
      <button
        v-for="item in menuItems"
        :key="item.id"
        class="admin-sidebar__link"
        :class="{ 'is-active': isItemActive(item) }"
        type="button"
        @click="navigate(item)"
      >
        <div class="admin-sidebar__link-icon">
          <i :class="item.icon"></i>
        </div>
        <span>{{ item.label }}</span>
      </button>
    </nav>

    <div class="admin-sidebar__footer">
      <div class="admin-sidebar__user">
        <div class="admin-sidebar__avatar">
          <i :class="footerAvatarIcon"></i>
        </div>
        <div class="admin-sidebar__user-info">
          <strong>{{ displayName }}</strong>
          <span>{{ displayMeta }}</span>
        </div>
      </div>
    </div>
  </aside>
</template>

<script>
const ADMIN_MENU_ITEMS = [
  { id: "admin-dashboard", label: "Tổng quan", icon: "fas fa-table-cells-large", to: "/dashboard", exact: true },
  { id: "admin-thong-ke", label: "Quản lý thống kê", icon: "fas fa-chart-pie", to: "/admin/thong-ke", prefixes: ["/admin/thong-ke"] },
  { id: "admin-customers", label: "Khách hàng", icon: "fas fa-users", to: "/admin/customers", prefixes: ["/admin/customers"] },
  { id: "admin-doi-tac", label: "Quản lý đối tác", icon: "fas fa-handshake", to: "/admin/doi-tac", prefixes: ["/admin/doi-tac"] },
  { id: "admin-doi-soat", label: "Đối soát doanh thu", icon: "fas fa-sack-dollar", to: "/admin/doi-soat", prefixes: ["/admin/doi-soat"] },
  { id: "admin-reviews", label: "Đánh giá", icon: "fas fa-star", to: "/admin/reviews", prefixes: ["/admin/reviews"] },
  { id: "admin-dia-diem", label: "Quản lý địa điểm", icon: "fas fa-location-dot", to: "/admin/quan-ly-dia-diem", prefixes: ["/admin/quan-ly-dia-diem"] },
  { id: "admin-tag", label: "Quản lý tag", icon: "fas fa-tags", to: "/admin/quan-ly-tag", prefixes: ["/admin/quan-ly-tag"] },
  { id: "admin-tour", label: "Quản lý tour", icon: "fas fa-compass", to: "/admin/tour", prefixes: ["/admin/tour", "/tour-management"] },
  { id: "admin-tour-khoi-hanh", label: "Tour khởi hành", icon: "fas fa-calendar-check", to: "/admin/tour-khoi-hanh", prefixes: ["/admin/tour-khoi-hanh"] },
  { id: "admin-day-config", label: "Cấu hình ngày", icon: "fas fa-calendar-days", to: "/admin/day-config", prefixes: ["/admin/day-config"] },
  { id: "admin-ai-config", label: "Cấu hình AI", icon: "fas fa-robot", to: "/admin/cau-hinh-ai", prefixes: ["/admin/cau-hinh-ai"] },
  { id: "admin-hoa-don", label: "Quản lý hóa đơn", icon: "fas fa-file-invoice-dollar", to: "/admin/hoa-don", prefixes: ["/admin/hoa-don"] },
  { id: "admin-phan-quyen", label: "Phân quyền", icon: "fas fa-shield-halved", to: "/admin/phan-quyen", prefixes: ["/admin/phan-quyen"] },
  { id: "admin-tai-khoan", label: "Tài khoản admin", icon: "fas fa-user-shield", to: "/admin/tai-khoan", prefixes: ["/admin/tai-khoan"] },
  { id: "admin-ho-so", label: "Hồ sơ cá nhân", icon: "fas fa-id-card", to: "/admin/ho-so", prefixes: ["/admin/ho-so"] },
];

const PARTNER_MENU_ITEMS = [
  { id: "partner-dashboard", label: "Tổng quan", icon: "fas fa-table-cells-large", to: "/doi-tac/dashboard", exact: true },
  { id: "partner-tour", label: "Quản lý tour", icon: "fas fa-route", to: "/doi-tac/quan-ly-tour", prefixes: ["/doi-tac/quan-ly-tour"] },
  { id: "partner-dia-diem", label: "Quản lý địa điểm", icon: "fas fa-location-dot", to: "/doi-tac/quan-ly-dia-diem", prefixes: ["/doi-tac/quan-ly-dia-diem"] },
  { id: "partner-don-hang", label: "Quản lý đơn hàng", icon: "fas fa-clipboard-list", to: "/doi-tac/don-hang", prefixes: ["/doi-tac/don-hang"] },
  { id: "partner-doi-soat", label: "Đối soát doanh thu", icon: "fas fa-sack-dollar", to: "/doi-tac/doanh-thu", prefixes: ["/doi-tac/doanh-thu"] },
  {
    id: "partner-account",
    label: "Thông tin tài khoản",
    icon: "fas fa-user-circle",
    to: "/doi-tac/thong-tin-tai-khoan",
    prefixes: ["/doi-tac/thong-tin-tai-khoan"],
  },
];

function isPathMatch(currentPath, prefix, exact = false) {
  if (!prefix) return false;
  if (exact) return currentPath === prefix;
  if (prefix.endsWith("/")) {
    return currentPath.startsWith(prefix);
  }
  return currentPath === prefix || currentPath.startsWith(`${prefix}/`);
}

export default {
  name: "Sidebar",
  data() {
    return {
      authType: "",
      userSnapshot: {},
      storageHandler: null,
    };
  },
  computed: {
    isPartner() {
      return this.authType === "partner";
    },
    menuItems() {
      return this.isPartner ? PARTNER_MENU_ITEMS : ADMIN_MENU_ITEMS;
    },
    brandSubtitle() {
      return this.isPartner ? "Trung tâm đối tác" : "Bảng quản trị";
    },
    displayName() {
      if (this.isPartner) {
        return (
          this.userSnapshot?.ten_doi_tac
          || this.userSnapshot?.ten_nguoi_dai_dien
          || "Đối tác"
        );
      }
      return this.userSnapshot?.Ho_va_ten || "Tài khoản admin";
    },
    displayMeta() {
      if (this.isPartner) {
        const maDoiTac = this.userSnapshot?.ma_doi_tac || this.userSnapshot?.Ma_doi_tac;
        if (maDoiTac) return `Mã đối tác: ${maDoiTac}`;
        return this.userSnapshot?.email || "Đối tác";
      }
      return this.userSnapshot?.Email || "Quản trị viên";
    },
    footerAvatarIcon() {
      return this.isPartner ? "fas fa-user-tie" : "fas fa-user-shield";
    },
  },
  methods: {
    inferAuthType(user) {
      const storedType = String(localStorage.getItem("auth_type") || "").toLowerCase().trim();
      if (storedType) return storedType;
      if (user?.Ma_doi_tac || user?.ma_doi_tac) return "partner";
      if (user?.Ma_quan_tri || user?.ma_quan_tri || user?.Vai_tro === "admin") return "admin";
      if (user?.Ma_khach_hang || user?.ma_khach_hang) return "customer";
      return "";
    },
    refreshSession() {
      let nextUser = {};
      try {
        nextUser = JSON.parse(localStorage.getItem("user") || "null") || {};
      } catch {
        nextUser = {};
      }
      this.userSnapshot = nextUser;
      this.authType = this.inferAuthType(nextUser);
    },
    resolveItemPath(item) {
      return item?.to || "/";
    },
    navigate(item) {
      const target = this.resolveItemPath(item);
      if (!target || this.$route.path === target) return;
      this.$router.push(target);
    },
    isItemActive(item) {
      const currentPath = this.$route?.path || "";

      if (item.exact) {
        return isPathMatch(currentPath, item.to, true);
      }

      const prefixes = item.prefixes || [item.to];
      return prefixes.some((prefix) => isPathMatch(currentPath, prefix));
    },
    goToHome() {
      this.$router.push("/");
    },
  },
  mounted() {
    this.refreshSession();
    this.storageHandler = () => this.refreshSession();
    window.addEventListener("storage", this.storageHandler);
    window.addEventListener("admin-profile-updated", this.storageHandler);
    window.addEventListener("partner-profile-updated", this.storageHandler);
  },
  beforeUnmount() {
    if (!this.storageHandler) return;
    window.removeEventListener("storage", this.storageHandler);
    window.removeEventListener("admin-profile-updated", this.storageHandler);
    window.removeEventListener("partner-profile-updated", this.storageHandler);
  },
};
</script>

<style scoped>
.admin-sidebar {
  width: 280px;
  min-height: 100vh;
  height: 100vh;
  display: flex;
  flex-direction: column;
  align-self: flex-start;
  position: sticky;
  top: 0;
  background: #ffffff;
  color: #1e293b;
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), width 0.3s ease;
  border-right: 1px solid #f1f5f9;
  z-index: 40;
}

.admin-sidebar__brand {
  border: 0;
  background: transparent;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 24px 20px;
  color: #0f172a;
  text-decoration: none;
  transition: opacity 0.2s ease;
  text-align: left;
}

.admin-sidebar__brand:hover {
  opacity: 0.9;
}

.admin-sidebar__brand-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
  color: white;
  display: grid;
  place-items: center;
  font-size: 1.2rem;
  box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2);
}

.admin-sidebar__brand-copy {
  display: flex;
  flex-direction: column;
}

.admin-sidebar__brand-copy strong {
  font-size: 1.25rem;
  line-height: 1.1;
  font-weight: 900;
  letter-spacing: -0.5px;
}

.admin-sidebar__brand-copy span {
  color: #64748b;
  margin-top: 4px;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.admin-sidebar__divider {
  height: 1px;
  margin: 0 20px 16px;
  background: #f1f5f9;
}

.admin-sidebar__nav {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 0 16px;
  flex: 1;
  overflow-y: auto;
}

.admin-sidebar__nav::-webkit-scrollbar {
  width: 4px;
}

.admin-sidebar__nav::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 4px;
}

.admin-sidebar__link {
  border: 0;
  background: transparent;
  display: flex;
  align-items: center;
  gap: 16px;
  width: 100%;
  padding: 16px 20px;
  border-radius: 16px;
  color: #64748b;
  text-decoration: none;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  font-size: 0.95rem;
  font-weight: 700;
  text-align: left;
}

.admin-sidebar__link-icon {
  width: 24px;
  display: flex;
  justify-content: center;
  font-size: 1.15rem;
  color: #94a3b8;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.admin-sidebar__link:hover {
  background: #f8fafc;
  color: #0f172a;
  transform: translateX(4px);
}

.admin-sidebar__link:hover .admin-sidebar__link-icon {
  color: #3b82f6;
}

.admin-sidebar__link:active {
  transform: scale(0.96) translateX(2px);
}

.admin-sidebar__link.is-active {
  background: #f4f7fc;
  color: #1e293b;
}

.admin-sidebar__link.is-active .admin-sidebar__link-icon {
  color: #3b82f6;
}

.admin-sidebar__footer {
  padding: 20px 16px 24px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, #ffffff 20%);
}

.admin-sidebar__user {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 16px;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  transition: all 0.2s ease;
}

.admin-sidebar__avatar {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #93c5fd 0%, #3b82f6 100%);
  color: #ffffff;
  font-size: 1.1rem;
}

.admin-sidebar__user-info {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.admin-sidebar__user-info strong {
  color: #0f172a;
  font-size: 0.9rem;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.admin-sidebar__user-info span {
  color: #64748b;
  font-size: 0.75rem;
  margin-top: 2px;
}

@media (max-width: 768px) {
  .admin-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    z-index: 50;
    height: 100vh;
    transform: translateX(-100%);
    box-shadow: 20px 0 40px rgba(0, 0, 0, 0.1);
  }

  body.sidebar-toggled .admin-sidebar {
    transform: translateX(0);
  }
}
</style>
