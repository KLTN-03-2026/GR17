<template>
  <div class="member-page">
    <div class="member-layout">
      <CustomerSidebar />
      <aside v-if="false" class="profile-panel">
        <div class="profile-card">
          <div class="profile-head">
            <div class="avatar">{{ chuVietTatKhachHang }}</div>
            <div>
              <strong>{{ tenKhachHang }}</strong>
              <span>{{ nhanThanhVien }}</span>
            </div>
          </div>

        </div>

        <nav class="profile-menu">
          <router-link class="menu-link is-active" to="/khach-hang/nhom-hanh-trinh">
            <i class="fas fa-users"></i>
            <span>Nhóm hành trình</span>
          </router-link>
          <router-link class="menu-link" to="/khach-hang/ke-hoach">
            <i class="fas fa-calendar-days"></i>
            <span>Danh sách kế hoạch</span>
          </router-link>
          <router-link class="menu-link" to="/khach-hang/danh-gia">
            <i class="fas fa-star"></i>
            <span>Bảng đánh giá</span>
          </router-link>
          <router-link class="menu-link" to="/khach-hang/dia-diem">
            <i class="fas fa-map-location-dot"></i>
            <span>Địa điểm</span>
          </router-link>
          <router-link class="profile-panel__link" to="/khach-hang/tour">
            <i class="fas fa-map"></i>
            <span>Danh sách tour</span>
          </router-link>
          <router-link class="menu-link" to="/khach-hang/danh-sach-yeu-thich">
            <i class="fas fa-heart"></i>
            <span>Danh sách yêu thích</span>
          </router-link>
          <router-link class="menu-link" to="/khach-hang/ho-so">
            <i class="fas fa-user-cog"></i>
            <span>Quản lý tài khoản</span>
          </router-link>
          <a class="menu-link" href="#" @click.prevent="logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Đăng xuất</span>
          </a>
        </nav>
      </aside>

      <section class="member-content">
        <header class="hero-card">
          <div>
            <p class="eyebrow">Nhóm hành trình / Thành viên</p>
            <h1>{{ group.name || "Quản lý thành viên nhóm" }}</h1>
            <p class="subtitle">
              Thêm thành viên, đổi vai trò và kiểm soát quyền nhóm trưởng trong cùng một màn hình.
            </p>
          </div>
          <div class="hero-actions">
            <router-link class="ghost-btn" to="/khach-hang/nhom-hanh-trinh">
              <i class="fas fa-arrow-left"></i>
              <span>Quay lại</span>
            </router-link>
            <button class="primary-btn" type="button" :disabled="!canManage || dangTai" @click="openAddDialog">
              <i class="fas fa-user-plus"></i>
              <span>Thêm thành viên</span>
            </button>
            <button class="ghost-btn" type="button" :disabled="dangTai" @click="taiDuLieu">
              <i class="fas fa-rotate-right"></i>
              <span>{{ dangTai ? "Đang tải..." : "Tải lại" }}</span>
            </button>
          </div>
        </header>

        <section class="stats-grid">
          <article class="stat-card">
            <span>Tổng thành viên</span>
            <strong>{{ members.length }}</strong>
          </article>
          <article class="stat-card">
            <span>Nhóm trưởng</span>
            <strong>{{ soLuongTruongNhom }}</strong>
          </article>
          <article class="stat-card">
            <span>Vai trò của bạn</span>
            <strong>{{ currentRoleLabel }}</strong>
          </article>
          <article class="stat-card">
            <span>Mã nhóm</span>
            <strong>{{ group.id || "--" }}</strong>
          </article>
        </section>

        <section class="overview-grid">
          <article class="overview-card">
            <span>Tên nhóm</span>
            <strong>{{ group.name || "--" }}</strong>
          </article>
          <article class="overview-card">
            <span>Khởi tạo</span>
            <strong>{{ group.createdAtLabel }}</strong>
          </article>
          <article class="overview-card">
            <span>Quyền thao tác</span>
            <strong>{{ canManage ? "Nhóm trưởng" : "Chỉ xem" }}</strong>
          </article>
        </section>

        <section class="toolbar">
          <label class="search-box">
            <i class="fas fa-search"></i>
            <input
              v-model.trim="search"
              type="text"
              placeholder="Tìm theo mã khách hàng, họ tên, email hoặc số điện thoại..."
            >
          </label>
          <select v-model="roleFilter" class="role-select">
            <option value="all">Vai trò: Tất cả</option>
            <option value="1">Nhóm trưởng</option>
            <option value="0">Thành viên</option>
          </select>
        </section>

        <div v-if="!canManage && currentMembership" class="notice notice--info">
          <i class="fas fa-circle-info"></i>
          <span>Chỉ nhóm trưởng mới có thể thêm, đổi vai trò hoặc xóa thành viên.</span>
        </div>
        <div v-if="thongBaoLoi" class="notice notice--error">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ thongBaoLoi }}</span>
        </div>
        <div v-if="thongBaoThanhCong" class="notice notice--success">
          <i class="fas fa-circle-check"></i>
          <span>{{ thongBaoThanhCong }}</span>
        </div>

        <section v-if="dangTai" class="loading-grid">
          <div class="loading-card"></div>
          <div class="loading-card"></div>
          <div class="loading-card"></div>
        </section>

        <section v-else-if="filteredMembers.length" class="table-card">
          <table class="member-table">
            <thead>
              <tr>
                <th>Mã TV</th>
                <th>Khách hàng</th>
                <th>Liên hệ</th>
                <th>Vai trò</th>
                <th>Tham gia</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="member in filteredMembers" :key="member.maThanhVienNhom">
                <td>{{ member.maThanhVienNhom }}</td>
                <td>
                  <div class="cell-stack">
                    <strong>{{ member.tenKhachHang }}</strong>
                    <span>{{ member.maKhachHang }}</span>
                    <small v-if="member.maKhachHang === maKhachHang">Bạn</small>
                  </div>
                </td>
                <td>
                  <div class="cell-stack">
                    <span>{{ member.customerEmail }}</span>
                    <span>{{ member.customerPhone }}</span>
                  </div>
                </td>
                <td>
                  <span class="role-badge" :class="member.roleValue === 1 ? 'is-leader' : 'is-member'">
                    {{ member.roleLabel }}
                  </span>
                </td>
                <td>{{ member.joinedAtLabel }}</td>
                <td>
                  <div class="row-actions">
                    <button class="icon-btn" type="button" :disabled="!canManage" @click="openRoleDialog(member)">
                      <i class="fas fa-user-gear"></i>
                    </button>
                    <button class="icon-btn danger" type="button" :disabled="!canManage" @click="openDeleteDialog(member)">
                      <i class="fas fa-user-minus"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </section>

        <section v-else class="empty-card">
          <i class="fas fa-users-slash"></i>
          <h2>Chưa có thành viên phù hợp</h2>
          <p>{{ emptyMessage }}</p>
          <button v-if="canManage" class="primary-btn" type="button" @click="openAddDialog">
            Thêm thành viên đầu tiên
          </button>
        </section>
      </section>
    </div>

    <div v-if="addDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card">
        <p class="eyebrow">Thêm thành viên mới</p>
        <h2>Bổ sung khách hàng vào nhóm {{ group.name || group.id }}</h2>
        <label class="field">
          <span>Tìm khách hàng</span>
          <input
            v-model.trim="tuKhoaTimKhachHang"
            type="text"
            placeholder="Nhập mã khách hàng, họ tên, email hoặc số điện thoại"
          >
        </label>
        <label class="field">
          <span>Khách hàng</span>
          <select v-model="addForm.maKhachHang">
            <option value="">Chọn khách hàng</option>
            <option v-for="customer in availableCustomers" :key="customer.id" :value="customer.id">
              {{ customer.id }} - {{ customer.name }} - {{ customer.phone }}
            </option>
          </select>
          <small v-if="errors.maKhachHang">{{ errors.maKhachHang }}</small>
        </label>
        <label class="field">
          <span>Vai trò</span>
          <select v-model="addForm.roleValue">
            <option value="0">Thành viên</option>
            <option value="1">Nhóm trưởng</option>
          </select>
          <small v-if="errors.roleValue">{{ errors.roleValue }}</small>
        </label>
        <p class="dialog-text" v-if="khachHangDangChon">
          {{ khachHangDangChon.email }} · {{ khachHangDangChon.phone }}
        </p>
        <div class="dialog-actions">
          <button class="ghost-btn" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="primary-btn" type="button" :disabled="isSubmitting" @click="createMember">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang thêm..." : "Xác nhận thêm" }}</span>
          </button>
        </div>
      </section>
    </div>

    <div v-if="roleDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card">
        <p class="eyebrow">Cập nhật vai trò</p>
        <h2>{{ thanhVienDangChon?.tenKhachHang }}</h2>
        <label class="field">
          <span>Vai trò mới</span>
          <select v-model="editForm.roleValue">
            <option value="0">Thành viên</option>
            <option value="1">Nhóm trưởng</option>
          </select>
          <small v-if="errors.roleValue">{{ errors.roleValue }}</small>
        </label>
        <p class="dialog-text">Nhóm cần ít nhất một nhóm trưởng để tiếp tục quản lý.</p>
        <div class="dialog-actions">
          <button class="ghost-btn" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="primary-btn" type="button" :disabled="isSubmitting" @click="updateMemberRole">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang lưu..." : "Lưu thay đổi" }}</span>
          </button>
        </div>
      </section>
    </div>

    <div v-if="deleteDialogOpen" class="dialog-backdrop" @click.self="closeDialogs">
      <section class="dialog-card danger-card">
        <p class="eyebrow">Xác nhận xóa thành viên</p>
        <h2>Xóa {{ thanhVienDangChon?.tenKhachHang }} khỏi nhóm?</h2>
        <p class="dialog-text">Thành viên sẽ mất quyền truy cập vào nhóm và các kế hoạch liên quan.</p>
        <div class="dialog-actions">
          <button class="ghost-btn" type="button" :disabled="isSubmitting" @click="closeDialogs">Hủy</button>
          <button class="danger-btn" type="button" :disabled="isSubmitting" @click="deleteMember">
            <i v-if="isSubmitting" class="fas fa-spinner fa-spin"></i>
            <span>{{ isSubmitting ? "Đang xóa..." : "Xóa thành viên" }}</span>
          </button>
        </div>
      </section>
    </div>
  </div>

</template>

<script>
import { goiApi } from '../../../services/httpClient.js';
import CustomerSidebar from "../CustomerSidebar.vue";
import {
  API_BASE,
  buildHeaders,
  createInitials,
  formatDateDisplay,
  getStoredCustomerId,
  getStoredUser,
  chuanHoaDanhSach,
} from "../../Shared/customerSession";

function normalizeGroup(duLieuPhanHoi) {
  const item = duLieuPhanHoi?.data?.data || duLieuPhanHoi?.data || duLieuPhanHoi;
  return {
    id: String(item?.Ma_nhom || item?.ma_nhom || ""),
    name: item?.ten_nhom || "Nhóm chưa xác định",
    createdAtLabel: formatDateDisplay(item?.created_at, true),
  };
}

function normalizeMember(row) {
  const customer = row?.khach_hang || row?.khachHang || {};
  const roleValue = Number(row?.vai_tro ?? 0);
  return {
    maThanhVienNhom: String(row?.Ma_thanh_vien || row?.ma_thanh_vien || ""),
    maKhachHang: String(row?.Ma_khach_hang || row?.ma_khach_hang || customer?.Ma_khach_hang || ""),
    tenKhachHang: customer?.Ho_va_ten || customer?.ho_va_ten || "Khách hàng chưa xác định",
    customerEmail: customer?.Email || customer?.email || "--",
    customerPhone: customer?.so_dien_thoai || customer?.So_dien_thoai || "--",
    roleValue,
    roleLabel: roleValue === 1 ? "Nhóm trưởng" : "Thành viên",
    joinedAtLabel: formatDateDisplay(row?.created_at, true),
  };
}

function normalizeCustomer(item) {
  return {
    id: String(item?.Ma_khach_hang || item?.ma_khach_hang || ""),
    name: item?.Ho_va_ten || item?.ho_va_ten || "Khách hàng chưa xác định",
    email: item?.Email || item?.email || "--",
    phone: item?.so_dien_thoai || item?.So_dien_thoai || "--",
  };
}
export default {
  name: "QuanLyThanhVienNhom",
  components: {
    CustomerSidebar,
  },
  data() {
    return {
      dangTai: false,
      isSubmitting: false,
      thongBaoLoi: "",
      thongBaoThanhCong: "",
      maKhachHang: "",
      group: { id: "", name: "", createdAtLabel: "--" },
      members: [],
      danhSachKhachHang: [],
      search: "",
      roleFilter: "all",
      tuKhoaTimKhachHang: "",
      addDialogOpen: false,
      roleDialogOpen: false,
      deleteDialogOpen: false,
      thanhVienDangChon: null,
      addForm: { maKhachHang: "", roleValue: "0" },
      editForm: { roleValue: "0" },
      errors: { maKhachHang: "", roleValue: "" },
    };
  },
  computed: {
    maNhom() {
      return String(this.$route.params.id || "").trim();
    },
    hoSoKhachHang() {
      return getStoredUser() || {};
    },
    tenKhachHang() {
      return this.hoSoKhachHang.Ho_va_ten || this.hoSoKhachHang.ho_va_ten || "Tài khoản của tôi";
    },
    chuVietTatKhachHang() {
      return createInitials(this.tenKhachHang, "TV");
    },
    nhanThanhVien() {
      return this.maKhachHang ? `Mã khách hàng ${this.maKhachHang}` : "Vui lòng đăng nhập để đồng bộ";
    },
    currentMembership() {
      return this.members.find((member) => member.maKhachHang === this.maKhachHang) || null;
    },
    canManage() {
      return this.currentMembership?.roleValue === 1;
    },
    currentRoleLabel() {
      return this.currentMembership?.roleLabel || "Không xác định";
    },
    soLuongTruongNhom() {
      return this.members.filter((member) => member.roleValue === 1).length;
    },
    filteredMembers() {
      const keyword = String(this.search || "").toLowerCase();
      return this.members.filter((member) => {
        const matchesKeyword = !keyword || [
          member.maThanhVienNhom,
          member.maKhachHang,
          member.tenKhachHang,
          member.customerEmail,
          member.customerPhone,
          member.roleLabel,
        ].join(" ").toLowerCase().includes(keyword);
        const matchesRole = this.roleFilter === "all" || String(member.roleValue) === this.roleFilter;
        return matchesKeyword && matchesRole;
      });
    },
    availableCustomers() {
      const existedIds = new Set(this.members.map((member) => member.maKhachHang));
      const keyword = String(this.tuKhoaTimKhachHang || "").toLowerCase();
      return this.danhSachKhachHang.filter((customer) => {
        if (existedIds.has(customer.id)) return false;
        if (!keyword) return true;
        return [customer.id, customer.name, customer.email, customer.phone].join(" ").toLowerCase().includes(keyword);
      });
    },
    khachHangDangChon() {
      return this.danhSachKhachHang.find((customer) => customer.id === this.addForm.maKhachHang) || null;
    },
    emptyMessage() {
      if (!this.maKhachHang) return "Bạn cần đăng nhập lại để đồng bộ thông tin nhóm.";
      if (!this.currentMembership && this.members.length) return "Tài khoản hiện tại không còn nằm trong nhóm này.";
      if (this.search || this.roleFilter !== "all") return "Không tìm thấy thành viên nào khớp với bộ lọc hiện tại.";
      return this.canManage
        ? "Nhóm chưa có thành viên nào ngoài bạn. Hãy thêm thành viên để bắt đầu cộng tác."
        : "Nhóm hiện chưa có dữ liệu thành viên để hiển thị.";
    },
  },
  watch: {
    maNhom() {
      this.taiDuLieu();
    },
  },
  methods: {
    logout() {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      localStorage.removeItem("auth_type");
      window.dispatchEvent(new Event("storage"));
      this.$router.push("/dang-nhap");
    },
    clearMessages() {
      this.thongBaoLoi = "";
      this.thongBaoThanhCong = "";
    },
    resetErrors() {
      this.errors = { maKhachHang: "", roleValue: "" };
    },
    resetForms() {
      this.tuKhoaTimKhachHang = "";
      this.addForm = { maKhachHang: "", roleValue: "0" };
      this.editForm = { roleValue: String(this.thanhVienDangChon?.roleValue ?? 0) };
      this.resetErrors();
    },
    closeDialogs(force = false) {
      if (this.isSubmitting && !force) return;
      this.addDialogOpen = false;
      this.roleDialogOpen = false;
      this.deleteDialogOpen = false;
      this.thanhVienDangChon = null;
      this.resetForms();
    },
    sortMembers(rows) {
      return [...rows].sort((a, b) => {
        if (a.roleValue !== b.roleValue) return b.roleValue - a.roleValue;
        return a.tenKhachHang.localeCompare(b.tenKhachHang, "vi");
      });
    },
    async taiNhom() {
      const phanHoi = await goiApi(`${API_BASE}/nhom/${encodeURIComponent(this.maNhom)}`, { headers: buildHeaders() });
      const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
      if (!phanHoi.ok) throw new Error(duLieuPhanHoi?.message || "Không thể tải thông tin nhóm.");
      return normalizeGroup(duLieuPhanHoi);
    },
    async taiThanhVien() {
      const phanHoi = await goiApi(`${API_BASE}/thanh-vien-nhom/nhom/${encodeURIComponent(this.maNhom)}`, { headers: buildHeaders() });
      const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
      if (!phanHoi.ok) {
        if (phanHoi.status === 404) return [];
        throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách thành viên.");
      }
      return this.sortMembers(chuanHoaDanhSach(duLieuPhanHoi).map((row) => normalizeMember(row)));
    },
    async taiKhachHang() {
      const phanHoi = await goiApi(`${API_BASE}/khach-hang`, { headers: buildHeaders() });
      const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
      if (!phanHoi.ok) throw new Error(duLieuPhanHoi?.message || "Không thể tải danh sách khách hàng.");
      return chuanHoaDanhSach(duLieuPhanHoi).map((item) => normalizeCustomer(item)).sort((a, b) => a.name.localeCompare(b.name, "vi"));
    },
    async taiDuLieu() {
      this.dangTai = true;
      this.clearMessages();
      this.maKhachHang = getStoredCustomerId();
      if (!this.maNhom) {
        this.thongBaoLoi = "Không tìm thấy mã nhóm hợp lệ trên URL hiện tại.";
        this.group = { id: "", name: "", createdAtLabel: "--" };
        this.members = [];
        this.danhSachKhachHang = [];
        this.dangTai = false;
        return;
      }
      if (!this.maKhachHang) {
        this.thongBaoLoi = "Không tìm thấy mã khách hàng trong phiên đăng nhập hiện tại.";
        this.group = { id: "", name: "", createdAtLabel: "--" };
        this.members = [];
        this.danhSachKhachHang = [];
        this.dangTai = false;
        return;
      }
      try {
        const [group, members, danhSachKhachHang] = await Promise.all([this.taiNhom(), this.taiThanhVien(), this.taiKhachHang()]);
        this.group = group;
        this.members = members;
        this.danhSachKhachHang = danhSachKhachHang;
        if (members.length && !members.some((member) => member.maKhachHang === this.maKhachHang)) {
          this.thongBaoLoi = "Bạn không còn là thành viên của nhóm này.";
        }
      } catch (error) {
        this.group = { id: "", name: "", createdAtLabel: "--" };
        this.members = [];
        this.danhSachKhachHang = [];
        this.thongBaoLoi = error.message || "Không thể tải dữ liệu quản lý thành viên.";
      } finally {
        this.dangTai = false;
      }
    },
    openAddDialog() {
      if (!this.canManage) {
        this.thongBaoLoi = "Chỉ nhóm trưởng mới có thể thêm thành viên.";
        return;
      }
      this.clearMessages();
      this.resetForms();
      this.addDialogOpen = true;
    },
    openRoleDialog(member) {
      if (!this.canManage) {
        this.thongBaoLoi = "Chỉ nhóm trưởng mới có thể cập nhật vai trò.";
        return;
      }
      this.clearMessages();
      this.thanhVienDangChon = member;
      this.resetForms();
      this.roleDialogOpen = true;
    },
    openDeleteDialog(member) {
      if (!this.canManage) {
        this.thongBaoLoi = "Chỉ nhóm trưởng mới có thể xóa thành viên.";
        return;
      }
      this.clearMessages();
      this.thanhVienDangChon = member;
      this.deleteDialogOpen = true;
    },
    validateAddForm() {
      this.resetErrors();
      if (!String(this.addForm.maKhachHang || "").trim()) this.errors.maKhachHang = "Bạn cần chọn một khách hàng.";
      if (![0, 1].includes(Number(this.addForm.roleValue))) this.errors.roleValue = "Vai trò chỉ chấp nhận 0 hoặc 1.";
      return !this.errors.maKhachHang && !this.errors.roleValue;
    },
    validateEditForm() {
      this.resetErrors();
      if (![0, 1].includes(Number(this.editForm.roleValue))) this.errors.roleValue = "Vai trò chỉ chấp nhận 0 hoặc 1.";
      if (this.thanhVienDangChon?.roleValue === 1 && Number(this.editForm.roleValue) === 0 && this.soLuongTruongNhom <= 1) {
        this.errors.roleValue = "Nhóm phải còn ít nhất một nhóm trưởng.";
      }
      return !this.errors.roleValue;
    },
    async createMember() {
      if (!this.validateAddForm()) return;
      this.isSubmitting = true;
      this.clearMessages();
      try {
        const phanHoi = await goiApi(`${API_BASE}/thanh-vien-nhom`, {
          method: "POST",
          headers: buildHeaders(true),
          body: JSON.stringify({
            Ma_nhom: this.maNhom,
            Ma_khach_hang: this.addForm.maKhachHang,
            vai_tro: Number(this.addForm.roleValue),
          }),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok) throw new Error(duLieuPhanHoi?.message || "Không thể thêm thành viên vào nhóm.");
        this.thongBaoThanhCong = "Đã thêm thành viên mới vào nhóm.";
        this.closeDialogs(true);
        await this.taiDuLieu();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể thêm thành viên vào nhóm.";
      } finally {
        this.isSubmitting = false;
      }
    },
    async updateMemberRole() {
      if (!this.thanhVienDangChon?.maThanhVienNhom || !this.validateEditForm()) return;
      this.isSubmitting = true;
      this.clearMessages();
      try {
        const phanHoi = await goiApi(`${API_BASE}/thanh-vien-nhom/${encodeURIComponent(this.thanhVienDangChon.maThanhVienNhom)}`, {
          method: "PUT",
          headers: buildHeaders(true),
          body: JSON.stringify({ vai_tro: Number(this.editForm.roleValue) }),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok) throw new Error(duLieuPhanHoi?.message || "Không thể cập nhật vai trò thành viên.");
        this.thongBaoThanhCong = `Đã cập nhật vai trò cho ${this.thanhVienDangChon.tenKhachHang}.`;
        this.closeDialogs(true);
        await this.taiDuLieu();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể cập nhật vai trò thành viên.";
      } finally {
        this.isSubmitting = false;
      }
    },
    async deleteMember() {
      if (!this.thanhVienDangChon?.maThanhVienNhom) return;
      if (this.members.length <= 1) {
        this.thongBaoLoi = "Không thể xóa thành viên cuối cùng khỏi nhóm. Hãy xóa nhóm nếu không còn sử dụng.";
        return;
      }
      if (this.thanhVienDangChon.roleValue === 1 && this.soLuongTruongNhom <= 1) {
        this.thongBaoLoi = "Nhóm cần ít nhất một nhóm trưởng. Hãy chuyển quyền cho người khác trước khi xóa.";
        return;
      }
      this.isSubmitting = true;
      this.clearMessages();
      try {
        const phanHoi = await goiApi(`${API_BASE}/thanh-vien-nhom/${encodeURIComponent(this.thanhVienDangChon.maThanhVienNhom)}`, {
          method: "DELETE",
          headers: buildHeaders(),
        });
        const duLieuPhanHoi = await phanHoi.json().catch(() => ({}));
        if (!phanHoi.ok) throw new Error(duLieuPhanHoi?.message || "Không thể xóa thành viên khỏi nhóm.");
        this.thongBaoThanhCong = `Đã xóa ${this.thanhVienDangChon.tenKhachHang} khỏi nhóm.`;
        this.closeDialogs(true);
        await this.taiDuLieu();
      } catch (error) {
        this.thongBaoLoi = error.message || "Không thể xóa thành viên khỏi nhóm.";
      } finally {
        this.isSubmitting = false;
      }
    },
  },
  mounted() {
    this.taiDuLieu();
  },
};
</script>

<style scoped>
.member-page{background:radial-gradient(circle at top right,rgba(12,104,150,.08),transparent 24%),linear-gradient(180deg,#eef5ff 0%,#f5f8ff 100%)}
.member-layout{width:min(1440px,calc(100% - 24px));margin:0 auto;display:grid;grid-template-columns:264px minmax(0,1fr);gap:20px;padding:12px 0 40px}
.profile-panel{position:sticky;top:12px;align-self:start}
.profile-card,.profile-menu,.hero-card,.stat-card,.overview-card,.table-card,.empty-card,.dialog-card{background:rgba(255,255,255,.94);border:1px solid #dbe5f3;box-shadow:0 24px 50px rgba(15,23,42,.08)}
.profile-card{padding:20px;border-radius:26px}.profile-head{display:flex;gap:14px;align-items:center}.avatar{width:56px;height:56px;display:grid;place-items:center;border-radius:50%;background:linear-gradient(135deg,#4aa3df 0%,#0f75a8 100%);color:#fff;font-weight:900}
.profile-head strong,.profile-head span{display:block}.profile-head strong{color:#0f2043;font-size:1.15rem}.profile-head span{margin-top:.35rem;color:#6b7a96}
.premium-btn,.primary-btn,.ghost-btn,.danger-btn{min-height:52px;border-radius:18px;padding:0 20px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:800;text-decoration:none}
.premium-btn{width:100%;margin-top:18px;border:0;background:linear-gradient(135deg,#0f75a8 0%,#1487c5 100%);color:#fff;box-shadow:0 18px 28px rgba(15,117,168,.25)}
.profile-menu{margin-top:14px;border-radius:26px;padding:12px;display:grid;gap:8px}
.menu-link{min-height:52px;border-radius:18px;padding:0 16px;display:flex;align-items:center;gap:12px;color:#42526e;font-weight:700;text-decoration:none}.menu-link.is-active{color:#2a49ff;background:linear-gradient(90deg,rgba(97,76,255,.14),rgba(97,76,255,.06));box-shadow:inset 3px 0 0 #5a46ff}
.hero-card{border-radius:30px;padding:28px 30px;display:flex;justify-content:space-between;gap:20px;align-items:flex-start}.eyebrow{margin:0 0 10px;text-transform:uppercase;letter-spacing:.18em;font-size:.82rem;font-weight:900;color:#c26b00}
.hero-card h1,.dialog-card h2{margin:0;color:#112347;line-height:1.04;font-weight:900}.hero-card h1{font-size:clamp(2rem,4.4vw,3.45rem)}.subtitle,.dialog-text{margin:14px 0 0;color:#61708d;line-height:1.7}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end}.primary-btn{border:0;color:#fff;background:linear-gradient(135deg,#0f75a8 0%,#1487c5 100%);box-shadow:0 18px 32px rgba(15,117,168,.22)}.ghost-btn{border:1px solid #d8e1ee;color:#284069;background:rgba(255,255,255,.92)}.danger-btn{border:0;color:#fff;background:linear-gradient(135deg,#dc2626 0%,#be123c 100%)}
.primary-btn:disabled,.ghost-btn:disabled,.danger-btn:disabled,.icon-btn:disabled{opacity:.6;cursor:not-allowed}
.stats-grid,.overview-grid,.loading-grid{margin-top:18px;display:grid;gap:16px}.stats-grid{grid-template-columns:repeat(4,minmax(0,1fr))}.overview-grid,.loading-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
.stat-card,.overview-card{border-radius:24px;padding:22px 24px}.stat-card span,.overview-card span{display:block;color:#6f7d97;font-weight:700}.stat-card strong,.overview-card strong{display:block;margin-top:10px;color:#0f2754}.stat-card strong{font-size:2rem;line-height:1.05}
.toolbar{margin-top:18px;display:grid;grid-template-columns:minmax(0,1fr) 220px;gap:14px}.search-box,.role-select,.field input,.field select{min-height:56px;border:1px solid #d8e1ee;border-radius:18px;background:rgba(255,255,255,.92)}
.search-box{display:flex;align-items:center;gap:12px;padding:0 18px}.search-box i{color:#244b8f}.search-box input,.field input,.field select{width:100%;border:0;outline:none;background:transparent;color:#112347}.role-select,.field input,.field select{padding:0 16px}
.notice{margin-top:16px;min-height:56px;border-radius:18px;padding:0 18px;display:flex;align-items:center;gap:12px;font-weight:700}.notice--error{background:#fff1f2;color:#be123c;border:1px solid #fecdd3}.notice--success{background:#ecfdf5;color:#047857;border:1px solid #a7f3d0}.notice--info{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe}
.loading-card{min-height:180px;border-radius:24px;background:linear-gradient(90deg,rgba(221,231,247,.8),rgba(244,248,255,.98),rgba(221,231,247,.8));background-size:200% 100%;animation:shimmer 1.2s linear infinite}
.table-card{margin-top:18px;border-radius:28px;overflow:auto}.member-table{width:100%;border-collapse:collapse}.member-table th,.member-table td{padding:20px 18px;text-align:left;vertical-align:middle}.member-table thead{background:rgba(15,39,84,.04)}.member-table th{color:#5f6d89;font-size:.88rem;text-transform:uppercase;letter-spacing:.12em}.member-table tbody tr + tr td{border-top:1px solid #e2e8f0}
.cell-stack{display:grid;gap:4px}.cell-stack strong{color:#102349}.cell-stack span{color:#5f6d89}.cell-stack small{color:#2563eb;font-weight:800}
.role-badge{display:inline-flex;align-items:center;min-height:34px;padding:0 14px;border-radius:999px;font-weight:800}.role-badge.is-leader{background:rgba(249,115,22,.14);color:#c2410c}.role-badge.is-member{background:rgba(37,99,235,.12);color:#1d4ed8}
.row-actions{display:flex;gap:10px}.icon-btn{width:42px;height:42px;border:0;border-radius:14px;display:grid;place-items:center;background:rgba(37,99,235,.1);color:#1d4ed8}.icon-btn.danger{background:rgba(225,29,72,.1);color:#be123c}
.empty-card{margin-top:18px;border-radius:30px;padding:44px 32px;text-align:center}.empty-card i{font-size:2.2rem;color:#2563eb}.empty-card h2{margin:16px 0 10px;color:#112347}.empty-card p{margin:0;color:#61708d;line-height:1.7}.empty-card .primary-btn{margin-top:18px}
.dialog-backdrop{position:fixed;inset:0;z-index:60;background:rgba(15,23,42,.36);backdrop-filter:blur(6px);padding:24px;display:grid;place-items:center}.dialog-card{width:min(620px,100%);border-radius:30px;padding:28px}.danger-card .eyebrow{color:#be123c}.field{display:grid;gap:8px;margin-top:16px}.field span{color:#233554;font-weight:800}.field small{color:#dc2626}.dialog-actions{margin-top:24px;display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap}
@keyframes shimmer{from{background-position:200% 0}to{background-position:-200% 0}}
@media (max-width:1180px){.member-layout{grid-template-columns:1fr}.profile-panel{position:static}.stats-grid,.overview-grid,.loading-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:860px){.member-layout{width:min(100%,calc(100% - 16px));gap:16px;padding-bottom:24px}.hero-card,.dialog-card{padding:22px}.hero-card{flex-direction:column}.hero-actions,.dialog-actions{width:100%;justify-content:stretch}.hero-actions > *,.dialog-actions > *{flex:1 1 100%}.toolbar,.stats-grid,.overview-grid,.loading-grid{grid-template-columns:1fr}}
</style>





