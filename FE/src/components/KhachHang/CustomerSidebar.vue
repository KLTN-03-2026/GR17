<template>
  <aside class="profile-panel">
    <div class="profile-panel__card">
      <div class="profile-panel__identity">
        <div class="profile-panel__avatar">{{ userInitials }}</div>
        <div>
          <strong>{{ userName }}</strong>
          <span>{{ customerLabel }}</span>
        </div>
      </div>
    </div>

    <nav class="profile-panel__menu">
      <router-link 
        v-for="item in menuItems" 
        :key="item.to"
        class="profile-panel__link" 
        :class="{ 'is-active': $route.path === item.to }"
        :to="item.to"
      >
        <i :class="item.icon"></i>
        <span>{{ item.text }}</span>
      </router-link>

      <a class="profile-panel__link logout-link" href="#" @click.prevent="handleLogout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Đăng xuất</span>
      </a>
    </nav>
  </aside>
</template>

<script>
import { getStoredUser, getStoredCustomerId } from "../Shared/customerSession";

export default {
  name: "CustomerSidebar",
  data() {
    return {
      menuItems: [
        { to: "/khach-hang/nhom-hanh-trinh", icon: "fas fa-users", text: "Nhóm hành trình" },
        { to: "/khach-hang/ke-hoach", icon: "fas fa-calendar-days", text: "Danh sách kế hoạch" },
        { to: "/khach-hang/hanh-trinh-da-luu", icon: "fas fa-bookmark", text: "Hành trình đã lưu" },
        { to: "/khach-hang/len-ke-hoach-ai", icon: "fas fa-magic", text: "Lên kế hoạch AI" },
        { to: "/khach-hang/danh-gia", icon: "fas fa-star", text: "Bảng đánh giá" },
        { to: "/khach-hang/dia-diem", icon: "fas fa-map-location-dot", text: "Địa điểm" },
        { to: "/khach-hang/tour", icon: "fas fa-map", text: "Danh sách tour" },
        { to: "/khach-hang/lich-su-don-hang", icon: "fas fa-receipt", text: "Lịch sử đơn hàng" },
        { to: "/khach-hang/danh-sach-yeu-thich", icon: "fas fa-heart", text: "Danh sách yêu thích" },
        { to: "/khach-hang/ho-so", icon: "fas fa-user-cog", text: "Quản lý tài khoản" },
      ]
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
    }
  },
  methods: {
    handleLogout() {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      this.$router.push("/dang-nhap");
    }
  }
};
</script>

<style scoped>
.profile-panel {
  padding: 20px 0 0;
  background: rgba(255, 255, 255, 0.82);
  border-right: 1px solid #e6edf8;
  min-width: 264px;
  flex-shrink: 0;
  height: 100%;
}

.profile-panel__card {
  display: grid;
  gap: 18px;
  padding: 18px 18px 24px 12px;
  border-bottom: 1px solid #e9eef7;
}

.profile-panel__identity {
  display: flex;
  align-items: center;
  gap: 14px;
}

.profile-panel__avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #ffffff;
  font-weight: 800;
  background: linear-gradient(135deg, #0b6b93 0%, #69b8ff 100%);
  box-shadow: 0 8px 16px rgba(11, 107, 147, 0.2);
}

.profile-panel__identity strong {
  display: block;
  color: #0f2b4a;
  font-size: 1rem;
  font-weight: 700;
}

.profile-panel__identity span {
  display: block;
  margin-top: 4px;
  color: #6b7c93;
  font-size: 0.85rem;
}

.profile-panel__menu {
  display: grid;
  gap: 8px;
  padding: 18px 10px 0 8px;
}

.profile-panel__link {
  min-height: 52px;
  padding: 0 16px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  gap: 12px;
  color: #5d7089;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.profile-panel__link i {
  width: 20px;
  text-align: center;
  font-size: 1.1rem;
}

.profile-panel__link:hover {
  background: rgba(40, 84, 255, 0.05);
  color: #2854ff;
  transform: translateX(4px);
}

.profile-panel__link.is-active {
  background: linear-gradient(135deg, #2854ff, #4f25f4);
  color: #ffffff;
  box-shadow: 0 8px 20px rgba(40, 84, 255, 0.3);
}

.logout-link:hover {
  background: rgba(239, 68, 68, 0.05);
  color: #ef4444;
}

@media (max-width: 1200px) {
  .profile-panel {
    border-right: 0;
    border-bottom: 1px solid #e4ecf8;
    padding-bottom: 20px;
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

