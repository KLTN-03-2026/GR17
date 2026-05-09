
<template>
  <div class="customer-page">
    <section class="customer-page__hero">
      <div>
        <p class="customer-page__eyebrow">Bảng Điều Khiển Quản Trị</p>
        <h1>Danh sách khách hàng</h1>
        <p class="customer-page__subtitle">
          Theo dõi tài khoản khách hàng, tìm nhanh thông tin và điều hướng sang màn chi tiết hoặc chỉnh sửa.
        </p>
      </div>

      <div class="customer-page__hero-actions">
        <router-link class="primary-button" to="/admin/customers/create">
          <i class="fas fa-plus"></i>
          <span>Thêm khách hàng</span>
        </router-link>
        <button class="ghost-button" type="button" @click="layDanhSach" :disabled="dang_tai">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dang_tai ? "Đang tải..." : "Tải lại dữ liệu" }}</span>
        </button>
      </div>
    </section>

    <section class="stats-grid">
      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--violet">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <span>Tổng khách hàng</span>
          <strong>{{ danh_sach.length.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--green">
          <i class="fas fa-user-check"></i>
        </div>
        <div>
          <span>Đang hoạt động</span>
          <strong>{{ tong_dang_hoat_dong.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--amber">
          <i class="fas fa-venus"></i>
        </div>
        <div>
          <span>Khách hàng nữ</span>
          <strong>{{ tong_khach_nu.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--blue">
          <i class="fas fa-mars"></i>
        </div>
        <div>
          <span>Khách hàng nam</span>
          <strong>{{ tong_khach_nam.toLocaleString() }}</strong>
        </div>
      </article>

      <article class="stats-card">
        <div class="stats-card__icon stats-card__icon--sky">
          <i class="fas fa-cake-candles"></i>
        </div>
        <div>
          <span>Tuổi trung bình</span>
          <strong>{{ nhan_tuoi_trung_binh }}</strong>
        </div>
      </article>
    </section>

    <section class="filter-bar">
      <label class="filter-search">
        <i class="fas fa-search"></i>
        <input
          v-model.trim="tu_khoa"
          type="text"
          placeholder="Tìm theo tên, email, số điện thoại, mã khách hàng..."
        >
      </label>
      
      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <input type="date" v-model="tu_ngay" class="filter-select" style="min-width: 140px;" />
        <span style="color: #64748b; font-weight: 600;">-</span>
        <input type="date" v-model="den_ngay" class="filter-select" style="min-width: 140px;" />
      </div>

      <select v-model="loc_gioi_tinh" class="filter-select">
        <option value="all">Giới tính: Tất cả</option>
        <option value="male">Nam</option>
        <option value="female">Nữ</option>
      </select>

      <select v-model="loc_trang_thai" class="filter-select">
        <option value="all">Trạng thái: Tất cả</option>
        <option value="active">Hoạt động</option>
        <option value="blocked">Bị khóa</option>
      </select>

      <button class="filter-apply" type="button" @click="apDungLoc">
        Áp dụng bộ lọc
      </button>
    </section>

    <div v-if="thong_bao_loi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thong_bao_loi }}</span>
    </div>

    <section class="content-grid">
      <article class="table-card">
        <div class="table-head">
          <div>
            <h2>Danh bạ khách hàng</h2>
            <p>{{ danh_sach_da_loc.length }} khách hàng phù hợp điều kiện hiện tại.</p>
          </div>
          <span class="table-chip">{{ dang_tai ? "Đồng bộ..." : "Dữ liệu trực tiếp" }}</span>
        </div>

        <div class="customer-table">
          <div class="customer-table__row customer-table__row--head">
            <span>Khách hàng</span>
            <span>Mã KH</span>
            <span>Giới tính</span>
            <span>Tuổi</span>
            <span>Trạng thái</span>
            <span>Ngày tạo</span>
          </div>

          <div
            v-for="khach_hang in danh_sach_phan_trang"
            :key="khach_hang.id"
            class="customer-table__row"
            :class="{ 'is-selected': khach_hang_dang_chon && khach_hang_dang_chon.id === khach_hang.id }"
            @click="ma_khach_hang_chon = khach_hang.id"
          >
            <div class="customer-person">
              <div class="customer-person__avatar" :style="{ background: khach_hang.avatarGradient }">
                {{ khach_hang.initials }}
              </div>
              <div>
                <strong>{{ khach_hang.name }}</strong>
                <small>{{ khach_hang.email }}</small>
                <small>{{ khach_hang.phone }}</small>
              </div>
            </div>

            <div class="customer-id">
              <span>KH</span>
              <strong>{{ khach_hang.id }}</strong>
            </div>

            <span :class="['pill', khach_hang.gender === 'Nam' ? 'pill--sky' : 'pill--rose']">
              {{ khach_hang.gender }}
            </span>

            <span class="muted">{{ khach_hang.ageLabel }}</span>

            <span :class="['pill', khach_hang.isActive ? 'pill--green' : 'pill--slate']">
              {{ khach_hang.isActive ? "Hoạt động" : "Bị khóa" }}
            </span>

            <span class="muted">{{ khach_hang.createdAtLabel }}</span>
          </div>
        </div>

        <footer class="table-footer">
          <span>
            Hiển thị {{ bat_dau_footer }}-{{ ket_thuc_footer }} trên tổng {{ danh_sach_da_loc.length.toLocaleString() }} khách hàng
          </span>

          <div class="pagination">
            <button class="page-btn" type="button" :disabled="trang === 1" @click="trang = Math.max(1, trang - 1)">
              <i class="fas fa-angle-left"></i>
            </button>
            <button
              v-for="so_trang in cac_trang_hien_thi"
              :key="so_trang"
              type="button"
              :class="['page-btn', { 'is-active': trang === so_trang }]"
              @click="trang = so_trang"
            >
              {{ so_trang }}
            </button>
            <button
              class="page-btn"
              type="button"
              :disabled="trang === tong_so_trang"
              @click="trang = Math.min(tong_so_trang, trang + 1)"
            >
              <i class="fas fa-angle-right"></i>
            </button>
          </div>
        </footer>
      </article>

      <aside class="profile-card">
        <div v-if="khach_hang_dang_chon">
          <div class="profile-card__header">
            <div class="profile-card__avatar" :style="{ background: khach_hang_dang_chon.avatarGradient }">
              {{ khach_hang_dang_chon.initials }}
            </div>
            <div>
              <p class="profile-card__eyebrow">Xem nhanh khách hàng</p>
              <h2>{{ khach_hang_dang_chon.name }}</h2>
              <div class="profile-card__tags">
                <span class="tag tag--slate">KH {{ khach_hang_dang_chon.id }}</span>
                <span :class="['tag', khach_hang_dang_chon.isActive ? 'tag--green' : 'tag--slate']">
                  {{ khach_hang_dang_chon.isActive ? "Hoạt động" : "Bị khóa" }}
                </span>
              </div>
            </div>
          </div>

          <div class="profile-card__body">
            <div class="profile-row">
              <span>Email</span>
              <strong>{{ khach_hang_dang_chon.email }}</strong>
            </div>
            <div class="profile-row">
              <span>Số điện thoại</span>
              <strong>{{ khach_hang_dang_chon.phone }}</strong>
            </div>
            <div class="profile-row">
              <span>Giới tính</span>
              <strong>{{ khach_hang_dang_chon.gender }}</strong>
            </div>
            <div class="profile-row">
              <span>Tuổi</span>
              <strong>{{ khach_hang_dang_chon.ageLabel }}</strong>
            </div>
            <div class="profile-row">
              <span>Ngày tạo</span>
              <strong>{{ khach_hang_dang_chon.createdAtLabel }}</strong>
            </div>
          </div>

          <div class="profile-card__actions">
            <router-link class="action-button action-button--primary" :to="`/admin/customers/${khach_hang_dang_chon.id}`">
              Xem chi tiết
            </router-link>
            <router-link class="action-button action-button--ghost" :to="`/admin/customers/${khach_hang_dang_chon.id}/edit`">
              Chỉnh sửa
            </router-link>
            <button class="action-button action-button--danger" type="button" @click="xoaKhachHang(khach_hang_dang_chon)">
              Xóa khách hàng
            </button>
          </div>
        </div>
        <div v-else class="profile-card profile-card--empty">
          <p>Chọn một khách hàng để xem nhanh thông tin.</p>
        </div>
      </aside>

    </section>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient.js";
import { showConfirm } from "../../../services/appDialog.js";
import { createToaster } from "@meforma/vue-toaster";
const toaster = createToaster({ position: "top-right" });

async function goiDuLieu(url, { method = "GET", body, params, headers } = {}) {
  const phanHoi = await goiApi(url, { method, body, params, headers });
  const duLieu = await phanHoi.json();

  if (!phanHoi.ok) {
    const loi = new Error(duLieu?.message || "Không thể hoàn tất yêu cầu.");
    loi.response = { data: duLieu, status: phanHoi.status };
    throw loi;
  }

  return { data: duLieu, status: phanHoi.status };
}

export default {
  name: "DanhSachKhachHang",
  data() {
    return {
      dang_tai: false,
      thong_bao_loi: "",
      danh_sach: [],
      tu_khoa: "",
      loc_gioi_tinh: "all",
      loc_trang_thai: "all",
      tu_ngay: "",
      den_ngay: "",
      trang: 1,
      so_luong_moi_trang: 6,
      thoi_gian_ap_dung: 0,
      ma_khach_hang_chon: "",
    };
  },
  computed: {
    danh_sach_da_loc() {
      const _ = this.thoi_gian_ap_dung;
      return this.danh_sach.filter((khach_hang) => {
        const tim_kiem_haystack = [khach_hang.id, khach_hang.name, khach_hang.email, khach_hang.phone].join(" ").toLowerCase();
        const khop_tim_kiem = tim_kiem_haystack.includes(this.tu_khoa.toLowerCase());
        const khop_gioi_tinh =
          this.loc_gioi_tinh === "all" ||
          khach_hang.genderKey === this.loc_gioi_tinh;
        const khop_trang_thai =
          this.loc_trang_thai === "all" ||
          (this.loc_trang_thai === "active" && khach_hang.isActive) ||
          (this.loc_trang_thai === "blocked" && !khach_hang.isActive);
          
        let khop_ngay = true;
        if (this.tu_ngay && khach_hang.createdAt) {
           const fromD = new Date(this.tu_ngay); fromD.setHours(0,0,0,0);
           if (new Date(khach_hang.createdAt) < fromD) khop_ngay = false;
        }
        if (this.den_ngay && khach_hang.createdAt) {
           const toD = new Date(this.den_ngay); toD.setHours(23,59,59,999);
           if (new Date(khach_hang.createdAt) > toD) khop_ngay = false;
        }

        return khop_tim_kiem && khop_gioi_tinh && khop_trang_thai && khop_ngay;
      });
    },
    tong_so_trang() {
      return Math.max(1, Math.ceil(this.danh_sach_da_loc.length / this.so_luong_moi_trang));
    },
    chi_so_bat_dau() {
      return Math.min((this.trang - 1) * this.so_luong_moi_trang, Math.max(this.danh_sach_da_loc.length - 1, 0));
    },
    chi_so_ket_thuc() {
      return Math.min(this.trang * this.so_luong_moi_trang, this.danh_sach_da_loc.length);
    },
    danh_sach_phan_trang() {
      return this.danh_sach_da_loc.slice(this.chi_so_bat_dau, this.chi_so_ket_thuc);
    },
    khach_hang_dang_chon() {
      return this.danh_sach_da_loc.find((khach_hang) => khach_hang.id === this.ma_khach_hang_chon) || this.danh_sach_phan_trang[0] || null;
    },
    cac_trang_hien_thi() {
      const cac_trang = [];
      const bat_dau = Math.max(1, this.trang - 1);
      const ket_thuc = Math.min(this.tong_so_trang, bat_dau + 2);
      for (let i = bat_dau; i <= ket_thuc; i += 1) {
        cac_trang.push(i);
      }
      return cac_trang;
    },
    bat_dau_footer() {
      return this.danh_sach_da_loc.length ? this.chi_so_bat_dau + 1 : 0;
    },
    ket_thuc_footer() {
      return this.danh_sach_da_loc.length ? this.chi_so_ket_thuc : 0;
    },
    tong_dang_hoat_dong() {
      return this.danh_sach.filter((khach_hang) => khach_hang.isActive).length;
    },
    tong_khach_nu() {
      return this.danh_sach.filter((khach_hang) => khach_hang.genderKey === "female").length;
    },
    tong_khach_nam() {
      return this.danh_sach.filter((khach_hang) => khach_hang.genderKey === "male").length;
    },
    nhan_tuoi_trung_binh() {
      const cac_do_tuoi = this.danh_sach.map((khach_hang) => khach_hang.age).filter((tuoi) => Number.isFinite(tuoi));
      if (!cac_do_tuoi.length) return "--";
      const trung_binh = cac_do_tuoi.reduce((tong, tuoi) => tong + tuoi, 0) / cac_do_tuoi.length;
      return `${Math.round(trung_binh)}`;
    },
  },
  watch: {
    tong_so_trang() {
      if (this.trang > this.tong_so_trang) {
        this.trang = this.tong_so_trang;
      }
    },
    danh_sach_phan_trang: {
      handler(items) {
        if (!items.length) {
          this.ma_khach_hang_chon = "";
          return;
        }
        if (!items.some((item) => item.id === this.ma_khach_hang_chon)) {
          this.ma_khach_hang_chon = items[0].id;
        }
      },
      immediate: true,
    },
  },
  methods: {
    async layDanhSach() {
      this.dang_tai = true;
      try {
        const res = await goiDuLieu("/api/khach-hang");
        if (res.data.status || res.data.success) {
          this.danh_sach = res.data.data.map((item, index) => this.mappingKhachHang(item, index));
          this.trang = 1;
          this.thoi_gian_ap_dung = Date.now();
        } else {
          toaster.error("Không thể tải danh sách khách hàng: " + res.data.message);
        }
      } catch (err) {
        console.error(err);
        toaster.error("Lỗi hệ thống khi tải dữ liệu!");
      } finally {
        this.dang_tai = false;
      }
    },

    async xoaKhachHang(khach_hang) {
      const confirmed = await showConfirm({
        title: "Xác nhận xóa",
        message: `Bạn có chắc chắn muốn xóa khách hàng "${khach_hang.name}" không?`,
        tone: "danger",
        confirmText: "Xóa",
      });
      if (confirmed) {
        try {
          const res = await goiDuLieu("/api/admin/khach-hang/" + khach_hang.id, {
            method: "DELETE",
          });
          if (res.data.status || res.data.success) {
            toaster.success("Thông báo: " + res.data.message);
            this.layDanhSach();
          } else {
            toaster.error("Lỗi: " + res.data.message);
          }
        } catch (err) {
          console.error(err);
          toaster.error("Lỗi hệ thống khi xóa khách hàng!");
        }
      }
    },

    apDungLoc() {
      this.trang = 1;
      this.thoi_gian_ap_dung = Date.now();
    },

    mappingKhachHang(item, index) {
      const tuoi = this.tinhTuoi(item.Ngay_sinh);
      const genderKey = item.Gioi_tinh ? "male" : "female";
      return {
        id: String(item.Ma_khach_hang ?? item.id ?? index + 1),
        name: item.Ho_va_ten || "Khách hàng",
        email: item.Email || "--",
        phone: item.so_dien_thoai || "--",
        gender: genderKey === "male" ? "Nam" : "Nữ",
        genderKey,
        age: tuoi,
        ageLabel: Number.isFinite(tuoi) ? `${tuoi} tuổi` : "--",
        isActive: this.chuanHoaBoolean(item.is_block),
        birthDateLabel: this.dinhDangNgay(item.Ngay_sinh),
        createdAt: item.created_at,
        createdAtLabel: this.dinhDangNgay(item.created_at),
        updatedAtLabel: this.dinhDangNgay(item.updated_at),
        initials: this.taoTenVietTat(item.Ho_va_ten),
        avatarGradient: this.layMauBacGiao(index),
      };
    },

    dinhDangNgay(value) {
      if (!value) return "--";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "--";
      return date.toLocaleDateString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    },

    tinhTuoi(value) {
      if (!value) return null;
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return null;
      const today = new Date();
      let tuoi = today.getFullYear() - date.getFullYear();
      const monthDiff = today.getMonth() - date.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < date.getDate())) {
        tuoi -= 1;
      }
      return tuoi;
    },

    taoTenVietTat(name) {
      return String(name || "KH")
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() || "")
        .join("");
    },

    layMauBacGiao(index) {
      const gradients = [
        "linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)",
        "linear-gradient(135deg, #f59e0b 0%, #ea580c 100%)",
        "linear-gradient(135deg, #14b8a6 0%, #0f766e 100%)",
        "linear-gradient(135deg, #ec4899 0%, #be185d 100%)",
      ];
      return gradients[index % gradients.length];
    },

    chuanHoaBoolean(value, fallback = false) {
      if (typeof value === "boolean") return value;
      if (typeof value === "number") return value !== 0;
      if (typeof value === "string") {
        const normalized = value.trim().toLowerCase();
        if (["1", "true", "yes", "on"].includes(normalized)) return true;
        if (["0", "false", "no", "off", ""].includes(normalized)) return false;
      }
      return fallback;
    },
  },
  mounted() {
    this.layDanhSach();
  },
};
</script>

<style scoped>
.customer-page {
  min-height: 100%;
  padding: 2rem 2.25rem;
  background:
    radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 24%),
    linear-gradient(180deg, #f7f9fe 0%, #eef3fb 100%);
}

.customer-page__hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.customer-page__eyebrow {
  margin: 0 0 0.4rem;
  color: #2453ff;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.82rem;
  font-weight: 900;
}

.customer-page__hero h1 {
  margin: 0;
  color: #111827;
  font-size: clamp(2.35rem, 4vw, 3.2rem);
  line-height: 1;
  font-weight: 900;
  letter-spacing: -0.05em;
}

.customer-page__subtitle {
  margin: 0.8rem 0 0;
  color: #5f7191;
  font-size: 1.05rem;
  max-width: 720px;
}

.customer-page__hero-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.ghost-button {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  min-height: 3.2rem;
  padding: 0 1.25rem;
  border-radius: 1rem;
  border: 1px solid #dbe4f0;
  background: rgba(255, 255, 255, 0.88);
  color: #334155;
  font-weight: 800;
}

.ghost-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.primary-button {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  min-height: 3.2rem;
  padding: 0 1.25rem;
  border-radius: 1rem;
  background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%);
  color: #ffffff;
  font-weight: 800;
  text-decoration: none;
  transition: transform 0.2s ease;
}

.primary-button:hover {
  transform: translateY(-1px);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

.stats-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.45rem;
  border-radius: 1.6rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
}

.stats-card__icon {
  width: 4.25rem;
  height: 4.25rem;
  border-radius: 1.25rem;
  display: grid;
  place-items: center;
  font-size: 1.45rem;
}

.stats-card__icon--violet { background: #efefff; color: #4f25f4; }
.stats-card__icon--green { background: #eaf9f1; color: #08986c; }
.stats-card__icon--amber { background: #fff7e8; color: #d97706; }
.stats-card__icon--blue { background: #edf4ff; color: #2563eb; }
.stats-card__icon--sky { background: #eaf4ff; color: #0369a1; }

.stats-card span {
  display: block;
  color: #60708d;
  font-weight: 700;
}

.stats-card strong {
  display: block;
  margin-top: 0.35rem;
  color: #111b39;
  font-size: 1.75rem;
  line-height: 1;
  font-weight: 900;
}

.filter-bar {
  display: grid;
  grid-template-columns: minmax(0, 2fr) repeat(2, minmax(180px, 0.8fr)) auto;
  gap: 1rem;
  padding: 1.15rem;
  border-radius: 1.6rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 14px 28px rgba(15, 23, 42, 0.04);
  margin-bottom: 1.5rem;
}

.filter-search,
.filter-select {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  min-height: 3.45rem;
  padding: 0 1rem;
  border-radius: 1rem;
  background: #f7f8fc;
  border: 1px solid #eef1f7;
  color: #324053;
}

.filter-search i {
  color: #8d99af;
}

.filter-search input,
.filter-select {
  border: 0;
  outline: none;
  font-size: 1rem;
}

.filter-search input {
  width: 100%;
  background: transparent;
}

.filter-apply {
  min-height: 3.45rem;
  padding: 0 1.45rem;
  border: 0;
  border-radius: 1rem;
  background: #131b35;
  color: #ffffff;
  font-weight: 800;
}

.notice {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.1rem;
  border-radius: 1rem;
  margin-bottom: 1rem;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
}

.content-grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(320px, 0.95fr);
  gap: 1rem;
  align-items: start;
}

.table-card {
  padding: 1.35rem 1.4rem 1rem;
  border-radius: 2rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
}

.table-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.table-head h2 {
  margin: 0;
  color: #111827;
  font-size: 1.35rem;
  font-weight: 900;
}

.table-head p {
  margin: 0.4rem 0 0;
  color: #64748b;
}

.table-chip {
  padding: 0.7rem 1rem;
  border-radius: 0.95rem;
  background: #eef2ff;
  color: #4338ca;
  font-weight: 800;
}

.customer-table {
  display: grid;
}

.customer-table__row {
  display: grid;
  grid-template-columns: minmax(280px, 2fr) 92px 110px 90px 120px 120px;
  gap: 1rem;
  align-items: center;
  padding: 1.2rem 0;
  border-top: 1px solid #eef2f7;
  transition: background-color 0.2s ease, border-radius 0.2s ease;
}

.customer-table__row:not(.customer-table__row--head) {
  cursor: pointer;
}

.customer-table__row.is-selected {
  background: #f8fbff;
}

.customer-table__row--head {
  border-top: 0;
  padding-top: 0.1rem;
  color: #637594;
  font-size: 0.82rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.customer-person {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.customer-person__avatar {
  width: 3.4rem;
  height: 3.4rem;
  border-radius: 1rem;
  display: grid;
  place-items: center;
  color: #ffffff;
  font-weight: 900;
  letter-spacing: 0.04em;
}

.customer-person strong {
  display: block;
  color: #111b39;
  font-size: 1.02rem;
  line-height: 1.35;
}

.customer-person small {
  display: block;
  color: #66768f;
  line-height: 1.45;
}

.customer-id {
  display: inline-flex;
  flex-direction: row;
  gap: 0.35rem;
  min-width: 4.25rem;
  padding: 0.45rem 0.8rem;
  border-radius: 0.75rem;
  background: #f3f5fa;
  color: #60708d;
  font-size: 0.9rem;
  font-weight: 700;
  align-items: center;
  justify-content: center;
  text-align: center;
  white-space: nowrap;
}

.customer-id strong {
  color: #5b6c8b;
}

.pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 1.95rem;
  width: fit-content;
  padding: 0 0.95rem;
  border-radius: 999px;
  font-weight: 800;
}

.pill--green { background: #eaf9f1; color: #08986c; }
.pill--slate { background: #eef2f7; color: #475569; }
.pill--sky { background: #eaf4ff; color: #0369a1; }
.pill--rose { background: #fff1f2; color: #be123c; }

.muted {
  color: #66768f;
}

.actions {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.icon-btn {
  width: 2.2rem;
  height: 2.2rem;
  border: 0;
  border-radius: 0.8rem;
  background: transparent;
  color: #8b97ae;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.icon-btn:hover {
  background: #f4f6fb;
  color: #344054;
}

.icon-btn--danger {
  color: #ef4444;
}

.icon-btn--danger:hover {
  background: #fef2f2;
  color: #dc2626;
}

.table-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding-top: 1.2rem;
}

.table-footer span {
  color: #64748b;
  font-size: 1rem;
}

.profile-card {
  border-radius: 2rem;
  background: #ffffff;
  border: 1px solid #ebeef5;
  box-shadow: 0 18px 34px rgba(15, 23, 42, 0.05);
  padding: 1.25rem;
  position: sticky;
  top: 1rem;
}

.profile-card--empty {
  min-height: 200px;
  display: grid;
  place-items: center;
  color: #64748b;
  text-align: center;
}

.profile-card__header {
  display: flex;
  align-items: flex-start;
  gap: 0.9rem;
}

.profile-card__avatar {
  width: 3.8rem;
  height: 3.8rem;
  border-radius: 1rem;
  display: grid;
  place-items: center;
  color: #fff;
  font-weight: 900;
  font-size: 1.05rem;
}

.profile-card__eyebrow {
  margin: 0 0 0.3rem;
  color: #4f46e5;
  font-size: 0.75rem;
  font-weight: 900;
  letter-spacing: 0.09em;
  text-transform: uppercase;
}

.profile-card__header h2 {
  margin: 0;
  font-size: 1.45rem;
  color: #111827;
}

.profile-card__tags {
  margin-top: 0.6rem;
  display: flex;
  gap: 0.45rem;
  flex-wrap: wrap;
}

.tag {
  display: inline-flex;
  align-items: center;
  min-height: 1.9rem;
  padding: 0 0.75rem;
  border-radius: 999px;
  font-size: 0.84rem;
  font-weight: 800;
}

.tag--slate {
  background: #eef2f7;
  color: #475569;
}

.tag--green {
  background: #eaf9f1;
  color: #08986c;
}

.profile-card__body {
  margin-top: 1rem;
  display: grid;
  gap: 0.7rem;
}

.profile-row {
  display: flex;
  justify-content: space-between;
  gap: 0.8rem;
  padding-bottom: 0.6rem;
  border-bottom: 1px solid #eef2f7;
}

.profile-row span {
  color: #64748b;
}

.profile-row strong {
  color: #0f172a;
  text-align: right;
}

.profile-card__actions {
  margin-top: 1rem;
  display: grid;
  gap: 0.55rem;
}

.action-button {
  min-height: 2.65rem;
  border: none;
  border-radius: 0.85rem;
  display: inline-flex;
  justify-content: center;
  align-items: center;
  text-decoration: none;
  font-weight: 800;
}

.action-button--primary {
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
}

.action-button--ghost {
  background: #eef2f7;
  color: #334155;
}

.action-button--danger {
  background: #fef2f2;
  color: #dc2626;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.page-btn {
  width: 3rem;
  height: 3rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  border: 1px solid #eceff6;
  border-radius: 1rem;
  background: #ffffff;
  color: #55657f;
  font-weight: 800;
  font-size: 0.95rem;
  font-variant-numeric: tabular-nums;
}

.page-btn.is-active {
  border-color: transparent;
  background: linear-gradient(135deg, #4f25f4 0%, #3d11d4 100%);
  color: #ffffff;
  box-shadow: 0 12px 24px rgba(79, 37, 244, 0.22);
}

.page-btn i {
  font-size: 0.9rem;
  line-height: 1;
}

@media (max-width: 1360px) {
  .content-grid {
    grid-template-columns: 1fr;
  }

  .profile-card {
    position: static;
  }
}

@media (max-width: 1280px) {
  .stats-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .filter-bar {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .customer-page {
    padding: 1rem;
  }

  .customer-page__hero {
    flex-direction: column;
    align-items: flex-start;
  }

  .customer-table__row,
  .customer-table__row--head {
    grid-template-columns: minmax(220px, 2fr) 92px 110px 110px;
  }

  .customer-table__row span:nth-child(3),
  .customer-table__row span:nth-child(4),
  .customer-table__row--head span:nth-child(3),
  .customer-table__row--head span:nth-child(4) {
    display: none;
  }
}

@media (max-width: 768px) {
  .stats-grid,
  .filter-bar {
    grid-template-columns: 1fr;
  }

  .customer-table__row--head {
    display: none;
  }

  .customer-table__row {
    grid-template-columns: 1fr;
    gap: 0.8rem;
  }

  .table-footer {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>

