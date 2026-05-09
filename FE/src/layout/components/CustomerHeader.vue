<template>
  <header class="customer-app-layout__topbar">
    <router-link class="customer-app-layout__brand" to="/">
      <div class="customer-app-layout__brand-mark">
        <i class="fas fa-plane-departure"></i>
      </div>
      <div class="customer-app-layout__brand-copy">
        <strong>Smart Travel</strong>
        <span>Không gian khách hàng</span>
      </div>
    </router-link>

    <div class="customer-app-layout__actions">
      <router-link class="customer-app-layout__link" to="/">
        <i class="fas fa-house"></i>
        <span>Trang chủ</span>
      </router-link>
      <router-link class="customer-app-layout__link" to="/khach-hang/tour">
        <i class="fas fa-map"></i>
        <span>Đặt tour</span>
      </router-link>
      <router-link class="customer-app-layout__link" to="/khach-hang/lich-su-don-hang">
        <i class="fas fa-receipt"></i>
        <span>Thanh toán</span>
      </router-link>
      <router-link class="customer-app-layout__profile" to="/khach-hang/ho-so">
        <div class="customer-app-layout__avatar">{{ userInitials }}</div>
        <div>
          <strong>{{ userName }}</strong>
          <span>{{ userLabel }}</span>
        </div>
      </router-link>

      <button class="customer-app-layout__logout" type="button" @click="handleLogout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Đăng xuất</span>
      </button>
    </div>
  </header>
</template>

<script>
import { getStoredCustomerId, getStoredUser, logoutCustomer } from "../../components/Shared/customerSession";
import { showConfirm } from "../../services/appDialog";

export default {
  name: "CustomerHeader",
  data() {
    return {
      storageHandler: null,
      userSnapshot: {},
    };
  },
  computed: {
    userName() {
      return this.userSnapshot?.Ho_va_ten || this.userSnapshot?.ho_va_ten || "Tài khoản của tôi";
    },
    userLabel() {
      const maKhachHang = getStoredCustomerId();
      return maKhachHang ? `Mã KH ${maKhachHang}` : "Khách hàng";
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
  },
  mounted() {
    this.refreshSession();
    this.storageHandler = () => this.refreshSession();
    window.addEventListener("storage", this.storageHandler);
  },
  beforeUnmount() {
    if (!this.storageHandler) return;
    window.removeEventListener("storage", this.storageHandler);
  },
  methods: {
    refreshSession() {
      this.userSnapshot = getStoredUser() || {};
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
.customer-app-layout__topbar {
  min-height: 72px;
  padding: 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  position: sticky;
  top: 0;
  z-index: 30;
  border-bottom: 1px solid rgba(219, 234, 254, 0.9);
  background: rgba(248, 251, 255, 0.92);
  backdrop-filter: blur(16px);
}

.customer-app-layout__brand {
  display: inline-flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
}

.customer-app-layout__brand-mark,
.customer-app-layout__avatar {
  width: 42px;
  height: 42px;
  border-radius: 14px;
  display: grid;
  place-items: center;
  color: #fff;
}

.customer-app-layout__brand-mark {
  background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%);
  box-shadow: 0 14px 24px rgba(37, 99, 235, 0.18);
}

.customer-app-layout__brand-copy strong,
.customer-app-layout__profile strong {
  display: block;
  color: #0f172a;
  font-size: 1rem;
  font-weight: 900;
  letter-spacing: -0.03em;
}

.customer-app-layout__brand-copy span,
.customer-app-layout__profile span {
  display: block;
  margin-top: 4px;
  color: #64748b;
  font-size: 0.74rem;
  font-weight: 800;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.customer-app-layout__actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 12px;
  flex-wrap: wrap;
}

.customer-app-layout__link,
.customer-app-layout__profile {
  min-height: 46px;
  padding: 0 16px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  border: 1px solid #dbeafe;
  background: rgba(255, 255, 255, 0.9);
  color: #0f172a;
  text-decoration: none;
  font-weight: 700;
}

.customer-app-layout__profile {
  padding-right: 18px;
}

.customer-app-layout__logout {
  min-height: 46px;
  padding: 0 16px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  border: 1px solid #fee2e2;
  background: rgba(255, 251, 251, 0.9);
  color: #dc2626;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}

.customer-app-layout__logout:hover {
  background: #fef2f2;
  border-color: #fecaca;
  transform: translateY(-1px);
}

.customer-app-layout__avatar {
  border-radius: 50%;
  background: linear-gradient(135deg, #0f766e 0%, #2563eb 100%);
  box-shadow: 0 12px 20px rgba(15, 118, 110, 0.18);
  font-weight: 800;
}

@media (max-width: 1080px) {
  .customer-app-layout__topbar {
    align-items: flex-start;
    flex-direction: column;
    padding: 14px 16px;
  }

  .customer-app-layout__actions {
    width: 100%;
    justify-content: flex-start;
  }
}

@media (max-width: 720px) {
  .customer-app-layout__link {
    flex: 1 1 calc(50% - 8px);
    justify-content: center;
  }

  .customer-app-layout__profile {
    width: 100%;
  }
}
</style>
