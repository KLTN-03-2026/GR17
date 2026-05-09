<template>
  <nav class="admin-header">
    <div class="admin-header__left">
      <button
        id="sidebarToggleTop"
        class="admin-header__toggle"
        type="button"
        @click="toggleSidebar"
      >
        <i class="fa fa-bars"></i>
      </button>
      <h2 class="admin-header__brand-sm">Quản trị hệ thống</h2>
    </div>

    <ul class="admin-header__nav">
      <li class="admin-header__item">
        <button
          class="admin-header__icon-button"
          type="button"
          @click="toggleDropdown('notifications')"
          :aria-expanded="dropdownState.notifications"
        >
          <i class="far fa-bell"></i>
          <span class="notification-badge" v-if="hasNotifications"></span>
        </button>
        <div class="admin-dropdown admin-dropdown--panel" :class="{ 'is-open': dropdownState.notifications }">
          <div class="admin-dropdown__header">
            <h6 class="admin-dropdown__title">Thông báo</h6>
            <button class="text-blue-link" v-if="hasNotifications" @click="clearNotifications">Đánh dấu đã đọc</button>
          </div>
          <div class="admin-dropdown__body">
            <template v-if="hasNotifications">
              <a v-for="note in notifications" :key="note.id" class="admin-dropdown__item" href="#">
                <div class="admin-dropdown__icon" :class="'admin-dropdown__icon--' + (note.type === 'error' ? 'danger' : note.type)">
                  <i :class="getNotificationIcon(note.type)"></i>
                </div>
                <div class="admin-dropdown__content">
                  <span class="admin-dropdown__meta">{{ formatTime(note.time) }}</span>
                  <span class="admin-dropdown__text">{{ note.message }}</span>
                </div>
              </a>
            </template>
            <div v-else class="admin-dropdown__empty">
              <i class="fas fa-box-open"></i>
              <p>Không có thông báo mới</p>
            </div>
          </div>
          <a class="admin-dropdown__footer" href="#">Xem tất cả nhật ký <i class="fas fa-arrow-right"></i></a>
        </div>
      </li>

      <li class="admin-header__divider"></li>

      <li class="admin-header__item">
        <button
          class="admin-header__profile"
          type="button"
          @click="toggleDropdown('user')"
          :aria-expanded="dropdownState.user"
        >
          <img class="admin-header__profile-image" :src="profileMain" alt="Hồ sơ">
          <i class="fas fa-chevron-down admin-header__profile-arrow"></i>
        </button>
        <div class="admin-dropdown admin-dropdown--menu" :class="{ 'is-open': dropdownState.user }">
          <div class="admin-dropdown__user-info">
            <strong>{{ displayName }}</strong>
            <span>{{ displaySubtitle }}</span>
          </div>
          <div class="admin-dropdown__separator"></div>

          <router-link class="admin-dropdown__menu-item" to="/admin/ho-so">
            <i class="far fa-user"></i>
            <span>Hồ sơ cá nhân</span>
          </router-link>

          <router-link class="admin-dropdown__menu-item" to="/admin/tai-khoan">
            <i class="fas fa-user-shield"></i>
            <span>Quản lý tài khoản admin</span>
          </router-link>

          <div class="admin-dropdown__separator"></div>
          <a class="admin-dropdown__menu-item danger-item" href="#" @click.prevent="logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Đăng xuất</span>
          </a>
        </div>
      </li>
    </ul>
  </nav>
</template>

<script>
import profileMain from "../../assets/startbootstrap-sb-admin-2-gh-pages/img/undraw_profile.svg";
import { globalNotifications } from "../../utils/notifications";
import { authService } from "../../services/authService";
import { showConfirm } from "../../services/appDialog";

export default {
  name: "AdminHeader",
  data() {
    return {
      profileMain,
      userSnapshot: {},
      dropdownState: {
        notifications: false,
        user: false,
      },
      handleOutsideClick: null,
      refreshSessionHandler: null,
      timer: null,
      now: new Date(),
    };
  },
  computed: {
    notifications() {
      return globalNotifications.items;
    },
    hasNotifications() {
      return this.notifications.length > 0;
    },
    displayName() {
      return this.userSnapshot?.Ho_va_ten || "Tài khoản Admin";
    },
    displaySubtitle() {
      return this.userSnapshot?.Email || "admin@smarttravel.com";
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
    toggleDropdown(key) {
      Object.keys(this.dropdownState).forEach((item) => {
        if (item !== key) this.dropdownState[item] = false;
      });
      this.dropdownState[key] = !this.dropdownState[key];
    },
    toggleSidebar() {
      document.body.classList.toggle("sidebar-toggled");
      document.getElementById("accordionSidebar")?.classList.toggle("toggled");
    },
    async logout() {
      const isConfirmed = await showConfirm({
        title: "Xác nhận đăng xuất",
        message: "Bạn có chắc chắn muốn đăng xuất khỏi hệ thống quản trị?",
        tone: "warning"
      });
      if (isConfirmed) {
        this.dropdownState.user = false;
        await authService.logoutAdmin();
      }
    },
    clearNotifications() {
      globalNotifications.clear();
      this.dropdownState.notifications = false;
    },
    getNotificationIcon(type) {
      if (type === "success") return "fas fa-check";
      if (type === "error" || type === "danger") return "fas fa-xmark";
      if (type === "primary") return "fas fa-info";
      return "fas fa-bell";
    },
    formatTime(date) {
      if (!date) return "Vừa xong";
      const nowTime = this.now.getTime();
      const diff = Math.floor((nowTime - new Date(date).getTime()) / 60000);
      if (diff < 1) return "Vừa xong";
      if (diff < 60) return `${diff} phút trước`;
      if (diff < 1440) return `${Math.floor(diff / 60)} giờ trước`;
      return `${Math.floor(diff / 1440)} ngày trước`;
    },
  },
  mounted() {
    this.refreshSession();

    this.handleOutsideClick = (event) => {
      if (!event.target.closest(".admin-header__nav")) {
        Object.keys(this.dropdownState).forEach((key) => {
          this.dropdownState[key] = false;
        });
      }
    };

    this.refreshSessionHandler = () => this.refreshSession();

    document.addEventListener("click", this.handleOutsideClick);
    window.addEventListener("storage", this.refreshSessionHandler);
    window.addEventListener("admin-profile-updated", this.refreshSessionHandler);

    this.timer = setInterval(() => {
      this.now = new Date();
    }, 60000);

    if (globalNotifications.items.length === 0) {
      globalNotifications.add("Khởi tạo hệ thống quản trị thành công", "success");
    }
  },
  beforeUnmount() {
    if (this.handleOutsideClick) {
      document.removeEventListener("click", this.handleOutsideClick);
    }
    if (this.refreshSessionHandler) {
      window.removeEventListener("storage", this.refreshSessionHandler);
      window.removeEventListener("admin-profile-updated", this.refreshSessionHandler);
    }
    if (this.timer) {
      clearInterval(this.timer);
    }
  },
};
</script>

<style scoped>
.admin-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 32px;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid #f1f5f9;
  position: sticky;
  top: 0;
  z-index: 30;
  height: 77px;
}

.admin-header__left {
  display: flex;
  align-items: center;
  gap: 20px;
  flex: 1;
}

.admin-header__toggle {
  display: none;
  border: none;
  background: transparent;
  color: #64748b;
  font-size: 1.25rem;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: background 0.2s ease;
}

.admin-header__toggle:hover {
  background: #f1f5f9;
  color: #0f172a;
}

.admin-header__brand-sm {
  margin: 0;
  font-size: 1.15rem;
  color: #0f172a;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.admin-header__nav {
  display: flex;
  align-items: center;
  gap: 12px;
  list-style: none;
  margin: 0;
  padding: 0;
}

.admin-header__item {
  position: relative;
}

.admin-header__icon-button {
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: #f8fafc;
  color: #64748b;
  cursor: pointer;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  transition: all 0.2s ease;
  font-size: 1.25rem;
  position: relative;
  border: 1px solid #f1f5f9;
}

.notification-badge {
  position: absolute;
  top: 10px;
  right: 12px;
  width: 8px;
  height: 8px;
  background-color: #ef4444;
  border-radius: 50%;
  border: 2px solid #ffffff;
  animation: pop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes pop {
  0% { transform: scale(0); }
  100% { transform: scale(1); }
}

.admin-header__icon-button:hover {
  background-color: #f1f5f9;
  color: #2563eb;
}

.admin-header__divider {
  width: 1px;
  height: 24px;
  background-color: #e2e8f0;
  margin: 0 8px;
}

.admin-header__profile {
  display: flex;
  align-items: center;
  gap: 8px;
  border: none;
  background: transparent;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 999px;
  transition: background-color 0.2s ease;
}

.admin-header__profile:hover {
  background-color: #f8fafc;
}

.admin-header__profile-arrow {
  color: #94a3b8;
  font-size: 0.75rem;
}

.admin-header__profile-image {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e2e8f0;
}

.admin-dropdown {
  position: absolute;
  top: calc(100% + 12px);
  right: 0;
  display: none;
  background-color: #ffffff;
  border: 1px solid #f1f5f9;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
  overflow: hidden;
  z-index: 50;
  animation: slideDown 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.admin-dropdown.is-open {
  display: block;
}

.admin-dropdown--panel {
  width: min(90vw, 380px);
}

.admin-dropdown--menu {
  width: 260px;
}

.admin-dropdown__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  background: #f8fafc;
}

.admin-dropdown__title {
  margin: 0;
  color: #0f172a;
  font-size: 0.95rem;
  font-weight: 800;
}

.text-blue-link {
  color: #2563eb;
  font-size: 0.8rem;
  font-weight: 600;
  background: none;
  border: none;
  cursor: pointer;
}

.text-blue-link:hover {
  text-decoration: underline;
}

.admin-dropdown__body {
  max-height: 300px;
  overflow-y: auto;
}

.admin-dropdown__empty {
  padding: 40px 20px;
  text-align: center;
  color: #94a3b8;
}

.admin-dropdown__empty i {
  font-size: 2rem;
  margin-bottom: 10px;
  color: #e2e8f0;
}

.admin-dropdown__empty p {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 500;
}

.admin-dropdown__item {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 16px;
  color: #475569;
  text-decoration: none;
  border-bottom: 1px solid #f8fafc;
  transition: background 0.2s ease;
}

.admin-dropdown__item:hover {
  background-color: #f8fafc;
}

.admin-dropdown__icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #ffffff;
  flex-shrink: 0;
  font-size: 1rem;
}

.admin-dropdown__icon--primary {
  background-color: #1d4ed8;
}

.admin-dropdown__icon--success {
  background-color: #10b981;
}

.admin-dropdown__icon--danger {
  background-color: #ef4444;
}

.admin-dropdown__content {
  display: flex;
  flex-direction: column;
}

.admin-dropdown__meta {
  font-size: 0.75rem;
  color: #94a3b8;
  font-weight: 600;
  margin-bottom: 4px;
}

.admin-dropdown__text {
  font-size: 0.9rem;
  color: #334155;
  line-height: 1.5;
  font-weight: 500;
}

.admin-dropdown__footer {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px;
  background: #f8fafc;
  color: #2563eb;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 700;
  transition: background 0.2s ease;
}

.admin-dropdown__footer:hover {
  background-color: #f1f5f9;
}

.admin-dropdown__user-info {
  padding: 16px;
  display: flex;
  flex-direction: column;
}

.admin-dropdown__user-info strong {
  color: #0f172a;
  font-size: 1rem;
}

.admin-dropdown__user-info span {
  color: #64748b;
  font-size: 0.8rem;
  margin-top: 4px;
}

.admin-dropdown__separator {
  height: 1px;
  background-color: #f1f5f9;
}

.admin-dropdown__menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  color: #475569;
  text-decoration: none;
  font-size: 0.95rem;
  font-weight: 500;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.admin-dropdown__menu-item i {
  color: #94a3b8;
  width: 20px;
  text-align: center;
}

.admin-dropdown__menu-item:hover {
  background-color: #f8fafc;
  color: #2563eb;
}

.admin-dropdown__menu-item:hover i {
  color: #2563eb;
}

.danger-item:hover {
  color: #e11d48 !important;
  background: #fff1f2 !important;
}

.danger-item:hover i {
  color: #e11d48 !important;
}

@media (max-width: 768px) {
  .admin-header {
    padding: 12px 16px;
  }

  .admin-header__toggle {
    display: inline-flex;
  }

  .admin-header__divider {
    display: none;
  }
}
</style>
