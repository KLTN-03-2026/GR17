<template>
  <aside class="admin-sidebar" id="accordionSidebar">
    <button class="admin-sidebar__brand" type="button" @click="goToHome">
      <div class="admin-sidebar__brand-icon">
        <i class="fas fa-plane"></i>
      </div>
      <div class="admin-sidebar__brand-copy">
        <strong>Smart Travel</strong>
        <span>Trung tâm đối tác</span>
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
          <i class="fas fa-user-tie"></i>
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
const PARTNER_MENU_ITEMS = [
  { id: "partner-dashboard", label: "Tổng quan", icon: "fas fa-table-cells-large", to: "/doi-tac/dashboard", exact: true },
  { id: "partner-tour", label: "Quản lý tour", icon: "fas fa-route", to: "/doi-tac/quan-ly-tour", prefixes: ["/doi-tac/quan-ly-tour"] },
  { id: "partner-dia-diem", label: "Quản lý địa điểm", icon: "fas fa-location-dot", to: "/doi-tac/quan-ly-dia-diem", prefixes: ["/doi-tac/quan-ly-dia-diem"] },
  { id: "partner-don-hang", label: "Quản lý đơn hàng", icon: "fas fa-clipboard-list", to: "/doi-tac/don-hang", prefixes: ["/doi-tac/don-hang"] },
  { id: "partner-voucher", label: "Mã giảm giá", icon: "fas fa-ticket-alt", to: "/doi-tac/voucher", prefixes: ["/doi-tac/voucher"] },
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
  name: "PartnerSidebar",
  data() {
    return {
      userSnapshot: {},
      storageHandler: null,
    };
  },
  computed: {
    menuItems() {
      return PARTNER_MENU_ITEMS;
    },
    displayName() {
      return (
        this.userSnapshot?.ten_doi_tac ||
        this.userSnapshot?.ten_nguoi_dai_dien ||
        "Đối tác"
      );
    },
    displayMeta() {
      const maDoiTac = this.userSnapshot?.ma_doi_tac || this.userSnapshot?.Ma_doi_tac;
      if (maDoiTac) return `Mã đối tác: ${maDoiTac}`;
      return this.userSnapshot?.email || "Đối tác";
    },
  },
  methods: {
    refreshSession() {
      try {
        this.userSnapshot = JSON.parse(localStorage.getItem("user") || "null") || {};
      } catch {
        this.userSnapshot = {};
      }
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
      this.$router.push("/doi-tac/dashboard");
    },
  },
  mounted() {
    this.refreshSession();
    this.storageHandler = () => this.refreshSession();
    window.addEventListener("storage", this.storageHandler);
    window.addEventListener("partner-profile-updated", this.storageHandler);
  },
  beforeUnmount() {
    if (!this.storageHandler) return;
    window.removeEventListener("storage", this.storageHandler);
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
