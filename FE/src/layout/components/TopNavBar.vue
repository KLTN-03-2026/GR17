<template>
  <nav ref="navRoot" class="topnav" :class="{ 'is-hidden': !showNavbar, 'is-scrolled': isScrolled }">
    <div class="topnav-container">
      <router-link to="/" class="topnav-brand-link">
        <span class="topnav-brand-mark">
          <i class="fas fa-plane"></i>
        </span>
        <span class="topnav-brand-copy">
          <strong>Smart Travel <span class="text-blue">AI</span></strong>
          <small>{{ copy.brandTagline }}</small>
        </span>
      </router-link>

      <button class="topnav-toggler" type="button" @click="toggleNav">
        <i class="fas fa-bars"></i>
      </button>

      <ul class="topnav-menu" :class="{ active: isNavOpen }">
        <li class="topnav-item">
          <router-link to="/" class="topnav-link">{{ copy.nav.home }}</router-link>
        </li>
        <li class="topnav-item">
          <router-link to="/khach-hang/ke-hoach" class="topnav-link">{{ copy.nav.plan }}</router-link>
        </li>
        <li class="topnav-item">
          <router-link to="/tour-management" class="topnav-link">{{ copy.nav.tours }}</router-link>
        </li>
        <li class="topnav-item">
          <router-link to="/khach-hang/dia-diem" class="topnav-link">{{ copy.nav.destinations }}</router-link>
        </li>

        <template v-if="!isAuthenticated">
          <li class="topnav-item">
            <router-link to="/dang-nhap" class="topnav-link">{{ copy.nav.login }}</router-link>
          </li>
          <li class="topnav-item">
            <router-link to="/dang-ky" class="topnav-link topnav-link-btn">{{ copy.nav.register }}</router-link>
          </li>
        </template>

        <li v-else class="topnav-item topnav-item--profile">
          <button
            type="button"
            class="account-trigger"
            :aria-expanded="isAccountOpen"
            @click.stop="toggleAccountMenu"
          >
            <span class="account-trigger__avatar">{{ avatarText }}</span>
            <span class="account-trigger__copy">
              <strong>{{ displayName }}</strong>
              <small>{{ displaySubtitle }}</small>
            </span>
            <i class="fas fa-chevron-down" :class="{ 'is-open': isAccountOpen }"></i>
          </button>

          <div class="account-dropdown" :class="{ 'is-open': isAccountOpen }">
            <div class="account-summary">
              <div class="account-summary__avatar">{{ avatarText }}</div>
              <div class="account-summary__copy">
                <strong>{{ displayName }}</strong>
                <span>{{ displaySubtitle }}</span>
              </div>
            </div>

            <button type="button" class="account-dropdown__item" @click="goToProfile">
              <span class="account-dropdown__item-icon">
                <i class="fas fa-user-circle"></i>
              </span>
              <span class="account-dropdown__item-copy">
                <strong>{{ profileTitleText }}</strong>
                <small>{{ profileHintText }}</small>
              </span>
            </button>

            <button type="button" class="account-dropdown__item" @click="goToWorkspace">
              <span class="account-dropdown__item-icon">
                <i class="fas fa-briefcase"></i>
              </span>
              <span class="account-dropdown__item-copy">
                <strong>{{ workspaceTitleText }}</strong>
                <small>{{ workspaceHintText }}</small>
              </span>
            </button>

            <div class="account-dropdown__section">
              <div class="account-dropdown__section-label">{{ copy.account.language }}</div>
              <div class="account-dropdown__switch">
                <button
                  v-for="item in languageOptions"
                  :key="item.value"
                  type="button"
                  :class="['account-dropdown__switch-button', { 'is-active': locale === item.value }]"
                  @click="setLocale(item.value)"
                >
                  {{ item.label }}
                </button>
              </div>
            </div>

            <div class="account-dropdown__separator"></div>

            <button
              type="button"
              class="account-dropdown__item account-dropdown__item--danger"
              @click="handleLogout"
            >
              <span class="account-dropdown__item-icon">
                <i class="fas fa-sign-out-alt"></i>
              </span>
              <span class="account-dropdown__item-copy">
                <strong>{{ copy.account.logout }}</strong>
                <small>{{ copy.account.logoutHint }}</small>
              </span>
            </button>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script>
import { createInitials, getStoredUser } from "../../components/Shared/customerSession";

const COPY = {
  vi: {
    brandTagline: "Lập kế hoạch thông minh",
    nav: {
      home: "TRANG CHỦ",
      plan: "KẾ HOẠCH",
      tours: "CHUYẾN ĐI",
      destinations: "ĐIỂM ĐẾN",
      contact: "LIÊN HỆ",
      login: "Đăng nhập",
      register: "Đăng ký",
    },
    account: {
      customer: "Thành viên",
      admin: "Quản trị viên",
      customerCode: "Mã khách hàng",
      workspaceAdmin: "Quản lý hệ thống",
      workspaceAdminHint: "Quản lý dữ liệu và hệ thống.",
      workspaceCustomer: "Quản lý tài khoản",
      workspaceCustomerHint: "Truy cập thông tin cá nhân của bạn.",
      language: "Ngôn ngữ hiển thị",
      logout: "Đăng xuất",
      logoutHint: "Kết thúc phiên hiện tại trên thiết bị.",
    },
  },
  en: {
    brandTagline: "Lập kế hoạch thông minh",
    nav: {
      home: "TRANG CHỦ",
      plan: "KẾ HOẠCH",
      tours: "CHUYẾN ĐI",
      destinations: "ĐIỂM ĐẾN",
      contact: "LIÊN HỆ",
      login: "Đăng nhập",
      register: "Đăng ký",
    },
    account: {
      customer: "Thành viên",
      admin: "Quản trị viên",
      customerCode: "Mã khách hàng",
      workspaceAdmin: "Quản lý hệ thống",
      workspaceAdminHint: "Quản lý dữ liệu và hệ thống.",
      workspaceCustomer: "Quản lý tài khoản",
      workspaceCustomerHint: "Truy cập thông tin cá nhân của bạn.",
      language: "Ngôn ngữ hiển thị",
      logout: "Đăng xuất",
      logoutHint: "Kết thúc phiên hiện tại trên thiết bị.",
    },
  },
};

export default {
  name: "TopNavBar",
  data() {
    return {
      isNavOpen: false,
      isAccountOpen: false,
      locale: "vi",
      hasToken: false,
      authType: "",
      sessionUser: null,
      handleOutsideClick: null,
      handleStorageUpdate: null,
      
      // Scroll state variables
      showNavbar: true,
      lastScrollPosition: 0,
      isScrolled: false,
      handleScroll: null,
    };
  },
  computed: {
    copy() {
      return COPY[this.locale] || COPY.vi;
    },
    isAuthenticated() {
      return this.hasToken;
    },
    displayName() {
      const user = this.sessionUser || {};
      return (
        user.Ho_va_ten ||
        user.ho_va_ten ||
        user.ten_doi_tac ||
        user.ten_nguoi_dai_dien ||
        user.name ||
        user.email ||
        "Smart Travel"
      );
    },
    displaySubtitle() {
      if (this.authType === "admin") {
        return this.copy.account.admin;
      }

      const user = this.sessionUser || {};
      if (this.authType === "partner") {
        const partnerCode = String(
          user.Ma_doi_tac ||
            user.ma_doi_tac ||
            user.id ||
            user.ID ||
            "",
        ).trim();

        return partnerCode
          ? `Mã đối tác ${partnerCode}`
          : "Đối tác";
      }

      const customerCode = String(
        user.Ma_khach_hang ||
          user.ma_khach_hang ||
          user.id ||
          user.ID ||
          "",
      ).trim();

      return customerCode
        ? `${this.copy.account.customerCode} ${customerCode}`
        : this.copy.account.customer;
    },
    avatarText() {
      return createInitials(
        this.displayName,
        this.authType === "admin" ? "AD" : this.authType === "partner" ? "DT" : "KH",
      );
    },
    workspacePath() {
      if (this.authType === "admin") return "/dashboard";
      if (this.authType === "partner") return "/doi-tac/dashboard";
      return "/khach-hang/ke-hoach";
    },
    profilePath() {
      if (this.authType === "admin") return "/admin/ho-so";
      if (this.authType === "partner") return "/doi-tac/thong-tin-tai-khoan";
      return "/khach-hang/ho-so";
    },
    profileTitleText() {
      return "Hồ sơ cá nhân";
    },
    profileHintText() {
      return this.authType === "admin"
        ? "Truy cập hồ sơ quản trị."
        : "Truy cập thông tin cá nhân của bạn.";
    },
    workspaceTitleText() {
      if (this.authType === "admin") return this.copy.account.workspaceAdmin;
      if (this.authType === "partner") return "Không gian đối tác";
      return this.copy.account.workspaceCustomer;
    },
    workspaceHintText() {
      if (this.authType === "admin") return this.copy.account.workspaceAdminHint;
      if (this.authType === "partner") return "Theo dõi tour và địa điểm của đối tác.";
      return this.copy.account.workspaceCustomerHint;
    },
    languageOptions() {
      return [
        { value: "vi", label: "VI" },
        { value: "en", label: "EN" },
      ];
    },
  },
  methods: {
    syncSession() {
      this.hasToken = Boolean(localStorage.getItem("token"));
      this.sessionUser = getStoredUser();
      let authType = (localStorage.getItem("auth_type") || "").toLowerCase().trim();
      if (!authType && this.sessionUser) {
        if (this.sessionUser?.Ma_quan_tri || this.sessionUser?.ma_quan_tri || this.sessionUser?.Vai_tro === "admin") {
          authType = "admin";
        } else if (this.sessionUser?.Ma_doi_tac || this.sessionUser?.ma_doi_tac) {
          authType = "partner";
        } else if (this.sessionUser?.Ma_khach_hang || this.sessionUser?.ma_khach_hang) {
          authType = "customer";
        }
      }
      this.authType = authType;
      this.locale = localStorage.getItem("preferred_locale") === "en" ? "en" : "vi";
    },
    toggleNav() {
      this.isNavOpen = !this.isNavOpen;
      if (!this.isNavOpen) {
        this.isAccountOpen = false;
      }
    },
    closeNav() {
      this.isNavOpen = false;
    },
    toggleAccountMenu() {
      this.isAccountOpen = !this.isAccountOpen;
    },
    closeMenus() {
      this.isNavOpen = false;
      this.isAccountOpen = false;
    },
    setLocale(value) {
      this.locale = value === "en" ? "en" : "vi";
      localStorage.setItem("preferred_locale", this.locale);
      window.dispatchEvent(new Event("storage"));
    },
    goToProfile() {
      this.closeMenus();

      if (this.$route.path !== this.profilePath) {
        this.$router.push(this.profilePath);
      }
    },
    goToWorkspace() {
      this.closeMenus();

      if (this.$route.path !== this.workspacePath) {
        this.$router.push(this.workspacePath);
      }
    },
    handleLogout() {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      localStorage.removeItem("auth_type");
      this.syncSession();
      this.closeMenus();
      this.$router.push("/dang-nhap");
    },
    onScroll() {
      const currentScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
      if (currentScrollPosition < 0) return;

      // Determine if we've scrolled down enough to trigger the glass effect
      this.isScrolled = currentScrollPosition > 50;

      // Determine slide direction
      if (Math.abs(currentScrollPosition - this.lastScrollPosition) < 50 && currentScrollPosition > 50) {
        // Debounce small scroll amounts
        return;
      }

      if (currentScrollPosition < this.lastScrollPosition) {
        // Scrolling UP
        this.showNavbar = true;
      } else if (currentScrollPosition > 100) {
        // Scrolling DOWN
        this.showNavbar = false;
        this.closeMenus(); // Close menus when scrolling down to hide nav
      }

      this.lastScrollPosition = currentScrollPosition;
    }
  },
  watch: {
    $route() {
      this.closeMenus();
      this.syncSession();
    },
  },
  mounted() {
    this.syncSession();

    this.handleOutsideClick = (event) => {
      if (!this.$refs.navRoot?.contains(event.target)) {
        this.closeMenus();
      }
    };

    this.handleStorageUpdate = () => {
      this.syncSession();
    };

    this.handleScroll = () => {
      this.onScroll();
    };

    document.addEventListener("click", this.handleOutsideClick);
    window.addEventListener("storage", this.handleStorageUpdate);
    window.addEventListener("scroll", this.handleScroll, { passive: true });
  },
  beforeUnmount() {
    if (this.handleOutsideClick) {
      document.removeEventListener("click", this.handleOutsideClick);
    }
    if (this.handleStorageUpdate) {
      window.removeEventListener("storage", this.handleStorageUpdate);
    }
    if (this.handleScroll) {
      window.removeEventListener("scroll", this.handleScroll);
    }
  },
};
</script>

<style scoped>
.topnav {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 50;
  background: rgba(255, 255, 255, 0.75);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(226, 232, 240, 0.5);
  transform: translateY(0);
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), 
              background-color 0.3s ease, 
              padding 0.3s ease, 
              box-shadow 0.3s ease;
  padding: 8px 0;
}

.topnav.is-hidden {
  transform: translateY(-100%);
}

.topnav.is-scrolled {
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  padding: 0;
}

.topnav-container {
  width: min(100%, 1280px);
  margin: 0 auto;
  padding: 0 24px;
  min-height: 70px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.topnav-brand-link {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  min-width: 0;
}

.topnav-brand-mark {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
  color: #fff;
  font-size: 1.2rem;
  box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
  transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.topnav-brand-link:hover .topnav-brand-mark {
  transform: scale(1.08) rotate(5deg);
}

.topnav-brand-copy {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
}

.topnav-brand-copy strong {
  color: #0f172a;
  font-size: 1.15rem;
  font-weight: 900;
  line-height: 1.1;
  letter-spacing: -0.5px;
}

.text-blue {
  color: #2563eb;
}

.topnav-brand-copy small {
  color: #94a3b8;
  font-size: 0.65rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.topnav-menu {
  display: flex;
  align-items: center;
  gap: 36px;
  list-style: none;
  margin: 0;
  padding: 0;
}

.topnav-item {
  position: relative;
}

.topnav-link {
  color: #64748b;
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 800;
  text-transform: uppercase;
  transition: color 0.3s ease;
  position: relative;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 0;
}

.topnav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0%;
  height: 3px;
  background-color: #2563eb;
  transition: width 0.3s ease;
}

.topnav-link:hover,
.topnav-link.router-link-active {
  color: #2563eb;
}

.topnav-link.router-link-active::after,
.topnav-link:hover::after {
  width: 100%;
}

.topnav-link-btn {
  min-width: 100px;
  min-height: 42px;
  padding: 0 20px;
  border-radius: 12px;
  background: #2563eb;
  color: #fff !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
  box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
}
.topnav-link-btn::after {
  display: none;
}

.topnav-link-btn.router-link-active,
.topnav-link-btn:hover {
  background: #1d4ed8;
  color: #fff !important;
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(37, 99, 235, 0.3);
}

.topnav-item--profile {
  margin-left: 12px;
}

.account-trigger {
  min-width: 160px;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  background: rgba(248, 250, 252, 0.6);
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 6px 16px 6px 6px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.account-trigger:hover {
  background: #ffffff;
  border-color: #cbd5e1;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}

.account-trigger > i {
  color: #94a3b8;
  font-size: 0.8rem;
  transition: transform 0.3s ease;
  margin-left: auto;
}

.account-trigger > i.is-open {
  transform: rotate(180deg);
}

.account-trigger__avatar,
.account-summary__avatar {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  color: #ffffff;
  font-weight: 800;
  font-size: 0.9rem;
  letter-spacing: 0.5px;
  flex-shrink: 0;
  box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);
}

.account-trigger__copy,
.account-summary__copy {
  display: flex;
  flex-direction: column;
  min-width: 0;
  text-align: left;
}

.account-trigger__copy strong,
.account-summary__copy strong {
  color: #0f172a;
  font-size: 0.9rem;
  font-weight: 800;
  line-height: 1.2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.account-trigger__copy small,
.account-summary__copy span {
  color: #64748b;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  margin-top: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.account-dropdown {
  position: absolute;
  top: calc(100% + 16px);
  right: 0;
  width: 300px;
  padding: 16px;
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid #f1f5f9;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  pointer-events: none;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.account-dropdown.is-open {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
  pointer-events: auto;
}

.account-summary {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px;
  border-radius: 14px;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
}

.account-dropdown__item {
  width: 100%;
  border: none;
  background: transparent;
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px;
  margin-top: 8px;
  border-radius: 12px;
  cursor: pointer;
  text-align: left;
  transition: background-color 0.2s ease, transform 0.2s ease;
}

.account-dropdown__item:hover {
  background: #f1f5f9;
  transform: translateX(2px);
}

.account-dropdown__item-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: grid;
  place-items: center;
  background: #e2e8f0;
  color: #475569;
  flex-shrink: 0;
  font-size: 1rem;
}

.account-dropdown__item-copy {
  display: flex;
  flex-direction: column;
  gap: 0.16rem;
}

.account-dropdown__item-copy strong {
  color: #0f172a;
  font-size: 0.85rem;
  font-weight: 700;
}

.account-dropdown__item-copy small {
  color: #64748b;
  font-size: 0.75rem;
}

.account-dropdown__section {
  margin-top: 12px;
  padding: 14px;
  border-radius: 14px;
  background: #f8fafc;
  border: 1px solid #f1f5f9;
}

.account-dropdown__section-label {
  color: #94a3b8;
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
}

.account-dropdown__switch {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 8px;
}

.account-dropdown__switch-button {
  min-height: 40px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #64748b;
  font-weight: 700;
  cursor: pointer;
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.account-dropdown__switch-button.is-active {
  background: #2563eb;
  border-color: #2563eb;
  color: #ffffff;
  box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
}

.account-dropdown__separator {
  height: 1px;
  margin: 12px 0;
  background: #f1f5f9;
}

.account-dropdown__item--danger .account-dropdown__item-icon {
  background: #fef2f2;
  color: #e11d48;
}

.account-dropdown__item--danger:hover {
  background: #fff1f2;
  color: #e11d48;
}

.topnav-toggler {
  display: none;
  border: none;
  background: transparent;
  color: #0f172a;
  font-size: 1.5rem;
  cursor: pointer;
}

@media (max-width: 1024px) {
  .topnav-container {
    padding: 0 20px;
  }

  .topnav-menu {
    gap: 20px;
  }

  .topnav-brand-copy small {
    display: none;
  }
}

@media (max-width: 900px) {
  .topnav-toggler {
    display: block;
  }

  .topnav-menu {
    position: absolute;
    top: calc(100% + 10px);
    left: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(16px);
    border: 1px solid #f1f5f9;
    border-radius: 20px;
    padding: 20px;
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
  }

  .topnav-menu.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }

  .topnav-link {
    display: block;
    padding: 12px 0;
    font-size: 0.9rem;
  }

  .topnav-link::after {
    display: none;
  }

  .topnav-link-btn,
  .account-trigger {
    width: 100%;
    margin-top: 10px;
  }

  .account-trigger {
    background: #f8fafc;
  }

  .topnav-item--profile {
    margin-left: 0;
  }

  .account-dropdown {
    position: static;
    width: 100%;
    margin-top: 12px;
    opacity: 1;
    visibility: visible;
    transform: none;
    display: none;
    pointer-events: auto;
    box-shadow: none;
    padding: 0;
    border: none;
  }

  .account-dropdown.is-open {
    display: block;
  }
}
</style>
