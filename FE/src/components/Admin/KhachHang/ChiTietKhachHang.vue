<template>
  <div class="customer-detail-page">
    <section class="detail-hero">
      <button class="back-button" type="button" @click="quayLai">
        <i class="fas fa-arrow-left"></i>
      </button>

      <div class="detail-hero__copy">
        <p class="detail-hero__eyebrow">Quản trị khách hàng</p>
        <h1>Chi tiết khách hàng</h1>
        <p>
          Xem hồ sơ đầy đủ, trạng thái tài khoản và thông tin liên hệ của khách hàng theo mã
          <strong>{{ nhan_ma_khach_hang }}</strong>.
        </p>
      </div>

      <div class="detail-hero__actions">
        <button class="ghost-button" type="button" @click="layThongTinKhachHang" :disabled="dang_tai">
          <i class="fas fa-rotate-right"></i>
          <span>{{ dang_tai ? "Đang tải..." : "Tải lại" }}</span>
        </button>

        <router-link class="primary-link" :to="`/admin/customers/${ma_khach_hang}/edit`">
          <i class="fas fa-pen"></i>
          <span>Chỉnh sửa</span>
        </router-link>
      </div>
    </section>

    <div v-if="thong_bao_loi" class="notice notice--error">
      <i class="fas fa-circle-exclamation"></i>
      <span>{{ thong_bao_loi }}</span>
    </div>

    <section v-if="dang_tai" class="loading-shell">
      <div class="loading-card"></div>
      <div class="loading-grid">
        <div class="loading-card"></div>
        <div class="loading-card"></div>
      </div>
    </section>

    <template v-else-if="thong_tin_khach_hang">
      <section class="profile-banner">
        <div class="profile-banner__main">
          <div class="profile-banner__avatar" :style="{ background: thong_tin_khach_hang.avatarGradient }">
            {{ thong_tin_khach_hang.initials }}
          </div>

          <div class="profile-banner__copy">
            <p class="profile-banner__eyebrow">Hồ sơ khách hàng</p>
            <h2>{{ thong_tin_khach_hang.name }}</h2>
            <div class="profile-banner__meta">
              <span class="tag tag--slate">{{ thong_tin_khach_hang.code }}</span>
              <span :class="['tag', thong_tin_khach_hang.is_block ? 'tag--green' : 'tag--rose']">
                {{ thong_tin_khach_hang.statusLabel }}
              </span>
              <span :class="['tag', thong_tin_khach_hang.genderKey === 'male' ? 'tag--sky' : 'tag--amber']">
                {{ thong_tin_khach_hang.gender }}
              </span>
            </div>
          </div>
        </div>

        <div class="profile-banner__side">
          <div class="hero-stat">
            <span>Tuổi</span>
            <strong>{{ thong_tin_khach_hang.ageLabel }}</strong>
          </div>
          <div class="hero-stat">
            <span>Ngày tạo</span>
            <strong>{{ thong_tin_khach_hang.createdAtLabel }}</strong>
          </div>
          <div class="hero-stat">
            <span>Cập nhật gần nhất</span>
            <strong>{{ thong_tin_khach_hang.updatedAtLabel }}</strong>
          </div>
        </div>
      </section>

      <section class="detail-grid">
        <article class="card">
          <header class="card__head">
            <div class="card__icon">
              <i class="fas fa-address-card"></i>
            </div>
            <div>
              <h3>Thông tin cá nhân</h3>
              <p>Chi tiết định danh và dữ liệu hồ sơ cơ bản.</p>
            </div>
          </header>

          <div class="info-grid">
            <div class="info-row">
              <span>Mã khách hàng</span>
              <strong>{{ thong_tin_khach_hang.code }}</strong>
            </div>
            <div class="info-row">
              <span>Họ và tên</span>
              <strong>{{ thong_tin_khach_hang.name }}</strong>
            </div>
            <div class="info-row">
              <span>Giới tính</span>
              <strong>{{ thong_tin_khach_hang.gender }}</strong>
            </div>
            <div class="info-row">
              <span>Ngày sinh</span>
              <strong>{{ thong_tin_khach_hang.birthDateLabel }}</strong>
            </div>
            <div class="info-row">
              <span>Tuổi hiện tại</span>
              <strong>{{ thong_tin_khach_hang.ageLabel }}</strong>
            </div>
            <div class="info-row">
              <span>Trạng thái tài khoản</span>
              <strong>{{ thong_tin_khach_hang.statusLabel }}</strong>
            </div>
          </div>
        </article>

        <article class="card">
          <header class="card__head">
            <div class="card__icon card__icon--blue">
              <i class="fas fa-envelope-open-text"></i>
            </div>
            <div>
              <h3>Liên hệ và hệ thống</h3>
              <p>Theo dõi email, điện thoại và mốc thời gian cập nhật.</p>
            </div>
          </header>

          <div class="info-grid">
            <div class="info-row">
              <span>Email</span>
              <strong>{{ thong_tin_khach_hang.email }}</strong>
            </div>
            <div class="info-row">
              <span>Số điện thoại</span>
              <strong>{{ thong_tin_khach_hang.phone }}</strong>
            </div>
            <div class="info-row">
              <span>Ngày tạo tài khoản</span>
              <strong>{{ thong_tin_khach_hang.createdAtLabel }}</strong>
            </div>
            <div class="info-row">
              <span>Cập nhật lần cuối</span>
              <strong>{{ thong_tin_khach_hang.updatedAtLabel }}</strong>
            </div>
            <div class="info-row">
              <span>Mã nội bộ</span>
              <strong>{{ thong_tin_khach_hang.rawId }}</strong>
            </div>
            <div class="info-row">
              <span>Loại tài khoản</span>
              <strong>Khách hàng</strong>
            </div>
          </div>

        </article>

        <article class="card card--wide">
          <header class="card__head">
            <div class="card__icon card__icon--violet">
              <i class="fas fa-lightbulb"></i>
            </div>
            <div>
              <h3>Gợi ý thao tác tiếp theo</h3>
            </div>
          </header>

          <div class="next-steps">
            <router-link class="action-tile action-tile--primary" :to="`/admin/customers/${thong_tin_khach_hang.id}/edit`">
              <i class="fas fa-user-pen"></i>
              <div>
                <strong>Chỉnh sửa hồ sơ</strong>
                <span>Cập nhật họ tên, giới tính, ngày sinh và số điện thoại.</span>
              </div>
            </router-link>

            <router-link class="action-tile" to="/admin/customers">
              <i class="fas fa-table-list"></i>
              <div>
                <strong>Quay về danh sách</strong>
                <span>Tiếp tục xem các tài khoản khách hàng khác trong bảng quản trị.</span>
              </div>
            </router-link>
          </div>
        </article>
      </section>
    </template>

    <section v-else class="empty-state">
      <i class="fas fa-user-slash"></i>
      <h2>Không tìm thấy khách hàng</h2>
      <p>Mã khách hàng này hiện không có trong hệ thống hoặc đã bị thay đổi.</p>
      <router-link class="primary-link" to="/admin/customers">Quay về danh sách</router-link>
    </section>
  </div>
</template>

<script>
import { goiApi } from "../../../services/httpClient.js";
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
  name: "ChiTietKhachHang",
  data() {
    return {
      dang_tai: false,
      thong_bao_loi: "",
      thong_tin_khach_hang: null,
    };
  },
  computed: {
    ma_khach_hang() {
      return this.chuanHoaMaKhachHang(this.$route.params.id);
    },
    nhan_ma_khach_hang() {
      return this.ma_khach_hang ? `KH ${this.ma_khach_hang}` : "--";
    },
  },
  watch: {
    "$route.params.id": {
      immediate: true,
      handler() {
        this.layThongTinKhachHang();
      },
    },
  },
  methods: {
    chuanHoaMaKhachHang(value) {
      return String(value || "")
        .trim()
        .replace(/^KH[-\s]*/i, "");
    },
    taoTenVietTat(name) {
      return String(name || "KH")
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() || "")
        .join("");
    },
    layMauBacGiao(id) {
      const gradients = [
        "linear-gradient(135deg, #4338ca 0%, #2563eb 100%)",
        "linear-gradient(135deg, #0891b2 0%, #0f766e 100%)",
        "linear-gradient(135deg, #d97706 0%, #ea580c 100%)",
        "linear-gradient(135deg, #db2777 0%, #9333ea 100%)",
      ];
      const numericId = Number.parseInt(id, 10);
      const index = Number.isFinite(numericId) ? numericId % gradients.length : 0;
      return gradients[index];
    },
    dinhDangNgayHienThi(value) {
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
    layDuLieuKhachHang(payload) {
      return payload?.data?.data || payload?.data || payload;
    },
    mappingKhachHang(item) {
      const id = String(item?.Ma_khach_hang ?? item?.id ?? "");
      const is_block = this.chuanHoaBoolean(item?.is_block);
      const age = this.tinhTuoi(item?.Ngay_sinh);
      const genderKey = item?.Gioi_tinh ? "male" : "female";

      return {
        id,
        rawId: id || "--",
        code: id ? `KH ${id}` : "--",
        name: item?.Ho_va_ten || "Khách hàng",
        email: item?.Email || "--",
        phone: item?.so_dien_thoai || "--",
        gender: genderKey === "male" ? "Nam" : "Nữ",
        genderKey,
        birthDateLabel: this.dinhDangNgayHienThi(item?.Ngay_sinh),
        ageLabel: Number.isFinite(age) ? `${age} tuổi` : "--",
        is_block,
        statusLabel: is_block ? "Hoạt động" : "Bị khóa",
        createdAtLabel: this.dinhDangNgayHienThi(item?.created_at),
        updatedAtLabel: this.dinhDangNgayHienThi(item?.updated_at),
        initials: this.taoTenVietTat(item?.Ho_va_ten),
        avatarGradient: this.layMauBacGiao(id),
      };
    },
    async layThongTinKhachHang() {
      if (!this.ma_khach_hang) {
        this.thong_tin_khach_hang = null;
        this.thong_bao_loi = "Thiếu mã khách hàng để tải dữ liệu.";
        return;
      }

      this.dang_tai = true;
      this.thong_bao_loi = "";

      try {
        const res = await goiDuLieu(`/api/khach-hang/profile/${encodeURIComponent(this.ma_khach_hang)}`);
        const record = this.layDuLieuKhachHang(res);
        this.thong_tin_khach_hang = record ? this.mappingKhachHang(record) : null;
      } catch (err) {
        console.error(err);
        this.thong_tin_khach_hang = null;
        this.thong_bao_loi = err.response?.data?.message || "Không thể tải chi tiết khách hàng.";
      } finally {
        this.dang_tai = false;
      }
    },
    quayLai() {
      this.$router.push("/admin/customers");
    },
  },
};
</script>

<style scoped>
.customer-detail-page {
  min-height: calc(100vh - 120px);
  padding: 1.5rem;
  background:
    radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 28%),
    radial-gradient(circle at top right, rgba(249, 115, 22, 0.08), transparent 24%),
    #f5f7fb;
}

.detail-hero,
.profile-banner,
.card,
.empty-state,
.loading-card {
  border: 1px solid #e6ebf4;
  background: rgba(255, 255, 255, 0.92);
  box-shadow: 0 22px 55px rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(10px);
}

.detail-hero {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 1rem;
  align-items: center;
  padding: 1.4rem 1.5rem;
  border-radius: 1.75rem;
}

.back-button,
.ghost-button,
.primary-link {
  min-height: 3rem;
  border-radius: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  font-weight: 800;
  text-decoration: none;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.back-button {
  width: 3rem;
  border: none;
  background: #eef3ff;
  color: #2952d3;
}

.ghost-button {
  padding: 0 1rem;
  border: 1px solid #dbe4f0;
  background: #ffffff;
  color: #334155;
}

.primary-link {
  padding: 0 1.2rem;
  border: none;
  background: linear-gradient(135deg, #3b82f6 0%, #4338ca 100%);
  color: #ffffff;
}

.back-button:hover,
.ghost-button:hover,
.primary-link:hover {
  transform: translateY(-1px);
}

.detail-hero__eyebrow,
.profile-banner__eyebrow {
  margin: 0 0 0.45rem;
  color: #c86b1a;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.detail-hero__copy h1,
.profile-banner__copy h2,
.card__head h3,
.empty-state h2 {
  margin: 0;
  color: #172033;
}

.detail-hero__copy p,
.card__head p,
.empty-state p {
  margin: 0.45rem 0 0;
  color: #5f6c82;
  line-height: 1.7;
}

.detail-hero__actions {
  display: flex;
  gap: 0.8rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.notice {
  margin-top: 1rem;
  padding: 0.95rem 1.1rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  font-weight: 700;
}

.notice--error {
  background: #fff1f2;
  color: #be123c;
  border: 1px solid #fecdd3;
}

.loading-shell {
  margin-top: 1rem;
  display: grid;
  gap: 1rem;
}

.loading-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.loading-card {
  min-height: 220px;
  border-radius: 1.5rem;
  position: relative;
  overflow: hidden;
}

.loading-card::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
  transform: translateX(-100%);
  animation: shimmer 1.4s infinite;
}

.profile-banner {
  margin-top: 1rem;
  padding: 1.35rem;
  border-radius: 1.75rem;
  display: grid;
  grid-template-columns: minmax(0, 1.6fr) minmax(280px, 0.9fr);
  gap: 1rem;
}

.profile-banner__main {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.profile-banner__avatar {
  width: 5.5rem;
  height: 5.5rem;
  border-radius: 1.6rem;
  display: grid;
  place-items: center;
  color: #ffffff;
  font-size: 1.8rem;
  font-weight: 800;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28);
}

.profile-banner__meta {
  margin-top: 0.85rem;
  display: flex;
  gap: 0.65rem;
  flex-wrap: wrap;
}

.profile-banner__side {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.8rem;
}

.hero-stat,
.info-row,
.action-tile {
  border-radius: 1.2rem;
  border: 1px solid #e6ebf4;
  background: #fbfcff;
}

.hero-stat {
  padding: 1rem;
}

.hero-stat span,
.info-row span,
.action-tile span {
  display: block;
  color: #64748b;
}

.hero-stat strong,
.info-row strong,
.action-tile strong {
  display: block;
  margin-top: 0.35rem;
  color: #142033;
}

.detail-grid {
  margin-top: 1rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.card {
  padding: 1.35rem;
  border-radius: 1.6rem;
  min-height: 100%;
}

.card--wide {
  grid-column: 1 / -1;
}

.card__head {
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.card__icon {
  width: 3rem;
  height: 3rem;
  border-radius: 1rem;
  display: grid;
  place-items: center;
  color: #4338ca;
  background: #eef2ff;
}

.card__icon--blue {
  color: #0f766e;
  background: #ecfeff;
}

.card__icon--violet {
  color: #7c3aed;
  background: #f5f3ff;
}

.info-grid {
  display: grid;
  gap: 0.85rem;
}

.info-row {
  padding: 1rem 1.1rem;
}

.tag {
  min-height: 2.1rem;
  padding: 0 0.85rem;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 800;
}

.tag--green {
  background: #e9fbef;
  color: #0f9f57;
}

.tag--rose {
  background: #fff1f2;
  color: #e11d48;
}

.tag--sky {
  background: #eff6ff;
  color: #2563eb;
}

.tag--amber {
  background: #fff7ed;
  color: #d97706;
}

.tag--slate {
  background: #f1f5f9;
  color: #475569;
}

.next-steps {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

.action-tile {
  padding: 1rem 1.1rem;
  display: flex;
  gap: 0.9rem;
  align-items: flex-start;
  text-decoration: none;
  color: inherit;
}

.action-tile i {
  width: 2.7rem;
  height: 2.7rem;
  border-radius: 0.95rem;
  display: grid;
  place-items: center;
  background: #eef3ff;
  color: #3452d1;
}

.action-tile--primary {
  background: linear-gradient(135deg, #f5f8ff 0%, #ffffff 100%);
}

.empty-state {
  margin-top: 1rem;
  padding: 3rem 1.5rem;
  border-radius: 1.75rem;
  text-align: center;
}

.empty-state i {
  width: 4.5rem;
  height: 4.5rem;
  margin: 0 auto 1rem;
  border-radius: 1.4rem;
  display: grid;
  place-items: center;
  background: #f4f7fb;
  color: #3452d1;
  font-size: 1.4rem;
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

@media (max-width: 1180px) {
  .detail-grid,
  .profile-banner {
    grid-template-columns: 1fr;
  }

  .profile-banner__side {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 840px) {
  .customer-detail-page {
    padding: 1rem;
  }

  .detail-hero {
    grid-template-columns: 1fr;
    justify-items: flex-start;
  }

  .detail-hero__actions,
  .next-steps,
  .loading-grid,
  .profile-banner__side {
    grid-template-columns: 1fr;
  }

  .detail-hero__actions {
    width: 100%;
  }

  .profile-banner__main {
    align-items: flex-start;
    flex-direction: column;
  }
}
</style>

