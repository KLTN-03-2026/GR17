<template>
  <aside class="profile-panel">
    <div class="profile-panel__card">
      <router-link class="profile-panel__brand" to="/khach-hang/dashboard">
        <div class="profile-panel__brand-mark">
          <i class="fas fa-plane-departure"></i>
        </div>
        <div>
          <strong>Smart Travel</strong>
          <span>Không gian khách hàng</span>
        </div>
      </router-link>

      <div class="profile-panel__identity">
        <div class="profile-panel__avatar">{{ userInitials }}</div>
        <div>
          <strong>{{ userName }}</strong>
          <span>{{ customerLabel }}</span>
        </div>
      </div>

      <router-link class="profile-panel__primary-action" :to="primaryAction.to">
        <i :class="primaryAction.icon"></i>
        <span>{{ primaryAction.text }}</span>
      </router-link>
    </div>

    <nav class="profile-panel__menu">
      <section
        v-for="group in menuGroups"
        :key="group.id"
        class="profile-panel__group"
      >
        <p class="profile-panel__group-label">{{ group.label }}</p>

        <router-link
          v-for="item in group.items"
          :key="item.to"
          class="profile-panel__link"
          :class="{ 'is-active': isItemActive(item) }"
          :to="item.to"
        >
          <i :class="item.icon"></i>
          <span>{{ item.text }}</span>
        </router-link>
      </section>

      <a class="profile-panel__link logout-link" href="#" @click.prevent="handleLogout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Đăng xuất</span>
      </a>
    </nav>
  </aside>
</template>

<script>
import { getStoredUser, getStoredCustomerId, logoutCustomer } from "../Shared/customerSession";
import { showConfirm } from "../../services/appDialog";
import { CUSTOMER_MENU_GROUPS, CUSTOMER_PRIMARY_ACTION } from "./layout/customerNavigation";

function isPathMatch(currentPath, prefix, exact = false) {
  if (!prefix) return false;
  if (exact) return currentPath === prefix;
  if (prefix.endsWith("/")) {
    return currentPath.startsWith(prefix);
  }
  return currentPath === prefix || currentPath.startsWith(`${prefix}/`);
}

export default {
  name: "CustomerSidebar",
  data() {
    return {
      menuGroups: CUSTOMER_MENU_GROUPS,
      primaryAction: CUSTOMER_PRIMARY_ACTION,
    };
  },
  computed: {
    user() {
      return getStoredUser() || {};
    },
    customerId() {
      return getStoredCustomerId();
    },
    userName() {
      return this.user.Ho_va_ten || this.user.ho_va_ten || "Tài khoản của tôi";
    },
    userInitials() {
      return (
        String(this.userName)
          .split(" ")
          .filter(Boolean)
          .slice(0, 2)
          .map((part) => part[0]?.toUpperCase() || "")
          .join("") || "KH"
      );
    },
    customerLabel() {
      return this.customerId ? `Mã khách hàng ${this.customerId}` : "Vui lòng đăng nhập";
    },
  },
  methods: {
    isItemActive(item) {
      const currentPath = this.$route?.path || "";

      if (item.exact) {
        return isPathMatch(currentPath, item.to, true);
      }

      const prefixes = item.prefixes || [item.to];
      return prefixes.some((prefix) => isPathMatch(currentPath, prefix));
    },
    async handleLogout() {
      const isConfirmed = await showConfirm({
        title: "Xác nhận đăng xuất",
        message: "Bạn có chắc chắn muốn đăng xuất?",
        tone: "warning"
      });
      if (isConfirmed) {
        await logoutCustomer();
      }
    },
  },
};
</script>

<style scoped>
.profile-panel {
  padding: 8px 0 0;
  background: transparent;
  min-width: 352px;
  flex-shrink: 0;
  position: sticky;
  top: 18px;
  align-self: flex-start;
}

.profile-panel__card,
.profile-panel__menu {
  background:
    linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(245, 249, 255, 0.98) 100%);
  border: 1px solid rgba(213, 225, 241, 0.92);
  box-shadow: 0 22px 44px rgba(15, 23, 42, 0.07);
}

.profile-panel__card {
  display: grid;
  gap: 22px;
  padding: 24px 24px 26px;
  border-radius: 30px 30px 0 0;
}

.profile-panel__brand {
  display: inline-flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
}

.profile-panel__brand-mark {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);
  color: #fff;
  box-shadow: 0 14px 24px rgba(37, 99, 235, 0.2);
}

.profile-panel__brand strong {
  display: block;
  color: #0f172a;
  font-size: 1.08rem;
  font-weight: 900;
  letter-spacing: -0.03em;
}

.profile-panel__brand span {
  display: block;
  margin-top: 4px;
  color: #64748b;
  font-size: 0.74rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.profile-panel__identity {
  display: flex;
  align-items: center;
  gap: 16px;
}

.profile-panel__avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #ffffff;
  font-weight: 800;
  background: linear-gradient(135deg, #0b6b93 0%, #2563eb 100%);
  box-shadow: 0 12px 20px rgba(11, 107, 147, 0.2);
}

.profile-panel__identity strong {
  display: block;
  color: #0f2b4a;
  font-size: 1.06rem;
  font-weight: 700;
}

.profile-panel__identity span {
  display: block;
  margin-top: 4px;
  color: #6b7c93;
  font-size: 0.85rem;
}

.profile-panel__primary-action {
  min-height: 56px;
  padding: 0 22px;
  border-radius: 18px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  background: linear-gradient(135deg, #0f766e 0%, #0369a1 100%);
  color: #fff;
  text-decoration: none;
  font-weight: 800;
  box-shadow: 0 16px 28px rgba(14, 116, 144, 0.24);
}

.profile-panel__menu {
  display: grid;
  gap: 18px;
  padding: 22px 16px 20px;
  margin-top: 1px;
  border-radius: 0 0 30px 30px;
}

.profile-panel__group {
  display: grid;
  gap: 10px;
}

.profile-panel__group-label {
  padding: 0 16px;
  color: #8b9bb2;
  font-size: 0.72rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.profile-panel__link {
  min-height: 56px;
  padding: 0 18px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 14px;
  color: #5d7089;
  text-decoration: none;
  font-weight: 700;
  line-height: 1.2;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.profile-panel__link i {
  width: 22px;
  text-align: center;
  font-size: 1.08rem;
}

.profile-panel__link:hover {
  background: rgba(14, 116, 144, 0.06);
  color: #0369a1;
  transform: translateX(4px);
}

.profile-panel__link.is-active {
  background: linear-gradient(135deg, #0f766e, #2563eb);
  color: #ffffff;
  box-shadow: 0 10px 24px rgba(37, 99, 235, 0.22);
}

.logout-link:hover {
  background: rgba(239, 68, 68, 0.05);
  color: #ef4444;
}

@media (max-width: 1200px) {
  .profile-panel {
    padding-bottom: 20px;
    position: static;
    min-width: 0;
  }

  .profile-panel__card {
    border-radius: 28px 28px 0 0;
  }

  .profile-panel__menu {
    border-radius: 0 0 28px 28px;
  }

  .profile-panel__menu {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
}

@media (max-width: 860px) {
  .profile-panel__menu {
    grid-template-columns: 1fr;
  }
}
</style>
